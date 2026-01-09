<?php
/**
 * API: Create Task
 */
require_once __DIR__ . '/../bootstrap.php';
require_once BASE_PATH . '/includes/csrf.php';
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    csrf_require();
}

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
$errors = [];
$errorField = null;

if (empty($title)) {
    $errors[] = 'Tiêu đề công việc là bắt buộc';
    $errorField = 'title';
}

if (strlen($title) > 255) {
    $errors[] = 'Tiêu đề không được vượt quá 255 ký tự';
    $errorField = $errorField ?? 'title';
}

// Validate date format and value
if (!empty($dueDate)) {
    // Check date format (YYYY-MM-DD)
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $dueDate)) {
        $errors[] = 'Định dạng ngày không hợp lệ (YYYY-MM-DD)';
        $errorField = $errorField ?? 'dueDate';
    } else {
        $dateTime = \DateTime::createFromFormat('Y-m-d', $dueDate);
        if (!$dateTime || $dateTime->format('Y-m-d') !== $dueDate) {
            $errors[] = 'Ngày không hợp lệ';
            $errorField = $errorField ?? 'dueDate';
        } elseif (strtotime($dueDate) < strtotime('today')) {
            $errors[] = 'Ngày hết hạn phải từ hôm nay trở đi';
            $errorField = $errorField ?? 'dueDate';
        }
    }
}

if (!empty($errors)) {
    if ($isAjax || $wantsJson || strpos($contentType, 'application/json') !== false) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false, 
            'error' => implode('. ', $errors),
            'field' => $errorField
        ]);
        exit;
    } else {
        Session::flash('error', implode('. ', $errors));
        header('Location: /php/tasks.php');
    }
    exit;
}

// Validate project membership if project_id is provided
if (!empty($projectId)) {
    $db = \Core\Database::getInstance();
    $userId = Session::get('user_id');
    
    // Check if user is a member of the project
    $isMember = $db->fetchOne(
        "SELECT 1 FROM project_members WHERE project_id = ? AND user_id = ?",
        [$projectId, $userId]
    );
    
    if (!$isMember) {
        $errorMsg = 'Bạn không phải là thành viên của dự án này';
        if ($isAjax || $wantsJson || strpos($contentType, 'application/json') !== false) {
            http_response_code(403);
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => $errorMsg]);
            exit;
        } else {
            Session::flash('error', $errorMsg);
            header('Location: /php/tasks.php');
            exit;
        }
    }
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
