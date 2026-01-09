<?php
/**
 * API: Change Password
 */
require_once __DIR__ . '/../bootstrap.php';
require_once BASE_PATH . '/includes/csrf.php';

use App\Controllers\SettingsController;

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
	csrf_require();
}

$controller = new SettingsController();
$controller->changePassword();
