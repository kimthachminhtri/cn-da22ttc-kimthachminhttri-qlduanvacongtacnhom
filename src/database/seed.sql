-- =============================================
-- TASKFLOW SEED DATA - DỮ LIỆU MẪU CHUYÊN NGHIỆP
-- Mô phỏng công ty phần mềm TechViet Solutions
-- Chạy sau khi đã tạo schema (taskflow2.sql)
-- =============================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- Xóa dữ liệu cũ (nếu có)
DELETE FROM event_attendees;
DELETE FROM calendar_events;
DELETE FROM notifications;
DELETE FROM comments;
DELETE FROM documents;
DELETE FROM task_checklists;
DELETE FROM task_labels;
DELETE FROM task_assignees;
DELETE FROM tasks;
DELETE FROM project_members;
DELETE FROM projects;
DELETE FROM labels;
DELETE FROM user_settings;
DELETE FROM activity_logs;
DELETE FROM users;

-- =============================================
-- 1. USERS - Đội ngũ TechViet Solutions
-- Password: password123 (hash bcrypt)
-- =============================================
INSERT INTO `users` (`id`, `email`, `password_hash`, `full_name`, `role`, `department`, `position`, `phone`, `is_active`, `created_at`) VALUES
-- Ban lãnh đạo
('usr-001', 'ceo@techviet.vn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Nguyễn Minh Tuấn', 'admin', 'Ban Giám đốc', 'CEO & Founder', '0901234001', 1, '2023-01-01 08:00:00'),
('usr-002', 'cto@techviet.vn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Trần Văn Hùng', 'admin', 'Ban Giám đốc', 'CTO', '0901234002', 1, '2023-01-01 08:00:00'),

-- Quản lý dự án
('usr-003', 'pm.linh@techviet.vn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Phạm Thị Linh', 'manager', 'Quản lý dự án', 'Project Manager', '0901234003', 1, '2023-02-15 08:00:00'),
('usr-004', 'pm.duc@techviet.vn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Lê Hoàng Đức', 'manager', 'Quản lý dự án', 'Senior PM', '0901234004', 1, '2023-03-01 08:00:00'),

-- Team Leader
('usr-005', 'lead.nam@techviet.vn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Hoàng Văn Nam', 'manager', 'Phát triển', 'Tech Lead - Backend', '0901234005', 1, '2023-03-15 08:00:00'),
('usr-006', 'lead.mai@techviet.vn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Nguyễn Thị Mai', 'manager', 'Phát triển', 'Tech Lead - Frontend', '0901234006', 1, '2023-04-01 08:00:00'),

-- Backend Developers
('usr-007', 'dev.khanh@techviet.vn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Trần Quốc Khánh', 'member', 'Phát triển', 'Senior Backend Developer', '0901234007', 1, '2023-05-01 08:00:00'),
('usr-008', 'dev.hoa@techviet.vn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Lê Thị Hoa', 'member', 'Phát triển', 'Backend Developer', '0901234008', 1, '2023-06-01 08:00:00'),
('usr-009', 'dev.long@techviet.vn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Phạm Hoàng Long', 'member', 'Phát triển', 'Junior Backend Developer', '0901234009', 1, '2024-01-15 08:00:00'),

-- Frontend Developers
('usr-010', 'dev.trang@techviet.vn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Nguyễn Thu Trang', 'member', 'Phát triển', 'Senior Frontend Developer', '0901234010', 1, '2023-05-15 08:00:00'),
('usr-011', 'dev.minh@techviet.vn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Vũ Đức Minh', 'member', 'Phát triển', 'Frontend Developer', '0901234011', 1, '2023-07-01 08:00:00'),
('usr-012', 'dev.an@techviet.vn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Đỗ Thị An', 'member', 'Phát triển', 'Junior Frontend Developer', '0901234012', 1, '2024-02-01 08:00:00'),

-- Mobile Developers
('usr-013', 'dev.binh@techviet.vn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Trương Văn Bình', 'member', 'Phát triển', 'Mobile Developer (iOS)', '0901234013', 1, '2023-08-01 08:00:00'),
('usr-014', 'dev.cuong@techviet.vn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Nguyễn Mạnh Cường', 'member', 'Phát triển', 'Mobile Developer (Android)', '0901234014', 1, '2023-08-15 08:00:00'),

-- UI/UX Design Team
('usr-015', 'design.huong@techviet.vn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Lê Thị Hương', 'member', 'Thiết kế', 'Senior UI/UX Designer', '0901234015', 1, '2023-04-15 08:00:00'),
('usr-016', 'design.phuc@techviet.vn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Trần Văn Phúc', 'member', 'Thiết kế', 'UI Designer', '0901234016', 1, '2023-09-01 08:00:00'),

-- QA/QC Team
('usr-017', 'qa.thao@techviet.vn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Phạm Thị Thảo', 'member', 'Kiểm thử', 'QA Lead', '0901234017', 1, '2023-05-01 08:00:00'),
('usr-018', 'qa.hung@techviet.vn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Nguyễn Văn Hưng', 'member', 'Kiểm thử', 'QA Engineer', '0901234018', 1, '2023-10-01 08:00:00'),

-- DevOps
('usr-019', 'devops.tai@techviet.vn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Lê Văn Tài', 'member', 'Vận hành', 'DevOps Engineer', '0901234019', 1, '2023-06-15 08:00:00'),

-- Business Analyst
('usr-020', 'ba.nga@techviet.vn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Hoàng Thị Nga', 'member', 'Phân tích', 'Business Analyst', '0901234020', 1, '2023-07-01 08:00:00'),

-- Khách hàng/Stakeholder (guest)
('usr-021', 'client.vinmart@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Nguyễn Văn Thành (VinMart)', 'guest', 'Khách hàng', 'Product Owner', '0909123456', 1, '2024-01-01 08:00:00'),
('usr-022', 'client.fpt@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Trần Minh Quân (FPT)', 'guest', 'Khách hàng', 'IT Manager', '0909234567', 1, '2024-02-01 08:00:00');


-- =============================================
-- 2. LABELS - Nhãn phân loại công việc
-- =============================================
INSERT INTO `labels` (`id`, `name`, `color`, `description`) VALUES
('lbl-001', 'Bug', '#dc2626', 'Lỗi phần mềm cần sửa'),
('lbl-002', 'Feature', '#16a34a', 'Tính năng mới'),
('lbl-003', 'Enhancement', '#2563eb', 'Cải tiến tính năng hiện có'),
('lbl-004', 'Hotfix', '#ea580c', 'Sửa lỗi khẩn cấp production'),
('lbl-005', 'Security', '#7c3aed', 'Liên quan bảo mật'),
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

-- Dự án 1: E-commerce Platform cho VinMart
('prj-001', 'VinMart E-commerce Platform', 
'Xây dựng nền tảng thương mại điện tử toàn diện cho VinMart bao gồm:
- Website bán hàng responsive
- Hệ thống quản lý sản phẩm, kho hàng
- Tích hợp thanh toán đa kênh (VNPay, MoMo, ZaloPay)
- Hệ thống quản lý đơn hàng và vận chuyển
- Chương trình khách hàng thân thiết
- Dashboard báo cáo doanh thu real-time', 
'#6366f1', 'shopping-cart', 'active', 'high', 68, 
'2024-01-15', '2024-07-31', 850000000, 'usr-001', '2024-01-10 09:00:00'),

-- Dự án 2: Mobile Banking App cho FPT
('prj-002', 'FPT Mobile Banking App', 
'Phát triển ứng dụng ngân hàng số cho FPT Bank:
- Đăng ký tài khoản online với eKYC
- Chuyển tiền nhanh 24/7
- Thanh toán hóa đơn, nạp tiền điện thoại
- Quản lý thẻ tín dụng/ghi nợ
- Tiết kiệm online, vay tiêu dùng
- Bảo mật 2FA, sinh trắc học', 
'#22c55e', 'smartphone', 'active', 'urgent', 45, 
'2024-03-01', '2024-10-31', 1200000000, 'usr-001', '2024-02-20 10:00:00'),

