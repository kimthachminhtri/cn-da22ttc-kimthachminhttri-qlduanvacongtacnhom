<?php
/**
 * Notification Helper Class
 * Dùng để tạo thông báo từ cron jobs và các nơi khác
 */

class NotificationHelper
{
    /**
     * Create a notification
     */
    public static function create(
        string $userId,
        string $type,
        string $title,
        string $message,
        ?string $link = null,
        ?string $actorId = null,
        ?array $data = null
    ): string|false {
        try {
            $db = Database::getInstance();
            
            $notificationId = sprintf(
                '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
                mt_rand(0, 0xffff), mt_rand(0, 0xffff),
                mt_rand(0, 0xffff),
                mt_rand(0, 0x0fff) | 0x4000,
                mt_rand(0, 0x3fff) | 0x8000,
                mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
            );
            
            $db->insert('notifications', [
                'id' => $notificationId,
                'user_id' => $userId,
                'type' => $type,
                'title' => $title,
                'message' => $message,
                'link' => $link,
                'actor_id' => $actorId,
                'data' => $data ? json_encode($data) : null,
                'is_read' => 0,
                'created_at' => date('Y-m-d H:i:s'),
            ]);
            
            return $notificationId;
            
        } catch (Exception $e) {
            error_log("NotificationHelper::create error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Create multiple notifications (for batch operations)
     */
    public static function createBatch(array $notifications): int
    {
        $created = 0;
        foreach ($notifications as $notification) {
            $result = self::create(
                $notification['user_id'],
                $notification['type'],
                $notification['title'],
                $notification['message'],
                $notification['link'] ?? null,
                $notification['actor_id'] ?? null,
                $notification['data'] ?? null
            );
            if ($result) $created++;
        }
        return $created;
    }
    
    /**
     * Get unread count for user
     */
    public static function getUnreadCount(string $userId): int
    {
        try {
            $db = Database::getInstance();
            $result = $db->fetchOne(
                "SELECT COUNT(*) as count FROM notifications WHERE user_id = ? AND is_read = 0",
                [$userId]
            );
            return (int)($result['count'] ?? 0);
        } catch (Exception $e) {
            return 0;
        }
    }
    
    /**
     * Mark notification as read
     */
    public static function markAsRead(string $notificationId, string $userId): bool
    {
        try {
            $db = Database::getInstance();
            $db->update('notifications', 
                ['is_read' => 1, 'read_at' => date('Y-m-d H:i:s')],
                'id = ? AND user_id = ?',
                [$notificationId, $userId]
            );
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Mark all notifications as read for user
     */
    public static function markAllAsRead(string $userId): bool
    {
        try {
            $db = Database::getInstance();
            $db->query(
                "UPDATE notifications SET is_read = 1, read_at = NOW() WHERE user_id = ? AND is_read = 0",
                [$userId]
            );
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
