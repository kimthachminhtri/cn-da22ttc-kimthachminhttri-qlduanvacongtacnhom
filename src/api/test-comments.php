<?php
/**
 * Test Comments API - Debug
 */

ini_set('display_errors', 0);
error_reporting(E_ALL);

header('Content-Type: application/json');

try {
    require_once __DIR__ . '/../bootstrap.php';
    require_once BASE_PATH . '/includes/csrf.php';
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        csrf_require();
    }
    
    $db = \Core\Database::getInstance();
    
    // Check if comments table exists
    $tableExists = $db->fetchOne("SHOW TABLES LIKE 'comments'");
    
    if (!$tableExists) {
        // Create table
        $sql = "CREATE TABLE IF NOT EXISTS `comments` (
            `id` VARCHAR(36) PRIMARY KEY,
            `entity_type` ENUM('task', 'document', 'project') NOT NULL DEFAULT 'task',
            `entity_id` VARCHAR(36) NOT NULL,
            `content` TEXT NOT NULL,
            `parent_id` VARCHAR(36) NULL,
            `created_by` VARCHAR(36) NOT NULL,
            `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
            `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX `idx_comments_entity` (`entity_type`, `entity_id`),
            INDEX `idx_comments_created_by` (`created_by`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        $db->query($sql);
        echo json_encode(['success' => true, 'message' => 'Comments table created']);
    } else {
        // Count comments
        $count = $db->fetchColumn("SELECT COUNT(*) FROM comments");
        echo json_encode([
            'success' => true, 
            'message' => 'Comments table exists',
            'count' => $count
        ]);
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