-- Dự án 3: HR Management System nội bộ
('prj-003', 'TechViet HRMS', 
'Hệ thống quản lý nhân sự nội bộ TechViet:
- Quản lý hồ sơ nhân viên
- Chấm công, tính lương tự động
- Quản lý nghỉ phép, OT
- Đánh giá KPI, review performance
- Tuyển dụng và onboarding
- Training management', 
'#f59e0b', 'users', 'active', 'medium', 82, 
'2023-10-01', '2024-03-31', 200000000, 'usr-002', '2023-09-15 08:00:00'),

-- Dự án 4: Healthcare Booking Platform
('prj-004', 'MedBook - Đặt lịch khám bệnh', 
'Nền tảng đặt lịch khám bệnh online:
- Tìm kiếm bác sĩ, phòng khám
- Đặt lịch hẹn online
- Tư vấn sức khỏe từ xa (Telemedicine)
- Quản lý hồ sơ bệnh án điện tử
- Thanh toán và bảo hiểm
- Nhắc lịch tái khám', 
'#ec4899', 'heart-pulse', 'planning', 'high', 15, 
'2024-06-01', '2024-12-31', 600000000, 'usr-001', '2024-05-01 09:00:00'),

-- Dự án 5: Logistics Management System
('prj-005', 'LogiTrack - Quản lý vận chuyển', 
'Hệ thống quản lý logistics và vận chuyển:
- Quản lý đơn hàng, lộ trình
- Tracking real-time với GPS
- Tối ưu hóa tuyến đường
- Quản lý đội xe, tài xế
- Báo cáo chi phí vận chuyển
- Tích hợp API với đối tác', 
'#14b8a6', 'truck', 'on_hold', 'medium', 35, 
'2024-02-01', '2024-08-31', 450000000, 'usr-002', '2024-01-20 10:00:00'),

-- Dự án 6: Internal DevOps Infrastructure
('prj-006', 'TechViet DevOps Platform', 
'Xây dựng hạ tầng DevOps nội bộ:
- CI/CD Pipeline với GitLab
- Kubernetes cluster management
- Monitoring với Prometheus/Grafana
- Log aggregation với ELK Stack
- Security scanning automation
- Infrastructure as Code với Terraform', 
'#0891b2', 'server', 'active', 'medium', 70, 
'2023-11-01', '2024-04-30', 150000000, 'usr-002', '2023-10-20 08:00:00'),

-- Dự án 7: AI Chatbot Platform
('prj-007', 'SmartBot - AI Customer Service', 
'Nền tảng chatbot AI hỗ trợ khách hàng:
- NLP tiếng Việt với GPT integration
- Multi-channel (Web, Facebook, Zalo)
- Knowledge base management
- Handoff to human agent
- Analytics và sentiment analysis
- Training interface cho bot', 
'#8b5cf6', 'bot', 'planning', 'low', 10, 
'2024-07-01', '2025-01-31', 350000000, 'usr-002', '2024-06-01 09:00:00'),

-- Dự án 8: Company Website Redesign
('prj-008', 'TechViet Website 2024', 
'Thiết kế lại website công ty:
- Landing page hiện đại
- Portfolio showcase
- Blog công nghệ
- Trang tuyển dụng
- SEO optimization
- Performance optimization', 
'#f97316', 'globe', 'completed', 'low', 100, 
'2024-01-01', '2024-02-28', 80000000, 'usr-003', '2023-12-15 10:00:00');


-- =============================================
-- 4. PROJECT_MEMBERS - Phân công thành viên dự án
-- =============================================
INSERT INTO `project_members` (`project_id`, `user_id`, `role`, `joined_at`) VALUES
-- Project 1: VinMart E-commerce (Team lớn)
('prj-001', 'usr-001', 'owner', '2024-01-10 09:00:00'),
('prj-001', 'usr-003', 'manager', '2024-01-10 09:00:00'),
('prj-001', 'usr-005', 'member', '2024-01-15 08:00:00'),
('prj-001', 'usr-006', 'member', '2024-01-15 08:00:00'),
('prj-001', 'usr-007', 'member', '2024-01-15 08:00:00'),
('prj-001', 'usr-008', 'member', '2024-01-20 08:00:00'),
('prj-001', 'usr-010', 'member', '2024-01-15 08:00:00'),
('prj-001', 'usr-011', 'member', '2024-01-20 08:00:00'),
('prj-001', 'usr-015', 'member', '2024-01-12 08:00:00'),
('prj-001', 'usr-017', 'member', '2024-02-01 08:00:00'),
('prj-001', 'usr-019', 'member', '2024-02-15 08:00:00'),
('prj-001', 'usr-020', 'member', '2024-01-10 09:00:00'),
('prj-001', 'usr-021', 'viewer', '2024-01-10 09:00:00'),

-- Project 2: FPT Mobile Banking (Team mobile + backend)
('prj-002', 'usr-001', 'owner', '2024-02-20 10:00:00'),
('prj-002', 'usr-004', 'manager', '2024-02-20 10:00:00'),
('prj-002', 'usr-005', 'member', '2024-03-01 08:00:00'),
('prj-002', 'usr-007', 'member', '2024-03-01 08:00:00'),
('prj-002', 'usr-009', 'member', '2024-03-05 08:00:00'),
('prj-002', 'usr-013', 'member', '2024-03-01 08:00:00'),
('prj-002', 'usr-014', 'member', '2024-03-01 08:00:00'),
('prj-002', 'usr-015', 'member', '2024-02-25 08:00:00'),
('prj-002', 'usr-016', 'member', '2024-03-10 08:00:00'),
('prj-002', 'usr-017', 'member', '2024-03-15 08:00:00'),
('prj-002', 'usr-018', 'member', '2024-03-15 08:00:00'),
('prj-002', 'usr-020', 'member', '2024-02-20 10:00:00'),
('prj-002', 'usr-022', 'viewer', '2024-02-20 10:00:00'),

-- Project 3: HRMS (Team nhỏ, internal)
('prj-003', 'usr-002', 'owner', '2023-09-15 08:00:00'),
('prj-003', 'usr-003', 'manager', '2023-09-15 08:00:00'),
('prj-003', 'usr-008', 'member', '2023-10-01 08:00:00'),
('prj-003', 'usr-011', 'member', '2023-10-01 08:00:00'),
('prj-003', 'usr-016', 'member', '2023-10-01 08:00:00'),
('prj-003', 'usr-018', 'member', '2023-11-01 08:00:00'),

-- Project 4: MedBook (Planning phase)
('prj-004', 'usr-001', 'owner', '2024-05-01 09:00:00'),
('prj-004', 'usr-004', 'manager', '2024-05-01 09:00:00'),
('prj-004', 'usr-020', 'member', '2024-05-01 09:00:00'),
('prj-004', 'usr-015', 'member', '2024-05-15 08:00:00'),

-- Project 5: LogiTrack (On hold)
('prj-005', 'usr-002', 'owner', '2024-01-20 10:00:00'),
('prj-005', 'usr-004', 'manager', '2024-01-20 10:00:00'),
('prj-005', 'usr-007', 'member', '2024-02-01 08:00:00'),
('prj-005', 'usr-010', 'member', '2024-02-01 08:00:00'),

-- Project 6: DevOps Platform
('prj-006', 'usr-002', 'owner', '2023-10-20 08:00:00'),
('prj-006', 'usr-019', 'manager', '2023-10-20 08:00:00'),
('prj-006', 'usr-005', 'member', '2023-11-01 08:00:00'),

-- Project 7: SmartBot AI
('prj-007', 'usr-002', 'owner', '2024-06-01 09:00:00'),
('prj-007', 'usr-003', 'manager', '2024-06-01 09:00:00'),
('prj-007', 'usr-020', 'member', '2024-06-01 09:00:00'),

-- Project 8: Website Redesign (Completed)
('prj-008', 'usr-003', 'owner', '2023-12-15 10:00:00'),
('prj-008', 'usr-006', 'manager', '2023-12-15 10:00:00'),
('prj-008', 'usr-010', 'member', '2024-01-01 08:00:00'),
('prj-008', 'usr-015', 'member', '2023-12-20 08:00:00'),
('prj-008', 'usr-016', 'member', '2024-01-05 08:00:00');


