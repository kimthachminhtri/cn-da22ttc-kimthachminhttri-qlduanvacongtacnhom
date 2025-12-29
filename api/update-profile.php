<?php
/**
 * API: Update Profile
 */
require_once __DIR__ . '/../bootstrap.php';

use App\Controllers\SettingsController;

$controller = new SettingsController();
$controller->updateProfile();
