<?php
/**
 * Migration: Create notifications table
 */

require_once __DIR__ . '/../includes/config.php';

try {
    $db = Database::getInstance();
    
    echo "<h2>Migration: Notifications Table</h2>";
    
    // Check if table exists
    $tables = $db->fetchAll("SHOW TABLES LIKE 'notifications'");
    
    if (empty($tables)) {
        echo "<p>Creating notifications table...</p>";
        
        $sql = "CREATE TABLE `notifications` (
            `id` VARCHAR(36) PRIMARY KEY,
            `user_id` VARCHAR(36) NOT NULL,
            `type` VARCHAR(50) NOT NULL,
            `title` VARCHAR(255) NOT NULL,
            `message` TEXT NOT NULL,
            `link` VARCHAR(500) NULL,
            `data` JSON NULL,
            `actor_id` VARCHAR(36) NULL,
            `is_read` TINYINT(1) NOT NULL DEFAULT 0,
            `read_at` DATETIME NULL,
            `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
            
            INDEX `idx_notifications_user` (`user_id`),
            INDEX `idx_notifications_user_read` (`user_id`, `is_read`),
            INDEX `idx_notifications_type` (`type`),
            INDEX `idx_notifications_created` (`created_at`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        $db->query($sql);
        
        echo "<p style='color: green;'>✅ Created notifications table!</p>";
        
        // Add sample notifications
        echo "<p>Adding sample notifications...</p>";
        
        $users = $db->fetchAll("SELECT id, full_name FROM users LIMIT 3");
        if (count($users) >= 2) {
            $user1 = $users[0];
            $user2 = $users[1] ?? $users[0];
            
            $sampleNotifications = [
                [
                    'user_id' => $user1['id'],
                    'type' => 'task_assigned',
                    'title' => 'Được giao công việc mới',
                    'message' => $user2['full_name'] . ' đã giao cho bạn task "Thiết kế trang chủ"',
                    'link' => 'tasks.php',
                    'actor_id' => $user2['id'],
                ],
                [
                    'user_id' => $user1['id'],
                    'type' => 'comment_added',
                    'title' => 'Bình luận mới',
                    'message' => $user2['full_name'] . ' đã bình luận vào task của bạn',
                    'link' => 'tasks.php',
                    'actor_id' => $user2['id'],
                ],
                [
                    'user_id' => $user1['id'],
                    'type' => 'task_due_soon',
                    'title' => 'Sắp đến hạn',
                    'message' => 'Task "Hoàn thành báo cáo" sẽ đến hạn vào ngày mai',
                    'link' => 'tasks.php',
                ],
            ];
            
            foreach ($sampleNotifications as $notif) {
                $notifId = sprintf(
                    '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
                    mt_rand(0, 0xffff), mt_rand(0, 0xffff),
                    mt_rand(0, 0xffff),
                    mt_rand(0, 0x0fff) | 0x4000,
                    mt_rand(0, 0x3fff) | 0x8000,
                    mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
                );
                
                $db->insert('notifications', [
                    'id' => $notifId,
                    'user_id' => $notif['user_id'],
                    'type' => $notif['type'],
                    'title' => $notif['title'],
                    'message' => $notif['message'],
                    'link' => $notif['link'],
                    'actor_id' => $notif['actor_id'] ?? null,
                    'is_read' => 0,
                    'created_at' => date('Y-m-d H:i:s', strtotime('-' . rand(1, 24) . ' hours')),
                ]);
            }
            
            echo "<p style='color: green;'>✅ Added " . count($sampleNotifications) . " sample notifications!</p>";
        }
        
    } else {
        echo "<p style='color: orange;'>⚠️ Notifications table already exists!</p>";
    }
    
    // Show current structure
    echo "<h3>Current notifications table structure:</h3>";
    $structure = $db->fetchAll("DESCRIBE notifications");
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
    
    // Show count
    $count = $db->fetchOne("SELECT COUNT(*) as cnt FROM notifications");
    echo "<p>Total notifications: " . ($count['cnt'] ?? 0) . "</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<p><a href='../notifications.php'>← Back to Notifications</a></p>";