-- =============================================
-- 5. TASKS - Công việc chi tiết cho các dự án
-- =============================================
INSERT INTO `tasks` (`id`, `project_id`, `title`, `description`, `status`, `priority`, `position`, `start_date`, `due_date`, `estimated_hours`, `actual_hours`, `created_by`, `created_at`) VALUES

-- ========== PROJECT 1: VinMart E-commerce ==========
-- Epic: Hệ thống sản phẩm
('tsk-001', 'prj-001', '[Epic] Quản lý sản phẩm', 'Xây dựng module quản lý sản phẩm hoàn chỉnh bao gồm CRUD, phân loại, thuộc tính, biến thể sản phẩm.', 'in_progress', 'high', 1, '2024-01-20', '2024-03-15', 120, 85, 'usr-003', '2024-01-15 09:00:00'),
('tsk-002', 'prj-001', 'API CRUD sản phẩm', 'Xây dựng RESTful API cho thêm/sửa/xóa/xem sản phẩm với validation đầy đủ.', 'done', 'high', 1, '2024-01-20', '2024-02-05', 24, 20, 'usr-005', '2024-01-15 09:30:00'),
('tsk-003', 'prj-001', 'Quản lý danh mục sản phẩm', 'Hệ thống phân loại sản phẩm đa cấp (category tree), hỗ trợ drag-drop sắp xếp.', 'done', 'high', 2, '2024-02-01', '2024-02-15', 16, 14, 'usr-007', '2024-01-20 10:00:00'),
('tsk-004', 'prj-001', 'Biến thể sản phẩm (SKU)', 'Quản lý biến thể theo màu sắc, kích thước với giá và tồn kho riêng.', 'in_progress', 'high', 3, '2024-02-15', '2024-03-01', 20, 12, 'usr-007', '2024-02-10 08:00:00'),
('tsk-005', 'prj-001', 'Upload và quản lý hình ảnh', 'Tích hợp upload multi-image, resize, CDN cho hình ảnh sản phẩm.', 'in_review', 'medium', 4, '2024-02-20', '2024-03-05', 12, 10, 'usr-008', '2024-02-15 09:00:00'),

-- Epic: Giỏ hàng & Checkout
('tsk-006', 'prj-001', '[Epic] Giỏ hàng & Thanh toán', 'Module giỏ hàng, checkout flow và tích hợp các cổng thanh toán.', 'in_progress', 'urgent', 2, '2024-02-01', '2024-04-15', 160, 90, 'usr-003', '2024-01-20 10:00:00'),
('tsk-007', 'prj-001', 'Shopping Cart với Redis', 'Giỏ hàng lưu trữ Redis, sync giữa guest và logged-in user.', 'done', 'high', 1, '2024-02-01', '2024-02-20', 24, 22, 'usr-007', '2024-01-25 08:00:00'),
('tsk-008', 'prj-001', 'Checkout Flow UI', 'Giao diện checkout multi-step: Địa chỉ → Vận chuyển → Thanh toán → Xác nhận.', 'done', 'high', 2, '2024-02-10', '2024-02-28', 20, 18, 'usr-010', '2024-02-05 09:00:00'),
('tsk-009', 'prj-001', 'Tích hợp VNPay', 'Tích hợp cổng thanh toán VNPay với IPN callback.', 'in_progress', 'urgent', 3, '2024-03-01', '2024-03-15', 16, 10, 'usr-007', '2024-02-25 10:00:00'),
('tsk-010', 'prj-001', 'Tích hợp MoMo Wallet', 'Tích hợp thanh toán MoMo QR và ví điện tử.', 'todo', 'high', 4, '2024-03-10', '2024-03-25', 16, NULL, 'usr-005', '2024-03-01 08:00:00'),
('tsk-011', 'prj-001', 'Tích hợp ZaloPay', 'Tích hợp ZaloPay cho thanh toán mobile.', 'backlog', 'medium', 5, NULL, '2024-04-05', 12, NULL, 'usr-005', '2024-03-05 09:00:00'),

-- Epic: Quản lý đơn hàng
('tsk-012', 'prj-001', '[Epic] Quản lý đơn hàng', 'Hệ thống quản lý đơn hàng từ đặt hàng đến giao hàng thành công.', 'in_progress', 'high', 3, '2024-02-15', '2024-04-30', 100, 45, 'usr-003', '2024-02-01 08:00:00'),
('tsk-013', 'prj-001', 'Order Management API', 'API quản lý đơn hàng: tạo, cập nhật trạng thái, hủy đơn.', 'in_progress', 'high', 1, '2024-02-15', '2024-03-10', 24, 16, 'usr-008', '2024-02-10 09:00:00'),
('tsk-014', 'prj-001', 'Order Tracking', 'Theo dõi trạng thái đơn hàng real-time với timeline.', 'todo', 'medium', 2, '2024-03-15', '2024-03-30', 16, NULL, 'usr-010', '2024-03-01 10:00:00'),
('tsk-015', 'prj-001', 'Email/SMS Notification', 'Gửi thông báo đơn hàng qua email và SMS.', 'backlog', 'medium', 3, NULL, '2024-04-10', 12, NULL, 'usr-008', '2024-03-05 08:00:00'),

-- Tasks độc lập
('tsk-016', 'prj-001', 'Setup CI/CD Pipeline', 'Thiết lập GitLab CI/CD cho auto deploy staging/production.', 'done', 'high', 4, '2024-01-15', '2024-01-25', 16, 14, 'usr-019', '2024-01-12 10:00:00'),
('tsk-017', 'prj-001', 'Performance Optimization', 'Tối ưu performance: lazy loading, code splitting, caching.', 'in_progress', 'medium', 5, '2024-03-01', '2024-03-20', 20, 8, 'usr-010', '2024-02-25 09:00:00'),
('tsk-018', 'prj-001', 'Security Audit', 'Kiểm tra bảo mật: SQL injection, XSS, CSRF, authentication.', 'todo', 'high', 6, '2024-03-20', '2024-04-05', 24, NULL, 'usr-017', '2024-03-10 08:00:00'),

-- ========== PROJECT 2: FPT Mobile Banking ==========
('tsk-019', 'prj-002', '[Epic] User Authentication', 'Hệ thống xác thực người dùng với eKYC và bảo mật cao.', 'in_progress', 'urgent', 1, '2024-03-01', '2024-04-30', 200, 80, 'usr-004', '2024-02-25 09:00:00'),
('tsk-020', 'prj-002', 'eKYC Integration', 'Tích hợp eKYC với VNPT/FPT.AI cho xác thực danh tính.', 'in_progress', 'urgent', 1, '2024-03-05', '2024-03-30', 40, 25, 'usr-007', '2024-03-01 08:00:00'),
('tsk-021', 'prj-002', 'Biometric Authentication', 'Xác thực sinh trắc học: Face ID, Touch ID, Fingerprint.', 'in_progress', 'high', 2, '2024-03-15', '2024-04-15', 32, 15, 'usr-013', '2024-03-10 09:00:00'),
('tsk-022', 'prj-002', 'OTP & 2FA System', 'Hệ thống OTP qua SMS/Email và Google Authenticator.', 'todo', 'high', 3, '2024-04-01', '2024-04-20', 24, NULL, 'usr-007', '2024-03-20 10:00:00'),

('tsk-023', 'prj-002', '[Epic] Money Transfer', 'Module chuyển tiền nội bộ và liên ngân hàng.', 'todo', 'high', 2, '2024-04-15', '2024-06-30', 160, NULL, 'usr-004', '2024-03-01 10:00:00'),
('tsk-024', 'prj-002', 'Internal Transfer', 'Chuyển tiền trong cùng hệ thống FPT Bank.', 'backlog', 'high', 1, NULL, '2024-05-15', 32, NULL, 'usr-009', '2024-03-15 08:00:00'),
('tsk-025', 'prj-002', 'Napas Integration', 'Tích hợp Napas cho chuyển tiền liên ngân hàng 24/7.', 'backlog', 'high', 2, NULL, '2024-06-15', 48, NULL, 'usr-007', '2024-03-15 09:00:00'),

