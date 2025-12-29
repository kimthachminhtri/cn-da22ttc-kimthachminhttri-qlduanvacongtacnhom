<?php
/**
 * API: Update/Delete Project
 */
require_once __DIR__ . '/../bootstrap.php';

use App\Models\Project;
use App\Middleware\AuthMiddleware;
use Core\Database;
use Core\Session;

header('Content-Type: application/json');

AuthMiddleware::handle();

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST' || $method === 'PUT') {
    $input = json_decode(file_get_contents('php://input'), true) ?? [];
    $_POST = array_merge($_POST, $input);
    
    $projectId = $_POST['project_id'] ?? null;
    
    if (!$projectId) {
        echo json_encode(['success' => false, 'error' => 'project_id is required']);
        exit;
    }
    
    try {
        $projectModel = new Project();
        $project = $projectModel->find($projectId);
        
        if (!$project) {
            echo json_encode(['success' => false, 'error' => 'Project not found']);
            exit;
        }
        
        // Check permission - owner/manager of project or system admin/manager
        $db = Database::getInstance();
        $userId = Session::get('user_id');
        $userRole = Session::get('user_role');
        
        $memberRole = $db->fetchOne(
            "SELECT role FROM project_members WHERE project_id = ? AND user_id = ?",
            [$projectId, $userId]
        );
        
        $canEdit = false;
        if ($memberRole && in_array($memberRole['role'], ['owner', 'manager'])) {
            $canEdit = true;
        } elseif (in_array($userRole, ['admin', 'manager'])) {
            $canEdit = true;
        }
        
        if (!$canEdit) {
            echo json_encode(['success' => false, 'error' => 'Permission denied']);
            exit;
        }
        
        $updateData = [];
        
        if (isset($input['name']) && !empty(trim($input['name']))) {
            $updateData['name'] = trim($input['name']);
        }
        if (isset($input['description'])) {
            $updateData['description'] = trim($input['description']);
        }
        if (isset($input['status'])) {
            $validStatuses = ['planning', 'active', 'on_hold', 'completed', 'cancelled'];
            if (in_array($input['status'], $validStatuses)) {
                $updateData['status'] = $input['status'];
            }
        }
        if (isset($input['priority'])) {
            $validPriorities = ['low', 'medium', 'high', 'urgent'];
            if (in_array($input['priority'], $validPriorities)) {
                $updateData['priority'] = $input['priority'];
            }
        }
        if (isset($input['color'])) {
            $updateData['color'] = $input['color'];
        }
        if (isset($input['start_date'])) {
            $updateData['start_date'] = $input['start_date'] ?: null;
        }
        if (isset($_POST['end_date'])) {
            $updateData['end_date'] = $_POST['end_date'] ?: null;
        }
        
        if (!empty($updateData)) {
            $projectModel->update($projectId, $updateData);
        }
        
        echo json_encode([
            'success' => true,
            'message' => 'Project updated successfully'
        ]);
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    
} elseif ($method === 'DELETE') {
    $projectId = $_GET['project_id'] ?? null;
    
    if (!$projectId) {
        echo json_encode(['success' => false, 'error' => 'project_id is required']);
        exit;
    }
    
    try {
        $projectModel = new Project();
        $project = $projectModel->find($projectId);
        
        if (!$project) {
            echo json_encode(['success' => false, 'error' => 'Project not found']);
            exit;
        }
        
        // Only project owner or system admin can delete
        $db = Database::getInstance();
        $userId = Session::get('user_id');
        $userRole = Session::get('user_role');
        
        $memberRole = $db->fetchOne(
            "SELECT role FROM project_members WHERE project_id = ? AND user_id = ?",
            [$projectId, $userId]
        );
        
        $canDelete = false;
        if ($memberRole && $memberRole['role'] === 'owner') {
            $canDelete = true;
        } elseif ($userRole === 'admin') {
            $canDelete = true;
        }
        
        if (!$canDelete) {
            echo json_encode(['success' => false, 'error' => 'Permission denied. Only project owner can delete.']);
            exit;
        }
        
        // Không cho phép xóa dự án đã hoàn thành
        if ($project['status'] === 'completed') {
            echo json_encode(['success' => false, 'error' => 'Không thể xóa dự án đã hoàn thành. Vui lòng đổi trạng thái trước khi xóa.']);
            exit;
        }
        
        $projectModel->delete($projectId);
        
        echo json_encode([
            'success' => true,
            'message' => 'Project deleted successfully'
        ]);
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
}
