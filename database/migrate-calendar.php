<?php
/**
 * Migration: Create calendar_events table
 */

require_once __DIR__ . '/../includes/config.php';

try {
    $db = Database::getInstance();
    
    echo "<h2>Migration: Calendar Events Table</h2>";
    
    // Check if table exists
    $tables = $db->fetchAll("SHOW TABLES LIKE 'calendar_events'");
    
    if (empty($tables)) {
        echo "<p>Creating calendar_events table...</p>";
        
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
        
        echo "<p style='color: green;'>✅ Created calendar_events table!</p>";
        
        // Add sample events
        echo "<p>Adding sample events...</p>";
        
        $sampleEvents = [
            [
                'title' => 'Họp team hàng tuần',
                'description' => 'Review tiến độ dự án và planning sprint mới',
                'start_date' => date('Y-m-d', strtotime('next monday')),
                'color' => '#6366f1',
                'location' => 'Phòng họp A',
                'reminder_minutes' => 30,
            ],
            [
                'title' => 'Demo sản phẩm',
                'description' => 'Demo các tính năng mới cho khách hàng',
                'start_date' => date('Y-m-d', strtotime('+5 days')),
                'color' => '#22c55e',
                'location' => 'Online - Google Meet',
                'reminder_minutes' => 60,
            ],
            [
                'title' => 'Deadline dự án TaskFlow',
                'description' => 'Hoàn thành phase 1',
                'start_date' => date('Y-m-d', strtotime('+10 days')),
                'color' => '#ef4444',
                'reminder_minutes' => 1440,
            ],
        ];
        
        // Get first user as creator
        $firstUser = $db->fetchOne("SELECT id FROM users LIMIT 1");
        $creatorId = $firstUser ? $firstUser['id'] : null;
        
        foreach ($sampleEvents as $event) {
            $eventId = sprintf(
                '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
                mt_rand(0, 0xffff), mt_rand(0, 0xffff),
                mt_rand(0, 0xffff),
                mt_rand(0, 0x0fff) | 0x4000,
                mt_rand(0, 0x3fff) | 0x8000,
                mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
            );
            
            $db->insert('calendar_events', [
                'id' => $eventId,
                'title' => $event['title'],
                'description' => $event['description'] ?? null,
                'start_date' => $event['start_date'],
                'end_date' => $event['start_date'],
                'is_all_day' => 1,
                'color' => $event['color'],
                'location' => $event['location'] ?? null,
                'reminder_minutes' => $event['reminder_minutes'] ?? null,
                'created_by' => $creatorId,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }
        
        echo "<p style='color: green;'>✅ Added " . count($sampleEvents) . " sample events!</p>";
        
    } else {
        echo "<p style='color: orange;'>⚠️ Calendar events table already exists!</p>";
    }
    
    // Show current structure
    echo "<h3>Current calendar_events table structure:</h3>";
    $structure = $db->fetchAll("DESCRIBE calendar_events");
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
    
    // Show events count
    $count = $db->fetchOne("SELECT COUNT(*) as cnt FROM calendar_events");
    echo "<p>Total events: " . ($count['cnt'] ?? 0) . "</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<p><a href='../calendar.php'>← Back to Calendar</a></p>";