('tsk-026', 'prj-002', 'UI/UX Design System', 'Xây dựng design system cho mobile app.', 'in_review', 'high', 3, '2024-03-01', '2024-03-25', 40, 38, 'usr-015', '2024-02-25 10:00:00'),
('tsk-027', 'prj-002', 'iOS App Foundation', 'Setup project iOS với Swift, architecture MVVM-C.', 'done', 'high', 4, '2024-03-01', '2024-03-15', 24, 20, 'usr-013', '2024-02-28 08:00:00'),
('tsk-028', 'prj-002', 'Android App Foundation', 'Setup project Android với Kotlin, architecture MVVM.', 'done', 'high', 5, '2024-03-01', '2024-03-15', 24, 22, 'usr-014', '2024-02-28 09:00:00'),

-- ========== PROJECT 3: HRMS ==========
('tsk-029', 'prj-003', 'Module Chấm công', 'Hệ thống chấm công với check-in/out, GPS tracking.', 'done', 'high', 1, '2023-10-15', '2023-11-30', 60, 55, 'usr-003', '2023-10-01 08:00:00'),
('tsk-030', 'prj-003', 'Module Tính lương', 'Tính lương tự động dựa trên chấm công, OT, phụ cấp.', 'done', 'high', 2, '2023-12-01', '2024-01-15', 80, 75, 'usr-008', '2023-11-20 09:00:00'),
('tsk-031', 'prj-003', 'Module Nghỉ phép', 'Quản lý đơn xin nghỉ, phê duyệt, số ngày phép còn lại.', 'done', 'medium', 3, '2024-01-15', '2024-02-15', 40, 38, 'usr-011', '2024-01-10 08:00:00'),
('tsk-032', 'prj-003', 'Dashboard HR Analytics', 'Báo cáo thống kê nhân sự, biểu đồ trực quan.', 'in_review', 'medium', 4, '2024-02-15', '2024-03-15', 32, 28, 'usr-011', '2024-02-10 09:00:00'),
('tsk-033', 'prj-003', 'Mobile App cho nhân viên', 'App mobile để chấm công, xem lương, xin nghỉ phép.', 'in_progress', 'medium', 5, '2024-03-01', '2024-03-31', 48, 20, 'usr-008', '2024-02-25 10:00:00'),

-- ========== PROJECT 4: MedBook (Planning) ==========
('tsk-034', 'prj-004', 'Phân tích yêu cầu', 'Thu thập và phân tích yêu cầu từ các bệnh viện, phòng khám.', 'in_progress', 'high', 1, '2024-05-15', '2024-06-15', 60, 20, 'usr-020', '2024-05-01 09:00:00'),
('tsk-035', 'prj-004', 'Thiết kế UI/UX', 'Wireframe và prototype cho app đặt lịch khám.', 'todo', 'high', 2, '2024-06-01', '2024-06-30', 80, NULL, 'usr-015', '2024-05-15 10:00:00'),
('tsk-036', 'prj-004', 'Database Design', 'Thiết kế cơ sở dữ liệu cho hệ thống y tế.', 'backlog', 'high', 3, NULL, '2024-06-20', 24, NULL, 'usr-004', '2024-05-20 08:00:00'),

-- ========== PROJECT 6: DevOps Platform ==========
('tsk-037', 'prj-006', 'Kubernetes Cluster Setup', 'Thiết lập K8s cluster trên AWS EKS.', 'done', 'high', 1, '2023-11-01', '2023-11-30', 40, 35, 'usr-019', '2023-10-25 08:00:00'),
('tsk-038', 'prj-006', 'GitLab CI/CD Templates', 'Tạo CI/CD templates chuẩn cho các dự án.', 'done', 'high', 2, '2023-12-01', '2023-12-20', 24, 20, 'usr-019', '2023-11-25 09:00:00'),
('tsk-039', 'prj-006', 'Monitoring Stack', 'Setup Prometheus + Grafana cho monitoring.', 'in_progress', 'medium', 3, '2024-01-15', '2024-02-28', 32, 25, 'usr-019', '2024-01-10 10:00:00'),
('tsk-040', 'prj-006', 'Log Aggregation', 'Triển khai ELK Stack cho centralized logging.', 'todo', 'medium', 4, '2024-03-01', '2024-03-31', 28, NULL, 'usr-019', '2024-02-20 08:00:00'),

-- ========== PROJECT 8: Website (Completed) ==========
('tsk-041', 'prj-008', 'Landing Page Design', 'Thiết kế trang chủ mới với style hiện đại.', 'done', 'high', 1, '2024-01-01', '2024-01-15', 24, 22, 'usr-015', '2023-12-20 09:00:00'),
('tsk-042', 'prj-008', 'Frontend Development', 'Code frontend với Next.js và Tailwind CSS.', 'done', 'high', 2, '2024-01-15', '2024-02-10', 40, 38, 'usr-010', '2024-01-10 08:00:00'),
('tsk-043', 'prj-008', 'SEO Optimization', 'Tối ưu SEO: meta tags, sitemap, structured data.', 'done', 'medium', 3, '2024-02-10', '2024-02-20', 16, 14, 'usr-010', '2024-02-05 09:00:00'),
('tsk-044', 'prj-008', 'Performance Testing', 'Test và tối ưu performance, đạt 90+ Lighthouse.', 'done', 'medium', 4, '2024-02-20', '2024-02-28', 12, 10, 'usr-017', '2024-02-15 10:00:00');


-- =============================================
-- 6. TASK_ASSIGNEES - Phân công người thực hiện
-- =============================================
INSERT INTO `task_assignees` (`task_id`, `user_id`, `assigned_by`, `assigned_at`) VALUES
-- Project 1 tasks
('tsk-002', 'usr-007', 'usr-005', '2024-01-15 10:00:00'),
('tsk-003', 'usr-007', 'usr-005', '2024-01-20 10:30:00'),
('tsk-004', 'usr-007', 'usr-005', '2024-02-10 08:30:00'),
('tsk-004', 'usr-008', 'usr-005', '2024-02-10 08:30:00'),
('tsk-005', 'usr-008', 'usr-005', '2024-02-15 09:30:00'),
('tsk-007', 'usr-007', 'usr-003', '2024-01-25 08:30:00'),
('tsk-008', 'usr-010', 'usr-006', '2024-02-05 09:30:00'),
('tsk-008', 'usr-011', 'usr-006', '2024-02-05 09:30:00'),
('tsk-009', 'usr-007', 'usr-005', '2024-02-25 10:30:00'),
('tsk-010', 'usr-008', 'usr-005', '2024-03-01 08:30:00'),
('tsk-013', 'usr-008', 'usr-005', '2024-02-10 09:30:00'),
('tsk-014', 'usr-010', 'usr-006', '2024-03-01 10:30:00'),
('tsk-016', 'usr-019', 'usr-003', '2024-01-12 10:30:00'),
('tsk-017', 'usr-010', 'usr-006', '2024-02-25 09:30:00'),
('tsk-017', 'usr-011', 'usr-006', '2024-02-25 09:30:00'),
('tsk-018', 'usr-017', 'usr-003', '2024-03-10 08:30:00'),

-- Project 2 tasks
('tsk-020', 'usr-007', 'usr-005', '2024-03-01 08:30:00'),
('tsk-020', 'usr-009', 'usr-005', '2024-03-05 09:00:00'),
('tsk-021', 'usr-013', 'usr-004', '2024-03-10 09:30:00'),
('tsk-021', 'usr-014', 'usr-004', '2024-03-10 09:30:00'),
('tsk-026', 'usr-015', 'usr-004', '2024-02-25 10:30:00'),
('tsk-026', 'usr-016', 'usr-004', '2024-03-10 08:00:00'),
('tsk-027', 'usr-013', 'usr-004', '2024-02-28 08:30:00'),
('tsk-028', 'usr-014', 'usr-004', '2024-02-28 09:30:00'),

-- Project 3 tasks
('tsk-029', 'usr-008', 'usr-003', '2023-10-01 08:30:00'),
('tsk-030', 'usr-008', 'usr-003', '2023-11-20 09:30:00'),
('tsk-031', 'usr-011', 'usr-003', '2024-01-10 08:30:00'),
('tsk-032', 'usr-011', 'usr-003', '2024-02-10 09:30:00'),
('tsk-032', 'usr-016', 'usr-003', '2024-02-10 09:30:00'),
('tsk-033', 'usr-008', 'usr-003', '2024-02-25 10:30:00'),

