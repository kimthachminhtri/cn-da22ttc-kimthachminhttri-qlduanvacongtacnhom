<?php
/**
 * Bootstrap file - Initialize application
 */

// Define base path
define('BASE_PATH', __DIR__);

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Timezone
date_default_timezone_set('Asia/Ho_Chi_Minh');

// Autoloader
spl_autoload_register(function ($class) {
    // Convert namespace to file path
    $prefix = '';
    $baseDir = BASE_PATH . '/';
    
    // Map namespaces to directories
    $namespaces = [
        'Core\\' => 'core/',
        'App\\Controllers\\' => 'app/controllers/',
        'App\\Models\\' => 'app/models/',
        'App\\Middleware\\' => 'app/middleware/',
        'App\\Services\\' => 'app/services/',
    ];
    
    foreach ($namespaces as $namespace => $dir) {
        if (strpos($class, $namespace) === 0) {
            $relativeClass = substr($class, strlen($namespace));
            $file = $baseDir . $dir . str_replace('\\', '/', $relativeClass) . '.php';
            
            if (file_exists($file)) {
                require_once $file;
                return;
            }
        }
    }
    
    // Fallback for old classes (backward compatibility)
    $oldClassFile = BASE_PATH . '/includes/classes/' . $class . '.php';
    if (file_exists($oldClassFile)) {
        require_once $oldClassFile;
    }
});

// Start session
\Core\Session::start();

// Load helper functions
require_once BASE_PATH . '/includes/functions.php';

/**
 * Helper function to get database instance
 */
function db(): \Core\Database
{
    return \Core\Database::getInstance();
}

/**
 * Helper function to check permission
 */
function can(string $permission): bool
{
    $role = \Core\Session::get('user_role', 'guest');
    return \Core\Permission::can($role, $permission);
}

/**
 * Helper function to get current user ID
 */
function userId(): ?string
{
    return \Core\Session::get('user_id');
}

/**
 * Helper function to get current user role
 */
function userRole(): string
{
    return \Core\Session::get('user_role', 'guest');
}

/**
 * Helper function to check if user is admin
 */
function isAdmin(): bool
{
    return \Core\Permission::isAdmin(userRole());
}

/**
 * Helper function to check if user is manager
 */
function isManager(): bool
{
    return \Core\Permission::isManager(userRole());
}
