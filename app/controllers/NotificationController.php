<?php
/**
 * Notification Controller
 */

namespace App\Controllers;

use App\Middleware\AuthMiddleware;
use Core\Database;

class NotificationController extends BaseController
{
    public function __construct()
    {
        AuthMiddleware::handle();
    }

    public function index(): void
    {
        $db = Database::getInstance();
        
        $notifications = $db->fetchAll(
            "SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 50",
            [$this->userId()]
        );
        
        // Format time ago
        foreach ($notifications as &$n) {
            $n['time_ago'] = $this->timeAgo($n['created_at']);
        }
        
        $this->view('notifications/index', [
            'notifications' => $notifications,
            'pageTitle' => 'Thông báo',
        ]);
    }

    public function markAsRead(): void
    {
        $db = Database::getInstance();
        $id = $this->input('id');
        $markAll = $this->input('mark_all');
        
        if ($markAll) {
            $db->update('notifications', ['is_read' => 1], 'user_id = ?', [$this->userId()]);
        } elseif ($id) {
            $db->update('notifications', ['is_read' => 1], 'id = ? AND user_id = ?', [$id, $this->userId()]);
        }
        
        $this->json(['success' => true]);
    }

    public function getUnreadCount(): void
    {
        $db = Database::getInstance();
        
        $count = $db->fetchColumn(
            "SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0",
            [$this->userId()]
        );
        
        $this->json(['count' => (int)$count]);
    }

    private function timeAgo(string $datetime): string
    {
        $time = strtotime($datetime);
        $diff = time() - $time;
        
        if ($diff < 60) return 'Vừa xong';
        if ($diff < 3600) return floor($diff / 60) . ' phút trước';
        if ($diff < 86400) return floor($diff / 3600) . ' giờ trước';
        if ($diff < 604800) return floor($diff / 86400) . ' ngày trước';
        
        return date('d/m/Y', $time);
    }
}