-- Project 4 tasks
('tsk-034', 'usr-020', 'usr-004', '2024-05-01 09:30:00'),
('tsk-035', 'usr-015', 'usr-004', '2024-05-15 10:30:00'),

-- Project 6 tasks
('tsk-037', 'usr-019', 'usr-002', '2023-10-25 08:30:00'),
('tsk-038', 'usr-019', 'usr-002', '2023-11-25 09:30:00'),
('tsk-039', 'usr-019', 'usr-002', '2024-01-10 10:30:00'),
('tsk-040', 'usr-019', 'usr-002', '2024-02-20 08:30:00'),

-- Project 8 tasks
('tsk-041', 'usr-015', 'usr-003', '2023-12-20 09:30:00'),
('tsk-041', 'usr-016', 'usr-003', '2024-01-05 08:00:00'),
('tsk-042', 'usr-010', 'usr-006', '2024-01-10 08:30:00'),
('tsk-043', 'usr-010', 'usr-006', '2024-02-05 09:30:00'),
('tsk-044', 'usr-017', 'usr-003', '2024-02-15 10:30:00');

-- =============================================
-- 7. TASK_LABELS - Gán nhãn cho công việc
-- =============================================
INSERT INTO `task_labels` (`task_id`, `label_id`) VALUES
-- API tasks
('tsk-002', 'lbl-008'), ('tsk-002', 'lbl-009'),
('tsk-003', 'lbl-008'), ('tsk-003', 'lbl-009'),
('tsk-007', 'lbl-008'), ('tsk-007', 'lbl-006'),
('tsk-013', 'lbl-008'),

-- Payment integration
('tsk-009', 'lbl-008'), ('tsk-009', 'lbl-005'),
('tsk-010', 'lbl-008'), ('tsk-010', 'lbl-005'),
('tsk-011', 'lbl-008'),

-- UI/UX tasks
('tsk-005', 'lbl-007'),
('tsk-008', 'lbl-007'),
('tsk-026', 'lbl-007'), ('tsk-026', 'lbl-015'),
('tsk-041', 'lbl-007'),

-- Mobile tasks
('tsk-021', 'lbl-015'), ('tsk-021', 'lbl-005'),
('tsk-027', 'lbl-015'),
('tsk-028', 'lbl-015'),
('tsk-033', 'lbl-015'),

-- DevOps tasks
('tsk-016', 'lbl-012'),
('tsk-037', 'lbl-012'),
('tsk-038', 'lbl-012'),
('tsk-039', 'lbl-012'),
('tsk-040', 'lbl-012'),

-- Security tasks
('tsk-018', 'lbl-005'), ('tsk-018', 'lbl-011'),
('tsk-020', 'lbl-005'),
('tsk-022', 'lbl-005'),

-- Performance
('tsk-017', 'lbl-006'),
('tsk-043', 'lbl-006'),
('tsk-044', 'lbl-011'), ('tsk-044', 'lbl-006'),

-- Documentation
('tsk-034', 'lbl-010'), ('tsk-034', 'lbl-013'),
('tsk-036', 'lbl-009'), ('tsk-036', 'lbl-010');

-- =============================================
-- 8. TASK_CHECKLISTS - Checklist chi tiết
-- =============================================
INSERT INTO `task_checklists` (`id`, `task_id`, `title`, `is_completed`, `position`, `created_at`) VALUES
-- Checklist cho tích hợp VNPay
('chk-001', 'tsk-009', 'Đăng ký merchant account VNPay', 1, 1, '2024-02-25 10:00:00'),
('chk-002', 'tsk-009', 'Implement payment request API', 1, 2, '2024-02-25 10:00:00'),
('chk-003', 'tsk-009', 'Implement IPN callback handler', 1, 3, '2024-02-25 10:00:00'),
('chk-004', 'tsk-009', 'Implement return URL handler', 0, 4, '2024-02-25 10:00:00'),
('chk-005', 'tsk-009', 'Test sandbox environment', 1, 5, '2024-02-25 10:00:00'),
('chk-006', 'tsk-009', 'Test production environment', 0, 6, '2024-02-25 10:00:00'),
('chk-007', 'tsk-009', 'Viết tài liệu tích hợp', 0, 7, '2024-02-25 10:00:00'),

-- Checklist cho eKYC
('chk-008', 'tsk-020', 'Tích hợp SDK VNPT eKYC', 1, 1, '2024-03-01 08:00:00'),
('chk-009', 'tsk-020', 'Implement OCR CMND/CCCD', 1, 2, '2024-03-01 08:00:00'),
('chk-010', 'tsk-020', 'Implement Face matching', 1, 3, '2024-03-01 08:00:00'),
('chk-011', 'tsk-020', 'Implement Liveness detection', 0, 4, '2024-03-01 08:00:00'),
('chk-012', 'tsk-020', 'Handle edge cases và errors', 0, 5, '2024-03-01 08:00:00'),
('chk-013', 'tsk-020', 'UAT với team QA', 0, 6, '2024-03-01 08:00:00'),

-- Checklist cho Biometric
('chk-014', 'tsk-021', 'Implement Face ID (iOS)', 1, 1, '2024-03-10 09:00:00'),
('chk-015', 'tsk-021', 'Implement Touch ID (iOS)', 1, 2, '2024-03-10 09:00:00'),
('chk-016', 'tsk-021', 'Implement Fingerprint (Android)', 0, 3, '2024-03-10 09:00:00'),
('chk-017', 'tsk-021', 'Implement Face Recognition (Android)', 0, 4, '2024-03-10 09:00:00'),
('chk-018', 'tsk-021', 'Fallback to PIN/Password', 0, 5, '2024-03-10 09:00:00'),

-- Checklist cho Security Audit
('chk-019', 'tsk-018', 'SQL Injection testing', 0, 1, '2024-03-10 08:00:00'),
('chk-020', 'tsk-018', 'XSS vulnerability scan', 0, 2, '2024-03-10 08:00:00'),
('chk-021', 'tsk-018', 'CSRF protection check', 0, 3, '2024-03-10 08:00:00'),
('chk-022', 'tsk-018', 'Authentication flow review', 0, 4, '2024-03-10 08:00:00'),
('chk-023', 'tsk-018', 'API security audit', 0, 5, '2024-03-10 08:00:00'),
('chk-024', 'tsk-018', 'Penetration testing', 0, 6, '2024-03-10 08:00:00'),
('chk-025', 'tsk-018', 'Security report documentation', 0, 7, '2024-03-10 08:00:00');


-- =============================================
-- 9. DOCUMENTS - Tài liệu dự án
-- =============================================
INSERT INTO `documents` (`id`, `name`, `description`, `type`, `mime_type`, `file_size`, `file_path`, `parent_id`, `project_id`, `is_starred`, `uploaded_by`, `created_at`) VALUES
-- Root folders
('doc-001', 'Thiết kế', 'Thư mục chứa file thiết kế UI/UX', 'folder', NULL, NULL, NULL, NULL, NULL, 0, 'usr-015', '2024-01-10 08:00:00'),
('doc-002', 'Tài liệu kỹ thuật', 'Tài liệu API, database, architecture', 'folder', NULL, NULL, NULL, NULL, NULL, 0, 'usr-005', '2024-01-10 08:00:00'),
('doc-003', 'Hợp đồng & Pháp lý', 'Hợp đồng, NDA, tài liệu pháp lý', 'folder', NULL, NULL, NULL, NULL, NULL, 0, 'usr-001', '2024-01-10 08:00:00'),
('doc-004', 'Meeting Notes', 'Biên bản họp, ghi chú cuộc họp', 'folder', NULL, NULL, NULL, NULL, NULL, 0, 'usr-003', '2024-01-10 08:00:00'),
('doc-005', 'Resources', 'Tài nguyên, assets, icons', 'folder', NULL, NULL, NULL, NULL, NULL, 0, 'usr-015', '2024-01-10 08:00:00'),

