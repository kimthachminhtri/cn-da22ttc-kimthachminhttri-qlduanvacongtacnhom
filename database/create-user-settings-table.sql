-- =============================================
-- USER_SETTINGS TABLE - Cài đặt người dùng
-- =============================================

DROP TABLE IF EXISTS `user_settings`;
CREATE TABLE `user_settings` (
    `user_id` VARCHAR(36) PRIMARY KEY,
    `theme` ENUM('light', 'dark', 'system') NOT NULL DEFAULT 'light',
    `language` VARCHAR(5) NOT NULL DEFAULT 'vi',
    `timezone` VARCHAR(50) NOT NULL DEFAULT 'Asia/Ho_Chi_Minh',
    `notification_email` TINYINT(1) NOT NULL DEFAULT 1,
    `notification_push` TINYINT(1) NOT NULL DEFAULT 1,
    `notification_task_assigned` TINYINT(1) NOT NULL DEFAULT 1,
    `notification_task_due` TINYINT(1) NOT NULL DEFAULT 1,
    `notification_comment` TINYINT(1) NOT NULL DEFAULT 1,
    `notification_mention` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    CONSTRAINT `fk_user_settings_user` FOREIGN KEY (`user_id`) 
        REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
