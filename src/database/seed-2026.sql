-- =============================================
-- TASKFLOW - DỮ LIỆU MẪU CHO NĂM 2026
-- Version: 2.1
-- =============================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- Xóa dữ liệu cũ
DELETE FROM `event_attendees`;
DELETE FROM `calendar_events`;
DELETE FROM `activity_logs`;
DELETE FROM `notifications`;
DELETE FROM `comments`;
DELETE FROM `document_shares`;
DELETE FROM `documents`;
DELETE FROM `task_checklists`;
DELETE FROM `task_labels`;
DELETE FROM `task_assignees`;
DELETE FROM `tasks`;
DELETE FROM `project_members`;
DELETE FROM `projects`;
DELETE FROM `labels`;
DELETE FROM `user_settings`;
DELETE FROM `users`;

-- =============================================
-- 1. USERS
-- =============================================
INSERT INTO `users` (`id`, `email`, `password_hash`, `full_name`, `avatar_url`, `role`, `department`, `position`, `phone`, `is_active`, `email_verified_at`, `last_login_at`, `created_at`) VALUES
('USR-001-ADMIN-CEO', 'ceo@saigontech.vn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Nguyen Van Minh', NULL, 'admin', 'Ban Giam doc', 'CEO', '0903123456', 1, '2024-01-01 08:00:00', '2026-01-09 08:30:00', '2024-01-01 00:00:00'),
('USR-002-ADMIN-CTO', 'cto@saigontech.vn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Tran Thi Huong', NULL, 'admin', 'Ban Giam doc', 'CTO', '0903123457', 1, '2024-01-01 08:00:00', '2026-01-09 09:15:00', '2024-01-01 00:00:00'),
('USR-003-MGR-PM01', 'pm.hung@saigontech.vn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Le Quoc Hung', NULL, 'manager', 'Project Management', 'Senior PM', '0903123458', 1, '2024-02-01 08:00:00', '2026-01-09 08:45:00', '2024-02-01 00:00:00'),
('USR-004-MGR-PM02', 'pm.linh@saigontech.vn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Pham Thuy Linh', NULL, 'manager', 'Project Management', 'Project Manager', '0903123459', 1, '2024-03-01 08:00:00', '2026-01-08 17:30:00', '2024-03-01 00:00:00'),
('USR-005-MGR-TECH', 'tech.lead@saigontech.vn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Vo Hoang Nam', NULL, 'manager', 'Engineering', 'Technical Lead', '0903123460', 1, '2024-02-15 08:00:00', '2026-01-09 10:00:00', '2024-02-15 00:00:00'),
('USR-006-MGR-SCRUM', 'scrum@saigontech.vn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Dang Minh Tuan', NULL, 'manager', 'Project Management', 'Scrum Master', '0903123461', 1, '2024-04-01 08:00:00', '2026-01-09 09:00:00', '2024-04-01 00:00:00'),
('USR-007-MGR-DESIGN', 'design.lead@saigontech.vn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Nguyen Thi Mai', NULL, 'manager', 'Design', 'Design Lead', '0903123462', 1, '2024-03-15 08:00:00', '2026-01-09 08:30:00', '2024-03-15 00:00:00'),
('USR-008-DEV-BE01', 'backend.tuan@saigontech.vn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Hoang Van Tuan', NULL, 'member', 'Engineering', 'Senior Backend Dev', '0903123463', 1, '2024-05-01 08:00:00', '2026-01-09 08:00:00', '2024-05-01 00:00:00'),
('USR-009-DEV-BE02', 'backend.duc@saigontech.vn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Tran Minh Duc', NULL, 'member', 'Engineering', 'Backend Developer', '0903123464', 1, '2024-06-01 08:00:00', '2026-01-09 09:30:00', '2024-06-01 00:00:00'),
('USR-010-DEV-BE03', 'backend.long@saigontech.vn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Nguyen Thanh Long', NULL, 'member', 'Engineering', 'Backend Developer', '0903123465', 1, '2024-07-01 08:00:00', '2026-01-08 18:00:00', '2024-07-01 00:00:00'),
('USR-011-DEV-BE04', 'backend.khanh@saigontech.vn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Le Trung Khanh', NULL, 'member', 'Engineering', 'Junior Backend Dev', '0903123466', 1, '2025-01-15 08:00:00', '2026-01-09 08:15:00', '2025-01-15 00:00:00'),
('USR-012-DEV-FE01', 'frontend.hoa@saigontech.vn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Phan Thi Hoa', NULL, 'member', 'Engineering', 'Senior Frontend Dev', '0903123467', 1, '2024-05-15 08:00:00', '2026-01-09 08:45:00', '2024-05-15 00:00:00'),
('USR-013-DEV-FE02', 'frontend.phong@saigontech.vn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Vu Dinh Phong', NULL, 'member', 'Engineering', 'Frontend Developer', '0903123468', 1, '2024-08-01 08:00:00', '2026-01-09 09:00:00', '2024-08-01 00:00:00'),
('USR-014-DEV-FE03', 'frontend.thao@saigontech.vn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Do Phuong Thao', NULL, 'member', 'Engineering', 'Junior Frontend Dev', '0903123469', 1, '2025-03-01 08:00:00', '2026-01-08 17:45:00', '2025-03-01 00:00:00'),
('USR-015-DEV-MOB1', 'mobile.an@saigontech.vn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Bui Van An', NULL, 'member', 'Engineering', 'Mobile Dev (iOS)', '0903123470', 1, '2024-09-01 08:00:00', '2026-01-09 08:30:00', '2024-09-01 00:00:00'),
('USR-016-DEV-MOB2', 'mobile.binh@saigontech.vn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Trinh Quoc Binh', NULL, 'member', 'Engineering', 'Mobile Dev (Android)', '0903123471', 1, '2024-09-15 08:00:00', '2026-01-09 09:15:00', '2024-09-15 00:00:00'),
('USR-017-DESIGN01', 'design.lan@saigontech.vn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Tran Ngoc Lan', NULL, 'member', 'Design', 'UI/UX Designer', '0903123472', 1, '2024-06-15 08:00:00', '2026-01-09 08:00:00', '2024-06-15 00:00:00'),
('USR-018-DESIGN02', 'design.khoa@saigontech.vn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Ly Dang Khoa', NULL, 'member', 'Design', 'Graphic Designer', '0903123473', 1, '2024-10-01 08:00:00', '2026-01-08 16:30:00', '2024-10-01 00:00:00'),
('USR-019-QA01', 'qa.hanh@saigontech.vn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Nguyen Thi Hanh', NULL, 'member', 'Quality Assurance', 'Senior QA Engineer', '0903123474', 1, '2024-04-15 08:00:00', '2026-01-09 08:30:00', '2024-04-15 00:00:00'),
('USR-020-QA02', 'qa.son@saigontech.vn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Pham Van Son', NULL, 'member', 'Quality Assurance', 'QA Engineer', '0903123475', 1, '2024-11-01 08:00:00', '2026-01-09 09:00:00', '2024-11-01 00:00:00'),
('USR-021-DEVOPS', 'devops@saigontech.vn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Cao Minh Quan', NULL, 'member', 'Engineering', 'DevOps Engineer', '0903123476', 1, '2024-07-15 08:00:00', '2026-01-09 07:30:00', '2024-07-15 00:00:00'),
('USR-022-BA', 'ba.thuy@saigontech.vn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Le Thi Thuy', NULL, 'member', 'Business Analysis', 'Business Analyst', '0903123477', 1, '2024-08-15 08:00:00', '2026-01-09 08:45:00', '2024-08-15 00:00:00'),
('USR-023-GUEST-VIN', 'client.vingroup@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Nguyen Hoang Anh', NULL, 'guest', 'VinGroup', 'Product Owner', '0903123478', 1, '2025-06-01 08:00:00', '2026-01-07 14:00:00', '2025-06-01 00:00:00'),
('USR-024-GUEST-FPT', 'client.fpt@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Tran Van Bao', NULL, 'guest', 'FPT Software', 'Technical Manager', '0903123479', 1, '2025-07-01 08:00:00', '2026-01-06 10:30:00', '2025-07-01 00:00:00'),
('USR-025-INACTIVE', 'former.employee@saigontech.vn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Tran Van Cuong', NULL, 'member', 'Engineering', 'Backend Developer', '0903123480', 0, '2024-06-01 08:00:00', '2025-09-30 17:00:00', '2024-06-01 00:00:00');


-- =============================================
-- 2. LABELS
-- =============================================
INSERT INTO `labels` (`id`, `name`, `color`, `description`, `created_at`) VALUES
('LBL-001-BUG', 'Bug', '#EF4444', 'Loi can sua', '2024-01-15 00:00:00'),
('LBL-002-FEATURE', 'Feature', '#3B82F6', 'Tinh nang moi', '2024-01-15 00:00:00'),
('LBL-003-ENHANCE', 'Enhancement', '#8B5CF6', 'Cai tien', '2024-01-15 00:00:00'),
('LBL-004-DOCS', 'Documentation', '#6B7280', 'Tai lieu', '2024-01-15 00:00:00'),
('LBL-005-TEST', 'Testing', '#F59E0B', 'Kiem thu', '2024-01-15 00:00:00'),
('LBL-006-DESIGN', 'Design', '#EC4899', 'Thiet ke UI/UX', '2024-01-15 00:00:00'),
('LBL-007-RESEARCH', 'Research', '#14B8A6', 'Nghien cuu', '2024-01-15 00:00:00'),
('LBL-008-URGENT', 'Urgent', '#DC2626', 'Khan cap', '2024-01-15 00:00:00'),
('LBL-009-SECURITY', 'Security', '#991B1B', 'Bao mat', '2024-01-15 00:00:00'),
('LBL-010-PERF', 'Performance', '#059669', 'Hieu nang', '2024-01-15 00:00:00'),
('LBL-011-REFACTOR', 'Refactor', '#7C3AED', 'Tai cau truc code', '2024-01-15 00:00:00'),
('LBL-012-HOTFIX', 'Hotfix', '#B91C1C', 'Sua loi khan cap', '2024-01-15 00:00:00');

