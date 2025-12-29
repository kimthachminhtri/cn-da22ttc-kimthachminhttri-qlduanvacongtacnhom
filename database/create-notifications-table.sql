-- =============================================
-- NOTIFICATIONS TABLE - Thông báo
-- =============================================

DROP TABLE IF EXISTS `notifications`;
CREATE TABLE `notifications` (
    `id` VARCHAR(36) PRIMARY KEY,
    `user_id` VARCHAR(36) NOT NULL COMMENT 'Người nhận thông báo',
    `type` VARCHAR(50) NOT NULL COMMENT 'Loại thông báo: task_assigned, comment_added, etc.',
    `title` VARCHAR(255) NOT NULL,
    `message` TEXT NOT NULL,
    `link` VARCHAR(500) NULL COMMENT 'Link đến trang liên quan',
    `data` JSON NULL COMMENT 'Dữ liệu bổ sung (JSON)',
    `actor_id` VARCHAR(36) NULL COMMENT 'Người tạo ra thông báo',
    `is_read` TINYINT(1) NOT NULL DEFAULT 0,
    `read_at` DATETIME NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    CONSTRAINT `fk_notifications_user` FOREIGN KEY (`user_id`) 
        REFERENCES `users`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_notifications_actor` FOREIGN KEY (`actor_id`) 
        REFERENCES `users`(`id`) ON DELETE SET NULL,
    
    INDEX `idx_notifications_user` (`user_id`),
    INDEX `idx_notifications_user_read` (`user_id`, `is_read`),
    INDEX `idx_notifications_type` (`type`),
    INDEX `idx_notifications_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
