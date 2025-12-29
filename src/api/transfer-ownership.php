<?php
/**
 * API: Transfer Project Ownership
 */
require_once __DIR__ . '/../bootstrap.php';

use App\Middleware\AuthMiddleware;
use Core\Database;
use Core\Session;

header('Content-Type: application/json');

AuthMiddleware::handle();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true) ?? [];

$projectId = $input['project_id'] ?? null;
$newOwnerId = $input['new_owner_id'] ?? null;

if (!$projectId || !$newOwnerId) {
    echo json_encode(['success' => false, 'error' => 'project_id and new_owner_id are required']);
    exit;
}

try {
    $db = Database::getInstance();
    $currentUserId = Session::get('user_id');
    $userRole = Session::get('user_role');
    
    // Check if current user is owner
    $currentMember = $db->fetchOne(
        "SELECT role FROM project_members WHERE project_id = ? AND user_id = ?",
        [$projectId, $currentUserId]
    );
    
    if (!$currentMember || $currentMember['role'] !== 'owner') {
        if ($userRole !== 'admin') {
            throw new Exception('Only project owner can transfer ownership');
        }
    }
    
    // Check if new owner is a member
    $newOwnerMember = $db->fetchOne(
        "SELECT pm.*, u.full_name FROM project_members pm 
         JOIN users u ON pm.user_id = u.id
         WHERE pm.project_id = ? AND pm.user_id = ?",
        [$projectId, $newOwnerId]
    );
    
    if (!$newOwnerMember) {
        throw new Exception('New owner must be a member of the project');
    }
    
    // Start transaction
    $db->beginTransaction();
    
    // Demote current owner to manager
    $db->update('project_members',
        ['role' => 'manager'],
        'project_id = ? AND user_id = ?',
        [$projectId, $currentUserId]
    );
    
    // Promote new owner
    $db->update('project_members',
        ['role' => 'owner'],
        'project_id = ? AND user_id = ?',
        [$projectId, $newOwnerId]
    );
    
    // Update project created_by (optional)
    $db->update('projects',
        ['created_by' => $newOwnerId, 'updated_at' => date('Y-m-d H:i:s')],
        'id = ?',
        [$projectId]
    );
    
    $db->commit();
    
    echo json_encode([
        'success' => true,
        'message' => "Đã chuyển quyền sở hữu cho {$newOwnerMember['full_name']}"
    ]);
    
} catch (Exception $e) {
    if (isset($db) && $db->getConnection()->inTransaction()) {
        $db->rollback();
    }
    
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
