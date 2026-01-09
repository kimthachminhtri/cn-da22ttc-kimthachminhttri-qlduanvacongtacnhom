<?php
/**
 * Migration: Security Fixes
 * - Add version column for optimistic locking
 */

require_once __DIR__ . '/../bootstrap.php';

use Core\Database;

echo "=== Security Fixes Migration ===\n\n";

try {
    $db = Database::getInstance();
    
    // Check if version column exists
    $columns = $db->fetchAll("SHOW COLUMNS FROM tasks LIKE 'version'");
    
    if (empty($columns)) {
        echo "Adding 'version' column to tasks table...\n";
        $db->query("ALTER TABLE `tasks` ADD COLUMN `version` INT UNSIGNED NOT NULL DEFAULT 1");
        echo "✓ Column 'version' added successfully\n";
        
        // Update existing records
        $db->query("UPDATE `tasks` SET `version` = 1 WHERE `version` IS NULL OR `version` = 0");
        echo "✓ Existing records updated\n";
    } else {
        echo "✓ Column 'version' already exists\n";
    }
    
    echo "\n=== Migration completed successfully ===\n";
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
