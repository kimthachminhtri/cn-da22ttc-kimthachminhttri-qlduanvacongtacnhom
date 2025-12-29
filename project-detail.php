<?php
/**
 * Project Detail Page - Entry Point
 */
require_once __DIR__ . '/bootstrap.php';

use App\Models\Project;
use App\Models\Task;
use App\Models\Document;
use App\Middleware\AuthMiddleware;
use Core\View;

AuthMiddleware::handle();

$projectId = $_GET['id'] ?? '';

if (!$projectId) {
    header('Location: /php/projects.php');
    exit;
}

$projectModel = new Project();
$taskModel = new Task();
$documentModel = new Document();

$project = $projectModel->getWithDetails($projectId);

if (!$project) {
    header('Location: /php/projects.php');
    exit;
}

$tasks = $taskModel->getByProject($projectId);
$documents = $documentModel->getByProject($projectId);

View::render('projects/detail', [
    'project' => $project,
    'tasks' => $tasks,
    'documents' => $documents,
    'pageTitle' => $project['name'],
]);
