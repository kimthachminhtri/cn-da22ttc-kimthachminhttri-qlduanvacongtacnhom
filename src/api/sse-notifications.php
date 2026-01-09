<?php
/**
 * Server-Sent Events (SSE) for Real-time Notifications
 * 
 * Endpoint này giữ kết nối mở và gửi notifications mới đến client
 * Client sử dụng EventSource API để nhận notifications
 * 
 * Usage: const eventSource = new EventSource('/api/sse-notifications.php');
 */

// Disable output buffering
if (ob_get_level()) ob_end_clean();

// Set headers for SSE
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
header('Connection: keep-alive');
header('X-Accel-Buffering: no'); // Disable nginx buffering

// Prevent timeout
set_time_limit(0);
ignore_user_abort(false);

require_once __DIR__ . '/../bootstrap.php';

use Core\Database;
use Core\Session;

// Start session to get user
Session::start();

// Check authentication
if (!Session::has('user_id')) {
    echo "event: error\n";
    echo "data: " . json_encode(['error' => 'Unauthorized']) . "\n\n";
    exit;
}

$userId = Session::get('user_id');

// IMPORTANT: Close session to prevent blocking other requests
session_write_close();

$db = Database::getInstance();

// Get last notification ID from query string (for reconnection)
$lastEventId = $_SERVER['HTTP_LAST_EVENT_ID'] ?? $_GET['lastEventId'] ?? null;

/**
 * Send SSE message
 */
function sendSSE(string $event, array $data, ?string $id = null): void {
    if ($id) {
        echo "id: {$id}\n";
    }
    echo "event: {$event}\n";
    echo "data: " . json_encode($data, JSON_UNESCAPED_UNICODE) . "\n\n";
    
    if (ob_get_level()) ob_flush();
    flush();
}

/**
 * Send heartbeat to keep connection alive
 */
function sendHeartbeat(): void {
    echo ": heartbeat\n\n";
    if (ob_get_level()) ob_flush();
    flush();
}

// Send initial connection success
sendSSE('connected', [
    'message' => 'Connected to notification stream',
    'userId' => $userId,
    'timestamp' => date('c')
]);

$lastCheck = time();
$lastNotificationId = $lastEventId;
$heartbeatInterval = 30; // seconds
$checkInterval = 3; // seconds

// Main loop
while (true) {
    // Check if client disconnected
    if (connection_aborted()) {
        break;
    }
    
    $now = time();
    
    // Check for new notifications every 3 seconds
    if ($now - $lastCheck >= $checkInterval) {
        $lastCheck = $now;
        
        try {
            // Build query for new notifications
            $query = "SELECT n.*, u.full_name as actor_name, u.avatar_url as actor_avatar
                      FROM notifications n
                      LEFT JOIN users u ON n.actor_id = u.id
                      WHERE n.user_id = ?";
            $params = [$userId];
            
            if ($lastNotificationId) {
                $query .= " AND n.id > ?";
                $params[] = $lastNotificationId;
            } else {
                // First connection: get unread notifications from last 24 hours
                $query .= " AND n.is_read = 0 AND n.created_at > DATE_SUB(NOW(), INTERVAL 24 HOUR)";
            }
            
            $query .= " ORDER BY n.created_at ASC LIMIT 10";
            
            $notifications = $db->fetchAll($query, $params);
            
            foreach ($notifications as $notification) {
                sendSSE('notification', [
                    'id' => $notification['id'],
                    'type' => $notification['type'],
                    'title' => $notification['title'],
                    'message' => $notification['message'],
                    'link' => $notification['link'],
                    'isRead' => (bool)$notification['is_read'],
                    'createdAt' => $notification['created_at'],
                    'actor' => $notification['actor_id'] ? [
                        'id' => $notification['actor_id'],
                        'name' => $notification['actor_name'],
                        'avatar' => $notification['actor_avatar']
                    ] : null
                ], $notification['id']);
                
                $lastNotificationId = $notification['id'];
            }
            
            // Also send unread count update
            $unreadCount = $db->fetchOne(
                "SELECT COUNT(*) as count FROM notifications WHERE user_id = ? AND is_read = 0",
                [$userId]
            );
            
            sendSSE('unread_count', [
                'count' => (int)($unreadCount['count'] ?? 0)
            ]);
            
        } catch (Exception $e) {
            sendSSE('error', ['message' => 'Database error']);
        }
    }
    
    // Send heartbeat every 30 seconds
    static $lastHeartbeat = 0;
    if ($now - $lastHeartbeat >= $heartbeatInterval) {
        $lastHeartbeat = $now;
        sendHeartbeat();
    }
    
    // Sleep to reduce CPU usage
    usleep(500000); // 0.5 seconds
}
