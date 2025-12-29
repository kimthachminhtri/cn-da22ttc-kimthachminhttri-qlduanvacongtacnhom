<?php
/**
 * API: Update Member
 */
require_once __DIR__ . '/../bootstrap.php';

use App\Controllers\TeamController;
use App\Middleware\AuthMiddleware;
use App\Middleware\PermissionMiddleware;

AuthMiddleware::handle();

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);
$_POST = array_merge($_POST, $input ?? []);

$userId = $_POST['user_id'] ?? $_GET['id'] ?? '';

if (!$userId) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Missing user_id']);
    exit;
}

$controller = new TeamController();
$controller->update($userId);
