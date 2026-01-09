-- =============================================
-- TASKFLOW PROFESSIONAL SEED DATA
-- Mô phỏng: Công ty Phần mềm SaigonTech Solutions
-- Phiên bản: 2.0 - Dữ liệu chuẩn cho Demo & Bảo vệ đồ án
-- Ngày tạo: 08/01/2026
-- =============================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- Xóa dữ liệu cũ (theo thứ tự phụ thuộc)
DELETE FROM event_attendees;
DELETE FROM calendar_events;
DELETE FROM notifications;
DELETE FROM activity_logs;
DELETE FROM comments;
DELETE FROM document_shares;
DELETE FROM documents;
DELETE FROM task_checklists;
DELETE FROM task_labels;
DELETE FROM task_assignees;
DELETE FROM tasks;
DELETE FROM project_members;
DELETE FROM projects;
DELETE FROM labels;
DELETE FROM user_settings;
DELETE FROM users;

-- =============================================
-- 1. USERS - Đội ngũ SaigonTech Solutions
-- =============================================
-- Password cho tất cả: password123
-- Hash: $2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi

INSERT INTO `users` (`id`, `email`, `password_hash`, `full_name`, `role`, `department`, `position`, `phone`, `is_active`, `last_login_at`, `created_at`) VALUES

-- ===== BAN GIÁM ĐỐC (2 Admin) =====
('u-admin-001', 'giamđoc@saigontech.vn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 
 'Nguyễn Văn Thành', 'admin', 'Ban Giám đốc', 'Giám đốc điều hành (CEO)', '0903100001', 1, '2026-01-08 08:30:00', '2024-01-01 08:00:00'),

('u-admin-002', 'phogiamđoc@saigontech.vn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 
 'Trần Thị Hương', 'admin', 'Ban Giám đốc', 'Phó Giám đốc (COO)', '0903100002', 1, '2026-01-07 17:45:00', '2024-01-15 08:00:00'),

-- ===== QUẢN LÝ DỰ ÁN (3 Manager) =====
('u-pm-001', 'pm.minh@saigontech.vn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 
 'Lê Quốc Minh', 'manager', 'Quản lý dự án', 'Project Manager Senior', '0903200001', 1, '2026-01-08 09:15:00', '2024-02-01 08:00:00'),

('u-pm-002', 'pm.lan@saigontech.vn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 
 'Phạm Thị Lan', 'manager', 'Quản lý dự án', 'Project Manager', '0903200002', 1, '2026-01-08 08:00:00', '2024-03-15 08:00:00'),

('u-pm-003', 'pm.hung@saigontech.vn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 
 'Võ Văn Hùng', 'manager', 'Quản lý dự án', 'Scrum Master', '0903200003', 1, '2026-01-06 16:30:00', '2024-05-01 08:00:00'),

-- ===== TECH LEAD (2 Manager) =====
('u-lead-001', 'lead.backend@saigontech.vn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 
 'Hoàng Đức Anh', 'manager', 'Phát triển phần mềm', 'Tech Lead Backend', '0903300001', 1, '2026-01-08 10:00:00', '2024-02-15 08:00:00'),

('u-lead-002', 'lead.frontend@saigontech.vn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 
 'Nguyễn Thị Mai', 'manager', 'Phát triển phần mềm', 'Tech Lead Frontend', '0903300002', 1, '2026-01-08 09:30:00', '2024-03-01 08:00:00'),

-- ===== BACKEND DEVELOPERS (4 Member) =====
('u-dev-be-001', 'dev.khanh@saigontech.vn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 
 'Trần Quốc Khánh', 'member', 'Phát triển phần mềm', 'Senior Backend Developer', '0903400001', 1, '2026-01-08 08:45:00', '2024-04-01 08:00:00'),

('u-dev-be-002', 'dev.tuan@saigontech.vn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 
 'Lê Văn Tuấn', 'member', 'Phát triển phần mềm', 'Backend Developer', '0903400002', 1, '2026-01-08 09:00:00', '2024-06-01 08:00:00'),

('u-dev-be-003', 'dev.hoa@saigontech.vn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 
 'Nguyễn Thị Hoa', 'member', 'Phát triển phần mềm', 'Backend Developer', '0903400003', 1, '2026-01-07 17:00:00', '2024-08-01 08:00:00'),

('u-dev-be-004', 'dev.long@saigontech.vn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 
 'Phạm Hoàng Long', 'member', 'Phát triển phần mềm', 'Junior Backend Developer', '0903400004', 1, '2026-01-08 08:30:00', '2025-01-15 08:00:00'),

-- ===== FRONTEND DEVELOPERS (3 Member) =====
('u-dev-fe-001', 'dev.trang@saigontech.vn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 
 'Vũ Thu Trang', 'member', 'Phát triển phần mềm', 'Senior Frontend Developer', '0903500001', 1, '2026-01-08 09:15:00', '2024-04-15 08:00:00'),

('u-dev-fe-002', 'dev.nam@saigontech.vn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 
 'Đỗ Văn Nam', 'member', 'Phát triển phần mềm', 'Frontend Developer', '0903500002', 1, '2026-01-08 08:00:00', '2024-07-01 08:00:00'),

('u-dev-fe-003', 'dev.linh@saigontech.vn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 
 'Trần Thùy Linh', 'member', 'Phát triển phần mềm', 'Junior Frontend Developer', '0903500003', 1, '2026-01-07 18:00:00', '2025-02-01 08:00:00'),

-- ===== MOBILE DEVELOPERS (2 Member) =====
('u-dev-mb-001', 'dev.ios@saigontech.vn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 
 'Nguyễn Minh Đức', 'member', 'Phát triển phần mềm', 'iOS Developer', '0903600001', 1, '2026-01-08 08:30:00', '2024-05-01 08:00:00'),

('u-dev-mb-002', 'dev.android@saigontech.vn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 
 'Lê Thành Công', 'member', 'Phát triển phần mềm', 'Android Developer', '0903600002', 1, '2026-01-08 09:00:00', '2024-05-15 08:00:00'),

-- ===== UI/UX DESIGNERS (2 Member) =====
('u-design-001', 'design.huong@saigontech.vn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 
 'Lê Thị Hương', 'member', 'Thiết kế', 'Senior UI/UX Designer', '0903700001', 1, '2026-01-08 09:30:00', '2024-03-01 08:00:00'),

('u-design-002', 'design.phuc@saigontech.vn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 
 'Trần Văn Phúc', 'member', 'Thiết kế', 'UI Designer', '0903700002', 1, '2026-01-07 17:30:00', '2024-09-01 08:00:00'),

-- ===== QA/QC TEAM (2 Member) =====
('u-qa-001', 'qa.thao@saigontech.vn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 
 'Phạm Thị Thảo', 'member', 'Kiểm thử', 'QA Lead', '0903800001', 1, '2026-01-08 08:45:00', '2024-04-01 08:00:00'),

