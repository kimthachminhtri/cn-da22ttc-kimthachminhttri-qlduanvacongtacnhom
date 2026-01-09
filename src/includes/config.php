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
define('BASE_URL', getenv('BASE_URL') ?: ($_ENV['BASE_URL'] ?? '/php'));

// Cấu hình database
// Cấu hình database - ưu tiên lấy từ biến môi trường (.env)
define('DB_HOST', getenv('DB_HOST') ?: ($_ENV['DB_HOST'] ?? 'localhost'));
define('DB_NAME', getenv('DB_NAME') ?: ($_ENV['DB_NAME'] ?? 'taskflow2'));
define('DB_USER', getenv('DB_USER') ?: ($_ENV['DB_USER'] ?? 'root'));
define('DB_PASS', getenv('DB_PASS') ?: ($_ENV['DB_PASS'] ?? ''));
define('DB_CHARSET', getenv('DB_CHARSET') ?: ($_ENV['DB_CHARSET'] ?? 'utf8mb4'));

// Timezone
date_default_timezone_set('Asia/Ho_Chi_Minh');

// Environment (use APP_ENV environment variable or default to development)
$appEnv = getenv('APP_ENV') ?: ($_ENV['APP_ENV'] ?? 'development');
define('APP_ENV', $appEnv);

// Error reporting: disable display_errors in production
error_reporting(E_ALL);
if (APP_ENV === 'production') {
    ini_set('display_errors', 0);
} else {
    ini_set('display_errors', 1);
}

// Session hardening - configure cookie flags before session_start()
ini_set('session.use_strict_mode', 1);
ini_set('session.cookie_httponly', 1);
// Set secure flag only when HTTPS is present
if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
    ini_set('session.cookie_secure', 1);
}
// samesite support
if (PHP_VERSION_ID >= 70300) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => '',
        'secure' => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
} else {
    ini_set('session.cookie_samesite', 'Lax');
}

// Session - Chỉ start session nếu chưa được start bởi bootstrap.php
// Điều này đảm bảo session chỉ được start một lần duy nhất
// và tránh session fixation vulnerability
if (session_status() === PHP_SESSION_NONE && !defined('SESSION_STARTED_BY_BOOTSTRAP')) {
    session_start();
    // Regenerate session ID để tránh session fixation
    if (empty($_SESSION['_session_initialized'])) {
        session_regenerate_id(true);
        $_SESSION['_session_initialized'] = true;
    }
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
