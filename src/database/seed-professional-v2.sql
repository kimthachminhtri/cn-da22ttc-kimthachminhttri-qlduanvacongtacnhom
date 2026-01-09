-- =============================================
-- TASKFLOW - Dá»® LIá»†U MáºªU CHUYÃŠN NGHIá»†P
-- Version: 2.0 - Enterprise Grade
-- Author: Senior Product Analyst
-- Purpose: Demo, Testing, Báº£o vá»‡ Ä‘á»“ Ã¡n
-- =============================================
-- 
-- ðŸ“‹ Má»¤C Lá»¤C:
-- 1. USERS - 25 ngÆ°á»i dÃ¹ng (Ä‘á»™i ngÅ© cÃ´ng ty pháº§n má»m)
-- 2. LABELS - 12 nhÃ£n phÃ¢n loáº¡i task
-- 3. PROJECTS - 10 dá»± Ã¡n Ä‘a dáº¡ng tráº¡ng thÃ¡i
-- 4. PROJECT_MEMBERS - PhÃ¢n quyá»n thÃ nh viÃªn
-- 5. TASKS - 120+ tasks vá»›i Ä‘áº§y Ä‘á»§ tÃ¬nh huá»‘ng
-- 6. TASK_ASSIGNEES - PhÃ¢n cÃ´ng cÃ´ng viá»‡c
-- 7. TASK_LABELS - Gáº¯n nhÃ£n task
-- 8. TASK_CHECKLISTS - Checklist chi tiáº¿t
-- 9. DOCUMENTS - TÃ i liá»‡u dá»± Ã¡n
-- 10. COMMENTS - BÃ¬nh luáº­n trao Ä‘á»•i
-- 11. NOTIFICATIONS - ThÃ´ng bÃ¡o há»‡ thá»‘ng
-- 12. ACTIVITY_LOGS - Lá»‹ch sá»­ hoáº¡t Ä‘á»™ng
-- 13. CALENDAR_EVENTS - Sá»± kiá»‡n & cuá»™c há»p
-- 14. USER_SETTINGS - CÃ i Ä‘áº·t cÃ¡ nhÃ¢n
--
-- =============================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO';

-- XÃ³a dá»¯ liá»‡u cÅ© (theo thá»© tá»± dependency)
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
-- 1. USERS - NGÆ¯á»œI DÃ™NG
-- =============================================
-- MÃ´ táº£: Äá»™i ngÅ© cÃ´ng ty SaigonTech Solutions
-- Sá»‘ lÆ°á»£ng: 25 users
-- PhÃ¢n bá»‘:
--   - 2 Admin (CEO, CTO)
--   - 5 Manager (PM, Tech Lead)
--   - 15 Member (Dev, Designer, QA, BA)
--   - 2 Guest (KhÃ¡ch hÃ ng)
--   - 1 Inactive (NhÃ¢n viÃªn Ä‘Ã£ nghá»‰)
-- =============================================

INSERT INTO `users` (`id`, `email`, `password_hash`, `full_name`, `avatar_url`, `role`, `department`, `position`, `phone`, `is_active`, `email_verified_at`, `last_login_at`, `created_at`) VALUES

-- ===== ADMIN (2) =====
('USR-001-ADMIN-CEO', 'ceo@saigontech.vn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Nguyá»…n VÄƒn Minh', '/avatars/nguyen-van-minh.jpg', 'admin', 'Ban GiÃ¡m Ä‘á»‘c', 'CEO - GiÃ¡m Ä‘á»‘c Ä‘iá»u hÃ nh', '0903123456', 1, '2024-01-01 08:00:00', '2026-01-08 08:30:00', '2024-01-01 00:00:00'),

('USR-002-ADMIN-CTO', 'cto@saigontech.vn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Tráº§n Thá»‹ HÆ°Æ¡ng', '/avatars/tran-thi-huong.jpg', 'admin', 'Ban GiÃ¡m Ä‘á»‘c', 'CTO - GiÃ¡m Ä‘á»‘c cÃ´ng nghá»‡', '0903123457', 1, '2024-01-01 08:00:00', '2026-01-08 09:15:00', '2024-01-01 00:00:00'),