('u-qa-002', 'qa.dat@saigontech.vn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 
 'Nguyễn Tiến Đạt', 'member', 'Kiểm thử', 'QA Engineer', '0903800002', 1, '2026-01-08 09:00:00', '2024-10-01 08:00:00'),

-- ===== DEVOPS (1 Member) =====
('u-devops-001', 'devops.tai@saigontech.vn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 
 'Lê Văn Tài', 'member', 'Vận hành', 'DevOps Engineer', '0903900001', 1, '2026-01-08 07:30:00', '2024-06-01 08:00:00'),

-- ===== BUSINESS ANALYST (1 Member) =====
('u-ba-001', 'ba.nga@saigontech.vn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 
 'Hoàng Thị Nga', 'member', 'Phân tích nghiệp vụ', 'Business Analyst', '0904000001', 1, '2026-01-08 08:00:00', '2024-07-01 08:00:00'),

-- ===== KHÁCH HÀNG / STAKEHOLDER (2 Guest) =====
('u-client-001', 'client.vingroup@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 
 'Nguyễn Hoàng Việt', 'guest', 'Khách hàng', 'Product Owner - VinGroup', '0909111001', 1, '2026-01-05 14:00:00', '2025-06-01 08:00:00'),

('u-client-002', 'client.fpt@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 
 'Trần Minh Quân', 'guest', 'Khách hàng', 'IT Manager - FPT Retail', '0909111002', 1, '2026-01-03 10:30:00', '2025-08-01 08:00:00'),

-- ===== TÀI KHOẢN BỊ KHÓA (1 Inactive) =====
('u-inactive-001', 'nghiviec@saigontech.vn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 
 'Trần Văn Nghỉ', 'member', 'Phát triển phần mềm', 'Developer (Đã nghỉ)', '0909999001', 0, '2025-10-15 17:00:00', '2024-06-01 08:00:00');


-- =============================================
-- 2. LABELS - Nhãn phân loại công việc
-- =============================================
INSERT INTO `labels` (`id`, `name`, `color`, `description`) VALUES
('lbl-001', 'Bug', '#dc2626', 'Lỗi phần mềm cần sửa gấp'),
('lbl-002', 'Feature', '#16a34a', 'Tính năng mới'),
('lbl-003', 'Enhancement', '#2563eb', 'Cải tiến tính năng hiện có'),
('lbl-004', 'Hotfix', '#ea580c', 'Sửa lỗi khẩn cấp trên production'),
('lbl-005', 'Security', '#7c3aed', 'Liên quan đến bảo mật'),
('lbl-006', 'Performance', '#0891b2', 'Tối ưu hiệu năng'),
('lbl-007', 'UI/UX', '#db2777', 'Thiết kế giao diện'),
('lbl-008', 'API', '#059669', 'Phát triển API'),
('lbl-009', 'Database', '#d97706', 'Liên quan cơ sở dữ liệu'),
('lbl-010', 'Documentation', '#6366f1', 'Tài liệu hướng dẫn'),
('lbl-011', 'Testing', '#8b5cf6', 'Kiểm thử'),
('lbl-012', 'DevOps', '#0d9488', 'CI/CD, Infrastructure'),
('lbl-013', 'Research', '#64748b', 'Nghiên cứu, POC'),
('lbl-014', 'Refactor', '#f59e0b', 'Tái cấu trúc code'),
('lbl-015', 'Mobile', '#ec4899', 'Ứng dụng di động');

-- =============================================
-- 3. PROJECTS - Các dự án thực tế
-- =============================================
INSERT INTO `projects` (`id`, `name`, `description`, `color`, `icon`, `status`, `priority`, `progress`, `start_date`, `end_date`, `budget`, `created_by`, `created_at`) VALUES

-- DỰ ÁN 1: E-commerce Platform (Đang triển khai - 72%)
('prj-001', 'VinMart E-commerce Platform', 
'Xây dựng nền tảng thương mại điện tử toàn diện cho VinMart:
• Website bán hàng responsive (React + Next.js)
• Hệ thống quản lý sản phẩm, kho hàng
• Tích hợp thanh toán: VNPay, MoMo, ZaloPay
• Hệ thống quản lý đơn hàng và vận chuyển
• Chương trình khách hàng thân thiết
• Dashboard báo cáo doanh thu real-time

Tech Stack: React, Node.js, PostgreSQL, Redis, AWS', 
'#6366f1', 'shopping-cart', 'active', 'high', 72, 
'2025-06-01', '2026-03-31', 1200000000, 'u-admin-001', '2025-05-15 09:00:00'),

-- DỰ ÁN 2: Mobile Banking App (Đang triển khai - 45%)
('prj-002', 'FPT Mobile Banking App', 
'Phát triển ứng dụng ngân hàng số cho FPT Bank:
• Đăng ký tài khoản online với eKYC
• Chuyển tiền nhanh 24/7 (Napas)
• Thanh toán hóa đơn, nạp tiền điện thoại
• Quản lý thẻ tín dụng/ghi nợ
• Tiết kiệm online, vay tiêu dùng
• Bảo mật 2FA, sinh trắc học

Tech Stack: Flutter, Spring Boot, Oracle, Kubernetes', 
'#22c55e', 'smartphone', 'active', 'urgent', 45, 
'2025-09-01', '2026-06-30', 2500000000, 'u-admin-001', '2025-08-20 10:00:00'),

-- DỰ ÁN 3: HRMS Nội bộ (Sắp hoàn thành - 92%)
('prj-003', 'SaigonTech HRMS', 
'Hệ thống quản lý nhân sự nội bộ:
• Quản lý hồ sơ nhân viên
• Chấm công, tính lương tự động
• Quản lý nghỉ phép, OT
• Đánh giá KPI, review performance
• Tuyển dụng và onboarding
• Training management

Tech Stack: PHP Laravel, MySQL, Vue.js', 
'#f59e0b', 'users', 'active', 'medium', 92, 
'2025-03-01', '2026-01-31', 350000000, 'u-admin-002', '2025-02-15 08:00:00'),

-- DỰ ÁN 4: Healthcare Platform (Lên kế hoạch - 8%)
('prj-004', 'MedCare - Đặt lịch khám bệnh', 
'Nền tảng đặt lịch khám bệnh online:
• Tìm kiếm bác sĩ, phòng khám
• Đặt lịch hẹn online
• Tư vấn sức khỏe từ xa (Telemedicine)
• Quản lý hồ sơ bệnh án điện tử
• Thanh toán và bảo hiểm

Tech Stack: React Native, Node.js, MongoDB', 
'#ec4899', 'heart-pulse', 'planning', 'high', 8, 
'2026-02-01', '2026-09-30', 800000000, 'u-admin-001', '2025-12-01 09:00:00'),

