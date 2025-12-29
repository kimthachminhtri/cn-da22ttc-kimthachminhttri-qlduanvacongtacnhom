-- =============================================
-- DOCUMENTS TABLE - Tài liệu
-- =============================================

DROP TABLE IF EXISTS `documents`;
CREATE TABLE `documents` (
    `id` VARCHAR(36) PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `type` ENUM('file', 'folder') NOT NULL DEFAULT 'file',
    `mime_type` VARCHAR(100) NULL,
    `file_size` BIGINT UNSIGNED NULL,
    `file_path` VARCHAR(500) NULL,
    `is_starred` TINYINT(1) NOT NULL DEFAULT 0,
    `parent_id` VARCHAR(36) NULL,
    `project_id` VARCHAR(36) NULL,
    `uploaded_by` VARCHAR(36) NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    CONSTRAINT `fk_documents_parent` FOREIGN KEY (`parent_id`) 
        REFERENCES `documents`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_documents_project` FOREIGN KEY (`project_id`) 
        REFERENCES `projects`(`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_documents_uploaded_by` FOREIGN KEY (`uploaded_by`) 
        REFERENCES `users`(`id`) ON DELETE SET NULL,
    
    INDEX `idx_documents_parent` (`parent_id`),
    INDEX `idx_documents_project` (`project_id`),
    INDEX `idx_documents_type` (`type`),
    INDEX `idx_documents_starred` (`is_starred`),
    INDEX `idx_documents_uploaded_by` (`uploaded_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
