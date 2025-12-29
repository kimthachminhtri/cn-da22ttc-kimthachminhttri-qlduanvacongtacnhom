-- Add reset token columns to users table
-- Run this if you don't have these columns

ALTER TABLE `users` 
ADD COLUMN `reset_token` VARCHAR(64) NULL AFTER `remember_token_expiry`,
ADD COLUMN `reset_token_expiry` DATETIME NULL AFTER `reset_token`;

-- Add index for faster lookup
CREATE INDEX `idx_users_reset_token` ON `users` (`reset_token`);
