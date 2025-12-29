<?php
/**
 * Manager Workload Page
 */
require_once __DIR__ . '/../bootstrap.php';

use App\Controllers\ManagerController;

$controller = new ManagerController();
$controller->workload();
