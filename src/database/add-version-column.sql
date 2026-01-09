-- Migration: Add version column for optimistic locking
-- Run this to add version column to tasks table

ALTER TABLE `tasks` ADD COLUMN `version` INT UNSIGNED NOT NULL DEFAULT 1 AFTER `updated_at`;

-- Update existing records
UPDATE `tasks` SET `version` = 1 WHERE `version` IS NULL OR `version` = 0;
