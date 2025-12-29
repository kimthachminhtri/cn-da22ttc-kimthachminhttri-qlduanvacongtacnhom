<?php
/**
 * Migration: Create comments table if not exists
 */
require_once __DIR__ . '/../bootstrap.php';

use Core\Database;

$db = Database::getInstance();

echo "=== Migrate Comments Table ===\n\n";

try {
    // Check if table exists
    $tableExists = $db->fetchOne("SHOW TABLES LIKE 'comments'");
    
    if (!$tableExists) {
        echo "Creating comments table...\n";
        
        $sql = "CREATE TABLE `comments` (
            `id` VARCHAR(36) PRIMARY KEY,
            `entity_type` ENUM('task', 'document', 'project') NOT NULL DEFAULT 'task',
            `entity_id` VARCHAR(36) NOT NULL,
            `content` TEXT NOT NULL,
            `parent_id` VARCHAR(36) NULL,
            `created_by` VARCHAR(36) NOT NULL,
            `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
            `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            
            INDEX `idx_comments_entity` (`entity_type`, `entity_id`),
            INDEX `idx_comments_parent` (`parent_id`),
            INDEX `idx_comments_created_by` (`created_by`),
            INDEX `idx_comments_created_at` (`created_at`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        $db->query($sql);
        echo "✓ Comments table created successfully!\n";
    } else {
        echo "✓ Comments table already exists.\n";
    }
    
    // Check task_checklists table
    $checklistExists = $db->fetchOne("SHOW TABLES LIKE 'task_checklists'");
    
    if (!$checklistExists) {
        echo "\nCreating task_checklists table...\n";
        
        $sql = "CREATE TABLE `task_checklists` (
            `id` VARCHAR(36) PRIMARY KEY,
            `task_id` VARCHAR(36) NOT NULL,
            `title` VARCHAR(500) NOT NULL,
            `is_completed` TINYINT(1) NOT NULL DEFAULT 0,
            `position` INT UNSIGNED NOT NULL DEFAULT 0,
            `completed_at` DATETIME NULL,
            `completed_by` VARCHAR(36) NULL,
            `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
            
            INDEX `idx_tc_task` (`task_id`),
            INDEX `idx_tc_position` (`position`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        $db->query($sql);
        echo "✓ Task checklists table created successfully!\n";
    } else {
        echo "✓ Task checklists table already exists.\n";
    }
    
    echo "\n=== Migration completed! ===\n";
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
