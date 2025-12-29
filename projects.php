<?php
/**
 * Projects Page - Entry Point
 */
require_once __DIR__ . '/bootstrap.php';

use App\Controllers\ProjectController;

$controller = new ProjectController();
$controller->index();