-- VinMart E-commerce documents
('doc-006', 'VinMart_UI_Kit.fig', 'Design system và UI Kit cho VinMart', 'file', 'application/figma', 25600000, '/uploads/prj-001/VinMart_UI_Kit.fig', 'doc-001', 'prj-001', 1, 'usr-015', '2024-01-15 09:00:00'),
('doc-007', 'VinMart_Wireframes.fig', 'Wireframes cho toàn bộ user flow', 'file', 'application/figma', 18432000, '/uploads/prj-001/VinMart_Wireframes.fig', 'doc-001', 'prj-001', 1, 'usr-015', '2024-01-18 10:00:00'),
('doc-008', 'API_Documentation_v2.pdf', 'Tài liệu API RESTful cho VinMart', 'file', 'application/pdf', 3145728, '/uploads/prj-001/API_Documentation_v2.pdf', 'doc-002', 'prj-001', 1, 'usr-005', '2024-02-01 14:00:00'),
('doc-009', 'Database_Schema.sql', 'Schema database MySQL', 'file', 'text/plain', 102400, '/uploads/prj-001/Database_Schema.sql', 'doc-002', 'prj-001', 0, 'usr-007', '2024-01-20 11:00:00'),
('doc-010', 'VinMart_Contract.pdf', 'Hợp đồng dự án với VinMart', 'file', 'application/pdf', 524288, '/uploads/prj-001/VinMart_Contract.pdf', 'doc-003', 'prj-001', 0, 'usr-001', '2024-01-10 09:00:00'),
('doc-011', 'Sprint_Planning_W10.docx', 'Biên bản Sprint Planning tuần 10', 'file', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 256000, '/uploads/prj-001/Sprint_Planning_W10.docx', 'doc-004', 'prj-001', 0, 'usr-003', '2024-03-04 16:00:00'),

-- FPT Mobile Banking documents
('doc-012', 'FPT_App_Design.fig', 'Design cho Mobile Banking App', 'file', 'application/figma', 32768000, '/uploads/prj-002/FPT_App_Design.fig', 'doc-001', 'prj-002', 1, 'usr-015', '2024-03-01 09:00:00'),
('doc-013', 'Security_Requirements.pdf', 'Yêu cầu bảo mật cho ứng dụng ngân hàng', 'file', 'application/pdf', 1048576, '/uploads/prj-002/Security_Requirements.pdf', 'doc-002', 'prj-002', 1, 'usr-004', '2024-02-25 10:00:00'),
('doc-014', 'eKYC_Integration_Guide.pdf', 'Hướng dẫn tích hợp eKYC VNPT', 'file', 'application/pdf', 2097152, '/uploads/prj-002/eKYC_Integration_Guide.pdf', 'doc-002', 'prj-002', 0, 'usr-007', '2024-03-05 11:00:00'),
('doc-015', 'FPT_NDA.pdf', 'Thỏa thuận bảo mật với FPT', 'file', 'application/pdf', 409600, '/uploads/prj-002/FPT_NDA.pdf', 'doc-003', 'prj-002', 0, 'usr-001', '2024-02-20 14:00:00'),

-- HRMS documents
('doc-016', 'HRMS_User_Manual.pdf', 'Hướng dẫn sử dụng hệ thống HRMS', 'file', 'application/pdf', 4194304, '/uploads/prj-003/HRMS_User_Manual.pdf', 'doc-002', 'prj-003', 1, 'usr-003', '2024-02-28 15:00:00'),
('doc-017', 'Payroll_Formula.xlsx', 'Công thức tính lương chi tiết', 'file', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 153600, '/uploads/prj-003/Payroll_Formula.xlsx', 'doc-002', 'prj-003', 0, 'usr-008', '2024-01-10 10:00:00'),

-- DevOps documents
('doc-018', 'K8s_Architecture.png', 'Sơ đồ kiến trúc Kubernetes', 'file', 'image/png', 819200, '/uploads/prj-006/K8s_Architecture.png', 'doc-002', 'prj-006', 1, 'usr-019', '2023-11-15 09:00:00'),
('doc-019', 'CI_CD_Pipeline.md', 'Tài liệu CI/CD Pipeline', 'file', 'text/markdown', 51200, '/uploads/prj-006/CI_CD_Pipeline.md', 'doc-002', 'prj-006', 0, 'usr-019', '2023-12-10 14:00:00'),

-- General resources
('doc-020', 'Brand_Guidelines.pdf', 'Hướng dẫn thương hiệu TechViet', 'file', 'application/pdf', 8388608, '/uploads/general/Brand_Guidelines.pdf', 'doc-005', NULL, 1, 'usr-015', '2024-01-05 09:00:00'),
('doc-021', 'Icon_Pack.zip', 'Bộ icon cho các dự án', 'file', 'application/zip', 15728640, '/uploads/general/Icon_Pack.zip', 'doc-005', NULL, 0, 'usr-016', '2024-01-08 10:00:00');

-- =============================================
-- 10. COMMENTS - Bình luận và thảo luận
-- =============================================
INSERT INTO `comments` (`id`, `entity_type`, `entity_id`, `content`, `created_by`, `created_at`) VALUES
-- Comments cho VNPay integration
('cmt-001', 'task', 'tsk-009', 'Đã hoàn thành test sandbox, tất cả các case đều pass. Chuẩn bị chuyển sang test production.', 'usr-007', '2024-03-10 14:30:00'),
('cmt-002', 'task', 'tsk-009', 'Nhớ handle timeout case khi VNPay response chậm nhé. Recommend set timeout 30s.', 'usr-005', '2024-03-10 15:00:00'),
('cmt-003', 'task', 'tsk-009', 'Đã thêm retry mechanism với exponential backoff. PR #245 đang chờ review.', 'usr-007', '2024-03-11 09:00:00'),

-- Comments cho eKYC
('cmt-004', 'task', 'tsk-020', 'SDK VNPT đã tích hợp xong, OCR accuracy đạt 98% trên test set.', 'usr-007', '2024-03-15 10:00:00'),
('cmt-005', 'task', 'tsk-020', 'Cần test thêm với ảnh chụp trong điều kiện ánh sáng yếu. @usr-018 có thể hỗ trợ test không?', 'usr-004', '2024-03-15 11:30:00'),
('cmt-006', 'task', 'tsk-020', 'Ok anh, em sẽ chuẩn bị test cases cho low-light conditions.', 'usr-018', '2024-03-15 13:00:00'),

-- Comments cho Biometric
('cmt-007', 'task', 'tsk-021', 'Face ID và Touch ID trên iOS đã hoạt động tốt. Đang chuyển sang Android.', 'usr-013', '2024-03-18 16:00:00'),
('cmt-008', 'task', 'tsk-021', 'Android Fingerprint API có breaking changes ở Android 14, cần update target SDK.', 'usr-014', '2024-03-19 09:30:00'),

-- Comments cho Performance
('cmt-009', 'task', 'tsk-017', 'Đã implement lazy loading cho images, LCP giảm từ 3.2s xuống 1.8s.', 'usr-010', '2024-03-08 14:00:00'),
('cmt-010', 'task', 'tsk-017', 'Nice! Thử thêm preload cho critical resources xem có cải thiện thêm không.', 'usr-006', '2024-03-08 15:30:00'),

-- Comments cho HRMS Dashboard
('cmt-011', 'task', 'tsk-032', 'Dashboard đã có đầy đủ charts: headcount, turnover rate, department distribution.', 'usr-011', '2024-03-05 11:00:00'),
('cmt-012', 'task', 'tsk-032', 'Có thể thêm export to PDF/Excel cho các báo cáo không?', 'usr-003', '2024-03-05 14:00:00'),
('cmt-013', 'task', 'tsk-032', 'Đã thêm export feature, đang chờ review.', 'usr-011', '2024-03-06 10:00:00'),

-- Comments cho documents
('cmt-014', 'document', 'doc-008', 'API docs đã update thêm phần authentication và error codes.', 'usr-005', '2024-02-15 09:00:00'),
('cmt-015', 'document', 'doc-013', 'Cần review lại phần encryption requirements với team security.', 'usr-004', '2024-03-01 10:00:00');


