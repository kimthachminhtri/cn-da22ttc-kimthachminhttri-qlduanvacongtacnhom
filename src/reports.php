<?php
/**
 * Reports Page - Entry Point
 */
require_once __DIR__ . '/bootstrap.php';

use App\Controllers\ReportsController;

$controller = new ReportsController();
$controller->index();