-- DỰ ÁN 5: Logistics System (Tạm dừng - 35%)
('prj-005', 'LogiTrack - Quản lý vận chuyển', 
'Hệ thống quản lý logistics và vận chuyển:
• Quản lý đơn hàng, lộ trình
• Tracking real-time với GPS
• Tối ưu hóa tuyến đường
• Quản lý đội xe, tài xế

Lý do tạm dừng: Chờ khách hàng xác nhận yêu cầu mới', 
'#14b8a6', 'truck', 'on_hold', 'medium', 35, 
'2025-07-01', '2026-02-28', 600000000, 'u-admin-002', '2025-06-15 10:00:00'),

-- DỰ ÁN 6: DevOps Infrastructure (Hoàn thành - 100%)
('prj-006', 'SaigonTech DevOps Platform', 
'Xây dựng hạ tầng DevOps nội bộ:
• CI/CD Pipeline với GitLab
• Kubernetes cluster management
• Monitoring với Prometheus/Grafana
• Log aggregation với ELK Stack

Đã hoàn thành và đang vận hành ổn định.', 
'#0891b2', 'server', 'completed', 'medium', 100, 
'2025-01-01', '2025-06-30', 200000000, 'u-admin-002', '2024-12-15 08:00:00'),

-- DỰ ÁN 7: AI Chatbot (Lên kế hoạch - 5%)
('prj-007', 'SmartBot - AI Customer Service', 
'Nền tảng chatbot AI hỗ trợ khách hàng:
• NLP tiếng Việt với GPT integration
• Multi-channel (Web, Facebook, Zalo)
• Knowledge base management

Đang trong giai đoạn nghiên cứu và POC.', 
'#8b5cf6', 'bot', 'planning', 'low', 5, 
'2026-04-01', '2026-12-31', 500000000, 'u-admin-002', '2025-11-01 09:00:00'),

-- DỰ ÁN 8: Website Redesign (Hoàn thành - 100%)
('prj-008', 'SaigonTech Website 2025', 
'Thiết kế lại website công ty:
• Landing page hiện đại
• Portfolio showcase
• Blog công nghệ
• Trang tuyển dụng

Đã hoàn thành và go-live tháng 3/2025.', 
'#f97316', 'globe', 'completed', 'low', 100, 
'2025-01-15', '2025-03-15', 120000000, 'u-pm-001', '2025-01-10 10:00:00');

-- =============================================
-- 4. PROJECT_MEMBERS - Phân công thành viên dự án
-- =============================================
INSERT INTO `project_members` (`project_id`, `user_id`, `role`, `joined_at`) VALUES

-- Project 1: VinMart E-commerce (Team 12 người)
('prj-001', 'u-admin-001', 'owner', '2025-05-15 09:00:00'),
('prj-001', 'u-pm-001', 'manager', '2025-05-15 09:00:00'),
('prj-001', 'u-lead-001', 'member', '2025-06-01 08:00:00'),
('prj-001', 'u-lead-002', 'member', '2025-06-01 08:00:00'),
('prj-001', 'u-dev-be-001', 'member', '2025-06-01 08:00:00'),
('prj-001', 'u-dev-be-002', 'member', '2025-06-15 08:00:00'),
('prj-001', 'u-dev-fe-001', 'member', '2025-06-01 08:00:00'),
('prj-001', 'u-dev-fe-002', 'member', '2025-06-15 08:00:00'),
('prj-001', 'u-design-001', 'member', '2025-05-20 08:00:00'),
('prj-001', 'u-qa-001', 'member', '2025-07-01 08:00:00'),
('prj-001', 'u-devops-001', 'member', '2025-08-01 08:00:00'),
('prj-001', 'u-client-001', 'viewer', '2025-05-15 09:00:00'),

-- Project 2: FPT Mobile Banking (Team 10 người)
('prj-002', 'u-admin-001', 'owner', '2025-08-20 10:00:00'),
('prj-002', 'u-pm-002', 'manager', '2025-08-20 10:00:00'),
('prj-002', 'u-lead-001', 'member', '2025-09-01 08:00:00'),
('prj-002', 'u-dev-be-001', 'member', '2025-09-01 08:00:00'),
('prj-002', 'u-dev-be-003', 'member', '2025-09-15 08:00:00'),
('prj-002', 'u-dev-mb-001', 'member', '2025-09-01 08:00:00'),
('prj-002', 'u-dev-mb-002', 'member', '2025-09-01 08:00:00'),
('prj-002', 'u-design-001', 'member', '2025-08-25 08:00:00'),
('prj-002', 'u-qa-001', 'member', '2025-10-01 08:00:00'),
('prj-002', 'u-client-002', 'viewer', '2025-08-20 10:00:00'),

-- Project 3: HRMS (Team 6 người)
('prj-003', 'u-admin-002', 'owner', '2025-02-15 08:00:00'),
('prj-003', 'u-pm-003', 'manager', '2025-02-15 08:00:00'),
('prj-003', 'u-dev-be-002', 'member', '2025-03-01 08:00:00'),
('prj-003', 'u-dev-fe-002', 'member', '2025-03-01 08:00:00'),
('prj-003', 'u-design-002', 'member', '2025-03-01 08:00:00'),
('prj-003', 'u-qa-002', 'member', '2025-04-01 08:00:00'),

-- Project 4: MedCare (Team 4 người - Planning)
('prj-004', 'u-admin-001', 'owner', '2025-12-01 09:00:00'),
('prj-004', 'u-pm-001', 'manager', '2025-12-01 09:00:00'),
('prj-004', 'u-ba-001', 'member', '2025-12-01 09:00:00'),
('prj-004', 'u-design-001', 'member', '2025-12-15 08:00:00'),

-- Project 5: LogiTrack (Team 5 người - On Hold)
('prj-005', 'u-admin-002', 'owner', '2025-06-15 10:00:00'),
('prj-005', 'u-pm-002', 'manager', '2025-06-15 10:00:00'),
('prj-005', 'u-dev-be-003', 'member', '2025-07-01 08:00:00'),
('prj-005', 'u-dev-fe-003', 'member', '2025-07-01 08:00:00'),
('prj-005', 'u-ba-001', 'member', '2025-06-20 08:00:00'),

-- Project 6: DevOps Platform (Team 3 người - Completed)
('prj-006', 'u-admin-002', 'owner', '2024-12-15 08:00:00'),
('prj-006', 'u-devops-001', 'manager', '2024-12-15 08:00:00'),
('prj-006', 'u-lead-001', 'member', '2025-01-01 08:00:00'),

-- Project 7: SmartBot AI (Team 3 người - Planning)
('prj-007', 'u-admin-002', 'owner', '2025-11-01 09:00:00'),
('prj-007', 'u-pm-003', 'manager', '2025-11-01 09:00:00'),
('prj-007', 'u-ba-001', 'member', '2025-11-01 09:00:00'),

