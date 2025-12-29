-- =============================================
-- CALENDAR_EVENTS TABLE - Sự kiện lịch
-- =============================================

DROP TABLE IF EXISTS `calendar_events`;
CREATE TABLE `calendar_events` (
    `id` VARCHAR(36) PRIMARY KEY,
    `title` VARCHAR(255) NOT NULL,
    `description` TEXT NULL,
    `start_date` DATE NOT NULL,
    `end_date` DATE NULL,
    `start_time` TIME NULL,
    `end_time` TIME NULL,
    `is_all_day` TINYINT(1) NOT NULL DEFAULT 1,
    `color` VARCHAR(7) NOT NULL DEFAULT '#6366f1',
    `location` VARCHAR(255) NULL,
    `reminder_minutes` INT NULL,
    `project_id` VARCHAR(36) NULL,
    `created_by` VARCHAR(36) NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    CONSTRAINT `fk_calendar_project` FOREIGN KEY (`project_id`) 
        REFERENCES `projects`(`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_calendar_created_by` FOREIGN KEY (`created_by`) 
        REFERENCES `users`(`id`) ON DELETE SET NULL,
    
    INDEX `idx_calendar_dates` (`start_date`, `end_date`),
    INDEX `idx_calendar_project` (`project_id`),
    INDEX `idx_calendar_created_by` (`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
