<?php
/**
 * Admin Reports & Statistics
 */
require_once __DIR__ . '/../bootstrap.php';

use App\Controllers\AdminController;

$controller = new AdminController();
$controller->reports();
