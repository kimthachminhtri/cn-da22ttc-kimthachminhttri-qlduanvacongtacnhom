<?php
/**
 * API: Notifications
 * CRUD cho thông báo
 */

require_once __DIR__ . '/../includes/config.php';
require_once BASE_PATH . '/includes/csrf.php';
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    csrf_require();
}

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];
$currentUserId = $_SESSION['user_id'];

try {
    $db = Database::getInstance();
    
    switch ($method) {
        case 'GET':
            $action = $_GET['action'] ?? 'list';
            
            if ($action === 'count') {
                // Get unread count
                $result = $db->fetchOne(
                    "SELECT COUNT(*) as count FROM notifications WHERE user_id = ? AND is_read = 0",
                    [$currentUserId]
                );
                echo json_encode([
                    'success' => true,
                    'count' => (int)($result['count'] ?? 0)
                ]);
            } else {
                // List notifications
                $limit = min((int)($_GET['limit'] ?? 20), 100);
                $offset = (int)($_GET['offset'] ?? 0);
                $unreadOnly = isset($_GET['unread']) && $_GET['unread'] === '1';
                
                $sql = "SELECT n.*, 
                        u.full_name as actor_name, u.avatar_url as actor_avatar
                        FROM notifications n
                        LEFT JOIN users u ON n.actor_id = u.id
                        WHERE n.user_id = ?";
                $params = [$currentUserId];
                
                if ($unreadOnly) {
                    $sql .= " AND n.is_read = 0";
                }
                
                $sql .= " ORDER BY n.created_at DESC LIMIT ? OFFSET ?";
                $params[] = $limit;
                $params[] = $offset;
                
                $notifications = $db->fetchAll($sql, $params);
                
                // Get total count
                $countSql = "SELECT COUNT(*) as total FROM notifications WHERE user_id = ?";
                if ($unreadOnly) {
                    $countSql .= " AND is_read = 0";
                }
                $countResult = $db->fetchOne($countSql, [$currentUserId]);
                
                echo json_encode([
                    'success' => true,
                    'data' => $notifications,
                    'total' => (int)($countResult['total'] ?? 0),
                    'limit' => $limit,
                    'offset' => $offset
                ]);
            }
            break;
            
        case 'PUT':
            // Mark as read
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (isset($input['mark_all']) && $input['mark_all']) {
                // Mark all as read
                $db->query(
                    "UPDATE notifications SET is_read = 1, read_at = NOW() WHERE user_id = ? AND is_read = 0",
                    [$currentUserId]
                );
                echo json_encode(['success' => true, 'message' => 'Đã đánh dấu tất cả đã đọc']);
            } elseif (isset($input['id'])) {
                // Mark single as read
                $db->update('notifications', 
                    ['is_read' => 1, 'read_at' => date('Y-m-d H:i:s')],
                    'id = ? AND user_id = ?',
                    [$input['id'], $currentUserId]
                );
                echo json_encode(['success' => true, 'message' => 'Đã đánh dấu đã đọc']);
            } else {
                throw new Exception('id or mark_all is required');
            }
            break;
            
        case 'POST':
            $input = json_decode(file_get_contents('php://input'), true);
            $action = $input['action'] ?? 'create';
            
            if ($action === 'mark_read') {
                // Mark single notification as read
                $notificationId = $input['notification_id'] ?? null;
                if ($notificationId) {
                    $db->update('notifications', 
                        ['is_read' => 1, 'read_at' => date('Y-m-d H:i:s')],
                        'id = ? AND user_id = ?',
                        [$notificationId, $currentUserId]
                    );
                }
                echo json_encode(['success' => true, 'message' => 'Đã đánh dấu đã đọc']);
                
            } elseif ($action === 'mark_all_read') {
                // Mark all as read
                $db->query(
                    "UPDATE notifications SET is_read = 1, read_at = NOW() WHERE user_id = ? AND is_read = 0",
                    [$currentUserId]
                );
                echo json_encode(['success' => true, 'message' => 'Đã đánh dấu tất cả đã đọc']);
                
            } else {
                // Create notification (internal use)
                $userId = $input['user_id'] ?? null;
                $type = $input['type'] ?? '';
                $title = trim($input['title'] ?? '');
                $message = trim($input['message'] ?? '');
                $link = $input['link'] ?? null;
                $actorId = $input['actor_id'] ?? $currentUserId;
                $data = $input['data'] ?? null;
                
                if (empty($userId) || empty($type) || empty($title) || empty($message)) {
                    throw new Exception('Missing required fields');
                }
                
                $notificationId = createNotification($db, [
                    'user_id' => $userId,
                    'type' => $type,
                    'title' => $title,
                    'message' => $message,
                    'link' => $link,
                    'actor_id' => $actorId,
                    'data' => $data
                ]);
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Đã tạo thông báo',
                    'data' => ['id' => $notificationId]
                ]);
            }
            break;
            
        case 'DELETE':
            $notificationId = $_GET['id'] ?? null;
            
            if ($notificationId === 'all') {
                // Delete all read notifications
                $db->delete('notifications', 'user_id = ? AND is_read = 1', [$currentUserId]);
                echo json_encode(['success' => true, 'message' => 'Đã xóa thông báo đã đọc']);
            } elseif ($notificationId) {
                // Delete single notification
                $db->delete('notifications', 'id = ? AND user_id = ?', [$notificationId, $currentUserId]);
                echo json_encode(['success' => true, 'message' => 'Đã xóa thông báo']);
            } else {
                throw new Exception('notification_id is required');
            }
            break;
            
        default:
            http_response_code(405);
            echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    }
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

/**
 * Helper function to create notification
 */
function createNotification($db, array $data): string {
    $notificationId = sprintf(
        '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand(0, 0xffff), mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0x0fff) | 0x4000,
        mt_rand(0, 0x3fff) | 0x8000,
        mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
    );
    
    $db->insert('notifications', [
        'id' => $notificationId,
        'user_id' => $data['user_id'],
        'type' => $data['type'],
        'title' => $data['title'],
        'message' => $data['message'],
        'link' => $data['link'] ?? null,
        'actor_id' => $data['actor_id'] ?? null,
        'data' => isset($data['data']) ? json_encode($data['data']) : null,
        'is_read' => 0,
        'created_at' => date('Y-m-d H:i:s'),
    ]);
    
    return $notificationId;
}