-- =============================================
-- 3. PROJECTS
-- =============================================
INSERT INTO `projects` (`id`, `name`, `description`, `color`, `icon`, `status`, `priority`, `progress`, `start_date`, `end_date`, `budget`, `created_by`, `created_at`) VALUES
('PRJ-001-VINMART', 'VinMart E-commerce Platform', 'Xay dung nen tang thuong mai dien tu cho VinMart', '#10B981', 'shopping-cart', 'active', 'high', 72, '2025-06-01', '2026-03-31', 2500000000.00, 'USR-003-MGR-PM01', '2025-05-15 00:00:00'),
('PRJ-002-FPTBANK', 'FPT Mobile Banking App', 'Phat trien ung dung mobile banking cho FPT Bank', '#3B82F6', 'credit-card', 'active', 'urgent', 45, '2025-08-01', '2026-06-30', 3500000000.00, 'USR-004-MGR-PM02', '2025-07-20 00:00:00'),
('PRJ-003-HRMS', 'SaigonTech HRMS Internal', 'He thong quan ly nhan su noi bo', '#8B5CF6', 'users', 'active', 'medium', 88, '2025-03-01', '2026-01-31', 450000000.00, 'USR-003-MGR-PM01', '2025-02-15 00:00:00'),
('PRJ-004-MEDCARE', 'MedCare Healthcare Platform', 'Nen tang y te so', '#EF4444', 'heart', 'active', 'high', 25, '2025-10-01', '2026-09-30', 1800000000.00, 'USR-004-MGR-PM02', '2025-09-15 00:00:00'),
('PRJ-005-SMARTBOT', 'SmartBot AI Assistant', 'Chatbot AI ho tro khach hang', '#F59E0B', 'message-circle', 'planning', 'medium', 5, '2026-02-01', '2026-08-31', 800000000.00, 'USR-002-ADMIN-CTO', '2025-12-01 00:00:00'),
('PRJ-006-LOGISTICS', 'LogiTrack Logistics System', 'He thong quan ly van chuyen', '#14B8A6', 'truck', 'planning', 'low', 0, '2026-04-01', '2026-12-31', 1200000000.00, 'USR-003-MGR-PM01', '2026-01-05 00:00:00'),
('PRJ-007-DEVOPS', 'DevOps Infrastructure', 'Xay dung ha tang DevOps', '#059669', 'server', 'completed', 'high', 100, '2025-01-01', '2025-06-30', 350000000.00, 'USR-002-ADMIN-CTO', '2024-12-15 00:00:00'),
('PRJ-008-WEBSITE', 'Corporate Website Redesign', 'Thiet ke lai website cong ty', '#EC4899', 'globe', 'completed', 'medium', 100, '2025-04-01', '2025-08-31', 180000000.00, 'USR-007-MGR-DESIGN', '2025-03-20 00:00:00'),
('PRJ-009-EDUTECH', 'EduTech Learning Platform', 'Nen tang hoc truc tuyen', '#6B7280', 'book-open', 'on_hold', 'low', 35, '2025-05-01', '2026-02-28', 650000000.00, 'USR-004-MGR-PM02', '2025-04-15 00:00:00'),
('PRJ-010-CRYPTO', 'CryptoWallet Exchange', 'San giao dich tien dien tu - DA HUY', '#9CA3AF', 'dollar-sign', 'cancelled', 'high', 15, '2025-02-01', '2025-12-31', 2000000000.00, 'USR-001-ADMIN-CEO', '2025-01-15 00:00:00');

-- =============================================
-- 4. PROJECT_MEMBERS
-- =============================================
INSERT INTO `project_members` (`project_id`, `user_id`, `role`, `joined_at`) VALUES
('PRJ-001-VINMART', 'USR-003-MGR-PM01', 'owner', '2025-05-15 08:00:00'),
('PRJ-001-VINMART', 'USR-005-MGR-TECH', 'manager', '2025-05-15 08:00:00'),
('PRJ-001-VINMART', 'USR-006-MGR-SCRUM', 'manager', '2025-05-20 08:00:00'),
('PRJ-001-VINMART', 'USR-008-DEV-BE01', 'member', '2025-06-01 08:00:00'),
('PRJ-001-VINMART', 'USR-009-DEV-BE02', 'member', '2025-06-01 08:00:00'),
('PRJ-001-VINMART', 'USR-010-DEV-BE03', 'member', '2025-06-15 08:00:00'),
('PRJ-001-VINMART', 'USR-012-DEV-FE01', 'member', '2025-06-01 08:00:00'),
('PRJ-001-VINMART', 'USR-013-DEV-FE02', 'member', '2025-06-01 08:00:00'),
('PRJ-001-VINMART', 'USR-017-DESIGN01', 'member', '2025-05-20 08:00:00'),
('PRJ-001-VINMART', 'USR-019-QA01', 'member', '2025-07-01 08:00:00'),
('PRJ-001-VINMART', 'USR-022-BA', 'member', '2025-05-15 08:00:00'),
('PRJ-001-VINMART', 'USR-023-GUEST-VIN', 'viewer', '2025-06-01 08:00:00'),
('PRJ-002-FPTBANK', 'USR-004-MGR-PM02', 'owner', '2025-07-20 08:00:00'),
('PRJ-002-FPTBANK', 'USR-005-MGR-TECH', 'manager', '2025-07-20 08:00:00'),
('PRJ-002-FPTBANK', 'USR-008-DEV-BE01', 'member', '2025-08-01 08:00:00'),
('PRJ-002-FPTBANK', 'USR-011-DEV-BE04', 'member', '2025-08-01 08:00:00'),
('PRJ-002-FPTBANK', 'USR-015-DEV-MOB1', 'member', '2025-08-01 08:00:00'),
('PRJ-002-FPTBANK', 'USR-016-DEV-MOB2', 'member', '2025-08-01 08:00:00'),
('PRJ-002-FPTBANK', 'USR-017-DESIGN01', 'member', '2025-07-25 08:00:00'),
('PRJ-002-FPTBANK', 'USR-019-QA01', 'member', '2025-09-01 08:00:00'),
('PRJ-002-FPTBANK', 'USR-020-QA02', 'member', '2025-09-01 08:00:00'),
('PRJ-002-FPTBANK', 'USR-024-GUEST-FPT', 'viewer', '2025-08-01 08:00:00'),
('PRJ-003-HRMS', 'USR-003-MGR-PM01', 'owner', '2025-02-15 08:00:00'),
('PRJ-003-HRMS', 'USR-009-DEV-BE02', 'member', '2025-03-01 08:00:00'),
('PRJ-003-HRMS', 'USR-012-DEV-FE01', 'member', '2025-03-01 08:00:00'),
('PRJ-003-HRMS', 'USR-014-DEV-FE03', 'member', '2025-03-15 08:00:00'),
('PRJ-003-HRMS', 'USR-018-DESIGN02', 'member', '2025-03-01 08:00:00'),
('PRJ-003-HRMS', 'USR-020-QA02', 'member', '2025-04-01 08:00:00'),
('PRJ-004-MEDCARE', 'USR-004-MGR-PM02', 'owner', '2025-09-15 08:00:00'),
('PRJ-004-MEDCARE', 'USR-006-MGR-SCRUM', 'manager', '2025-09-20 08:00:00'),
('PRJ-004-MEDCARE', 'USR-010-DEV-BE03', 'member', '2025-10-01 08:00:00'),
('PRJ-004-MEDCARE', 'USR-011-DEV-BE04', 'member', '2025-10-01 08:00:00'),
('PRJ-004-MEDCARE', 'USR-013-DEV-FE02', 'member', '2025-10-01 08:00:00'),
('PRJ-004-MEDCARE', 'USR-014-DEV-FE03', 'member', '2025-10-15 08:00:00'),
('PRJ-004-MEDCARE', 'USR-017-DESIGN01', 'member', '2025-09-20 08:00:00'),
('PRJ-004-MEDCARE', 'USR-022-BA', 'member', '2025-09-15 08:00:00'),
('PRJ-005-SMARTBOT', 'USR-002-ADMIN-CTO', 'owner', '2025-12-01 08:00:00'),
('PRJ-005-SMARTBOT', 'USR-005-MGR-TECH', 'manager', '2025-12-01 08:00:00'),
('PRJ-005-SMARTBOT', 'USR-008-DEV-BE01', 'member', '2025-12-15 08:00:00'),
('PRJ-005-SMARTBOT', 'USR-022-BA', 'member', '2025-12-01 08:00:00'),
('PRJ-006-LOGISTICS', 'USR-003-MGR-PM01', 'owner', '2026-01-05 08:00:00'),
('PRJ-007-DEVOPS', 'USR-002-ADMIN-CTO', 'owner', '2024-12-15 08:00:00'),
('PRJ-007-DEVOPS', 'USR-021-DEVOPS', 'member', '2025-01-01 08:00:00'),
('PRJ-007-DEVOPS', 'USR-008-DEV-BE01', 'member', '2025-01-15 08:00:00'),
('PRJ-008-WEBSITE', 'USR-007-MGR-DESIGN', 'owner', '2025-03-20 08:00:00'),
('PRJ-008-WEBSITE', 'USR-017-DESIGN01', 'member', '2025-04-01 08:00:00'),
('PRJ-008-WEBSITE', 'USR-018-DESIGN02', 'member', '2025-04-01 08:00:00'),
('PRJ-008-WEBSITE', 'USR-012-DEV-FE01', 'member', '2025-04-15 08:00:00'),
('PRJ-009-EDUTECH', 'USR-004-MGR-PM02', 'owner', '2025-04-15 08:00:00'),
('PRJ-009-EDUTECH', 'USR-009-DEV-BE02', 'member', '2025-05-01 08:00:00'),
('PRJ-009-EDUTECH', 'USR-013-DEV-FE02', 'member', '2025-05-01 08:00:00'),
('PRJ-010-CRYPTO', 'USR-001-ADMIN-CEO', 'owner', '2025-01-15 08:00:00'),
('PRJ-010-CRYPTO', 'USR-005-MGR-TECH', 'manager', '2025-01-15 08:00:00');


