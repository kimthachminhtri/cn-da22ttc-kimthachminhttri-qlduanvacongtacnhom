<?php
/**
 * Core Session Management
 */

namespace Core;

class Session
{
    private static bool $started = false;

    public static function start(): void
    {
        if (self::$started) {
            return;
        }

        if (session_status() === PHP_SESSION_NONE) {
            $configFile = BASE_PATH . '/config/app.php';
            $lifetime = 120; // default 120 minutes
            
            if (file_exists($configFile)) {
                $config = require $configFile;
                $lifetime = $config['session']['lifetime'] ?? 120;
            }
            
            session_set_cookie_params([
                'lifetime' => $lifetime * 60,
                'path' => '/',
                'secure' => isset($_SERVER['HTTPS']),
                'httponly' => true,
                'samesite' => 'Lax',
            ]);
            
            session_start();
        }
        
        self::$started = true;
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        self::start();
        return $_SESSION[$key] ?? $default;
    }

    public static function set(string $key, mixed $value): void
    {
        self::start();
        $_SESSION[$key] = $value;
    }

    public static function has(string $key): bool
    {
        self::start();
        return isset($_SESSION[$key]);
    }

    public static function remove(string $key): void
    {
        self::start();
        unset($_SESSION[$key]);
    }

    public static function flash(string $key, mixed $value = null): mixed
    {
        self::start();
        
        if ($value !== null) {
            $_SESSION['_flash'][$key] = $value;
            return $value;
        }
        
        $flashValue = $_SESSION['_flash'][$key] ?? null;
        unset($_SESSION['_flash'][$key]);
        return $flashValue;
    }

    public static function destroy(): void
    {
        self::start();
        $_SESSION = [];
        
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }
        
        session_destroy();
        self::$started = false;
    }

    public static function regenerate(): void
    {
        self::start();
        session_regenerate_id(true);
    }
}
