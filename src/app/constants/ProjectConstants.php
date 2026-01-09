<?php
/**
 * Project Constants - Định nghĩa các hằng số cho Project
 */

namespace App\Constants;

class ProjectConstants
{
    // Project Member Roles
    public const ROLE_OWNER = 'owner';
    public const ROLE_MANAGER = 'manager';
    public const ROLE_MEMBER = 'member';
    public const ROLE_VIEWER = 'viewer';

    public const VALID_ROLES = [
        self::ROLE_OWNER,
        self::ROLE_MANAGER,
        self::ROLE_MEMBER,
        self::ROLE_VIEWER,
    ];

    public const ROLE_LABELS = [
        self::ROLE_OWNER => 'Chủ sở hữu',
        self::ROLE_MANAGER => 'Quản lý',
        self::ROLE_MEMBER => 'Thành viên',
        self::ROLE_VIEWER => 'Người xem',
    ];

    // Project Statuses
    public const STATUS_ACTIVE = 'active';
    public const STATUS_ARCHIVED = 'archived';
    public const STATUS_COMPLETED = 'completed';

    public const VALID_STATUSES = [
        self::STATUS_ACTIVE,
        self::STATUS_ARCHIVED,
        self::STATUS_COMPLETED,
    ];

    // Project Colors
    public const DEFAULT_COLORS = [
        '#3B82F6', // blue
        '#10B981', // green
        '#F59E0B', // yellow
        '#EF4444', // red
        '#8B5CF6', // purple
        '#EC4899', // pink
        '#06B6D4', // cyan
        '#F97316', // orange
    ];

    /**
     * Kiểm tra role có hợp lệ không
     */
    public static function isValidRole(string $role): bool
    {
        return in_array($role, self::VALID_ROLES, true);
    }

    /**
     * Kiểm tra role có quyền quản lý không
     */
    public static function canManage(string $role): bool
    {
        return in_array($role, [self::ROLE_OWNER, self::ROLE_MANAGER], true);
    }

    /**
     * Lấy label của role
     */
    public static function getRoleLabel(string $role): string
    {
        return self::ROLE_LABELS[$role] ?? $role;
    }
}
