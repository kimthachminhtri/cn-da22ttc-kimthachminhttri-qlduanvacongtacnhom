<?php
/**
 * Core Permission Handler
 * Handles 4 roles: admin, manager, member, guest
 */

namespace Core;

class Permission
{
    private static ?array $config = null;
    
    private static function loadConfig(): array
    {
        // Always reload config to ensure fresh data
        self::$config = require BASE_PATH . '/config/permissions.php';
        return self::$config;
    }

    /**
     * Check if role has permission
     */
    public static function can(string $role, string $permission): bool
    {
        $config = self::loadConfig();
        $permissions = $config['permissions'][$role] ?? [];
        return in_array($permission, $permissions);
    }

    /**
     * Check if role has all permissions
     */
    public static function canAll(string $role, array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if (!self::can($role, $permission)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Check if role has any permission
     */
    public static function canAny(string $role, array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if (self::can($role, $permission)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get all permissions for role
     */
    public static function getPermissions(string $role): array
    {
        $config = self::loadConfig();
        return $config['permissions'][$role] ?? [];
    }

    /**
     * Get role info
     */
    public static function getRoleInfo(string $role): ?array
    {
        $config = self::loadConfig();
        return $config['roles'][$role] ?? null;
    }

    /**
     * Get all roles
     */
    public static function getAllRoles(): array
    {
        $config = self::loadConfig();
        return $config['roles'];
    }

    /**
     * Check if role is admin
     */
    public static function isAdmin(string $role): bool
    {
        return $role === 'admin';
    }

    /**
     * Check if role is manager or higher
     */
    public static function isManager(string $role): bool
    {
        return in_array($role, ['admin', 'manager']);
    }

    /**
     * Get project role permissions
     */
    public static function getProjectRolePermissions(string $projectRole): array
    {
        $config = self::loadConfig();
        return $config['project_roles'][$projectRole]['permissions'] ?? [];
    }

    /**
     * Check project-level permission
     */
    public static function canInProject(string $projectRole, string $permission): bool
    {
        $permissions = self::getProjectRolePermissions($projectRole);
        return in_array('*', $permissions) || in_array($permission, $permissions);
    }
}