-- Project 8: Website Redesign (Team 4 người - Completed)
('prj-008', 'u-pm-001', 'owner', '2025-01-10 10:00:00'),
('prj-008', 'u-lead-002', 'manager', '2025-01-10 10:00:00'),
('prj-008', 'u-dev-fe-001', 'member', '2025-01-15 08:00:00'),
('prj-008', 'u-design-001', 'member', '2025-01-12 08:00:00');


-- =============================================
-- 5. TASKS - Công việc chi tiết
-- =============================================
-- Phân bố: Done (35%), In Progress (25%), Todo (20%), In Review (10%), Backlog (10%)

INSERT INTO `tasks` (`id`, `project_id`, `title`, `description`, `status`, `priority`, `position`, `start_date`, `due_date`, `estimated_hours`, `actual_hours`, `created_by`, `created_at`) VALUES

-- ========== PROJECT 1: VinMart E-commerce (25 tasks) ==========

-- DONE Tasks (9 tasks)
('t-001-001', 'prj-001', 'Thiết kế Database Schema', 'Thiết kế cấu trúc database cho hệ thống e-commerce: users, products, orders, payments...', 'done', 'high', 1, '2025-06-01', '2025-06-15', 40, 38, 'u-lead-001', '2025-06-01 08:00:00'),
('t-001-002', 'prj-001', 'Setup CI/CD Pipeline', 'Thiết lập GitLab CI/CD cho auto deploy staging/production', 'done', 'high', 2, '2025-06-01', '2025-06-10', 24, 20, 'u-devops-001', '2025-06-01 09:00:00'),
('t-001-003', 'prj-001', 'API Authentication (JWT)', 'Implement JWT authentication với refresh token', 'done', 'high', 3, '2025-06-10', '2025-06-25', 32, 30, 'u-dev-be-001', '2025-06-10 08:00:00'),
('t-001-004', 'prj-001', 'UI Design System', 'Xây dựng design system: colors, typography, components', 'done', 'high', 4, '2025-05-20', '2025-06-15', 48, 45, 'u-design-001', '2025-05-20 08:00:00'),
('t-001-005', 'prj-001', 'Product Listing Page', 'Trang danh sách sản phẩm với filter, sort, pagination', 'done', 'high', 5, '2025-06-20', '2025-07-10', 40, 42, 'u-dev-fe-001', '2025-06-20 08:00:00'),
('t-001-006', 'prj-001', 'Product Detail Page', 'Trang chi tiết sản phẩm với gallery, variants, reviews', 'done', 'medium', 6, '2025-07-01', '2025-07-20', 32, 35, 'u-dev-fe-001', '2025-07-01 08:00:00'),
('t-001-007', 'prj-001', 'Shopping Cart API', 'API giỏ hàng: add, update, remove, sync', 'done', 'high', 7, '2025-07-05', '2025-07-25', 28, 26, 'u-dev-be-001', '2025-07-05 08:00:00'),
('t-001-008', 'prj-001', 'Shopping Cart UI', 'Giao diện giỏ hàng responsive', 'done', 'high', 8, '2025-07-15', '2025-08-01', 24, 22, 'u-dev-fe-002', '2025-07-15 08:00:00'),
('t-001-009', 'prj-001', 'User Registration Flow', 'Đăng ký tài khoản với email verification', 'done', 'medium', 9, '2025-06-15', '2025-07-01', 20, 18, 'u-dev-be-002', '2025-06-15 08:00:00'),

-- IN PROGRESS Tasks (6 tasks)
('t-001-010', 'prj-001', 'Tích hợp VNPay', 'Tích hợp cổng thanh toán VNPay với IPN callback', 'in_progress', 'urgent', 10, '2025-12-01', '2026-01-15', 40, 25, 'u-dev-be-001', '2025-12-01 08:00:00'),
('t-001-011', 'prj-001', 'Checkout Flow UI', 'Giao diện checkout multi-step: Address → Shipping → Payment → Confirm', 'in_progress', 'high', 11, '2025-12-15', '2026-01-20', 36, 20, 'u-dev-fe-001', '2025-12-15 08:00:00'),
('t-001-012', 'prj-001', 'Order Management API', 'API quản lý đơn hàng: create, update status, cancel', 'in_progress', 'high', 12, '2025-12-20', '2026-01-25', 32, 15, 'u-dev-be-002', '2025-12-20 08:00:00'),
('t-001-013', 'prj-001', 'Admin Dashboard', 'Dashboard quản trị: thống kê doanh thu, đơn hàng, sản phẩm', 'in_progress', 'medium', 13, '2026-01-02', '2026-01-30', 48, 12, 'u-dev-fe-002', '2026-01-02 08:00:00'),
('t-001-014', 'prj-001', 'Product Search với Elasticsearch', 'Tích hợp Elasticsearch cho full-text search sản phẩm', 'in_progress', 'medium', 14, '2026-01-05', '2026-02-01', 40, 8, 'u-lead-001', '2026-01-05 08:00:00'),
('t-001-015', 'prj-001', 'Performance Optimization', 'Tối ưu performance: lazy loading, code splitting, caching', 'in_progress', 'medium', 15, '2026-01-06', '2026-01-25', 24, 5, 'u-dev-fe-001', '2026-01-06 08:00:00'),

-- IN REVIEW Tasks (3 tasks)
('t-001-016', 'prj-001', 'Tích hợp MoMo Wallet', 'Tích hợp thanh toán MoMo QR và ví điện tử', 'in_review', 'high', 16, '2025-11-15', '2025-12-15', 32, 30, 'u-dev-be-001', '2025-11-15 08:00:00'),
('t-001-017', 'prj-001', 'Email Notification System', 'Hệ thống gửi email: order confirmation, shipping update', 'in_review', 'medium', 17, '2025-11-20', '2025-12-20', 24, 22, 'u-dev-be-002', '2025-11-20 08:00:00'),
('t-001-018', 'prj-001', 'Product Reviews & Ratings', 'Tính năng đánh giá và review sản phẩm', 'in_review', 'medium', 18, '2025-12-01', '2025-12-25', 28, 26, 'u-dev-fe-002', '2025-12-01 08:00:00'),

-- TODO Tasks (4 tasks)
('t-001-019', 'prj-001', 'Tích hợp ZaloPay', 'Tích hợp ZaloPay cho thanh toán mobile', 'todo', 'medium', 19, '2026-01-20', '2026-02-10', 28, NULL, 'u-pm-001', '2026-01-08 08:00:00'),
('t-001-020', 'prj-001', 'Loyalty Program', 'Chương trình tích điểm khách hàng thân thiết', 'todo', 'low', 20, '2026-02-01', '2026-02-28', 40, NULL, 'u-pm-001', '2026-01-08 09:00:00'),
('t-001-021', 'prj-001', 'Mobile Responsive Testing', 'Kiểm thử responsive trên các thiết bị mobile', 'todo', 'high', 21, '2026-01-25', '2026-02-05', 16, NULL, 'u-qa-001', '2026-01-08 10:00:00'),
('t-001-022', 'prj-001', 'Security Audit', 'Kiểm tra bảo mật: SQL injection, XSS, CSRF', 'todo', 'urgent', 22, '2026-02-01', '2026-02-15', 32, NULL, 'u-qa-001', '2026-01-08 11:00:00'),

