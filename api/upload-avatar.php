<?php
/**
 * API: Upload Avatar
 */
require_once __DIR__ . '/../bootstrap.php';

use App\Controllers\SettingsController;

$controller = new SettingsController();
$controller->uploadAvatar();
