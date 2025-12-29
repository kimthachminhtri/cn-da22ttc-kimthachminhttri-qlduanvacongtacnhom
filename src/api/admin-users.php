<?php
/**
 * API: Admin Users Management
 */
require_once __DIR__ . '/../bootstrap.php';

use App\Middleware\AuthMiddleware;
use App\Middleware\PermissionMiddleware;
use App\Models\User;
use Core\Session;
use Core\Database;

header('Content-Type: application/json');

try {
    AuthMiddleware::handle();
    PermissionMiddleware::requireAdmin();
    
    $action = $_GET['action'] ?? '';
    $userModel = new User();
    $db = Database::getInstance();
    $currentUserId = Session::get('user_id');
    
    switch ($action) {
        case 'create':
            $email = trim($_POST['email'] ?? '');
            $fullName = trim($_POST['full_name'] ?? '');
            $password = $_POST['password'] ?? '';
            $role = $_POST['role'] ?? 'member';
            
            if (empty($email) || empty($fullName) || empty($password)) {
                throw new Exception('Vui lòng điền đầy đủ thông tin');
            }
            
            if ($userModel->findByEmail($email)) {
                throw new Exception('Email đã được sử dụng');
            }
            
            $userId = $userModel->createUser([
                'email' => $email,
                'full_name' => $fullName,
                'password' => $password,
                'role' => $role,
                'position' => $_POST['position'] ?? '',
                'department' => $_POST['department'] ?? '',
                'is_active' => 1,
            ]);
            
            if ($userId) {
                Session::flash('success', 'Tạo người dùng thành công');
                header('Location: /php/admin/users.php');
                exit;
            }
            throw new Exception('Có lỗi xảy ra');
            
        case 'update':
            $userId = $_POST['user_id'] ?? null;
            if (!$userId) {
                throw new Exception('Missing user_id');
            }
            
            $data = [];
            if (isset($_POST['role'])) $data['role'] = $_POST['role'];
            if (isset($_POST['is_active'])) $data['is_active'] = $_POST['is_active'] ? 1 : 0;
            if (isset($_POST['full_name'])) $data['full_name'] = $_POST['full_name'];
            if (isset($_POST['position'])) $data['position'] = $_POST['position'];
            if (isset($_POST['department'])) $data['department'] = $_POST['department'];
            
            if (!empty($data)) {
                $userModel->update($userId, $data);
            }
            
            echo json_encode(['success' => true, 'message' => 'Cập nhật thành công']);
            break;
            
        case 'delete':
            $userId = $_POST['user_id'] ?? null;
            if (!$userId) {
                throw new Exception('Missing user_id');
            }
            
            if ($userId === $currentUserId) {
                throw new Exception('Không thể xóa chính mình');
            }
            
            $userModel->update($userId, ['is_active' => 0]);
            echo json_encode(['success' => true, 'message' => 'Đã vô hiệu hóa người dùng']);
            break;
            
        case 'bulk':
            $input = json_decode(file_get_contents('php://input'), true);
            $bulkAction = $input['action'] ?? '';
            $userIds = $input['user_ids'] ?? [];
            
            if (empty($userIds)) {
                throw new Exception('Không có người dùng nào được chọn');
            }
            
            // Remove current user from list to prevent self-modification
            $userIds = array_filter($userIds, fn($id) => $id !== $currentUserId);
            
            if (empty($userIds)) {
                throw new Exception('Không thể thực hiện thao tác này với chính mình');
            }
            
            $placeholders = implode(',', array_fill(0, count($userIds), '?'));
            
            switch ($bulkAction) {
                case 'activate':
                    $db->query("UPDATE users SET is_active = 1, updated_at = NOW() WHERE id IN ({$placeholders})", $userIds);
                    break;
                case 'deactivate':
                    $db->query("UPDATE users SET is_active = 0, updated_at = NOW() WHERE id IN ({$placeholders})", $userIds);
                    break;
                case 'delete':
                    // Soft delete
                    $db->query("UPDATE users SET is_active = 0, updated_at = NOW() WHERE id IN ({$placeholders})", $userIds);
                    break;
                default:
                    throw new Exception('Invalid bulk action');
            }
            
            echo json_encode(['success' => true, 'message' => 'Thao tác thành công', 'affected' => count($userIds)]);
            break;
            
        default:
            throw new Exception('Invalid action');
    }
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