-- BACKLOG Tasks (3 tasks)
('t-001-023', 'prj-001', 'Recommendation Engine', 'Hệ thống gợi ý sản phẩm dựa trên AI/ML', 'backlog', 'low', 23, NULL, '2026-03-15', 60, NULL, 'u-pm-001', '2025-12-01 08:00:00'),
('t-001-024', 'prj-001', 'Multi-language Support', 'Hỗ trợ đa ngôn ngữ: Việt, Anh', 'backlog', 'low', 24, NULL, '2026-03-20', 40, NULL, 'u-pm-001', '2025-12-01 09:00:00'),
('t-001-025', 'prj-001', 'PWA Implementation', 'Chuyển đổi sang Progressive Web App', 'backlog', 'low', 25, NULL, '2026-03-31', 48, NULL, 'u-lead-002', '2025-12-01 10:00:00');


-- ========== PROJECT 2: FPT Mobile Banking (20 tasks) ==========

-- DONE Tasks (5 tasks)
('t-002-001', 'prj-002', 'App Architecture Design', 'Thiết kế kiến trúc app Flutter với Clean Architecture', 'done', 'high', 1, '2025-09-01', '2025-09-15', 32, 30, 'u-lead-001', '2025-09-01 08:00:00'),
('t-002-002', 'prj-002', 'UI/UX Design System', 'Design system cho mobile banking app', 'done', 'high', 2, '2025-08-25', '2025-09-20', 48, 50, 'u-design-001', '2025-08-25 08:00:00'),
('t-002-003', 'prj-002', 'Login & Biometric Auth', 'Đăng nhập với Face ID, Touch ID, PIN', 'done', 'urgent', 3, '2025-09-15', '2025-10-10', 40, 38, 'u-dev-mb-001', '2025-09-15 08:00:00'),
('t-002-004', 'prj-002', 'Account Overview Screen', 'Màn hình tổng quan tài khoản, số dư', 'done', 'high', 4, '2025-10-01', '2025-10-20', 28, 26, 'u-dev-mb-002', '2025-10-01 08:00:00'),
('t-002-005', 'prj-002', 'Transaction History', 'Lịch sử giao dịch với filter, search', 'done', 'medium', 5, '2025-10-15', '2025-11-05', 24, 22, 'u-dev-mb-001', '2025-10-15 08:00:00'),

-- IN PROGRESS Tasks (6 tasks)
('t-002-006', 'prj-002', 'eKYC Integration', 'Tích hợp eKYC với VNPT/FPT.AI cho xác thực danh tính', 'in_progress', 'urgent', 6, '2025-11-01', '2026-01-15', 60, 40, 'u-dev-be-001', '2025-11-01 08:00:00'),
('t-002-007', 'prj-002', 'Internal Transfer', 'Chuyển tiền trong cùng hệ thống FPT Bank', 'in_progress', 'high', 7, '2025-12-01', '2026-01-20', 48, 30, 'u-dev-be-003', '2025-12-01 08:00:00'),
('t-002-008', 'prj-002', 'OTP & 2FA System', 'Hệ thống OTP qua SMS/Email và Google Authenticator', 'in_progress', 'urgent', 8, '2025-12-15', '2026-01-25', 36, 20, 'u-dev-be-001', '2025-12-15 08:00:00'),
('t-002-009', 'prj-002', 'Bill Payment Module', 'Thanh toán hóa đơn điện, nước, internet', 'in_progress', 'medium', 9, '2026-01-02', '2026-02-01', 40, 15, 'u-dev-mb-002', '2026-01-02 08:00:00'),
('t-002-010', 'prj-002', 'Push Notification', 'Thông báo đẩy cho giao dịch, khuyến mãi', 'in_progress', 'medium', 10, '2026-01-05', '2026-01-30', 24, 8, 'u-dev-mb-001', '2026-01-05 08:00:00'),
('t-002-011', 'prj-002', 'Security Testing', 'Penetration testing và security audit', 'in_progress', 'urgent', 11, '2026-01-06', '2026-02-15', 48, 10, 'u-qa-001', '2026-01-06 08:00:00'),

-- TODO Tasks (5 tasks) - Một số quá hạn
('t-002-012', 'prj-002', 'Napas Integration', 'Tích hợp Napas cho chuyển tiền liên ngân hàng 24/7', 'todo', 'urgent', 12, '2026-01-10', '2026-02-28', 60, NULL, 'u-pm-002', '2025-12-01 08:00:00'),
('t-002-013', 'prj-002', 'QR Code Payment', 'Thanh toán bằng QR Code (VietQR)', 'todo', 'high', 13, '2026-02-01', '2026-03-15', 40, NULL, 'u-pm-002', '2025-12-15 08:00:00'),
('t-002-014', 'prj-002', 'Savings Account', 'Tính năng tiết kiệm online', 'todo', 'medium', 14, '2026-03-01', '2026-04-15', 48, NULL, 'u-pm-002', '2026-01-08 08:00:00'),
('t-002-015', 'prj-002', 'Card Management', 'Quản lý thẻ: khóa/mở, đổi PIN, xem CVV', 'todo', 'medium', 15, '2026-03-15', '2026-04-30', 36, NULL, 'u-pm-002', '2026-01-08 09:00:00'),
('t-002-016', 'prj-002', 'App Store Submission', 'Chuẩn bị và submit lên App Store, Play Store', 'todo', 'high', 16, '2026-05-01', '2026-05-31', 24, NULL, 'u-pm-002', '2026-01-08 10:00:00'),

-- BACKLOG Tasks (4 tasks)
('t-002-017', 'prj-002', 'Loan Application', 'Đăng ký vay tiêu dùng online', 'backlog', 'low', 17, NULL, '2026-06-30', 60, NULL, 'u-pm-002', '2025-11-01 08:00:00'),
('t-002-018', 'prj-002', 'Investment Features', 'Tính năng đầu tư: quỹ, chứng khoán', 'backlog', 'low', 18, NULL, '2026-06-30', 80, NULL, 'u-pm-002', '2025-11-01 09:00:00'),
('t-002-019', 'prj-002', 'Voice Banking', 'Giao dịch bằng giọng nói', 'backlog', 'low', 19, NULL, '2026-06-30', 60, NULL, 'u-pm-002', '2025-11-01 10:00:00'),
('t-002-020', 'prj-002', 'Smartwatch App', 'Ứng dụng cho Apple Watch, Wear OS', 'backlog', 'low', 20, NULL, '2026-06-30', 48, NULL, 'u-pm-002', '2025-11-01 11:00:00');


