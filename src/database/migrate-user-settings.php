<?php
/**
 * Migration: Create user_settings table
 */

require_once __DIR__ . '/../includes/config.php';

try {
    $db = Database::getInstance();
    
    echo "<h2>Migration: User Settings Table</h2>";
    
    // Check if table exists
    $tables = $db->fetchAll("SHOW TABLES LIKE 'user_settings'");
    
    if (empty($tables)) {
        echo "<p>Creating user_settings table...</p>";
        
        $sql = "CREATE TABLE `user_settings` (
            `user_id` VARCHAR(36) PRIMARY KEY,
            `theme` ENUM('light', 'dark', 'system') NOT NULL DEFAULT 'light',
            `language` VARCHAR(5) NOT NULL DEFAULT 'vi',
            `timezone` VARCHAR(50) NOT NULL DEFAULT 'Asia/Ho_Chi_Minh',
            `notification_email` TINYINT(1) NOT NULL DEFAULT 1,
            `notification_push` TINYINT(1) NOT NULL DEFAULT 1,
            `notification_task_assigned` TINYINT(1) NOT NULL DEFAULT 1,
            `notification_task_due` TINYINT(1) NOT NULL DEFAULT 1,
            `notification_comment` TINYINT(1) NOT NULL DEFAULT 1,
            `notification_mention` TINYINT(1) NOT NULL DEFAULT 1,
            `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
            `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            
            CONSTRAINT `fk_user_settings_user` FOREIGN KEY (`user_id`) 
                REFERENCES `users`(`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        $db->query($sql);
        
        echo "<p style='color: green;'>✅ Created user_settings table!</p>";
        
        // Create default settings for existing users
        echo "<p>Creating default settings for existing users...</p>";
        
        $users = $db->fetchAll("SELECT id FROM users");
        $count = 0;
        
        foreach ($users as $user) {
            try {
                $db->insert('user_settings', [
                    'user_id' => $user['id'],
                    'theme' => 'light',
                    'language' => 'vi',
                    'timezone' => 'Asia/Ho_Chi_Minh',
                ]);
                $count++;
            } catch (Exception $e) {
                // Skip if already exists
            }
        }
        
        echo "<p style='color: green;'>✅ Created settings for $count users!</p>";
        
    } else {
        echo "<p style='color: orange;'>⚠️ User settings table already exists!</p>";
    }
    
    // Show current structure
    echo "<h3>Current user_settings table structure:</h3>";
    $structure = $db->fetchAll("DESCRIBE user_settings");
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    foreach ($structure as $col) {
        echo "<tr>";
        echo "<td>{$col['Field']}</td>";
        echo "<td>{$col['Type']}</td>";
        echo "<td>{$col['Null']}</td>";
        echo "<td>{$col['Key']}</td>";
        echo "<td>{$col['Default']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Show settings count
    $count = $db->fetchOne("SELECT COUNT(*) as cnt FROM user_settings");
    echo "<p>Total user settings: " . ($count['cnt'] ?? 0) . "</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<p><a href='../settings.php'>← Back to Settings</a></p>";
