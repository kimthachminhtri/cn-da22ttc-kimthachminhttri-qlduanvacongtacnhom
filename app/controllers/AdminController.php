<?php
/**
 * Admin Controller - Professional Admin Panel
 */

namespace App\Controllers;

use App\Models\User;
use App\Models\Project;
use App\Models\Task;
use App\Middleware\AuthMiddleware;
use App\Middleware\PermissionMiddleware;
use Core\Database;
use Core\Session;

class AdminController extends BaseController
{
    private User $userModel;
    private Project $projectModel;
    private Task $taskModel;
    private Database $db;

    public function __construct()
    {
        AuthMiddleware::handle();
        PermissionMiddleware::requireAdmin();
        
        $this->userModel = new User();
        $this->projectModel = new Project();
        $this->taskModel = new Task();
        $this->db = Database::getInstance();
    }

    public function dashboard(): void
    {
        // Basic stats
        $stats = [
            'users' => (int) $this->db->fetchColumn("SELECT COUNT(*) FROM users"),
            'active_users' => (int) $this->db->fetchColumn("SELECT COUNT(*) FROM users WHERE is_active = 1"),
            'projects' => (int) $this->db->fetchColumn("SELECT COUNT(*) FROM projects"),
            'active_projects' => (int) $this->db->fetchColumn("SELECT COUNT(*) FROM projects WHERE status = 'active'"),
            'tasks' => (int) $this->db->fetchColumn("SELECT COUNT(*) FROM tasks"),
            'completed_tasks' => (int) $this->db->fetchColumn("SELECT COUNT(*) FROM tasks WHERE status = 'done'"),
            'documents' => (int) $this->db->fetchColumn("SELECT COUNT(*) FROM documents WHERE type = 'file'"),
            'storage_used' => (int) $this->db->fetchColumn("SELECT COALESCE(SUM(file_size), 0) FROM documents WHERE type = 'file'"),
        ];
        
        // Role distribution
        $roleStats = [];
        $totalUsers = max($stats['users'], 1);
        $roles = $this->db->fetchAll("SELECT role, COUNT(*) as count FROM users GROUP BY role");
        foreach ($roles as $role) {
            $roleStats[$role['role']] = round(($role['count'] / $totalUsers) * 100);
        }
        
        // Task stats
        $totalTasks = max($stats['tasks'], 1);
        $taskStats = [
            'done' => (int) $this->db->fetchColumn("SELECT COUNT(*) FROM tasks WHERE status = 'done'"),
            'in_progress' => (int) $this->db->fetchColumn("SELECT COUNT(*) FROM tasks WHERE status = 'in_progress'"),
            'todo' => (int) $this->db->fetchColumn("SELECT COUNT(*) FROM tasks WHERE status IN ('todo', 'backlog')"),
            'overdue' => (int) $this->db->fetchColumn("SELECT COUNT(*) FROM tasks WHERE due_date < CURDATE() AND status != 'done'"),
        ];
        $taskStats['done_percent'] = round(($taskStats['done'] / $totalTasks) * 100);
        $taskStats['in_progress_percent'] = round(($taskStats['in_progress'] / $totalTasks) * 100);
        $taskStats['todo_percent'] = round(($taskStats['todo'] / $totalTasks) * 100);
        $taskStats['overdue_percent'] = round(($taskStats['overdue'] / $totalTasks) * 100);
        
        // Recent users
        $recentUsers = $this->db->fetchAll("SELECT * FROM users ORDER BY created_at DESC LIMIT 5");
        
        // Recent activities
        $activities = $this->db->fetchAll(
            "SELECT al.*, u.full_name 
             FROM activity_logs al 
             LEFT JOIN users u ON al.user_id = u.id 
             ORDER BY al.created_at DESC LIMIT 10"
        );
        
        // System info
        $systemInfo = [
            'mysql_version' => $this->db->fetchColumn("SELECT VERSION()"),
        ];
        
        // User growth - last 6 months
        $userGrowth = $this->db->fetchAll(
            "SELECT DATE_FORMAT(created_at, '%Y-%m') as month, 
                    DATE_FORMAT(created_at, '%m/%Y') as label,
                    COUNT(*) as count 
             FROM users 
             WHERE created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
             GROUP BY DATE_FORMAT(created_at, '%Y-%m')
             ORDER BY month ASC"
        );
        
        // Upcoming deadlines - tasks due in next 7 days
        $upcomingDeadlines = $this->db->fetchAll(
            "SELECT t.id, t.title, t.due_date, t.priority, t.status,
                    p.name as project_name, p.color as project_color,
                    (SELECT u.full_name FROM task_assignees ta 
                     JOIN users u ON ta.user_id = u.id 
                     WHERE ta.task_id = t.id LIMIT 1) as assignee_name
             FROM tasks t
             LEFT JOIN projects p ON t.project_id = p.id
             WHERE t.due_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)
               AND t.status != 'done'
             ORDER BY t.due_date ASC
             LIMIT 10"
        );
        
