<?php
/**
 * API: Toggle Star Document
 */

require_once __DIR__ . '/../includes/config.php';
require_once BASE_PATH . '/includes/csrf.php';
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    csrf_require();
}

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$docId = $input['document_id'] ?? $_POST['document_id'] ?? null;

if (!$docId) {
    echo json_encode(['success' => false, 'error' => 'document_id is required']);
    exit;
}

try {
    $db = Database::getInstance();
    
    // Get current star status
    $doc = $db->fetchOne("SELECT id, is_starred FROM documents WHERE id = ?", [$docId]);
    
    if (!$doc) {
        echo json_encode(['success' => false, 'error' => 'Document not found']);
        exit;
    }
    
    // Toggle star
    $newStatus = $doc['is_starred'] ? 0 : 1;
    $db->update('documents', ['is_starred' => $newStatus], 'id = ?', [$docId]);
    
    echo json_encode([
        'success' => true,
        'starred' => (bool)$newStatus,
        'message' => $newStatus ? 'Đã đánh dấu' : 'Đã bỏ đánh dấu'
    ]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
