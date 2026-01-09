<?php
/**
 * API: Update Task
 */
require_once __DIR__ . '/../bootstrap.php';
require_once BASE_PATH . '/includes/csrf.php';
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    csrf_require();
}

use App\Models\Task;
use App\Middleware\AuthMiddleware;
use App\Constants\TaskConstants;
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
    // - Full edit: admin, system manager (cần verify project role tốt hơn nhưng tạm thời giữ), or task creator, or project owner/manager
    
    // Check project role directly
    $projectRole = null;
    if (!empty($task['project_id'])) {
        $projectMember = $db->fetchOne(
            "SELECT role FROM project_members WHERE project_id = ? AND user_id = ?",
            [$task['project_id'], $userId]
        );
        $projectRole = $projectMember['role'] ?? null;
    }

    $isProjectAdmin = in_array($projectRole, ['owner', 'manager']);
    $isSystemAdmin = $userRole === 'admin';
    $isCreator = $task['created_by'] === $userId;

    $hasFullEdit = $isSystemAdmin || $isCreator || $isProjectAdmin || Permission::can($userRole, 'tasks.edit');
    $canUpdateStatus = $hasFullEdit || (bool)$isAssigned;
    
    if (!$canUpdateStatus) {
        http_response_code(403);
        echo json_encode(['success' => false, 'error' => 'Bạn không có quyền chỉnh sửa công việc này']);
        exit;
    }
    
    // Optimistic locking: check version to prevent race conditions
    $clientVersion = isset($_POST['version']) ? (int)$_POST['version'] : null;
    $currentVersion = (int)($task['version'] ?? 1);
    
    if ($clientVersion !== null && $clientVersion !== $currentVersion) {
        http_response_code(409); // Conflict
        echo json_encode([
            'success' => false, 
            'error' => 'Dữ liệu đã được cập nhật bởi người khác. Vui lòng tải lại trang.',
            'conflict' => true,
            'current_version' => $currentVersion
        ]);
        exit;
    }
    
    try {
        $updateData = [];
        
        // Increment version for optimistic locking
        $updateData['version'] = $currentVersion + 1;
        
        // Status - everyone who can edit can update status
        if (isset($_POST['status'])) {
            if (TaskConstants::isValidStatus($_POST['status'])) {
                $updateData['status'] = $_POST['status'];
                $updateData['completed_at'] = $_POST['status'] === TaskConstants::STATUS_DONE ? date('Y-m-d H:i:s') : null;
            }
        }
        
        // These fields require full edit permission
        if ($hasFullEdit) {
            if (isset($_POST['title']) && !empty(trim($_POST['title']))) {
                $updateData['title'] = trim($_POST['title']);
            }
            
            if (isset($_POST['description'])) {
                $updateData['description'] = trim($_POST['description']);
            }
            
            if (isset($_POST['priority'])) {
                if (TaskConstants::isValidPriority($_POST['priority'])) {
                    $updateData['priority'] = $_POST['priority'];
                }
            }
            
            if (isset($_POST['due_date'])) {
                $updateData['due_date'] = $_POST['due_date'] ?: null;
            }
            
            // Assignee logic remains the same...
            if (isset($_POST['assignee_id'])) { // Removed Permission check here, used $hasFullEdit implicitly
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
    // Check project role for delete
    $projectRole = null;
    if (!empty($task['project_id'])) {
        $projectMember = $db->fetchOne(
            "SELECT role FROM project_members WHERE project_id = ? AND user_id = ?",
            [$task['project_id'], $userId]
        );
        $projectRole = $projectMember['role'] ?? null;
    }

    $isProjectAdmin = in_array($projectRole, ['owner', 'manager']);
    $isSystemAdmin = $userRole === 'admin';
    $isCreator = $task['created_by'] === $userId;
    
    // Only admin, project owner/manager, or creator can delete
    $canDelete = $isSystemAdmin || $isCreator || $isProjectAdmin;
    
    if (!$canDelete) {
        http_response_code(403);
        echo json_encode(['success' => false, 'error' => 'Bạn không có quyền xóa task này']);
        exit;
    }
    
    // Không cho phép xóa task đã hoàn thành
    if ($task['status'] === TaskConstants::STATUS_DONE) {
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
