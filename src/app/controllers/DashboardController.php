<?php
/**
 * Dashboard Controller
 */

namespace App\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Middleware\AuthMiddleware;
use Core\Session;

class DashboardController extends BaseController
{
    private Project $projectModel;
    private Task $taskModel;
    private User $userModel;

    public function __construct()
    {
        AuthMiddleware::handle();
        $this->projectModel = new Project();
        $this->taskModel = new Task();
        $this->userModel = new User();
    }

    public function index(): void
    {
        $userRole = $this->userRole();
        $userId = $this->userId();
        
        // Check if user is Manager or Admin - show enhanced dashboard
        if (in_array($userRole, ['admin', 'manager'])) {
            $this->managerDashboard($userId, $userRole);
        } else {
            $this->memberDashboard($userId);
        }
    }

    /**
     * Enhanced dashboard for Manager/Admin
     */
    private function managerDashboard(string $userId, string $userRole): void
    {
        // Get managed projects with stats
        $projects = $this->projectModel->getManagedProjects($userId);
        $projectStats = $this->projectModel->getManagerProjectStats($userId);
        
        // Get team tasks and stats
        $teamTasks = $this->taskModel->getTeamTasks($userId);
        $taskStats = $this->taskModel->getTeamTaskStats($userId);
        
        // Get team members with workload
        $teamMembers = $this->userModel->getTeamWithWorkload($userId);
        
        // Get top performers
        $topPerformers = $this->userModel->getTopPerformers($userId, 5);
        
        // Get members needing attention
        $needsAttention = $this->userModel->getMembersNeedingAttention($userId, 5);
        
        // Get overdue tasks grouped by assignee
        $overdueTasks = $this->taskModel->getOverdueTasksByAssignee($userId);
        
        // Get tasks due this week
        $dueThisWeek = $this->taskModel->getTeamTasksDueThisWeek($userId);
        
        // Get tasks for Gantt chart
        $ganttTasks = $this->taskModel->getTasksForGantt($userId);
        
        // Get recent activities
        $activities = $this->getRecentActivities();
        
        $this->view('dashboard/index', [
            'projects' => $projects,
            'projectStats' => $projectStats,
            'tasks' => $teamTasks,
            'taskStats' => $taskStats,
            'users' => $teamMembers,
            'topPerformers' => $topPerformers,
            'needsAttention' => $needsAttention,
            'overdueTasks' => $overdueTasks,
            'dueThisWeek' => $dueThisWeek,
            'ganttTasks' => $ganttTasks,
            'activities' => $activities,
            'isManager' => true,
            'userRole' => $userRole,
            'pageTitle' => 'Dashboard',
        ]);
    }

    /**
     * Standard dashboard for Member
     */
    private function memberDashboard(string $userId): void
    {
        // Get user's projects
        $projects = $this->projectModel->getUserProjects($userId);
        
        // Get user's tasks
        $tasks = $this->taskModel->getUserTasks($userId);
        
        // Get team members
        $users = $this->userModel->getActive();
        
        // Get recent activities
        $activities = $this->getRecentActivities();
        
        // Get tasks for Gantt chart
        $ganttTasks = $this->taskModel->getTasksForGantt($userId);
        
        $this->view('dashboard/index', [
            'projects' => $projects,
            'tasks' => $tasks,
            'users' => $users,
            'activities' => $activities,
            'ganttTasks' => $ganttTasks,
            'isManager' => false,
            'pageTitle' => 'Dashboard',
        ]);
    }

    /**
     * Get recent activities from database
     * Query từ bảng activity_logs với format phù hợp cho hiển thị
     */
    private function getRecentActivities(int $limit = 10): array
    {
        $db = \Core\Database::getInstance();
        
        $activities = $db->fetchAll(
            "SELECT al.*, u.full_name as user_name, u.avatar_url
             FROM activity_logs al
             LEFT JOIN users u ON al.user_id = u.id
             ORDER BY al.created_at DESC
             LIMIT ?",
            [$limit]
        );
        
        // Format activities for display
        $formatted = [];
        foreach ($activities as $activity) {
            $description = $this->formatActivityDescription($activity);
            $formatted[] = [
                'id' => $activity['id'],
                'user_id' => $activity['user_id'],
                'user_name' => $activity['user_name'] ?? 'Hệ thống',
                'avatar_url' => $activity['avatar_url'] ?? null,
                'description' => $description,
                'entity_type' => $activity['entity_type'],
                'entity_id' => $activity['entity_id'],
                'action' => $activity['action'],
                'time_ago' => $this->timeAgo($activity['created_at']),
                'created_at' => $activity['created_at'],
            ];
        }
        
        return $formatted;
    }

    /**
     * Format activity description based on action and entity type
     */
    private function formatActivityDescription(array $activity): string
    {
        $action = $activity['action'] ?? '';
        $entityType = $activity['entity_type'] ?? '';
        $newValues = $activity['new_values'] ? json_decode($activity['new_values'], true) : [];
        
        $entityNames = [
            'task' => 'công việc',
            'project' => 'dự án',
            'document' => 'tài liệu',
            'comment' => 'bình luận',
            'user' => 'người dùng',
        ];
        
        $actionNames = [
            'create' => 'đã tạo',
            'update' => 'đã cập nhật',
            'delete' => 'đã xóa',
            'complete' => 'đã hoàn thành',
            'assign' => 'đã giao',
            'comment' => 'đã bình luận vào',
            'upload' => 'đã tải lên',
            'login' => 'đã đăng nhập',
            'logout' => 'đã đăng xuất',
        ];
        
        $entityName = $entityNames[$entityType] ?? $entityType;
        $actionName = $actionNames[$action] ?? $action;
        
        // Get entity title/name from new_values if available
        $title = $newValues['title'] ?? $newValues['name'] ?? '';
        
        if ($title) {
            return "{$actionName} {$entityName} \"{$title}\"";
        }
        
        return "{$actionName} {$entityName}";
    }

    /**
     * Calculate time ago from datetime
     */
    private function timeAgo(string $datetime): string
    {
        $time = strtotime($datetime);
        $diff = time() - $time;
        
        if ($diff < 60) return 'Vừa xong';
        if ($diff < 3600) return floor($diff / 60) . ' phút trước';
        if ($diff < 86400) return floor($diff / 3600) . ' giờ trước';
        if ($diff < 604800) return floor($diff / 86400) . ' ngày trước';
        if ($diff < 2592000) return floor($diff / 604800) . ' tuần trước';
        
        return date('d/m/Y', $time);
    }
}
