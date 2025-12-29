<?php
/**
 * Manager Controller - Tools for Manager role
 */

namespace App\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Middleware\AuthMiddleware;
use App\Middleware\PermissionMiddleware;
use Core\Session;

class ManagerController extends BaseController
{
    private Project $projectModel;
    private Task $taskModel;
    private User $userModel;

    public function __construct()
    {
        AuthMiddleware::handle();
        
        // Only allow admin and manager
        $userRole = Session::get('user_role', 'guest');
        if (!in_array($userRole, ['admin', 'manager'])) {
            header('Location: /php/index.php');
            exit;
        }
        
        $this->projectModel = new Project();
        $this->taskModel = new Task();
        $this->userModel = new User();
    }

    /**
     * Trang quản lý khối lượng công việc
     */
    public function workload(): void
    {
        $userId = $this->userId();
        
        // Lấy thành viên với thông tin khối lượng công việc
        $teamMembers = $this->userModel->getTeamWithWorkload($userId);
        
        // Get all team tasks
        $teamTasks = $this->taskModel->getTeamTasks($userId);
        
        // Get managed projects
        $projects = $this->projectModel->getManagedProjects($userId);
        
        $this->view('manager/workload', [
            'teamMembers' => $teamMembers,
            'teamTasks' => $teamTasks,
            'projects' => $projects,
            'pageTitle' => 'Quản lý khối lượng công việc',
        ]);
    }

    /**
     * Trang lịch nhóm
     */
    public function teamCalendar(): void
    {
        $userId = $this->userId();
        
        $currentMonth = isset($_GET['month']) ? (int)$_GET['month'] : (int)date('n');
        $currentYear = isset($_GET['year']) ? (int)$_GET['year'] : (int)date('Y');
        
        // Get team members
        $teamMembers = $this->userModel->getTeamWithWorkload($userId);
        
        // Get all team tasks with due dates
        $teamTasks = $this->taskModel->getTeamTasks($userId);
        
        // Filter tasks for current month
        $monthTasks = array_filter($teamTasks, function($task) use ($currentMonth, $currentYear) {
            if (empty($task['due_date'])) return false;
            $taskMonth = (int)date('n', strtotime($task['due_date']));
            $taskYear = (int)date('Y', strtotime($task['due_date']));
            return $taskMonth === $currentMonth && $taskYear === $currentYear;
        });
        
        $this->view('manager/team-calendar', [
            'teamMembers' => $teamMembers,
            'teamTasks' => $monthTasks,
            'allTasks' => $teamTasks,
            'currentMonth' => $currentMonth,
            'currentYear' => $currentYear,
            'pageTitle' => 'Lịch nhóm',
        ]);
    }

    /**
     * Trang hiệu suất
     */
    public function performance(): void
    {
        $userId = $this->userId();
        
        // Get team members with workload
        $teamMembers = $this->userModel->getTeamWithWorkload($userId);
        
        // Get top performers
        $topPerformers = $this->userModel->getTopPerformers($userId, 10);
        
        // Get members needing attention
        $needsAttention = $this->userModel->getMembersNeedingAttention($userId, 10);
        
        // Get task stats
        $taskStats = $this->taskModel->getTeamTaskStats($userId);
        
        // Get project stats
        $projectStats = $this->projectModel->getManagerProjectStats($userId);
        
        // Get managed projects with progress
        $projects = $this->projectModel->getManagedProjects($userId);
        
        $this->view('manager/performance', [
            'teamMembers' => $teamMembers,
            'topPerformers' => $topPerformers,
            'needsAttention' => $needsAttention,
            'taskStats' => $taskStats,
            'projectStats' => $projectStats,
            'projects' => $projects,
            'pageTitle' => 'Hiệu suất nhóm',
        ]);
    }
}
