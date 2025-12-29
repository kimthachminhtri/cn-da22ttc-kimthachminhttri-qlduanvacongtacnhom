<?php
/**
 * API: Create Team Member
 */

require_once __DIR__ . '/../includes/config.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

// Kiểm tra quyền (chỉ admin và manager mới được tạo thành viên)
if (!auth()->can('team.manage')) {
    $_SESSION['error'] = 'Bạn không có quyền thực hiện thao tác này';
    header('Location: ../team.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../team.php');
    exit;
}

$fullName = trim($_POST['full_name'] ?? '');
$email = trim($_POST['email'] ?? '');
$department = trim($_POST['department'] ?? '');
$position = trim($_POST['position'] ?? '');
$role = $_POST['role'] ?? 'member';
$password = $_POST['password'] ?? '';

// Validate
$errors = [];
if (empty($fullName)) {
    $errors[] = 'Họ tên là bắt buộc';
}
if (empty($email)) {
    $errors[] = 'Email là bắt buộc';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Email không hợp lệ';
}
if (empty($password)) {
    $errors[] = 'Mật khẩu là bắt buộc';
} elseif (strlen($password) < 6) {
    $errors[] = 'Mật khẩu phải có ít nhất 6 ký tự';
}
if (!in_array($role, ['admin', 'manager', 'member', 'guest'])) {
    $errors[] = 'Vai trò không hợp lệ';
}

if (!empty($errors)) {
    $_SESSION['error'] = implode(', ', $errors);
    header('Location: ../team.php');
    exit;
}

try {
    $userModel = new User();
    
    // Check if email exists
    if ($userModel->findByEmail($email)) {
        $_SESSION['error'] = 'Email đã được sử dụng';
        header('Location: ../team.php');
        exit;
    }
    
    // Create user
    $userId = $userModel->createUser([
        'email' => $email,
        'full_name' => $fullName,
        'password' => $password,
        'role' => $role,
        'department' => $department ?: null,
        'position' => $position ?: null,
        'is_active' => 1,
    ]);
    
    if ($userId) {
        // Create default user settings
        try {
            db()->insert('user_settings', ['user_id' => $userId]);
        } catch (Exception $e) {
            // Ignore if fails
        }
        
        $_SESSION['success'] = "Đã tạo thành viên '$fullName' thành công";
    } else {
        $_SESSION['error'] = 'Không thể tạo thành viên';
    }
    
} catch (Exception $e) {
    $_SESSION['error'] = 'Có lỗi xảy ra: ' . $e->getMessage();
}

header('Location: ../team.php');
exit;
