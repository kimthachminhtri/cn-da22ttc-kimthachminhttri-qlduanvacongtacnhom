<?php
/**
 * Project Controller
 */

namespace App\Controllers;

use App\Models\Project;
use App\Models\User;
use App\Models\Task;
use App\Middleware\AuthMiddleware;
use App\Middleware\PermissionMiddleware;
use Core\Session;

class ProjectController extends BaseController
{
    private Project $projectModel;
    private User $userModel;
    private Task $taskModel;

    public function __construct()
    {
        AuthMiddleware::handle();
        $this->projectModel = new Project();
        $this->userModel = new User();
        $this->taskModel = new Task();
    }

    public function index(): void
    {
        $userRole = $this->userRole();
        $userId = $this->userId();
        $isManager = in_array($userRole, ['admin', 'manager']);
        
        // Get projects based on role
        if ($isManager) {
            $projects = $this->projectModel->getManagedProjects($userId);
        } else {
            $projects = $this->projectModel->getUserProjectsWithStats($userId);
        }
        
        $users = $this->userModel->getActive();
        
        // Calculate project stats for Manager
        $projectStats = [];
        if ($isManager) {
            $projectStats = $this->projectModel->getManagerProjectStats($userId);
        }
        
        $this->view('projects/index', [
            'projects' => $projects,
            'users' => $users,
            'projectStats' => $projectStats,
            'isManager' => $isManager,
            'pageTitle' => 'Dự án',
            'userRole' => $userRole,
        ]);
    }

    public function show(string $id): void
    {
        $project = $this->projectModel->getWithDetails($id);
        
        if (!$project) {
            $this->redirect('/php/projects.php');
            return;
        }
        
        // Load documents for this project
        $documentModel = new \App\Models\Document();
        $documents = $documentModel->getByProject($id);
        
        $this->view('projects/detail', [
            'project' => $project,
            'documents' => $documents,
            'pageTitle' => $project['name'],
        ]);
    }

    public function create(): void
    {
        if (!PermissionMiddleware::can('projects.create')) {
            return;
        }

        $errors = $this->validate([
            'name' => 'required|min:2',
        ]);

        if (!empty($errors)) {
            $this->error(implode(', ', $errors));
            $this->redirect('/php/projects.php');
            return;
        }

        $projectId = $this->generateUUID();
        
        $this->projectModel->create([
            'id' => $projectId,
            'name' => $this->input('name'),
            'description' => $this->input('description', ''),
            'color' => $this->input('color', '#6366f1'),
            'status' => 'planning',
            'priority' => $this->input('priority', 'medium'),
            'progress' => 0,
            'start_date' => $this->input('startDate') ?: null,
            'end_date' => $this->input('endDate') ?: null,
            'created_by' => $this->userId(),
        ]);

        // Add creator as owner
        $this->projectModel->addMember($projectId, $this->userId(), 'owner');

        $this->success('Tạo dự án thành công');
        $this->redirect('/php/project-detail.php?id=' . $projectId);
    }

    public function update(string $id): void
    {
        $project = $this->projectModel->find($id);
        
        if (!$project) {
            $this->json(['success' => false, 'error' => 'Project not found'], 404);
            return;
        }

        // Check permission
        if (!$this->canEditProject($id)) {
            $this->json(['success' => false, 'error' => 'Permission denied'], 403);
            return;
        }

        $data = [];
        
        if ($this->input('name')) $data['name'] = $this->input('name');
        if ($this->input('description') !== null) $data['description'] = $this->input('description');
        if ($this->input('status')) $data['status'] = $this->input('status');
        if ($this->input('priority')) $data['priority'] = $this->input('priority');

        if (!empty($data)) {
            $this->projectModel->update($id, $data);
        }

        $this->json(['success' => true, 'message' => 'Project updated']);
    }

    public function delete(string $id): void
    {
        if (!PermissionMiddleware::can('projects.delete')) {
            return;
        }

        $project = $this->projectModel->find($id);
        
        if (!$project) {
            $this->json(['success' => false, 'error' => 'Project not found'], 404);
            return;
        }

        $this->projectModel->delete($id);
        $this->json(['success' => true, 'message' => 'Project deleted']);
    }

    private function canEditProject(string $projectId): bool
    {
        if ($this->isAdmin()) return true;
        
        $members = $this->projectModel->getMembers($projectId);
        foreach ($members as $member) {
            if ($member['id'] === $this->userId()) {
                return in_array($member['project_role'], ['owner', 'manager']);
            }
        }
        
        return $this->isManager();
    }

    private function generateUUID(): string
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }
}
