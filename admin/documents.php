<?php
/**
 * Admin Documents Management
 */
require_once __DIR__ . '/../bootstrap.php';

use App\Controllers\AdminController;

$controller = new AdminController();
$controller->documents();
