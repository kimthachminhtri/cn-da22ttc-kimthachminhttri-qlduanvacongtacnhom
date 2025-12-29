<?php
/**
 * API: Delete Document/Folder
 */

ob_start();
require_once __DIR__ . '/../bootstrap.php';

ini_set('display_errors', 0);
ini_set('html_errors', 0);

use App\Middleware\AuthMiddleware;
use Core\Session;
use Core\Database;
use Core\Permission;

ob_end_clean();
header('Content-Type: application/json');

try {
    AuthMiddleware::handle();
    
    if ($_SERVER['REQUEST_METHOD'] !== 'DELETE' && $_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'error' => 'Method not allowed']);
        exit;
    }
    
    $docId = $_GET['id'] ?? $_POST['id'] ?? null;
    
    if (!$docId) {
        echo json_encode(['success' => false, 'error' => 'Document ID is required']);
        exit;
    }
    
    $db = Database::getInstance();
    $userId = Session::get('user_id');
    $userRole = Session::get('user_role', 'member');
    
    // Get document
    $doc = $db->fetchOne("SELECT * FROM documents WHERE id = ?", [$docId]);
    
    if (!$doc) {
        echo json_encode(['success' => false, 'error' => 'Document not found']);
        exit;
    }
    
    // Check permission
    $canDelete = Permission::can($userRole, 'documents.delete') || 
                 $doc['uploaded_by'] === $userId || 
                 $userRole === 'admin';
    
    if (!$canDelete) {
        echo json_encode(['success' => false, 'error' => 'Bạn không có quyền xóa tài liệu này']);
        exit;
    }
    
    // If it's a folder, delete all children first
    if ($doc['type'] === 'folder') {
        deleteFolder($db, $docId);
    } else {
        // If it's a file, delete the physical file
        if (!empty($doc['file_path'])) {
            $filePath = __DIR__ . '/../' . $doc['file_path'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            // Also check in uploads/documents
            $filePath2 = __DIR__ . '/../uploads/documents/' . basename($doc['file_path']);
            if (file_exists($filePath2)) {
                unlink($filePath2);
            }
        }
    }
    
    // Delete from database
    $db->delete('documents', 'id = ?', [$docId]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Đã xóa thành công'
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

// Helper function to delete folder and its contents recursively
function deleteFolder($db, $folderId) {
    // Get all children
    $children = $db->fetchAll("SELECT * FROM documents WHERE parent_id = ?", [$folderId]);
    
    foreach ($children as $child) {
        if ($child['type'] === 'folder') {
            deleteFolder($db, $child['id']);
        } else {
            // Delete physical file
            if (!empty($child['file_path'])) {
                $filePath = __DIR__ . '/../' . $child['file_path'];
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }
        }
        // Delete child record
        $db->delete('documents', 'id = ?', [$child['id']]);
    }
}
