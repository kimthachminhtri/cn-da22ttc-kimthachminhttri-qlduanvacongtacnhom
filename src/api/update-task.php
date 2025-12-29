<?php
/**
 * API: Update Task
 */
require_once __DIR__ . '/../bootstrap.php';

use App\Models\Task;
use App\Middleware\AuthMiddleware;
use Core\Session;
use Core\Database;
use Core\Permission;

header('Content-Type: application/json');

AuthMiddleware::handle();

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true) ?? [];
$_POST = array_merge($_POST, $input);

$taskId = $_POST['task_id'] ?? $_GET['id'] ?? null;

if (!$taskId) {
    echo json_encode(['success' => false, 'error' => 'task_id is required']);
    exit;
}

$taskModel = new Task();
$task = $taskModel->find($taskId);

if (!$task) {
    echo json_encode(['success' => false, 'error' => 'Task not found']);
    exit;
}

$userId = Session::get('user_id');
$userRole = Session::get('user_role', 'guest');
$db = Database::getInstance();

// Check if user is assigned to this task
$isAssigned = $db->fetchOne(
    "SELECT 1 FROM task_assignees WHERE task_id = ? AND user_id = ?",
    [$taskId, $userId]
);

if ($method === 'POST' || $method === 'PUT') {
    // Determine edit level:
    // - Full edit: admin, manager, or task creator
    // - Status only: member assigned to task
    $hasFullEdit = Permission::can($userRole, 'tasks.edit') || $task['created_by'] === $userId;
    $canUpdateStatus = $hasFullEdit || (bool)$isAssigned;
    
    if (!$canUpdateStatus) {
        http_response_code(403);
        echo json_encode(['success' => false, 'error' => 'Bạn không có quyền chỉnh sửa công việc này']);
        exit;
    }
    
    try {
        $updateData = [];
        
        // Status - everyone who can edit can update status
        if (isset($_POST['status'])) {
            $validStatuses = ['backlog', 'todo', 'in_progress', 'in_review', 'done'];
            if (in_array($_POST['status'], $validStatuses)) {
                $updateData['status'] = $_POST['status'];
                $updateData['completed_at'] = $_POST['status'] === 'done' ? date('Y-m-d H:i:s') : null;
            }
        }
        
        // These fields require full edit permission (admin, manager, creator)
        if ($hasFullEdit) {
            if (isset($_POST['title']) && !empty(trim($_POST['title']))) {
                $updateData['title'] = trim($_POST['title']);
            }
            
            if (isset($_POST['description'])) {
                $updateData['description'] = trim($_POST['description']);
            }
            
            if (isset($_POST['priority'])) {
                $validPriorities = ['low', 'medium', 'high', 'urgent'];
                if (in_array($_POST['priority'], $validPriorities)) {
                    $updateData['priority'] = $_POST['priority'];
                }
            }
            
            if (isset($_POST['due_date'])) {
                $updateData['due_date'] = $_POST['due_date'] ?: null;
            }
            
            // Update assignee - only admin/manager can change
            if (isset($_POST['assignee_id']) && Permission::can($userRole, 'tasks.edit')) {
                $db->delete('task_assignees', 'task_id = ?', [$taskId]);
                
                if (!empty($_POST['assignee_id'])) {
                    $db->insert('task_assignees', [
                        'task_id' => $taskId,
                        'user_id' => $_POST['assignee_id'],
                        'assigned_by' => $userId,
                        'assigned_at' => date('Y-m-d H:i:s'),
                    ]);
                }
            }
        }
        
        if (!empty($updateData)) {
            $taskModel->update($taskId, $updateData);
        }
        
        echo json_encode(['success' => true, 'message' => 'Task updated']);
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    
} elseif ($method === 'DELETE') {
    // Only admin, manager, or creator can delete
    $canDelete = Permission::can($userRole, 'tasks.delete') || $task['created_by'] === $userId;
    
    if (!$canDelete) {
        http_response_code(403);
        echo json_encode(['success' => false, 'error' => 'Bạn không có quyền xóa task này']);
        exit;
    }
    
    // Không cho phép xóa task đã hoàn thành
    if ($task['status'] === 'done') {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Không thể xóa công việc đã hoàn thành. Vui lòng đổi trạng thái trước khi xóa.']);
        exit;
    }
    
    try {
        $taskModel->delete($taskId);
        echo json_encode(['success' => true, 'message' => 'Task deleted']);
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
}
