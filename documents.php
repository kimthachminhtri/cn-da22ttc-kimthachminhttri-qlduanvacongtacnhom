<?php
/**
 * Documents Page - Entry Point
 */
require_once __DIR__ . '/bootstrap.php';

use App\Controllers\DocumentController;

$controller = new DocumentController();
$controller->index();
