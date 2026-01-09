<?php
/**
 * API: Bulk Operations
 * 
 * Thao tác hàng loạt cho:
 * - Tasks: update status, priority, assignee, delete
 * - Projects: update status, delete, archive
 * - Users: activate/deactivate, role change (Admin only)
 * - Documents: delete, move folder
 */

require_once __DIR__ . '/../bootstrap.php';
require_once BASE_PATH . '/includes/csrf.php';

use App\Middleware\AuthMiddleware;
use Core\Database;
use Core\Session;
use Core\Logger;

header('Content-Type: application/json');

AuthMiddleware::handle();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

$userId = Session::get('user_id');
$userRole = Session::get('user_role', 'member');
$db = Database::getInstance();

try {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        throw new Exception('Invalid JSON input');
    }
    
    $operation = $input['operation'] ?? '';
    $entity = $input['entity'] ?? '';
    $ids = $input['ids'] ?? [];
    $data = $input['data'] ?? [];
    
    if (empty($operation) || empty($entity) || empty($ids)) {
        throw new Exception('Missing required parameters: operation, entity, ids');
    }
    
    if (!is_array($ids) || count($ids) === 0) {
        throw new Exception('IDs must be a non-empty array');
    }
    
    // Limit bulk operations
    if (count($ids) > 100) {
        throw new Exception('Maximum 100 items per bulk operation');
    }
    
    $db->beginTransaction();
    $results = ['success' => 0, 'failed' => 0, 'errors' => []];
    
    switch ($entity) {
        case 'tasks':
            $results = handleTasksBulk($db, $operation, $ids, $data, $userId, $userRole);
            break;
        case 'projects':
            $results = handleProjectsBulk($db, $operation, $ids, $data, $userId, $userRole);
            break;
        case 'users':
            if ($userRole !== 'admin') {
                throw new Exception('Only admin can perform bulk operations on users');
            }
            $results = handleUsersBulk($db, $operation, $ids, $data, $userId);
            break;
        case 'documents':
            $results = handleDocumentsBulk($db, $operation, $ids, $data, $userId, $userRole);
            break;
        default:
            throw new Exception('Invalid entity type: ' . $entity);
    }
    
    $db->commit();
    
    Logger::info("Bulk operation completed", [
        'user_id' => $userId,
        'operation' => $operation,
        'entity' => $entity,
        'count' => count($ids),
        'success' => $results['success'],
        'failed' => $results['failed']
    ]);
    
    echo json_encode([
        'success' => true,
        'message' => "Đã xử lý {$results['success']} mục thành công" . 
                     ($results['failed'] > 0 ? ", {$results['failed']} mục thất bại" : ""),
        'results' => $results
    ]);
    
} catch (Exception $e) {
    if ($db->inTransaction()) {
        $db->rollback();
    }
    
    Logger::error("Bulk operation failed", [
        'user_id' => $userId,
        'error' => $e->getMessage()
    ]);
    
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

// ========================================
// TASKS BULK OPERATIONS
// ========================================
function handleTasksBulk($db, $operation, $ids, $data, $userId, $userRole) {
    $results = ['success' => 0, 'failed' => 0, 'errors' => []];
    
    foreach ($ids as $taskId) {
        try {
            $task = $db->fetchOne(
                "SELECT t.*, p.id as project_id FROM tasks t 
                 LEFT JOIN projects p ON t.project_id = p.id 
                 WHERE t.id = ?",
                [$taskId]
            );
            
            if (!$task) {
                throw new Exception("Không tìm thấy");
            }
            
            // Check permissions
            if ($userRole !== 'admin') {
                $hasAccess = $db->fetchOne(
                    "SELECT 1 FROM project_members WHERE project_id = ? AND user_id = ?
                     UNION SELECT 1 FROM task_assignees WHERE task_id = ? AND user_id = ?",
                    [$task['project_id'], $userId, $taskId, $userId]
                );
                
                if (!$hasAccess && $task['created_by'] !== $userId) {
                    throw new Exception("Không có quyền");
                }
            }
            
            switch ($operation) {
                case 'update_status':
                    $status = $data['status'] ?? '';
                    if (!in_array($status, ['todo', 'in_progress', 'in_review', 'done', 'backlog'])) {
                        throw new Exception('Trạng thái không hợp lệ');
                    }
                    $updateData = ['status' => $status, 'updated_at' => date('Y-m-d H:i:s')];
                    if ($status === 'done') {
                        $updateData['completed_at'] = date('Y-m-d H:i:s');
                    }
                    $db->execute(
                        "UPDATE tasks SET status = ?, updated_at = NOW()" . ($status === 'done' ? ", completed_at = NOW()" : "") . " WHERE id = ?",
                        [$status, $taskId]
                    );
                    break;
                    
                case 'update_priority':
                    $priority = $data['priority'] ?? '';
                    if (!in_array($priority, ['low', 'medium', 'high', 'urgent'])) {
                        throw new Exception('Độ ưu tiên không hợp lệ');
                    }
                    $db->execute("UPDATE tasks SET priority = ?, updated_at = NOW() WHERE id = ?", [$priority, $taskId]);
                    break;
                    
                case 'assign_user':
                    $assigneeId = $data['assignee_id'] ?? '';
                    if (empty($assigneeId)) throw new Exception('Thiếu assignee_id');
                    
                    $db->execute("DELETE FROM task_assignees WHERE task_id = ?", [$taskId]);
                    $db->execute(
                        "INSERT INTO task_assignees (task_id, user_id, assigned_by, assigned_at) VALUES (?, ?, ?, NOW())",
                        [$taskId, $assigneeId, $userId]
                    );
                    break;
                    
                case 'delete':
                    if ($userRole !== 'admin' && $task['created_by'] !== $userId) {
                        throw new Exception("Chỉ admin hoặc người tạo mới có thể xóa");
                    }
                    $db->execute("DELETE FROM task_assignees WHERE task_id = ?", [$taskId]);
                    $db->execute("DELETE FROM task_comments WHERE task_id = ?", [$taskId]);
                    $db->execute("DELETE FROM tasks WHERE id = ?", [$taskId]);
                    break;
                    
                default:
                    throw new Exception('Thao tác không hợp lệ');
            }
            
            $results['success']++;
        } catch (Exception $e) {
            $results['failed']++;
            $results['errors'][] = "Task $taskId: " . $e->getMessage();
        }
    }
    
    return $results;
}

// ========================================
// PROJECTS BULK OPERATIONS
// ========================================
function handleProjectsBulk($db, $operation, $ids, $data, $userId, $userRole) {
    $results = ['success' => 0, 'failed' => 0, 'errors' => []];
    
    foreach ($ids as $projectId) {
        try {
            if ($userRole !== 'admin') {
                $member = $db->fetchOne(
                    "SELECT role FROM project_members WHERE project_id = ? AND user_id = ?",
                    [$projectId, $userId]
                );
                if (!$member || !in_array($member['role'], ['owner', 'manager'])) {
                    throw new Exception("Không có quyền");
                }
            }
            
            switch ($operation) {
                case 'update_status':
                    $status = $data['status'] ?? '';
                    if (!in_array($status, ['planning', 'active', 'on_hold', 'completed', 'cancelled'])) {
                        throw new Exception('Trạng thái không hợp lệ');
                    }
                    $db->execute("UPDATE projects SET status = ?, updated_at = NOW() WHERE id = ?", [$status, $projectId]);
                    break;
                    
                case 'archive':
                    $db->execute("UPDATE projects SET status = 'completed', updated_at = NOW() WHERE id = ?", [$projectId]);
                    break;
                    
                case 'delete':
                    if ($userRole !== 'admin') {
                        $isOwner = $db->fetchOne(
                            "SELECT 1 FROM project_members WHERE project_id = ? AND user_id = ? AND role = 'owner'",
                            [$projectId, $userId]
                        );
                        if (!$isOwner) throw new Exception("Chỉ owner mới có thể xóa");
                    }
                    $db->execute("DELETE FROM projects WHERE id = ?", [$projectId]);
                    break;
                    
                default:
                    throw new Exception('Thao tác không hợp lệ');
            }
            
            $results['success']++;
        } catch (Exception $e) {
            $results['failed']++;
            $results['errors'][] = "Project $projectId: " . $e->getMessage();
        }
    }
    
    return $results;
}

// ========================================
// USERS BULK OPERATIONS (Admin only)
// ========================================
function handleUsersBulk($db, $operation, $ids, $data, $userId) {
    $results = ['success' => 0, 'failed' => 0, 'errors' => []];
    
    foreach ($ids as $targetUserId) {
        try {
            if ($targetUserId === $userId) {
                throw new Exception("Không thể thao tác trên chính mình");
            }
            
            switch ($operation) {
                case 'activate':
                    $db->execute("UPDATE users SET is_active = 1, updated_at = NOW() WHERE id = ?", [$targetUserId]);
                    break;
                    
                case 'deactivate':
                    $db->execute("UPDATE users SET is_active = 0, updated_at = NOW() WHERE id = ?", [$targetUserId]);
                    break;
                    
                case 'change_role':
                    $role = $data['role'] ?? '';
                    if (!in_array($role, ['admin', 'manager', 'member', 'guest'])) {
                        throw new Exception('Role không hợp lệ');
                    }
                    $db->execute("UPDATE users SET role = ?, updated_at = NOW() WHERE id = ?", [$role, $targetUserId]);
                    break;
                    
                case 'delete':
                    $hasProjects = $db->fetchOne(
                        "SELECT 1 FROM project_members WHERE user_id = ? AND role = 'owner' LIMIT 1",
                        [$targetUserId]
                    );
                    if ($hasProjects) {
                        throw new Exception("User đang sở hữu dự án, cần chuyển quyền trước");
                    }
                    $db->execute("DELETE FROM users WHERE id = ?", [$targetUserId]);
                    break;
                    
                default:
                    throw new Exception('Thao tác không hợp lệ');
            }
            
            $results['success']++;
        } catch (Exception $e) {
            $results['failed']++;
            $results['errors'][] = "User $targetUserId: " . $e->getMessage();
        }
    }
    
    return $results;
}

// ========================================
// DOCUMENTS BULK OPERATIONS
// ========================================
function handleDocumentsBulk($db, $operation, $ids, $data, $userId, $userRole) {
    $results = ['success' => 0, 'failed' => 0, 'errors' => []];
    
    foreach ($ids as $documentId) {
        try {
            $document = $db->fetchOne("SELECT * FROM documents WHERE id = ?", [$documentId]);
            
            if (!$document) {
                throw new Exception("Không tìm thấy");
            }
            
            if ($userRole !== 'admin' && $document['uploaded_by'] !== $userId) {
                $isMember = $db->fetchOne(
                    "SELECT 1 FROM project_members WHERE project_id = ? AND user_id = ?",
                    [$document['project_id'], $userId]
                );
                if (!$isMember) throw new Exception("Không có quyền");
            }
            
            switch ($operation) {
                case 'delete':
                    $filePath = BASE_PATH . '/' . $document['file_path'];
                    if (file_exists($filePath)) {
                        unlink($filePath);
                    }
                    $db->execute("DELETE FROM documents WHERE id = ?", [$documentId]);
                    break;
                    
                case 'move':
                    $newProjectId = $data['project_id'] ?? '';
                    if (empty($newProjectId)) throw new Exception('Thiếu project_id');
                    $db->execute("UPDATE documents SET project_id = ?, updated_at = NOW() WHERE id = ?", [$newProjectId, $documentId]);
                    break;
                    
                default:
                    throw new Exception('Thao tác không hợp lệ');
            }
            
            $results['success']++;
        } catch (Exception $e) {
            $results['failed']++;
            $results['errors'][] = "Document $documentId: " . $e->getMessage();
        }
    }
    
    return $results;
}
