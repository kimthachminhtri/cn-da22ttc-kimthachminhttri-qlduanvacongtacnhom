<?php
/**
 * API: Delete/Deactivate Team Member
 */

require_once __DIR__ . '/../includes/config.php';

header('Content-Type: application/json');

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Chưa đăng nhập']);
    exit;
}

// Kiểm tra quyền (chỉ admin mới được xóa thành viên)
if (!auth()->can('team.manage')) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Bạn không có quyền thực hiện thao tác này']);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];
if (!in_array($method, ['POST', 'DELETE'])) {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

// Get member ID
$memberId = $_GET['id'] ?? '';
if (empty($memberId)) {
    $input = json_decode(file_get_contents('php://input'), true);
    $memberId = $input['member_id'] ?? '';
}

$action = $_GET['action'] ?? 'deactivate'; // deactivate or delete

if (empty($memberId)) {
    echo json_encode(['success' => false, 'error' => 'ID thành viên là bắt buộc']);
    exit;
}

// Prevent self-deletion
if ($memberId === $_SESSION['user_id']) {
    echo json_encode(['success' => false, 'error' => 'Bạn không thể xóa/vô hiệu hóa chính mình']);
    exit;
}

try {
    $userModel = new User();
    
    // Check if member exists
    $member = $userModel->find($memberId);
    if (!$member) {
        echo json_encode(['success' => false, 'error' => 'Không tìm thấy thành viên']);
        exit;
    }
    
    // Check if trying to delete an admin (only super admin can do this)
    $currentUser = $userModel->find($_SESSION['user_id']);
    if ($member['role'] === 'admin' && $currentUser['role'] !== 'admin') {
        echo json_encode(['success' => false, 'error' => 'Chỉ admin mới có thể xóa admin khác']);
        exit;
    }
    
    if ($action === 'delete') {
        // Hard delete - permanently remove user
        // First, check if user has any tasks assigned
        $db = Database::getInstance();
        $taskCount = $db->fetchOne(
            "SELECT COUNT(*) as count FROM task_assignees WHERE user_id = ?", 
            [$memberId]
        );
        
        if ($taskCount && $taskCount['count'] > 0) {
            echo json_encode([
                'success' => false, 
                'error' => "Không thể xóa vì thành viên này đang được giao {$taskCount['count']} công việc. Hãy vô hiệu hóa thay vì xóa."
            ]);
            exit;
        }
        
        // Delete user settings first
        $db->query("DELETE FROM user_settings WHERE user_id = ?", [$memberId]);
        
        // Delete user
        $result = $userModel->delete($memberId);
        $message = 'Đã xóa thành viên vĩnh viễn';
        
    } else {
        // Soft delete - just deactivate
        $result = $userModel->update($memberId, ['is_active' => 0]);
        $message = 'Đã vô hiệu hóa thành viên';
    }
    
    if ($result) {
        echo json_encode([
            'success' => true, 
            'message' => $message,
            'action' => $action
        ]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Không thể thực hiện thao tác']);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Có lỗi xảy ra: ' . $e->getMessage()]);
}