-- ========== PROJECT 3: HRMS (15 tasks) ==========

-- DONE Tasks (12 tasks - 92% hoàn thành)
('t-003-001', 'prj-003', 'Database Design', 'Thiết kế database cho hệ thống HRMS', 'done', 'high', 1, '2025-03-01', '2025-03-15', 24, 22, 'u-pm-003', '2025-03-01 08:00:00'),
('t-003-002', 'prj-003', 'Employee Management', 'CRUD nhân viên, hồ sơ, phòng ban', 'done', 'high', 2, '2025-03-15', '2025-04-15', 48, 45, 'u-dev-be-002', '2025-03-15 08:00:00'),
('t-003-003', 'prj-003', 'Attendance System', 'Hệ thống chấm công với GPS tracking', 'done', 'high', 3, '2025-04-01', '2025-05-01', 56, 52, 'u-dev-be-002', '2025-04-01 08:00:00'),
('t-003-004', 'prj-003', 'Leave Management', 'Quản lý nghỉ phép, phê duyệt, số ngày còn lại', 'done', 'high', 4, '2025-05-01', '2025-06-01', 40, 38, 'u-dev-fe-002', '2025-05-01 08:00:00'),
('t-003-005', 'prj-003', 'Payroll Calculation', 'Tính lương tự động dựa trên chấm công, OT', 'done', 'urgent', 5, '2025-06-01', '2025-07-15', 64, 60, 'u-dev-be-002', '2025-06-01 08:00:00'),
('t-003-006', 'prj-003', 'Payslip Generation', 'Tạo phiếu lương PDF tự động', 'done', 'high', 6, '2025-07-15', '2025-08-15', 32, 30, 'u-dev-be-002', '2025-07-15 08:00:00'),
('t-003-007', 'prj-003', 'Employee Portal UI', 'Giao diện portal cho nhân viên', 'done', 'high', 7, '2025-04-15', '2025-06-01', 48, 50, 'u-dev-fe-002', '2025-04-15 08:00:00'),
('t-003-008', 'prj-003', 'Admin Dashboard', 'Dashboard quản trị HR', 'done', 'medium', 8, '2025-06-15', '2025-08-01', 40, 42, 'u-dev-fe-002', '2025-06-15 08:00:00'),
('t-003-009', 'prj-003', 'Reports & Analytics', 'Báo cáo nhân sự, biểu đồ thống kê', 'done', 'medium', 9, '2025-08-01', '2025-09-15', 36, 34, 'u-dev-fe-002', '2025-08-01 08:00:00'),
('t-003-010', 'prj-003', 'Email Notifications', 'Thông báo email cho phê duyệt, nhắc nhở', 'done', 'medium', 10, '2025-09-01', '2025-10-01', 20, 18, 'u-dev-be-002', '2025-09-01 08:00:00'),
('t-003-011', 'prj-003', 'UI/UX Design', 'Thiết kế giao diện HRMS', 'done', 'high', 11, '2025-03-01', '2025-04-15', 40, 38, 'u-design-002', '2025-03-01 08:00:00'),
('t-003-012', 'prj-003', 'UAT Testing', 'User Acceptance Testing', 'done', 'high', 12, '2025-10-01', '2025-11-15', 32, 30, 'u-qa-002', '2025-10-01 08:00:00'),

-- IN PROGRESS Tasks (2 tasks)
('t-003-013', 'prj-003', 'KPI Management', 'Module đánh giá KPI nhân viên', 'in_progress', 'medium', 13, '2025-11-15', '2026-01-15', 40, 30, 'u-dev-be-002', '2025-11-15 08:00:00'),
('t-003-014', 'prj-003', 'Mobile App', 'App mobile cho nhân viên chấm công, xem lương', 'in_progress', 'medium', 14, '2025-12-01', '2026-01-31', 48, 20, 'u-dev-fe-002', '2025-12-01 08:00:00'),

-- TODO Tasks (1 task)
('t-003-015', 'prj-003', 'Training Module', 'Module quản lý đào tạo nhân viên', 'todo', 'low', 15, '2026-02-01', '2026-03-15', 40, NULL, 'u-pm-003', '2026-01-08 08:00:00');

-- ========== PROJECT 4: MedCare (5 tasks - Planning) ==========
('t-004-001', 'prj-004', 'Requirement Analysis', 'Phân tích yêu cầu từ các bệnh viện, phòng khám', 'in_progress', 'high', 1, '2025-12-01', '2026-01-15', 60, 35, 'u-ba-001', '2025-12-01 08:00:00'),
('t-004-002', 'prj-004', 'Competitor Analysis', 'Phân tích đối thủ: BookingCare, Hello Bacsi', 'done', 'medium', 2, '2025-12-01', '2025-12-20', 24, 22, 'u-ba-001', '2025-12-01 09:00:00'),
('t-004-003', 'prj-004', 'UI/UX Wireframes', 'Wireframe cho app đặt lịch khám', 'in_progress', 'high', 3, '2025-12-15', '2026-01-20', 40, 15, 'u-design-001', '2025-12-15 08:00:00'),
('t-004-004', 'prj-004', 'Technical Architecture', 'Thiết kế kiến trúc hệ thống', 'todo', 'high', 4, '2026-01-15', '2026-02-01', 32, NULL, 'u-pm-001', '2026-01-08 08:00:00'),
('t-004-005', 'prj-004', 'Database Design', 'Thiết kế database cho hệ thống y tế', 'backlog', 'high', 5, NULL, '2026-02-15', 24, NULL, 'u-pm-001', '2026-01-08 09:00:00');

-- ========== PROJECT 5: LogiTrack (8 tasks - On Hold) ==========
('t-005-001', 'prj-005', 'Requirement Gathering', 'Thu thập yêu cầu từ khách hàng', 'done', 'high', 1, '2025-07-01', '2025-07-20', 32, 30, 'u-ba-001', '2025-07-01 08:00:00'),
('t-005-002', 'prj-005', 'System Design', 'Thiết kế hệ thống logistics', 'done', 'high', 2, '2025-07-15', '2025-08-10', 40, 38, 'u-pm-002', '2025-07-15 08:00:00'),
('t-005-003', 'prj-005', 'Order Management API', 'API quản lý đơn hàng vận chuyển', 'in_progress', 'high', 3, '2025-08-01', '2025-09-15', 48, 25, 'u-dev-be-003', '2025-08-01 08:00:00'),
('t-005-004', 'prj-005', 'Driver App UI', 'Giao diện app cho tài xế', 'in_progress', 'medium', 4, '2025-08-15', '2025-10-01', 40, 20, 'u-dev-fe-003', '2025-08-15 08:00:00'),
('t-005-005', 'prj-005', 'GPS Tracking', 'Tích hợp GPS tracking real-time', 'todo', 'high', 5, '2025-10-01', '2025-11-15', 48, NULL, 'u-pm-002', '2025-08-01 08:00:00'),
('t-005-006', 'prj-005', 'Route Optimization', 'Thuật toán tối ưu tuyến đường', 'backlog', 'medium', 6, NULL, '2025-12-15', 60, NULL, 'u-pm-002', '2025-08-01 09:00:00'),
('t-005-007', 'prj-005', 'Admin Dashboard', 'Dashboard quản lý vận chuyển', 'backlog', 'medium', 7, NULL, '2026-01-15', 40, NULL, 'u-pm-002', '2025-08-01 10:00:00'),
('t-005-008', 'prj-005', 'Partner API Integration', 'Tích hợp API với đối tác vận chuyển', 'backlog', 'low', 8, NULL, '2026-02-28', 48, NULL, 'u-pm-002', '2025-08-01 11:00:00');


