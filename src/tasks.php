<?php
/**
 * Tasks Page - Entry Point
 */
require_once __DIR__ . '/bootstrap.php';

use App\Controllers\TaskController;

$controller = new TaskController();
$controller->index();
