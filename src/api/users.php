<?php
/**
 * API: Users Management
 */
require_once __DIR__ . '/../bootstrap.php';

use App\Models\User;
use App\Middleware\AuthMiddleware;
use Core\Session;

header('Content-Type: application/json');

AuthMiddleware::handle();
$method = $_SERVER['REQUEST_METHOD'];

// Enforce CSRF for non-GET requests
require_once BASE_PATH . '/includes/csrf.php';
if ($method !== 'GET') {
    csrf_require();
}
$currentUserId = Session::get('user_id');
$userRole = Session::get('user_role');

// Only admin and manager can manage users
if (!in_array($userRole, ['admin', 'manager'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Permission denied']);
    exit;
}

$userModel = new User();

try {
    switch ($method) {
        case 'GET':
            // Get single user
            $userId = $_GET['id'] ?? null;
            if (!$userId) {
                throw new Exception('user_id is required');
            }
            
            $user = $userModel->find($userId);
            if (!$user) {
                throw new Exception('User not found');
            }
            
            // Remove sensitive data
            unset($user['password_hash']);
            
            // If detail requested, add workload stats
            if (isset($_GET['detail'])) {
                // Get task counts
                $db = \Core\Database::getInstance();
                
                $activeTasks = $db->fetchColumn(
                    "SELECT COUNT(*) FROM task_assignees ta 
                     JOIN tasks t ON ta.task_id = t.id 
                     WHERE ta.user_id = ? AND t.status != 'done'",
                    [$userId]
                );
                
                $completedTasks = $db->fetchColumn(
                    "SELECT COUNT(*) FROM task_assignees ta 
                     JOIN tasks t ON ta.task_id = t.id 
                     WHERE ta.user_id = ? AND t.status = 'done'",
                    [$userId]
                );
                
                $overdueTasks = $db->fetchColumn(
                    "SELECT COUNT(*) FROM task_assignees ta 
                     JOIN tasks t ON ta.task_id = t.id 
                     WHERE ta.user_id = ? AND t.due_date < CURDATE() AND t.status != 'done'",
                    [$userId]
                );
                
                $user['active_tasks'] = (int)$activeTasks;
                $user['completed_tasks'] = (int)$completedTasks;
                $user['overdue_tasks'] = (int)$overdueTasks;
                
                $total = $user['active_tasks'] + $user['completed_tasks'];
                $user['completion_rate'] = $total > 0 ? round($user['completed_tasks'] / $total * 100) : 0;
            }
            
            echo json_encode(['success' => true, 'data' => $user]);
            break;
            
        case 'PUT':
            // Update user
            $input = json_decode(file_get_contents('php://input'), true) ?? [];
            
            $userId = $input['user_id'] ?? null;
            if (!$userId) {
                throw new Exception('user_id is required');
            }
            
            $user = $userModel->find($userId);
            if (!$user) {
                throw new Exception('User not found');
            }
            
            // Manager can only edit members, not other managers or admins
            if ($userRole === 'manager' && in_array($user['role'], ['admin', 'manager'])) {
                throw new Exception('Permission denied. Cannot edit admin or manager.');
            }
            
            $updateData = [];
            
            if (isset($input['full_name']) && !empty(trim($input['full_name']))) {
                $updateData['full_name'] = trim($input['full_name']);
            }
            
            if (isset($input['email']) && !empty(trim($input['email']))) {
                $email = trim($input['email']);
                // Check if email is taken by another user
                $existingUser = $userModel->findByEmail($email);
                if ($existingUser && $existingUser['id'] !== $userId) {
                    throw new Exception('Email đã được sử dụng');
                }
                $updateData['email'] = $email;
            }
            
            if (isset($input['role'])) {
                $validRoles = ['admin', 'manager', 'member', 'guest'];
                if (in_array($input['role'], $validRoles)) {
                    // Only admin can set admin role
                    if ($input['role'] === 'admin' && $userRole !== 'admin') {
                        throw new Exception('Only admin can assign admin role');
                    }
                    $updateData['role'] = $input['role'];
                }
            }
            
            if (isset($input['is_active'])) {
                $updateData['is_active'] = $input['is_active'] ? 1 : 0;
            }
            
            if (isset($input['department'])) {
                $updateData['department'] = trim($input['department']) ?: null;
            }
            
            if (isset($input['position'])) {
                $updateData['position'] = trim($input['position']) ?: null;
            }
            
            if (!empty($updateData)) {
                $userModel->update($userId, $updateData);
            }
            
            echo json_encode(['success' => true, 'message' => 'User updated']);
            break;
            
        case 'DELETE':
            // Deactivate user (soft delete)
            $userId = $_GET['id'] ?? null;
            if (!$userId) {
                throw new Exception('user_id is required');
            }
            
            // Cannot delete yourself
            if ($userId === $currentUserId) {
                throw new Exception('Cannot deactivate yourself');
            }
            
            $user = $userModel->find($userId);
            if (!$user) {
                throw new Exception('User not found');
            }
            
            // Manager cannot delete admin or other managers
            if ($userRole === 'manager' && in_array($user['role'], ['admin', 'manager'])) {
                throw new Exception('Permission denied');
            }
            
            // Soft delete - just deactivate
            $userModel->update($userId, ['is_active' => 0]);
            
            echo json_encode(['success' => true, 'message' => 'User deactivated']);
            break;
            
        default:
            http_response_code(405);
            echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    }
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
