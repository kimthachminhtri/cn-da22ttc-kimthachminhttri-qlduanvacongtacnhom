<?php
/**
 * Team Controller
 */

namespace App\Controllers;

use App\Models\User;
use App\Models\Project;
use App\Models\Task;
use App\Middleware\AuthMiddleware;
use App\Middleware\PermissionMiddleware;
use Core\Session;

class TeamController extends BaseController
{
    private User $userModel;
    private Project $projectModel;
    private Task $taskModel;

    public function __construct()
    {
        AuthMiddleware::handle();
        $this->userModel = new User();
        $this->projectModel = new Project();
        $this->taskModel = new Task();
    }

    public function index(): void
    {
        $userRole = $this->userRole();
        $userId = $this->userId();
        $isManager = in_array($userRole, ['admin', 'manager']);
        
        // Get filter parameters
        $projectFilter = $_GET['project'] ?? '';
        $statusFilter = $_GET['status'] ?? 'active';
        
        // Get users based on role
        if ($isManager) {
            $users = $this->userModel->getTeamWithWorkload($userId);
        } else {
            $users = $this->userModel->getAllWithTaskCounts();
        }
        
        // Filter by status
        if ($statusFilter === 'active') {
            $users = array_filter($users, fn($u) => ($u['is_active'] ?? 1) == 1);
        } elseif ($statusFilter === 'inactive') {
            $users = array_filter($users, fn($u) => ($u['is_active'] ?? 1) == 0);
        }
        
        // Get projects for filter dropdown (for managers)
        $projects = [];
        if ($isManager) {
            $projects = $this->projectModel->getManagedProjects($userId);
        }
        
        // Calculate team stats
        $teamStats = [
            'total' => count($users),
            'active' => count(array_filter($users, fn($u) => ($u['is_active'] ?? 1) == 1)),
            'overloaded' => count(array_filter($users, fn($u) => ($u['active_tasks'] ?? 0) > 8)),
            'idle' => count(array_filter($users, fn($u) => ($u['active_tasks'] ?? 0) < 2)),
        ];
        
        $this->view('team/index', [
            'users' => $users,
            'projects' => $projects,
            'teamStats' => $teamStats,
            'isManager' => $isManager,
            'projectFilter' => $projectFilter,
            'statusFilter' => $statusFilter,
            'pageTitle' => 'Nhóm',
        ]);
    }

    public function create(): void
    {
        if (!PermissionMiddleware::can('users.create')) {
            return;
        }

        $errors = $this->validate([
            'full_name' => 'required|min:2',
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        if (!empty($errors)) {
            $this->error(implode(', ', $errors));
            $this->back();
            return;
        }

        $email = $this->input('email');
        
        if ($this->userModel->findByEmail($email)) {
            $this->error('Email đã được sử dụng');
            $this->back();
            return;
        }

        $userId = $this->userModel->createUser([
            'email' => $email,
            'full_name' => $this->input('full_name'),
            'password' => $this->input('password'),
            'role' => $this->input('role', 'member'),
            'position' => $this->input('position', ''),
            'department' => $this->input('department', ''),
            'is_active' => 1,
        ]);

        if ($userId) {
            $this->success('Thêm thành viên thành công');
        } else {
            $this->error('Có lỗi xảy ra');
        }
        
        $this->redirect('/php/team.php');
    }

    public function update(string $id): void
    {
        if (!PermissionMiddleware::can('users.edit')) {
            return;
        }

        $user = $this->userModel->find($id);
        
        if (!$user) {
            $this->json(['success' => false, 'error' => 'User not found'], 404);
            return;
        }

        $data = [];
        
        if ($this->input('full_name')) $data['full_name'] = $this->input('full_name');
        if ($this->input('email')) $data['email'] = $this->input('email');
        if ($this->input('role')) $data['role'] = $this->input('role');
        if ($this->input('position') !== null) $data['position'] = $this->input('position');
        if ($this->input('department') !== null) $data['department'] = $this->input('department');
        if ($this->input('is_active') !== null) $data['is_active'] = (int)$this->input('is_active');

        if (!empty($data)) {
            $this->userModel->update($id, $data);
        }

        $this->json(['success' => true, 'message' => 'User updated']);
    }

    public function delete(string $id): void
    {
        if (!PermissionMiddleware::can('users.delete')) {
            return;
        }

        // Cannot delete self
        if ($id === $this->userId()) {
            $this->json(['success' => false, 'error' => 'Không thể xóa chính mình'], 400);
            return;
        }

        $user = $this->userModel->find($id);
        
        if (!$user) {
            $this->json(['success' => false, 'error' => 'User not found'], 404);
            return;
        }

        // Soft delete - just deactivate
        $this->userModel->update($id, ['is_active' => 0]);
        
        $this->json(['success' => true, 'message' => 'User deactivated']);
    }

    public function activate(string $id): void
    {
        if (!PermissionMiddleware::can('users.edit')) {
            return;
        }

        $user = $this->userModel->find($id);
        
        if (!$user) {
            $this->json(['success' => false, 'error' => 'User not found'], 404);
            return;
        }

        $this->userModel->update($id, ['is_active' => 1]);
        
        $this->json(['success' => true, 'message' => 'User activated']);
    }
}
