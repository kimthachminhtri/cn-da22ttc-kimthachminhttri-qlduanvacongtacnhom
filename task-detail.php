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
    header('Location: /php/tasks.php');
    exit;
}

$taskModel = new Task();
$projectModel = new Project();

$task = $taskModel->getWithDetails($taskId);

if (!$task) {
    header('Location: /php/tasks.php');
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
