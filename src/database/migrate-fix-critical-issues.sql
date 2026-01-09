-- =============================================
-- TASKFLOW - MIGRATION: FIX CRITICAL ISSUES
-- Version: 1.0
-- Date: 2026-01-08
-- Description: Sửa các lỗi nghiêm trọng trong database schema
-- =============================================

-- Chạy file này nếu database đã tồn tại và cần cập nhật

SET NAMES utf8mb4;

-- =============================================
-- FIX 1: Thêm cột reset_token vào bảng users
-- Dùng cho chức năng quên mật khẩu
-- =============================================
ALTER TABLE `users` 
ADD COLUMN IF NOT EXISTS `reset_token` VARCHAR(64) NULL AFTER `remember_token_expiry`,
ADD COLUMN IF NOT EXISTS `reset_token_expiry` DATETIME NULL AFTER `reset_token`;

-- =============================================
-- FIX 2: Thêm cột version vào bảng tasks
-- Dùng cho optimistic locking tránh race condition
-- =============================================
ALTER TABLE `tasks` 
ADD COLUMN IF NOT EXISTS `version` INT UNSIGNED NOT NULL DEFAULT 1 AFTER `actual_hours`;

-- Update existing records
UPDATE `tasks` SET `version` = 1 WHERE `version` IS NULL OR `version` = 0;

-- =============================================
-- FIX 3: Tạo bảng system_settings
-- Dùng cho cài đặt hệ thống trong Admin Panel
-- =============================================
CREATE TABLE IF NOT EXISTS `system_settings` (
    `setting_key` VARCHAR(100) PRIMARY KEY,
    `setting_value` TEXT NULL,
    `setting_type` ENUM('string', 'number', 'boolean', 'json') DEFAULT 'string',
    `description` VARCHAR(255) NULL,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default settings (ignore if exists)
INSERT IGNORE INTO `system_settings` (`setting_key`, `setting_value`, `setting_type`, `description`) VALUES
('site_name', 'TaskFlow', 'string', 'Tên hệ thống'),
('site_description', 'Hệ thống quản lý công việc và dự án', 'string', 'Mô tả hệ thống'),
('allow_registration', '1', 'boolean', 'Cho phép đăng ký tài khoản mới'),
('max_upload_size', '52428800', 'number', 'Kích thước file upload tối đa (bytes)'),
('maintenance_mode', '0', 'boolean', 'Chế độ bảo trì'),
('default_language', 'vi', 'string', 'Ngôn ngữ mặc định'),
('default_timezone', 'Asia/Ho_Chi_Minh', 'string', 'Múi giờ mặc định');

-- =============================================
-- FIX 4: Thêm cột vào bảng notifications
-- Dùng cho hiển thị actor và link trong thông báo
-- =============================================

-- Kiểm tra và thêm cột actor_id
SET @exist_actor := (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'notifications' AND COLUMN_NAME = 'actor_id');
SET @sql_actor := IF(@exist_actor = 0, 
    'ALTER TABLE `notifications` ADD COLUMN `actor_id` VARCHAR(36) NULL AFTER `user_id`', 
    'SELECT 1');
PREPARE stmt FROM @sql_actor;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Kiểm tra và thêm cột link
SET @exist_link := (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'notifications' AND COLUMN_NAME = 'link');
SET @sql_link := IF(@exist_link = 0, 
    'ALTER TABLE `notifications` ADD COLUMN `link` VARCHAR(500) NULL AFTER `data`', 
    'SELECT 1');
PREPARE stmt FROM @sql_link;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Thêm foreign key cho actor_id (nếu chưa có)
SET @exist_fk := (SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS 
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'notifications' AND CONSTRAINT_NAME = 'fk_notif_actor');
SET @sql_fk := IF(@exist_fk = 0, 
    'ALTER TABLE `notifications` ADD CONSTRAINT `fk_notif_actor` FOREIGN KEY (`actor_id`) REFERENCES `users`(`id`) ON DELETE SET NULL', 
    'SELECT 1');
PREPARE stmt FROM @sql_fk;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Thêm index cho actor_id (nếu chưa có)
SET @exist_idx := (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS 
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'notifications' AND INDEX_NAME = 'idx_notif_actor');
SET @sql_idx := IF(@exist_idx = 0, 
    'CREATE INDEX `idx_notif_actor` ON `notifications`(`actor_id`)', 
    'SELECT 1');
PREPARE stmt FROM @sql_idx;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- =============================================
-- VERIFICATION: Kiểm tra các thay đổi
-- =============================================
SELECT 'Checking users table...' AS status;
SELECT COLUMN_NAME, DATA_TYPE, IS_NULLABLE 
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' 
AND COLUMN_NAME IN ('reset_token', 'reset_token_expiry');

SELECT 'Checking tasks table...' AS status;
SELECT COLUMN_NAME, DATA_TYPE, COLUMN_DEFAULT 
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tasks' 
AND COLUMN_NAME = 'version';

SELECT 'Checking system_settings table...' AS status;
SELECT COUNT(*) AS settings_count FROM system_settings;

SELECT 'Checking notifications table...' AS status;
SELECT COLUMN_NAME, DATA_TYPE, IS_NULLABLE 
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'notifications' 
AND COLUMN_NAME IN ('actor_id', 'link');

SELECT 'Migration completed successfully!' AS result;
