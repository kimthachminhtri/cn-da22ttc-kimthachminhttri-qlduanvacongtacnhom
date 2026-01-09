# 📊 TÀI LIỆU DỮ LIỆU MẪU TASKFLOW

## 🎯 MỤC ĐÍCH
Dữ liệu mẫu chuyên nghiệp phục vụ:
- **Demo sản phẩm** cho khách hàng và stakeholders
- **Kiểm thử chức năng** toàn diện
- **Bảo vệ đồ án** với dữ liệu thực tế
- **Training** người dùng mới

---

## 📋 TỔNG QUAN DỮ LIỆU

### Thống kê theo bảng

| Bảng | Số lượng | Mô tả |
|------|----------|-------|
| `users` | 25 | Đội ngũ công ty phần mềm |
| `labels` | 12 | Nhãn phân loại task |
| `projects` | 10 | Dự án đa dạng trạng thái |
| `project_members` | 55 | Phân quyền thành viên |
| `tasks` | 133 | Tasks với đầy đủ tình huống |
| `task_assignees` | 105 | Phân công công việc |
| `task_labels` | 35 | Gắn nhãn task |
| `task_checklists` | 28 | Checklist chi tiết |
| `documents` | 20 | Tài liệu dự án |
| `document_shares` | 10 | Chia sẻ tài liệu |
| `comments` | 28 | Bình luận trao đổi |
| `notifications` | 20 | Thông báo hệ thống |
| `activity_logs` | 20 | Lịch sử hoạt động |
| `calendar_events` | 15 | Sự kiện & cuộc họp |
| `event_attendees` | 30 | Người tham dự |
| `user_settings` | 24 | Cài đặt cá nhân |

---

## 👥 NGƯỜI DÙNG (25 users)

### Phân bố vai trò

| Vai trò | Số lượng | Mô tả |
|---------|----------|-------|
| Admin | 2 | CEO, CTO |
| Manager | 5 | PM, Tech Lead, Scrum Master, Design Lead |
| Member | 15 | Developers, Designers, QA, DevOps, BA |
| Guest | 2 | Khách hàng VinGroup, FPT |
| Inactive | 1 | Nhân viên đã nghỉ |

### Tài khoản demo chính

| Email | Mật khẩu | Vai trò | Ghi chú |
|-------|----------|---------|---------|
| `ceo@saigontech.vn` | `password` | Admin | CEO - Toàn quyền |
| `cto@saigontech.vn` | `password` | Admin | CTO - Quản lý kỹ thuật |
| `pm.hung@saigontech.vn` | `password` | Manager | Senior PM |
| `backend.tuan@saigontech.vn` | `password` | Member | Senior Backend Dev |
| `client.vingroup@gmail.com` | `password` | Guest | Khách hàng |

> **Lưu ý**: Tất cả mật khẩu đều là `password` (hash: `$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi`)

---

## 🏗️ DỰ ÁN (10 projects)

### Phân bố trạng thái

| Trạng thái | Số lượng | Dự án |
|------------|----------|-------|
| Active | 4 | VinMart, FPT Bank, HRMS, MedCare |
| Planning | 2 | SmartBot AI, LogiTrack |
| Completed | 2 | DevOps Infrastructure, Website Redesign |
| On Hold | 1 | EduTech |
| Cancelled | 1 | CryptoWallet |

### Chi tiết dự án

#### 1. VinMart E-commerce (PRJ-001-VINMART)
- **Trạng thái**: Active (72%)
- **Team**: 12 người
- **Tasks**: 35 tasks
- **Đặc điểm**: Dự án lớn nhất, có khách hàng guest

#### 2. FPT Mobile Banking (PRJ-002-FPTBANK)
- **Trạng thái**: Active (45%)
- **Team**: 10 người
- **Tasks**: 30 tasks
- **Đặc điểm**: Yêu cầu bảo mật cao (PCI-DSS)

#### 3. HRMS Internal (PRJ-003-HRMS)
- **Trạng thái**: Active (88%)
- **Team**: 6 người
- **Tasks**: 20 tasks
- **Đặc điểm**: Dự án nội bộ, sắp hoàn thành

#### 4. MedCare Healthcare (PRJ-004-MEDCARE)
- **Trạng thái**: Active (25%)
- **Team**: 8 người
- **Tasks**: 15 tasks
- **Đặc điểm**: Dự án mới, tích hợp HL7 FHIR

---

## ✅ TASKS (133 tasks)

### Phân bố trạng thái