-- =============================================
-- 5. TASKS - Cong viec trong thang 1/2026
-- =============================================
INSERT INTO `tasks` (`id`, `project_id`, `title`, `description`, `status`, `priority`, `position`, `start_date`, `due_date`, `completed_at`, `estimated_hours`, `actual_hours`, `created_by`, `created_at`) VALUES
-- VinMart - Tasks trong thang 1/2026
('TSK-VM-001', 'PRJ-001-VINMART', 'VNPay Integration - Phase 2', 'Tich hop thanh toan VNPay giai doan 2', 'in_progress', 'urgent', 1, '2026-01-05', '2026-01-15', NULL, 40.00, 25.00, 'USR-008-DEV-BE01', '2026-01-02 08:00:00'),
('TSK-VM-002', 'PRJ-001-VINMART', 'MoMo Payment Integration', 'Tich hop vi MoMo', 'in_progress', 'high', 2, '2026-01-08', '2026-01-20', NULL, 32.00, 15.00, 'USR-009-DEV-BE02', '2026-01-05 08:00:00'),
('TSK-VM-003', 'PRJ-001-VINMART', 'Checkout UI Optimization', 'Toi uu giao dien checkout', 'todo', 'medium', 3, '2026-01-12', '2026-01-25', NULL, 24.00, NULL, 'USR-012-DEV-FE01', '2026-01-08 08:00:00'),
('TSK-VM-004', 'PRJ-001-VINMART', 'Payment Testing', 'Test toan bo flow thanh toan', 'todo', 'high', 4, '2026-01-20', '2026-01-30', NULL, 24.00, NULL, 'USR-019-QA01', '2026-01-10 08:00:00'),
('TSK-VM-005', 'PRJ-001-VINMART', 'Fix Cart Bug on Mobile', 'Sua loi gio hang tren mobile', 'in_progress', 'urgent', 5, '2026-01-06', '2026-01-10', NULL, 8.00, 6.00, 'USR-013-DEV-FE02', '2026-01-05 08:00:00'),
('TSK-VM-006', 'PRJ-001-VINMART', 'Product Search Elasticsearch', 'Tim kiem san pham voi Elasticsearch', 'backlog', 'medium', 6, NULL, NULL, NULL, 40.00, NULL, 'USR-008-DEV-BE01', '2026-01-08 08:00:00'),

-- FPT Bank - Tasks trong thang 1/2026
('TSK-FB-001', 'PRJ-002-FPTBANK', 'Internal Transfer Feature', 'Chuyen tien noi bo', 'in_progress', 'urgent', 1, '2026-01-02', '2026-01-18', NULL, 48.00, 30.00, 'USR-008-DEV-BE01', '2025-12-28 08:00:00'),
('TSK-FB-002', 'PRJ-002-FPTBANK', 'Transfer UI Implementation', 'Giao dien chuyen tien', 'in_progress', 'high', 2, '2026-01-05', '2026-01-20', NULL, 32.00, 18.00, 'USR-015-DEV-MOB1', '2026-01-02 08:00:00'),
('TSK-FB-003', 'PRJ-002-FPTBANK', 'OTP Enhancement', 'Cai tien he thong OTP', 'in_review', 'high', 3, '2026-01-03', '2026-01-12', NULL, 16.00, 14.00, 'USR-011-DEV-BE04', '2026-01-01 08:00:00'),
('TSK-FB-004', 'PRJ-002-FPTBANK', 'Bill Payment Integration', 'Tich hop thanh toan hoa don', 'todo', 'high', 4, '2026-01-15', '2026-01-30', NULL, 40.00, NULL, 'USR-008-DEV-BE01', '2026-01-10 08:00:00'),
('TSK-FB-005', 'PRJ-002-FPTBANK', 'Security Audit Preparation', 'Chuan bi kiem tra bao mat', 'todo', 'urgent', 5, '2026-01-20', '2026-02-05', NULL, 32.00, NULL, 'USR-019-QA01', '2026-01-12 08:00:00'),

-- HRMS - Tasks trong thang 1/2026
('TSK-HR-001', 'PRJ-003-HRMS', 'Payroll Calculation Engine', 'Engine tinh luong', 'in_progress', 'urgent', 1, '2026-01-02', '2026-01-15', NULL, 48.00, 35.00, 'USR-009-DEV-BE02', '2025-12-20 08:00:00'),
('TSK-HR-002', 'PRJ-003-HRMS', 'Payslip Generation', 'Tao phieu luong PDF', 'in_progress', 'high', 2, '2026-01-08', '2026-01-20', NULL, 24.00, 12.00, 'USR-014-DEV-FE03', '2026-01-05 08:00:00'),
('TSK-HR-003', 'PRJ-003-HRMS', 'Tax Report Module', 'Bao cao thue TNCN', 'todo', 'high', 3, '2026-01-15', '2026-01-28', NULL, 24.00, NULL, 'USR-009-DEV-BE02', '2026-01-10 08:00:00'),
('TSK-HR-004', 'PRJ-003-HRMS', 'UAT Testing', 'Kiem thu UAT truoc go-live', 'todo', 'urgent', 4, '2026-01-22', '2026-01-30', NULL, 32.00, NULL, 'USR-020-QA02', '2026-01-15 08:00:00'),

-- MedCare - Tasks trong thang 1/2026
('TSK-MC-001', 'PRJ-004-MEDCARE', 'Patient Registration Module', 'Module dang ky benh nhan', 'in_progress', 'high', 1, '2026-01-05', '2026-01-25', NULL, 40.00, 20.00, 'USR-010-DEV-BE03', '2026-01-02 08:00:00'),
('TSK-MC-002', 'PRJ-004-MEDCARE', 'Appointment Booking System', 'He thong dat lich kham', 'in_progress', 'high', 2, '2026-01-08', '2026-01-28', NULL, 40.00, 15.00, 'USR-011-DEV-BE04', '2026-01-05 08:00:00'),
('TSK-MC-003', 'PRJ-004-MEDCARE', 'Doctor Portal UI', 'Giao dien bac si', 'todo', 'medium', 3, '2026-01-15', '2026-02-05', NULL, 48.00, NULL, 'USR-013-DEV-FE02', '2026-01-10 08:00:00'),

-- Tasks qua han (OVERDUE)
('TSK-VM-OVD1', 'PRJ-001-VINMART', '[OVERDUE] Fix Payment Timeout', 'Sua loi timeout thanh toan', 'in_progress', 'urgent', 100, '2025-12-20', '2026-01-05', NULL, 16.00, 12.00, 'USR-008-DEV-BE01', '2025-12-15 08:00:00'),
('TSK-FB-OVD1', 'PRJ-002-FPTBANK', '[OVERDUE] OTP Delivery Issue', 'Khac phuc OTP gui cham', 'in_progress', 'urgent', 100, '2025-12-25', '2026-01-03', NULL, 16.00, 14.00, 'USR-008-DEV-BE01', '2025-12-20 08:00:00');

-- =============================================
-- 6. TASK_ASSIGNEES
-- =============================================
INSERT INTO `task_assignees` (`task_id`, `user_id`, `assigned_by`, `assigned_at`) VALUES
('TSK-VM-001', 'USR-008-DEV-BE01', 'USR-005-MGR-TECH', '2026-01-02 09:00:00'),
('TSK-VM-002', 'USR-009-DEV-BE02', 'USR-005-MGR-TECH', '2026-01-05 09:00:00'),
('TSK-VM-003', 'USR-012-DEV-FE01', 'USR-005-MGR-TECH', '2026-01-08 09:00:00'),
('TSK-VM-004', 'USR-019-QA01', 'USR-003-MGR-PM01', '2026-01-10 09:00:00'),
('TSK-VM-005', 'USR-013-DEV-FE02', 'USR-005-MGR-TECH', '2026-01-05 09:00:00'),
('TSK-FB-001', 'USR-008-DEV-BE01', 'USR-005-MGR-TECH', '2025-12-28 09:00:00'),
('TSK-FB-002', 'USR-015-DEV-MOB1', 'USR-005-MGR-TECH', '2026-01-02 09:00:00'),
('TSK-FB-003', 'USR-011-DEV-BE04', 'USR-005-MGR-TECH', '2026-01-01 09:00:00'),
('TSK-FB-004', 'USR-008-DEV-BE01', 'USR-005-MGR-TECH', '2026-01-10 09:00:00'),
('TSK-FB-005', 'USR-019-QA01', 'USR-004-MGR-PM02', '2026-01-12 09:00:00'),
('TSK-HR-001', 'USR-009-DEV-BE02', 'USR-003-MGR-PM01', '2025-12-20 09:00:00'),
('TSK-HR-002', 'USR-014-DEV-FE03', 'USR-003-MGR-PM01', '2026-01-05 09:00:00'),
('TSK-HR-003', 'USR-009-DEV-BE02', 'USR-003-MGR-PM01', '2026-01-10 09:00:00'),
('TSK-HR-004', 'USR-020-QA02', 'USR-003-MGR-PM01', '2026-01-15 09:00:00'),
('TSK-MC-001', 'USR-010-DEV-BE03', 'USR-006-MGR-SCRUM', '2026-01-02 09:00:00'),
('TSK-MC-002', 'USR-011-DEV-BE04', 'USR-006-MGR-SCRUM', '2026-01-05 09:00:00'),
('TSK-MC-003', 'USR-013-DEV-FE02', 'USR-006-MGR-SCRUM', '2026-01-10 09:00:00'),
('TSK-VM-OVD1', 'USR-008-DEV-BE01', 'USR-005-MGR-TECH', '2025-12-15 09:00:00'),
('TSK-FB-OVD1', 'USR-008-DEV-BE01', 'USR-005-MGR-TECH', '2025-12-20 09:00:00');


