<?php
/**
 * Calendar Page - Entry Point
 */
require_once __DIR__ . '/bootstrap.php';

use App\Controllers\CalendarController;

$controller = new CalendarController();
$controller->index();
