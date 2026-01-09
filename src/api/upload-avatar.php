<?php
/**
 * API: Upload Avatar
 */
require_once __DIR__ . '/../bootstrap.php';
require_once BASE_PATH . '/includes/csrf.php';
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
	csrf_require();
}

use App\Controllers\SettingsController;

$controller = new SettingsController();
$controller->uploadAvatar();
