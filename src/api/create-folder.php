<?php
/**
 * API: Create Folder
 */

require_once __DIR__ . '/../bootstrap.php';

use Core\Session;
use Core\Permission;

Session::start();

if (!Session::has('user_id')) {
    header('Location: ../login.php');
    exit;
}

// Check permission
$userRole = Session::get('user_role', 'guest');
if (!Permission::can($userRole, 'documents.create')) {
    $_SESSION['error'] = 'Bạn không có quyền tạo thư mục';
    header('Location: ../documents.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../documents.php');
    exit;
}

$name = trim($_POST['name'] ?? '');
$parentId = $_POST['parent_id'] ?? null;
$projectId = $_POST['project_id'] ?? null;

if (empty($name)) {
    $_SESSION['error'] = 'Vui lòng nhập tên thư mục';
    header('Location: ../documents.php');
    exit;
}

try {
    $db = \Core\Database::getInstance();
    
    // Generate UUID
    $folderId = sprintf(
        '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand(0, 0xffff), mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0x0fff) | 0x4000,
        mt_rand(0, 0x3fff) | 0x8000,
        mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
    );
    
    $db->insert('documents', [
        'id' => $folderId,
        'name' => $name,
        'type' => 'folder',
        'parent_id' => $parentId ?: null,
        'project_id' => $projectId ?: null,
        'uploaded_by' => Session::get('user_id'),
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s'),
    ]);
    
    $_SESSION['success'] = 'Tạo thư mục thành công';
    
    // Redirect back to appropriate page
    if ($projectId) {
        $redirect = '../project-detail.php?id=' . $projectId . '&tab=documents';
    } elseif ($parentId) {
        $redirect = '../documents.php?folder=' . $parentId;
    } else {
        $redirect = '../documents.php';
    }
    header('Location: ' . $redirect);
    exit;
    
} catch (Exception $e) {
    $_SESSION['error'] = 'Có lỗi xảy ra: ' . $e->getMessage();
    
    if ($projectId) {
        header('Location: ../project-detail.php?id=' . $projectId . '&tab=documents');
    } else {
        header('Location: ../documents.php');
    }
    exit;
}
