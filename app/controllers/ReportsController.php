<?php
/**
 * Reports Controller
 */

namespace App\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Middleware\AuthMiddleware;
use App\Middleware\PermissionMiddleware;

class ReportsController extends BaseController
{
    private Project $projectModel;
    private Task $taskModel;
    private User $userModel;

    public function __construct()
    {
        AuthMiddleware::handle();
        PermissionMiddleware::can('reports.view');
        
        $this->projectModel = new Project();
        $this->taskModel = new Task();
        $this->userModel = new User();
    }

    public function index(): void
    {
        $userRole = $this->userRole();
        $userId = $this->userId();
        
        // Get time filter
        $period = $_GET['period'] ?? 'month';
        $dateRange = $this->getDateRange($period);
        
        // Admin sees all, Manager/Member sees only their projects
        if ($userRole === 'admin') {
            $projects = $this->projectModel->getAllWithStats();
            $tasks = $this->taskModel->all();
            // Get all users with workload stats for admin
            $users = $this->userModel->getAllWithWorkload();
        } else {
            // Get only projects user manages or participates in
            $projects = $this->projectModel->getManagedProjects($userId);
            $tasks = $this->taskModel->getTeamTasks($userId);
            
            // Get team members from user's projects only
            $users = $this->userModel->getTeamWithWorkload($userId);
        }
        
        // Calculate statistics
        $stats = $this->calculateStats($tasks, $projects);
        
        // Get top performers
        $topPerformers = [];
        if (in_array($userRole, ['admin', 'manager'])) {
            $topPerformers = $this->userModel->getTopPerformers($userId, 5);
        }
        
        // Get recent activities (placeholder)
        $activities = $this->getRecentActivities();
        
        // Get overdue tasks list
        $overdueTasks = array_filter($tasks, fn($t) => 
            !empty($t['due_date']) && 
            strtotime($t['due_date']) < strtotime('today') && 
            ($t['status'] ?? '') !== 'done'
        );
        
        $this->view('reports/index', [
            'projects' => $projects,
            'tasks' => $tasks,
            'users' => $users,
            'stats' => $stats,
            'topPerformers' => $topPerformers,
            'activities' => $activities,
            'overdueTasks' => $overdueTasks,
            'userRole' => $userRole,
            'period' => $period,
            'dateRange' => $dateRange,
            'pageTitle' => 'Báo cáo',
        ]);
    }

    private function getDateRange(string $period): array
    {
        $now = time();
        switch ($period) {
            case 'week':
                return [
                    'start' => date('Y-m-d', strtotime('monday this week')),
                    'end' => date('Y-m-d', strtotime('sunday this week')),
                    'label' => 'Tuần này'
                ];
            case 'month':
                return [
                    'start' => date('Y-m-01'),
                    'end' => date('Y-m-t'),
                    'label' => 'Tháng ' . date('m/Y')
                ];
            case 'quarter':
                $quarter = ceil(date('n') / 3);
                $startMonth = ($quarter - 1) * 3 + 1;
                return [
                    'start' => date('Y-' . str_pad($startMonth, 2, '0', STR_PAD_LEFT) . '-01'),
                    'end' => date('Y-m-t', strtotime(date('Y-' . str_pad($startMonth + 2, 2, '0', STR_PAD_LEFT) . '-01'))),
                    'label' => 'Quý ' . $quarter . '/' . date('Y')
                ];
            case 'year':
                return [
                    'start' => date('Y-01-01'),
                    'end' => date('Y-12-31'),
                    'label' => 'Năm ' . date('Y')
                ];
            default:
                return [
                    'start' => date('Y-m-01'),
                    'end' => date('Y-m-t'),
                    'label' => 'Tháng này'
                ];
        }
    }

    private function calculateStats(array $tasks, array $projects): array
    {
        $totalTasks = count($tasks);
        $completedTasks = count(array_filter($tasks, fn($t) => ($t['status'] ?? '') === 'done'));
        $inProgressTasks = count(array_filter($tasks, fn($t) => ($t['status'] ?? '') === 'in_progress'));
        $overdueTasks = count(array_filter($tasks, fn($t) => 
            !empty($t['due_date']) && 
            strtotime($t['due_date']) < strtotime('today') && 
            ($t['status'] ?? '') !== 'done'
        ));
        $dueThisWeek = count(array_filter($tasks, fn($t) => 
            !empty($t['due_date']) && 
            strtotime($t['due_date']) >= strtotime('today') &&
            strtotime($t['due_date']) <= strtotime('+7 days') &&
            ($t['status'] ?? '') !== 'done'
        ));
        
        $activeProjects = count(array_filter($projects, fn($p) => ($p['status'] ?? '') === 'active'));
        
        return [
            'total_tasks' => $totalTasks,
            'completed_tasks' => $completedTasks,
            'in_progress_tasks' => $inProgressTasks,
            'overdue_tasks' => $overdueTasks,
            'due_this_week' => $dueThisWeek,
            'completion_rate' => $totalTasks > 0 ? round($completedTasks / $totalTasks * 100) : 0,
            'total_projects' => count($projects),
            'active_projects' => $activeProjects,
        ];
    }

    private function getRecentActivities(): array
    {
        return [
            ['user_name' => 'Admin', 'description' => 'Đã tạo dự án mới', 'time_ago' => '1 giờ trước'],
            ['user_name' => 'Manager', 'description' => 'Đã hoàn thành task', 'time_ago' => '2 giờ trước'],
            ['user_name' => 'Member', 'description' => 'Đã upload tài liệu', 'time_ago' => '3 giờ trước'],
        ];
    }
}
