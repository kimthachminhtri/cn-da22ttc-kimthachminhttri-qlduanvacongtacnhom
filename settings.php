<?php
/**
 * Settings Page - Entry Point
 */
require_once __DIR__ . '/bootstrap.php';

use App\Controllers\SettingsController;

$controller = new SettingsController();
$controller->index();
