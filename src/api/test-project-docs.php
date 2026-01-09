<?php
/**
 * Test Project Documents
 */
require_once __DIR__ . '/../bootstrap.php';
require_once BASE_PATH . '/includes/csrf.php';
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    csrf_require();
}

header('Content-Type: application/json');

$projectId = $_GET['project_id'] ?? null;

$db = \Core\Database::getInstance();

if ($projectId) {
    // Lấy tài liệu của project cụ thể
    $docs = $db->fetchAll(
        "SELECT id, name, type, project_id, parent_id FROM documents WHERE project_id = ?",
        [$projectId]
    );
    echo json_encode([
        'project_id' => $projectId,
        'count' => count($docs),
        'documents' => $docs
    ]);
} else {
    // Lấy tất cả tài liệu với project_id
    $docs = $db->fetchAll(
        "SELECT id, name, type, project_id, parent_id FROM documents ORDER BY project_id, name"
    );
    echo json_encode([
        'total' => count($docs),
        'documents' => $docs
    ]);
}
