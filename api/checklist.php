<?php
/**
 * API: Task Checklist
 */

ob_start();
require_once __DIR__ . '/../bootstrap.php';

// Tắt display errors SAU khi load bootstrap
ini_set('display_errors', 0);
ini_set('html_errors', 0);

use App\Middleware\AuthMiddleware;
use Core\Session;
use Core\Database;
use Core\Permission;

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
    
    $method = $_SERVER['REQUEST_METHOD'];
    $currentUserId = Session::get('user_id');
    $userRole = Session::get('user_role', 'guest');
    $db = Database::getInstance();
    $taskModel = new \App\Models\Task();
    
    switch ($method) {
        case 'GET':
            // Lấy checklist của task - chỉ cần quyền xem task
            $taskId = $_GET['task_id'] ?? null;
            if (!$taskId) {
                throw new Exception('task_id is required');
            }
            
            // Check if user can view this task
            if (!Permission::can($userRole, 'tasks.view')) {
                throw new Exception('Bạn không có quyền xem công việc này');
            }
            
            $checklist = $taskModel->getChecklist($taskId);
            
            // Tính progress
            $total = count($checklist);
            $completed = count(array_filter($checklist, fn($c) => $c['is_completed']));
            $progress = $total > 0 ? round(($completed / $total) * 100) : 0;
            
            echo json_encode([
                'success' => true,
                'data' => [
                    'items' => $checklist,
                    'total' => $total,
                    'completed' => $completed,
                    'progress' => $progress
                ]
            ]);
            break;
            
        case 'POST':
            // Thêm checklist item mới - cần quyền edit task
            $input = json_decode(file_get_contents('php://input'), true);
            
            $taskId = $input['task_id'] ?? null;
            $title = trim($input['title'] ?? '');
            
            if (!$taskId) {
                throw new Exception('task_id is required');
            }
            if (empty($title)) {
                throw new Exception('title is required');
            }
            
            // Check permission - user must have tasks.edit OR be the creator OR be assigned
            $task = $taskModel->find($taskId);
            if (!$task) {
                throw new Exception('Task not found');
            }
            
            $canEdit = Permission::can($userRole, 'tasks.edit') || $task['created_by'] === $currentUserId;
            if (!$canEdit) {
                $isAssigned = $db->fetchOne(
                    "SELECT 1 FROM task_assignees WHERE task_id = ? AND user_id = ?",
                    [$taskId, $currentUserId]
                );
                $canEdit = (bool)$isAssigned;
            }
            
            if (!$canEdit) {
                throw new Exception('Bạn không có quyền chỉnh sửa checklist này');
            }
            
            // Lấy position cao nhất
            $maxPos = $db->fetchColumn(
                "SELECT COALESCE(MAX(position), -1) FROM task_checklists WHERE task_id = ?",
                [$taskId]
            );
            
            // Generate ID
            $itemId = sprintf(
                '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
                mt_rand(0, 0xffff), mt_rand(0, 0xffff),
                mt_rand(0, 0xffff),
                mt_rand(0, 0x0fff) | 0x4000,
                mt_rand(0, 0x3fff) | 0x8000,
                mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
            );
            
            $db->insert('task_checklists', [
                'id' => $itemId,
                'task_id' => $taskId,
                'title' => $title,
                'is_completed' => 0,
                'position' => $maxPos + 1,
                'created_at' => date('Y-m-d H:i:s'),
            ]);
            
            echo json_encode([
                'success' => true,
                'message' => 'Đã thêm mục mới',
                'data' => [
                    'id' => $itemId,
                    'title' => $title,
                    'is_completed' => 0,
                    'position' => $maxPos + 1
                ]
            ]);
            break;
            
        case 'PUT':
            // Toggle hoàn thành hoặc cập nhật title
            $input = json_decode(file_get_contents('php://input'), true);
            
            $itemId = $input['item_id'] ?? null;
            $action = $input['action'] ?? 'toggle'; // toggle, update
            
            if (!$itemId) {
                throw new Exception('item_id is required');
            }
            
            // Kiểm tra item tồn tại
            $item = $db->fetchOne("SELECT * FROM task_checklists WHERE id = ?", [$itemId]);
            if (!$item) {
                throw new Exception('Checklist item not found');
            }
            
            // Check permission
            $task = $taskModel->find($item['task_id']);
            $canEdit = Permission::can($userRole, 'tasks.edit') || ($task && $task['created_by'] === $currentUserId);
            if (!$canEdit && $task) {
                $isAssigned = $db->fetchOne(
                    "SELECT 1 FROM task_assignees WHERE task_id = ? AND user_id = ?",
                    [$item['task_id'], $currentUserId]
                );
                $canEdit = (bool)$isAssigned;
            }
            
            if (!$canEdit) {
                throw new Exception('Bạn không có quyền chỉnh sửa checklist này');
            }
            
            if ($action === 'toggle') {
                // Toggle is_completed
                $newStatus = $item['is_completed'] ? 0 : 1;
                $db->update('task_checklists', [
                    'is_completed' => $newStatus,
                    'completed_at' => $newStatus ? date('Y-m-d H:i:s') : null,
                    'completed_by' => $newStatus ? $currentUserId : null,
                ], 'id = ?', [$itemId]);
                
                echo json_encode([
                    'success' => true,
                    'message' => $newStatus ? 'Đã hoàn thành' : 'Đã bỏ hoàn thành',
                    'data' => ['is_completed' => $newStatus]
                ]);
            } else {
                // Update title
                $title = trim($input['title'] ?? '');
                if (empty($title)) {
                    throw new Exception('title is required');
                }
                
                $db->update('task_checklists', ['title' => $title], 'id = ?', [$itemId]);
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Đã cập nhật'
                ]);
            }
            break;
            
        case 'DELETE':
            // Xóa checklist item
            $itemId = $_GET['item_id'] ?? null;
            
            if (!$itemId) {
                throw new Exception('item_id is required');
            }
            
            // Kiểm tra item tồn tại và quyền
            $item = $db->fetchOne("SELECT * FROM task_checklists WHERE id = ?", [$itemId]);
            if (!$item) {
                throw new Exception('Item not found');
            }
            
            // Check permission
            $task = $taskModel->find($item['task_id']);
            $canDelete = Permission::can($userRole, 'tasks.edit') || ($task && $task['created_by'] === $currentUserId);
            if (!$canDelete && $task) {
                $isAssigned = $db->fetchOne(
                    "SELECT 1 FROM task_assignees WHERE task_id = ? AND user_id = ?",
                    [$item['task_id'], $currentUserId]
                );
                $canDelete = (bool)$isAssigned;
            }
            
            if (!$canDelete) {
                throw new Exception('Bạn không có quyền xóa checklist này');
            }
            
            // Không cho phép xóa checklist item đã hoàn thành
            if ($item['is_completed']) {
                throw new Exception('Không thể xóa mục đã hoàn thành. Vui lòng bỏ hoàn thành trước khi xóa.');
            }
            
            $db->delete('task_checklists', 'id = ?', [$itemId]);
            
            echo json_encode([
                'success' => true,
                'message' => 'Đã xóa mục'
            ]);
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
