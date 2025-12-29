-- =============================================
-- TASKFLOW DATABASE SCHEMA
-- Version: 1.0
-- Database: MySQL 8.0+
-- =============================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- =============================================
-- 1. USERS - Người dùng
-- =============================================
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
    `id` VARCHAR(36) PRIMARY KEY DEFAULT (UUID()),
    `email` VARCHAR(255) NOT NULL UNIQUE,
    `password_hash` VARCHAR(255) NOT NULL,
    `full_name` VARCHAR(100) NOT NULL,
    `avatar_url` VARCHAR(500) NULL,
    `role` ENUM('admin', 'manager', 'member', 'guest') NOT NULL DEFAULT 'member',
    `department` VARCHAR(100) NULL,
    `position` VARCHAR(100) NULL,
    `phone` VARCHAR(20) NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `email_verified_at` DATETIME NULL,
    `remember_token` VARCHAR(64) NULL,
    `remember_token_expiry` DATETIME NULL,
    `last_login_at` DATETIME NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX `idx_users_email` (`email`),
    INDEX `idx_users_role` (`role`),
    INDEX `idx_users_is_active` (`is_active`),
    INDEX `idx_users_department` (`department`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- 2. LABELS - Nhãn
-- =============================================
DROP TABLE IF EXISTS `labels`;
CREATE TABLE `labels` (
    `id` VARCHAR(36) PRIMARY KEY DEFAULT (UUID()),
    `name` VARCHAR(50) NOT NULL,
    `color` VARCHAR(7) NOT NULL DEFAULT '#6366f1',
    `description` VARCHAR(255) NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    INDEX `idx_labels_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- 3. PROJECTS - Dự án
-- =============================================
DROP TABLE IF EXISTS `projects`;
CREATE TABLE `projects` (
    `id` VARCHAR(36) PRIMARY KEY DEFAULT (UUID()),
    `name` VARCHAR(200) NOT NULL,
    `description` TEXT NULL,
    `color` VARCHAR(7) NOT NULL DEFAULT '#6366f1',
    `icon` VARCHAR(50) NULL DEFAULT 'folder',
    `status` ENUM('planning', 'active', 'on_hold', 'completed', 'cancelled') NOT NULL DEFAULT 'planning',
    `priority` ENUM('low', 'medium', 'high', 'urgent') NOT NULL DEFAULT 'medium',
    `progress` TINYINT UNSIGNED NOT NULL DEFAULT 0,
    `start_date` DATE NULL,
    `end_date` DATE NULL,
    `budget` DECIMAL(15, 2) NULL,
    `created_by` VARCHAR(36) NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    CONSTRAINT `fk_projects_created_by` FOREIGN KEY (`created_by`) 
        REFERENCES `users`(`id`) ON DELETE SET NULL,
    
    INDEX `idx_projects_status` (`status`),
    INDEX `idx_projects_priority` (`priority`),
    INDEX `idx_projects_created_by` (`created_by`),
    INDEX `idx_projects_dates` (`start_date`, `end_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- 4. PROJECT_MEMBERS - Thành viên dự án
-- =============================================
DROP TABLE IF EXISTS `project_members`;
CREATE TABLE `project_members` (
    `project_id` VARCHAR(36) NOT NULL,
    `user_id` VARCHAR(36) NOT NULL,
    `role` ENUM('owner', 'manager', 'member', 'viewer') NOT NULL DEFAULT 'member',
    `joined_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    PRIMARY KEY (`project_id`, `user_id`),
    
    CONSTRAINT `fk_pm_project` FOREIGN KEY (`project_id`) 
        REFERENCES `projects`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_pm_user` FOREIGN KEY (`user_id`) 
        REFERENCES `users`(`id`) ON DELETE CASCADE,
    
    INDEX `idx_pm_user` (`user_id`),
    INDEX `idx_pm_role` (`role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- 5. TASKS - Công việc
-- =============================================
DROP TABLE IF EXISTS `tasks`;
CREATE TABLE `tasks` (
    `id` VARCHAR(36) PRIMARY KEY DEFAULT (UUID()),
    `project_id` VARCHAR(36) NULL,
    `title` VARCHAR(500) NOT NULL,
    `description` TEXT NULL,
    `status` ENUM('backlog', 'todo', 'in_progress', 'in_review', 'done') NOT NULL DEFAULT 'todo',
    `priority` ENUM('low', 'medium', 'high', 'urgent') NOT NULL DEFAULT 'medium',
    `position` INT UNSIGNED NOT NULL DEFAULT 0,
    `start_date` DATE NULL,
    `due_date` DATE NULL,
    `completed_at` DATETIME NULL,
    `estimated_hours` DECIMAL(6, 2) NULL,
    `actual_hours` DECIMAL(6, 2) NULL,
    `created_by` VARCHAR(36) NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    CONSTRAINT `fk_tasks_project` FOREIGN KEY (`project_id`) 
        REFERENCES `projects`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_tasks_created_by` FOREIGN KEY (`created_by`) 
        REFERENCES `users`(`id`) ON DELETE SET NULL,
    
    INDEX `idx_tasks_project` (`project_id`),
    INDEX `idx_tasks_status` (`status`),
    INDEX `idx_tasks_priority` (`priority`),
    INDEX `idx_tasks_due_date` (`due_date`),
    INDEX `idx_tasks_position` (`position`),
    INDEX `idx_tasks_created_by` (`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- 6. TASK_ASSIGNEES - Người được giao task
-- =============================================
DROP TABLE IF EXISTS `task_assignees`;
CREATE TABLE `task_assignees` (
    `task_id` VARCHAR(36) NOT NULL,
    `user_id` VARCHAR(36) NOT NULL,
    `assigned_by` VARCHAR(36) NULL,
    `assigned_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    PRIMARY KEY (`task_id`, `user_id`),
    
    CONSTRAINT `fk_ta_task` FOREIGN KEY (`task_id`) 
        REFERENCES `tasks`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_ta_user` FOREIGN KEY (`user_id`) 
        REFERENCES `users`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_ta_assigned_by` FOREIGN KEY (`assigned_by`) 
        REFERENCES `users`(`id`) ON DELETE SET NULL,
    
    INDEX `idx_ta_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- 7. TASK_LABELS - Nhãn của task
-- =============================================
DROP TABLE IF EXISTS `task_labels`;
CREATE TABLE `task_labels` (
    `task_id` VARCHAR(36) NOT NULL,
    `label_id` VARCHAR(36) NOT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    PRIMARY KEY (`task_id`, `label_id`),
    
    CONSTRAINT `fk_tl_task` FOREIGN KEY (`task_id`) 
        REFERENCES `tasks`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_tl_label` FOREIGN KEY (`label_id`) 
        REFERENCES `labels`(`id`) ON DELETE CASCADE,
    
    INDEX `idx_tl_label` (`label_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- 8. TASK_CHECKLISTS - Checklist của task
-- =============================================
DROP TABLE IF EXISTS `task_checklists`;
CREATE TABLE `task_checklists` (
    `id` VARCHAR(36) PRIMARY KEY DEFAULT (UUID()),
    `task_id` VARCHAR(36) NOT NULL,
    `title` VARCHAR(500) NOT NULL,
    `is_completed` TINYINT(1) NOT NULL DEFAULT 0,
    `position` INT UNSIGNED NOT NULL DEFAULT 0,
    `completed_at` DATETIME NULL,
    `completed_by` VARCHAR(36) NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    CONSTRAINT `fk_tc_task` FOREIGN KEY (`task_id`) 
        REFERENCES `tasks`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_tc_completed_by` FOREIGN KEY (`completed_by`) 
        REFERENCES `users`(`id`) ON DELETE SET NULL,
    
    INDEX `idx_tc_task` (`task_id`),
    INDEX `idx_tc_position` (`position`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- 9. DOCUMENTS - Tài liệu
-- =============================================
DROP TABLE IF EXISTS `documents`;
CREATE TABLE `documents` (
    `id` VARCHAR(36) PRIMARY KEY DEFAULT (UUID()),
    `name` VARCHAR(255) NOT NULL,
    `description` TEXT NULL,
    `type` ENUM('folder', 'file') NOT NULL DEFAULT 'file',
    `mime_type` VARCHAR(100) NULL,
    `file_size` BIGINT UNSIGNED NULL,
    `file_path` VARCHAR(500) NULL,
    `parent_id` VARCHAR(36) NULL,
    `project_id` VARCHAR(36) NULL,
    `is_starred` TINYINT(1) NOT NULL DEFAULT 0,
    `download_count` INT UNSIGNED NOT NULL DEFAULT 0,
    `uploaded_by` VARCHAR(36) NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    CONSTRAINT `fk_docs_parent` FOREIGN KEY (`parent_id`) 
        REFERENCES `documents`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_docs_project` FOREIGN KEY (`project_id`) 
        REFERENCES `projects`(`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_docs_uploaded_by` FOREIGN KEY (`uploaded_by`) 
        REFERENCES `users`(`id`) ON DELETE SET NULL,
    
    INDEX `idx_docs_parent` (`parent_id`),
    INDEX `idx_docs_project` (`project_id`),
    INDEX `idx_docs_type` (`type`),
    INDEX `idx_docs_starred` (`is_starred`),
    INDEX `idx_docs_uploaded_by` (`uploaded_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- 10. DOCUMENT_SHARES - Chia sẻ tài liệu
-- =============================================
DROP TABLE IF EXISTS `document_shares`;
CREATE TABLE `document_shares` (
    `document_id` VARCHAR(36) NOT NULL,
    `user_id` VARCHAR(36) NOT NULL,
    `permission` ENUM('view', 'edit') NOT NULL DEFAULT 'view',
    `shared_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `shared_by` VARCHAR(36) NULL,
    
    PRIMARY KEY (`document_id`, `user_id`),
    
    CONSTRAINT `fk_ds_document` FOREIGN KEY (`document_id`) 
        REFERENCES `documents`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_ds_user` FOREIGN KEY (`user_id`) 
        REFERENCES `users`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_ds_shared_by` FOREIGN KEY (`shared_by`) 
        REFERENCES `users`(`id`) ON DELETE SET NULL,
    
    INDEX `idx_ds_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- 11. COMMENTS - Bình luận
-- =============================================
DROP TABLE IF EXISTS `comments`;
CREATE TABLE `comments` (
    `id` VARCHAR(36) PRIMARY KEY DEFAULT (UUID()),
    `entity_type` ENUM('task', 'document', 'project') NOT NULL DEFAULT 'task',
    `entity_id` VARCHAR(36) NOT NULL,
    `content` TEXT NOT NULL,
    `parent_id` VARCHAR(36) NULL,
    `created_by` VARCHAR(36) NOT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    CONSTRAINT `fk_comments_parent` FOREIGN KEY (`parent_id`) 
        REFERENCES `comments`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_comments_user` FOREIGN KEY (`created_by`) 
        REFERENCES `users`(`id`) ON DELETE CASCADE,
    
    INDEX `idx_comments_entity` (`entity_type`, `entity_id`),
    INDEX `idx_comments_parent` (`parent_id`),
    INDEX `idx_comments_created_by` (`created_by`),
    INDEX `idx_comments_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- 12. NOTIFICATIONS - Thông báo
-- =============================================
DROP TABLE IF EXISTS `notifications`;
CREATE TABLE `notifications` (
    `id` VARCHAR(36) PRIMARY KEY DEFAULT (UUID()),
    `user_id` VARCHAR(36) NOT NULL,
    `type` VARCHAR(50) NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `message` TEXT NULL,
    `data` JSON NULL,
    `is_read` TINYINT(1) NOT NULL DEFAULT 0,
    `read_at` DATETIME NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    CONSTRAINT `fk_notif_user` FOREIGN KEY (`user_id`) 
        REFERENCES `users`(`id`) ON DELETE CASCADE,
    
    INDEX `idx_notif_user` (`user_id`),
    INDEX `idx_notif_is_read` (`is_read`),
    INDEX `idx_notif_type` (`type`),
    INDEX `idx_notif_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- 13. ACTIVITY_LOGS - Lịch sử hoạt động
-- =============================================
DROP TABLE IF EXISTS `activity_logs`;
CREATE TABLE `activity_logs` (
    `id` VARCHAR(36) PRIMARY KEY DEFAULT (UUID()),
    `user_id` VARCHAR(36) NULL,
    `entity_type` VARCHAR(50) NOT NULL,
    `entity_id` VARCHAR(36) NOT NULL,
    `action` VARCHAR(50) NOT NULL,
    `old_values` JSON NULL,
    `new_values` JSON NULL,
    `ip_address` VARCHAR(45) NULL,
    `user_agent` VARCHAR(500) NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    CONSTRAINT `fk_activity_user` FOREIGN KEY (`user_id`) 
        REFERENCES `users`(`id`) ON DELETE SET NULL,
    
    INDEX `idx_activity_user` (`user_id`),
    INDEX `idx_activity_entity` (`entity_type`, `entity_id`),
    INDEX `idx_activity_action` (`action`),
    INDEX `idx_activity_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- 14. CALENDAR_EVENTS - Sự kiện lịch
-- =============================================
DROP TABLE IF EXISTS `calendar_events`;
CREATE TABLE `calendar_events` (
    `id` VARCHAR(36) PRIMARY KEY DEFAULT (UUID()),
    `title` VARCHAR(255) NOT NULL,
    `description` TEXT NULL,
    `type` ENUM('meeting', 'deadline', 'reminder', 'event') NOT NULL DEFAULT 'event',
    `color` VARCHAR(7) NULL,
    `start_time` DATETIME NOT NULL,
    `end_time` DATETIME NULL,
    `is_all_day` TINYINT(1) NOT NULL DEFAULT 0,
    `location` VARCHAR(255) NULL,
    `project_id` VARCHAR(36) NULL,
    `task_id` VARCHAR(36) NULL,
    `created_by` VARCHAR(36) NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    CONSTRAINT `fk_events_project` FOREIGN KEY (`project_id`) 
        REFERENCES `projects`(`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_events_task` FOREIGN KEY (`task_id`) 
        REFERENCES `tasks`(`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_events_created_by` FOREIGN KEY (`created_by`) 
        REFERENCES `users`(`id`) ON DELETE SET NULL,
    
    INDEX `idx_events_dates` (`start_time`, `end_time`),
    INDEX `idx_events_type` (`type`),
    INDEX `idx_events_project` (`project_id`),
    INDEX `idx_events_created_by` (`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- 15. EVENT_ATTENDEES - Người tham dự sự kiện
-- =============================================
DROP TABLE IF EXISTS `event_attendees`;
CREATE TABLE `event_attendees` (
    `event_id` VARCHAR(36) NOT NULL,
    `user_id` VARCHAR(36) NOT NULL,
    `status` ENUM('pending', 'accepted', 'declined', 'tentative') NOT NULL DEFAULT 'pending',
    `responded_at` DATETIME NULL,
    
    PRIMARY KEY (`event_id`, `user_id`),
    
    CONSTRAINT `fk_ea_event` FOREIGN KEY (`event_id`) 
        REFERENCES `calendar_events`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_ea_user` FOREIGN KEY (`user_id`) 
        REFERENCES `users`(`id`) ON DELETE CASCADE,
    
    INDEX `idx_ea_user` (`user_id`),
    INDEX `idx_ea_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- 16. USER_SETTINGS - Cài đặt người dùng
-- =============================================
DROP TABLE IF EXISTS `user_settings`;
CREATE TABLE `user_settings` (
    `user_id` VARCHAR(36) PRIMARY KEY,
    `theme` ENUM('light', 'dark', 'system') NOT NULL DEFAULT 'system',
    `language` VARCHAR(10) NOT NULL DEFAULT 'vi',
    `timezone` VARCHAR(50) NOT NULL DEFAULT 'Asia/Ho_Chi_Minh',
    `notification_email` TINYINT(1) NOT NULL DEFAULT 1,
    `notification_push` TINYINT(1) NOT NULL DEFAULT 1,
    `notification_task_assigned` TINYINT(1) NOT NULL DEFAULT 1,
    `notification_task_due` TINYINT(1) NOT NULL DEFAULT 1,
    `notification_comment` TINYINT(1) NOT NULL DEFAULT 1,
    `notification_mention` TINYINT(1) NOT NULL DEFAULT 1,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    CONSTRAINT `fk_settings_user` FOREIGN KEY (`user_id`) 
        REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
