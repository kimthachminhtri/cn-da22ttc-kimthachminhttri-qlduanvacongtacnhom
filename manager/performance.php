<?php
/**
 * Manager Performance Page
 */
require_once __DIR__ . '/../bootstrap.php';

use App\Controllers\ManagerController;

$controller = new ManagerController();
$controller->performance();