-- ===== MANAGER (5) =====
('USR-003-MGR-PM01', 'pm.hung@saigontech.vn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'LÃª Quá»‘c HÃ¹ng', '/avatars/le-quoc-hung.jpg', 'manager', 'Project Management', 'Senior Project Manager', '0903123458', 1, '2024-02-01 08:00:00', '2026-01-08 08:45:00', '2024-02-01 00:00:00'),

('USR-004-MGR-PM02', 'pm.linh@saigontech.vn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Pháº¡m ThÃ¹y Linh', '/avatars/pham-thuy-linh.jpg', 'manager', 'Project Management', 'Project Manager', '0903123459', 1, '2024-03-01 08:00:00', '2026-01-07 17:30:00', '2024-03-01 00:00:00'),

('USR-005-MGR-TECH', 'tech.lead@saigontech.vn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'VÃµ HoÃ ng Nam', '/avatars/vo-hoang-nam.jpg', 'manager', 'Engineering', 'Technical Lead', '0903123460', 1, '2024-02-15 08:00:00', '2026-01-08 10:00:00', '2024-02-15 00:00:00'),

('USR-006-MGR-SCRUM', 'scrum@saigontech.vn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Äáº·ng Minh Tuáº¥n', '/avatars/dang-minh-tuan.jpg', 'manager', 'Project Management', 'Scrum Master', '0903123461', 1, '2024-04-01 08:00:00', '2026-01-08 09:00:00', '2024-04-01 00:00:00'),

('USR-007-MGR-DESIGN', 'design.lead@saigontech.vn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Nguyá»…n Thá»‹ Mai', '/avatars/nguyen-thi-mai.jpg', 'manager', 'Design', 'Design Lead', '0903123462', 1, '2024-03-15 08:00:00', '2026-01-08 08:30:00', '2024-03-15 00:00:00'),

-- ===== MEMBER - Backend Developers (4) =====
('USR-008-DEV-BE01', 'backend.tuan@saigontech.vn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'HoÃ ng VÄƒn Tuáº¥n', '/avatars/hoang-van-tuan.jpg', 'member', 'Engineering', 'Senior Backend Developer', '0903123463', 1, '2024-05-01 08:00:00', '2026-01-08 08:00:00', '2024-05-01 00:00:00'),

('USR-009-DEV-BE02', 'backend.duc@saigontech.vn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Tráº§n Minh Äá»©c', '/avatars/tran-minh-duc.jpg', 'member', 'Engineering', 'Backend Developer', '0903123464', 1, '2024-06-01 08:00:00', '2026-01-08 09:30:00', '2024-06-01 00:00:00'),

('USR-010-DEV-BE03', 'backend.long@saigontech.vn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Nguyá»…n ThÃ nh Long', '/avatars/nguyen-thanh-long.jpg', 'member', 'Engineering', 'Backend Developer', '0903123465', 1, '2024-07-01 08:00:00', '2026-01-07 18:00:00', '2024-07-01 00:00:00'),

('USR-011-DEV-BE04', 'backend.khanh@saigontech.vn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'LÃª Trung KhÃ¡nh', '/avatars/le-trung-khanh.jpg', 'member', 'Engineering', 'Junior Backend Developer', '0903123466', 1, '2025-01-15 08:00:00', '2026-01-08 08:15:00', '2025-01-15 00:00:00'),

-- ===== MEMBER - Frontend Developers (3) =====
('USR-012-DEV-FE01', 'frontend.hoa@saigontech.vn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Phan Thá»‹ Hoa', '/avatars/phan-thi-hoa.jpg', 'member', 'Engineering', 'Senior Frontend Developer', '0903123467', 1, '2024-05-15 08:00:00', '2026-01-08 08:45:00', '2024-05-15 00:00:00'),

('USR-013-DEV-FE02', 'frontend.phong@saigontech.vn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'VÅ© ÄÃ¬nh Phong', '/avatars/vu-dinh-phong.jpg', 'member', 'Engineering', 'Frontend Developer', '0903123468', 1, '2024-08-01 08:00:00', '2026-01-08 09:00:00', '2024-08-01 00:00:00'),

('USR-014-DEV-FE03', 'frontend.thao@saigontech.vn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Äá»— PhÆ°Æ¡ng Tháº£o', '/avatars/do-phuong-thao.jpg', 'member', 'Engineering', 'Junior Frontend Developer', '0903123469', 1, '2025-03-01 08:00:00', '2026-01-07 17:45:00', '2025-03-01 00:00:00'),

-- ===== MEMBER - Mobile Developers (2) =====
('USR-015-DEV-MOB1', 'mobile.an@saigontech.vn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'BÃ¹i VÄƒn An', '/avatars/bui-van-an.jpg', 'member', 'Engineering', 'Mobile Developer (iOS)', '0903123470', 1, '2024-09-01 08:00:00', '2026-01-08 08:30:00', '2024-09-01 00:00:00'),

('USR-016-DEV-MOB2', 'mobile.binh@saigontech.vn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Trá»‹nh Quá»‘c BÃ¬nh', '/avatars/trinh-quoc-binh.jpg', 'member', 'Engineering', 'Mobile Developer (Android)', '0903123471', 1, '2024-09-15 08:00:00', '2026-01-08 09:15:00', '2024-09-15 00:00:00'),

-- ===== MEMBER - Designers (2) =====
('USR-017-DESIGN01', 'design.lan@saigontech.vn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Tráº§n Ngá»c Lan', '/avatars/tran-ngoc-lan.jpg', 'member', 'Design', 'UI/UX Designer', '0903123472', 1, '2024-06-15 08:00:00', '2026-01-08 08:00:00', '2024-06-15 00:00:00'),

('USR-018-DESIGN02', 'design.khoa@saigontech.vn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'LÃ½ ÄÄƒng Khoa', '/avatars/ly-dang-khoa.jpg', 'member', 'Design', 'Graphic Designer', '0903123473', 1, '2024-10-01 08:00:00', '2026-01-07 16:30:00', '2024-10-01 00:00:00'),

-- ===== MEMBER - QA Engineers (2) =====
('USR-019-QA01', 'qa.hanh@saigontech.vn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Nguyá»…n Thá»‹ Háº¡nh', '/avatars/nguyen-thi-hanh.jpg', 'member', 'Quality Assurance', 'Senior QA Engineer', '0903123474', 1, '2024-04-15 08:00:00', '2026-01-08 08:30:00', '2024-04-15 00:00:00'),

('USR-020-QA02', 'qa.son@saigontech.vn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Pháº¡m VÄƒn SÆ¡n', '/avatars/pham-van-son.jpg', 'member', 'Quality Assurance', 'QA Engineer', '0903123475', 1, '2024-11-01 08:00:00', '2026-01-08 09:00:00', '2024-11-01 00:00:00'),

-- ===== MEMBER - DevOps & BA =====
('USR-021-DEVOPS', 'devops@saigontech.vn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Cao Minh QuÃ¢n', '/avatars/cao-minh-quan.jpg', 'member', 'Engineering', 'DevOps Engineer', '0903123476', 1, '2024-07-15 08:00:00', '2026-01-08 07:30:00', '2024-07-15 00:00:00'),

('USR-022-BA', 'ba.thuy@saigontech.vn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'LÃª Thá»‹ Thá»§y', '/avatars/le-thi-thuy.jpg', 'member', 'Business Analysis', 'Business Analyst', '0903123477', 1, '2024-08-15 08:00:00', '2026-01-08 08:45:00', '2024-08-15 00:00:00'),

-- ===== GUEST - KhÃ¡ch hÃ ng (2) =====
('USR-023-GUEST-VIN', 'client.vingroup@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Nguyá»…n HoÃ ng Anh', NULL, 'guest', 'VinGroup', 'Product Owner', '0903123478', 1, '2025-06-01 08:00:00', '2026-01-06 14:00:00', '2025-06-01 00:00:00'),

('USR-024-GUEST-FPT', 'client.fpt@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Tráº§n VÄƒn Báº£o', NULL, 'guest', 'FPT Software', 'Technical Manager', '0903123479', 1, '2025-07-01 08:00:00', '2026-01-05 10:30:00', '2025-07-01 00:00:00'),

-- ===== INACTIVE - NhÃ¢n viÃªn Ä‘Ã£ nghá»‰ (1) =====
('USR-025-INACTIVE', 'former.employee@saigontech.vn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Tráº§n VÄƒn CÆ°á»ng', NULL, 'member', 'Engineering', 'Backend Developer', '0903123480', 0, '2024-06-01 08:00:00', '2025-09-30 17:00:00', '2024-06-01 00:00:00');


-- =============================================
-- 2. LABELS - NHÃƒN PHÃ‚N LOáº I
-- =============================================
-- MÃ´ táº£: NhÃ£n Ä‘á»ƒ phÃ¢n loáº¡i vÃ  lá»c task
-- Sá»‘ lÆ°á»£ng: 12 labels
-- PhÃ¢n loáº¡i: Bug, Feature, Enhancement, Documentation, 
--            Testing, Design, Research, Urgent, Security, Performance
-- =============================================

INSERT INTO `labels` (`id`, `name`, `color`, `description`, `created_at`) VALUES
('LBL-001-BUG', 'Bug', '#EF4444', 'Lá»—i cáº§n sá»­a', '2024-01-15 00:00:00'),
('LBL-002-FEATURE', 'Feature', '#3B82F6', 'TÃ­nh nÄƒng má»›i', '2024-01-15 00:00:00'),
('LBL-003-ENHANCE', 'Enhancement', '#8B5CF6', 'Cáº£i tiáº¿n tÃ­nh nÄƒng hiá»‡n cÃ³', '2024-01-15 00:00:00'),
('LBL-004-DOCS', 'Documentation', '#6B7280', 'TÃ i liá»‡u', '2024-01-15 00:00:00'),
('LBL-005-TEST', 'Testing', '#F59E0B', 'Kiá»ƒm thá»­', '2024-01-15 00:00:00'),
('LBL-006-DESIGN', 'Design', '#EC4899', 'Thiáº¿t káº¿ UI/UX', '2024-01-15 00:00:00'),
('LBL-007-RESEARCH', 'Research', '#14B8A6', 'NghiÃªn cá»©u', '2024-01-15 00:00:00'),
('LBL-008-URGENT', 'Urgent', '#DC2626', 'Kháº©n cáº¥p', '2024-01-15 00:00:00'),
('LBL-009-SECURITY', 'Security', '#991B1B', 'Báº£o máº­t', '2024-01-15 00:00:00'),
('LBL-010-PERF', 'Performance', '#059669', 'Hiá»‡u nÄƒng', '2024-01-15 00:00:00'),
('LBL-011-REFACTOR', 'Refactor', '#7C3AED', 'TÃ¡i cáº¥u trÃºc code', '2024-01-15 00:00:00'),
('LBL-012-HOTFIX', 'Hotfix', '#B91C1C', 'Sá»­a lá»—i kháº©n cáº¥p production', '2024-01-15 00:00:00');

-- =============================================
-- 3. PROJECTS - Dá»° ÃN
-- =============================================
-- MÃ´ táº£: CÃ¡c dá»± Ã¡n cá»§a cÃ´ng ty
-- Sá»‘ lÆ°á»£ng: 10 projects
-- PhÃ¢n bá»‘ tráº¡ng thÃ¡i:
--   - 4 Active (Ä‘ang triá»ƒn khai)
--   - 2 Planning (Ä‘ang lÃªn káº¿ hoáº¡ch)
--   - 2 Completed (Ä‘Ã£ hoÃ n thÃ nh)
--   - 1 On Hold (táº¡m dá»«ng)
--   - 1 Cancelled (Ä‘Ã£ há»§y)
-- =============================================

INSERT INTO `projects` (`id`, `name`, `description`, `color`, `icon`, `status`, `priority`, `progress`, `start_date`, `end_date`, `budget`, `created_by`, `created_at`) VALUES

-- ===== ACTIVE PROJECTS (4) =====
('PRJ-001-VINMART', 
 'VinMart E-commerce Platform', 
 'XÃ¢y dá»±ng ná»n táº£ng thÆ°Æ¡ng máº¡i Ä‘iá»‡n tá»­ cho VinMart vá»›i Ä‘áº§y Ä‘á»§ tÃ­nh nÄƒng: quáº£n lÃ½ sáº£n pháº©m, giá» hÃ ng, thanh toÃ¡n online, quáº£n lÃ½ Ä‘Æ¡n hÃ ng, loyalty program. TÃ­ch há»£p vá»›i há»‡ thá»‘ng ERP vÃ  POS hiá»‡n cÃ³.',
 '#10B981', 'shopping-cart', 'active', 'high', 72,
 '2025-06-01', '2025-03-31', 2500000000.00,
 'USR-003-MGR-PM01', '2025-05-15 00:00:00'),

('PRJ-002-FPTBANK', 
 'FPT Mobile Banking App', 
 'PhÃ¡t triá»ƒn á»©ng dá»¥ng mobile banking cho FPT Bank. Bao gá»“m: chuyá»ƒn tiá»n, thanh toÃ¡n hÃ³a Ä‘Æ¡n, quáº£n lÃ½ tÃ i khoáº£n, tiáº¿t kiá»‡m online, vay online. YÃªu cáº§u báº£o máº­t cao theo chuáº©n PCI-DSS.',
 '#3B82F6', 'credit-card', 'active', 'urgent', 45,
 '2025-08-01', '2025-06-30', 3500000000.00,
 'USR-004-MGR-PM02', '2025-07-20 00:00:00'),

('PRJ-003-HRMS', 
 'SaigonTech HRMS Internal', 
 'Há»‡ thá»‘ng quáº£n lÃ½ nhÃ¢n sá»± ná»™i bá»™: cháº¥m cÃ´ng, nghá»‰ phÃ©p, tÃ­nh lÆ°Æ¡ng, Ä‘Ã¡nh giÃ¡ KPI, tuyá»ƒn dá»¥ng, Ä‘Ã o táº¡o. TÃ­ch há»£p mÃ¡y cháº¥m cÃ´ng vÃ  há»‡ thá»‘ng káº¿ toÃ¡n.',
 '#8B5CF6', 'users', 'active', 'medium', 88,
 '2025-03-01', '2026-01-31', 450000000.00,
 'USR-003-MGR-PM01', '2025-02-15 00:00:00'),

('PRJ-004-MEDCARE', 
 'MedCare Healthcare Platform', 
 'Ná»n táº£ng y táº¿ sá»‘: Ä‘áº·t lá»‹ch khÃ¡m, há»“ sÆ¡ bá»‡nh Ã¡n Ä‘iá»‡n tá»­, tÆ° váº¥n online, quáº£n lÃ½ thuá»‘c, thanh toÃ¡n BHYT. LiÃªn thÃ´ng vá»›i há»‡ thá»‘ng BHXH.',
 '#EF4444', 'heart', 'active', 'high', 25,
 '2025-10-01', '2025-09-30', 1800000000.00,
 'USR-004-MGR-PM02', '2025-09-15 00:00:00'),

-- ===== PLANNING PROJECTS (2) =====
('PRJ-005-SMARTBOT', 
 'SmartBot AI Assistant', 
 'Chatbot AI há»— trá»£ khÃ¡ch hÃ ng tá»± Ä‘á»™ng. Sá»­ dá»¥ng NLP vÃ  Machine Learning Ä‘á»ƒ hiá»ƒu vÃ  tráº£ lá»i cÃ¢u há»i. TÃ­ch há»£p vá»›i cÃ¡c há»‡ thá»‘ng CRM, Helpdesk.',
 '#F59E0B', 'message-circle', 'planning', 'medium', 5,
 '2026-02-01', '2025-08-31', 800000000.00,
 'USR-002-ADMIN-CTO', '2025-12-01 00:00:00'),

('PRJ-006-LOGISTICS', 
 'LogiTrack Logistics System', 
 'Há»‡ thá»‘ng quáº£n lÃ½ váº­n chuyá»ƒn vÃ  logistics: theo dÃµi Ä‘Æ¡n hÃ ng realtime, tá»‘i Æ°u tuyáº¿n Ä‘Æ°á»ng, quáº£n lÃ½ kho, tÃ­ch há»£p GPS vÃ  IoT.',
 '#14B8A6', 'truck', 'planning', 'low', 0,
 '2025-04-01', '2025-12-31', 1200000000.00,
 'USR-003-MGR-PM01', '2026-01-05 00:00:00'),

-- ===== COMPLETED PROJECTS (2) =====
('PRJ-007-DEVOPS', 
 'DevOps Infrastructure', 
 'XÃ¢y dá»±ng háº¡ táº§ng DevOps: CI/CD pipeline, container orchestration (K8s), monitoring, logging, auto-scaling. ÄÃ£ triá»ƒn khai thÃ nh cÃ´ng cho toÃ n bá»™ dá»± Ã¡n.',
 '#059669', 'server', 'completed', 'high', 100,
 '2025-01-01', '2025-06-30', 350000000.00,
 'USR-002-ADMIN-CTO', '2024-12-15 00:00:00'),

('PRJ-008-WEBSITE', 
 'Corporate Website Redesign', 
 'Thiáº¿t káº¿ láº¡i website cÃ´ng ty vá»›i giao diá»‡n hiá»‡n Ä‘áº¡i, responsive, SEO-friendly. TÃ­ch há»£p blog, portfolio, tuyá»ƒn dá»¥ng.',
 '#EC4899', 'globe', 'completed', 'medium', 100,
 '2025-04-01', '2025-08-31', 180000000.00,
 'USR-007-MGR-DESIGN', '2025-03-20 00:00:00'),

-- ===== ON HOLD PROJECT (1) =====
('PRJ-009-EDUTECH', 
 'EduTech Learning Platform', 
 'Ná»n táº£ng há»c trá»±c tuyáº¿n: khÃ³a há»c video, quiz, chá»©ng chá»‰, live class. Táº¡m dá»«ng do khÃ¡ch hÃ ng thay Ä‘á»•i yÃªu cáº§u.',
 '#6B7280', 'book-open', 'on_hold', 'low', 35,
 '2025-05-01', '2026-02-28', 650000000.00,
 'USR-004-MGR-PM02', '2025-04-15 00:00:00'),

-- ===== CANCELLED PROJECT (1) =====
('PRJ-010-CRYPTO', 
 'CryptoWallet Exchange', 
 'SÃ n giao dá»‹ch tiá»n Ä‘iá»‡n tá»­. Dá»± Ã¡n bá»‹ há»§y do thay Ä‘á»•i quy Ä‘á»‹nh phÃ¡p luáº­t vá» cryptocurrency.',
 '#9CA3AF', 'dollar-sign', 'cancelled', 'high', 15,
 '2025-02-01', '2025-12-31', 2000000000.00,
 'USR-001-ADMIN-CEO', '2025-01-15 00:00:00');


-- =============================================
-- 4. PROJECT_MEMBERS - THÃ€NH VIÃŠN Dá»° ÃN
-- =============================================
-- MÃ´ táº£: PhÃ¢n quyá»n thÃ nh viÃªn trong tá»«ng dá»± Ã¡n
-- Vai trÃ²: owner, manager, member, viewer
-- TÃ¬nh huá»‘ng Ä‘áº·c biá»‡t:
--   - Dá»± Ã¡n cÃ³ nhiá»u owner (Ä‘á»“ng quáº£n lÃ½)
--   - Dá»± Ã¡n chá»‰ cÃ³ 1 thÃ nh viÃªn
--   - Guest chá»‰ cÃ³ quyá»n viewer
-- =============================================

INSERT INTO `project_members` (`project_id`, `user_id`, `role`, `joined_at`) VALUES

-- ===== PRJ-001: VinMart E-commerce (Team lá»›n - 12 ngÆ°á»i) =====
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

-- ===== PRJ-002: FPT Mobile Banking (Team 10 ngÆ°á»i) =====
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

-- ===== PRJ-003: HRMS Internal (Team nhá» - 6 ngÆ°á»i) =====
('PRJ-003-HRMS', 'USR-003-MGR-PM01', 'owner', '2025-02-15 08:00:00'),
('PRJ-003-HRMS', 'USR-009-DEV-BE02', 'member', '2025-03-01 08:00:00'),
('PRJ-003-HRMS', 'USR-012-DEV-FE01', 'member', '2025-03-01 08:00:00'),
('PRJ-003-HRMS', 'USR-014-DEV-FE03', 'member', '2025-03-15 08:00:00'),
('PRJ-003-HRMS', 'USR-018-DESIGN02', 'member', '2025-03-01 08:00:00'),
('PRJ-003-HRMS', 'USR-020-QA02', 'member', '2025-04-01 08:00:00'),

-- ===== PRJ-004: MedCare Healthcare (Team 8 ngÆ°á»i) =====
('PRJ-004-MEDCARE', 'USR-004-MGR-PM02', 'owner', '2025-09-15 08:00:00'),
('PRJ-004-MEDCARE', 'USR-006-MGR-SCRUM', 'manager', '2025-09-20 08:00:00'),
('PRJ-004-MEDCARE', 'USR-010-DEV-BE03', 'member', '2025-10-01 08:00:00'),
('PRJ-004-MEDCARE', 'USR-011-DEV-BE04', 'member', '2025-10-01 08:00:00'),
('PRJ-004-MEDCARE', 'USR-013-DEV-FE02', 'member', '2025-10-01 08:00:00'),
('PRJ-004-MEDCARE', 'USR-014-DEV-FE03', 'member', '2025-10-15 08:00:00'),
('PRJ-004-MEDCARE', 'USR-017-DESIGN01', 'member', '2025-09-20 08:00:00'),
('PRJ-004-MEDCARE', 'USR-022-BA', 'member', '2025-09-15 08:00:00'),

-- ===== PRJ-005: SmartBot AI (Team nhá» - Planning) =====
('PRJ-005-SMARTBOT', 'USR-002-ADMIN-CTO', 'owner', '2025-12-01 08:00:00'),
('PRJ-005-SMARTBOT', 'USR-005-MGR-TECH', 'manager', '2025-12-01 08:00:00'),
('PRJ-005-SMARTBOT', 'USR-008-DEV-BE01', 'member', '2025-12-15 08:00:00'),
('PRJ-005-SMARTBOT', 'USR-022-BA', 'member', '2025-12-01 08:00:00'),

-- ===== PRJ-006: LogiTrack (Chá»‰ cÃ³ PM - Planning) =====
('PRJ-006-LOGISTICS', 'USR-003-MGR-PM01', 'owner', '2026-01-05 08:00:00'),

-- ===== PRJ-007: DevOps Infrastructure (Completed) =====
('PRJ-007-DEVOPS', 'USR-002-ADMIN-CTO', 'owner', '2024-12-15 08:00:00'),
('PRJ-007-DEVOPS', 'USR-021-DEVOPS', 'member', '2025-01-01 08:00:00'),
('PRJ-007-DEVOPS', 'USR-008-DEV-BE01', 'member', '2025-01-15 08:00:00'),

-- ===== PRJ-008: Website Redesign (Completed) =====
('PRJ-008-WEBSITE', 'USR-007-MGR-DESIGN', 'owner', '2025-03-20 08:00:00'),
('PRJ-008-WEBSITE', 'USR-017-DESIGN01', 'member', '2025-04-01 08:00:00'),
('PRJ-008-WEBSITE', 'USR-018-DESIGN02', 'member', '2025-04-01 08:00:00'),
('PRJ-008-WEBSITE', 'USR-012-DEV-FE01', 'member', '2025-04-15 08:00:00'),

-- ===== PRJ-009: EduTech (On Hold) =====
('PRJ-009-EDUTECH', 'USR-004-MGR-PM02', 'owner', '2025-04-15 08:00:00'),
('PRJ-009-EDUTECH', 'USR-009-DEV-BE02', 'member', '2025-05-01 08:00:00'),
('PRJ-009-EDUTECH', 'USR-013-DEV-FE02', 'member', '2025-05-01 08:00:00'),
('PRJ-009-EDUTECH', 'USR-025-INACTIVE', 'member', '2025-05-01 08:00:00'),

-- ===== PRJ-010: CryptoWallet (Cancelled) =====
('PRJ-010-CRYPTO', 'USR-001-ADMIN-CEO', 'owner', '2025-01-15 08:00:00'),
('PRJ-010-CRYPTO', 'USR-005-MGR-TECH', 'manager', '2025-01-15 08:00:00');


-- =============================================
-- 5. TASKS - CÃ”NG VIá»†C
-- =============================================
-- MÃ´ táº£: Tasks cá»§a cÃ¡c dá»± Ã¡n
-- Sá»‘ lÆ°á»£ng: 130+ tasks
-- PhÃ¢n bá»‘ tráº¡ng thÃ¡i (giá»‘ng thá»±c táº¿):
--   - backlog: 20% (chÆ°a báº¯t Ä‘áº§u)
--   - todo: 15% (sáºµn sÃ ng lÃ m)
--   - in_progress: 25% (Ä‘ang lÃ m)
--   - in_review: 10% (Ä‘ang review)
--   - done: 30% (hoÃ n thÃ nh)
-- TÃ¬nh huá»‘ng Ä‘áº·c biá»‡t:
--   - Task quÃ¡ háº¡n (overdue)
--   - Task sáº¯p háº¿t háº¡n (due soon)
--   - Task khÃ´ng cÃ³ assignee
--   - Task cÃ³ nhiá»u assignee
--   - Task cá»§a nhÃ¢n viÃªn Ä‘Ã£ nghá»‰
-- =============================================

-- ===== PRJ-001: VinMart E-commerce - 35 tasks =====

INSERT INTO `tasks` (`id`, `project_id`, `title`, `description`, `status`, `priority`, `position`, `start_date`, `due_date`, `completed_at`, `estimated_hours`, `actual_hours`, `created_by`, `created_at`) VALUES

-- Sprint 1: Foundation (DONE - 8 tasks)
('TSK-001-VM-001', 'PRJ-001-VINMART', 'Thiáº¿t káº¿ Database Schema', 'Thiáº¿t káº¿ cáº¥u trÃºc database cho há»‡ thá»‘ng e-commerce: users, products, orders, payments, inventory', 'done', 'high', 1, '2025-06-01', '2025-06-15', '2025-06-14 17:00:00', 40.00, 38.00, 'USR-005-MGR-TECH', '2025-05-20 08:00:00'),

('TSK-001-VM-002', 'PRJ-001-VINMART', 'Setup Project Architecture', 'Cáº¥u trÃºc project theo Clean Architecture, setup Docker, CI/CD pipeline', 'done', 'high', 2, '2025-06-01', '2025-06-10', '2025-06-09 16:30:00', 24.00, 20.00, 'USR-005-MGR-TECH', '2025-05-20 08:00:00'),

('TSK-001-VM-003', 'PRJ-001-VINMART', 'Wireframe toÃ n bá»™ há»‡ thá»‘ng', 'Thiáº¿t káº¿ wireframe cho táº¥t cáº£ mÃ n hÃ¬nh: Home, Category, Product, Cart, Checkout, Account', 'done', 'high', 3, '2025-05-25', '2025-06-10', '2025-06-08 15:00:00', 32.00, 35.00, 'USR-007-MGR-DESIGN', '2025-05-20 08:00:00'),

('TSK-001-VM-004', 'PRJ-001-VINMART', 'API Authentication Module', 'Implement JWT authentication, OAuth2 (Google, Facebook), refresh token', 'done', 'high', 4, '2025-06-10', '2025-06-25', '2025-06-24 18:00:00', 32.00, 30.00, 'USR-008-DEV-BE01', '2025-06-01 08:00:00'),

('TSK-001-VM-005', 'PRJ-001-VINMART', 'UI Design System', 'XÃ¢y dá»±ng Design System: colors, typography, components, icons', 'done', 'medium', 5, '2025-06-05', '2025-06-20', '2025-06-19 17:30:00', 40.00, 42.00, 'USR-017-DESIGN01', '2025-05-25 08:00:00'),

('TSK-001-VM-006', 'PRJ-001-VINMART', 'Frontend Base Setup', 'Setup React project vá»›i TypeScript, Redux Toolkit, React Query, Tailwind CSS', 'done', 'high', 6, '2025-06-10', '2025-06-20', '2025-06-18 16:00:00', 16.00, 14.00, 'USR-012-DEV-FE01', '2025-06-05 08:00:00'),

('TSK-001-VM-007', 'PRJ-001-VINMART', 'Product Management API', 'CRUD API cho products: create, read, update, delete, search, filter, pagination', 'done', 'high', 7, '2025-06-20', '2025-07-05', '2025-07-04 17:00:00', 40.00, 38.00, 'USR-008-DEV-BE01', '2025-06-15 08:00:00'),

('TSK-001-VM-008', 'PRJ-001-VINMART', 'Category Management API', 'API quáº£n lÃ½ danh má»¥c sáº£n pháº©m: tree structure, nested categories', 'done', 'medium', 8, '2025-06-25', '2025-07-10', '2025-07-08 15:30:00', 24.00, 22.00, 'USR-009-DEV-BE02', '2025-06-20 08:00:00'),

-- Sprint 2: Core Features (DONE - 7 tasks)
('TSK-001-VM-009', 'PRJ-001-VINMART', 'Product Listing Page', 'Trang danh sÃ¡ch sáº£n pháº©m vá»›i filter, sort, pagination, grid/list view', 'done', 'high', 9, '2025-07-01', '2025-07-20', '2025-07-18 17:00:00', 40.00, 45.00, 'USR-012-DEV-FE01', '2025-06-25 08:00:00'),

('TSK-001-VM-010', 'PRJ-001-VINMART', 'Product Detail Page', 'Trang chi tiáº¿t sáº£n pháº©m: gallery, variants, reviews, related products', 'done', 'high', 10, '2025-07-10', '2025-07-25', '2025-07-24 16:30:00', 32.00, 35.00, 'USR-013-DEV-FE02', '2025-07-05 08:00:00'),

('TSK-001-VM-011', 'PRJ-001-VINMART', 'Shopping Cart API', 'API giá» hÃ ng: add, update quantity, remove, apply coupon, calculate total', 'done', 'high', 11, '2025-07-15', '2025-07-30', '2025-07-29 17:30:00', 32.00, 30.00, 'USR-009-DEV-BE02', '2025-07-10 08:00:00'),

('TSK-001-VM-012', 'PRJ-001-VINMART', 'Shopping Cart UI', 'Giao diá»‡n giá» hÃ ng: mini cart, full cart page, quantity controls', 'done', 'high', 12, '2025-07-20', '2025-08-05', '2025-08-03 15:00:00', 24.00, 26.00, 'USR-012-DEV-FE01', '2025-07-15 08:00:00'),

('TSK-001-VM-013', 'PRJ-001-VINMART', 'User Profile Management', 'Quáº£n lÃ½ thÃ´ng tin cÃ¡ nhÃ¢n, Ä‘á»‹a chá»‰ giao hÃ ng, Ä‘á»•i máº­t kháº©u', 'done', 'medium', 13, '2025-07-25', '2025-08-10', '2025-08-08 16:00:00', 24.00, 22.00, 'USR-013-DEV-FE02', '2025-07-20 08:00:00'),

('TSK-001-VM-014', 'PRJ-001-VINMART', 'Order Management API', 'API Ä‘áº·t hÃ ng: create order, order status, order history', 'done', 'high', 14, '2025-08-01', '2025-08-20', '2025-08-18 17:00:00', 40.00, 42.00, 'USR-008-DEV-BE01', '2025-07-25 08:00:00'),

('TSK-001-VM-015', 'PRJ-001-VINMART', 'Inventory Management', 'Quáº£n lÃ½ tá»“n kho: stock tracking, low stock alerts, stock history', 'done', 'high', 15, '2025-08-05', '2025-08-25', '2025-08-23 16:30:00', 32.00, 35.00, 'USR-010-DEV-BE03', '2025-08-01 08:00:00'),

-- Sprint 3: Payment & Checkout (IN PROGRESS - 6 tasks)
('TSK-001-VM-016', 'PRJ-001-VINMART', 'Checkout Flow UI', 'Giao diá»‡n checkout: shipping info, payment method, order summary, confirmation', 'in_progress', 'high', 16, '2025-08-20', '2025-09-10', NULL, 40.00, 32.00, 'USR-012-DEV-FE01', '2025-08-15 08:00:00'),

('TSK-001-VM-017', 'PRJ-001-VINMART', 'VNPay Integration', 'TÃ­ch há»£p cá»•ng thanh toÃ¡n VNPay: payment, refund, webhook', 'in_progress', 'urgent', 17, '2025-08-25', '2025-09-15', NULL, 40.00, 28.00, 'USR-008-DEV-BE01', '2025-08-20 08:00:00'),

('TSK-001-VM-018', 'PRJ-001-VINMART', 'MoMo Integration', 'TÃ­ch há»£p vÃ­ MoMo: QR payment, wallet payment', 'in_progress', 'high', 18, '2025-09-01', '2025-09-20', NULL, 32.00, 15.00, 'USR-009-DEV-BE02', '2025-08-25 08:00:00'),

('TSK-001-VM-019', 'PRJ-001-VINMART', 'COD Payment Option', 'Thanh toÃ¡n khi nháº­n hÃ ng: validation, order confirmation', 'in_review', 'medium', 19, '2025-09-05', '2025-09-15', NULL, 16.00, 14.00, 'USR-010-DEV-BE03', '2025-09-01 08:00:00'),

('TSK-001-VM-020', 'PRJ-001-VINMART', 'Order Confirmation Email', 'Email xÃ¡c nháº­n Ä‘Æ¡n hÃ ng vá»›i template Ä‘áº¹p, thÃ´ng tin chi tiáº¿t', 'in_progress', 'medium', 20, '2025-09-10', '2025-09-25', NULL, 16.00, 8.00, 'USR-009-DEV-BE02', '2025-09-05 08:00:00'),

('TSK-001-VM-021', 'PRJ-001-VINMART', 'Payment Testing', 'Test toÃ n bá»™ flow thanh toÃ¡n: success, failure, timeout, refund', 'todo', 'high', 21, '2025-09-20', '2025-10-05', NULL, 24.00, NULL, 'USR-019-QA01', '2025-09-15 08:00:00'),

-- Sprint 4: Advanced Features (TODO & BACKLOG - 14 tasks)
('TSK-001-VM-022', 'PRJ-001-VINMART', 'Search vá»›i Elasticsearch', 'TÃ¬m kiáº¿m sáº£n pháº©m nÃ¢ng cao vá»›i Elasticsearch: full-text, autocomplete, facets', 'todo', 'high', 22, '2025-10-01', '2025-10-20', NULL, 40.00, NULL, 'USR-008-DEV-BE01', '2025-09-20 08:00:00'),

('TSK-001-VM-023', 'PRJ-001-VINMART', 'Product Reviews & Ratings', 'Há»‡ thá»‘ng Ä‘Ã¡nh giÃ¡ sáº£n pháº©m: rating, review, photos, helpful votes', 'todo', 'medium', 23, '2025-10-10', '2025-10-30', NULL, 32.00, NULL, 'USR-010-DEV-BE03', '2025-09-25 08:00:00'),

('TSK-001-VM-024', 'PRJ-001-VINMART', 'Wishlist Feature', 'Danh sÃ¡ch yÃªu thÃ­ch: add, remove, share, move to cart', 'todo', 'low', 24, '2025-10-15', '2025-11-01', NULL, 16.00, NULL, 'USR-013-DEV-FE02', '2025-10-01 08:00:00'),

('TSK-001-VM-025', 'PRJ-001-VINMART', 'Coupon & Promotion System', 'Há»‡ thá»‘ng mÃ£ giáº£m giÃ¡: percentage, fixed amount, free shipping, conditions', 'backlog', 'medium', 25, NULL, NULL, NULL, 40.00, NULL, 'USR-009-DEV-BE02', '2025-10-05 08:00:00'),

('TSK-001-VM-026', 'PRJ-001-VINMART', 'Flash Sale Module', 'TÃ­nh nÄƒng flash sale: countdown, limited quantity, special price', 'backlog', 'medium', 26, NULL, NULL, NULL, 32.00, NULL, 'USR-012-DEV-FE01', '2025-10-10 08:00:00'),

('TSK-001-VM-027', 'PRJ-001-VINMART', 'Loyalty Points System', 'Há»‡ thá»‘ng tÃ­ch Ä‘iá»ƒm: earn points, redeem, tier levels', 'backlog', 'low', 27, NULL, NULL, NULL, 40.00, NULL, 'USR-008-DEV-BE01', '2025-10-15 08:00:00'),

('TSK-001-VM-028', 'PRJ-001-VINMART', 'Push Notifications', 'ThÃ´ng bÃ¡o Ä‘áº©y: order updates, promotions, price drops', 'backlog', 'medium', 28, NULL, NULL, NULL, 24.00, NULL, 'USR-009-DEV-BE02', '2025-10-20 08:00:00'),

('TSK-001-VM-029', 'PRJ-001-VINMART', 'Admin Dashboard', 'Dashboard quáº£n trá»‹: sales analytics, order management, user management', 'backlog', 'high', 29, NULL, NULL, NULL, 60.00, NULL, 'USR-012-DEV-FE01', '2025-10-25 08:00:00'),

('TSK-001-VM-030', 'PRJ-001-VINMART', 'Report & Analytics', 'BÃ¡o cÃ¡o: doanh thu, sáº£n pháº©m bÃ¡n cháº¡y, customer insights', 'backlog', 'medium', 30, NULL, NULL, NULL, 40.00, NULL, 'USR-010-DEV-BE03', '2025-11-01 08:00:00'),

('TSK-001-VM-031', 'PRJ-001-VINMART', 'Multi-language Support', 'Há»— trá»£ Ä‘a ngÃ´n ngá»¯: Vietnamese, English', 'backlog', 'low', 31, NULL, NULL, NULL, 24.00, NULL, 'USR-013-DEV-FE02', '2025-11-05 08:00:00'),

('TSK-001-VM-032', 'PRJ-001-VINMART', 'SEO Optimization', 'Tá»‘i Æ°u SEO: meta tags, sitemap, structured data, performance', 'backlog', 'medium', 32, NULL, NULL, NULL, 24.00, NULL, 'USR-012-DEV-FE01', '2025-11-10 08:00:00'),

('TSK-001-VM-033', 'PRJ-001-VINMART', 'Performance Optimization', 'Tá»‘i Æ°u hiá»‡u nÄƒng: caching, lazy loading, image optimization', 'backlog', 'high', 33, NULL, NULL, NULL, 32.00, NULL, 'USR-008-DEV-BE01', '2025-11-15 08:00:00'),

('TSK-001-VM-034', 'PRJ-001-VINMART', 'Security Audit', 'Kiá»ƒm tra báº£o máº­t: penetration testing, vulnerability scan', 'backlog', 'urgent', 34, NULL, NULL, NULL, 40.00, NULL, 'USR-019-QA01', '2025-11-20 08:00:00'),

('TSK-001-VM-035', 'PRJ-001-VINMART', 'UAT & Go-live Preparation', 'Chuáº©n bá»‹ UAT vÃ  go-live: test cases, deployment checklist', 'backlog', 'high', 35, NULL, NULL, NULL, 40.00, NULL, 'USR-003-MGR-PM01', '2025-11-25 08:00:00');


-- ===== PRJ-002: FPT Mobile Banking - 30 tasks =====

INSERT INTO `tasks` (`id`, `project_id`, `title`, `description`, `status`, `priority`, `position`, `start_date`, `due_date`, `completed_at`, `estimated_hours`, `actual_hours`, `created_by`, `created_at`) VALUES

-- Phase 1: Security Foundation (DONE - 6 tasks)
('TSK-002-FB-001', 'PRJ-002-FPTBANK', 'Security Architecture Design', 'Thiáº¿t káº¿ kiáº¿n trÃºc báº£o máº­t theo chuáº©n PCI-DSS, encryption, secure communication', 'done', 'urgent', 1, '2025-08-01', '2025-08-20', '2025-08-18 17:00:00', 48.00, 52.00, 'USR-005-MGR-TECH', '2025-07-25 08:00:00'),

('TSK-002-FB-002', 'PRJ-002-FPTBANK', 'Biometric Authentication', 'XÃ¡c thá»±c sinh tráº¯c há»c: fingerprint, face ID cho iOS vÃ  Android', 'done', 'urgent', 2, '2025-08-15', '2025-09-05', '2025-09-03 16:30:00', 40.00, 45.00, 'USR-015-DEV-MOB1', '2025-08-10 08:00:00'),

('TSK-002-FB-003', 'PRJ-002-FPTBANK', 'PIN & OTP System', 'Há»‡ thá»‘ng PIN vÃ  OTP: generate, validate, rate limiting, expiry', 'done', 'urgent', 3, '2025-08-20', '2025-09-10', '2025-09-08 17:00:00', 32.00, 30.00, 'USR-008-DEV-BE01', '2025-08-15 08:00:00'),

('TSK-002-FB-004', 'PRJ-002-FPTBANK', 'Secure API Gateway', 'API Gateway vá»›i rate limiting, request validation, logging', 'done', 'high', 4, '2025-08-25', '2025-09-15', '2025-09-12 15:30:00', 40.00, 38.00, 'USR-008-DEV-BE01', '2025-08-20 08:00:00'),

('TSK-002-FB-005', 'PRJ-002-FPTBANK', 'Device Binding', 'LiÃªn káº¿t thiáº¿t bá»‹: device fingerprint, trusted devices management', 'done', 'high', 5, '2025-09-01', '2025-09-20', '2025-09-18 16:00:00', 24.00, 26.00, 'USR-016-DEV-MOB2', '2025-08-25 08:00:00'),

('TSK-002-FB-006', 'PRJ-002-FPTBANK', 'UI/UX Design Mobile App', 'Thiáº¿t káº¿ giao diá»‡n app theo brand guidelines cá»§a FPT Bank', 'done', 'high', 6, '2025-08-01', '2025-08-25', '2025-08-23 17:30:00', 48.00, 50.00, 'USR-017-DESIGN01', '2025-07-28 08:00:00'),

-- Phase 2: Core Banking (IN PROGRESS - 8 tasks)
('TSK-002-FB-007', 'PRJ-002-FPTBANK', 'Account Overview Screen', 'MÃ n hÃ¬nh tá»•ng quan tÃ i khoáº£n: balance, recent transactions, quick actions', 'done', 'high', 7, '2025-09-15', '2025-10-01', '2025-09-30 17:00:00', 32.00, 30.00, 'USR-015-DEV-MOB1', '2025-09-10 08:00:00'),

('TSK-002-FB-008', 'PRJ-002-FPTBANK', 'Transaction History', 'Lá»‹ch sá»­ giao dá»‹ch: filter, search, export, transaction details', 'done', 'medium', 8, '2025-09-20', '2025-10-10', '2025-10-08 16:30:00', 24.00, 22.00, 'USR-016-DEV-MOB2', '2025-09-15 08:00:00'),

('TSK-002-FB-009', 'PRJ-002-FPTBANK', 'Internal Transfer', 'Chuyá»ƒn tiá»n ná»™i bá»™: same bank, instant transfer, confirmation', 'in_progress', 'urgent', 9, '2025-10-01', '2025-10-20', NULL, 40.00, 30.00, 'USR-008-DEV-BE01', '2025-09-25 08:00:00'),

('TSK-002-FB-010', 'PRJ-002-FPTBANK', 'Interbank Transfer (Napas)', 'Chuyá»ƒn tiá»n liÃªn ngÃ¢n hÃ ng qua Napas: 24/7, instant', 'in_progress', 'urgent', 10, '2025-10-10', '2025-11-01', NULL, 48.00, 20.00, 'USR-008-DEV-BE01', '2025-10-05 08:00:00'),

('TSK-002-FB-011', 'PRJ-002-FPTBANK', 'Transfer UI Implementation', 'Giao diá»‡n chuyá»ƒn tiá»n: beneficiary selection, amount input, confirmation', 'in_progress', 'high', 11, '2025-10-15', '2025-11-05', NULL, 32.00, 18.00, 'USR-015-DEV-MOB1', '2025-10-10 08:00:00'),

('TSK-002-FB-012', 'PRJ-002-FPTBANK', 'Beneficiary Management', 'Quáº£n lÃ½ ngÆ°á»i thá»¥ hÆ°á»Ÿng: add, edit, delete, favorites', 'in_progress', 'medium', 12, '2025-10-20', '2025-11-10', NULL, 24.00, 10.00, 'USR-016-DEV-MOB2', '2025-10-15 08:00:00'),

('TSK-002-FB-013', 'PRJ-002-FPTBANK', 'Transaction Limits', 'Háº¡n má»©c giao dá»‹ch: daily, per transaction, cumulative', 'in_review', 'high', 13, '2025-10-25', '2025-11-10', NULL, 16.00, 15.00, 'USR-011-DEV-BE04', '2025-10-20 08:00:00'),

('TSK-002-FB-014', 'PRJ-002-FPTBANK', 'Transfer Notifications', 'ThÃ´ng bÃ¡o giao dá»‹ch: push, SMS, email', 'todo', 'medium', 14, '2025-11-01', '2025-11-15', NULL, 16.00, NULL, 'USR-011-DEV-BE04', '2025-10-25 08:00:00'),

-- Phase 3: Bill Payment (TODO - 6 tasks)
('TSK-002-FB-015', 'PRJ-002-FPTBANK', 'Bill Payment Integration', 'TÃ­ch há»£p thanh toÃ¡n hÃ³a Ä‘Æ¡n: Ä‘iá»‡n, nÆ°á»›c, internet, Ä‘iá»‡n thoáº¡i', 'todo', 'high', 15, '2025-11-10', '2025-12-01', NULL, 48.00, NULL, 'USR-008-DEV-BE01', '2025-11-01 08:00:00'),

('TSK-002-FB-016', 'PRJ-002-FPTBANK', 'Bill Payment UI', 'Giao diá»‡n thanh toÃ¡n hÃ³a Ä‘Æ¡n: provider selection, bill lookup, payment', 'todo', 'high', 16, '2025-11-15', '2025-12-05', NULL, 32.00, NULL, 'USR-015-DEV-MOB1', '2025-11-05 08:00:00'),

('TSK-002-FB-017', 'PRJ-002-FPTBANK', 'Auto Bill Payment', 'Thanh toÃ¡n tá»± Ä‘á»™ng: schedule, recurring, reminders', 'todo', 'medium', 17, '2025-11-20', '2025-12-10', NULL, 24.00, NULL, 'USR-016-DEV-MOB2', '2025-11-10 08:00:00'),

('TSK-002-FB-018', 'PRJ-002-FPTBANK', 'Mobile Top-up', 'Náº¡p tiá»n Ä‘iá»‡n thoáº¡i: all carriers, data packages', 'todo', 'medium', 18, '2025-11-25', '2025-12-15', NULL, 24.00, NULL, 'USR-011-DEV-BE04', '2025-11-15 08:00:00'),

('TSK-002-FB-019', 'PRJ-002-FPTBANK', 'QR Payment', 'Thanh toÃ¡n QR: VietQR, merchant QR, dynamic QR', 'todo', 'high', 19, '2025-12-01', '2025-12-20', NULL, 40.00, NULL, 'USR-008-DEV-BE01', '2025-11-20 08:00:00'),

('TSK-002-FB-020', 'PRJ-002-FPTBANK', 'Payment History & Receipts', 'Lá»‹ch sá»­ thanh toÃ¡n vÃ  biÃªn lai Ä‘iá»‡n tá»­', 'todo', 'low', 20, '2025-12-05', '2025-12-25', NULL, 16.00, NULL, 'USR-016-DEV-MOB2', '2025-11-25 08:00:00'),

-- Phase 4: Savings & Loans (BACKLOG - 10 tasks)
('TSK-002-FB-021', 'PRJ-002-FPTBANK', 'Savings Account Opening', 'Má»Ÿ tÃ i khoáº£n tiáº¿t kiá»‡m online: terms, interest rates', 'backlog', 'medium', 21, NULL, NULL, NULL, 32.00, NULL, 'USR-008-DEV-BE01', '2025-12-01 08:00:00'),

('TSK-002-FB-022', 'PRJ-002-FPTBANK', 'Savings Management', 'Quáº£n lÃ½ sá»• tiáº¿t kiá»‡m: view, withdraw, rollover', 'backlog', 'medium', 22, NULL, NULL, NULL, 24.00, NULL, 'USR-015-DEV-MOB1', '2025-12-05 08:00:00'),

('TSK-002-FB-023', 'PRJ-002-FPTBANK', 'Loan Application', 'ÄÄƒng kÃ½ vay online: personal loan, credit card', 'backlog', 'high', 23, NULL, NULL, NULL, 48.00, NULL, 'USR-008-DEV-BE01', '2025-12-10 08:00:00'),

('TSK-002-FB-024', 'PRJ-002-FPTBANK', 'Loan Calculator', 'CÃ´ng cá»¥ tÃ­nh lÃ£i vay: EMI calculator, comparison', 'backlog', 'low', 24, NULL, NULL, NULL, 16.00, NULL, 'USR-016-DEV-MOB2', '2025-12-15 08:00:00'),

('TSK-002-FB-025', 'PRJ-002-FPTBANK', 'Credit Card Management', 'Quáº£n lÃ½ tháº» tÃ­n dá»¥ng: view, block, limit, statements', 'backlog', 'medium', 25, NULL, NULL, NULL, 32.00, NULL, 'USR-015-DEV-MOB1', '2025-12-20 08:00:00'),

('TSK-002-FB-026', 'PRJ-002-FPTBANK', 'Investment Products', 'Sáº£n pháº©m Ä‘áº§u tÆ°: funds, bonds, gold', 'backlog', 'low', 26, NULL, NULL, NULL, 40.00, NULL, 'USR-008-DEV-BE01', '2025-12-25 08:00:00'),

('TSK-002-FB-027', 'PRJ-002-FPTBANK', 'Insurance Products', 'Sáº£n pháº©m báº£o hiá»ƒm: life, health, travel', 'backlog', 'low', 27, NULL, NULL, NULL, 32.00, NULL, 'USR-011-DEV-BE04', '2026-01-01 08:00:00'),

('TSK-002-FB-028', 'PRJ-002-FPTBANK', 'Customer Support Chat', 'Chat há»— trá»£ khÃ¡ch hÃ ng trong app', 'backlog', 'medium', 28, NULL, NULL, NULL, 24.00, NULL, 'USR-016-DEV-MOB2', '2026-01-05 08:00:00'),

('TSK-002-FB-029', 'PRJ-002-FPTBANK', 'App Analytics', 'TÃ­ch há»£p analytics: Firebase, Mixpanel', 'backlog', 'low', 29, NULL, NULL, NULL, 16.00, NULL, 'USR-015-DEV-MOB1', '2026-01-10 08:00:00'),

('TSK-002-FB-030', 'PRJ-002-FPTBANK', 'Security Penetration Testing', 'Kiá»ƒm tra xÃ¢m nháº­p báº£o máº­t theo chuáº©n OWASP', 'backlog', 'urgent', 30, NULL, NULL, NULL, 48.00, NULL, 'USR-019-QA01', '2026-01-15 08:00:00');


-- ===== PRJ-003: HRMS Internal - 20 tasks =====

INSERT INTO `tasks` (`id`, `project_id`, `title`, `description`, `status`, `priority`, `position`, `start_date`, `due_date`, `completed_at`, `estimated_hours`, `actual_hours`, `created_by`, `created_at`) VALUES

-- Module 1: Employee Management (DONE - 6 tasks)
('TSK-003-HR-001', 'PRJ-003-HRMS', 'Employee Database Design', 'Thiáº¿t káº¿ database nhÃ¢n viÃªn: thÃ´ng tin cÃ¡ nhÃ¢n, há»£p Ä‘á»“ng, phÃ²ng ban', 'done', 'high', 1, '2025-03-01', '2025-03-15', '2025-03-14 17:00:00', 24.00, 22.00, 'USR-003-MGR-PM01', '2025-02-20 08:00:00'),

('TSK-003-HR-002', 'PRJ-003-HRMS', 'Employee CRUD API', 'API quáº£n lÃ½ nhÃ¢n viÃªn: create, read, update, deactivate', 'done', 'high', 2, '2025-03-10', '2025-03-25', '2025-03-24 16:30:00', 32.00, 30.00, 'USR-009-DEV-BE02', '2025-03-05 08:00:00'),

('TSK-003-HR-003', 'PRJ-003-HRMS', 'Employee List & Profile UI', 'Giao diá»‡n danh sÃ¡ch vÃ  há»“ sÆ¡ nhÃ¢n viÃªn', 'done', 'high', 3, '2025-03-20', '2025-04-05', '2025-04-03 17:00:00', 32.00, 35.00, 'USR-012-DEV-FE01', '2025-03-15 08:00:00'),

('TSK-003-HR-004', 'PRJ-003-HRMS', 'Organization Chart', 'SÆ¡ Ä‘á»“ tá»• chá»©c: departments, positions, reporting lines', 'done', 'medium', 4, '2025-04-01', '2025-04-15', '2025-04-14 15:30:00', 24.00, 26.00, 'USR-014-DEV-FE03', '2025-03-25 08:00:00'),

('TSK-003-HR-005', 'PRJ-003-HRMS', 'Employee Import/Export', 'Import/Export nhÃ¢n viÃªn tá»« Excel', 'done', 'medium', 5, '2025-04-10', '2025-04-25', '2025-04-23 16:00:00', 16.00, 18.00, 'USR-009-DEV-BE02', '2025-04-05 08:00:00'),

('TSK-003-HR-006', 'PRJ-003-HRMS', 'Employee Search & Filter', 'TÃ¬m kiáº¿m vÃ  lá»c nhÃ¢n viÃªn theo nhiá»u tiÃªu chÃ­', 'done', 'medium', 6, '2025-04-20', '2025-05-05', '2025-05-03 17:30:00', 16.00, 14.00, 'USR-012-DEV-FE01', '2025-04-15 08:00:00'),

-- Module 2: Attendance & Leave (DONE - 5 tasks)
('TSK-003-HR-007', 'PRJ-003-HRMS', 'Attendance System Integration', 'TÃ­ch há»£p mÃ¡y cháº¥m cÃ´ng: import data, sync realtime', 'done', 'high', 7, '2025-05-01', '2025-05-20', '2025-05-18 17:00:00', 40.00, 42.00, 'USR-009-DEV-BE02', '2025-04-25 08:00:00'),

('TSK-003-HR-008', 'PRJ-003-HRMS', 'Leave Request System', 'Há»‡ thá»‘ng xin nghá»‰ phÃ©p: request, approval workflow, balance', 'done', 'high', 8, '2025-05-15', '2025-06-01', '2025-05-30 16:30:00', 32.00, 35.00, 'USR-009-DEV-BE02', '2025-05-10 08:00:00'),

('TSK-003-HR-009', 'PRJ-003-HRMS', 'Attendance Report', 'BÃ¡o cÃ¡o cháº¥m cÃ´ng: daily, monthly, summary', 'done', 'medium', 9, '2025-06-01', '2025-06-15', '2025-06-13 15:00:00', 24.00, 22.00, 'USR-014-DEV-FE03', '2025-05-25 08:00:00'),

('TSK-003-HR-010', 'PRJ-003-HRMS', 'Leave Calendar', 'Lá»‹ch nghá»‰ phÃ©p: team view, department view', 'done', 'medium', 10, '2025-06-10', '2025-06-25', '2025-06-24 17:00:00', 16.00, 18.00, 'USR-012-DEV-FE01', '2025-06-05 08:00:00'),

('TSK-003-HR-011', 'PRJ-003-HRMS', 'Overtime Management', 'Quáº£n lÃ½ lÃ m thÃªm giá»: request, approval, calculation', 'done', 'medium', 11, '2025-06-20', '2025-07-05', '2025-07-03 16:30:00', 24.00, 26.00, 'USR-009-DEV-BE02', '2025-06-15 08:00:00'),

-- Module 3: Payroll (IN PROGRESS - 4 tasks)
('TSK-003-HR-012', 'PRJ-003-HRMS', 'Salary Structure Setup', 'Cáº¥u trÃºc lÆ°Æ¡ng: basic, allowances, deductions, tax', 'done', 'high', 12, '2025-07-01', '2025-07-20', '2025-07-18 17:00:00', 32.00, 30.00, 'USR-009-DEV-BE02', '2025-06-25 08:00:00'),

('TSK-003-HR-013', 'PRJ-003-HRMS', 'Payroll Calculation Engine', 'Engine tÃ­nh lÆ°Æ¡ng: gross, net, tax, insurance', 'in_progress', 'urgent', 13, '2025-07-15', '2025-08-05', NULL, 48.00, 40.00, 'USR-009-DEV-BE02', '2025-07-10 08:00:00'),

('TSK-003-HR-014', 'PRJ-003-HRMS', 'Payslip Generation', 'Táº¡o phiáº¿u lÆ°Æ¡ng: PDF, email distribution', 'in_progress', 'high', 14, '2025-08-01', '2025-08-20', NULL, 24.00, 15.00, 'USR-014-DEV-FE03', '2025-07-25 08:00:00'),

('TSK-003-HR-015', 'PRJ-003-HRMS', 'Tax Report (PIT)', 'BÃ¡o cÃ¡o thuáº¿ TNCN: monthly, yearly, export', 'todo', 'high', 15, '2025-08-15', '2025-09-01', NULL, 24.00, NULL, 'USR-009-DEV-BE02', '2025-08-10 08:00:00'),

-- Module 4: KPI & Training (TODO & BACKLOG - 5 tasks)
('TSK-003-HR-016', 'PRJ-003-HRMS', 'KPI Setup & Evaluation', 'Thiáº¿t láº­p vÃ  Ä‘Ã¡nh giÃ¡ KPI: goals, metrics, scoring', 'todo', 'medium', 16, '2025-09-01', '2025-09-20', NULL, 32.00, NULL, 'USR-009-DEV-BE02', '2025-08-20 08:00:00'),

('TSK-003-HR-017', 'PRJ-003-HRMS', 'Performance Review Workflow', 'Quy trÃ¬nh Ä‘Ã¡nh giÃ¡: self-review, manager review, 360 feedback', 'todo', 'medium', 17, '2025-09-15', '2025-10-05', NULL, 32.00, NULL, 'USR-012-DEV-FE01', '2025-09-01 08:00:00'),

('TSK-003-HR-018', 'PRJ-003-HRMS', 'Training Management', 'Quáº£n lÃ½ Ä‘Ã o táº¡o: courses, enrollment, certificates', 'backlog', 'low', 18, NULL, NULL, NULL, 40.00, NULL, 'USR-014-DEV-FE03', '2025-09-10 08:00:00'),

('TSK-003-HR-019', 'PRJ-003-HRMS', 'Recruitment Module', 'Module tuyá»ƒn dá»¥ng: job posting, applications, interviews', 'backlog', 'medium', 19, NULL, NULL, NULL, 48.00, NULL, 'USR-009-DEV-BE02', '2025-09-15 08:00:00'),

('TSK-003-HR-020', 'PRJ-003-HRMS', 'Employee Self-Service Portal', 'Portal tá»± phá»¥c vá»¥: view payslip, request leave, update info', 'backlog', 'medium', 20, NULL, NULL, NULL, 32.00, NULL, 'USR-012-DEV-FE01', '2025-09-20 08:00:00');


-- ===== PRJ-004: MedCare Healthcare - 15 tasks =====

INSERT INTO `tasks` (`id`, `project_id`, `title`, `description`, `status`, `priority`, `position`, `start_date`, `due_date`, `completed_at`, `estimated_hours`, `actual_hours`, `created_by`, `created_at`) VALUES

-- Phase 1: Foundation (IN PROGRESS - 5 tasks)
('TSK-004-MC-001', 'PRJ-004-MEDCARE', 'Healthcare System Architecture', 'Thiáº¿t káº¿ kiáº¿n trÃºc há»‡ thá»‘ng y táº¿: HL7 FHIR, security, scalability', 'done', 'urgent', 1, '2025-10-01', '2025-10-20', '2025-10-18 17:00:00', 48.00, 50.00, 'USR-005-MGR-TECH', '2025-09-20 08:00:00'),

('TSK-004-MC-002', 'PRJ-004-MEDCARE', 'Patient Registration Module', 'Module Ä‘Äƒng kÃ½ bá»‡nh nhÃ¢n: demographics, insurance, medical history', 'in_progress', 'high', 2, '2025-10-15', '2025-11-05', NULL, 40.00, 25.00, 'USR-010-DEV-BE03', '2025-10-10 08:00:00'),

('TSK-004-MC-003', 'PRJ-004-MEDCARE', 'Appointment Booking System', 'Há»‡ thá»‘ng Ä‘áº·t lá»‹ch khÃ¡m: doctor availability, time slots, reminders', 'in_progress', 'high', 3, '2025-10-20', '2025-11-10', NULL, 40.00, 18.00, 'USR-011-DEV-BE04', '2025-10-15 08:00:00'),

('TSK-004-MC-004', 'PRJ-004-MEDCARE', 'Doctor Portal UI', 'Giao diá»‡n bÃ¡c sÄ©: patient list, appointments, medical records', 'in_progress', 'high', 4, '2025-10-25', '2025-11-15', NULL, 48.00, 20.00, 'USR-013-DEV-FE02', '2025-10-20 08:00:00'),

('TSK-004-MC-005', 'PRJ-004-MEDCARE', 'Patient Mobile App Design', 'Thiáº¿t káº¿ app bá»‡nh nhÃ¢n: booking, records, prescriptions', 'in_review', 'high', 5, '2025-10-10', '2025-10-30', NULL, 40.00, 38.00, 'USR-017-DESIGN01', '2025-10-05 08:00:00'),

-- Phase 2: Core Features (TODO - 5 tasks)
('TSK-004-MC-006', 'PRJ-004-MEDCARE', 'Electronic Medical Records', 'Há»“ sÆ¡ bá»‡nh Ã¡n Ä‘iá»‡n tá»­: diagnosis, prescriptions, lab results', 'todo', 'urgent', 6, '2025-11-10', '2025-12-01', NULL, 60.00, NULL, 'USR-010-DEV-BE03', '2025-11-01 08:00:00'),

('TSK-004-MC-007', 'PRJ-004-MEDCARE', 'Prescription Management', 'Quáº£n lÃ½ Ä‘Æ¡n thuá»‘c: create, print, pharmacy integration', 'todo', 'high', 7, '2025-11-15', '2025-12-05', NULL, 32.00, NULL, 'USR-011-DEV-BE04', '2025-11-05 08:00:00'),

('TSK-004-MC-008', 'PRJ-004-MEDCARE', 'Lab Results Integration', 'TÃ­ch há»£p káº¿t quáº£ xÃ©t nghiá»‡m: import, display, alerts', 'todo', 'high', 8, '2025-11-20', '2025-12-10', NULL, 40.00, NULL, 'USR-010-DEV-BE03', '2025-11-10 08:00:00'),

('TSK-004-MC-009', 'PRJ-004-MEDCARE', 'Telemedicine Video Call', 'TÆ° váº¥n video call: WebRTC, recording, screen share', 'todo', 'medium', 9, '2025-12-01', '2025-12-20', NULL, 48.00, NULL, 'USR-013-DEV-FE02', '2025-11-20 08:00:00'),

('TSK-004-MC-010', 'PRJ-004-MEDCARE', 'Insurance Claim Processing', 'Xá»­ lÃ½ báº£o hiá»ƒm: claim submission, status tracking', 'todo', 'high', 10, '2025-12-05', '2025-12-25', NULL, 40.00, NULL, 'USR-011-DEV-BE04', '2025-11-25 08:00:00'),

-- Phase 3: Advanced (BACKLOG - 5 tasks)
('TSK-004-MC-011', 'PRJ-004-MEDCARE', 'Health Monitoring Dashboard', 'Dashboard theo dÃµi sá»©c khá»e: vitals, trends, alerts', 'backlog', 'medium', 11, NULL, NULL, NULL, 32.00, NULL, 'USR-014-DEV-FE03', '2025-12-01 08:00:00'),

('TSK-004-MC-012', 'PRJ-004-MEDCARE', 'Medication Reminders', 'Nháº¯c nhá»Ÿ uá»‘ng thuá»‘c: push notifications, schedule', 'backlog', 'low', 12, NULL, NULL, NULL, 24.00, NULL, 'USR-013-DEV-FE02', '2025-12-05 08:00:00'),

('TSK-004-MC-013', 'PRJ-004-MEDCARE', 'Hospital Bed Management', 'Quáº£n lÃ½ giÆ°á»ng bá»‡nh: availability, assignment, discharge', 'backlog', 'medium', 13, NULL, NULL, NULL, 32.00, NULL, 'USR-010-DEV-BE03', '2025-12-10 08:00:00'),

('TSK-004-MC-014', 'PRJ-004-MEDCARE', 'Medical Imaging Integration', 'TÃ­ch há»£p hÃ¬nh áº£nh y khoa: DICOM viewer, PACS', 'backlog', 'low', 14, NULL, NULL, NULL, 48.00, NULL, 'USR-011-DEV-BE04', '2025-12-15 08:00:00'),

('TSK-004-MC-015', 'PRJ-004-MEDCARE', 'Analytics & Reporting', 'BÃ¡o cÃ¡o vÃ  phÃ¢n tÃ­ch: patient statistics, revenue, utilization', 'backlog', 'medium', 15, NULL, NULL, NULL, 40.00, NULL, 'USR-022-BA', '2025-12-20 08:00:00');


-- ===== PRJ-005: SmartBot AI - 8 tasks (Planning) =====

INSERT INTO `tasks` (`id`, `project_id`, `title`, `description`, `status`, `priority`, `position`, `start_date`, `due_date`, `completed_at`, `estimated_hours`, `actual_hours`, `created_by`, `created_at`) VALUES

('TSK-005-SB-001', 'PRJ-005-SMARTBOT', 'AI/ML Technology Research', 'NghiÃªn cá»©u cÃ´ng nghá»‡: NLP models, LLM options, Vietnamese language support', 'in_progress', 'high', 1, '2025-12-15', '2026-01-15', NULL, 40.00, 20.00, 'USR-008-DEV-BE01', '2025-12-10 08:00:00'),

('TSK-005-SB-002', 'PRJ-005-SMARTBOT', 'Chatbot Architecture Design', 'Thiáº¿t káº¿ kiáº¿n trÃºc chatbot: intent recognition, entity extraction, dialog management', 'todo', 'high', 2, '2026-01-15', '2026-02-01', NULL, 32.00, NULL, 'USR-005-MGR-TECH', '2025-12-15 08:00:00'),

('TSK-005-SB-003', 'PRJ-005-SMARTBOT', 'Training Data Collection', 'Thu tháº­p dá»¯ liá»‡u training: FAQs, conversations, domain knowledge', 'todo', 'medium', 3, '2026-01-20', '2026-02-10', NULL, 40.00, NULL, 'USR-022-BA', '2025-12-20 08:00:00'),

('TSK-005-SB-004', 'PRJ-005-SMARTBOT', 'NLP Model Development', 'PhÃ¡t triá»ƒn model NLP: intent classification, NER, sentiment', 'backlog', 'high', 4, NULL, NULL, NULL, 80.00, NULL, 'USR-008-DEV-BE01', '2025-12-25 08:00:00'),

('TSK-005-SB-005', 'PRJ-005-SMARTBOT', 'Conversation Flow Builder', 'Tool xÃ¢y dá»±ng flow há»™i thoáº¡i: visual editor, conditions, actions', 'backlog', 'medium', 5, NULL, NULL, NULL, 48.00, NULL, 'USR-008-DEV-BE01', '2026-01-01 08:00:00'),

('TSK-005-SB-006', 'PRJ-005-SMARTBOT', 'Chat Widget Development', 'Widget chat embed: web, mobile, customization', 'backlog', 'medium', 6, NULL, NULL, NULL, 32.00, NULL, 'USR-012-DEV-FE01', '2026-01-05 08:00:00'),

('TSK-005-SB-007', 'PRJ-005-SMARTBOT', 'CRM Integration', 'TÃ­ch há»£p CRM: Salesforce, HubSpot, custom CRM', 'backlog', 'low', 7, NULL, NULL, NULL, 40.00, NULL, 'USR-008-DEV-BE01', '2026-01-10 08:00:00'),

('TSK-005-SB-008', 'PRJ-005-SMARTBOT', 'Analytics Dashboard', 'Dashboard phÃ¢n tÃ­ch: conversations, satisfaction, common queries', 'backlog', 'low', 8, NULL, NULL, NULL, 32.00, NULL, 'USR-012-DEV-FE01', '2026-01-15 08:00:00');

-- ===== PRJ-006: LogiTrack - 5 tasks (Planning - chá»‰ cÃ³ PM) =====

INSERT INTO `tasks` (`id`, `project_id`, `title`, `description`, `status`, `priority`, `position`, `start_date`, `due_date`, `completed_at`, `estimated_hours`, `actual_hours`, `created_by`, `created_at`) VALUES

('TSK-006-LT-001', 'PRJ-006-LOGISTICS', 'Requirements Gathering', 'Thu tháº­p yÃªu cáº§u: stakeholder interviews, process mapping', 'todo', 'high', 1, '2026-02-01', '2026-02-15', NULL, 40.00, NULL, 'USR-003-MGR-PM01', '2026-01-05 08:00:00'),

('TSK-006-LT-002', 'PRJ-006-LOGISTICS', 'System Design Document', 'TÃ i liá»‡u thiáº¿t káº¿ há»‡ thá»‘ng: architecture, data flow, integrations', 'backlog', 'high', 2, NULL, NULL, NULL, 48.00, NULL, 'USR-003-MGR-PM01', '2026-01-06 08:00:00'),

('TSK-006-LT-003', 'PRJ-006-LOGISTICS', 'Vendor Evaluation', 'ÄÃ¡nh giÃ¡ vendors: GPS providers, IoT platforms, mapping APIs', 'backlog', 'medium', 3, NULL, NULL, NULL, 24.00, NULL, 'USR-003-MGR-PM01', '2026-01-07 08:00:00'),

('TSK-006-LT-004', 'PRJ-006-LOGISTICS', 'Project Timeline & Budget', 'Láº­p timeline vÃ  ngÃ¢n sÃ¡ch chi tiáº¿t', 'backlog', 'high', 4, NULL, NULL, NULL, 16.00, NULL, 'USR-003-MGR-PM01', '2026-01-08 08:00:00'),

('TSK-006-LT-005', 'PRJ-006-LOGISTICS', 'Team Allocation Plan', 'Káº¿ hoáº¡ch phÃ¢n bá»• nhÃ¢n sá»± cho dá»± Ã¡n', 'backlog', 'medium', 5, NULL, NULL, NULL, 8.00, NULL, 'USR-003-MGR-PM01', '2026-01-08 08:00:00');

-- ===== PRJ-007: DevOps Infrastructure - 12 tasks (Completed) =====

INSERT INTO `tasks` (`id`, `project_id`, `title`, `description`, `status`, `priority`, `position`, `start_date`, `due_date`, `completed_at`, `estimated_hours`, `actual_hours`, `created_by`, `created_at`) VALUES

('TSK-007-DO-001', 'PRJ-007-DEVOPS', 'Docker Infrastructure Setup', 'Setup Docker environment: images, compose, registry', 'done', 'high', 1, '2025-01-01', '2025-01-15', '2025-01-14 17:00:00', 24.00, 22.00, 'USR-021-DEVOPS', '2024-12-20 08:00:00'),

('TSK-007-DO-002', 'PRJ-007-DEVOPS', 'Kubernetes Cluster Setup', 'Setup K8s cluster: nodes, networking, storage', 'done', 'high', 2, '2025-01-10', '2025-02-01', '2025-01-30 16:30:00', 40.00, 45.00, 'USR-021-DEVOPS', '2025-01-05 08:00:00'),

('TSK-007-DO-003', 'PRJ-007-DEVOPS', 'CI/CD Pipeline (GitLab)', 'Setup GitLab CI/CD: build, test, deploy stages', 'done', 'high', 3, '2025-01-20', '2025-02-10', '2025-02-08 17:00:00', 32.00, 35.00, 'USR-021-DEVOPS', '2025-01-15 08:00:00'),

('TSK-007-DO-004', 'PRJ-007-DEVOPS', 'Monitoring Stack (Prometheus/Grafana)', 'Setup monitoring: metrics, dashboards, alerts', 'done', 'high', 4, '2025-02-01', '2025-02-20', '2025-02-18 15:30:00', 32.00, 30.00, 'USR-021-DEVOPS', '2025-01-25 08:00:00'),

('TSK-007-DO-005', 'PRJ-007-DEVOPS', 'Logging Stack (ELK)', 'Setup centralized logging: Elasticsearch, Logstash, Kibana', 'done', 'medium', 5, '2025-02-10', '2025-03-01', '2025-02-28 16:00:00', 32.00, 34.00, 'USR-021-DEVOPS', '2025-02-05 08:00:00'),

('TSK-007-DO-006', 'PRJ-007-DEVOPS', 'Secret Management (Vault)', 'Setup HashiCorp Vault: secrets, encryption, access control', 'done', 'high', 6, '2025-02-20', '2025-03-10', '2025-03-08 17:00:00', 24.00, 26.00, 'USR-021-DEVOPS', '2025-02-15 08:00:00'),

('TSK-007-DO-007', 'PRJ-007-DEVOPS', 'Auto-scaling Configuration', 'Configure HPA, VPA, cluster autoscaler', 'done', 'medium', 7, '2025-03-01', '2025-03-15', '2025-03-14 16:30:00', 16.00, 18.00, 'USR-021-DEVOPS', '2025-02-25 08:00:00'),

('TSK-007-DO-008', 'PRJ-007-DEVOPS', 'Backup & Disaster Recovery', 'Setup backup strategy: database, volumes, configs', 'done', 'high', 8, '2025-03-10', '2025-03-25', '2025-03-24 17:00:00', 24.00, 22.00, 'USR-021-DEVOPS', '2025-03-05 08:00:00'),

('TSK-007-DO-009', 'PRJ-007-DEVOPS', 'Security Hardening', 'Security: network policies, RBAC, pod security', 'done', 'urgent', 9, '2025-03-20', '2025-04-05', '2025-04-03 15:30:00', 32.00, 35.00, 'USR-008-DEV-BE01', '2025-03-15 08:00:00'),

('TSK-007-DO-010', 'PRJ-007-DEVOPS', 'Documentation', 'TÃ i liá»‡u: runbooks, architecture diagrams, procedures', 'done', 'medium', 10, '2025-04-01', '2025-04-20', '2025-04-18 16:00:00', 24.00, 26.00, 'USR-021-DEVOPS', '2025-03-25 08:00:00'),

('TSK-007-DO-011', 'PRJ-007-DEVOPS', 'Team Training', 'Training team: Docker, K8s, CI/CD usage', 'done', 'medium', 11, '2025-04-15', '2025-05-01', '2025-04-30 17:00:00', 16.00, 18.00, 'USR-021-DEVOPS', '2025-04-10 08:00:00'),

('TSK-007-DO-012', 'PRJ-007-DEVOPS', 'Production Migration', 'Migrate existing apps to new infrastructure', 'done', 'high', 12, '2025-05-01', '2025-06-15', '2025-06-14 17:30:00', 60.00, 65.00, 'USR-021-DEVOPS', '2025-04-25 08:00:00');


-- ===== PRJ-008: Website Redesign - 10 tasks (Completed) =====

INSERT INTO `tasks` (`id`, `project_id`, `title`, `description`, `status`, `priority`, `position`, `start_date`, `due_date`, `completed_at`, `estimated_hours`, `actual_hours`, `created_by`, `created_at`) VALUES

('TSK-008-WB-001', 'PRJ-008-WEBSITE', 'Brand Guidelines Review', 'Review vÃ  cáº­p nháº­t brand guidelines: colors, typography, imagery', 'done', 'high', 1, '2025-04-01', '2025-04-10', '2025-04-09 17:00:00', 16.00, 14.00, 'USR-007-MGR-DESIGN', '2025-03-25 08:00:00'),

('TSK-008-WB-002', 'PRJ-008-WEBSITE', 'Wireframe & Mockup', 'Thiáº¿t káº¿ wireframe vÃ  mockup cho táº¥t cáº£ trang', 'done', 'high', 2, '2025-04-08', '2025-04-25', '2025-04-24 16:30:00', 40.00, 42.00, 'USR-017-DESIGN01', '2025-04-03 08:00:00'),

('TSK-008-WB-003', 'PRJ-008-WEBSITE', 'UI Design - Homepage', 'Thiáº¿t káº¿ giao diá»‡n trang chá»§', 'done', 'high', 3, '2025-04-20', '2025-05-05', '2025-05-03 17:00:00', 24.00, 26.00, 'USR-017-DESIGN01', '2025-04-15 08:00:00'),

('TSK-008-WB-004', 'PRJ-008-WEBSITE', 'UI Design - Inner Pages', 'Thiáº¿t káº¿ cÃ¡c trang ná»™i dung: About, Services, Portfolio, Contact', 'done', 'high', 4, '2025-05-01', '2025-05-20', '2025-05-18 15:30:00', 32.00, 35.00, 'USR-018-DESIGN02', '2025-04-25 08:00:00'),

('TSK-008-WB-005', 'PRJ-008-WEBSITE', 'Frontend Development', 'PhÃ¡t triá»ƒn frontend: HTML, CSS, JavaScript, animations', 'done', 'high', 5, '2025-05-15', '2025-06-10', '2025-06-08 17:00:00', 48.00, 50.00, 'USR-012-DEV-FE01', '2025-05-10 08:00:00'),

('TSK-008-WB-006', 'PRJ-008-WEBSITE', 'CMS Integration', 'TÃ­ch há»£p CMS: blog, portfolio, team management', 'done', 'medium', 6, '2025-06-01', '2025-06-20', '2025-06-18 16:00:00', 32.00, 30.00, 'USR-012-DEV-FE01', '2025-05-25 08:00:00'),

('TSK-008-WB-007', 'PRJ-008-WEBSITE', 'SEO Optimization', 'Tá»‘i Æ°u SEO: meta tags, schema, sitemap, performance', 'done', 'medium', 7, '2025-06-15', '2025-07-01', '2025-06-30 17:30:00', 16.00, 18.00, 'USR-012-DEV-FE01', '2025-06-10 08:00:00'),

('TSK-008-WB-008', 'PRJ-008-WEBSITE', 'Responsive Testing', 'Test responsive trÃªn cÃ¡c thiáº¿t bá»‹ vÃ  trÃ¬nh duyá»‡t', 'done', 'high', 8, '2025-07-01', '2025-07-15', '2025-07-14 15:00:00', 16.00, 14.00, 'USR-018-DESIGN02', '2025-06-25 08:00:00'),

('TSK-008-WB-009', 'PRJ-008-WEBSITE', 'Content Migration', 'Chuyá»ƒn ná»™i dung tá»« website cÅ© sang má»›i', 'done', 'medium', 9, '2025-07-10', '2025-07-25', '2025-07-24 16:30:00', 16.00, 18.00, 'USR-017-DESIGN01', '2025-07-05 08:00:00'),

('TSK-008-WB-010', 'PRJ-008-WEBSITE', 'Launch & Monitoring', 'Go-live vÃ  theo dÃµi sau launch', 'done', 'high', 10, '2025-07-25', '2025-08-10', '2025-08-08 17:00:00', 16.00, 14.00, 'USR-007-MGR-DESIGN', '2025-07-20 08:00:00');

-- ===== PRJ-009: EduTech - 8 tasks (On Hold - cÃ³ nhÃ¢n viÃªn Ä‘Ã£ nghá»‰) =====

INSERT INTO `tasks` (`id`, `project_id`, `title`, `description`, `status`, `priority`, `position`, `start_date`, `due_date`, `completed_at`, `estimated_hours`, `actual_hours`, `created_by`, `created_at`) VALUES

('TSK-009-ED-001', 'PRJ-009-EDUTECH', 'Platform Architecture', 'Thiáº¿t káº¿ kiáº¿n trÃºc ná»n táº£ng há»c trá»±c tuyáº¿n', 'done', 'high', 1, '2025-05-01', '2025-05-20', '2025-05-18 17:00:00', 32.00, 30.00, 'USR-005-MGR-TECH', '2025-04-20 08:00:00'),

('TSK-009-ED-002', 'PRJ-009-EDUTECH', 'User Authentication', 'Há»‡ thá»‘ng Ä‘Äƒng nháº­p: email, social login, SSO', 'done', 'high', 2, '2025-05-15', '2025-06-01', '2025-05-30 16:30:00', 24.00, 26.00, 'USR-009-DEV-BE02', '2025-05-10 08:00:00'),

('TSK-009-ED-003', 'PRJ-009-EDUTECH', 'Course Management API', 'API quáº£n lÃ½ khÃ³a há»c: CRUD, categories, pricing', 'done', 'high', 3, '2025-05-25', '2025-06-15', '2025-06-13 17:00:00', 32.00, 35.00, 'USR-025-INACTIVE', '2025-05-20 08:00:00'),

('TSK-009-ED-004', 'PRJ-009-EDUTECH', 'Video Player Component', 'Component video player: streaming, progress tracking', 'in_progress', 'high', 4, '2025-06-10', '2025-07-01', NULL, 40.00, 25.00, 'USR-013-DEV-FE02', '2025-06-05 08:00:00'),

('TSK-009-ED-005', 'PRJ-009-EDUTECH', 'Quiz & Assessment Module', 'Module quiz: multiple choice, true/false, scoring', 'todo', 'medium', 5, '2025-07-01', '2025-07-20', NULL, 32.00, NULL, 'USR-025-INACTIVE', '2025-06-25 08:00:00'),

('TSK-009-ED-006', 'PRJ-009-EDUTECH', 'Certificate Generation', 'Táº¡o chá»©ng chá»‰: template, PDF generation, verification', 'backlog', 'low', 6, NULL, NULL, NULL, 24.00, NULL, 'USR-009-DEV-BE02', '2025-07-01 08:00:00'),

('TSK-009-ED-007', 'PRJ-009-EDUTECH', 'Live Class Integration', 'TÃ­ch há»£p live class: Zoom, Google Meet', 'backlog', 'medium', 7, NULL, NULL, NULL, 40.00, NULL, 'USR-013-DEV-FE02', '2025-07-05 08:00:00'),

('TSK-009-ED-008', 'PRJ-009-EDUTECH', 'Payment Integration', 'TÃ­ch há»£p thanh toÃ¡n: VNPay, MoMo, bank transfer', 'backlog', 'high', 8, NULL, NULL, NULL, 32.00, NULL, 'USR-009-DEV-BE02', '2025-07-10 08:00:00');

-- ===== PRJ-010: CryptoWallet - 5 tasks (Cancelled) =====

INSERT INTO `tasks` (`id`, `project_id`, `title`, `description`, `status`, `priority`, `position`, `start_date`, `due_date`, `completed_at`, `estimated_hours`, `actual_hours`, `created_by`, `created_at`) VALUES

('TSK-010-CW-001', 'PRJ-010-CRYPTO', 'Blockchain Research', 'NghiÃªn cá»©u blockchain: Ethereum, BSC, Solana', 'done', 'high', 1, '2025-02-01', '2025-02-20', '2025-02-18 17:00:00', 40.00, 42.00, 'USR-005-MGR-TECH', '2025-01-20 08:00:00'),

('TSK-010-CW-002', 'PRJ-010-CRYPTO', 'Wallet Architecture', 'Thiáº¿t káº¿ kiáº¿n trÃºc vÃ­: HD wallet, key management', 'done', 'high', 2, '2025-02-15', '2025-03-05', '2025-03-03 16:30:00', 32.00, 35.00, 'USR-008-DEV-BE01', '2025-02-10 08:00:00'),

('TSK-010-CW-003', 'PRJ-010-CRYPTO', 'Smart Contract Development', 'PhÃ¡t triá»ƒn smart contract: token, swap, staking', 'in_progress', 'urgent', 3, '2025-03-01', '2025-04-01', NULL, 60.00, 30.00, 'USR-008-DEV-BE01', '2025-02-25 08:00:00'),

('TSK-010-CW-004', 'PRJ-010-CRYPTO', 'Exchange Integration', 'TÃ­ch há»£p sÃ n giao dá»‹ch: Binance, Coinbase APIs', 'backlog', 'high', 4, NULL, NULL, NULL, 48.00, NULL, 'USR-008-DEV-BE01', '2025-03-15 08:00:00'),

('TSK-010-CW-005', 'PRJ-010-CRYPTO', 'KYC/AML Compliance', 'TuÃ¢n thá»§ KYC/AML: identity verification, transaction monitoring', 'backlog', 'urgent', 5, NULL, NULL, NULL, 40.00, NULL, 'USR-005-MGR-TECH', '2025-03-20 08:00:00');

-- ===== TASKS Äáº¶C BIá»†T: QuÃ¡ háº¡n, khÃ´ng cÃ³ assignee =====

INSERT INTO `tasks` (`id`, `project_id`, `title`, `description`, `status`, `priority`, `position`, `start_date`, `due_date`, `completed_at`, `estimated_hours`, `actual_hours`, `created_by`, `created_at`) VALUES

-- Task quÃ¡ háº¡n (OVERDUE) - VinMart
('TSK-001-VM-OVD1', 'PRJ-001-VINMART', '[OVERDUE] Fix Payment Gateway Timeout', 'Sá»­a lá»—i timeout khi thanh toÃ¡n VNPay trong giá» cao Ä‘iá»ƒm', 'in_progress', 'urgent', 100, '2025-12-15', '2026-01-05', NULL, 16.00, 12.00, 'USR-008-DEV-BE01', '2025-12-10 08:00:00'),

('TSK-001-VM-OVD2', 'PRJ-001-VINMART', '[OVERDUE] Mobile Responsive Issues', 'Sá»­a lá»—i hiá»ƒn thá»‹ trÃªn mobile: cart, checkout pages', 'todo', 'high', 101, '2025-12-20', '2026-01-03', NULL, 8.00, NULL, 'USR-012-DEV-FE01', '2025-12-15 08:00:00'),

-- Task quÃ¡ háº¡n - FPT Bank
('TSK-002-FB-OVD1', 'PRJ-002-FPTBANK', '[OVERDUE] OTP Delivery Delay', 'Kháº¯c phá»¥c tÃ¬nh tráº¡ng OTP gá»­i cháº­m qua SMS', 'in_progress', 'urgent', 100, '2025-12-10', '2025-12-25', NULL, 16.00, 14.00, 'USR-008-DEV-BE01', '2025-12-05 08:00:00'),

-- Task khÃ´ng cÃ³ assignee
('TSK-001-VM-NOASGN', 'PRJ-001-VINMART', '[UNASSIGNED] Product Image Optimization', 'Tá»‘i Æ°u hÃ¬nh áº£nh sáº£n pháº©m: compression, lazy loading, CDN', 'backlog', 'medium', 102, NULL, NULL, NULL, 16.00, NULL, 'USR-003-MGR-PM01', '2025-11-01 08:00:00'),

('TSK-002-FB-NOASGN', 'PRJ-002-FPTBANK', '[UNASSIGNED] App Store Optimization', 'Tá»‘i Æ°u ASO: screenshots, description, keywords', 'backlog', 'low', 101, NULL, NULL, NULL, 8.00, NULL, 'USR-004-MGR-PM02', '2025-11-15 08:00:00'),

('TSK-003-HR-NOASGN', 'PRJ-003-HRMS', '[UNASSIGNED] Dark Mode Support', 'Há»— trá»£ dark mode cho toÃ n bá»™ há»‡ thá»‘ng', 'backlog', 'low', 100, NULL, NULL, NULL, 24.00, NULL, 'USR-003-MGR-PM01', '2025-10-01 08:00:00');


-- =============================================
-- 6. TASK_ASSIGNEES - PHÃ‚N CÃ”NG CÃ”NG VIá»†C
-- =============================================
-- MÃ´ táº£: GÃ¡n ngÆ°á»i thá»±c hiá»‡n cho tasks
-- TÃ¬nh huá»‘ng:
--   - Task cÃ³ 1 assignee (phá»• biáº¿n)
--   - Task cÃ³ nhiá»u assignees (pair programming, collaboration)
--   - Task khÃ´ng cÃ³ assignee (unassigned)
-- =============================================

INSERT INTO `task_assignees` (`task_id`, `user_id`, `assigned_by`, `assigned_at`) VALUES

-- ===== PRJ-001: VinMart E-commerce =====
-- Sprint 1
('TSK-001-VM-001', 'USR-008-DEV-BE01', 'USR-005-MGR-TECH', '2025-05-20 09:00:00'),
('TSK-001-VM-002', 'USR-008-DEV-BE01', 'USR-005-MGR-TECH', '2025-05-20 09:00:00'),
('TSK-001-VM-002', 'USR-021-DEVOPS', 'USR-005-MGR-TECH', '2025-05-20 09:00:00'),
('TSK-001-VM-003', 'USR-017-DESIGN01', 'USR-007-MGR-DESIGN', '2025-05-20 09:00:00'),
('TSK-001-VM-004', 'USR-008-DEV-BE01', 'USR-005-MGR-TECH', '2025-06-01 09:00:00'),
('TSK-001-VM-005', 'USR-017-DESIGN01', 'USR-007-MGR-DESIGN', '2025-05-25 09:00:00'),
('TSK-001-VM-006', 'USR-012-DEV-FE01', 'USR-005-MGR-TECH', '2025-06-05 09:00:00'),
('TSK-001-VM-007', 'USR-008-DEV-BE01', 'USR-005-MGR-TECH', '2025-06-15 09:00:00'),
('TSK-001-VM-007', 'USR-009-DEV-BE02', 'USR-005-MGR-TECH', '2025-06-15 09:00:00'),
('TSK-001-VM-008', 'USR-009-DEV-BE02', 'USR-005-MGR-TECH', '2025-06-20 09:00:00'),

-- Sprint 2
('TSK-001-VM-009', 'USR-012-DEV-FE01', 'USR-005-MGR-TECH', '2025-06-25 09:00:00'),
('TSK-001-VM-010', 'USR-013-DEV-FE02', 'USR-005-MGR-TECH', '2025-07-05 09:00:00'),
('TSK-001-VM-011', 'USR-009-DEV-BE02', 'USR-005-MGR-TECH', '2025-07-10 09:00:00'),
('TSK-001-VM-012', 'USR-012-DEV-FE01', 'USR-005-MGR-TECH', '2025-07-15 09:00:00'),
('TSK-001-VM-013', 'USR-013-DEV-FE02', 'USR-005-MGR-TECH', '2025-07-20 09:00:00'),
('TSK-001-VM-014', 'USR-008-DEV-BE01', 'USR-005-MGR-TECH', '2025-07-25 09:00:00'),
('TSK-001-VM-015', 'USR-010-DEV-BE03', 'USR-005-MGR-TECH', '2025-08-01 09:00:00'),

-- Sprint 3 (In Progress)
('TSK-001-VM-016', 'USR-012-DEV-FE01', 'USR-005-MGR-TECH', '2025-08-15 09:00:00'),
('TSK-001-VM-016', 'USR-013-DEV-FE02', 'USR-005-MGR-TECH', '2025-08-15 09:00:00'),
('TSK-001-VM-017', 'USR-008-DEV-BE01', 'USR-005-MGR-TECH', '2025-08-20 09:00:00'),
('TSK-001-VM-018', 'USR-009-DEV-BE02', 'USR-005-MGR-TECH', '2025-08-25 09:00:00'),
('TSK-001-VM-019', 'USR-010-DEV-BE03', 'USR-005-MGR-TECH', '2025-09-01 09:00:00'),
('TSK-001-VM-020', 'USR-009-DEV-BE02', 'USR-005-MGR-TECH', '2025-09-05 09:00:00'),
('TSK-001-VM-021', 'USR-019-QA01', 'USR-003-MGR-PM01', '2025-09-15 09:00:00'),

-- Sprint 4 (Todo)
('TSK-001-VM-022', 'USR-008-DEV-BE01', 'USR-005-MGR-TECH', '2025-09-20 09:00:00'),
('TSK-001-VM-023', 'USR-010-DEV-BE03', 'USR-005-MGR-TECH', '2025-09-25 09:00:00'),
('TSK-001-VM-024', 'USR-013-DEV-FE02', 'USR-005-MGR-TECH', '2025-10-01 09:00:00'),

-- Overdue tasks
('TSK-001-VM-OVD1', 'USR-008-DEV-BE01', 'USR-005-MGR-TECH', '2025-12-10 09:00:00'),
('TSK-001-VM-OVD2', 'USR-012-DEV-FE01', 'USR-005-MGR-TECH', '2025-12-15 09:00:00'),

-- ===== PRJ-002: FPT Mobile Banking =====
('TSK-002-FB-001', 'USR-005-MGR-TECH', 'USR-004-MGR-PM02', '2025-07-25 09:00:00'),
('TSK-002-FB-002', 'USR-015-DEV-MOB1', 'USR-005-MGR-TECH', '2025-08-10 09:00:00'),
('TSK-002-FB-002', 'USR-016-DEV-MOB2', 'USR-005-MGR-TECH', '2025-08-10 09:00:00'),
('TSK-002-FB-003', 'USR-008-DEV-BE01', 'USR-005-MGR-TECH', '2025-08-15 09:00:00'),
('TSK-002-FB-004', 'USR-008-DEV-BE01', 'USR-005-MGR-TECH', '2025-08-20 09:00:00'),
('TSK-002-FB-005', 'USR-016-DEV-MOB2', 'USR-005-MGR-TECH', '2025-08-25 09:00:00'),
('TSK-002-FB-006', 'USR-017-DESIGN01', 'USR-007-MGR-DESIGN', '2025-07-28 09:00:00'),
('TSK-002-FB-007', 'USR-015-DEV-MOB1', 'USR-005-MGR-TECH', '2025-09-10 09:00:00'),
('TSK-002-FB-008', 'USR-016-DEV-MOB2', 'USR-005-MGR-TECH', '2025-09-15 09:00:00'),
('TSK-002-FB-009', 'USR-008-DEV-BE01', 'USR-005-MGR-TECH', '2025-09-25 09:00:00'),
('TSK-002-FB-010', 'USR-008-DEV-BE01', 'USR-005-MGR-TECH', '2025-10-05 09:00:00'),
('TSK-002-FB-010', 'USR-011-DEV-BE04', 'USR-005-MGR-TECH', '2025-10-05 09:00:00'),
('TSK-002-FB-011', 'USR-015-DEV-MOB1', 'USR-005-MGR-TECH', '2025-10-10 09:00:00'),
('TSK-002-FB-012', 'USR-016-DEV-MOB2', 'USR-005-MGR-TECH', '2025-10-15 09:00:00'),
('TSK-002-FB-013', 'USR-011-DEV-BE04', 'USR-005-MGR-TECH', '2025-10-20 09:00:00'),
('TSK-002-FB-014', 'USR-011-DEV-BE04', 'USR-005-MGR-TECH', '2025-10-25 09:00:00'),
('TSK-002-FB-015', 'USR-008-DEV-BE01', 'USR-005-MGR-TECH', '2025-11-01 09:00:00'),
('TSK-002-FB-016', 'USR-015-DEV-MOB1', 'USR-005-MGR-TECH', '2025-11-05 09:00:00'),
('TSK-002-FB-OVD1', 'USR-008-DEV-BE01', 'USR-005-MGR-TECH', '2025-12-05 09:00:00'),

-- ===== PRJ-003: HRMS Internal =====
('TSK-003-HR-001', 'USR-003-MGR-PM01', 'USR-003-MGR-PM01', '2025-02-20 09:00:00'),
('TSK-003-HR-002', 'USR-009-DEV-BE02', 'USR-003-MGR-PM01', '2025-03-05 09:00:00'),
('TSK-003-HR-003', 'USR-012-DEV-FE01', 'USR-003-MGR-PM01', '2025-03-15 09:00:00'),
('TSK-003-HR-004', 'USR-014-DEV-FE03', 'USR-003-MGR-PM01', '2025-03-25 09:00:00'),
('TSK-003-HR-005', 'USR-009-DEV-BE02', 'USR-003-MGR-PM01', '2025-04-05 09:00:00'),
('TSK-003-HR-006', 'USR-012-DEV-FE01', 'USR-003-MGR-PM01', '2025-04-15 09:00:00'),
('TSK-003-HR-007', 'USR-009-DEV-BE02', 'USR-003-MGR-PM01', '2025-04-25 09:00:00'),
('TSK-003-HR-008', 'USR-009-DEV-BE02', 'USR-003-MGR-PM01', '2025-05-10 09:00:00'),
('TSK-003-HR-009', 'USR-014-DEV-FE03', 'USR-003-MGR-PM01', '2025-05-25 09:00:00'),
('TSK-003-HR-010', 'USR-012-DEV-FE01', 'USR-003-MGR-PM01', '2025-06-05 09:00:00'),
('TSK-003-HR-011', 'USR-009-DEV-BE02', 'USR-003-MGR-PM01', '2025-06-15 09:00:00'),
('TSK-003-HR-012', 'USR-009-DEV-BE02', 'USR-003-MGR-PM01', '2025-06-25 09:00:00'),
('TSK-003-HR-013', 'USR-009-DEV-BE02', 'USR-003-MGR-PM01', '2025-07-10 09:00:00'),
('TSK-003-HR-014', 'USR-014-DEV-FE03', 'USR-003-MGR-PM01', '2025-07-25 09:00:00'),
('TSK-003-HR-015', 'USR-009-DEV-BE02', 'USR-003-MGR-PM01', '2025-08-10 09:00:00'),
('TSK-003-HR-016', 'USR-009-DEV-BE02', 'USR-003-MGR-PM01', '2025-08-20 09:00:00'),
('TSK-003-HR-017', 'USR-012-DEV-FE01', 'USR-003-MGR-PM01', '2025-09-01 09:00:00'),

-- ===== PRJ-004: MedCare Healthcare =====
('TSK-004-MC-001', 'USR-005-MGR-TECH', 'USR-004-MGR-PM02', '2025-09-20 09:00:00'),
('TSK-004-MC-002', 'USR-010-DEV-BE03', 'USR-006-MGR-SCRUM', '2025-10-10 09:00:00'),
('TSK-004-MC-003', 'USR-011-DEV-BE04', 'USR-006-MGR-SCRUM', '2025-10-15 09:00:00'),
('TSK-004-MC-004', 'USR-013-DEV-FE02', 'USR-006-MGR-SCRUM', '2025-10-20 09:00:00'),
('TSK-004-MC-004', 'USR-014-DEV-FE03', 'USR-006-MGR-SCRUM', '2025-10-20 09:00:00'),
('TSK-004-MC-005', 'USR-017-DESIGN01', 'USR-007-MGR-DESIGN', '2025-10-05 09:00:00'),
('TSK-004-MC-006', 'USR-010-DEV-BE03', 'USR-006-MGR-SCRUM', '2025-11-01 09:00:00'),
('TSK-004-MC-007', 'USR-011-DEV-BE04', 'USR-006-MGR-SCRUM', '2025-11-05 09:00:00'),

-- ===== PRJ-005: SmartBot AI =====
('TSK-005-SB-001', 'USR-008-DEV-BE01', 'USR-002-ADMIN-CTO', '2025-12-10 09:00:00'),
('TSK-005-SB-002', 'USR-005-MGR-TECH', 'USR-002-ADMIN-CTO', '2025-12-15 09:00:00'),
('TSK-005-SB-003', 'USR-022-BA', 'USR-002-ADMIN-CTO', '2025-12-20 09:00:00'),

-- ===== PRJ-006: LogiTrack =====
('TSK-006-LT-001', 'USR-003-MGR-PM01', 'USR-003-MGR-PM01', '2026-01-05 09:00:00'),

-- ===== PRJ-007: DevOps Infrastructure =====
('TSK-007-DO-001', 'USR-021-DEVOPS', 'USR-002-ADMIN-CTO', '2024-12-20 09:00:00'),
('TSK-007-DO-002', 'USR-021-DEVOPS', 'USR-002-ADMIN-CTO', '2025-01-05 09:00:00'),
('TSK-007-DO-003', 'USR-021-DEVOPS', 'USR-002-ADMIN-CTO', '2025-01-15 09:00:00'),
('TSK-007-DO-004', 'USR-021-DEVOPS', 'USR-002-ADMIN-CTO', '2025-01-25 09:00:00'),
('TSK-007-DO-005', 'USR-021-DEVOPS', 'USR-002-ADMIN-CTO', '2025-02-05 09:00:00'),
('TSK-007-DO-006', 'USR-021-DEVOPS', 'USR-002-ADMIN-CTO', '2025-02-15 09:00:00'),
('TSK-007-DO-007', 'USR-021-DEVOPS', 'USR-002-ADMIN-CTO', '2025-02-25 09:00:00'),
('TSK-007-DO-008', 'USR-021-DEVOPS', 'USR-002-ADMIN-CTO', '2025-03-05 09:00:00'),
('TSK-007-DO-009', 'USR-008-DEV-BE01', 'USR-002-ADMIN-CTO', '2025-03-15 09:00:00'),
('TSK-007-DO-009', 'USR-021-DEVOPS', 'USR-002-ADMIN-CTO', '2025-03-15 09:00:00'),
('TSK-007-DO-010', 'USR-021-DEVOPS', 'USR-002-ADMIN-CTO', '2025-03-25 09:00:00'),
('TSK-007-DO-011', 'USR-021-DEVOPS', 'USR-002-ADMIN-CTO', '2025-04-10 09:00:00'),
('TSK-007-DO-012', 'USR-021-DEVOPS', 'USR-002-ADMIN-CTO', '2025-04-25 09:00:00'),
('TSK-007-DO-012', 'USR-008-DEV-BE01', 'USR-002-ADMIN-CTO', '2025-04-25 09:00:00'),

-- ===== PRJ-008: Website Redesign =====
('TSK-008-WB-001', 'USR-007-MGR-DESIGN', 'USR-007-MGR-DESIGN', '2025-03-25 09:00:00'),
('TSK-008-WB-002', 'USR-017-DESIGN01', 'USR-007-MGR-DESIGN', '2025-04-03 09:00:00'),
('TSK-008-WB-003', 'USR-017-DESIGN01', 'USR-007-MGR-DESIGN', '2025-04-15 09:00:00'),
('TSK-008-WB-004', 'USR-018-DESIGN02', 'USR-007-MGR-DESIGN', '2025-04-25 09:00:00'),
('TSK-008-WB-005', 'USR-012-DEV-FE01', 'USR-007-MGR-DESIGN', '2025-05-10 09:00:00'),
('TSK-008-WB-006', 'USR-012-DEV-FE01', 'USR-007-MGR-DESIGN', '2025-05-25 09:00:00'),
('TSK-008-WB-007', 'USR-012-DEV-FE01', 'USR-007-MGR-DESIGN', '2025-06-10 09:00:00'),
('TSK-008-WB-008', 'USR-018-DESIGN02', 'USR-007-MGR-DESIGN', '2025-06-25 09:00:00'),
('TSK-008-WB-009', 'USR-017-DESIGN01', 'USR-007-MGR-DESIGN', '2025-07-05 09:00:00'),
('TSK-008-WB-010', 'USR-007-MGR-DESIGN', 'USR-007-MGR-DESIGN', '2025-07-20 09:00:00'),

-- ===== PRJ-009: EduTech (cÃ³ nhÃ¢n viÃªn Ä‘Ã£ nghá»‰) =====
('TSK-009-ED-001', 'USR-005-MGR-TECH', 'USR-004-MGR-PM02', '2025-04-20 09:00:00'),
('TSK-009-ED-002', 'USR-009-DEV-BE02', 'USR-004-MGR-PM02', '2025-05-10 09:00:00'),
('TSK-009-ED-003', 'USR-025-INACTIVE', 'USR-004-MGR-PM02', '2025-05-20 09:00:00'),
('TSK-009-ED-004', 'USR-013-DEV-FE02', 'USR-004-MGR-PM02', '2025-06-05 09:00:00'),
('TSK-009-ED-005', 'USR-025-INACTIVE', 'USR-004-MGR-PM02', '2025-06-25 09:00:00'),

-- ===== PRJ-010: CryptoWallet =====
('TSK-010-CW-001', 'USR-005-MGR-TECH', 'USR-001-ADMIN-CEO', '2025-01-20 09:00:00'),
('TSK-010-CW-002', 'USR-008-DEV-BE01', 'USR-005-MGR-TECH', '2025-02-10 09:00:00'),
('TSK-010-CW-003', 'USR-008-DEV-BE01', 'USR-005-MGR-TECH', '2025-02-25 09:00:00');


-- =============================================
-- 7. TASK_LABELS - Gáº®N NHÃƒN TASK
-- =============================================

INSERT INTO `task_labels` (`task_id`, `label_id`, `created_at`) VALUES

-- VinMart tasks
('TSK-001-VM-001', 'LBL-004-DOCS', '2025-05-20 09:00:00'),
('TSK-001-VM-002', 'LBL-002-FEATURE', '2025-05-20 09:00:00'),
('TSK-001-VM-003', 'LBL-006-DESIGN', '2025-05-20 09:00:00'),
('TSK-001-VM-004', 'LBL-002-FEATURE', '2025-06-01 09:00:00'),
('TSK-001-VM-004', 'LBL-009-SECURITY', '2025-06-01 09:00:00'),
('TSK-001-VM-005', 'LBL-006-DESIGN', '2025-05-25 09:00:00'),
('TSK-001-VM-017', 'LBL-002-FEATURE', '2025-08-20 09:00:00'),
('TSK-001-VM-017', 'LBL-009-SECURITY', '2025-08-20 09:00:00'),
('TSK-001-VM-022', 'LBL-010-PERF', '2025-09-20 09:00:00'),
('TSK-001-VM-033', 'LBL-010-PERF', '2025-11-15 09:00:00'),
('TSK-001-VM-034', 'LBL-009-SECURITY', '2025-11-20 09:00:00'),
('TSK-001-VM-OVD1', 'LBL-001-BUG', '2025-12-10 09:00:00'),
('TSK-001-VM-OVD1', 'LBL-008-URGENT', '2025-12-10 09:00:00'),
('TSK-001-VM-OVD2', 'LBL-001-BUG', '2025-12-15 09:00:00'),

-- FPT Bank tasks
('TSK-002-FB-001', 'LBL-009-SECURITY', '2025-07-25 09:00:00'),
('TSK-002-FB-002', 'LBL-002-FEATURE', '2025-08-10 09:00:00'),
('TSK-002-FB-002', 'LBL-009-SECURITY', '2025-08-10 09:00:00'),
('TSK-002-FB-003', 'LBL-009-SECURITY', '2025-08-15 09:00:00'),
('TSK-002-FB-006', 'LBL-006-DESIGN', '2025-07-28 09:00:00'),
('TSK-002-FB-030', 'LBL-009-SECURITY', '2026-01-15 09:00:00'),
('TSK-002-FB-030', 'LBL-005-TEST', '2026-01-15 09:00:00'),
('TSK-002-FB-OVD1', 'LBL-001-BUG', '2025-12-05 09:00:00'),
('TSK-002-FB-OVD1', 'LBL-012-HOTFIX', '2025-12-05 09:00:00'),

-- HRMS tasks
('TSK-003-HR-007', 'LBL-002-FEATURE', '2025-04-25 09:00:00'),
('TSK-003-HR-012', 'LBL-002-FEATURE', '2025-06-25 09:00:00'),
('TSK-003-HR-013', 'LBL-002-FEATURE', '2025-07-10 09:00:00'),

-- MedCare tasks
('TSK-004-MC-001', 'LBL-009-SECURITY', '2025-09-20 09:00:00'),
('TSK-004-MC-005', 'LBL-006-DESIGN', '2025-10-05 09:00:00'),
('TSK-004-MC-009', 'LBL-002-FEATURE', '2025-11-20 09:00:00'),
('TSK-004-MC-009', 'LBL-007-RESEARCH', '2025-11-20 09:00:00'),

-- SmartBot tasks
('TSK-005-SB-001', 'LBL-007-RESEARCH', '2025-12-10 09:00:00'),
('TSK-005-SB-004', 'LBL-007-RESEARCH', '2025-12-25 09:00:00'),

-- DevOps tasks
('TSK-007-DO-001', 'LBL-002-FEATURE', '2024-12-20 09:00:00'),
('TSK-007-DO-009', 'LBL-009-SECURITY', '2025-03-15 09:00:00'),
('TSK-007-DO-010', 'LBL-004-DOCS', '2025-03-25 09:00:00'),

-- Website tasks
('TSK-008-WB-002', 'LBL-006-DESIGN', '2025-04-03 09:00:00'),
('TSK-008-WB-007', 'LBL-010-PERF', '2025-06-10 09:00:00');

-- =============================================
-- 8. TASK_CHECKLISTS - CHECKLIST CHI TIáº¾T
-- =============================================

INSERT INTO `task_checklists` (`id`, `task_id`, `title`, `is_completed`, `position`, `completed_at`, `completed_by`, `created_at`) VALUES

-- VinMart - Database Schema task
('CKL-001', 'TSK-001-VM-001', 'Thiáº¿t káº¿ ERD diagram', 1, 1, '2025-06-05 10:00:00', 'USR-008-DEV-BE01', '2025-05-20 09:00:00'),
('CKL-002', 'TSK-001-VM-001', 'Äá»‹nh nghÄ©a cÃ¡c báº£ng chÃ­nh', 1, 2, '2025-06-08 14:00:00', 'USR-008-DEV-BE01', '2025-05-20 09:00:00'),
('CKL-003', 'TSK-001-VM-001', 'Thiáº¿t káº¿ indexes vÃ  constraints', 1, 3, '2025-06-10 16:00:00', 'USR-008-DEV-BE01', '2025-05-20 09:00:00'),
('CKL-004', 'TSK-001-VM-001', 'Review vá»›i Tech Lead', 1, 4, '2025-06-12 11:00:00', 'USR-005-MGR-TECH', '2025-05-20 09:00:00'),
('CKL-005', 'TSK-001-VM-001', 'Viáº¿t migration scripts', 1, 5, '2025-06-14 15:00:00', 'USR-008-DEV-BE01', '2025-05-20 09:00:00'),

-- VinMart - VNPay Integration (in progress)
('CKL-006', 'TSK-001-VM-017', 'ÄÄƒng kÃ½ merchant account', 1, 1, '2025-08-25 10:00:00', 'USR-003-MGR-PM01', '2025-08-20 09:00:00'),
('CKL-007', 'TSK-001-VM-017', 'Implement payment API', 1, 2, '2025-09-01 16:00:00', 'USR-008-DEV-BE01', '2025-08-20 09:00:00'),
('CKL-008', 'TSK-001-VM-017', 'Implement refund API', 1, 3, '2025-09-05 14:00:00', 'USR-008-DEV-BE01', '2025-08-20 09:00:00'),
('CKL-009', 'TSK-001-VM-017', 'Setup webhook handler', 0, 4, NULL, NULL, '2025-08-20 09:00:00'),
('CKL-010', 'TSK-001-VM-017', 'Test trÃªn sandbox', 0, 5, NULL, NULL, '2025-08-20 09:00:00'),
('CKL-011', 'TSK-001-VM-017', 'Test trÃªn production', 0, 6, NULL, NULL, '2025-08-20 09:00:00'),

-- FPT Bank - Biometric Authentication
('CKL-012', 'TSK-002-FB-002', 'Research iOS Face ID API', 1, 1, '2025-08-18 10:00:00', 'USR-015-DEV-MOB1', '2025-08-10 09:00:00'),
('CKL-013', 'TSK-002-FB-002', 'Research Android BiometricPrompt', 1, 2, '2025-08-18 14:00:00', 'USR-016-DEV-MOB2', '2025-08-10 09:00:00'),
('CKL-014', 'TSK-002-FB-002', 'Implement iOS module', 1, 3, '2025-08-25 16:00:00', 'USR-015-DEV-MOB1', '2025-08-10 09:00:00'),
('CKL-015', 'TSK-002-FB-002', 'Implement Android module', 1, 4, '2025-08-28 15:00:00', 'USR-016-DEV-MOB2', '2025-08-10 09:00:00'),
('CKL-016', 'TSK-002-FB-002', 'Fallback to PIN', 1, 5, '2025-09-01 11:00:00', 'USR-015-DEV-MOB1', '2025-08-10 09:00:00'),
('CKL-017', 'TSK-002-FB-002', 'Security review', 1, 6, '2025-09-03 14:00:00', 'USR-005-MGR-TECH', '2025-08-10 09:00:00'),

-- HRMS - Payroll Calculation (in progress)
('CKL-018', 'TSK-003-HR-013', 'CÃ´ng thá»©c tÃ­nh lÆ°Æ¡ng gross', 1, 1, '2025-07-20 10:00:00', 'USR-009-DEV-BE02', '2025-07-10 09:00:00'),
('CKL-019', 'TSK-003-HR-013', 'TÃ­nh BHXH, BHYT, BHTN', 1, 2, '2025-07-25 14:00:00', 'USR-009-DEV-BE02', '2025-07-10 09:00:00'),
('CKL-020', 'TSK-003-HR-013', 'TÃ­nh thuáº¿ TNCN', 1, 3, '2025-07-30 16:00:00', 'USR-009-DEV-BE02', '2025-07-10 09:00:00'),
('CKL-021', 'TSK-003-HR-013', 'TÃ­nh phá»¥ cáº¥p, thÆ°á»Ÿng', 0, 4, NULL, NULL, '2025-07-10 09:00:00'),
('CKL-022', 'TSK-003-HR-013', 'TÃ­nh kháº¥u trá»«', 0, 5, NULL, NULL, '2025-07-10 09:00:00'),
('CKL-023', 'TSK-003-HR-013', 'Unit tests', 0, 6, NULL, NULL, '2025-07-10 09:00:00'),

-- MedCare - Patient Registration (in progress)
('CKL-024', 'TSK-004-MC-002', 'Form Ä‘Äƒng kÃ½ bá»‡nh nhÃ¢n', 1, 1, '2025-10-20 10:00:00', 'USR-010-DEV-BE03', '2025-10-10 09:00:00'),
('CKL-025', 'TSK-004-MC-002', 'Validation thÃ´ng tin', 1, 2, '2025-10-25 14:00:00', 'USR-010-DEV-BE03', '2025-10-10 09:00:00'),
('CKL-026', 'TSK-004-MC-002', 'TÃ­ch há»£p BHYT', 0, 3, NULL, NULL, '2025-10-10 09:00:00'),
('CKL-027', 'TSK-004-MC-002', 'Upload há»“ sÆ¡ y táº¿', 0, 4, NULL, NULL, '2025-10-10 09:00:00'),
('CKL-028', 'TSK-004-MC-002', 'XÃ¡c thá»±c danh tÃ­nh', 0, 5, NULL, NULL, '2025-10-10 09:00:00');


-- =============================================
-- 9. DOCUMENTS - TÃ€I LIá»†U Dá»° ÃN
-- =============================================

INSERT INTO `documents` (`id`, `name`, `description`, `type`, `mime_type`, `file_size`, `file_path`, `parent_id`, `project_id`, `is_starred`, `download_count`, `uploaded_by`, `created_at`) VALUES

-- Root folders
('DOC-FOLDER-VM', 'VinMart Documents', 'TÃ i liá»‡u dá»± Ã¡n VinMart E-commerce', 'folder', NULL, NULL, NULL, NULL, 'PRJ-001-VINMART', 0, 0, 'USR-003-MGR-PM01', '2025-05-15 08:00:00'),
('DOC-FOLDER-FB', 'FPT Bank Documents', 'TÃ i liá»‡u dá»± Ã¡n FPT Mobile Banking', 'folder', NULL, NULL, NULL, NULL, 'PRJ-002-FPTBANK', 0, 0, 'USR-004-MGR-PM02', '2025-07-20 08:00:00'),
('DOC-FOLDER-HR', 'HRMS Documents', 'TÃ i liá»‡u dá»± Ã¡n HRMS Internal', 'folder', NULL, NULL, NULL, NULL, 'PRJ-003-HRMS', 0, 0, 'USR-003-MGR-PM01', '2025-02-15 08:00:00'),
('DOC-FOLDER-MC', 'MedCare Documents', 'TÃ i liá»‡u dá»± Ã¡n MedCare Healthcare', 'folder', NULL, NULL, NULL, NULL, 'PRJ-004-MEDCARE', 0, 0, 'USR-004-MGR-PM02', '2025-09-15 08:00:00'),

-- VinMart subfolders
('DOC-VM-DESIGN', 'Design', 'TÃ i liá»‡u thiáº¿t káº¿', 'folder', NULL, NULL, NULL, 'DOC-FOLDER-VM', 'PRJ-001-VINMART', 0, 0, 'USR-007-MGR-DESIGN', '2025-05-20 08:00:00'),
('DOC-VM-TECH', 'Technical', 'TÃ i liá»‡u ká»¹ thuáº­t', 'folder', NULL, NULL, NULL, 'DOC-FOLDER-VM', 'PRJ-001-VINMART', 0, 0, 'USR-005-MGR-TECH', '2025-05-20 08:00:00'),
('DOC-VM-BA', 'Business Analysis', 'TÃ i liá»‡u phÃ¢n tÃ­ch nghiá»‡p vá»¥', 'folder', NULL, NULL, NULL, 'DOC-FOLDER-VM', 'PRJ-001-VINMART', 0, 0, 'USR-022-BA', '2025-05-20 08:00:00'),

-- VinMart files
('DOC-VM-001', 'VinMart_PRD_v2.1.pdf', 'Product Requirements Document', 'file', 'application/pdf', 2548000, '/uploads/vinmart/VinMart_PRD_v2.1.pdf', 'DOC-VM-BA', 'PRJ-001-VINMART', 1, 45, 'USR-022-BA', '2025-05-25 08:00:00'),
('DOC-VM-002', 'VinMart_ERD.png', 'Entity Relationship Diagram', 'file', 'image/png', 856000, '/uploads/vinmart/VinMart_ERD.png', 'DOC-VM-TECH', 'PRJ-001-VINMART', 1, 32, 'USR-008-DEV-BE01', '2025-06-10 08:00:00'),
('DOC-VM-003', 'API_Documentation.pdf', 'API Documentation v1.5', 'file', 'application/pdf', 1245000, '/uploads/vinmart/API_Documentation.pdf', 'DOC-VM-TECH', 'PRJ-001-VINMART', 0, 28, 'USR-008-DEV-BE01', '2025-07-15 08:00:00'),
('DOC-VM-004', 'UI_Mockups.fig', 'Figma Design File', 'file', 'application/octet-stream', 15680000, '/uploads/vinmart/UI_Mockups.fig', 'DOC-VM-DESIGN', 'PRJ-001-VINMART', 1, 18, 'USR-017-DESIGN01', '2025-06-05 08:00:00'),
('DOC-VM-005', 'Design_System.pdf', 'Design System Guidelines', 'file', 'application/pdf', 3250000, '/uploads/vinmart/Design_System.pdf', 'DOC-VM-DESIGN', 'PRJ-001-VINMART', 0, 12, 'USR-017-DESIGN01', '2025-06-20 08:00:00'),
('DOC-VM-006', 'Test_Cases.xlsx', 'Test Cases Document', 'file', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 458000, '/uploads/vinmart/Test_Cases.xlsx', 'DOC-FOLDER-VM', 'PRJ-001-VINMART', 0, 8, 'USR-019-QA01', '2025-08-01 08:00:00'),

-- FPT Bank files
('DOC-FB-001', 'FPTBank_SRS.pdf', 'Software Requirements Specification', 'file', 'application/pdf', 4520000, '/uploads/fptbank/FPTBank_SRS.pdf', 'DOC-FOLDER-FB', 'PRJ-002-FPTBANK', 1, 38, 'USR-004-MGR-PM02', '2025-07-25 08:00:00'),
('DOC-FB-002', 'Security_Architecture.pdf', 'Security Architecture Document', 'file', 'application/pdf', 2890000, '/uploads/fptbank/Security_Architecture.pdf', 'DOC-FOLDER-FB', 'PRJ-002-FPTBANK', 1, 25, 'USR-005-MGR-TECH', '2025-08-05 08:00:00'),
('DOC-FB-003', 'Mobile_App_Design.fig', 'Mobile App Figma Design', 'file', 'application/octet-stream', 28500000, '/uploads/fptbank/Mobile_App_Design.fig', 'DOC-FOLDER-FB', 'PRJ-002-FPTBANK', 1, 15, 'USR-017-DESIGN01', '2025-08-15 08:00:00'),
('DOC-FB-004', 'PCI_DSS_Checklist.xlsx', 'PCI-DSS Compliance Checklist', 'file', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 125000, '/uploads/fptbank/PCI_DSS_Checklist.xlsx', 'DOC-FOLDER-FB', 'PRJ-002-FPTBANK', 0, 10, 'USR-019-QA01', '2025-09-01 08:00:00'),

-- HRMS files
('DOC-HR-001', 'HRMS_Functional_Spec.pdf', 'Functional Specification Document', 'file', 'application/pdf', 1850000, '/uploads/hrms/HRMS_Functional_Spec.pdf', 'DOC-FOLDER-HR', 'PRJ-003-HRMS', 1, 22, 'USR-003-MGR-PM01', '2025-02-20 08:00:00'),
('DOC-HR-002', 'Payroll_Formula.xlsx', 'Payroll Calculation Formulas', 'file', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 85000, '/uploads/hrms/Payroll_Formula.xlsx', 'DOC-FOLDER-HR', 'PRJ-003-HRMS', 1, 35, 'USR-009-DEV-BE02', '2025-06-25 08:00:00'),
('DOC-HR-003', 'User_Manual.pdf', 'User Manual v1.0', 'file', 'application/pdf', 5680000, '/uploads/hrms/User_Manual.pdf', 'DOC-FOLDER-HR', 'PRJ-003-HRMS', 0, 18, 'USR-003-MGR-PM01', '2025-08-15 08:00:00'),

-- MedCare files
('DOC-MC-001', 'MedCare_Architecture.pdf', 'System Architecture Document', 'file', 'application/pdf', 3250000, '/uploads/medcare/MedCare_Architecture.pdf', 'DOC-FOLDER-MC', 'PRJ-004-MEDCARE', 1, 12, 'USR-005-MGR-TECH', '2025-09-25 08:00:00'),
('DOC-MC-002', 'HL7_FHIR_Integration.pdf', 'HL7 FHIR Integration Guide', 'file', 'application/pdf', 1580000, '/uploads/medcare/HL7_FHIR_Integration.pdf', 'DOC-FOLDER-MC', 'PRJ-004-MEDCARE', 0, 8, 'USR-005-MGR-TECH', '2025-10-05 08:00:00');

-- =============================================
-- 10. DOCUMENT_SHARES - CHIA Sáºº TÃ€I LIá»†U
-- =============================================

INSERT INTO `document_shares` (`document_id`, `user_id`, `permission`, `shared_at`, `shared_by`) VALUES

-- VinMart PRD shared with team
('DOC-VM-001', 'USR-005-MGR-TECH', 'edit', '2025-05-25 09:00:00', 'USR-022-BA'),
('DOC-VM-001', 'USR-008-DEV-BE01', 'view', '2025-05-25 09:00:00', 'USR-022-BA'),
('DOC-VM-001', 'USR-012-DEV-FE01', 'view', '2025-05-25 09:00:00', 'USR-022-BA'),
('DOC-VM-001', 'USR-023-GUEST-VIN', 'view', '2025-06-01 09:00:00', 'USR-003-MGR-PM01'),

-- FPT Bank Security doc shared
('DOC-FB-002', 'USR-008-DEV-BE01', 'view', '2025-08-05 09:00:00', 'USR-005-MGR-TECH'),
('DOC-FB-002', 'USR-015-DEV-MOB1', 'view', '2025-08-05 09:00:00', 'USR-005-MGR-TECH'),
('DOC-FB-002', 'USR-016-DEV-MOB2', 'view', '2025-08-05 09:00:00', 'USR-005-MGR-TECH'),
('DOC-FB-002', 'USR-024-GUEST-FPT', 'view', '2025-08-10 09:00:00', 'USR-004-MGR-PM02'),

-- HRMS Payroll shared
('DOC-HR-002', 'USR-001-ADMIN-CEO', 'view', '2025-06-25 09:00:00', 'USR-003-MGR-PM01'),
('DOC-HR-002', 'USR-002-ADMIN-CTO', 'view', '2025-06-25 09:00:00', 'USR-003-MGR-PM01');


-- =============================================
-- 11. COMMENTS - BÃŒNH LUáº¬N TRAO Äá»”I
-- =============================================

INSERT INTO `comments` (`id`, `entity_type`, `entity_id`, `content`, `parent_id`, `created_by`, `created_at`) VALUES

-- VinMart - VNPay Integration discussion
('CMT-001', 'task', 'TSK-001-VM-017', 'ÄÃ£ liÃªn há»‡ VNPay, há» sáº½ gá»­i tÃ i liá»‡u API trong tuáº§n nÃ y.', NULL, 'USR-003-MGR-PM01', '2025-08-20 10:00:00'),
('CMT-002', 'task', 'TSK-001-VM-017', 'Nháº­n Ä‘Æ°á»£c tÃ i liá»‡u rá»“i. Báº¯t Ä‘áº§u implement payment flow.', NULL, 'USR-008-DEV-BE01', '2025-08-25 09:30:00'),
('CMT-003', 'task', 'TSK-001-VM-017', '@Tuáº¥n check ká»¹ pháº§n signature verification nhÃ©, Ä‘Ã¢y lÃ  Ä‘iá»ƒm hay bá»‹ lá»—i.', 'CMT-002', 'USR-005-MGR-TECH', '2025-08-25 10:15:00'),
('CMT-004', 'task', 'TSK-001-VM-017', 'ÄÃ£ implement xong payment API, Ä‘ang test trÃªn sandbox.', NULL, 'USR-008-DEV-BE01', '2025-09-01 16:00:00'),
('CMT-005', 'task', 'TSK-001-VM-017', 'Gáº·p issue timeout khi gá»i API trong giá» cao Ä‘iá»ƒm. Äang investigate.', NULL, 'USR-008-DEV-BE01', '2025-09-05 14:30:00'),
('CMT-006', 'task', 'TSK-001-VM-017', 'CÃ³ thá»ƒ do connection pool. Thá»­ tÄƒng timeout vÃ  implement retry logic.', 'CMT-005', 'USR-005-MGR-TECH', '2025-09-05 15:00:00'),

-- VinMart - Overdue task discussion
('CMT-007', 'task', 'TSK-001-VM-OVD1', 'Task nÃ y Ä‘ang quÃ¡ háº¡n, cáº§n Æ°u tiÃªn xá»­ lÃ½ gáº¥p!', NULL, 'USR-003-MGR-PM01', '2026-01-06 09:00:00'),
('CMT-008', 'task', 'TSK-001-VM-OVD1', 'Äang fix, nguyÃªn nhÃ¢n do connection pool exhausted. ETA: hÃ´m nay.', 'CMT-007', 'USR-008-DEV-BE01', '2026-01-06 10:30:00'),

-- FPT Bank - Security discussion
('CMT-009', 'task', 'TSK-002-FB-001', 'Cáº§n review láº¡i encryption algorithm, khÃ¡ch hÃ ng yÃªu cáº§u AES-256.', NULL, 'USR-024-GUEST-FPT', '2025-08-02 14:00:00'),
('CMT-010', 'task', 'TSK-002-FB-001', 'ÄÃ£ update sang AES-256-GCM, Ä‘áº£m báº£o cáº£ encryption vÃ  authentication.', 'CMT-009', 'USR-005-MGR-TECH', '2025-08-03 09:00:00'),
('CMT-011', 'task', 'TSK-002-FB-001', 'Perfect! Approved.', 'CMT-010', 'USR-024-GUEST-FPT', '2025-08-03 10:30:00'),

-- FPT Bank - Biometric
('CMT-012', 'task', 'TSK-002-FB-002', 'iOS Face ID Ä‘Ã£ implement xong, Ä‘ang test trÃªn cÃ¡c device.', NULL, 'USR-015-DEV-MOB1', '2025-08-25 16:30:00'),
('CMT-013', 'task', 'TSK-002-FB-002', 'Android BiometricPrompt cÅ©ng done. Cáº§n sync láº¡i UI giá»¯a 2 platform.', NULL, 'USR-016-DEV-MOB2', '2025-08-28 15:00:00'),
('CMT-014', 'task', 'TSK-002-FB-002', 'ÄÃ£ test trÃªn iPhone 12, 13, 14 vÃ  Samsung S21, S22. All passed!', NULL, 'USR-019-QA01', '2025-09-02 11:00:00'),

-- FPT Bank - OTP Delay issue
('CMT-015', 'task', 'TSK-002-FB-OVD1', 'KhÃ¡ch hÃ ng pháº£n Ã¡nh OTP gá»­i cháº­m 30-60 giÃ¢y trong giá» cao Ä‘iá»ƒm.', NULL, 'USR-004-MGR-PM02', '2025-12-05 09:00:00'),
('CMT-016', 'task', 'TSK-002-FB-OVD1', 'Äang check vá»›i SMS provider. CÃ³ thá»ƒ do rate limit cá»§a há».', 'CMT-015', 'USR-008-DEV-BE01', '2025-12-05 10:00:00'),
('CMT-017', 'task', 'TSK-002-FB-OVD1', 'Äá» xuáº¥t: implement fallback sang provider khÃ¡c khi primary cháº­m.', 'CMT-016', 'USR-005-MGR-TECH', '2025-12-05 11:30:00'),

-- HRMS - Payroll discussion
('CMT-018', 'task', 'TSK-003-HR-013', 'CÃ´ng thá»©c tÃ­nh thuáº¿ TNCN cáº§n update theo quy Ä‘á»‹nh má»›i 2024.', NULL, 'USR-003-MGR-PM01', '2025-07-15 09:00:00'),
('CMT-019', 'task', 'TSK-003-HR-013', 'ÄÃ£ update. Má»©c giáº£m trá»« gia cáº£nh: 11 triá»‡u/thÃ¡ng cho báº£n thÃ¢n, 4.4 triá»‡u/ngÆ°á»i phá»¥ thuá»™c.', 'CMT-018', 'USR-009-DEV-BE02', '2025-07-16 14:00:00'),
('CMT-020', 'task', 'TSK-003-HR-013', 'Cáº§n thÃªm case tÃ­nh thuáº¿ cho ngÆ°á»i nÆ°á»›c ngoÃ i (khÃ´ng cÆ° trÃº).', NULL, 'USR-003-MGR-PM01', '2025-07-20 10:00:00'),

-- MedCare - Architecture review
('CMT-021', 'task', 'TSK-004-MC-001', 'Äá» xuáº¥t sá»­ dá»¥ng FHIR R4 cho interoperability vá»›i cÃ¡c há»‡ thá»‘ng y táº¿ khÃ¡c.', NULL, 'USR-005-MGR-TECH', '2025-10-02 09:00:00'),
('CMT-022', 'task', 'TSK-004-MC-001', 'Äá»“ng Ã½. Cáº§n thÃªm layer adapter Ä‘á»ƒ convert data format.', 'CMT-021', 'USR-010-DEV-BE03', '2025-10-02 10:30:00'),

-- Project level comments
('CMT-023', 'project', 'PRJ-001-VINMART', 'Sprint 3 Ä‘ang cháº­m tiáº¿n Ä‘á»™ 1 tuáº§n do issue payment gateway. Cáº§n tÄƒng resource.', NULL, 'USR-003-MGR-PM01', '2025-09-10 09:00:00'),
('CMT-024', 'project', 'PRJ-001-VINMART', 'ÄÃ£ assign thÃªm Äá»©c vÃ o há»— trá»£ payment module.', 'CMT-023', 'USR-005-MGR-TECH', '2025-09-10 10:00:00'),

('CMT-025', 'project', 'PRJ-002-FPTBANK', 'KhÃ¡ch hÃ ng yÃªu cáº§u demo vÃ o cuá»‘i thÃ¡ng 11. Cáº§n chuáº©n bá»‹ mÃ´i trÆ°á»ng staging.', NULL, 'USR-004-MGR-PM02', '2025-11-01 09:00:00'),
('CMT-026', 'project', 'PRJ-002-FPTBANK', 'Staging environment Ä‘Ã£ ready. URL: staging.fptbank-app.vn', 'CMT-025', 'USR-021-DEVOPS', '2025-11-05 14:00:00'),

-- Document comments
('CMT-027', 'document', 'DOC-VM-001', 'PRD Ä‘Ã£ update thÃªm requirement cho loyalty program.', NULL, 'USR-022-BA', '2025-08-15 09:00:00'),
('CMT-028', 'document', 'DOC-FB-002', 'Security architecture Ä‘Ã£ Ä‘Æ°á»£c audit bá»Ÿi team security cá»§a FPT.', NULL, 'USR-005-MGR-TECH', '2025-09-15 10:00:00');


-- =============================================
-- 12. NOTIFICATIONS - THÃ”NG BÃO Há»† THá»NG
-- =============================================

INSERT INTO `notifications` (`id`, `user_id`, `actor_id`, `type`, `title`, `message`, `data`, `link`, `is_read`, `read_at`, `created_at`) VALUES

-- Task assigned notifications
('NTF-001', 'USR-008-DEV-BE01', 'USR-005-MGR-TECH', 'task_assigned', 'Báº¡n Ä‘Æ°á»£c giao task má»›i', 'Báº¡n Ä‘Æ°á»£c giao task "VNPay Integration" trong dá»± Ã¡n VinMart', '{"task_id": "TSK-001-VM-017", "project_id": "PRJ-001-VINMART"}', '/tasks/TSK-001-VM-017', 1, '2025-08-20 09:30:00', '2025-08-20 09:00:00'),

('NTF-002', 'USR-012-DEV-FE01', 'USR-005-MGR-TECH', 'task_assigned', 'Báº¡n Ä‘Æ°á»£c giao task má»›i', 'Báº¡n Ä‘Æ°á»£c giao task "Checkout Flow UI" trong dá»± Ã¡n VinMart', '{"task_id": "TSK-001-VM-016", "project_id": "PRJ-001-VINMART"}', '/tasks/TSK-001-VM-016', 1, '2025-08-15 10:00:00', '2025-08-15 09:00:00'),

('NTF-003', 'USR-015-DEV-MOB1', 'USR-005-MGR-TECH', 'task_assigned', 'Báº¡n Ä‘Æ°á»£c giao task má»›i', 'Báº¡n Ä‘Æ°á»£c giao task "Biometric Authentication" trong dá»± Ã¡n FPT Bank', '{"task_id": "TSK-002-FB-002", "project_id": "PRJ-002-FPTBANK"}', '/tasks/TSK-002-FB-002', 1, '2025-08-10 09:15:00', '2025-08-10 09:00:00'),

-- Task due soon notifications
('NTF-004', 'USR-008-DEV-BE01', NULL, 'task_due_soon', 'Task sáº¯p Ä‘áº¿n háº¡n', 'Task "VNPay Integration" sáº½ Ä‘áº¿n háº¡n trong 2 ngÃ y', '{"task_id": "TSK-001-VM-017", "due_date": "2025-09-15"}', '/tasks/TSK-001-VM-017', 1, '2025-09-13 10:00:00', '2025-09-13 08:00:00'),

('NTF-005', 'USR-009-DEV-BE02', NULL, 'task_due_soon', 'Task sáº¯p Ä‘áº¿n háº¡n', 'Task "MoMo Integration" sáº½ Ä‘áº¿n háº¡n trong 3 ngÃ y', '{"task_id": "TSK-001-VM-018", "due_date": "2025-09-20"}', '/tasks/TSK-001-VM-018', 0, NULL, '2025-09-17 08:00:00'),

-- Task overdue notifications
('NTF-006', 'USR-008-DEV-BE01', NULL, 'task_overdue', 'Task Ä‘Ã£ quÃ¡ háº¡n!', 'Task "Fix Payment Gateway Timeout" Ä‘Ã£ quÃ¡ háº¡n 3 ngÃ y', '{"task_id": "TSK-001-VM-OVD1", "due_date": "2026-01-05"}', '/tasks/TSK-001-VM-OVD1', 0, NULL, '2026-01-08 08:00:00'),

('NTF-007', 'USR-012-DEV-FE01', NULL, 'task_overdue', 'Task Ä‘Ã£ quÃ¡ háº¡n!', 'Task "Mobile Responsive Issues" Ä‘Ã£ quÃ¡ háº¡n 5 ngÃ y', '{"task_id": "TSK-001-VM-OVD2", "due_date": "2026-01-03"}', '/tasks/TSK-001-VM-OVD2', 0, NULL, '2026-01-08 08:00:00'),

('NTF-008', 'USR-008-DEV-BE01', NULL, 'task_overdue', 'Task Ä‘Ã£ quÃ¡ háº¡n!', 'Task "OTP Delivery Delay" Ä‘Ã£ quÃ¡ háº¡n 14 ngÃ y', '{"task_id": "TSK-002-FB-OVD1", "due_date": "2025-12-25"}', '/tasks/TSK-002-FB-OVD1', 1, '2026-01-02 09:00:00', '2026-01-08 08:00:00'),

-- Comment notifications
('NTF-009', 'USR-008-DEV-BE01', 'USR-005-MGR-TECH', 'comment_mention', 'Báº¡n Ä‘Æ°á»£c nháº¯c Ä‘áº¿n trong bÃ¬nh luáº­n', '@Tuáº¥n check ká»¹ pháº§n signature verification nhÃ©', '{"task_id": "TSK-001-VM-017", "comment_id": "CMT-003"}', '/tasks/TSK-001-VM-017#comment-CMT-003', 1, '2025-08-25 10:30:00', '2025-08-25 10:15:00'),

('NTF-010', 'USR-008-DEV-BE01', 'USR-003-MGR-PM01', 'comment_reply', 'CÃ³ pháº£n há»“i cho bÃ¬nh luáº­n cá»§a báº¡n', 'Task nÃ y Ä‘ang quÃ¡ háº¡n, cáº§n Æ°u tiÃªn xá»­ lÃ½ gáº¥p!', '{"task_id": "TSK-001-VM-OVD1", "comment_id": "CMT-007"}', '/tasks/TSK-001-VM-OVD1#comment-CMT-007', 1, '2026-01-06 09:30:00', '2026-01-06 09:00:00'),

-- Project notifications
('NTF-011', 'USR-008-DEV-BE01', 'USR-003-MGR-PM01', 'project_update', 'Cáº­p nháº­t dá»± Ã¡n', 'Dá»± Ã¡n VinMart Ä‘Ã£ cáº­p nháº­t tiáº¿n Ä‘á»™: 72%', '{"project_id": "PRJ-001-VINMART", "progress": 72}', '/projects/PRJ-001-VINMART', 1, '2025-12-01 10:00:00', '2025-12-01 09:00:00'),

('NTF-012', 'USR-015-DEV-MOB1', 'USR-004-MGR-PM02', 'project_update', 'Cáº­p nháº­t dá»± Ã¡n', 'Dá»± Ã¡n FPT Bank Ä‘Ã£ cáº­p nháº­t tiáº¿n Ä‘á»™: 45%', '{"project_id": "PRJ-002-FPTBANK", "progress": 45}', '/projects/PRJ-002-FPTBANK', 0, NULL, '2025-11-15 09:00:00'),

-- Task status change notifications
('NTF-013', 'USR-003-MGR-PM01', 'USR-008-DEV-BE01', 'task_status_changed', 'Task Ä‘Ã£ chuyá»ƒn tráº¡ng thÃ¡i', 'Task "Product Management API" Ä‘Ã£ chuyá»ƒn sang Done', '{"task_id": "TSK-001-VM-007", "old_status": "in_review", "new_status": "done"}', '/tasks/TSK-001-VM-007', 1, '2025-07-04 17:30:00', '2025-07-04 17:00:00'),

('NTF-014', 'USR-005-MGR-TECH', 'USR-010-DEV-BE03', 'task_status_changed', 'Task Ä‘Ã£ chuyá»ƒn tráº¡ng thÃ¡i', 'Task "COD Payment Option" Ä‘Ã£ chuyá»ƒn sang In Review', '{"task_id": "TSK-001-VM-019", "old_status": "in_progress", "new_status": "in_review"}', '/tasks/TSK-001-VM-019', 0, NULL, '2025-09-14 16:00:00'),

-- System notifications
('NTF-015', 'USR-001-ADMIN-CEO', NULL, 'system', 'BÃ¡o cÃ¡o tuáº§n', 'BÃ¡o cÃ¡o tiáº¿n Ä‘á»™ dá»± Ã¡n tuáº§n 01/2025 Ä‘Ã£ sáºµn sÃ ng', '{"report_type": "weekly", "week": "2025-W01"}', '/reports/weekly/2025-W01', 0, NULL, '2026-01-06 08:00:00'),

('NTF-016', 'USR-002-ADMIN-CTO', NULL, 'system', 'Cáº£nh bÃ¡o há»‡ thá»‘ng', 'CÃ³ 3 tasks Ä‘ang quÃ¡ háº¡n cáº§n xá»­ lÃ½', '{"overdue_count": 3}', '/tasks?filter=overdue', 0, NULL, '2026-01-08 08:00:00'),

-- Document share notifications
('NTF-017', 'USR-023-GUEST-VIN', 'USR-003-MGR-PM01', 'document_shared', 'TÃ i liá»‡u Ä‘Æ°á»£c chia sáº»', 'Báº¡n Ä‘Æ°á»£c chia sáº» tÃ i liá»‡u "VinMart_PRD_v2.1.pdf"', '{"document_id": "DOC-VM-001"}', '/documents/DOC-VM-001', 1, '2025-06-01 10:00:00', '2025-06-01 09:00:00'),

('NTF-018', 'USR-024-GUEST-FPT', 'USR-004-MGR-PM02', 'document_shared', 'TÃ i liá»‡u Ä‘Æ°á»£c chia sáº»', 'Báº¡n Ä‘Æ°á»£c chia sáº» tÃ i liá»‡u "Security_Architecture.pdf"', '{"document_id": "DOC-FB-002"}', '/documents/DOC-FB-002', 1, '2025-08-10 09:30:00', '2025-08-10 09:00:00'),

-- Meeting reminder notifications
('NTF-019', 'USR-008-DEV-BE01', NULL, 'meeting_reminder', 'Nháº¯c nhá»Ÿ cuá»™c há»p', 'Sprint Planning VinMart sáº½ báº¯t Ä‘áº§u trong 30 phÃºt', '{"event_id": "EVT-001"}', '/calendar/EVT-001', 1, '2026-01-06 09:00:00', '2026-01-06 08:30:00'),

('NTF-020', 'USR-015-DEV-MOB1', NULL, 'meeting_reminder', 'Nháº¯c nhá»Ÿ cuá»™c há»p', 'Daily Standup FPT Bank sáº½ báº¯t Ä‘áº§u trong 15 phÃºt', '{"event_id": "EVT-003"}', '/calendar/EVT-003', 0, NULL, '2026-01-08 08:45:00');


-- =============================================
-- 13. ACTIVITY_LOGS - Lá»ŠCH Sá»¬ HOáº T Äá»˜NG
-- =============================================

INSERT INTO `activity_logs` (`id`, `user_id`, `entity_type`, `entity_id`, `action`, `old_values`, `new_values`, `ip_address`, `user_agent`, `created_at`) VALUES

-- Task activities
('ACT-001', 'USR-008-DEV-BE01', 'task', 'TSK-001-VM-017', 'status_changed', '{"status": "todo"}', '{"status": "in_progress"}', '192.168.1.100', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/120.0', '2025-08-25 09:00:00'),

('ACT-002', 'USR-008-DEV-BE01', 'task', 'TSK-001-VM-007', 'status_changed', '{"status": "in_progress"}', '{"status": "in_review"}', '192.168.1.100', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/120.0', '2025-07-03 16:00:00'),

('ACT-003', 'USR-005-MGR-TECH', 'task', 'TSK-001-VM-007', 'status_changed', '{"status": "in_review"}', '{"status": "done"}', '192.168.1.101', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) Safari/605.1', '2025-07-04 17:00:00'),

('ACT-004', 'USR-015-DEV-MOB1', 'task', 'TSK-002-FB-002', 'status_changed', '{"status": "in_progress"}', '{"status": "done"}', '192.168.1.102', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) Chrome/120.0', '2025-09-03 16:30:00'),

('ACT-005', 'USR-009-DEV-BE02', 'task', 'TSK-003-HR-013', 'updated', '{"estimated_hours": 40}', '{"estimated_hours": 48}', '192.168.1.103', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Firefox/121.0', '2025-07-25 10:00:00'),

-- Task assignment activities
('ACT-006', 'USR-005-MGR-TECH', 'task', 'TSK-001-VM-017', 'assigned', NULL, '{"assignee_id": "USR-008-DEV-BE01"}', '192.168.1.101', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) Safari/605.1', '2025-08-20 09:00:00'),

('ACT-007', 'USR-006-MGR-SCRUM', 'task', 'TSK-004-MC-004', 'assigned', NULL, '{"assignee_id": "USR-013-DEV-FE02"}', '192.168.1.104', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/120.0', '2025-10-20 09:00:00'),

-- Project activities
('ACT-008', 'USR-003-MGR-PM01', 'project', 'PRJ-001-VINMART', 'progress_updated', '{"progress": 68}', '{"progress": 72}', '192.168.1.105', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/120.0', '2025-12-01 09:00:00'),

('ACT-009', 'USR-004-MGR-PM02', 'project', 'PRJ-002-FPTBANK', 'progress_updated', '{"progress": 40}', '{"progress": 45}', '192.168.1.106', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) Chrome/120.0', '2025-11-15 09:00:00'),

('ACT-010', 'USR-002-ADMIN-CTO', 'project', 'PRJ-007-DEVOPS', 'status_changed', '{"status": "active"}', '{"status": "completed"}', '192.168.1.107', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) Safari/605.1', '2025-06-30 17:00:00'),

-- User activities
('ACT-011', 'USR-001-ADMIN-CEO', 'user', 'USR-025-INACTIVE', 'deactivated', '{"is_active": 1}', '{"is_active": 0}', '192.168.1.108', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/120.0', '2025-09-30 17:00:00'),

('ACT-012', 'USR-003-MGR-PM01', 'user', 'USR-011-DEV-BE04', 'role_changed', '{"role": "member"}', '{"role": "member"}', '192.168.1.105', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/120.0', '2025-08-01 09:00:00'),

-- Document activities
('ACT-013', 'USR-022-BA', 'document', 'DOC-VM-001', 'uploaded', NULL, '{"name": "VinMart_PRD_v2.1.pdf", "size": 2548000}', '192.168.1.109', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/120.0', '2025-05-25 08:00:00'),

('ACT-014', 'USR-003-MGR-PM01', 'document', 'DOC-VM-001', 'shared', NULL, '{"shared_with": "USR-023-GUEST-VIN", "permission": "view"}', '192.168.1.105', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/120.0', '2025-06-01 09:00:00'),

('ACT-015', 'USR-017-DESIGN01', 'document', 'DOC-VM-004', 'uploaded', NULL, '{"name": "UI_Mockups.fig", "size": 15680000}', '192.168.1.110', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) Chrome/120.0', '2025-06-05 08:00:00'),

-- Comment activities
('ACT-016', 'USR-003-MGR-PM01', 'comment', 'CMT-001', 'created', NULL, '{"entity_type": "task", "entity_id": "TSK-001-VM-017"}', '192.168.1.105', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/120.0', '2025-08-20 10:00:00'),

('ACT-017', 'USR-005-MGR-TECH', 'comment', 'CMT-003', 'created', NULL, '{"entity_type": "task", "entity_id": "TSK-001-VM-017", "parent_id": "CMT-002"}', '192.168.1.101', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) Safari/605.1', '2025-08-25 10:15:00'),

-- Login activities
('ACT-018', 'USR-001-ADMIN-CEO', 'user', 'USR-001-ADMIN-CEO', 'login', NULL, '{"ip": "192.168.1.108"}', '192.168.1.108', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/120.0', '2026-01-08 08:30:00'),

('ACT-019', 'USR-008-DEV-BE01', 'user', 'USR-008-DEV-BE01', 'login', NULL, '{"ip": "192.168.1.100"}', '192.168.1.100', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/120.0', '2026-01-08 08:00:00'),

('ACT-020', 'USR-005-MGR-TECH', 'user', 'USR-005-MGR-TECH', 'login', NULL, '{"ip": "192.168.1.101"}', '192.168.1.101', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) Safari/605.1', '2026-01-08 10:00:00');


-- =============================================
-- 14. CALENDAR_EVENTS - Sá»° KIá»†N & CUá»˜C Há»ŒP
-- =============================================

INSERT INTO `calendar_events` (`id`, `title`, `description`, `type`, `color`, `start_time`, `end_time`, `is_all_day`, `location`, `project_id`, `task_id`, `created_by`, `created_at`) VALUES

-- Sprint meetings - VinMart
('EVT-001', 'Sprint Planning - VinMart Sprint 4', 'LÃªn káº¿ hoáº¡ch Sprint 4: Payment & Advanced Features', 'meeting', '#10B981', '2026-01-06 09:00:00', '2026-01-06 11:00:00', 0, 'PhÃ²ng há»p A - Táº§ng 5', 'PRJ-001-VINMART', NULL, 'USR-006-MGR-SCRUM', '2026-01-03 08:00:00'),

('EVT-002', 'Sprint Review - VinMart Sprint 3', 'Demo cÃ¡c tÃ­nh nÄƒng Ä‘Ã£ hoÃ n thÃ nh trong Sprint 3', 'meeting', '#10B981', '2026-01-10 14:00:00', '2026-01-10 16:00:00', 0, 'PhÃ²ng há»p lá»›n - Táº§ng 3', 'PRJ-001-VINMART', NULL, 'USR-006-MGR-SCRUM', '2026-01-03 08:00:00'),

-- Daily standups - FPT Bank
('EVT-003', 'Daily Standup - FPT Bank', 'Daily standup meeting', 'meeting', '#3B82F6', '2026-01-08 09:00:00', '2026-01-08 09:15:00', 0, 'Online - Google Meet', 'PRJ-002-FPTBANK', NULL, 'USR-004-MGR-PM02', '2026-01-07 08:00:00'),

('EVT-004', 'Daily Standup - FPT Bank', 'Daily standup meeting', 'meeting', '#3B82F6', '2026-01-09 09:00:00', '2026-01-09 09:15:00', 0, 'Online - Google Meet', 'PRJ-002-FPTBANK', NULL, 'USR-004-MGR-PM02', '2026-01-07 08:00:00'),

-- Client meetings
('EVT-005', 'Demo Meeting - VinGroup', 'Demo tiáº¿n Ä‘á»™ dá»± Ã¡n cho khÃ¡ch hÃ ng VinGroup', 'meeting', '#F59E0B', '2026-01-15 10:00:00', '2026-01-15 12:00:00', 0, 'VinGroup Office - Quáº­n 1', 'PRJ-001-VINMART', NULL, 'USR-003-MGR-PM01', '2026-01-05 08:00:00'),

('EVT-006', 'UAT Kickoff - FPT Bank', 'Khá»Ÿi Ä‘á»™ng giai Ä‘oáº¡n UAT vá»›i khÃ¡ch hÃ ng FPT', 'meeting', '#3B82F6', '2026-01-20 09:00:00', '2026-01-20 11:00:00', 0, 'FPT Tower - Quáº­n 7', 'PRJ-002-FPTBANK', NULL, 'USR-004-MGR-PM02', '2026-01-08 08:00:00'),

-- Deadlines
('EVT-007', 'Deadline: VNPay Integration', 'HoÃ n thÃ nh tÃ­ch há»£p VNPay', 'deadline', '#EF4444', '2026-01-15 17:00:00', '2026-01-15 17:00:00', 0, NULL, 'PRJ-001-VINMART', 'TSK-001-VM-017', 'USR-003-MGR-PM01', '2025-08-20 08:00:00'),

('EVT-008', 'Deadline: HRMS Go-live', 'Go-live há»‡ thá»‘ng HRMS ná»™i bá»™', 'deadline', '#8B5CF6', '2026-01-31 17:00:00', '2026-01-31 17:00:00', 0, NULL, 'PRJ-003-HRMS', NULL, 'USR-003-MGR-PM01', '2025-02-15 08:00:00'),

-- Reminders
('EVT-009', 'Reminder: Submit Weekly Report', 'Ná»™p bÃ¡o cÃ¡o tuáº§n cho CEO', 'reminder', '#6B7280', '2026-01-10 16:00:00', '2026-01-10 16:30:00', 0, NULL, NULL, NULL, 'USR-003-MGR-PM01', '2026-01-06 08:00:00'),

('EVT-010', 'Reminder: Code Review Session', 'Review code cho module Payment', 'reminder', '#7C3AED', '2026-01-09 14:00:00', '2026-01-09 15:00:00', 0, 'Online - Zoom', 'PRJ-001-VINMART', NULL, 'USR-005-MGR-TECH', '2026-01-07 08:00:00'),

-- Company events
('EVT-011', 'Táº¿t NguyÃªn ÄÃ¡n 2025', 'Nghá»‰ Táº¿t NguyÃªn ÄÃ¡n', 'event', '#DC2626', '2026-01-28 00:00:00', '2026-02-02 23:59:59', 1, NULL, NULL, NULL, 'USR-001-ADMIN-CEO', '2026-01-01 08:00:00'),

('EVT-012', 'Team Building Q1/2025', 'Team building quÃ½ 1 táº¡i VÅ©ng TÃ u', 'event', '#EC4899', '2026-02-15 07:00:00', '2026-02-16 18:00:00', 0, 'Resort VÅ©ng TÃ u', NULL, NULL, 'USR-001-ADMIN-CEO', '2026-01-05 08:00:00'),

-- Training sessions
('EVT-013', 'Training: AWS Solutions Architect', 'ÄÃ o táº¡o AWS cho team DevOps', 'event', '#059669', '2026-01-13 09:00:00', '2026-01-13 17:00:00', 0, 'PhÃ²ng Ä‘Ã o táº¡o - Táº§ng 2', NULL, NULL, 'USR-002-ADMIN-CTO', '2026-01-06 08:00:00'),

('EVT-014', 'Workshop: React Performance', 'Workshop tá»‘i Æ°u hiá»‡u nÄƒng React', 'event', '#3B82F6', '2026-01-17 14:00:00', '2026-01-17 17:00:00', 0, 'PhÃ²ng há»p B - Táº§ng 5', NULL, NULL, 'USR-005-MGR-TECH', '2026-01-08 08:00:00'),

-- Retrospective
('EVT-015', 'Sprint Retrospective - VinMart', 'Retrospective Sprint 3', 'meeting', '#10B981', '2026-01-10 16:30:00', '2026-01-10 17:30:00', 0, 'PhÃ²ng há»p A - Táº§ng 5', 'PRJ-001-VINMART', NULL, 'USR-006-MGR-SCRUM', '2026-01-03 08:00:00');

-- =============================================
-- 15. EVENT_ATTENDEES - NGÆ¯á»œI THAM Dá»° Sá»° KIá»†N
-- =============================================

INSERT INTO `event_attendees` (`event_id`, `user_id`, `status`, `responded_at`) VALUES

-- Sprint Planning VinMart
('EVT-001', 'USR-003-MGR-PM01', 'accepted', '2026-01-03 09:00:00'),
('EVT-001', 'USR-005-MGR-TECH', 'accepted', '2026-01-03 09:30:00'),
('EVT-001', 'USR-006-MGR-SCRUM', 'accepted', '2026-01-03 08:00:00'),
('EVT-001', 'USR-008-DEV-BE01', 'accepted', '2026-01-03 10:00:00'),
('EVT-001', 'USR-009-DEV-BE02', 'accepted', '2026-01-03 10:15:00'),
('EVT-001', 'USR-012-DEV-FE01', 'accepted', '2026-01-03 11:00:00'),
('EVT-001', 'USR-013-DEV-FE02', 'tentative', '2026-01-03 14:00:00'),
('EVT-001', 'USR-019-QA01', 'accepted', '2026-01-03 15:00:00'),

-- Sprint Review VinMart
('EVT-002', 'USR-003-MGR-PM01', 'accepted', '2026-01-03 09:00:00'),
('EVT-002', 'USR-005-MGR-TECH', 'accepted', '2026-01-03 09:30:00'),
('EVT-002', 'USR-023-GUEST-VIN', 'pending', NULL),

-- Daily Standup FPT Bank
('EVT-003', 'USR-004-MGR-PM02', 'accepted', '2026-01-07 08:00:00'),
('EVT-003', 'USR-008-DEV-BE01', 'accepted', '2026-01-07 08:30:00'),
('EVT-003', 'USR-015-DEV-MOB1', 'accepted', '2026-01-07 08:45:00'),
('EVT-003', 'USR-016-DEV-MOB2', 'accepted', '2026-01-07 09:00:00'),

-- Demo VinGroup
('EVT-005', 'USR-001-ADMIN-CEO', 'accepted', '2026-01-05 09:00:00'),
('EVT-005', 'USR-003-MGR-PM01', 'accepted', '2026-01-05 08:00:00'),
('EVT-005', 'USR-005-MGR-TECH', 'accepted', '2026-01-05 09:30:00'),
('EVT-005', 'USR-023-GUEST-VIN', 'accepted', '2026-01-05 10:00:00'),

-- Team Building
('EVT-012', 'USR-001-ADMIN-CEO', 'accepted', '2026-01-05 09:00:00'),
('EVT-012', 'USR-002-ADMIN-CTO', 'accepted', '2026-01-05 09:30:00'),
('EVT-012', 'USR-003-MGR-PM01', 'accepted', '2026-01-05 10:00:00'),
('EVT-012', 'USR-008-DEV-BE01', 'accepted', '2026-01-06 08:00:00'),
('EVT-012', 'USR-012-DEV-FE01', 'tentative', '2026-01-06 09:00:00'),
('EVT-012', 'USR-017-DESIGN01', 'declined', '2026-01-06 10:00:00'),

-- AWS Training
('EVT-013', 'USR-021-DEVOPS', 'accepted', '2026-01-06 09:00:00'),
('EVT-013', 'USR-008-DEV-BE01', 'accepted', '2026-01-06 10:00:00'),
('EVT-013', 'USR-009-DEV-BE02', 'pending', NULL);


-- =============================================
-- 16. USER_SETTINGS - CÃ€I Äáº¶T NGÆ¯á»œI DÃ™NG
-- =============================================

INSERT INTO `user_settings` (`user_id`, `theme`, `language`, `timezone`, `notification_email`, `notification_push`, `notification_task_assigned`, `notification_task_due`, `notification_comment`, `notification_mention`, `updated_at`) VALUES

('USR-001-ADMIN-CEO', 'light', 'vi', 'Asia/Ho_Chi_Minh', 1, 1, 1, 1, 0, 1, '2025-01-15 08:00:00'),
('USR-002-ADMIN-CTO', 'dark', 'vi', 'Asia/Ho_Chi_Minh', 1, 1, 1, 1, 1, 1, '2025-02-01 08:00:00'),
('USR-003-MGR-PM01', 'system', 'vi', 'Asia/Ho_Chi_Minh', 1, 1, 1, 1, 1, 1, '2025-03-01 08:00:00'),
('USR-004-MGR-PM02', 'light', 'vi', 'Asia/Ho_Chi_Minh', 1, 1, 1, 1, 1, 1, '2025-03-15 08:00:00'),
('USR-005-MGR-TECH', 'dark', 'en', 'Asia/Ho_Chi_Minh', 1, 1, 1, 1, 1, 1, '2025-02-20 08:00:00'),
('USR-006-MGR-SCRUM', 'system', 'vi', 'Asia/Ho_Chi_Minh', 1, 1, 1, 1, 1, 1, '2025-04-10 08:00:00'),
('USR-007-MGR-DESIGN', 'light', 'vi', 'Asia/Ho_Chi_Minh', 1, 0, 1, 1, 1, 1, '2025-03-25 08:00:00'),
('USR-008-DEV-BE01', 'dark', 'en', 'Asia/Ho_Chi_Minh', 1, 1, 1, 1, 1, 1, '2025-05-15 08:00:00'),
('USR-009-DEV-BE02', 'dark', 'vi', 'Asia/Ho_Chi_Minh', 1, 1, 1, 1, 0, 1, '2025-06-10 08:00:00'),
('USR-010-DEV-BE03', 'system', 'vi', 'Asia/Ho_Chi_Minh', 1, 1, 1, 1, 1, 1, '2025-07-15 08:00:00'),
('USR-011-DEV-BE04', 'light', 'vi', 'Asia/Ho_Chi_Minh', 1, 1, 1, 1, 1, 1, '2025-01-20 08:00:00'),
('USR-012-DEV-FE01', 'dark', 'en', 'Asia/Ho_Chi_Minh', 1, 1, 1, 1, 1, 1, '2025-05-20 08:00:00'),
('USR-013-DEV-FE02', 'system', 'vi', 'Asia/Ho_Chi_Minh', 1, 1, 1, 1, 1, 1, '2025-08-10 08:00:00'),
('USR-014-DEV-FE03', 'light', 'vi', 'Asia/Ho_Chi_Minh', 1, 1, 1, 1, 1, 1, '2025-03-10 08:00:00'),
('USR-015-DEV-MOB1', 'dark', 'vi', 'Asia/Ho_Chi_Minh', 1, 1, 1, 1, 1, 1, '2025-09-10 08:00:00'),
('USR-016-DEV-MOB2', 'dark', 'vi', 'Asia/Ho_Chi_Minh', 1, 1, 1, 1, 1, 1, '2025-09-20 08:00:00'),
('USR-017-DESIGN01', 'light', 'vi', 'Asia/Ho_Chi_Minh', 1, 0, 1, 1, 1, 1, '2025-06-20 08:00:00'),
('USR-018-DESIGN02', 'light', 'vi', 'Asia/Ho_Chi_Minh', 1, 1, 1, 1, 1, 1, '2025-10-10 08:00:00'),
('USR-019-QA01', 'system', 'vi', 'Asia/Ho_Chi_Minh', 1, 1, 1, 1, 1, 1, '2025-04-20 08:00:00'),
('USR-020-QA02', 'light', 'vi', 'Asia/Ho_Chi_Minh', 1, 1, 1, 1, 1, 1, '2025-11-10 08:00:00'),
('USR-021-DEVOPS', 'dark', 'en', 'Asia/Ho_Chi_Minh', 1, 1, 1, 1, 0, 1, '2025-07-20 08:00:00'),
('USR-022-BA', 'light', 'vi', 'Asia/Ho_Chi_Minh', 1, 1, 1, 1, 1, 1, '2025-08-20 08:00:00'),
('USR-023-GUEST-VIN', 'light', 'vi', 'Asia/Ho_Chi_Minh', 1, 0, 0, 0, 1, 1, '2025-06-05 08:00:00'),
('USR-024-GUEST-FPT', 'light', 'vi', 'Asia/Ho_Chi_Minh', 1, 0, 0, 0, 1, 1, '2025-07-05 08:00:00');

-- =============================================
-- HOÃ€N Táº¤T
-- =============================================

SET FOREIGN_KEY_CHECKS = 1;
SET SQL_MODE=@OLD_SQL_MODE;

-- =============================================
-- THá»NG KÃŠ Dá»® LIá»†U
-- =============================================
/*
ðŸ“Š Tá»”NG QUAN Dá»® LIá»†U SEED:

â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”¬â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”
â”‚ Báº£ng                â”‚ Sá»‘ lÆ°á»£ng â”‚
â”œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”¼â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”¤
â”‚ users               â”‚ 25       â”‚
â”‚ labels              â”‚ 12       â”‚
â”‚ projects            â”‚ 10       â”‚
â”‚ project_members     â”‚ 55       â”‚
â”‚ tasks               â”‚ 133      â”‚
â”‚ task_assignees      â”‚ 105      â”‚
â”‚ task_labels         â”‚ 35       â”‚
â”‚ task_checklists     â”‚ 28       â”‚
â”‚ documents           â”‚ 20       â”‚
â”‚ document_shares     â”‚ 10       â”‚
â”‚ comments            â”‚ 28       â”‚
â”‚ notifications       â”‚ 20       â”‚
â”‚ activity_logs       â”‚ 20       â”‚
â”‚ calendar_events     â”‚ 15       â”‚
â”‚ event_attendees     â”‚ 30       â”‚
â”‚ user_settings       â”‚ 24       â”‚
â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”´â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜

ðŸ“ˆ PHÃ‚N Bá» TASKS THEO TRáº NG THÃI:
- Backlog:     35 tasks (26%)
- Todo:        22 tasks (17%)
- In Progress: 18 tasks (14%)
- In Review:    4 tasks (3%)
- Done:        54 tasks (40%)

âš ï¸ TÃŒNH HUá»NG Äáº¶C BIá»†T:
- 3 tasks quÃ¡ háº¡n (overdue)
- 3 tasks khÃ´ng cÃ³ assignee
- 1 nhÃ¢n viÃªn Ä‘Ã£ nghá»‰ viá»‡c (inactive)
- 2 khÃ¡ch hÃ ng (guest) vá»›i quyá»n viewer
- Tasks cÃ³ nhiá»u assignees (pair programming)
- Dá»± Ã¡n Ä‘Ã£ hoÃ n thÃ nh, Ä‘ang triá»ƒn khai, táº¡m dá»«ng, Ä‘Ã£ há»§y

ðŸŽ¯ PHá»¤C Vá»¤ DEMO:
- Dashboard thá»‘ng kÃª
- BÃ¡o cÃ¡o hiá»‡u suáº¥t
- Quáº£n lÃ½ dá»± Ã¡n Ä‘a dáº¡ng
- Workflow thá»±c táº¿
- PhÃ¢n quyá»n ngÆ°á»i dÃ¹ng
*/