-- =============================================
-- 7. CALENDAR_EVENTS - Su kien thang 1/2026
-- =============================================
INSERT INTO `calendar_events` (`id`, `title`, `description`, `type`, `color`, `start_time`, `end_time`, `is_all_day`, `location`, `project_id`, `task_id`, `created_by`, `created_at`) VALUES
('EVT-001', 'Sprint Planning - VinMart Sprint 4', 'Len ke hoach Sprint 4', 'meeting', '#10B981', '2026-01-06 09:00:00', '2026-01-06 11:00:00', 0, 'Phong hop A - Tang 5', 'PRJ-001-VINMART', NULL, 'USR-006-MGR-SCRUM', '2026-01-03 08:00:00'),
('EVT-002', 'Sprint Review - VinMart Sprint 3', 'Demo cac tinh nang Sprint 3', 'meeting', '#10B981', '2026-01-10 14:00:00', '2026-01-10 16:00:00', 0, 'Phong hop lon - Tang 3', 'PRJ-001-VINMART', NULL, 'USR-006-MGR-SCRUM', '2026-01-03 08:00:00'),
('EVT-003', 'Daily Standup - FPT Bank', 'Daily standup meeting', 'meeting', '#3B82F6', '2026-01-09 09:00:00', '2026-01-09 09:15:00', 0, 'Online - Google Meet', 'PRJ-002-FPTBANK', NULL, 'USR-004-MGR-PM02', '2026-01-07 08:00:00'),
('EVT-004', 'Daily Standup - FPT Bank', 'Daily standup meeting', 'meeting', '#3B82F6', '2026-01-10 09:00:00', '2026-01-10 09:15:00', 0, 'Online - Google Meet', 'PRJ-002-FPTBANK', NULL, 'USR-004-MGR-PM02', '2026-01-07 08:00:00'),
('EVT-005', 'Demo Meeting - VinGroup', 'Demo tien do du an cho VinGroup', 'meeting', '#F59E0B', '2026-01-15 10:00:00', '2026-01-15 12:00:00', 0, 'VinGroup Office - Quan 1', 'PRJ-001-VINMART', NULL, 'USR-003-MGR-PM01', '2026-01-05 08:00:00'),
('EVT-006', 'UAT Kickoff - FPT Bank', 'Khoi dong UAT voi FPT', 'meeting', '#3B82F6', '2026-01-20 09:00:00', '2026-01-20 11:00:00', 0, 'FPT Tower - Quan 7', 'PRJ-002-FPTBANK', NULL, 'USR-004-MGR-PM02', '2026-01-08 08:00:00'),
('EVT-007', 'Deadline: VNPay Integration', 'Hoan thanh tich hop VNPay', 'deadline', '#EF4444', '2026-01-15 17:00:00', '2026-01-15 17:00:00', 0, NULL, 'PRJ-001-VINMART', 'TSK-VM-001', 'USR-003-MGR-PM01', '2026-01-02 08:00:00'),
('EVT-008', 'Deadline: HRMS Go-live', 'Go-live he thong HRMS', 'deadline', '#8B5CF6', '2026-01-31 17:00:00', '2026-01-31 17:00:00', 0, NULL, 'PRJ-003-HRMS', NULL, 'USR-003-MGR-PM01', '2025-02-15 08:00:00'),
('EVT-009', 'Weekly Report Submission', 'Nop bao cao tuan', 'reminder', '#6B7280', '2026-01-10 16:00:00', '2026-01-10 16:30:00', 0, NULL, NULL, NULL, 'USR-003-MGR-PM01', '2026-01-06 08:00:00'),
('EVT-010', 'Code Review Session', 'Review code Payment module', 'reminder', '#7C3AED', '2026-01-09 14:00:00', '2026-01-09 15:00:00', 0, 'Online - Zoom', 'PRJ-001-VINMART', NULL, 'USR-005-MGR-TECH', '2026-01-07 08:00:00'),
('EVT-011', 'Tet Nguyen Dan 2026', 'Nghi Tet Nguyen Dan', 'event', '#DC2626', '2026-01-28 00:00:00', '2026-02-02 23:59:59', 1, NULL, NULL, NULL, 'USR-001-ADMIN-CEO', '2026-01-01 08:00:00'),
('EVT-012', 'Team Building Q1/2026', 'Team building quy 1', 'event', '#EC4899', '2026-02-15 07:00:00', '2026-02-16 18:00:00', 0, 'Resort Vung Tau', NULL, NULL, 'USR-001-ADMIN-CEO', '2026-01-05 08:00:00'),
('EVT-013', 'AWS Training', 'Dao tao AWS cho team', 'event', '#059669', '2026-01-13 09:00:00', '2026-01-13 17:00:00', 0, 'Phong dao tao - Tang 2', NULL, NULL, 'USR-002-ADMIN-CTO', '2026-01-06 08:00:00'),
('EVT-014', 'React Performance Workshop', 'Workshop toi uu React', 'event', '#3B82F6', '2026-01-17 14:00:00', '2026-01-17 17:00:00', 0, 'Phong hop B - Tang 5', NULL, NULL, 'USR-005-MGR-TECH', '2026-01-08 08:00:00'),
('EVT-015', 'Sprint Retrospective', 'Retrospective Sprint 3', 'meeting', '#10B981', '2026-01-10 16:30:00', '2026-01-10 17:30:00', 0, 'Phong hop A - Tang 5', 'PRJ-001-VINMART', NULL, 'USR-006-MGR-SCRUM', '2026-01-03 08:00:00');

-- =============================================
-- 8. EVENT_ATTENDEES
-- =============================================
INSERT INTO `event_attendees` (`event_id`, `user_id`, `status`, `responded_at`) VALUES
('EVT-001', 'USR-003-MGR-PM01', 'accepted', '2026-01-03 09:00:00'),
('EVT-001', 'USR-005-MGR-TECH', 'accepted', '2026-01-03 09:30:00'),
('EVT-001', 'USR-006-MGR-SCRUM', 'accepted', '2026-01-03 08:00:00'),
('EVT-001', 'USR-008-DEV-BE01', 'accepted', '2026-01-03 10:00:00'),
('EVT-001', 'USR-009-DEV-BE02', 'accepted', '2026-01-03 10:15:00'),
('EVT-001', 'USR-012-DEV-FE01', 'accepted', '2026-01-03 11:00:00'),
('EVT-002', 'USR-003-MGR-PM01', 'accepted', '2026-01-03 09:00:00'),
('EVT-002', 'USR-005-MGR-TECH', 'accepted', '2026-01-03 09:30:00'),
('EVT-002', 'USR-023-GUEST-VIN', 'pending', NULL),
('EVT-003', 'USR-004-MGR-PM02', 'accepted', '2026-01-07 08:00:00'),
('EVT-003', 'USR-008-DEV-BE01', 'accepted', '2026-01-07 08:30:00'),
('EVT-003', 'USR-015-DEV-MOB1', 'accepted', '2026-01-07 08:45:00'),
('EVT-005', 'USR-001-ADMIN-CEO', 'accepted', '2026-01-05 09:00:00'),
('EVT-005', 'USR-003-MGR-PM01', 'accepted', '2026-01-05 08:00:00'),
('EVT-005', 'USR-023-GUEST-VIN', 'accepted', '2026-01-05 10:00:00'),
('EVT-013', 'USR-021-DEVOPS', 'accepted', '2026-01-06 09:00:00'),
('EVT-013', 'USR-008-DEV-BE01', 'accepted', '2026-01-06 10:00:00');

