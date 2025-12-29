<?php
/**
 * Login Page - Entry Point
 */
require_once __DIR__ . '/bootstrap.php';

use App\Controllers\AuthController;
use Core\Session;

$controller = new AuthController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller->login();
} else {
    $controller->showLogin();
}
