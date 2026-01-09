<?php
/**
 * Task Constants - Định nghĩa các hằng số cho Task
 * Sử dụng để tránh magic strings trong code
 */

namespace App\Constants;

class TaskConstants
{
    // Task Statuses
    public const STATUS_BACKLOG = 'backlog';
    public const STATUS_TODO = 'todo';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_IN_REVIEW = 'in_review';
    public const STATUS_DONE = 'done';

    public const VALID_STATUSES = [
        self::STATUS_BACKLOG,
        self::STATUS_TODO,
        self::STATUS_IN_PROGRESS,
        self::STATUS_IN_REVIEW,
        self::STATUS_DONE,
    ];

    public const STATUS_LABELS = [
        self::STATUS_BACKLOG => 'Backlog',
        self::STATUS_TODO => 'Cần làm',
        self::STATUS_IN_PROGRESS => 'Đang làm',
        self::STATUS_IN_REVIEW => 'Đang review',
        self::STATUS_DONE => 'Hoàn thành',
    ];

    // Task Priorities
    public const PRIORITY_LOW = 'low';
    public const PRIORITY_MEDIUM = 'medium';
    public const PRIORITY_HIGH = 'high';
    public const PRIORITY_URGENT = 'urgent';

    public const VALID_PRIORITIES = [
        self::PRIORITY_LOW,
        self::PRIORITY_MEDIUM,
        self::PRIORITY_HIGH,
        self::PRIORITY_URGENT,
    ];

    public const PRIORITY_LABELS = [
        self::PRIORITY_LOW => 'Thấp',
        self::PRIORITY_MEDIUM => 'Trung bình',
        self::PRIORITY_HIGH => 'Cao',
        self::PRIORITY_URGENT => 'Khẩn cấp',
    ];

    public const PRIORITY_COLORS = [
        self::PRIORITY_LOW => 'gray',
        self::PRIORITY_MEDIUM => 'blue',
        self::PRIORITY_HIGH => 'orange',
        self::PRIORITY_URGENT => 'red',
    ];

    /**
     * Kiểm tra status có hợp lệ không
     */
    public static function isValidStatus(string $status): bool
    {
        return in_array($status, self::VALID_STATUSES, true);
    }

    /**
     * Kiểm tra priority có hợp lệ không
     */
    public static function isValidPriority(string $priority): bool
    {
        return in_array($priority, self::VALID_PRIORITIES, true);
    }

    /**
     * Lấy label của status
     */
    public static function getStatusLabel(string $status): string
    {
        return self::STATUS_LABELS[$status] ?? $status;
    }

    /**
     * Lấy label của priority
     */
    public static function getPriorityLabel(string $priority): string
    {
        return self::PRIORITY_LABELS[$priority] ?? $priority;
    }
}
