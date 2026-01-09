<?php
/**
 * API: Comments
 */

// Định nghĩa hàm trước khi dùng
function generateCommentUUID(): string {
    return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff),
        mt_rand(0, 0x0fff) | 0x4000, mt_rand(0, 0x3fff) | 0x8000,
        mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff));
}

// Bắt tất cả output
ob_start();

require_once __DIR__ . '/../bootstrap.php';
require_once BASE_PATH . '/includes/functions.php'; // Ensure timeAgo() is available

// Tắt display errors SAU khi load bootstrap
ini_set('display_errors', 0);
ini_set('html_errors', 0);

use App\Middleware\AuthMiddleware;
use Core\Session;
use Core\Database;

// Clear any previous output
ob_end_clean();

header('Content-Type: application/json');

// Custom error handler để trả về JSON
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => "Error [$errno]: $errstr in $errfile:$errline"]);
    exit;
});

try {
    AuthMiddleware::handle();
    
    // Enforce CSRF for state-changing requests
    require_once BASE_PATH . '/includes/csrf.php';
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        csrf_require();
    }

    $method = $_SERVER['REQUEST_METHOD'];
    $currentUserId = Session::get('user_id');
    $db = Database::getInstance();
    switch ($method) {
        case 'GET':
            $entityType = $_GET['entity_type'] ?? 'task';
            $entityId = $_GET['entity_id'] ?? $_GET['task_id'] ?? null;
            
            if (!$entityId) {
                throw new Exception('entity_id is required');
            }
            
            // Lấy tất cả comments (bao gồm cả replies)
            // Sắp xếp theo thời gian tạo giảm dần (mới nhất trước)
            $allComments = $db->fetchAll(
                "SELECT c.*, u.full_name, u.avatar_url
                 FROM comments c
                 JOIN users u ON c.created_by = u.id
                 WHERE c.entity_type = ? AND c.entity_id = ?
                 ORDER BY c.created_at DESC",
                [$entityType, $entityId]
            );
            
            // Tổ chức comments theo cấu trúc cha-con (hỗ trợ nested replies)
            $commentsById = [];
            $rootComments = [];
            
            foreach ($allComments as &$comment) {
                $comment['time_ago'] = timeAgo($comment['created_at']);
                $comment['replies'] = [];
                $comment['depth'] = 0; // Độ sâu của comment
                $commentsById[$comment['id']] = &$comment;
            }
            unset($comment);
            
            // Tính độ sâu và tìm root parent cho mỗi comment
            foreach ($allComments as &$comment) {
                if (!empty($comment['parent_id'])) {
                    $depth = 1;
                    $parentId = $comment['parent_id'];
                    $rootParentId = $parentId;
                    
                    // Tìm root parent và tính độ sâu
                    while (isset($commentsById[$parentId]) && !empty($commentsById[$parentId]['parent_id'])) {
                        $depth++;
                        $parentId = $commentsById[$parentId]['parent_id'];
                        $rootParentId = $parentId;
                    }
                    
                    $comment['depth'] = $depth;
                    $comment['root_parent_id'] = $rootParentId;
                }
            }
            unset($comment);
            
            // Xây dựng cấu trúc cha-con đệ quy
            // Hàm đệ quy để build tree
            function buildCommentTree(&$commentsById, $parentId = null) {
                $result = [];
                foreach ($commentsById as &$comment) {
                    if (($comment['parent_id'] ?? null) === $parentId) {
                        $comment['replies'] = buildCommentTree($commentsById, $comment['id']);
                        // Sắp xếp replies theo thời gian (cũ nhất trước để dễ theo dõi conversation)
                        usort($comment['replies'], fn($a, $b) => strtotime($a['created_at']) - strtotime($b['created_at']));
                        $result[] = $comment;
                    }
                }
                return $result;
            }
            
            $rootComments = buildCommentTree($commentsById, null);
            // Root comments: mới nhất trước
            usort($rootComments, fn($a, $b) => strtotime($b['created_at']) - strtotime($a['created_at']));
            
            echo json_encode(['success' => true, 'data' => $rootComments]);
            break;
            
        case 'POST':
            $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
            
            $entityType = $input['entity_type'] ?? 'task';
            $entityId = $input['entity_id'] ?? $input['task_id'] ?? null;
            $content = trim($input['content'] ?? '');
            $parentId = $input['parent_id'] ?? null; // Hỗ trợ reply
            
            if (!$entityId || empty($content)) {
                throw new Exception('entity_id and content are required');
            }
            
            // Sanitize content to prevent XSS
            // Allow basic formatting but strip dangerous tags
            $content = strip_tags($content, '<b><i><u><strong><em><br><p>');
            $content = htmlspecialchars($content, ENT_QUOTES, 'UTF-8');
            
            // Limit content length
            if (strlen($content) > 10000) {
                throw new Exception('Nội dung bình luận quá dài (tối đa 10000 ký tự)');
            }
            
            // Nếu là reply, kiểm tra parent comment tồn tại
            if ($parentId) {
                $parentComment = $db->fetchOne("SELECT id FROM comments WHERE id = ?", [$parentId]);
                if (!$parentComment) {
                    throw new Exception('Parent comment not found');
                }
            }
            
            $commentId = generateCommentUUID();
            $db->insert('comments', [
                'id' => $commentId,
                'entity_type' => $entityType,
                'entity_id' => $entityId,
                'content' => $content,
                'parent_id' => $parentId,
                'created_by' => $currentUserId,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            
            $comment = $db->fetchOne(
                "SELECT c.*, u.full_name, u.avatar_url
                 FROM comments c
                 JOIN users u ON c.created_by = u.id
                 WHERE c.id = ?",
                [$commentId]
            );
            $comment['time_ago'] = 'Vừa xong';
            $comment['replies'] = [];
            
            echo json_encode(['success' => true, 'data' => $comment]);
            break;
            
        case 'PUT':
            $input = json_decode(file_get_contents('php://input'), true);
            $commentId = $input['comment_id'] ?? null;
            $content = trim($input['content'] ?? '');
            
            if (!$commentId || empty($content)) {
                throw new Exception('comment_id and content are required');
            }
            
            $comment = $db->fetchOne("SELECT * FROM comments WHERE id = ?", [$commentId]);
            if (!$comment || $comment['created_by'] !== $currentUserId) {
                throw new Exception('Comment not found or permission denied');
            }
            
            $db->update('comments', ['content' => $content], 'id = ?', [$commentId]);
            echo json_encode(['success' => true, 'message' => 'Comment updated']);
            break;
            
        case 'DELETE':
            $commentId = $_GET['comment_id'] ?? null;
            
            if (!$commentId) {
                throw new Exception('comment_id is required');
            }
            
            $comment = $db->fetchOne("SELECT * FROM comments WHERE id = ?", [$commentId]);
            if (!$comment || $comment['created_by'] !== $currentUserId) {
                throw new Exception('Comment not found or permission denied');
            }
            
            // Xóa comment sẽ tự động xóa replies do ON DELETE CASCADE
            $db->delete('comments', 'id = ?', [$commentId]);
            echo json_encode(['success' => true, 'message' => 'Comment deleted']);
            break;
            
        default:
            http_response_code(405);
            echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    }
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}