-- =============================================
-- 9. USER_SETTINGS
-- =============================================
INSERT INTO `user_settings` (`user_id`, `theme`, `language`, `timezone`, `notification_email`, `notification_push`, `notification_task_assigned`, `notification_task_due`, `notification_comment`, `notification_mention`, `updated_at`) VALUES
('USR-001-ADMIN-CEO', 'light', 'vi', 'Asia/Ho_Chi_Minh', 1, 1, 1, 1, 0, 1, '2024-01-15 08:00:00'),
('USR-002-ADMIN-CTO', 'dark', 'vi', 'Asia/Ho_Chi_Minh', 1, 1, 1, 1, 1, 1, '2024-02-01 08:00:00'),
('USR-003-MGR-PM01', 'system', 'vi', 'Asia/Ho_Chi_Minh', 1, 1, 1, 1, 1, 1, '2024-03-01 08:00:00'),
('USR-004-MGR-PM02', 'light', 'vi', 'Asia/Ho_Chi_Minh', 1, 1, 1, 1, 1, 1, '2024-03-15 08:00:00'),
('USR-005-MGR-TECH', 'dark', 'en', 'Asia/Ho_Chi_Minh', 1, 1, 1, 1, 1, 1, '2024-02-20 08:00:00'),
('USR-006-MGR-SCRUM', 'system', 'vi', 'Asia/Ho_Chi_Minh', 1, 1, 1, 1, 1, 1, '2024-04-10 08:00:00'),
('USR-007-MGR-DESIGN', 'light', 'vi', 'Asia/Ho_Chi_Minh', 1, 0, 1, 1, 1, 1, '2024-03-25 08:00:00'),
('USR-008-DEV-BE01', 'dark', 'en', 'Asia/Ho_Chi_Minh', 1, 1, 1, 1, 1, 1, '2024-05-15 08:00:00'),
('USR-009-DEV-BE02', 'dark', 'vi', 'Asia/Ho_Chi_Minh', 1, 1, 1, 1, 0, 1, '2024-06-10 08:00:00'),
('USR-010-DEV-BE03', 'system', 'vi', 'Asia/Ho_Chi_Minh', 1, 1, 1, 1, 1, 1, '2024-07-15 08:00:00'),
('USR-011-DEV-BE04', 'light', 'vi', 'Asia/Ho_Chi_Minh', 1, 1, 1, 1, 1, 1, '2025-01-20 08:00:00'),
('USR-012-DEV-FE01', 'dark', 'en', 'Asia/Ho_Chi_Minh', 1, 1, 1, 1, 1, 1, '2024-05-20 08:00:00'),
('USR-013-DEV-FE02', 'system', 'vi', 'Asia/Ho_Chi_Minh', 1, 1, 1, 1, 1, 1, '2024-08-10 08:00:00'),
('USR-014-DEV-FE03', 'light', 'vi', 'Asia/Ho_Chi_Minh', 1, 1, 1, 1, 1, 1, '2025-03-10 08:00:00'),
('USR-015-DEV-MOB1', 'dark', 'vi', 'Asia/Ho_Chi_Minh', 1, 1, 1, 1, 1, 1, '2024-09-10 08:00:00'),
('USR-016-DEV-MOB2', 'dark', 'vi', 'Asia/Ho_Chi_Minh', 1, 1, 1, 1, 1, 1, '2024-09-20 08:00:00'),
('USR-017-DESIGN01', 'light', 'vi', 'Asia/Ho_Chi_Minh', 1, 0, 1, 1, 1, 1, '2024-06-20 08:00:00'),
('USR-018-DESIGN02', 'light', 'vi', 'Asia/Ho_Chi_Minh', 1, 1, 1, 1, 1, 1, '2024-10-10 08:00:00'),
('USR-019-QA01', 'system', 'vi', 'Asia/Ho_Chi_Minh', 1, 1, 1, 1, 1, 1, '2024-04-20 08:00:00'),
('USR-020-QA02', 'light', 'vi', 'Asia/Ho_Chi_Minh', 1, 1, 1, 1, 1, 1, '2024-11-10 08:00:00'),
('USR-021-DEVOPS', 'dark', 'en', 'Asia/Ho_Chi_Minh', 1, 1, 1, 1, 0, 1, '2024-07-20 08:00:00'),
('USR-022-BA', 'light', 'vi', 'Asia/Ho_Chi_Minh', 1, 1, 1, 1, 1, 1, '2024-08-20 08:00:00'),
('USR-023-GUEST-VIN', 'light', 'vi', 'Asia/Ho_Chi_Minh', 1, 0, 0, 0, 1, 1, '2025-06-05 08:00:00'),
('USR-024-GUEST-FPT', 'light', 'vi', 'Asia/Ho_Chi_Minh', 1, 0, 0, 0, 1, 1, '2025-07-05 08:00:00');

SET FOREIGN_KEY_CHECKS = 1;

-- =============================================
-- HOAN TAT - Du lieu cho thang 1/2026
-- =============================================
/*
THONG KE:
- 25 Users
- 12 Labels  
- 10 Projects
- 52 Project Members
- 21 Tasks (trong thang 1/2026)
- 19 Task Assignees
- 15 Calendar Events (trong thang 1/2026)
- 17 Event Attendees
- 24 User Settings

TAI KHOAN DEMO:
- ceo@saigontech.vn / password (Admin)
- pm.hung@saigontech.vn / password (Manager)
- backend.tuan@saigontech.vn / password (Member)
*/


-- =============================================
-- 10. TASK_LABELS
-- =============================================
INSERT INTO `task_labels` (`task_id`, `label_id`) VALUES
('TSK-VM-001', 'LBL-002-FEATURE'),
('TSK-VM-001', 'LBL-008-URGENT'),
('TSK-VM-002', 'LBL-002-FEATURE'),
('TSK-VM-003', 'LBL-003-ENHANCE'),
('TSK-VM-003', 'LBL-006-DESIGN'),
('TSK-VM-004', 'LBL-005-TEST'),
('TSK-VM-005', 'LBL-001-BUG'),
('TSK-VM-005', 'LBL-008-URGENT'),
('TSK-VM-006', 'LBL-007-RESEARCH'),
('TSK-VM-006', 'LBL-010-PERF'),
('TSK-FB-001', 'LBL-002-FEATURE'),
('TSK-FB-001', 'LBL-009-SECURITY'),
('TSK-FB-002', 'LBL-006-DESIGN'),
('TSK-FB-003', 'LBL-009-SECURITY'),
('TSK-FB-003', 'LBL-003-ENHANCE'),
('TSK-FB-004', 'LBL-002-FEATURE'),
('TSK-FB-005', 'LBL-009-SECURITY'),
('TSK-FB-005', 'LBL-005-TEST'),
('TSK-HR-001', 'LBL-002-FEATURE'),
('TSK-HR-001', 'LBL-008-URGENT'),
('TSK-HR-002', 'LBL-002-FEATURE'),
('TSK-HR-003', 'LBL-002-FEATURE'),
('TSK-HR-004', 'LBL-005-TEST'),
('TSK-MC-001', 'LBL-002-FEATURE'),
('TSK-MC-002', 'LBL-002-FEATURE'),
('TSK-MC-003', 'LBL-006-DESIGN'),
('TSK-VM-OVD1', 'LBL-001-BUG'),
('TSK-VM-OVD1', 'LBL-012-HOTFIX'),
('TSK-FB-OVD1', 'LBL-001-BUG'),
('TSK-FB-OVD1', 'LBL-012-HOTFIX');

-- =============================================
-- 11. TASK_CHECKLISTS
-- =============================================
INSERT INTO `task_checklists` (`id`, `task_id`, `title`, `is_completed`, `completed_at`, `position`, `created_at`) VALUES
-- VNPay Integration checklist
('CHK-001', 'TSK-VM-001', 'Tao API endpoint thanh toan', 1, '2026-01-07 15:00:00', 1, '2026-01-02 09:00:00'),
('CHK-002', 'TSK-VM-001', 'Tich hop VNPay SDK', 1, '2026-01-08 11:00:00', 2, '2026-01-02 09:00:00'),
('CHK-003', 'TSK-VM-001', 'Xu ly callback thanh toan', 1, '2026-01-09 14:00:00', 3, '2026-01-02 09:00:00'),
('CHK-004', 'TSK-VM-001', 'Test sandbox environment', 0, NULL, 4, '2026-01-02 09:00:00'),
('CHK-005', 'TSK-VM-001', 'Deploy production', 0, NULL, 5, '2026-01-02 09:00:00'),
-- MoMo Integration checklist
('CHK-006', 'TSK-VM-002', 'Dang ky MoMo merchant', 1, '2026-01-06 10:00:00', 1, '2026-01-05 09:00:00'),
('CHK-007', 'TSK-VM-002', 'Tich hop MoMo API', 0, NULL, 2, '2026-01-05 09:00:00'),
('CHK-008', 'TSK-VM-002', 'Xu ly IPN callback', 0, NULL, 3, '2026-01-05 09:00:00'),
('CHK-009', 'TSK-VM-002', 'Test va verify', 0, NULL, 4, '2026-01-05 09:00:00'),
-- Internal Transfer checklist
('CHK-010', 'TSK-FB-001', 'Thiet ke database schema', 1, '2026-01-03 16:00:00', 1, '2025-12-28 09:00:00'),
('CHK-011', 'TSK-FB-001', 'API chuyen tien noi bo', 1, '2026-01-06 17:00:00', 2, '2025-12-28 09:00:00'),
('CHK-012', 'TSK-FB-001', 'Validation so du', 1, '2026-01-07 11:00:00', 3, '2025-12-28 09:00:00'),
('CHK-013', 'TSK-FB-001', 'Transaction logging', 0, NULL, 4, '2025-12-28 09:00:00'),
('CHK-014', 'TSK-FB-001', 'Security review', 0, NULL, 5, '2025-12-28 09:00:00'),
-- OTP Enhancement checklist
('CHK-015', 'TSK-FB-003', 'Cai thien thuat toan OTP', 1, '2026-01-05 14:00:00', 1, '2026-01-01 09:00:00'),
('CHK-016', 'TSK-FB-003', 'Tang thoi gian het han', 1, '2026-01-06 10:00:00', 2, '2026-01-01 09:00:00'),
('CHK-017', 'TSK-FB-003', 'Them retry mechanism', 1, '2026-01-07 15:00:00', 3, '2026-01-01 09:00:00'),
('CHK-018', 'TSK-FB-003', 'Unit test', 1, '2026-01-08 11:00:00', 4, '2026-01-01 09:00:00'),
-- Payroll Engine checklist
('CHK-019', 'TSK-HR-001', 'Cong thuc tinh luong co ban', 1, '2026-01-05 16:00:00', 1, '2025-12-20 09:00:00'),
('CHK-020', 'TSK-HR-001', 'Tinh phu cap va tru', 1, '2026-01-07 14:00:00', 2, '2025-12-20 09:00:00'),
('CHK-021', 'TSK-HR-001', 'Tinh thue TNCN', 0, NULL, 3, '2025-12-20 09:00:00'),
('CHK-022', 'TSK-HR-001', 'Tinh BHXH, BHYT', 0, NULL, 4, '2025-12-20 09:00:00'),
('CHK-023', 'TSK-HR-001', 'Export Excel', 0, NULL, 5, '2025-12-20 09:00:00');

