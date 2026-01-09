<?php
/**
 * API: Create Project
 */
require_once __DIR__ . '/../bootstrap.php';
require_once BASE_PATH . '/includes/csrf.php';
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    csrf_require();
}

use App\Models\Project;
use App\Middleware\AuthMiddleware;
use Core\Session;
use Core\Permission;

// Check authentication
AuthMiddleware::handle();

// Check permission
$userRole = Session::get('user_role', 'guest');
if (!Permission::can($userRole, 'projects.create')) {
    http_response_code(403);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Bạn không có quyền tạo dự án']);
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

$name = trim($_POST['name'] ?? '');
$description = trim($_POST['description'] ?? '');
$color = $_POST['color'] ?? '#6366f1';
$priority = $_POST['priority'] ?? 'medium';
$startDate = $_POST['start_date'] ?? $_POST['startDate'] ?? null;
$endDate = $_POST['end_date'] ?? $_POST['endDate'] ?? null;

// Check if this is an AJAX request
$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
          strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
$wantsJson = strpos($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json') !== false;

// Validate
if (empty($name)) {
    if ($isAjax || $wantsJson) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'error' => 'Tên dự án là bắt buộc', 'field' => 'name']);
    } else {
        Session::flash('error', 'Tên dự án là bắt buộc');
        header('Location: /php/projects.php');
    }
    exit;
}

if (strlen($name) < 2) {
    if ($isAjax || $wantsJson) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'error' => 'Tên dự án phải có ít nhất 2 ký tự', 'field' => 'name']);
    } else {
        Session::flash('error', 'Tên dự án phải có ít nhất 2 ký tự');
        header('Location: /php/projects.php');
    }
    exit;
}

try {
    $projectModel = new Project();
    $userId = Session::get('user_id');
    $db = \Core\Database::getInstance();
    
    // Generate UUID
    $projectId = sprintf(
        '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand(0, 0xffff), mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0x0fff) | 0x4000,
        mt_rand(0, 0x3fff) | 0x8000,
        mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
    );
    
    // Use transaction to ensure atomicity
    $db->beginTransaction();
    
    try {
        // Create project
        $projectModel->create([
            'id' => $projectId,
            'name' => $name,
            'description' => $description,
            'color' => $color,
            'status' => 'planning',
            'priority' => $priority,
            'progress' => 0,
            'start_date' => $startDate ?: null,
            'end_date' => $endDate ?: null,
            'created_by' => $userId,
        ]);
        
        // Add creator as owner
        $projectModel->addMember($projectId, $userId, 'owner');
        
        $db->commit();
    } catch (Exception $e) {
        $db->rollback();
        throw $e;
    }
    
    if ($isAjax || $wantsJson) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'message' => 'Tạo dự án thành công',
            'project_id' => $projectId
        ]);
    } else {
        Session::flash('success', 'Tạo dự án thành công');
        header('Location: /php/project-detail.php?id=' . $projectId);
    }
    
} catch (Exception $e) {
    if ($isAjax || $wantsJson) {
        http_response_code(500);
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    } else {
        Session::flash('error', 'Lỗi: ' . $e->getMessage());
        header('Location: /php/projects.php');
    }
}