```
Backlog:     ████████████████████████████ 35 (26%)
Todo:        █████████████████ 22 (17%)
In Progress: ██████████████ 18 (14%)
In Review:   ███ 4 (3%)
Done:        ████████████████████████████████████████ 54 (40%)
```

### Tình huống nghiệp vụ đặc biệt

| Tình huống | Task ID | Mô tả |
|------------|---------|-------|
| **Quá hạn (Overdue)** | TSK-001-VM-OVD1 | Fix Payment Gateway Timeout |
| **Quá hạn (Overdue)** | TSK-001-VM-OVD2 | Mobile Responsive Issues |
| **Quá hạn (Overdue)** | TSK-002-FB-OVD1 | OTP Delivery Delay |
| **Không có assignee** | TSK-001-VM-NOASGN | Product Image Optimization |
| **Không có assignee** | TSK-002-FB-NOASGN | App Store Optimization |
| **Không có assignee** | TSK-003-HR-NOASGN | Dark Mode Support |
| **Nhiều assignees** | TSK-001-VM-002 | Setup Project Architecture |
| **Nhiều assignees** | TSK-002-FB-002 | Biometric Authentication |
| **Nhân viên đã nghỉ** | TSK-009-ED-003 | Course Management API |

---

## 🎯 HƯỚNG DẪN SỬ DỤNG

### Import dữ liệu

```bash
# MySQL Command Line
mysql -u root -p taskflow < database/seed-professional-v2.sql

# Hoặc qua phpMyAdmin
# 1. Chọn database taskflow
# 2. Tab Import
# 3. Chọn file seed-professional-v2.sql
# 4. Click Go
```

### Queries demo hữu ích

```sql
-- 1. Thống kê tasks theo trạng thái
SELECT status, COUNT(*) as count 
FROM tasks 
GROUP BY status;

-- 2. Tasks quá hạn
SELECT t.title, t.due_date, p.name as project
FROM tasks t
JOIN projects p ON t.project_id = p.id
WHERE t.due_date < CURDATE() 
AND t.status NOT IN ('done');

-- 3. Workload theo member
SELECT u.full_name, COUNT(ta.task_id) as task_count
FROM users u
LEFT JOIN task_assignees ta ON u.id = ta.user_id
LEFT JOIN tasks t ON ta.task_id = t.id AND t.status = 'in_progress'
WHERE u.role = 'member' AND u.is_active = 1
GROUP BY u.id
ORDER BY task_count DESC;

-- 4. Tiến độ dự án
SELECT name, status, progress, 
       (SELECT COUNT(*) FROM tasks WHERE project_id = p.id) as total_tasks,
       (SELECT COUNT(*) FROM tasks WHERE project_id = p.id AND status = 'done') as done_tasks
FROM projects p
WHERE status = 'active';

-- 5. Hoạt động gần đây
SELECT al.action, al.entity_type, u.full_name, al.created_at
FROM activity_logs al
JOIN users u ON al.user_id = u.id
ORDER BY al.created_at DESC
LIMIT 20;
```

---

## 📊 DEMO SCENARIOS

### Scenario 1: Dashboard Overview
- Hiển thị tổng quan tasks theo trạng thái
- Biểu đồ tiến độ dự án
- Tasks quá hạn cần xử lý
- Hoạt động gần đây

### Scenario 2: Project Management
- Xem chi tiết dự án VinMart
- Kanban board với các tasks
- Team members và phân quyền
- Documents và comments

### Scenario 3: Task Workflow
- Tạo task mới
- Assign cho member
- Cập nhật trạng thái
- Thêm comment và checklist

### Scenario 4: Reporting
- Báo cáo hiệu suất team
- Tasks hoàn thành theo thời gian
- Workload distribution
- Overdue analysis

### Scenario 5: User Management
- Phân quyền theo vai trò
- Guest access (viewer only)
- Inactive user handling

---

## ⚠️ LƯU Ý QUAN TRỌNG

1. **Backup trước khi import**: Dữ liệu cũ sẽ bị xóa
2. **Foreign key checks**: Script tự động disable/enable
3. **UUID format**: Sử dụng ID có ý nghĩa để dễ debug
4. **Dates**: Dữ liệu sử dụng ngày tháng thực tế (2024-2025)
5. **Password**: Tất cả đều là `password` cho mục đích demo

---

## 📁 FILES

- `seed-professional-v2.sql` - File SQL chính
- `SEED_DATA_DOCUMENTATION.md` - Tài liệu này

---

*Tạo bởi: Senior Product Analyst*
*Version: 2.0*
*Cập nhật: January 2025*