-- =============================================
-- 12. DOCUMENTS
-- =============================================
INSERT INTO `documents` (`id`, `name`, `description`, `type`, `mime_type`, `file_size`, `file_path`, `project_id`, `is_starred`, `download_count`, `uploaded_by`, `created_at`) VALUES
-- VinMart Documents
('DOC-001', 'VinMart_SRS_v2.1.pdf', 'Tai lieu dac ta yeu cau phan mem', 'file', 'application/pdf', 2458624, 'uploads/projects/PRJ-001-VINMART/VinMart_SRS_v2.1.pdf', 'PRJ-001-VINMART', 1, 15, 'USR-022-BA', '2025-06-10 08:00:00'),
('DOC-002', 'API_Documentation.pdf', 'Tai lieu API cho VinMart', 'file', 'application/pdf', 1536000, 'uploads/projects/PRJ-001-VINMART/API_Documentation.pdf', 'PRJ-001-VINMART', 1, 28, 'USR-008-DEV-BE01', '2025-08-15 08:00:00'),
('DOC-003', 'UI_Mockup_Checkout.fig', 'Figma mockup trang checkout', 'file', 'application/octet-stream', 8945664, 'uploads/projects/PRJ-001-VINMART/UI_Mockup_Checkout.fig', 'PRJ-001-VINMART', 0, 12, 'USR-017-DESIGN01', '2025-12-20 08:00:00'),
('DOC-004', 'Payment_Flow_Diagram.png', 'So do luong thanh toan', 'file', 'image/png', 524288, 'uploads/projects/PRJ-001-VINMART/Payment_Flow_Diagram.png', 'PRJ-001-VINMART', 0, 8, 'USR-005-MGR-TECH', '2026-01-03 08:00:00'),
-- FPT Bank Documents
('DOC-005', 'FPTBank_Security_Spec.pdf', 'Dac ta bao mat ung dung', 'file', 'application/pdf', 3145728, 'uploads/projects/PRJ-002-FPTBANK/FPTBank_Security_Spec.pdf', 'PRJ-002-FPTBANK', 1, 22, 'USR-005-MGR-TECH', '2025-08-20 08:00:00'),
('DOC-006', 'Mobile_App_Wireframe.pdf', 'Wireframe ung dung mobile', 'file', 'application/pdf', 4587520, 'uploads/projects/PRJ-002-FPTBANK/Mobile_App_Wireframe.pdf', 'PRJ-002-FPTBANK', 1, 35, 'USR-017-DESIGN01', '2025-09-10 08:00:00'),
('DOC-007', 'Transfer_API_Spec.xlsx', 'Dac ta API chuyen tien', 'file', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 156672, 'uploads/projects/PRJ-002-FPTBANK/Transfer_API_Spec.xlsx', 'PRJ-002-FPTBANK', 0, 10, 'USR-008-DEV-BE01', '2026-01-05 08:00:00'),
-- HRMS Documents
('DOC-008', 'HRMS_User_Manual.pdf', 'Huong dan su dung HRMS', 'file', 'application/pdf', 2097152, 'uploads/projects/PRJ-003-HRMS/HRMS_User_Manual.pdf', 'PRJ-003-HRMS', 1, 45, 'USR-022-BA', '2025-12-15 08:00:00'),
('DOC-009', 'Payroll_Formula.xlsx', 'Cong thuc tinh luong', 'file', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 89088, 'uploads/projects/PRJ-003-HRMS/Payroll_Formula.xlsx', 'PRJ-003-HRMS', 1, 18, 'USR-003-MGR-PM01', '2026-01-02 08:00:00'),
-- MedCare Documents
('DOC-010', 'MedCare_PRD.pdf', 'Product Requirements Document', 'file', 'application/pdf', 1835008, 'uploads/projects/PRJ-004-MEDCARE/MedCare_PRD.pdf', 'PRJ-004-MEDCARE', 1, 14, 'USR-022-BA', '2025-10-01 08:00:00'),
('DOC-011', 'Patient_Flow.png', 'Luong dang ky benh nhan', 'file', 'image/png', 412672, 'uploads/projects/PRJ-004-MEDCARE/Patient_Flow.png', 'PRJ-004-MEDCARE', 0, 6, 'USR-017-DESIGN01', '2026-01-05 08:00:00'),
-- SmartBot Documents
('DOC-012', 'AI_Research_Report.pdf', 'Bao cao nghien cuu AI/ML', 'file', 'application/pdf', 2621440, 'uploads/projects/PRJ-005-SMARTBOT/AI_Research_Report.pdf', 'PRJ-005-SMARTBOT', 1, 9, 'USR-002-ADMIN-CTO', '2025-12-10 08:00:00');

-- =============================================
-- 13. DOCUMENT_SHARES
-- =============================================
INSERT INTO `document_shares` (`document_id`, `user_id`, `permission`, `shared_by`, `shared_at`) VALUES
('DOC-001', 'USR-023-GUEST-VIN', 'view', 'USR-003-MGR-PM01', '2025-06-15 08:00:00'),
('DOC-002', 'USR-023-GUEST-VIN', 'view', 'USR-003-MGR-PM01', '2025-08-20 08:00:00'),
('DOC-003', 'USR-012-DEV-FE01', 'edit', 'USR-017-DESIGN01', '2025-12-21 08:00:00'),
('DOC-003', 'USR-013-DEV-FE02', 'view', 'USR-017-DESIGN01', '2025-12-21 08:00:00'),
('DOC-005', 'USR-024-GUEST-FPT', 'view', 'USR-004-MGR-PM02', '2025-08-25 08:00:00'),
('DOC-006', 'USR-024-GUEST-FPT', 'view', 'USR-004-MGR-PM02', '2025-09-15 08:00:00'),
('DOC-006', 'USR-015-DEV-MOB1', 'edit', 'USR-017-DESIGN01', '2025-09-12 08:00:00'),
('DOC-006', 'USR-016-DEV-MOB2', 'edit', 'USR-017-DESIGN01', '2025-09-12 08:00:00'),
('DOC-008', 'USR-001-ADMIN-CEO', 'view', 'USR-003-MGR-PM01', '2025-12-20 08:00:00'),
('DOC-009', 'USR-009-DEV-BE02', 'edit', 'USR-003-MGR-PM01', '2026-01-03 08:00:00'),
('DOC-010', 'USR-006-MGR-SCRUM', 'edit', 'USR-004-MGR-PM02', '2025-10-05 08:00:00');

-- =============================================
-- 14. COMMENTS
-- =============================================
INSERT INTO `comments` (`id`, `entity_type`, `entity_id`, `content`, `parent_id`, `created_by`, `created_at`) VALUES
-- Comments on VNPay Integration
('CMT-001', 'task', 'TSK-VM-001', 'Da hoan thanh tich hop SDK, dang test callback', NULL, 'USR-008-DEV-BE01', '2026-01-08 14:30:00'),
('CMT-002', 'task', 'TSK-VM-001', 'Nho check ky transaction signature nhe', NULL, 'USR-005-MGR-TECH', '2026-01-08 15:00:00'),
('CMT-003', 'task', 'TSK-VM-001', '@USR-005-MGR-TECH Da implement, dang cho review', 'CMT-002', 'USR-008-DEV-BE01', '2026-01-09 09:30:00'),
('CMT-004', 'task', 'TSK-VM-001', 'Client hoi tien do, co kip deadline 15/1 khong?', NULL, 'USR-003-MGR-PM01', '2026-01-09 10:00:00'),
('CMT-005', 'task', 'TSK-VM-001', 'Co the hoan thanh dung han neu khong co blocker', 'CMT-004', 'USR-008-DEV-BE01', '2026-01-09 10:15:00'),
-- Comments on MoMo Integration
('CMT-006', 'task', 'TSK-VM-002', 'MoMo da cap merchant ID, bat dau tich hop', NULL, 'USR-009-DEV-BE02', '2026-01-06 11:00:00'),
('CMT-007', 'task', 'TSK-VM-002', 'Tham khao code VNPay de dam bao consistency', 'CMT-006', 'USR-005-MGR-TECH', '2026-01-06 14:00:00'),
-- Comments on Cart Bug
('CMT-008', 'task', 'TSK-VM-005', 'Loi xay ra tren iOS Safari, dang debug', NULL, 'USR-013-DEV-FE02', '2026-01-06 09:00:00'),
('CMT-009', 'task', 'TSK-VM-005', 'Co the la do CSS flexbox, check lai viewport', 'CMT-008', 'USR-012-DEV-FE01', '2026-01-06 09:30:00'),
('CMT-010', 'task', 'TSK-VM-005', 'Da tim ra nguyen nhan, fix trong hom nay', 'CMT-008', 'USR-013-DEV-FE02', '2026-01-06 14:00:00'),
-- Comments on Internal Transfer
('CMT-011', 'task', 'TSK-FB-001', 'Can confirm lai limit chuyen tien voi FPT', NULL, 'USR-008-DEV-BE01', '2026-01-03 10:00:00'),
('CMT-012', 'task', 'TSK-FB-001', 'Limit: 500tr/ngay, 2ty/thang. Da confirm voi client', 'CMT-011', 'USR-004-MGR-PM02', '2026-01-03 11:30:00'),
('CMT-013', 'task', 'TSK-FB-001', 'OK, se implement theo spec nay', 'CMT-011', 'USR-008-DEV-BE01', '2026-01-03 13:00:00'),
-- Comments on OTP Enhancement
('CMT-014', 'task', 'TSK-FB-003', 'Da tang OTP expiry tu 60s len 120s', NULL, 'USR-011-DEV-BE04', '2026-01-06 15:00:00'),
('CMT-015', 'task', 'TSK-FB-003', 'Test OK, OTP delivery time giam tu 5s xuong 2s', 'CMT-014', 'USR-019-QA01', '2026-01-08 10:00:00'),
-- Comments on Payroll Engine
('CMT-016', 'task', 'TSK-HR-001', 'Cong thuc tinh thue TNCN kha phuc tap, can review', NULL, 'USR-009-DEV-BE02', '2026-01-06 16:00:00'),
('CMT-017', 'task', 'TSK-HR-001', 'Lien he phong ke toan de verify cong thuc', 'CMT-016', 'USR-003-MGR-PM01', '2026-01-07 08:30:00'),
-- Comments on Overdue tasks
('CMT-018', 'task', 'TSK-VM-OVD1', 'Loi do timeout gateway, dang lien he VNPay', NULL, 'USR-008-DEV-BE01', '2026-01-03 09:00:00'),
('CMT-019', 'task', 'TSK-VM-OVD1', 'Day la blocker, can xu ly gap', 'CMT-018', 'USR-005-MGR-TECH', '2026-01-03 09:30:00'),
('CMT-020', 'task', 'TSK-FB-OVD1', 'Da identify root cause: SMS gateway qua tai', NULL, 'USR-008-DEV-BE01', '2026-01-02 14:00:00');


