<?php
/**
 * Cron Job: Event Reminders
 * Chạy mỗi phút để kiểm tra và gửi nhắc nhở sự kiện
 * 
 * Crontab: * * * * * php /path/to/taskflow/cron/event-reminders.php
 */

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/classes/Notification.php';

// Prevent web access
if (php_sapi_name() !== 'cli' && !defined('CRON_RUNNING')) {
    die('This script can only be run from command line');
}

try {
    $db = Database::getInstance();
    $now = new DateTime();
    
    echo "[" . $now->format('Y-m-d H:i:s') . "] Checking event reminders...\n";
    
    // Get events that need reminders
    // Events where: start_time - reminder_minutes = now (within 1 minute window)
    // Note: Database schema uses start_time as DATETIME
    $events = $db->fetchAll(
        "SELECT ce.*, u.id as user_id
         FROM calendar_events ce
         JOIN users u ON ce.created_by = u.id
         WHERE ce.start_time >= NOW()
         AND NOT EXISTS (
             SELECT 1 FROM notifications n 
             WHERE n.link LIKE CONCAT('%event_id=', ce.id, '%')
             AND n.type = 'event_reminder'
             AND n.created_at > DATE_SUB(NOW(), INTERVAL 60 MINUTE)
         )"
    );
    
    $sentCount = 0;
    
    foreach ($events as $event) {
        // start_time is DATETIME in database
        $eventDateTime = new DateTime($event['start_time']);
        
        // Calculate reminder time (default 30 minutes before if not set)
        $reminderMinutes = 30;
        $reminderTime = clone $eventDateTime;
        $reminderTime->modify("-{$reminderMinutes} minutes");
        
        // Check if it's time to send reminder (within 1 minute window)
        $diff = $now->getTimestamp() - $reminderTime->getTimestamp();
        
        if ($diff >= 0 && $diff < 60) {
            // Time to send reminder
            $reminderText = formatReminderTime($reminderMinutes);
            
            NotificationHelper::create(
                $event['user_id'],
                'event_reminder',
                'Nhắc nhở sự kiện',
                "Sự kiện \"{$event['title']}\" sẽ bắt đầu trong $reminderText",
                "calendar.php?event_id={$event['id']}",
                null,
                ['event_id' => $event['id']]
            );
            
            $sentCount++;
            echo "  - Sent reminder for event: {$event['title']}\n";
        }
    }
    
    // Also check task deadlines
    $tasks = $db->fetchAll(
        "SELECT t.*, ta.user_id
         FROM tasks t
         JOIN task_assignees ta ON t.id = ta.task_id
         WHERE t.due_date = CURDATE()
         AND t.status != 'done'
         AND NOT EXISTS (
             SELECT 1 FROM notifications n 
             WHERE n.link LIKE CONCAT('%task-detail.php?id=', t.id, '%')
             AND n.type = 'task_due_today'
             AND DATE(n.created_at) = CURDATE()
         )"
    );
    
    foreach ($tasks as $task) {
        NotificationHelper::create(
            $task['user_id'],
            'task_due_today',
            'Công việc đến hạn hôm nay',
            "Task \"{$task['title']}\" đến hạn hôm nay",
            "task-detail.php?id={$task['id']}",
            null,
            ['task_id' => $task['id']]
        );
        
        $sentCount++;
        echo "  - Sent due today reminder for task: {$task['title']}\n";
    }
    
    echo "[" . date('Y-m-d H:i:s') . "] Done. Sent $sentCount reminders.\n";
    
} catch (Exception $e) {
    echo "[ERROR] " . $e->getMessage() . "\n";
    exit(1);
}

function formatReminderTime($minutes) {
    if ($minutes < 60) {
        return "$minutes phút";
    } elseif ($minutes < 1440) {
        return ($minutes / 60) . " giờ";
    } else {
        return ($minutes / 1440) . " ngày";
    }
}
