<?php
/**
 * Cấu hình chung cho ứng dụng
 * Legacy config - backward compatibility with new structure
 */

// Define BASE_PATH if not defined (for new structure)
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
}

// Cấu hình cơ bản
define('SITE_NAME', 'TaskFlow');
define('SITE_DESCRIPTION', 'Modern project management and team collaboration platform');
define('BASE_URL', '/php');

// Cấu hình database
define('DB_HOST', 'localhost');
define('DB_NAME', 'taskflow2');
define('DB_USER', 'root');
define('DB_PASS', '');

// Timezone
date_default_timezone_set('Asia/Ho_Chi_Minh');

// Error reporting (tắt trong production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Hàm autoload classes
 */
spl_autoload_register(function ($class) {
    $file = __DIR__ . '/classes/' . $class . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

/**
 * Helper function để lấy database instance
 */
function db(): Database
{
    return Database::getInstance();
}

/**
 * Helper function để lấy model instance
 */
function model(string $name): Model
{
    $className = ucfirst($name);
    return new $className();
}

/**
 * Helper function để lấy Auth instance
 */
function auth(): Auth
{
    return Auth::getInstance();
}
