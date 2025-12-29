<?php
/**
 * API: Project Members Management
 */
require_once __DIR__ . '/../bootstrap.php';

use App\Models\Project;
use App\Middleware\AuthMiddleware;
use Core\Database;
use Core\Session;

header('Content-Type: application/json');

AuthMiddleware::handle();

$method = $_SERVER['REQUEST_METHOD'];
$currentUserId = Session::get('user_id');
$userRole = Session::get('user_role');

try {
    $db = Database::getInstance();
    $projectModel = new Project();
    
    switch ($method) {
        case 'GET':
            // Lấy danh sách thành viên của dự án
            $projectId = $_GET['project_id'] ?? null;
            
            if (!$projectId) {
                throw new Exception('project_id is required');
            }
            
            $members = $projectModel->getMembers($projectId);
            
            // Format response
            $formatted = array_map(function($m) {
                return [
                    'id' => $m['id'],
                    'name' => $m['full_name'],
                    'email' => $m['email'],
                    'avatar' => $m['avatar_url'],
                    'position' => $m['position'],
                    'department' => $m['department'],
                    'project_role' => $m['project_role'] ?? 'member',
                    'joined_at' => $m['joined_at'],
                ];
            }, $members);
            
            echo json_encode([
                'success' => true,
                'data' => $formatted
            ]);
            break;
            
        case 'POST':
            // Thêm thành viên vào dự án
            $input = json_decode(file_get_contents('php://input'), true);
            
            $projectId = $input['project_id'] ?? null;
            $userId = $input['user_id'] ?? null;
            $role = $input['role'] ?? 'member';
            
            if (!$projectId || !$userId) {
                throw new Exception('project_id and user_id are required');
            }
            
            // Validate role
            $validRoles = ['owner', 'manager', 'member', 'viewer'];
            if (!in_array($role, $validRoles)) {
                throw new Exception('Invalid role. Must be: owner, manager, member, or viewer');
            }
            
            // Check permission (only owner/manager can add members)
            $currentMember = $db->fetchOne(
                "SELECT role FROM project_members WHERE project_id = ? AND user_id = ?",
                [$projectId, $currentUserId]
            );
            
            if (!$currentMember || !in_array($currentMember['role'], ['owner', 'manager'])) {
                if ($userRole !== 'admin') {
                    throw new Exception('Permission denied. Only project owner/manager can add members.');
                }
            }
            
            // Check if user exists
            $user = $db->fetchOne("SELECT id, full_name FROM users WHERE id = ?", [$userId]);
            if (!$user) {
                throw new Exception('User not found');
            }
            
            // Check if already a member
            $existing = $db->fetchOne(
                "SELECT * FROM project_members WHERE project_id = ? AND user_id = ?",
                [$projectId, $userId]
            );
            
            if ($existing) {
                throw new Exception('User is already a member of this project');
            }
            
            // Add member
            $db->insert('project_members', [
                'project_id' => $projectId,
                'user_id' => $userId,
                'role' => $role,
                'joined_at' => date('Y-m-d H:i:s'),
            ]);
            
            echo json_encode([
                'success' => true,
                'message' => "Đã thêm {$user['full_name']} vào dự án"
            ]);
            break;
            
        case 'PUT':
            // Cập nhật quyền thành viên
            $input = json_decode(file_get_contents('php://input'), true);
            
            $projectId = $input['project_id'] ?? null;
            $userId = $input['user_id'] ?? null;
            $newRole = $input['role'] ?? null;
            
            if (!$projectId || !$userId || !$newRole) {
                throw new Exception('project_id, user_id, and role are required');
            }
            
            // Validate role
            $validRoles = ['owner', 'manager', 'member', 'viewer'];
            if (!in_array($newRole, $validRoles)) {
                throw new Exception('Invalid role');
            }
            
            // Check permission (only owner can change roles)
            $currentMember = $db->fetchOne(
                "SELECT role FROM project_members WHERE project_id = ? AND user_id = ?",
                [$projectId, $currentUserId]
            );
            
            if (!$currentMember || $currentMember['role'] !== 'owner') {
                if ($userRole !== 'admin') {
                    throw new Exception('Permission denied. Only project owner can change member roles.');
                }
            }
            
            // Cannot change own role if owner
            if ($userId === $currentUserId && $currentMember['role'] === 'owner') {
                throw new Exception('Cannot change your own role as owner');
            }
            
            // Update role
            $db->update('project_members', 
                ['role' => $newRole],
                'project_id = ? AND user_id = ?',
                [$projectId, $userId]
            );
            
            echo json_encode([
                'success' => true,
                'message' => 'Đã cập nhật quyền thành viên'
            ]);
            break;
            
        case 'DELETE':
            // Xóa thành viên khỏi dự án
            $projectId = $_GET['project_id'] ?? null;
            $userId = $_GET['user_id'] ?? null;
            
            if (!$projectId || !$userId) {
                throw new Exception('project_id and user_id are required');
            }
            
            // Check permission
            $currentMember = $db->fetchOne(
                "SELECT role FROM project_members WHERE project_id = ? AND user_id = ?",
                [$projectId, $currentUserId]
            );
            
            // Can remove self, or owner/manager can remove others
            $canRemove = false;
            if ($userId === $currentUserId) {
                // Cannot remove self if owner
                $targetMember = $db->fetchOne(
                    "SELECT role FROM project_members WHERE project_id = ? AND user_id = ?",
                    [$projectId, $userId]
                );
                if ($targetMember && $targetMember['role'] === 'owner') {
                    throw new Exception('Owner cannot leave the project. Transfer ownership first.');
                }
                $canRemove = true;
            } elseif ($currentMember && in_array($currentMember['role'], ['owner', 'manager'])) {
                $canRemove = true;
            } elseif ($userRole === 'admin') {
                $canRemove = true;
            }
            
            if (!$canRemove) {
                throw new Exception('Permission denied');
            }
            
            // Get user info for message
            $user = $db->fetchOne("SELECT full_name FROM users WHERE id = ?", [$userId]);
            
            // Remove member
            $deleted = $db->delete('project_members', 
                'project_id = ? AND user_id = ?', 
                [$projectId, $userId]
            );
            
            if ($deleted) {
                echo json_encode([
                    'success' => true,
                    'message' => "Đã xóa {$user['full_name']} khỏi dự án"
                ]);
            } else {
                throw new Exception('Member not found in project');
            }
            break;
            
        default:
            http_response_code(405);
            echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    }
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
