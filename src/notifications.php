<?php
/**
 * Notifications Page - Entry Point
 */
require_once __DIR__ . '/bootstrap.php';

use App\Controllers\NotificationController;

$controller = new NotificationController();
$controller->index();