-- =============================================
-- 11. CALENDAR_EVENTS - Lịch và sự kiện
-- =============================================
INSERT INTO `calendar_events` (`id`, `title`, `description`, `type`, `color`, `start_time`, `end_time`, `is_all_day`, `location`, `project_id`, `created_by`, `created_at`) VALUES
-- Sprint meetings
('evt-001', 'Sprint Planning - VinMart', 'Lên kế hoạch Sprint 12 cho dự án VinMart E-commerce', 'meeting', '#6366f1', '2024-03-18 09:00:00', '2024-03-18 11:00:00', 0, 'Phòng họp A - Tầng 5', 'prj-001', 'usr-003', '2024-03-11 08:00:00'),
('evt-002', 'Daily Standup - VinMart', 'Daily standup meeting', 'meeting', '#22c55e', '2024-03-19 09:00:00', '2024-03-19 09:15:00', 0, 'Online - Google Meet', 'prj-001', 'usr-003', '2024-03-01 08:00:00'),
('evt-003', 'Sprint Review - VinMart', 'Demo các tính năng hoàn thành trong Sprint 11', 'meeting', '#f59e0b', '2024-03-15 14:00:00', '2024-03-15 16:00:00', 0, 'Phòng họp lớn - Tầng 3', 'prj-001', 'usr-003', '2024-03-08 09:00:00'),
('evt-004', 'Sprint Retrospective', 'Đánh giá Sprint 11, cải tiến quy trình', 'meeting', '#ec4899', '2024-03-15 16:30:00', '2024-03-15 17:30:00', 0, 'Phòng họp A - Tầng 5', 'prj-001', 'usr-003', '2024-03-08 09:30:00'),

-- FPT Banking meetings
('evt-005', 'Kickoff Meeting - FPT Banking', 'Họp khởi động dự án với khách hàng FPT', 'meeting', '#8b5cf6', '2024-03-01 09:00:00', '2024-03-01 11:00:00', 0, 'FPT Tower - Cầu Giấy', 'prj-002', 'usr-004', '2024-02-25 10:00:00'),
('evt-006', 'Security Review - FPT', 'Review bảo mật với team security FPT', 'meeting', '#dc2626', '2024-03-20 14:00:00', '2024-03-20 16:00:00', 0, 'Online - Microsoft Teams', 'prj-002', 'usr-004', '2024-03-15 08:00:00'),
('evt-007', 'UAT Planning - FPT', 'Lên kế hoạch User Acceptance Testing', 'meeting', '#0891b2', '2024-03-25 10:00:00', '2024-03-25 11:30:00', 0, 'Phòng họp B - Tầng 5', 'prj-002', 'usr-004', '2024-03-18 09:00:00'),

-- Deadlines
('evt-008', 'Deadline: VNPay Integration', 'Hoàn thành tích hợp VNPay cho VinMart', 'deadline', '#ef4444', '2024-03-15 23:59:00', NULL, 0, NULL, 'prj-001', 'usr-003', '2024-02-01 08:00:00'),
('evt-009', 'Deadline: eKYC Module', 'Hoàn thành module eKYC cho FPT Banking', 'deadline', '#ef4444', '2024-03-30 23:59:00', NULL, 0, NULL, 'prj-002', 'usr-004', '2024-03-01 08:00:00'),
('evt-010', 'Deadline: HRMS Go-live', 'Ra mắt hệ thống HRMS nội bộ', 'deadline', '#ef4444', '2024-03-31 23:59:00', NULL, 0, NULL, 'prj-003', 'usr-003', '2024-01-15 08:00:00'),

-- Company events
('evt-011', 'Tech Talk: Microservices', 'Chia sẻ kinh nghiệm triển khai Microservices', 'event', '#14b8a6', '2024-03-22 15:00:00', '2024-03-22 17:00:00', 0, 'Phòng họp lớn - Tầng 3', NULL, 'usr-002', '2024-03-10 09:00:00'),
('evt-012', 'Team Building Q1', 'Hoạt động team building quý 1/2024', 'event', '#f97316', '2024-03-30 08:00:00', '2024-03-30 18:00:00', 1, 'Flamingo Đại Lải Resort', NULL, 'usr-001', '2024-03-01 10:00:00'),
('evt-013', 'Company All-hands', 'Họp toàn công ty - Báo cáo Q1', 'meeting', '#6366f1', '2024-04-01 09:00:00', '2024-04-01 11:00:00', 0, 'Hội trường - Tầng 1', NULL, 'usr-001', '2024-03-15 08:00:00'),

-- Training
('evt-014', 'Training: AWS Solutions Architect', 'Khóa đào tạo AWS cho team DevOps', 'event', '#0891b2', '2024-03-25 09:00:00', '2024-03-27 17:00:00', 0, 'Phòng Training - Tầng 4', NULL, 'usr-002', '2024-03-01 09:00:00'),
('evt-015', 'Workshop: React Native', 'Workshop thực hành React Native cho team mobile', 'event', '#22c55e', '2024-03-28 14:00:00', '2024-03-28 17:00:00', 0, 'Phòng họp B - Tầng 5', NULL, 'usr-006', '2024-03-10 10:00:00'),

-- Personal reminders
('evt-016', 'Code Review: Cart Feature', 'Review code tính năng giỏ hàng', 'reminder', '#a855f7', '2024-03-19 14:00:00', '2024-03-19 15:00:00', 0, NULL, 'prj-001', 'usr-005', '2024-03-18 09:00:00'),
('evt-017', '1-on-1 với PM', 'Họp 1-1 với Project Manager', 'meeting', '#64748b', '2024-03-20 11:00:00', '2024-03-20 11:30:00', 0, 'Phòng họp nhỏ - Tầng 5', NULL, 'usr-007', '2024-03-15 08:00:00');

-- =============================================
-- 12. EVENT_ATTENDEES - Người tham dự sự kiện
-- =============================================
INSERT INTO `event_attendees` (`event_id`, `user_id`, `status`) VALUES
-- Sprint Planning VinMart
('evt-001', 'usr-003', 'accepted'),
('evt-001', 'usr-005', 'accepted'),
('evt-001', 'usr-006', 'accepted'),
('evt-001', 'usr-007', 'accepted'),
('evt-001', 'usr-010', 'accepted'),
('evt-001', 'usr-017', 'tentative'),
('evt-001', 'usr-020', 'accepted'),

-- Sprint Review VinMart
('evt-003', 'usr-001', 'accepted'),
('evt-003', 'usr-003', 'accepted'),
('evt-003', 'usr-005', 'accepted'),
('evt-003', 'usr-006', 'accepted'),
('evt-003', 'usr-021', 'accepted'),

-- FPT Kickoff
('evt-005', 'usr-001', 'accepted'),
('evt-005', 'usr-004', 'accepted'),
('evt-005', 'usr-005', 'accepted'),
('evt-005', 'usr-015', 'accepted'),
('evt-005', 'usr-022', 'accepted'),

-- Team Building
('evt-012', 'usr-001', 'accepted'),
('evt-012', 'usr-002', 'accepted'),
('evt-012', 'usr-003', 'accepted'),
('evt-012', 'usr-004', 'accepted'),
('evt-012', 'usr-005', 'accepted'),
('evt-012', 'usr-006', 'accepted'),
('evt-012', 'usr-007', 'tentative'),
('evt-012', 'usr-010', 'accepted'),
('evt-012', 'usr-015', 'accepted'),
('evt-012', 'usr-017', 'declined'),
('evt-012', 'usr-019', 'accepted'),

-- Tech Talk
('evt-011', 'usr-002', 'accepted'),
('evt-011', 'usr-005', 'accepted'),
('evt-011', 'usr-007', 'accepted'),
('evt-011', 'usr-008', 'accepted'),
('evt-011', 'usr-019', 'accepted'),

-- AWS Training
('evt-014', 'usr-019', 'accepted'),
('evt-014', 'usr-005', 'accepted'),
('evt-014', 'usr-007', 'tentative');


-- =============================================
-- 13. NOTIFICATIONS - Thông báo
-- =============================================
INSERT INTO `notifications` (`id`, `user_id`, `type`, `title`, `message`, `data`, `is_read`, `created_at`) VALUES
-- Task assignments
('ntf-001', 'usr-007', 'task_assigned', 'Bạn được giao task mới', 'Bạn được giao task "Tích hợp VNPay" trong dự án VinMart E-commerce', '{"task_id": "tsk-009", "project_id": "prj-001"}', 1, '2024-02-25 10:30:00'),
('ntf-002', 'usr-008', 'task_assigned', 'Bạn được giao task mới', 'Bạn được giao task "Tích hợp MoMo Wallet" trong dự án VinMart E-commerce', '{"task_id": "tsk-010", "project_id": "prj-001"}', 0, '2024-03-01 08:30:00'),
('ntf-003', 'usr-013', 'task_assigned', 'Bạn được giao task mới', 'Bạn được giao task "Biometric Authentication" trong dự án FPT Mobile Banking', '{"task_id": "tsk-021", "project_id": "prj-002"}', 1, '2024-03-10 09:30:00'),

