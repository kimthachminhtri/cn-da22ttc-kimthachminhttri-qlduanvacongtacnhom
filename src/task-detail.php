<?php
/**
 * Task Detail Page - Entry Point
 */
require_once __DIR__ . '/bootstrap.php';

use App\Models\Task;
use App\Models\Project;
use App\Middleware\AuthMiddleware;
use Core\View;

AuthMiddleware::handle();

$taskId = $_GET['id'] ?? '';

if (!$taskId) {
    http_response_code(404);
    View::render('errors/404', [
        'message' => 'ID công việc không hợp lệ.',
        'pageTitle' => 'Không tìm thấy'
    ]);
    exit;
}

$taskModel = new Task();
$projectModel = new Project();

$task = $taskModel->getWithDetails($taskId);

if (!$task) {
    http_response_code(404);
    View::render('errors/404', [
        'message' => 'Công việc không tồn tại hoặc đã bị xóa.',
        'pageTitle' => 'Không tìm thấy'
    ]);
    exit;
}

$project = null;
if (!empty($task['project_id'])) {
    $project = $projectModel->find($task['project_id']);
}

View::render('tasks/detail', [
    'task' => $task,
    'project' => $project,
    'pageTitle' => $task['title'],
]);
