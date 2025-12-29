<?php
/**
 * Admin Backup & Restore
 */
require_once __DIR__ . '/../bootstrap.php';

use App\Controllers\AdminController;

// Handle download
if (isset($_GET['download'])) {
    $controller = new AdminController();
    $controller->downloadBackup();
    exit;
}

$controller = new AdminController();
$controller->backup();