-- Comments
('ntf-004', 'usr-007', 'comment', 'Bình luận mới', 'Hoàng Văn Nam đã bình luận trong task "Tích hợp VNPay"', '{"task_id": "tsk-009", "comment_id": "cmt-002"}', 1, '2024-03-10 15:00:00'),
('ntf-005', 'usr-018', 'comment', 'Bạn được mention', 'Lê Hoàng Đức đã mention bạn trong task "eKYC Integration"', '{"task_id": "tsk-020", "comment_id": "cmt-005"}', 0, '2024-03-15 11:30:00'),

-- Task due reminders
('ntf-006', 'usr-007', 'task_due', 'Task sắp đến hạn', 'Task "Tích hợp VNPay" sẽ đến hạn trong 2 ngày', '{"task_id": "tsk-009"}', 0, '2024-03-13 09:00:00'),
('ntf-007', 'usr-010', 'task_due', 'Task sắp đến hạn', 'Task "Performance Optimization" sẽ đến hạn trong 3 ngày', '{"task_id": "tsk-017"}', 0, '2024-03-17 09:00:00'),
('ntf-008', 'usr-008', 'task_overdue', 'Task đã quá hạn', 'Task "Order Management API" đã quá hạn 2 ngày', '{"task_id": "tsk-013"}', 0, '2024-03-12 09:00:00'),

-- Project updates
('ntf-009', 'usr-021', 'project_update', 'Cập nhật dự án', 'Dự án VinMart E-commerce đã đạt 68% tiến độ', '{"project_id": "prj-001"}', 0, '2024-03-15 10:00:00'),

-- Meeting reminders
('ntf-010', 'usr-005', 'event_reminder', 'Nhắc nhở cuộc họp', 'Sprint Planning - VinMart sẽ bắt đầu trong 30 phút', '{"event_id": "evt-001"}', 0, '2024-03-18 08:30:00'),
('ntf-011', 'usr-007', 'event_reminder', 'Nhắc nhở cuộc họp', 'Code Review: Cart Feature sẽ bắt đầu trong 1 giờ', '{"event_id": "evt-016"}', 0, '2024-03-19 13:00:00'),

-- System notifications
('ntf-012', 'usr-001', 'system', 'Backup hoàn tất', 'Backup database hàng ngày đã hoàn tất thành công', '{}', 1, '2024-03-18 02:00:00'),
('ntf-013', 'usr-002', 'system', 'Cảnh báo server', 'CPU usage trên production server đạt 85%', '{}', 0, '2024-03-17 14:30:00');

-- =============================================
-- 14. USER_SETTINGS - Cài đặt người dùng
-- =============================================
INSERT INTO `user_settings` (`user_id`, `theme`, `language`, `timezone`, `notification_email`, `notification_push`) VALUES
('usr-001', 'light', 'vi', 'Asia/Ho_Chi_Minh', 1, 1),
('usr-002', 'dark', 'vi', 'Asia/Ho_Chi_Minh', 1, 1),
('usr-003', 'light', 'vi', 'Asia/Ho_Chi_Minh', 1, 1),
('usr-004', 'system', 'vi', 'Asia/Ho_Chi_Minh', 1, 1),
('usr-005', 'dark', 'vi', 'Asia/Ho_Chi_Minh', 1, 0),
('usr-006', 'light', 'vi', 'Asia/Ho_Chi_Minh', 1, 1),
('usr-007', 'dark', 'vi', 'Asia/Ho_Chi_Minh', 0, 1),
('usr-008', 'light', 'vi', 'Asia/Ho_Chi_Minh', 1, 1),
('usr-009', 'system', 'vi', 'Asia/Ho_Chi_Minh', 1, 1),
('usr-010', 'light', 'vi', 'Asia/Ho_Chi_Minh', 1, 1),
('usr-011', 'dark', 'vi', 'Asia/Ho_Chi_Minh', 1, 0),
('usr-012', 'light', 'vi', 'Asia/Ho_Chi_Minh', 1, 1),
('usr-013', 'system', 'vi', 'Asia/Ho_Chi_Minh', 1, 1),
('usr-014', 'dark', 'vi', 'Asia/Ho_Chi_Minh', 1, 1),
('usr-015', 'light', 'vi', 'Asia/Ho_Chi_Minh', 1, 1),
('usr-016', 'light', 'vi', 'Asia/Ho_Chi_Minh', 1, 1),
('usr-017', 'system', 'vi', 'Asia/Ho_Chi_Minh', 1, 1),
('usr-018', 'light', 'vi', 'Asia/Ho_Chi_Minh', 1, 0),
('usr-019', 'dark', 'vi', 'Asia/Ho_Chi_Minh', 0, 1),
('usr-020', 'light', 'vi', 'Asia/Ho_Chi_Minh', 1, 1),
('usr-021', 'light', 'vi', 'Asia/Ho_Chi_Minh', 1, 0),
('usr-022', 'light', 'vi', 'Asia/Ho_Chi_Minh', 1, 0);

-- =============================================
-- 15. ACTIVITY_LOGS - Nhật ký hoạt động
-- =============================================
INSERT INTO `activity_logs` (`id`, `user_id`, `action`, `entity_type`, `entity_id`, `ip_address`, `user_agent`, `created_at`) VALUES
-- Login activities
('log-001', 'usr-001', 'login', 'user', 'usr-001', '192.168.1.100', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/122.0.0.0', '2024-03-18 08:00:00'),
('log-002', 'usr-003', 'login', 'user', 'usr-003', '192.168.1.101', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) Safari/605.1.15', '2024-03-18 08:15:00'),
('log-003', 'usr-007', 'login', 'user', 'usr-007', '192.168.1.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Firefox/123.0', '2024-03-18 08:30:00'),

-- Task activities
('log-004', 'usr-007', 'update', 'task', 'tsk-009', '192.168.1.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Firefox/123.0', '2024-03-01 09:00:00'),
('log-005', 'usr-007', 'comment', 'task', 'tsk-009', '192.168.1.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Firefox/123.0', '2024-03-10 14:30:00'),
('log-006', 'usr-010', 'update', 'task', 'tsk-008', '192.168.1.103', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/122.0.0.0', '2024-02-28 17:00:00'),

-- Project activities
('log-007', 'usr-003', 'update', 'project', 'prj-001', '192.168.1.101', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) Safari/605.1.15', '2024-03-15 10:00:00'),
('log-008', 'usr-004', 'create', 'project', 'prj-004', '192.168.1.104', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/122.0.0.0', '2024-05-01 09:00:00'),

-- Document activities
('log-009', 'usr-015', 'upload', 'document', 'doc-006', '192.168.1.105', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) Chrome/122.0.0.0', '2024-01-15 09:00:00'),
('log-010', 'usr-005', 'upload', 'document', 'doc-008', '192.168.1.106', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/122.0.0.0', '2024-02-01 14:00:00'),

-- Member activities
('log-011', 'usr-003', 'add_member', 'project', 'prj-001', '192.168.1.101', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) Safari/605.1.15', '2024-02-01 08:00:00'),
('log-012', 'usr-004', 'add_member', 'project', 'prj-002', '192.168.1.104', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/122.0.0.0', '2024-03-05 09:00:00');

SET FOREIGN_KEY_CHECKS = 1;

-- =============================================
-- HOÀN TẤT SEED DATA
-- Tổng cộng:
-- - 22 Users (đội ngũ TechViet Solutions)
-- - 15 Labels (phân loại công việc)
-- - 8 Projects (dự án thực tế)
-- - 44 Tasks (công việc chi tiết)
-- - 21 Documents (tài liệu dự án)
-- - 17 Calendar Events (lịch họp, deadline)
-- - 15 Comments (thảo luận)
-- - 13 Notifications (thông báo)
-- =============================================
