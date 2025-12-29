<?php
/**
 * API: Create Task
 */
require_once __DIR__ . '/../bootstrap.php';

use App\Models\Task;
use App\Middleware\AuthMiddleware;
use Core\Session;
use Core\Permission;

// Check authentication
AuthMiddleware::handle();

// Check permission
$userRole = Session::get('user_role', 'guest');
if (!Permission::can($userRole, 'tasks.create')) {
    http_response_code(403);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Bạn không có quyền tạo công việc']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

// Parse input - support both JSON and form data
$contentType = $_SERVER['CONTENT_TYPE'] ?? '';
if (strpos($contentType, 'application/json') !== false) {
    $input = json_decode(file_get_contents('php://input'), true) ?? [];
    $_POST = array_merge($_POST, $input);
}

$title = trim($_POST['title'] ?? '');
$description = trim($_POST['description'] ?? '');
$projectId = $_POST['project_id'] ?? $_POST['projectId'] ?? null;
$priority = $_POST['priority'] ?? 'medium';
$dueDate = $_POST['due_date'] ?? $_POST['dueDate'] ?? null;
$assigneeId = $_POST['assignee_id'] ?? $_POST['assigneeId'] ?? null;
$status = $_POST['status'] ?? 'todo';

// Check if this is an AJAX request
$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
          strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
$wantsJson = strpos($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json') !== false;

// Validate
if (empty($title)) {
    if ($isAjax || $wantsJson) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'error' => 'Tiêu đề công việc là bắt buộc']);
    } else {
        Session::flash('error', 'Tiêu đề công việc là bắt buộc');
        header('Location: /php/tasks.php');
    }
    exit;
}

try {
    $taskModel = new Task();
    $userId = Session::get('user_id');
    
    // Generate UUID
    $taskId = sprintf(
        '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand(0, 0xffff), mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0x0fff) | 0x4000,
        mt_rand(0, 0x3fff) | 0x8000,
        mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
    );
    
    // Create task
    $taskModel->create([
        'id' => $taskId,
        'project_id' => $projectId ?: null,
        'title' => $title,
        'description' => $description,
        'status' => $status,
        'priority' => $priority,
        'position' => 0,
        'due_date' => $dueDate ?: null,
        'created_by' => $userId,
    ]);
    
    // Assign user if specified
    if ($assigneeId) {
        $taskModel->assignUser($taskId, $assigneeId, $userId);
    }
    
    if ($isAjax || $wantsJson) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'message' => 'Tạo công việc thành công',
            'task_id' => $taskId
        ]);
    } else {
        Session::flash('success', 'Tạo công việc thành công');
        header('Location: /php/task-detail.php?id=' . $taskId);
    }
    
} catch (Exception $e) {
    if ($isAjax || $wantsJson) {
        http_response_code(500);
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    } else {
        Session::flash('error', 'Lỗi: ' . $e->getMessage());
        header('Location: /php/tasks.php');
    }
}
