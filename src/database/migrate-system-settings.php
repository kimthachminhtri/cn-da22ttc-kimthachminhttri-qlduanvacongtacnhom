<?php
/**
 * Migration: Create system_settings table
 */

require_once __DIR__ . '/../includes/config.php';

try {
    $db = Database::getInstance();
    
    echo "<h2>Migration: System Settings Table</h2>";
    
    // Check if table exists
    $tables = $db->fetchAll("SHOW TABLES LIKE 'system_settings'");
    
    if (empty($tables)) {
        echo "<p>Creating system_settings table...</p>";
        
        $sql = "CREATE TABLE `system_settings` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `setting_key` VARCHAR(100) NOT NULL UNIQUE,
            `setting_value` TEXT NULL,
            `setting_group` VARCHAR(50) DEFAULT 'general',
            `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
            `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            
            INDEX `idx_settings_group` (`setting_group`),
            INDEX `idx_settings_key` (`setting_key`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        $db->query($sql);
        
        echo "<p style='color: green;'>✅ Created system_settings table!</p>";
        
        // Add default settings
        $defaultSettings = [
            ['app_name', 'TaskFlow', 'general'],
            ['app_url', 'http://localhost/php', 'general'],
            ['timezone', 'Asia/Ho_Chi_Minh', 'general'],
            ['language', 'vi', 'general'],
            ['allow_registration', '1', 'general'],
            ['smtp_host', '', 'email'],
            ['smtp_port', '587', 'email'],
            ['smtp_username', '', 'email'],
            ['smtp_password', '', 'email'],
            ['mail_from', '', 'email'],
            ['mail_from_name', 'TaskFlow', 'email'],
        ];
        
        foreach ($defaultSettings as $setting) {
            $db->insert('system_settings', [
                'setting_key' => $setting[0],
                'setting_value' => $setting[1],
                'setting_group' => $setting[2],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }
        
        echo "<p style='color: green;'>✅ Added default settings!</p>";
        
    } else {
        echo "<p style='color: orange;'>⚠️ System settings table already exists!</p>";
    }
    
    // Show current settings
    echo "<h3>Current settings:</h3>";
    $settings = $db->fetchAll("SELECT * FROM system_settings ORDER BY setting_group, setting_key");
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>Key</th><th>Value</th><th>Group</th></tr>";
    foreach ($settings as $s) {
        $value = $s['setting_key'] === 'smtp_password' ? '••••••••' : htmlspecialchars($s['setting_value']);
        echo "<tr><td>{$s['setting_key']}</td><td>{$value}</td><td>{$s['setting_group']}</td></tr>";
    }
    echo "</table>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<p><a href='../index.php'>← Back to Home</a></p>";
