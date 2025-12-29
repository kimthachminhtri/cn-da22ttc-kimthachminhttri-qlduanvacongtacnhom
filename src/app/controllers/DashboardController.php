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

    private function getRecentActivities(): array
    {
        // TODO: Implement activity log from database
        return [
            [
                'user_name' => 'Nguyễn Văn A',
                'description' => 'Đã hoàn thành task "Thiết kế UI"',
                'time_ago' => '5 phút trước',
            ],
            [
                'user_name' => 'Trần Thị B',
                'description' => 'Đã tạo dự án mới "Website Redesign"',
                'time_ago' => '1 giờ trước',
            ],
            [
                'user_name' => 'Lê Văn C',
                'description' => 'Đã comment vào task "API Integration"',
                'time_ago' => '2 giờ trước',
            ],
        ];
    }
}
