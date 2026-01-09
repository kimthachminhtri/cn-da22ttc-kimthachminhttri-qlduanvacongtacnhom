<?php
/**
 * API: Reports & Analytics
 */

require_once __DIR__ . '/../includes/config.php';
require_once BASE_PATH . '/includes/csrf.php';
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    csrf_require();
}

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

$action = $_GET['action'] ?? 'stats';
$format = $_GET['format'] ?? 'json';
$currentUserId = $_SESSION['user_id'];
$userRole = $_SESSION['user_role'] ?? 'member';

try {
    $db = Database::getInstance();
    
    // Get user's project IDs for filtering (non-admin users)
    $projectIds = [];
    if ($userRole !== 'admin') {
        $projects = $db->fetchAll("SELECT project_id FROM project_members WHERE user_id = ?", [$currentUserId]);
        $projectIds = array_column($projects, 'project_id');
    }
    
    switch ($action) {
        case 'stats':
            // Get comprehensive statistics (filtered by user's projects for non-admin)
            $stats = [
                'tasks' => getTaskStats($db, $userRole, $projectIds),
                'projects' => getProjectStats($db, $userRole, $projectIds),
                'team' => getTeamStats($db, $userRole, $projectIds),
                'weekly' => getWeeklyProgress($db, $userRole, $projectIds),
            ];
            
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'data' => $stats]);
            break;
            
        case 'export':
            $type = $_GET['type'] ?? 'tasks';
            exportData($db, $type, $format, $userRole, $projectIds);
            break;
            
        default:
            throw new Exception('Invalid action');
    }
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

function getTaskStats($db, $userRole, $projectIds) {
    $stats = [
        'total' => 0,
        'by_status' => [],
        'by_priority' => [],
        'overdue' => 0,
        'due_this_week' => 0,
    ];
    
    $projectFilter = '';
    $params = [];
    if ($userRole !== 'admin' && !empty($projectIds)) {
        $placeholders = implode(',', array_fill(0, count($projectIds), '?'));
        $projectFilter = " WHERE project_id IN ($placeholders)";
        $params = $projectIds;
    } elseif ($userRole !== 'admin' && empty($projectIds)) {
        return $stats; // No projects, return empty stats
    }
    
    // By status
    $byStatus = $db->fetchAll("SELECT status, COUNT(*) as count FROM tasks $projectFilter GROUP BY status", $params);
    foreach ($byStatus as $s) {
        $stats['by_status'][$s['status']] = (int)$s['count'];
        $stats['total'] += (int)$s['count'];
    }
    
    // By priority
    $byPriority = $db->fetchAll("SELECT priority, COUNT(*) as count FROM tasks $projectFilter GROUP BY priority", $params);
    foreach ($byPriority as $p) {
        $stats['by_priority'][$p['priority']] = (int)$p['count'];
    }
    
    // Overdue
    $overdueFilter = $projectFilter ? str_replace('WHERE', 'WHERE due_date < CURDATE() AND status != \'done\' AND', $projectFilter) : "WHERE due_date < CURDATE() AND status != 'done'";
    $overdue = $db->fetchOne("SELECT COUNT(*) as count FROM tasks $overdueFilter", $params);
    $stats['overdue'] = (int)($overdue['count'] ?? 0);
    
    // Due this week
    $dueWeekFilter = $projectFilter ? str_replace('WHERE', 'WHERE due_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY) AND status != \'done\' AND', $projectFilter) : "WHERE due_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY) AND status != 'done'";
    $dueWeek = $db->fetchOne("SELECT COUNT(*) as count FROM tasks $dueWeekFilter", $params);
    $stats['due_this_week'] = (int)($dueWeek['count'] ?? 0);
    
    return $stats;
}

function getProjectStats($db, $userRole, $projectIds) {
    $stats = [
        'total' => 0,
        'by_status' => [],
        'avg_progress' => 0,
    ];
    
    $projectFilter = '';
    $params = [];
    if ($userRole !== 'admin' && !empty($projectIds)) {
        $placeholders = implode(',', array_fill(0, count($projectIds), '?'));
        $projectFilter = " WHERE id IN ($placeholders)";
        $params = $projectIds;
    } elseif ($userRole !== 'admin' && empty($projectIds)) {
        return $stats;
    }
    
    $byStatus = $db->fetchAll("SELECT status, COUNT(*) as count FROM projects $projectFilter GROUP BY status", $params);
    foreach ($byStatus as $s) {
        $stats['by_status'][$s['status']] = (int)$s['count'];
        $stats['total'] += (int)$s['count'];
    }
    
    $avgProgress = $db->fetchOne("SELECT AVG(progress) as avg FROM projects $projectFilter", $params);
    $stats['avg_progress'] = round($avgProgress['avg'] ?? 0);
    
    return $stats;
}

