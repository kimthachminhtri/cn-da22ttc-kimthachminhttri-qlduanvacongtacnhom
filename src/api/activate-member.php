<?php
/**
 * API: Activate/Deactivate Team Member
 */
require_once __DIR__ . '/../bootstrap.php';
require_once BASE_PATH . '/includes/csrf.php';
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    csrf_require();
}

use App\Models\User;
use App\Middleware\AuthMiddleware;
use App\Middleware\PermissionMiddleware;
use Core\Session;

header('Content-Type: application/json');

AuthMiddleware::handle();

if (!PermissionMiddleware::can('users.edit')) {
    exit;
}

// Get input
$input = json_decode(file_get_contents('php://input'), true);
$_POST = array_merge($_POST, $input ?? []);

$userId = $_POST['user_id'] ?? $_GET['id'] ?? '';
$isActive = isset($_POST['is_active']) ? (bool)$_POST['is_active'] : true;

if (empty($userId)) {
    echo json_encode(['success' => false, 'error' => 'ID người dùng là bắt buộc']);
    exit;
}

// Cannot change own status
if ($userId === Session::get('user_id')) {
    echo json_encode(['success' => false, 'error' => 'Không thể thay đổi trạng thái của chính mình']);
    exit;
}

try {
    $userModel = new User();
    
    $member = $userModel->find($userId);
    if (!$member) {
        echo json_encode(['success' => false, 'error' => 'Không tìm thấy người dùng']);
        exit;
    }
    
    $userModel->update($userId, ['is_active' => $isActive ? 1 : 0]);
    
    echo json_encode([
        'success' => true, 
        'message' => $isActive ? 'Đã kích hoạt người dùng' : 'Đã vô hiệu hóa người dùng'
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Có lỗi xảy ra']);
}
