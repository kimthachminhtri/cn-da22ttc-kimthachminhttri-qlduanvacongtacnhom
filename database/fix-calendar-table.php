<?php
/**
 * Fix calendar_events table structure
 */

require_once __DIR__ . '/../includes/config.php';

try {
    $db = Database::getInstance();
    
    echo "<h2>Fix Calendar Events Table</h2>";
    
    // Disable foreign key checks
    $db->query("SET FOREIGN_KEY_CHECKS = 0");
    
    // Drop and recreate table
    echo "<p>Dropping old table...</p>";
    $db->query("DROP TABLE IF EXISTS calendar_events");
    
    echo "<p>Creating new table...</p>";
    $sql = "CREATE TABLE `calendar_events` (
        `id` VARCHAR(36) PRIMARY KEY,
        `title` VARCHAR(255) NOT NULL,
        `description` TEXT NULL,
        `start_date` DATE NOT NULL,
        `end_date` DATE NULL,
        `start_time` TIME NULL,
        `end_time` TIME NULL,
        `is_all_day` TINYINT(1) NOT NULL DEFAULT 1,
        `color` VARCHAR(7) NOT NULL DEFAULT '#6366f1',
        `location` VARCHAR(255) NULL,
        `reminder_minutes` INT NULL,
        `project_id` VARCHAR(36) NULL,
        `created_by` VARCHAR(36) NULL,
        `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
        `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        
        INDEX `idx_calendar_dates` (`start_date`, `end_date`),
        INDEX `idx_calendar_project` (`project_id`),
        INDEX `idx_calendar_created_by` (`created_by`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    $db->query($sql);
    
    // Re-enable foreign key checks
    $db->query("SET FOREIGN_KEY_CHECKS = 1");
    
    echo "<p style='color: green;'>✅ Created calendar_events table!</p>";
    
    // Show final structure
    echo "<h3>Table structure:</h3>";
    $structure = $db->fetchAll("DESCRIBE calendar_events");
    echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
    echo "<tr style='background: #f0f0f0;'><th style='padding: 5px;'>Field</th><th style='padding: 5px;'>Type</th><th style='padding: 5px;'>Null</th><th style='padding: 5px;'>Default</th></tr>";
    foreach ($structure as $col) {
        echo "<tr>";
        echo "<td style='padding: 5px;'>{$col['Field']}</td>";
        echo "<td style='padding: 5px;'>{$col['Type']}</td>";
        echo "<td style='padding: 5px;'>{$col['Null']}</td>";
        echo "<td style='padding: 5px;'>{$col['Default']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<p style='color: green; font-weight: bold;'>✅ Done! You can now create events.</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<p><a href='../calendar.php'>← Back to Calendar</a></p>";