-- =============================================
-- 15. NOTIFICATIONS - Thong bao gan day
-- =============================================
INSERT INTO `notifications` (`id`, `user_id`, `type`, `title`, `message`, `data`, `is_read`, `read_at`, `created_at`) VALUES
-- Notifications cho USR-008-DEV-BE01 (Backend Senior)
('NTF-001', 'USR-008-DEV-BE01', 'task_assigned', 'Task moi duoc giao', 'Ban duoc giao task "Bill Payment Integration"', '{"task_id":"TSK-FB-004","project_id":"PRJ-002-FPTBANK"}', 0, NULL, '2026-01-10 09:00:00'),
('NTF-002', 'USR-008-DEV-BE01', 'comment', 'Binh luan moi', 'Le Quoc Hung da binh luan tren task "VNPay Integration"', '{"task_id":"TSK-VM-001","comment_id":"CMT-004"}', 1, '2026-01-09 10:05:00', '2026-01-09 10:00:00'),
('NTF-003', 'USR-008-DEV-BE01', 'task_due_soon', 'Task sap den han', 'Task "VNPay Integration - Phase 2" se den han trong 6 ngay', '{"task_id":"TSK-VM-001"}', 1, '2026-01-09 08:30:00', '2026-01-09 08:00:00'),
('NTF-004', 'USR-008-DEV-BE01', 'meeting', 'Cuoc hop sap dien ra', 'Sprint Planning - VinMart Sprint 4 bat dau luc 09:00', '{"event_id":"EVT-001"}', 1, '2026-01-06 08:45:00', '2026-01-06 08:30:00'),
-- Notifications cho USR-003-MGR-PM01 (PM)
('NTF-005', 'USR-003-MGR-PM01', 'task_overdue', 'Task qua han', 'Task "Fix Payment Timeout" da qua han 4 ngay', '{"task_id":"TSK-VM-OVD1"}', 1, '2026-01-09 08:15:00', '2026-01-09 08:00:00'),
('NTF-006', 'USR-003-MGR-PM01', 'project_update', 'Cap nhat du an', 'Du an VinMart dat 72% tien do', '{"project_id":"PRJ-001-VINMART"}', 0, NULL, '2026-01-08 17:00:00'),
('NTF-007', 'USR-003-MGR-PM01', 'comment', 'Binh luan moi', 'Hoang Van Tuan da tra loi binh luan cua ban', '{"task_id":"TSK-VM-001","comment_id":"CMT-005"}', 1, '2026-01-09 10:20:00', '2026-01-09 10:15:00'),
-- Notifications cho USR-009-DEV-BE02
('NTF-008', 'USR-009-DEV-BE02', 'task_assigned', 'Task moi duoc giao', 'Ban duoc giao task "Tax Report Module"', '{"task_id":"TSK-HR-003","project_id":"PRJ-003-HRMS"}', 0, NULL, '2026-01-10 09:00:00'),
('NTF-009', 'USR-009-DEV-BE02', 'mention', 'Ban duoc nhac den', 'Le Quoc Hung da nhac den ban trong binh luan', '{"task_id":"TSK-HR-001","comment_id":"CMT-017"}', 1, '2026-01-07 08:35:00', '2026-01-07 08:30:00'),
-- Notifications cho USR-019-QA01
('NTF-010', 'USR-019-QA01', 'task_assigned', 'Task moi duoc giao', 'Ban duoc giao task "Payment Testing"', '{"task_id":"TSK-VM-004","project_id":"PRJ-001-VINMART"}', 1, '2026-01-10 09:10:00', '2026-01-10 09:00:00'),
('NTF-011', 'USR-019-QA01', 'task_assigned', 'Task moi duoc giao', 'Ban duoc giao task "Security Audit Preparation"', '{"task_id":"TSK-FB-005","project_id":"PRJ-002-FPTBANK"}', 0, NULL, '2026-01-12 09:00:00'),
-- Notifications cho USR-004-MGR-PM02
('NTF-012', 'USR-004-MGR-PM02', 'task_overdue', 'Task qua han', 'Task "OTP Delivery Issue" da qua han 6 ngay', '{"task_id":"TSK-FB-OVD1"}', 1, '2026-01-09 08:10:00', '2026-01-09 08:00:00'),
('NTF-013', 'USR-004-MGR-PM02', 'task_status_changed', 'Trang thai task thay doi', 'Task "OTP Enhancement" chuyen sang In Review', '{"task_id":"TSK-FB-003"}', 1, '2026-01-08 16:00:00', '2026-01-08 15:30:00'),
-- Notifications cho USR-005-MGR-TECH
('NTF-014', 'USR-005-MGR-TECH', 'comment', 'Binh luan moi', 'Hoang Van Tuan da tra loi binh luan cua ban', '{"task_id":"TSK-VM-001","comment_id":"CMT-003"}', 1, '2026-01-09 09:35:00', '2026-01-09 09:30:00'),
('NTF-015', 'USR-005-MGR-TECH', 'meeting', 'Cuoc hop sap dien ra', 'Code Review Session bat dau luc 14:00', '{"event_id":"EVT-010"}', 0, NULL, '2026-01-09 13:30:00'),
-- Notifications cho USR-012-DEV-FE01
('NTF-016', 'USR-012-DEV-FE01', 'task_assigned', 'Task moi duoc giao', 'Ban duoc giao task "Checkout UI Optimization"', '{"task_id":"TSK-VM-003","project_id":"PRJ-001-VINMART"}', 1, '2026-01-08 09:10:00', '2026-01-08 09:00:00'),
('NTF-017', 'USR-012-DEV-FE01', 'document_shared', 'Tai lieu duoc chia se', 'Tran Ngoc Lan da chia se "UI_Mockup_Checkout.fig" voi ban', '{"document_id":"DOC-003"}', 1, '2025-12-21 08:15:00', '2025-12-21 08:00:00'),
-- Notifications cho USR-013-DEV-FE02
('NTF-018', 'USR-013-DEV-FE02', 'comment', 'Binh luan moi', 'Phan Thi Hoa da binh luan tren task cua ban', '{"task_id":"TSK-VM-005","comment_id":"CMT-009"}', 1, '2026-01-06 09:35:00', '2026-01-06 09:30:00'),
('NTF-019', 'USR-013-DEV-FE02', 'task_due_soon', 'Task sap den han', 'Task "Fix Cart Bug on Mobile" se den han ngay mai', '{"task_id":"TSK-VM-005"}', 1, '2026-01-09 08:05:00', '2026-01-09 08:00:00'),
-- Notifications cho USR-023-GUEST-VIN (Client)
('NTF-020', 'USR-023-GUEST-VIN', 'meeting', 'Cuoc hop sap dien ra', 'Demo Meeting - VinGroup vao ngay 15/01', '{"event_id":"EVT-005"}', 0, NULL, '2026-01-08 08:00:00'),
-- System notifications
('NTF-021', 'USR-001-ADMIN-CEO', 'system', 'Bao cao tuan', 'Bao cao tong hop tuan 01/2026 da san sang', '{"report_type":"weekly","week":"2026-W01"}', 0, NULL, '2026-01-06 08:00:00'),
('NTF-022', 'USR-002-ADMIN-CTO', 'system', 'Canh bao he thong', 'Server load cao bat thuong luc 02:00 AM', '{"alert_type":"performance","severity":"warning"}', 1, '2026-01-08 08:30:00', '2026-01-08 02:15:00');


