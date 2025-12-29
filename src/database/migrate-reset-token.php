<?php
/**
 * Migration: Add reset_token columns to users table
 * Run: http://localhost/php/database/migrate-reset-token.php
 */

require_once __DIR__ . '/../bootstrap.php';

use Core\Database;

echo "<h2>Migration: Add Reset Token Columns</h2>";

try {
    $db = Database::getInstance();
    
    // Check if columns exist
    $columns = $db->fetchAll("SHOW COLUMNS FROM users LIKE 'reset_token%'");
    
    if (count($columns) >= 2) {
        echo "<p style='color:green'>✓ Columns already exist. No migration needed.</p>";
    } else {
        // Add columns
        $db->query("ALTER TABLE `users` 
            ADD COLUMN IF NOT EXISTS `reset_token` VARCHAR(64) NULL,
            ADD COLUMN IF NOT EXISTS `reset_token_expiry` DATETIME NULL");
        
        // Add index
        try {
            $db->query("CREATE INDEX `idx_users_reset_token` ON `users` (`reset_token`)");
        } catch (Exception $e) {
            // Index might already exist
        }
        
        echo "<p style='color:green'>✓ Migration completed successfully!</p>";
    }
    
    // Show current columns
    echo "<h3>Current users table columns:</h3>";
    $allColumns = $db->fetchAll("SHOW COLUMNS FROM users");
    echo "<ul>";
    foreach ($allColumns as $col) {
        echo "<li>{$col['Field']} - {$col['Type']}</li>";
    }
    echo "</ul>";
    
} catch (Exception $e) {
    echo "<p style='color:red'>✗ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<p><a href='/php/login.php'>← Back to Login</a></p>";
