<?php
/**
 * Core URL Router
 * Simple routing system for the application
 */

namespace Core;

class Router
{
    private static array $routes = [];
    private static array $namedRoutes = [];

    /**
     * Register GET route
     */
    public static function get(string $path, callable|array $handler, ?string $name = null): void
    {
        self::addRoute('GET', $path, $handler, $name);
    }

    /**
     * Register POST route
     */
    public static function post(string $path, callable|array $handler, ?string $name = null): void
    {
        self::addRoute('POST', $path, $handler, $name);
    }

    /**
     * Register PUT route
     */
    public static function put(string $path, callable|array $handler, ?string $name = null): void
    {
        self::addRoute('PUT', $path, $handler, $name);
    }

    /**
     * Register DELETE route
     */
    public static function delete(string $path, callable|array $handler, ?string $name = null): void
    {
        self::addRoute('DELETE', $path, $handler, $name);
    }

    /**
     * Add route to registry
     */
    private static function addRoute(string $method, string $path, callable|array $handler, ?string $name): void
    {
        $pattern = self::pathToPattern($path);
        
        self::$routes[$method][$pattern] = [
            'handler' => $handler,
            'path' => $path,
        ];
        
        if ($name) {
            self::$namedRoutes[$name] = $path;
        }
    }

    /**
     * Convert path to regex pattern
     */
    private static function pathToPattern(string $path): string
    {
        // Convert {param} to named capture groups
        $pattern = preg_replace('/\{([a-zA-Z_]+)\}/', '(?P<$1>[^/]+)', $path);
        return '#^' . $pattern . '$#';
    }

    /**
     * Dispatch request to handler
     */
    public static function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        // Remove base path
        $config = require BASE_PATH . '/config/app.php';
        $basePath = $config['base_path'] ?? '';
        if ($basePath && strpos($uri, $basePath) === 0) {
            $uri = substr($uri, strlen($basePath)) ?: '/';
        }

        // Handle PUT/DELETE via POST with _method
        if ($method === 'POST' && isset($_POST['_method'])) {
            $method = strtoupper($_POST['_method']);
        }

        // Find matching route
        $routes = self::$routes[$method] ?? [];
        
        foreach ($routes as $pattern => $route) {
            if (preg_match($pattern, $uri, $matches)) {
                // Extract named parameters
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                
                // Call handler
                $handler = $route['handler'];
                
                if (is_array($handler)) {
                    [$class, $method] = $handler;
                    $controller = new $class();
                    call_user_func_array([$controller, $method], $params);
                } else {
                    call_user_func_array($handler, $params);
                }
                return;
            }
        }

        // No route found - 404
        http_response_code(404);
        include BASE_PATH . '/app/views/errors/404.php';
    }

    /**
     * Generate URL for named route
     */
    public static function url(string $name, array $params = []): string
    {
        $path = self::$namedRoutes[$name] ?? $name;
        
        foreach ($params as $key => $value) {
            $path = str_replace('{' . $key . '}', $value, $path);
        }
        
        $config = require BASE_PATH . '/config/app.php';
        return ($config['base_path'] ?? '') . $path;
    }

    /**
     * Redirect to URL
     */
    public static function redirect(string $url, int $status = 302): void
    {
        header("Location: $url", true, $status);
        exit;
    }

    /**
     * Redirect to named route
     */
    public static function redirectTo(string $name, array $params = [], int $status = 302): void
    {
        self::redirect(self::url($name, $params), $status);
    }
}