-- ========== PROJECT 6: DevOps Platform (8 tasks - Completed) ==========
('t-006-001', 'prj-006', 'Kubernetes Cluster Setup', 'Thiết lập K8s cluster trên AWS EKS', 'done', 'high', 1, '2025-01-01', '2025-01-31', 48, 45, 'u-devops-001', '2025-01-01 08:00:00'),
('t-006-002', 'prj-006', 'GitLab CI/CD Templates', 'Tạo CI/CD templates chuẩn cho các dự án', 'done', 'high', 2, '2025-02-01', '2025-02-28', 32, 30, 'u-devops-001', '2025-02-01 08:00:00'),
('t-006-003', 'prj-006', 'Prometheus Setup', 'Cài đặt Prometheus cho monitoring', 'done', 'high', 3, '2025-03-01', '2025-03-20', 24, 22, 'u-devops-001', '2025-03-01 08:00:00'),
('t-006-004', 'prj-006', 'Grafana Dashboards', 'Tạo dashboards Grafana cho monitoring', 'done', 'medium', 4, '2025-03-15', '2025-04-10', 28, 26, 'u-devops-001', '2025-03-15 08:00:00'),
('t-006-005', 'prj-006', 'ELK Stack Setup', 'Triển khai ELK Stack cho centralized logging', 'done', 'high', 5, '2025-04-01', '2025-05-01', 40, 38, 'u-devops-001', '2025-04-01 08:00:00'),
('t-006-006', 'prj-006', 'Security Scanning', 'Tích hợp security scanning vào CI/CD', 'done', 'high', 6, '2025-05-01', '2025-05-20', 24, 22, 'u-devops-001', '2025-05-01 08:00:00'),
('t-006-007', 'prj-006', 'Terraform IaC', 'Infrastructure as Code với Terraform', 'done', 'medium', 7, '2025-05-15', '2025-06-15', 36, 34, 'u-devops-001', '2025-05-15 08:00:00'),
('t-006-008', 'prj-006', 'Documentation', 'Tài liệu hướng dẫn sử dụng DevOps platform', 'done', 'medium', 8, '2025-06-01', '2025-06-30', 20, 18, 'u-devops-001', '2025-06-01 08:00:00');

-- ========== PROJECT 8: Website Redesign (6 tasks - Completed) ==========
('t-008-001', 'prj-008', 'Design Mockups', 'Thiết kế mockup trang chủ và các trang con', 'done', 'high', 1, '2025-01-15', '2025-02-01', 32, 30, 'u-design-001', '2025-01-15 08:00:00'),
('t-008-002', 'prj-008', 'Frontend Development', 'Code frontend với Next.js và Tailwind CSS', 'done', 'high', 2, '2025-02-01', '2025-02-28', 48, 45, 'u-dev-fe-001', '2025-02-01 08:00:00'),
('t-008-003', 'prj-008', 'CMS Integration', 'Tích hợp CMS cho blog và tuyển dụng', 'done', 'medium', 3, '2025-02-15', '2025-03-05', 24, 22, 'u-dev-fe-001', '2025-02-15 08:00:00'),
('t-008-004', 'prj-008', 'SEO Optimization', 'Tối ưu SEO: meta tags, sitemap, structured data', 'done', 'medium', 4, '2025-03-01', '2025-03-10', 16, 14, 'u-dev-fe-001', '2025-03-01 08:00:00'),
('t-008-005', 'prj-008', 'Performance Testing', 'Test và tối ưu performance, đạt 90+ Lighthouse', 'done', 'medium', 5, '2025-03-05', '2025-03-12', 12, 10, 'u-qa-001', '2025-03-05 08:00:00'),
('t-008-006', 'prj-008', 'Go-Live & Monitoring', 'Deploy production và setup monitoring', 'done', 'high', 6, '2025-03-12', '2025-03-15', 8, 8, 'u-devops-001', '2025-03-12 08:00:00');

-- ========== TASKS QUÁ HẠN (Overdue) ==========
-- Task quá hạn 1: Trong project VinMart
('t-001-ovd-001', 'prj-001', 'Inventory Management API', 'API quản lý tồn kho, nhập xuất', 'in_progress', 'high', 26, '2025-11-01', '2025-12-15', 40, 35, 'u-dev-be-002', '2025-11-01 08:00:00'),

-- Task quá hạn 2: Trong project Mobile Banking
('t-002-ovd-001', 'prj-002', 'User Profile Management', 'Quản lý thông tin cá nhân, đổi mật khẩu', 'todo', 'medium', 21, '2025-12-01', '2025-12-31', 24, NULL, 'u-pm-002', '2025-12-01 08:00:00'),

-- Task quá hạn 3: Không có người được giao
('t-001-ovd-002', 'prj-001', 'API Documentation', 'Viết tài liệu API với Swagger/OpenAPI', 'todo', 'medium', 27, '2025-12-01', '2026-01-05', 20, NULL, 'u-pm-001', '2025-12-01 08:00:00');


-- =============================================
-- 6. TASK_ASSIGNEES - Phân công người thực hiện
-- =============================================
INSERT INTO `task_assignees` (`task_id`, `user_id`, `assigned_by`, `assigned_at`) VALUES