function getTeamStats($db, $userRole, $projectIds) {
    $stats = [
        'total_members' => 0,
        'productivity' => [],
    ];
    
    if ($userRole === 'admin') {
        $members = $db->fetchOne("SELECT COUNT(*) as count FROM users WHERE is_active = 1");
        $stats['total_members'] = (int)($members['count'] ?? 0);
        
        $productivity = $db->fetchAll(
            "SELECT u.full_name as name, 
                    COUNT(CASE WHEN t.status = 'done' THEN 1 END) as completed,
                    COUNT(*) as total
             FROM users u
             LEFT JOIN task_assignees ta ON u.id = ta.user_id
             LEFT JOIN tasks t ON ta.task_id = t.id
             WHERE u.is_active = 1
             GROUP BY u.id, u.full_name
             HAVING total > 0
             ORDER BY completed DESC
             LIMIT 10"
        );
    } else {
        if (empty($projectIds)) {
            return $stats;
        }
        
        $placeholders = implode(',', array_fill(0, count($projectIds), '?'));
        
        // Count members in user's projects
        $members = $db->fetchOne(
            "SELECT COUNT(DISTINCT user_id) as count FROM project_members WHERE project_id IN ($placeholders)",
            $projectIds
        );
        $stats['total_members'] = (int)($members['count'] ?? 0);
        
        // Productivity for team members in user's projects
        $productivity = $db->fetchAll(
            "SELECT u.full_name as name, 
                    COUNT(CASE WHEN t.status = 'done' THEN 1 END) as completed,
                    COUNT(*) as total
             FROM users u
             JOIN project_members pm ON u.id = pm.user_id
             LEFT JOIN task_assignees ta ON u.id = ta.user_id
             LEFT JOIN tasks t ON ta.task_id = t.id AND t.project_id IN ($placeholders)
             WHERE pm.project_id IN ($placeholders) AND u.is_active = 1
             GROUP BY u.id, u.full_name
             HAVING total > 0
             ORDER BY completed DESC
             LIMIT 10",
            array_merge($projectIds, $projectIds)
        );
    }
    
    $stats['productivity'] = $productivity;
    return $stats;
}

function getWeeklyProgress($db, $userRole, $projectIds) {
    $projectFilter = '';
    $params = [];
    
    if ($userRole !== 'admin' && !empty($projectIds)) {
        $placeholders = implode(',', array_fill(0, count($projectIds), '?'));
        $projectFilter = " AND project_id IN ($placeholders)";
        $params = $projectIds;
    } elseif ($userRole !== 'admin' && empty($projectIds)) {
        return [];
    }
    
    $weekly = $db->fetchAll(
        "SELECT DATE(updated_at) as date, COUNT(*) as count 
         FROM tasks 
         WHERE status = 'done' AND updated_at >= DATE_SUB(NOW(), INTERVAL 8 WEEK) $projectFilter
         GROUP BY DATE(updated_at)
         ORDER BY date ASC",
        $params
    );
    return $weekly;
}

function exportData($db, $type, $format, $userRole, $projectIds) {
    $data = [];
    $filename = "taskflow-{$type}-" . date('Y-m-d');
    
    $projectFilter = '';
    $params = [];
    if ($userRole !== 'admin' && !empty($projectIds)) {
        $placeholders = implode(',', array_fill(0, count($projectIds), '?'));
        $projectFilter = " WHERE t.project_id IN ($placeholders)";
        $params = $projectIds;
    } elseif ($userRole !== 'admin' && empty($projectIds)) {
        $data = [];
    }
    
    switch ($type) {
        case 'tasks':
            if ($userRole === 'admin') {
                $data = $db->fetchAll(
                    "SELECT t.title, t.status, t.priority, t.due_date, 
                            p.name as project_name, u.full_name as assignee
                     FROM tasks t
                     LEFT JOIN projects p ON t.project_id = p.id
                     LEFT JOIN task_assignees ta ON t.id = ta.task_id
                     LEFT JOIN users u ON ta.user_id = u.id
                     ORDER BY t.created_at DESC"
                );
            } elseif (!empty($projectIds)) {
                $placeholders = implode(',', array_fill(0, count($projectIds), '?'));
                $data = $db->fetchAll(
                    "SELECT t.title, t.status, t.priority, t.due_date, 
                            p.name as project_name, u.full_name as assignee
                     FROM tasks t
                     LEFT JOIN projects p ON t.project_id = p.id
                     LEFT JOIN task_assignees ta ON t.id = ta.task_id
                     LEFT JOIN users u ON ta.user_id = u.id
                     WHERE t.project_id IN ($placeholders)
                     ORDER BY t.created_at DESC",
                    $projectIds
                );
            }
            break;
            
        case 'projects':
            if ($userRole === 'admin') {
                $data = $db->fetchAll(
                    "SELECT name, status, progress, start_date, end_date
                     FROM projects
                     ORDER BY created_at DESC"
                );
            } elseif (!empty($projectIds)) {
                $placeholders = implode(',', array_fill(0, count($projectIds), '?'));
                $data = $db->fetchAll(
                    "SELECT name, status, progress, start_date, end_date
                     FROM projects
                     WHERE id IN ($placeholders)
                     ORDER BY created_at DESC",
                    $projectIds
                );
            }
            break;
    }
    
    if ($format === 'csv') {
        header('Content-Type: text/csv; charset=utf-8');
        header("Content-Disposition: attachment; filename={$filename}.csv");
        
        $output = fopen('php://output', 'w');
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM
        
        if (!empty($data)) {
            fputcsv($output, array_keys($data[0]));
            foreach ($data as $row) {
                fputcsv($output, $row);
            }
        }
        fclose($output);
    } else {
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'data' => $data]);
    }
}
