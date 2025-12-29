<?php
/**
 * API: Activity Log
 * Ghi và lấy log hoạt động
 */

require_once __DIR__ . '/../includes/config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    // Get activity logs
    $limit = min((int)($_GET['limit'] ?? 20), 100);
    $offset = (int)($_GET['offset'] ?? 0);
    $entityType = $_GET['entity_type'] ?? null;
    $entityId = $_GET['entity_id'] ?? null;
    
    try {
        $db = Database::getInstance();
        
        $sql = "SELECT a.*, u.full_name, u.avatar_url 
                FROM activity_logs a 
                LEFT JOIN users u ON a.user_id = u.id 
                WHERE 1=1";
        $params = [];
        
        if ($entityType) {
            $sql .= " AND a.entity_type = ?";
            $params[] = $entityType;
        }
        if ($entityId) {
            $sql .= " AND a.entity_id = ?";
            $params[] = $entityId;
        }
        
        $sql .= " ORDER BY a.created_at DESC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        
        $activities = $db->fetchAll($sql, $params);
        
        // Format activities
        $formatted = array_map(function($a) {
            return [
                'id' => $a['id'],
                'user' => [
                    'id' => $a['user_id'],
                    'name' => $a['full_name'],
                    'avatar' => $a['avatar_url'],
                ],
                'action' => $a['action'],
                'entity_type' => $a['entity_type'],
                'entity_id' => $a['entity_id'],
                'details' => json_decode($a['details'] ?? '{}', true),
                'time' => $a['created_at'],
                'time_ago' => timeAgo($a['created_at']),
            ];
        }, $activities);
        
        echo json_encode([
            'success' => true,
            'data' => $formatted,
        ]);
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    
} elseif ($method === 'POST') {
    // Log new activity
    $input = json_decode(file_get_contents('php://input'), true);
    
    $action = $input['action'] ?? '';
    $entityType = $input['entity_type'] ?? '';
    $entityId = $input['entity_id'] ?? '';
    $details = $input['details'] ?? [];
    
    if (empty($action) || empty($entityType)) {
        echo json_encode(['success' => false, 'error' => 'action and entity_type are required']);
        exit;
    }
    
    try {
        logActivity($action, $entityType, $entityId, $details);
        
        echo json_encode([
            'success' => true,
            'message' => 'Activity logged'
        ]);
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
}

/**
 * Log activity to database
 */
function logActivity(string $action, string $entityType, ?string $entityId = null, array $details = []): void
{
    $db = Database::getInstance();
    
    $logId = sprintf(
        '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand(0, 0xffff), mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0x0fff) | 0x4000,
        mt_rand(0, 0x3fff) | 0x8000,
        mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
    );
    
    $db->insert('activity_logs', [
        'id' => $logId,
        'user_id' => $_SESSION['user_id'] ?? null,
        'action' => $action,
        'entity_type' => $entityType,
        'entity_id' => $entityId,
        'details' => json_encode($details),
        'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null,
        'created_at' => date('Y-m-d H:i:s'),
    ]);
}

/**
 * Helper: Tính thời gian tương đối
 */
function timeAgo(string $datetime): string
{
    $time = strtotime($datetime);
    $diff = time() - $time;
    
    if ($diff < 60) return 'Vừa xong';
    if ($diff < 3600) return floor($diff / 60) . ' phút trước';
    if ($diff < 86400) return floor($diff / 3600) . ' giờ trước';
    if ($diff < 604800) return floor($diff / 86400) . ' ngày trước';
    if ($diff < 2592000) return floor($diff / 604800) . ' tuần trước';
    
    return date('d/m/Y', $time);
}
