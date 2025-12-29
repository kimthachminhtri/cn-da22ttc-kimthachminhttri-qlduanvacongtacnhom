<?php
/**
 * Task Controller
 */

namespace App\Controllers;

use App\Models\Task;
use App\Models\Project;
use App\Models\User;
use App\Middleware\AuthMiddleware;
use App\Middleware\PermissionMiddleware;

class TaskController extends BaseController
{
    private Task $taskModel;
    private Project $projectModel;
    private User $userModel;

    public function __construct()
    {
        AuthMiddleware::handle();
        $this->taskModel = new Task();
        $this->projectModel = new Project();
        $this->userModel = new User();
    }

    public function index(): void
    {
        $tasks = $this->taskModel->getUserTasks($this->userId());
        $projects = $this->projectModel->getUserProjects($this->userId());
        $users = $this->userModel->getActive();
        
        $this->view('tasks/index', [
            'tasks' => $tasks,
            'projects' => $projects,
            'users' => $users,
            'pageTitle' => 'Công việc',
            'userRole' => $this->userRole(),
        ]);
    }

    public function show(string $id): void
    {
        $task = $this->taskModel->getWithDetails($id);
        
        if (!$task) {
            $this->redirect('/php/tasks.php');
            return;
        }
        
        $project = null;
        if ($task['project_id']) {
            $project = $this->projectModel->find($task['project_id']);
        }
        
        $users = $this->userModel->getActive();
        
        $this->view('tasks/detail', [
            'task' => $task,
            'project' => $project,
            'users' => $users,
            'pageTitle' => $task['title'],
            'userRole' => $this->userRole(),
        ]);
    }

    public function create(): void
    {
        if (!PermissionMiddleware::can('tasks.create')) {
            return;
        }

        $errors = $this->validate([
            'title' => 'required|min:2',
        ]);

        if (!empty($errors)) {
            $this->error(implode(', ', $errors));
            $this->redirect('/php/tasks.php');
            return;
        }

        $taskId = $this->generateUUID();
        
        $this->taskModel->create([
            'id' => $taskId,
            'project_id' => $this->input('projectId') ?: null,
            'title' => $this->input('title'),
            'description' => $this->input('description', ''),
            'status' => 'todo',
            'priority' => $this->input('priority', 'medium'),
            'position' => 0,
            'due_date' => $this->input('dueDate') ?: null,
            'created_by' => $this->userId(),
        ]);

        // Assign user if specified
        $assigneeId = $this->input('assigneeId');
        if ($assigneeId) {
            $this->taskModel->assignUser($taskId, $assigneeId, $this->userId());
        }

        $this->success('Tạo công việc thành công');
        $this->redirect('/php/task-detail.php?id=' . $taskId);
    }

    public function update(string $id): void
    {
        $task = $this->taskModel->find($id);
        
        if (!$task) {
            $this->json(['success' => false, 'error' => 'Task not found'], 404);
            return;
        }

        // Check permission
        if (!$this->canEditTask($task)) {
            $this->json(['success' => false, 'error' => 'Permission denied'], 403);
            return;
        }

        $data = [];
        
        if ($this->input('title')) $data['title'] = $this->input('title');
        if ($this->input('description') !== null) $data['description'] = $this->input('description');
        if ($this->input('status')) {
            $data['status'] = $this->input('status');
            if ($data['status'] === 'done') {
                $data['completed_at'] = date('Y-m-d H:i:s');
            } else {
                $data['completed_at'] = null;
            }
        }
        if ($this->input('priority')) $data['priority'] = $this->input('priority');
        if ($this->input('due_date') !== null) $data['due_date'] = $this->input('due_date') ?: null;

        if (!empty($data)) {
            $this->taskModel->update($id, $data);
        }

        $this->json(['success' => true, 'message' => 'Task updated']);
    }

    public function delete(string $id): void
    {
        $task = $this->taskModel->find($id);
        
        if (!$task) {
            $this->json(['success' => false, 'error' => 'Task not found'], 404);
            return;
        }

        // Check permission
        if (!$this->canDeleteTask($task)) {
            $this->json(['success' => false, 'error' => 'Bạn không có quyền xóa task này'], 403);
            return;
        }

        $this->taskModel->delete($id);
        $this->json(['success' => true, 'message' => 'Task deleted']);
    }

    private function canEditTask(array $task): bool
    {
        if ($this->isAdmin()) return true;
        if ($task['created_by'] === $this->userId()) return true;
        
        // Check if assigned to this task
        $assignees = $this->taskModel->getAssignees($task['id']);
        foreach ($assignees as $assignee) {
            if ($assignee['id'] === $this->userId()) return true;
        }
        
        return $this->isManager();
    }

    private function canDeleteTask(array $task): bool
    {
        if ($this->isAdmin()) return true;
        if ($task['created_by'] === $this->userId()) return true;
        
        return PermissionMiddleware::can('tasks.delete');
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