-- Project 1: VinMart E-commerce
('t-001-001', 'u-lead-001', 'u-pm-001', '2025-06-01 08:30:00'),
('t-001-002', 'u-devops-001', 'u-pm-001', '2025-06-01 09:30:00'),
('t-001-003', 'u-dev-be-001', 'u-lead-001', '2025-06-10 08:30:00'),
('t-001-004', 'u-design-001', 'u-pm-001', '2025-05-20 08:30:00'),
('t-001-005', 'u-dev-fe-001', 'u-lead-002', '2025-06-20 08:30:00'),
('t-001-006', 'u-dev-fe-001', 'u-lead-002', '2025-07-01 08:30:00'),
('t-001-007', 'u-dev-be-001', 'u-lead-001', '2025-07-05 08:30:00'),
('t-001-008', 'u-dev-fe-002', 'u-lead-002', '2025-07-15 08:30:00'),
('t-001-009', 'u-dev-be-002', 'u-lead-001', '2025-06-15 08:30:00'),
('t-001-010', 'u-dev-be-001', 'u-lead-001', '2025-12-01 08:30:00'),
('t-001-011', 'u-dev-fe-001', 'u-lead-002', '2025-12-15 08:30:00'),
('t-001-012', 'u-dev-be-002', 'u-lead-001', '2025-12-20 08:30:00'),
('t-001-013', 'u-dev-fe-002', 'u-lead-002', '2026-01-02 08:30:00'),
('t-001-014', 'u-lead-001', 'u-pm-001', '2026-01-05 08:30:00'),
('t-001-015', 'u-dev-fe-001', 'u-lead-002', '2026-01-06 08:30:00'),
('t-001-016', 'u-dev-be-001', 'u-lead-001', '2025-11-15 08:30:00'),
('t-001-017', 'u-dev-be-002', 'u-lead-001', '2025-11-20 08:30:00'),
('t-001-018', 'u-dev-fe-002', 'u-lead-002', '2025-12-01 08:30:00'),
('t-001-021', 'u-qa-001', 'u-pm-001', '2026-01-08 10:30:00'),
('t-001-022', 'u-qa-001', 'u-pm-001', '2026-01-08 11:30:00'),
('t-001-ovd-001', 'u-dev-be-002', 'u-lead-001', '2025-11-01 08:30:00'),
-- Task không có người được giao: t-001-019, t-001-020, t-001-023, t-001-024, t-001-025, t-001-ovd-002

-- Project 2: FPT Mobile Banking
('t-002-001', 'u-lead-001', 'u-pm-002', '2025-09-01 08:30:00'),
('t-002-002', 'u-design-001', 'u-pm-002', '2025-08-25 08:30:00'),
('t-002-003', 'u-dev-mb-001', 'u-lead-001', '2025-09-15 08:30:00'),
('t-002-004', 'u-dev-mb-002', 'u-lead-001', '2025-10-01 08:30:00'),
('t-002-005', 'u-dev-mb-001', 'u-lead-001', '2025-10-15 08:30:00'),
('t-002-006', 'u-dev-be-001', 'u-lead-001', '2025-11-01 08:30:00'),
('t-002-006', 'u-dev-be-003', 'u-lead-001', '2025-11-01 09:00:00'), -- 2 người cùng làm
('t-002-007', 'u-dev-be-003', 'u-lead-001', '2025-12-01 08:30:00'),
('t-002-008', 'u-dev-be-001', 'u-lead-001', '2025-12-15 08:30:00'),
('t-002-009', 'u-dev-mb-002', 'u-lead-001', '2026-01-02 08:30:00'),
('t-002-010', 'u-dev-mb-001', 'u-lead-001', '2026-01-05 08:30:00'),
('t-002-011', 'u-qa-001', 'u-pm-002', '2026-01-06 08:30:00'),

-- Project 3: HRMS
('t-003-001', 'u-pm-003', 'u-admin-002', '2025-03-01 08:30:00'),
('t-003-002', 'u-dev-be-002', 'u-pm-003', '2025-03-15 08:30:00'),
('t-003-003', 'u-dev-be-002', 'u-pm-003', '2025-04-01 08:30:00'),
('t-003-004', 'u-dev-fe-002', 'u-pm-003', '2025-05-01 08:30:00'),
('t-003-005', 'u-dev-be-002', 'u-pm-003', '2025-06-01 08:30:00'),
('t-003-006', 'u-dev-be-002', 'u-pm-003', '2025-07-15 08:30:00'),
('t-003-007', 'u-dev-fe-002', 'u-pm-003', '2025-04-15 08:30:00'),
('t-003-008', 'u-dev-fe-002', 'u-pm-003', '2025-06-15 08:30:00'),
('t-003-009', 'u-dev-fe-002', 'u-pm-003', '2025-08-01 08:30:00'),
('t-003-010', 'u-dev-be-002', 'u-pm-003', '2025-09-01 08:30:00'),
('t-003-011', 'u-design-002', 'u-pm-003', '2025-03-01 08:30:00'),
('t-003-012', 'u-qa-002', 'u-pm-003', '2025-10-01 08:30:00'),
('t-003-013', 'u-dev-be-002', 'u-pm-003', '2025-11-15 08:30:00'),
('t-003-014', 'u-dev-fe-002', 'u-pm-003', '2025-12-01 08:30:00'),

-- Project 4: MedCare
('t-004-001', 'u-ba-001', 'u-pm-001', '2025-12-01 08:30:00'),
('t-004-002', 'u-ba-001', 'u-pm-001', '2025-12-01 09:30:00'),
('t-004-003', 'u-design-001', 'u-pm-001', '2025-12-15 08:30:00'),

-- Project 5: LogiTrack
('t-005-001', 'u-ba-001', 'u-pm-002', '2025-07-01 08:30:00'),
('t-005-002', 'u-pm-002', 'u-admin-002', '2025-07-15 08:30:00'),
('t-005-003', 'u-dev-be-003', 'u-pm-002', '2025-08-01 08:30:00'),
('t-005-004', 'u-dev-fe-003', 'u-pm-002', '2025-08-15 08:30:00'),

-- Project 6: DevOps Platform
('t-006-001', 'u-devops-001', 'u-admin-002', '2025-01-01 08:30:00'),
('t-006-002', 'u-devops-001', 'u-admin-002', '2025-02-01 08:30:00'),
('t-006-003', 'u-devops-001', 'u-admin-002', '2025-03-01 08:30:00'),
('t-006-004', 'u-devops-001', 'u-admin-002', '2025-03-15 08:30:00'),
('t-006-005', 'u-devops-001', 'u-admin-002', '2025-04-01 08:30:00'),
('t-006-006', 'u-devops-001', 'u-admin-002', '2025-05-01 08:30:00'),
('t-006-007', 'u-devops-001', 'u-admin-002', '2025-05-15 08:30:00'),
('t-006-008', 'u-devops-001', 'u-admin-002', '2025-06-01 08:30:00'),

-- Project 8: Website Redesign
('t-008-001', 'u-design-001', 'u-pm-001', '2025-01-15 08:30:00'),
('t-008-002', 'u-dev-fe-001', 'u-lead-002', '2025-02-01 08:30:00'),
('t-008-003', 'u-dev-fe-001', 'u-lead-002', '2025-02-15 08:30:00'),
('t-008-004', 'u-dev-fe-001', 'u-lead-002', '2025-03-01 08:30:00'),
('t-008-005', 'u-qa-001', 'u-pm-001', '2025-03-05 08:30:00'),
('t-008-006', 'u-devops-001', 'u-pm-001', '2025-03-12 08:30:00');