-- =============================================
-- 16. ACTIVITY_LOGS - Nhat ky hoat dong
-- =============================================
INSERT INTO `activity_logs` (`id`, `user_id`, `entity_type`, `entity_id`, `action`, `old_values`, `new_values`, `ip_address`, `user_agent`, `created_at`) VALUES
-- Login activities
('LOG-001', 'USR-001-ADMIN-CEO', 'user', 'USR-001-ADMIN-CEO', 'login', NULL, NULL, '192.168.1.100', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/120.0', '2026-01-09 08:30:00'),
('LOG-002', 'USR-002-ADMIN-CTO', 'user', 'USR-002-ADMIN-CTO', 'login', NULL, NULL, '192.168.1.101', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) Safari/605.1', '2026-01-09 09:15:00'),
('LOG-003', 'USR-003-MGR-PM01', 'user', 'USR-003-MGR-PM01', 'login', NULL, NULL, '192.168.1.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/120.0', '2026-01-09 08:45:00'),
('LOG-004', 'USR-008-DEV-BE01', 'user', 'USR-008-DEV-BE01', 'login', NULL, NULL, '192.168.1.110', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/120.0', '2026-01-09 08:00:00'),
-- Task activities
('LOG-005', 'USR-008-DEV-BE01', 'task', 'TSK-VM-001', 'update', '{"status":"todo"}', '{"status":"in_progress"}', '192.168.1.110', 'Mozilla/5.0 Chrome/120.0', '2026-01-05 09:00:00'),
('LOG-006', 'USR-008-DEV-BE01', 'task', 'TSK-VM-001', 'update', '{"actual_hours":20}', '{"actual_hours":25}', '192.168.1.110', 'Mozilla/5.0 Chrome/120.0', '2026-01-09 17:00:00'),
('LOG-007', 'USR-009-DEV-BE02', 'task', 'TSK-VM-002', 'update', '{"status":"todo"}', '{"status":"in_progress"}', '192.168.1.111', 'Mozilla/5.0 Chrome/120.0', '2026-01-08 09:00:00'),
('LOG-008', 'USR-011-DEV-BE04', 'task', 'TSK-FB-003', 'update', '{"status":"in_progress"}', '{"status":"in_review"}', '192.168.1.112', 'Mozilla/5.0 Chrome/120.0', '2026-01-08 15:30:00'),
('LOG-009', 'USR-013-DEV-FE02', 'task', 'TSK-VM-005', 'update', '{"actual_hours":4}', '{"actual_hours":6}', '192.168.1.113', 'Mozilla/5.0 Chrome/120.0', '2026-01-08 17:00:00'),
-- Task creation
('LOG-010', 'USR-008-DEV-BE01', 'task', 'TSK-VM-006', 'create', NULL, '{"title":"Product Search Elasticsearch"}', '192.168.1.110', 'Mozilla/5.0 Chrome/120.0', '2026-01-08 08:00:00'),
('LOG-011', 'USR-003-MGR-PM01', 'task', 'TSK-HR-004', 'create', NULL, '{"title":"UAT Testing"}', '192.168.1.102', 'Mozilla/5.0 Chrome/120.0', '2026-01-15 08:00:00'),
-- Task assignment
('LOG-012', 'USR-005-MGR-TECH', 'task', 'TSK-VM-001', 'assign', NULL, '{"assigned_to":"USR-008-DEV-BE01"}', '192.168.1.105', 'Mozilla/5.0 Chrome/120.0', '2026-01-02 09:00:00'),
('LOG-013', 'USR-005-MGR-TECH', 'task', 'TSK-FB-004', 'assign', NULL, '{"assigned_to":"USR-008-DEV-BE01"}', '192.168.1.105', 'Mozilla/5.0 Chrome/120.0', '2026-01-10 09:00:00'),
-- Comment activities
('LOG-014', 'USR-008-DEV-BE01', 'comment', 'CMT-001', 'create', NULL, '{"task_id":"TSK-VM-001"}', '192.168.1.110', 'Mozilla/5.0 Chrome/120.0', '2026-01-08 14:30:00'),
('LOG-015', 'USR-005-MGR-TECH', 'comment', 'CMT-002', 'create', NULL, '{"task_id":"TSK-VM-001"}', '192.168.1.105', 'Mozilla/5.0 Chrome/120.0', '2026-01-08 15:00:00'),
('LOG-016', 'USR-003-MGR-PM01', 'comment', 'CMT-004', 'create', NULL, '{"task_id":"TSK-VM-001"}', '192.168.1.102', 'Mozilla/5.0 Chrome/120.0', '2026-01-09 10:00:00'),
-- Document activities
('LOG-017', 'USR-005-MGR-TECH', 'document', 'DOC-004', 'upload', NULL, '{"name":"Payment_Flow_Diagram.png"}', '192.168.1.105', 'Mozilla/5.0 Chrome/120.0', '2026-01-03 08:00:00'),
('LOG-018', 'USR-008-DEV-BE01', 'document', 'DOC-007', 'upload', NULL, '{"name":"Transfer_API_Spec.xlsx"}', '192.168.1.110', 'Mozilla/5.0 Chrome/120.0', '2026-01-05 08:00:00'),
('LOG-019', 'USR-017-DESIGN01', 'document', 'DOC-003', 'share', NULL, '{"shared_with":"USR-012-DEV-FE01"}', '192.168.1.117', 'Mozilla/5.0 Chrome/120.0', '2025-12-21 08:00:00'),
-- Project activities
('LOG-020', 'USR-003-MGR-PM01', 'project', 'PRJ-001-VINMART', 'update', '{"progress":70}', '{"progress":72}', '192.168.1.102', 'Mozilla/5.0 Chrome/120.0', '2026-01-08 17:00:00'),
('LOG-021', 'USR-003-MGR-PM01', 'project', 'PRJ-006-LOGISTICS', 'create', NULL, '{"name":"LogiTrack Logistics System"}', '192.168.1.102', 'Mozilla/5.0 Chrome/120.0', '2026-01-05 08:00:00'),
-- Calendar activities
('LOG-022', 'USR-006-MGR-SCRUM', 'event', 'EVT-001', 'create', NULL, '{"title":"Sprint Planning - VinMart Sprint 4"}', '192.168.1.106', 'Mozilla/5.0 Chrome/120.0', '2026-01-03 08:00:00'),
('LOG-023', 'USR-003-MGR-PM01', 'event', 'EVT-005', 'create', NULL, '{"title":"Demo Meeting - VinGroup"}', '192.168.1.102', 'Mozilla/5.0 Chrome/120.0', '2026-01-05 08:00:00'),
-- Checklist activities
('LOG-024', 'USR-008-DEV-BE01', 'checklist', 'CHK-001', 'complete', '{"is_completed":0}', '{"is_completed":1}', '192.168.1.110', 'Mozilla/5.0 Chrome/120.0', '2026-01-07 15:00:00'),
('LOG-025', 'USR-008-DEV-BE01', 'checklist', 'CHK-002', 'complete', '{"is_completed":0}', '{"is_completed":1}', '192.168.1.110', 'Mozilla/5.0 Chrome/120.0', '2026-01-08 11:00:00'),
('LOG-026', 'USR-008-DEV-BE01', 'checklist', 'CHK-003', 'complete', '{"is_completed":0}', '{"is_completed":1}', '192.168.1.110', 'Mozilla/5.0 Chrome/120.0', '2026-01-09 14:00:00'),
('LOG-027', 'USR-011-DEV-BE04', 'checklist', 'CHK-015', 'complete', '{"is_completed":0}', '{"is_completed":1}', '192.168.1.112', 'Mozilla/5.0 Chrome/120.0', '2026-01-05 14:00:00'),
('LOG-028', 'USR-011-DEV-BE04', 'checklist', 'CHK-016', 'complete', '{"is_completed":0}', '{"is_completed":1}', '192.168.1.112', 'Mozilla/5.0 Chrome/120.0', '2026-01-06 10:00:00'),
-- Member activities
('LOG-029', 'USR-003-MGR-PM01', 'project', 'PRJ-001-VINMART', 'add_member', NULL, '{"user_id":"USR-010-DEV-BE03"}', '192.168.1.102', 'Mozilla/5.0 Chrome/120.0', '2025-06-15 08:00:00'),
('LOG-030', 'USR-004-MGR-PM02', 'project', 'PRJ-002-FPTBANK', 'add_member', NULL, '{"user_id":"USR-020-QA02"}', '192.168.1.104', 'Mozilla/5.0 Chrome/120.0', '2025-09-01 08:00:00');

-- =============================================
-- HOAN TAT - Du lieu day du cho thang 1/2026
-- =============================================
/*
THONG KE CAP NHAT:
- 25 Users
- 12 Labels  
- 10 Projects
- 52 Project Members
- 21 Tasks
- 19 Task Assignees
- 30 Task Labels
- 23 Task Checklists
- 12 Documents
- 11 Document Shares
- 20 Comments
- 15 Calendar Events
- 17 Event Attendees
- 24 User Settings
- 22 Notifications
- 30 Activity Logs

TAI KHOAN DEMO:
- ceo@saigontech.vn / password (Admin - CEO)
- cto@saigontech.vn / password (Admin - CTO)
- pm.hung@saigontech.vn / password (Manager - PM)
- pm.linh@saigontech.vn / password (Manager - PM)
- backend.tuan@saigontech.vn / password (Member - Senior Backend)
- frontend.hoa@saigontech.vn / password (Member - Senior Frontend)
- qa.hanh@saigontech.vn / password (Member - QA)
- client.vingroup@gmail.com / password (Guest - VinGroup)
- client.fpt@gmail.com / password (Guest - FPT)

DU LIEU THUC TE:
- 4 du an dang active voi tasks thuc te
- 2 tasks qua han (overdue) de test canh bao
- Cac su kien lich trong thang 1/2026
- Comments va activity logs thuc te
- Notifications chua doc va da doc
*/
