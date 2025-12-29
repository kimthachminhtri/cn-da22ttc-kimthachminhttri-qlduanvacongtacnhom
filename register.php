<?php
/**
 * Register Page - Entry Point
 */
require_once __DIR__ . '/bootstrap.php';

use App\Controllers\AuthController;

$controller = new AuthController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller->register();
} else {
    $controller->showRegister();
}
