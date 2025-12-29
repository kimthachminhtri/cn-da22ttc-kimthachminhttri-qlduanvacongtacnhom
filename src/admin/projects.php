<?php
/**
 * Admin Projects Page - Entry Point
 */
require_once __DIR__ . '/../bootstrap.php';

use App\Controllers\AdminController;

$controller = new AdminController();
$controller->projects();
