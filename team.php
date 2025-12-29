<?php
/**
 * Team Page - Entry Point
 */
require_once __DIR__ . '/bootstrap.php';

use App\Controllers\TeamController;

$controller = new TeamController();
$controller->index();
