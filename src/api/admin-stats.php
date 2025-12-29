<?php
/**
 * API: Admin Stats - Real-time statistics
 * Provides live stats for admin dashboard and manager quick stats
 */
require_once __DIR__ . '/../bootstrap.php';

use App\Middleware\AuthMiddleware;
use Core\Database;
use Core\Session;

header('Content-Type: application/json');

AuthMiddleware::handle();

// Allow both admin and manager
$userRole = Session::get('user_role', 'guest');
if (!in_array($userRole, ['admin', 'manager'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Không có quyền truy cập']);
    exit;
}

try {
    $db = Database::getInstance();
    $userId = Session::get('user_id');
    
    // For manager, only count their managed projects
    if ($userRole === 'manager') {
        $projectCount = (int) $db->fetchColumn(
            "SELECT COUNT(DISTINCT p.id) FROM projects p 
             JOIN project_members pm ON p.id = pm.project_id 
             WHERE pm.user_id = ? AND pm.role IN ('owner', 'manager')",
            [$userId]
        );
        
        $activeTasks = (int) $db->fetchColumn(
            "SELECT COUNT(DISTINCT t.id) FROM tasks t 
             JOIN projects p ON t.project_id = p.id
             JOIN project_members pm ON p.id = pm.project_id 
             WHERE pm.user_id = ? AND pm.role IN ('owner', 'manager') AND t.status IN ('todo', 'in_progress')",
            [$userId]
        );
        
        $overdueTasks = (int) $db->fetchColumn(
            "SELECT COUNT(DISTINCT t.id) FROM tasks t 
             JOIN projects p ON t.project_id = p.id
             JOIN project_members pm ON p.id = pm.project_id 
             WHERE pm.user_id = ? AND pm.role IN ('owner', 'manager') 
             AND t.due_date < CURDATE() AND t.status != 'done'",
            [$userId]
        );
        
        $memberCount = (int) $db->fetchColumn(
            "SELECT COUNT(DISTINCT u.id) FROM users u
             JOIN project_members pm ON u.id = pm.user_id
             WHERE pm.project_id IN (
                 SELECT project_id FROM project_members WHERE user_id = ? AND role IN ('owner', 'manager')
             ) AND u.is_active = 1",
            [$userId]
        );
        
        // Quick stats format for header
        echo json_encode([
            'success' => true,
            'stats' => [
                'projects' => $projectCount,
                'active_tasks' => $activeTasks,
                'overdue_tasks' => $overdueTasks,
                'users' => $memberCount,
            ]
        ]);
        exit;
    }
    
    // Full stats for admin
    $stats = [
        'users' => [
            'total' => (int) $db->fetchColumn("SELECT COUNT(*) FROM users"),
            'active' => (int) $db->fetchColumn("SELECT COUNT(*) FROM users WHERE is_active = 1"),
            'new_today' => (int) $db->fetchColumn("SELECT COUNT(*) FROM users WHERE DATE(created_at) = CURDATE()"),
            'new_week' => (int) $db->fetchColumn("SELECT COUNT(*) FROM users WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)"),
            'online' => (int) $db->fetchColumn("SELECT COUNT(*) FROM users WHERE last_login_at >= DATE_SUB(NOW(), INTERVAL 15 MINUTE)"),
        ],
        'projects' => [
            'total' => (int) $db->fetchColumn("SELECT COUNT(*) FROM projects"),
            'active' => (int) $db->fetchColumn("SELECT COUNT(*) FROM projects WHERE status = 'active'"),
            'completed' => (int) $db->fetchColumn("SELECT COUNT(*) FROM projects WHERE status = 'completed'"),
            'on_hold' => (int) $db->fetchColumn("SELECT COUNT(*) FROM projects WHERE status = 'on_hold'"),
        ],
        'tasks' => [
            'total' => (int) $db->fetchColumn("SELECT COUNT(*) FROM tasks"),
            'done' => (int) $db->fetchColumn("SELECT COUNT(*) FROM tasks WHERE status = 'done'"),
            'in_progress' => (int) $db->fetchColumn("SELECT COUNT(*) FROM tasks WHERE status = 'in_progress'"),
            'todo' => (int) $db->fetchColumn("SELECT COUNT(*) FROM tasks WHERE status IN ('todo', 'backlog')"),
            'overdue' => (int) $db->fetchColumn("SELECT COUNT(*) FROM tasks WHERE due_date < CURDATE() AND status != 'done'"),
            'due_today' => (int) $db->fetchColumn("SELECT COUNT(*) FROM tasks WHERE due_date = CURDATE() AND status != 'done'"),
            'due_week' => (int) $db->fetchColumn("SELECT COUNT(*) FROM tasks WHERE due_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY) AND status != 'done'"),
        ],
        'documents' => [
            'total' => (int) $db->fetchColumn("SELECT COUNT(*) FROM documents WHERE type = 'file'"),
            'storage_used' => (int) $db->fetchColumn("SELECT COALESCE(SUM(file_size), 0) FROM documents WHERE type = 'file'"),
            'uploaded_today' => (int) $db->fetchColumn("SELECT COUNT(*) FROM documents WHERE type = 'file' AND DATE(created_at) = CURDATE()"),
        ],
        'activity' => [
            'today' => (int) $db->fetchColumn("SELECT COUNT(*) FROM activity_logs WHERE DATE(created_at) = CURDATE()"),
            'last_hour' => (int) $db->fetchColumn("SELECT COUNT(*) FROM activity_logs WHERE created_at >= DATE_SUB(NOW(), INTERVAL 1 HOUR)"),
            'errors_24h' => (int) $db->fetchColumn("SELECT COUNT(*) FROM activity_logs WHERE action = 'error' AND created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)"),
        ],
        'system' => [
            'php_version' => phpversion(),
            'mysql_version' => $db->fetchColumn("SELECT VERSION()"),
            'memory_usage' => memory_get_usage(true),
            'memory_limit' => ini_get('memory_limit'),
            'disk_free' => disk_free_space('.'),
            'disk_total' => disk_total_space('.'),
        ],
        'timestamp' => date('Y-m-d H:i:s'),
    ];
    
    // Also include quick stats format for admin
    echo json_encode([
        'success' => true,
        'data' => $stats,
        'stats' => [
            'projects' => $stats['projects']['total'],
            'active_tasks' => $stats['tasks']['in_progress'] + $stats['tasks']['todo'],
            'overdue_tasks' => $stats['tasks']['overdue'],
            'users' => $stats['users']['active'],
        ]
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
    ]);
}