        // Error logs count (last 24h) - from activity_logs with action 'error'
        $errorCount = (int) $this->db->fetchColumn(
            "SELECT COUNT(*) FROM activity_logs 
             WHERE action = 'error' AND created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)"
        );
        
        // Storage breakdown by file type
        $storageBreakdown = $this->db->fetchAll(
            "SELECT 
                CASE 
                    WHEN mime_type LIKE 'image/%' THEN 'images'
                    WHEN mime_type LIKE 'application/pdf' THEN 'pdf'
                    WHEN mime_type LIKE 'application/vnd%' OR mime_type LIKE 'application/msword%' THEN 'documents'
                    ELSE 'other'
                END as file_type,
                COUNT(*) as count,
                COALESCE(SUM(file_size), 0) as total_size
             FROM documents 
             WHERE type = 'file'
             GROUP BY file_type"
        );
        
        $this->view('admin/dashboard', [
            'stats' => $stats,
            'roleStats' => $roleStats,
            'taskStats' => $taskStats,
            'recentUsers' => $recentUsers,
            'activities' => $activities,
            'systemInfo' => $systemInfo,
            'userGrowth' => $userGrowth,
            'upcomingDeadlines' => $upcomingDeadlines,
            'errorCount' => $errorCount,
            'storageBreakdown' => $storageBreakdown,
            'pageTitle' => 'Dashboard',
        ], 'admin');
    }

    public function users(): void
    {
        $search = $_GET['search'] ?? '';
        $role = $_GET['role'] ?? '';
        $status = $_GET['status'] ?? '';
        $page = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 15;
        $offset = ($page - 1) * $perPage;
        
        // Build WHERE clause
        $where = "WHERE 1=1";
        $params = [];
        
        if ($search) {
            $where .= " AND (full_name LIKE ? OR email LIKE ?)";
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
        }
        
        if ($role) {
            $where .= " AND role = ?";
            $params[] = $role;
        }
        
        if ($status !== '') {
            $where .= " AND is_active = ?";
            $params[] = $status;
        }
        
        // Get total count
        $totalItems = (int) $this->db->fetchColumn("SELECT COUNT(*) FROM users {$where}", $params);
        
        // Get paginated users
        $sql = "SELECT * FROM users {$where} ORDER BY created_at DESC LIMIT {$perPage} OFFSET {$offset}";
        $users = $this->db->fetchAll($sql, $params);
        
        // Stats for filters
        $userStats = [
            'total' => $this->db->fetchColumn("SELECT COUNT(*) FROM users"),
            'active' => $this->db->fetchColumn("SELECT COUNT(*) FROM users WHERE is_active = 1"),
            'inactive' => $this->db->fetchColumn("SELECT COUNT(*) FROM users WHERE is_active = 0"),
            'admin' => $this->db->fetchColumn("SELECT COUNT(*) FROM users WHERE role = 'admin'"),
            'manager' => $this->db->fetchColumn("SELECT COUNT(*) FROM users WHERE role = 'manager'"),
            'member' => $this->db->fetchColumn("SELECT COUNT(*) FROM users WHERE role = 'member'"),
        ];
        
        $this->view('admin/users', [
            'users' => $users,
            'userStats' => $userStats,
            'filters' => ['search' => $search, 'role' => $role, 'status' => $status],
            'pagination' => [
                'current' => $page,
                'total' => (int) ceil($totalItems / $perPage),
                'perPage' => $perPage,
                'totalItems' => $totalItems,
                'baseUrl' => '?',
            ],
            'pageTitle' => 'Quản lý người dùng',
        ], 'admin');
    }

    public function projects(): void
    {
        $page = max(1, (int)($_GET['page'] ?? 1));
        $search = trim($_GET['search'] ?? '');
        $status = $_GET['status'] ?? '';
        $perPage = 15;
        $offset = ($page - 1) * $perPage;
        
        $where = "WHERE 1=1";
        $params = [];
        
        // Search by name
        if ($search) {
            $where .= " AND (p.name LIKE ? OR p.description LIKE ?)";
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
        }
        
        // Filter by status
        if ($status) {
            $where .= " AND p.status = ?";
            $params[] = $status;
        }
        
        $totalItems = (int) $this->db->fetchColumn("SELECT COUNT(*) FROM projects p {$where}", $params);
        
        $projects = $this->db->fetchAll(
            "SELECT p.*, u.full_name as creator_name,
                    (SELECT COUNT(*) FROM project_members WHERE project_id = p.id) as member_count,
                    (SELECT COUNT(*) FROM tasks WHERE project_id = p.id) as task_count,
                    (SELECT COUNT(*) FROM tasks WHERE project_id = p.id AND status = 'done') as completed_tasks
             FROM projects p
             LEFT JOIN users u ON p.created_by = u.id
             {$where}
             ORDER BY p.created_at DESC
             LIMIT {$perPage} OFFSET {$offset}",
            $params
        );
        
        // Project stats
        $projectStats = [
            'total' => (int) $this->db->fetchColumn("SELECT COUNT(*) FROM projects"),
            'active' => (int) $this->db->fetchColumn("SELECT COUNT(*) FROM projects WHERE status = 'active'"),
            'completed' => (int) $this->db->fetchColumn("SELECT COUNT(*) FROM projects WHERE status = 'completed'"),
            'on_hold' => (int) $this->db->fetchColumn("SELECT COUNT(*) FROM projects WHERE status = 'on_hold'"),
            'cancelled' => (int) $this->db->fetchColumn("SELECT COUNT(*) FROM projects WHERE status = 'cancelled'"),
        ];
        
        $this->view('admin/projects', [
            'projects' => $projects,
            'projectStats' => $projectStats,
            'filters' => ['search' => $search, 'status' => $status],
            'pagination' => [
                'current' => $page,
                'total' => (int) ceil(max($totalItems, 1) / $perPage),
                'perPage' => $perPage,
                'totalItems' => $totalItems,
                'baseUrl' => '?',
            ],
            'pageTitle' => 'Quản lý dự án',
        ], 'admin');
    }

    public function tasks(): void
    {
        $page = max(1, (int)($_GET['page'] ?? 1));
        $status = $_GET['status'] ?? '';
        $projectId = $_GET['project'] ?? '';
        $priority = $_GET['priority'] ?? '';
        $search = trim($_GET['search'] ?? '');
        $perPage = 20;
        $offset = ($page - 1) * $perPage;
        
        $where = "WHERE 1=1";
        $params = [];
        
        // Filter by status
        if ($status === 'overdue') {
            $where .= " AND t.due_date < CURDATE() AND t.status != 'done'";
        } elseif ($status === 'todo') {
            $where .= " AND t.status IN ('todo', 'backlog')";
        } elseif ($status) {
            $where .= " AND t.status = ?";
            $params[] = $status;
        }
        
        // Filter by project
        if ($projectId) {
            $where .= " AND t.project_id = ?";
            $params[] = $projectId;
        }
        
        // Filter by priority
        if ($priority) {
            $where .= " AND t.priority = ?";
            $params[] = $priority;
        }
        
        // Search by title
        if ($search) {
            $where .= " AND t.title LIKE ?";
            $params[] = "%{$search}%";
        }
        
        $totalItems = (int) $this->db->fetchColumn("SELECT COUNT(*) FROM tasks t {$where}", $params);
        
        $tasks = $this->db->fetchAll(
            "SELECT t.*, p.name as project_name, p.color as project_color, u.full_name as creator_name
             FROM tasks t
             LEFT JOIN projects p ON t.project_id = p.id
             LEFT JOIN users u ON t.created_by = u.id
             {$where}
             ORDER BY t.created_at DESC
             LIMIT {$perPage} OFFSET {$offset}",
            $params
        );
        
        // Task stats
        $taskStats = [
            'total' => $this->db->fetchColumn("SELECT COUNT(*) FROM tasks"),
            'done' => $this->db->fetchColumn("SELECT COUNT(*) FROM tasks WHERE status = 'done'"),
            'in_progress' => $this->db->fetchColumn("SELECT COUNT(*) FROM tasks WHERE status = 'in_progress'"),
            'todo' => $this->db->fetchColumn("SELECT COUNT(*) FROM tasks WHERE status IN ('todo', 'backlog')"),
            'overdue' => $this->db->fetchColumn("SELECT COUNT(*) FROM tasks WHERE due_date < CURDATE() AND status != 'done'"),
        ];
        
        // Get all projects for filter dropdown
        $projects = $this->db->fetchAll(
            "SELECT id, name, color FROM projects ORDER BY name ASC"
        );
        
        $this->view('admin/tasks', [
            'tasks' => $tasks,
            'taskStats' => $taskStats,
            'projects' => $projects,
            'filters' => [
                'status' => $status,
                'project' => $projectId,
                'priority' => $priority,
                'search' => $search,
            ],
            'pagination' => [
                'current' => $page,
                'total' => (int) ceil(max($totalItems, 1) / $perPage),
                'perPage' => $perPage,
                'totalItems' => $totalItems,
                'baseUrl' => '?',
            ],
            'pageTitle' => 'Quản lý công việc',
        ], 'admin');
    }

    public function documents(): void
    {
        $page = max(1, (int)($_GET['page'] ?? 1));
        $search = trim($_GET['search'] ?? '');
        $projectId = $_GET['project'] ?? '';
        $fileType = $_GET['type'] ?? '';
        $perPage = 20;
        $offset = ($page - 1) * $perPage;
        
        $where = "WHERE d.type = 'file'";
        $params = [];
        
        // Search by name
        if ($search) {
            $where .= " AND d.name LIKE ?";
            $params[] = "%{$search}%";
        }
        
        // Filter by project
        if ($projectId) {
            $where .= " AND d.project_id = ?";
            $params[] = $projectId;
        }
        
        // Filter by file type
        if ($fileType) {
            switch ($fileType) {
                case 'image':
                    $where .= " AND d.mime_type LIKE 'image/%'";
                    break;
                case 'pdf':
                    $where .= " AND d.mime_type = 'application/pdf'";
                    break;
                case 'document':
                    $where .= " AND (d.mime_type LIKE 'application/vnd%' OR d.mime_type LIKE 'application/msword%' OR d.mime_type LIKE 'text/%')";
                    break;
                case 'archive':
                    $where .= " AND (d.mime_type LIKE '%zip%' OR d.mime_type LIKE '%rar%' OR d.mime_type LIKE '%tar%')";
                    break;
            }
        }
        
        $totalItems = (int) $this->db->fetchColumn("SELECT COUNT(*) FROM documents d {$where}", $params);
        
        $documents = $this->db->fetchAll(
            "SELECT d.*, u.full_name as uploader_name, p.name as project_name, p.color as project_color
             FROM documents d
             LEFT JOIN users u ON d.uploaded_by = u.id
             LEFT JOIN projects p ON d.project_id = p.id
             {$where}
             ORDER BY d.created_at DESC
             LIMIT {$perPage} OFFSET {$offset}",
            $params
        );
        
        $totalSize = $this->db->fetchColumn("SELECT COALESCE(SUM(file_size), 0) FROM documents WHERE type = 'file'");
        
        // Document stats
        $docStats = [
            'total' => (int) $this->db->fetchColumn("SELECT COUNT(*) FROM documents WHERE type = 'file'"),
            'images' => (int) $this->db->fetchColumn("SELECT COUNT(*) FROM documents WHERE type = 'file' AND mime_type LIKE 'image/%'"),
            'pdfs' => (int) $this->db->fetchColumn("SELECT COUNT(*) FROM documents WHERE type = 'file' AND mime_type = 'application/pdf'"),
            'documents' => (int) $this->db->fetchColumn("SELECT COUNT(*) FROM documents WHERE type = 'file' AND (mime_type LIKE 'application/vnd%' OR mime_type LIKE 'application/msword%' OR mime_type LIKE 'text/%')"),
            'others' => (int) $this->db->fetchColumn("SELECT COUNT(*) FROM documents WHERE type = 'file' AND mime_type NOT LIKE 'image/%' AND mime_type != 'application/pdf' AND mime_type NOT LIKE 'application/vnd%' AND mime_type NOT LIKE 'application/msword%' AND mime_type NOT LIKE 'text/%'"),
        ];
        
        // Get all projects for filter dropdown
        $projects = $this->db->fetchAll(
            "SELECT id, name, color FROM projects ORDER BY name ASC"
        );
        
        $this->view('admin/documents', [
            'documents' => $documents,
            'totalSize' => $totalSize,
            'docStats' => $docStats,
            'projects' => $projects,
            'filters' => [
                'search' => $search,
                'project' => $projectId,
                'type' => $fileType,
            ],
            'pagination' => [
                'current' => $page,
                'total' => (int) ceil(max($totalItems, 1) / $perPage),
                'perPage' => $perPage,
                'totalItems' => $totalItems,
                'baseUrl' => '?',
            ],
            'pageTitle' => 'Quản lý tài liệu',
        ], 'admin');
    }

    public function settings(): void
    {
        // Load all settings
        $settingsRows = $this->db->fetchAll("SELECT setting_key, setting_value FROM system_settings");
        $settings = [];
        foreach ($settingsRows as $row) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
        
        $this->view('admin/settings', [
            'settings' => $settings,
            'pageTitle' => 'Cài đặt hệ thống',
        ], 'admin');
    }

    public function logs(): void
    {
        $page = max(1, (int)($_GET['page'] ?? 1));
        $limit = 50;
        $offset = ($page - 1) * $limit;
        
        $total = $this->db->fetchColumn("SELECT COUNT(*) FROM activity_logs");
        $logs = $this->db->fetchAll(
            "SELECT al.*, u.full_name, u.email
             FROM activity_logs al
             LEFT JOIN users u ON al.user_id = u.id
             ORDER BY al.created_at DESC
             LIMIT ? OFFSET ?",
            [$limit, $offset]
        );
        
        $this->view('admin/logs', [
            'logs' => $logs,
            'total' => $total,
            'page' => $page,
            'totalPages' => ceil($total / $limit),
            'pageTitle' => 'Activity Logs',
        ], 'admin');
    }

    public function backup(): void
    {
        $this->view('admin/backup', [
            'pageTitle' => 'Backup & Restore',
        ], 'admin');
    }

    public function reports(): void
    {
        $period = $_GET['period'] ?? 'month'; // week, month, quarter, year
        
        // Date ranges based on period
        $dateRanges = [
            'week' => ['start' => date('Y-m-d', strtotime('-7 days')), 'end' => date('Y-m-d')],
            'month' => ['start' => date('Y-m-d', strtotime('-30 days')), 'end' => date('Y-m-d')],
            'quarter' => ['start' => date('Y-m-d', strtotime('-90 days')), 'end' => date('Y-m-d')],
            'year' => ['start' => date('Y-m-d', strtotime('-365 days')), 'end' => date('Y-m-d')],
        ];
        $range = $dateRanges[$period] ?? $dateRanges['month'];
        $startDate = $range['start'];
        $endDate = $range['end'] . ' 23:59:59';
        
        // Overview stats - filtered by period
        $overview = [
            'total_users' => (int) $this->db->fetchColumn("SELECT COUNT(*) FROM users"),
            'new_users' => (int) $this->db->fetchColumn(
                "SELECT COUNT(*) FROM users WHERE created_at BETWEEN ? AND ?",
                [$startDate, $endDate]
            ),
            'total_projects' => (int) $this->db->fetchColumn("SELECT COUNT(*) FROM projects"),
            'new_projects' => (int) $this->db->fetchColumn(
                "SELECT COUNT(*) FROM projects WHERE created_at BETWEEN ? AND ?",
                [$startDate, $endDate]
            ),
            'total_tasks' => (int) $this->db->fetchColumn("SELECT COUNT(*) FROM tasks"),
            'completed_tasks' => (int) $this->db->fetchColumn(
                "SELECT COUNT(*) FROM tasks WHERE status = 'done' AND updated_at BETWEEN ? AND ?",
                [$startDate, $endDate]
            ),
            'total_documents' => (int) $this->db->fetchColumn("SELECT COUNT(*) FROM documents WHERE type = 'file'"),
            'new_documents' => (int) $this->db->fetchColumn(
                "SELECT COUNT(*) FROM documents WHERE type = 'file' AND created_at BETWEEN ? AND ?",
                [$startDate, $endDate]
            ),
        ];
        
        // User registration trend - filtered by period
        $userTrend = $this->db->fetchAll(
            "SELECT DATE(created_at) as date, COUNT(*) as count 
             FROM users 
             WHERE created_at BETWEEN ? AND ?
             GROUP BY DATE(created_at)
             ORDER BY date ASC",
            [$startDate, $endDate]
        );
        
        // Task completion trend - filtered by period
        $taskTrend = $this->db->fetchAll(
            "SELECT DATE(updated_at) as date, COUNT(*) as count 
             FROM tasks 
             WHERE status = 'done' AND updated_at BETWEEN ? AND ?
             GROUP BY DATE(updated_at)
             ORDER BY date ASC",
            [$startDate, $endDate]
        );
        
        // Top active users - filtered by period (based on tasks completed in period)
        $topUsers = $this->db->fetchAll(
            "SELECT u.id, u.full_name, u.email, u.avatar_url,
                    (SELECT COUNT(*) FROM tasks WHERE created_by = u.id AND created_at BETWEEN ? AND ?) as tasks_created,
                    (SELECT COUNT(*) FROM tasks t JOIN task_assignees ta ON t.id = ta.task_id 
                     WHERE ta.user_id = u.id AND t.status = 'done' AND t.updated_at BETWEEN ? AND ?) as tasks_completed,
                    (SELECT COUNT(*) FROM projects WHERE created_by = u.id AND created_at BETWEEN ? AND ?) as projects_created,
                    (SELECT COUNT(*) FROM comments WHERE created_by = u.id AND created_at BETWEEN ? AND ?) as comments_count,
                    (
                        (SELECT COUNT(*) FROM tasks WHERE created_by = u.id AND created_at BETWEEN ? AND ?) +
                        (SELECT COUNT(*) FROM tasks t JOIN task_assignees ta ON t.id = ta.task_id WHERE ta.user_id = u.id AND t.status = 'done' AND t.updated_at BETWEEN ? AND ?) * 2 +
                        (SELECT COUNT(*) FROM projects WHERE created_by = u.id AND created_at BETWEEN ? AND ?) * 3 +
                        (SELECT COUNT(*) FROM comments WHERE created_by = u.id AND created_at BETWEEN ? AND ?)
                    ) as activity_score
             FROM users u
             WHERE u.is_active = 1
             ORDER BY activity_score DESC
             LIMIT 10",
            [$startDate, $endDate, $startDate, $endDate, $startDate, $endDate, $startDate, $endDate,
             $startDate, $endDate, $startDate, $endDate, $startDate, $endDate, $startDate, $endDate]
        );
        
        // Project performance - filtered by period (tasks updated in period)
        $projectStats = $this->db->fetchAll(
            "SELECT p.id, p.name, p.color, p.status, p.progress,
                    (SELECT COUNT(*) FROM tasks WHERE project_id = p.id AND created_at BETWEEN ? AND ?) as total_tasks,
                    (SELECT COUNT(*) FROM tasks WHERE project_id = p.id AND status = 'done' AND updated_at BETWEEN ? AND ?) as completed_tasks,
                    (SELECT COUNT(*) FROM tasks WHERE project_id = p.id AND due_date < CURDATE() AND status != 'done') as overdue_tasks,
                    (SELECT COUNT(*) FROM project_members WHERE project_id = p.id) as member_count
             FROM projects p
             WHERE p.created_at <= ?
             ORDER BY total_tasks DESC
             LIMIT 10",
            [$startDate, $endDate, $startDate, $endDate, $endDate]
        );
        
        // Task status distribution - filtered by period
        $taskDistribution = $this->db->fetchAll(
            "SELECT status, COUNT(*) as count FROM tasks 
             WHERE created_at BETWEEN ? AND ?
             GROUP BY status",
            [$startDate, $endDate]
        );
        
        // If no tasks in period, show all tasks distribution
        if (empty($taskDistribution)) {
            $taskDistribution = $this->db->fetchAll(
                "SELECT status, COUNT(*) as count FROM tasks GROUP BY status"
            );
        }
        
        // Task priority distribution - filtered by period
        $priorityDistribution = $this->db->fetchAll(
            "SELECT priority, COUNT(*) as count FROM tasks 
             WHERE created_at BETWEEN ? AND ?
             GROUP BY priority",
            [$startDate, $endDate]
        );
        
        // If no tasks in period, show all
        if (empty($priorityDistribution)) {
            $priorityDistribution = $this->db->fetchAll(
                "SELECT priority, COUNT(*) as count FROM tasks GROUP BY priority"
            );
        }
        
        // Activity summary - filtered by period
        $activitySummary = [
            ['type' => 'new_users', 'label' => 'Người dùng mới', 'total' => (int) $this->db->fetchColumn("SELECT COUNT(*) FROM users WHERE created_at BETWEEN ? AND ?", [$startDate, $endDate]), 'icon' => 'user-plus', 'color' => 'blue'],
            ['type' => 'new_projects', 'label' => 'Dự án mới', 'total' => (int) $this->db->fetchColumn("SELECT COUNT(*) FROM projects WHERE created_at BETWEEN ? AND ?", [$startDate, $endDate]), 'icon' => 'folder-plus', 'color' => 'green'],
            ['type' => 'new_tasks', 'label' => 'Tasks mới', 'total' => (int) $this->db->fetchColumn("SELECT COUNT(*) FROM tasks WHERE created_at BETWEEN ? AND ?", [$startDate, $endDate]), 'icon' => 'plus-square', 'color' => 'yellow'],
            ['type' => 'tasks_done', 'label' => 'Tasks hoàn thành', 'total' => (int) $this->db->fetchColumn("SELECT COUNT(*) FROM tasks WHERE status = 'done' AND updated_at BETWEEN ? AND ?", [$startDate, $endDate]), 'icon' => 'check-circle', 'color' => 'green'],
            ['type' => 'tasks_overdue', 'label' => 'Tasks quá hạn', 'total' => (int) $this->db->fetchColumn("SELECT COUNT(*) FROM tasks WHERE due_date < CURDATE() AND status != 'done'"), 'icon' => 'alert-circle', 'color' => 'red'],
            ['type' => 'new_documents', 'label' => 'Tài liệu mới', 'total' => (int) $this->db->fetchColumn("SELECT COUNT(*) FROM documents WHERE type = 'file' AND created_at BETWEEN ? AND ?", [$startDate, $endDate]), 'icon' => 'file-plus', 'color' => 'purple'],
            ['type' => 'new_comments', 'label' => 'Bình luận mới', 'total' => (int) $this->db->fetchColumn("SELECT COUNT(*) FROM comments WHERE created_at BETWEEN ? AND ?", [$startDate, $endDate]), 'icon' => 'message-circle', 'color' => 'blue'],
            ['type' => 'new_members', 'label' => 'Thành viên mới', 'total' => (int) $this->db->fetchColumn("SELECT COUNT(*) FROM project_members WHERE joined_at BETWEEN ? AND ?", [$startDate, $endDate]), 'icon' => 'users', 'color' => 'green'],
        ];
        
        // Member productivity - filtered by period
        $memberProductivity = $this->db->fetchAll(
            "SELECT u.id, u.full_name, u.email, u.avatar_url, u.role, u.department,
                    (SELECT COUNT(*) FROM tasks WHERE created_by = u.id AND created_at BETWEEN ? AND ?) as tasks_created,
                    (SELECT COUNT(*) FROM tasks t 
                     JOIN task_assignees ta ON t.id = ta.task_id 
                     WHERE ta.user_id = u.id AND t.created_at BETWEEN ? AND ?) as tasks_assigned,
                    (SELECT COUNT(*) FROM tasks t 
                     JOIN task_assignees ta ON t.id = ta.task_id 
                     WHERE ta.user_id = u.id AND t.status = 'done' AND t.updated_at BETWEEN ? AND ?) as tasks_completed,
                    (SELECT COUNT(*) FROM tasks t 
                     JOIN task_assignees ta ON t.id = ta.task_id 
                     WHERE ta.user_id = u.id AND t.status = 'in_progress') as tasks_in_progress,
                    (SELECT COUNT(*) FROM tasks t 
                     JOIN task_assignees ta ON t.id = ta.task_id 
                     WHERE ta.user_id = u.id AND t.due_date < CURDATE() AND t.status != 'done') as tasks_overdue,
                    (SELECT COUNT(*) FROM projects WHERE created_by = u.id AND created_at BETWEEN ? AND ?) as projects_created,
                    (SELECT COUNT(*) FROM project_members WHERE user_id = u.id) as projects_joined,
                    (SELECT COUNT(*) FROM comments WHERE created_by = u.id AND created_at BETWEEN ? AND ?) as comments_count,
                    (SELECT COUNT(*) FROM documents WHERE uploaded_by = u.id AND type = 'file' AND created_at BETWEEN ? AND ?) as documents_uploaded
             FROM users u
             WHERE u.is_active = 1
             ORDER BY tasks_completed DESC, tasks_created DESC
             LIMIT 20",
            [$startDate, $endDate, $startDate, $endDate, $startDate, $endDate, $startDate, $endDate, $startDate, $endDate, $startDate, $endDate]
        );
        
        // Storage by project - filtered by period
        $storageByProject = $this->db->fetchAll(
            "SELECT p.id, p.name, p.color,
                    COUNT(d.id) as file_count,
                    COALESCE(SUM(d.file_size), 0) as total_size
             FROM projects p
             LEFT JOIN documents d ON d.project_id = p.id AND d.type = 'file' AND d.created_at BETWEEN ? AND ?
             GROUP BY p.id
             HAVING file_count > 0
             ORDER BY total_size DESC
             LIMIT 10",
            [$startDate, $endDate]
        );
        
        // Period comparison (current period vs previous period of same length)
        $periodDays = (strtotime($range['end']) - strtotime($range['start'])) / 86400;
        $prevStart = date('Y-m-d', strtotime($range['start'] . " -{$periodDays} days"));
        $prevEnd = date('Y-m-d', strtotime($range['start'] . " -1 day")) . ' 23:59:59';
        
        $currentPeriod = $this->db->fetchOne(
            "SELECT 
                (SELECT COUNT(*) FROM users WHERE created_at BETWEEN ? AND ?) as users,
                (SELECT COUNT(*) FROM projects WHERE created_at BETWEEN ? AND ?) as projects,
                (SELECT COUNT(*) FROM tasks WHERE status = 'done' AND updated_at BETWEEN ? AND ?) as tasks_done",
            [$startDate, $endDate, $startDate, $endDate, $startDate, $endDate]
        );
        
        $previousPeriod = $this->db->fetchOne(
            "SELECT 
                (SELECT COUNT(*) FROM users WHERE created_at BETWEEN ? AND ?) as users,
                (SELECT COUNT(*) FROM projects WHERE created_at BETWEEN ? AND ?) as projects,
                (SELECT COUNT(*) FROM tasks WHERE status = 'done' AND updated_at BETWEEN ? AND ?) as tasks_done",
            [$prevStart, $prevEnd, $prevStart, $prevEnd, $prevStart, $prevEnd]
        );
        
        $this->view('admin/reports', [
            'period' => $period,
            'range' => $range,
            'overview' => $overview,
            'userTrend' => $userTrend,
            'taskTrend' => $taskTrend,
            'topUsers' => $topUsers,
            'projectStats' => $projectStats,
            'taskDistribution' => $taskDistribution,
            'priorityDistribution' => $priorityDistribution,
            'activitySummary' => $activitySummary,
            'memberProductivity' => $memberProductivity,
            'storageByProject' => $storageByProject,
            'currentMonth' => $currentPeriod,
            'previousMonth' => $previousPeriod,
            'pageTitle' => 'Báo cáo & Thống kê',
        ], 'admin');
    }

    // API Methods
    public function createUser(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/php/admin/users.php');
            return;
        }

        $email = trim($_POST['email'] ?? '');
        $fullName = trim($_POST['full_name'] ?? '');
        $password = $_POST['password'] ?? '';
        $role = $_POST['role'] ?? 'member';

        if (empty($email) || empty($fullName) || empty($password)) {
            $this->error('Vui lòng điền đầy đủ thông tin');
            $this->redirect('/php/admin/users.php');
            return;
        }

        if ($this->userModel->findByEmail($email)) {
            $this->error('Email đã được sử dụng');
            $this->redirect('/php/admin/users.php');
            return;
        }

        $userId = $this->userModel->createUser([
            'email' => $email,
            'full_name' => $fullName,
            'password' => $password,
            'role' => $role,
            'position' => $_POST['position'] ?? '',
            'department' => $_POST['department'] ?? '',
            'is_active' => 1,
        ]);

        if ($userId) {
            $this->success('Tạo người dùng thành công');
        } else {
            $this->error('Có lỗi xảy ra');
        }
        
        $this->redirect('/php/admin/users.php');
    }

    public function updateUser(): void
    {
        $userId = $_POST['user_id'] ?? null;
        if (!$userId) {
            $this->json(['success' => false, 'error' => 'Missing user_id'], 400);
            return;
        }

        $data = [];
        if (isset($_POST['role'])) $data['role'] = $_POST['role'];
        if (isset($_POST['is_active'])) $data['is_active'] = $_POST['is_active'] ? 1 : 0;
        if (isset($_POST['full_name'])) $data['full_name'] = $_POST['full_name'];
        if (isset($_POST['position'])) $data['position'] = $_POST['position'];
        if (isset($_POST['department'])) $data['department'] = $_POST['department'];

        if (!empty($data)) {
            $this->userModel->update($userId, $data);
        }

        $this->json(['success' => true, 'message' => 'Cập nhật thành công']);
    }

    public function deleteUser(): void
    {
        $userId = $_POST['user_id'] ?? $_GET['user_id'] ?? null;
        
        if (!$userId) {
            $this->json(['success' => false, 'error' => 'Missing user_id'], 400);
            return;
        }
        
        if ($userId === $this->userId()) {
            $this->json(['success' => false, 'error' => 'Không thể xóa chính mình'], 400);
            return;
        }
        
        // Soft delete
        $this->userModel->update($userId, ['is_active' => 0]);
        $this->json(['success' => true, 'message' => 'Đã vô hiệu hóa người dùng']);
    }

    public function downloadBackup(): void
    {
        $tables = $this->db->fetchAll("SHOW TABLES");
        $dbName = 'taskflow2';
        
        $sql = "-- TaskFlow Database Backup\n";
        $sql .= "-- Generated: " . date('Y-m-d H:i:s') . "\n\n";
        $sql .= "SET FOREIGN_KEY_CHECKS = 0;\n\n";
        
        foreach ($tables as $table) {
            $tableName = array_values($table)[0];
            
            // Get create table
            $createTable = $this->db->fetchOne("SHOW CREATE TABLE `{$tableName}`");
            $sql .= "DROP TABLE IF EXISTS `{$tableName}`;\n";
            $sql .= $createTable['Create Table'] . ";\n\n";
            
            // Get data
            $rows = $this->db->fetchAll("SELECT * FROM `{$tableName}`");
            if (!empty($rows)) {
                foreach ($rows as $row) {
                    $values = array_map(function($v) {
                        if ($v === null) return 'NULL';
                        return "'" . addslashes($v) . "'";
                    }, array_values($row));
                    $sql .= "INSERT INTO `{$tableName}` VALUES (" . implode(', ', $values) . ");\n";
                }
                $sql .= "\n";
            }
        }
        
        $sql .= "SET FOREIGN_KEY_CHECKS = 1;\n";
        
        header('Content-Type: application/sql');
        header('Content-Disposition: attachment; filename="taskflow_backup_' . date('Y-m-d_His') . '.sql"');
        echo $sql;
        exit;
    }
}
