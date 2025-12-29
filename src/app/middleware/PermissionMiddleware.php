<?php
/**
 * Permission Middleware
 * Handles authorization based on 4 roles: admin, manager, member, guest
 */

namespace App\Middleware;

use Core\Permission;
use Core\Session;
use Core\Router;

class PermissionMiddleware
{
    /**
     * Check if user has permission
     */
    public static function can(string $permission): bool
    {
        $role = AuthMiddleware::userRole();
        
        if (!Permission::can($role, $permission)) {
            self::forbidden();
            return false;
        }
        
        return true;
    }

    /**
     * Check if user has all permissions
     */
    public static function canAll(array $permissions): bool
    {
        $role = AuthMiddleware::userRole();
        
        if (!Permission::canAll($role, $permissions)) {
            self::forbidden();
            return false;
        }
        
        return true;
    }

    /**
     * Check if user has any permission
     */
    public static function canAny(array $permissions): bool
    {
        $role = AuthMiddleware::userRole();
        
        if (!Permission::canAny($role, $permissions)) {
            self::forbidden();
            return false;
        }
        
        return true;
    }

    /**
     * Require admin role
     */
    public static function requireAdmin(): bool
    {
        $role = AuthMiddleware::userRole();
        
        if (!Permission::isAdmin($role)) {
            self::forbidden();
            return false;
        }
        
        return true;
    }

    /**
     * Require manager or higher role
     */
    public static function requireManager(): bool
    {
        $role = AuthMiddleware::userRole();
        
        if (!Permission::isManager($role)) {
            self::forbidden();
            return false;
        }
        
        return true;
    }

    /**
     * Require specific roles
     */
    public static function requireRole(array $roles): bool
    {
        $role = AuthMiddleware::userRole();
        
        if (!in_array($role, $roles)) {
            self::forbidden();
            return false;
        }
        
        return true;
    }

    /**
     * Handle forbidden access
     */
    private static function forbidden(): void
    {
        if (self::isAjax()) {
            http_response_code(403);
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'error' => 'Bạn không có quyền thực hiện hành động này'
            ]);
            exit;
        }
        
        header('Location: /php/403.php');
        exit;
    }

    /**
     * Check if request is AJAX or API request
     */
    private static function isAjax(): bool
    {
        // Check XHR header
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            return true;
        }
        
        // Check if URL contains /api/
        $uri = $_SERVER['REQUEST_URI'] ?? '';
        if (strpos($uri, '/api/') !== false) {
            return true;
        }
        
        // Check if request expects JSON
        $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
        if (strpos($accept, 'application/json') !== false) {
            return true;
        }
        
        // Check if request sends JSON
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        if (strpos($contentType, 'application/json') !== false) {
            return true;
        }
        
        return false;
    }
}
