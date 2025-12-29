# CHANGELOG - TaskFlow

## [2.1.1] - 2024-12-20

### � Sửaa lỗi phân quyền (Permission Fixes)

#### API Endpoints
- **api/update-task.php**: 
  - Thêm kiểm tra quyền `tasks.edit` trước khi cho phép cập nhật
  - Cho phép người tạo task hoặc người được giao task có thể chỉnh sửa
  - Chỉ người có quyền `tasks.edit` mới được thay đổi assignee

- **api/upload-document.php**: 
  - Thêm kiểm tra quyền `documents.create` trước khi upload
  - Chuyển sang sử dụng bootstrap.php thay vì includes/config.php

- **api/create-folder.php**: 
  - Thêm kiểm tra quyền `documents.create` trước khi tạo thư mục
  - Chuyển sang sử dụng bootstrap.php

- **api/checklist.php**: 
  - Thêm kiểm tra quyền `tasks.view` khi xem checklist
  - Thêm kiểm tra quyền `tasks.edit` hoặc là creator/assignee khi thêm/sửa/xóa checklist item

- **api/calendar.php**: 
  - Thêm kiểm tra quyền khi sửa sự kiện (chỉ creator hoặc admin)
  - Thêm kiểm tra quyền khi xóa sự kiện (chỉ creator hoặc admin)

---

## [2.1.0] - 2024-12-20

### 🔧 Sửa lỗi Calendar Module

- **api/calendar.php**: Sửa lỗi không khớp database schema
- **app/models/CalendarEvent.php**: Cập nhật query SQL
- **app/views/calendar/index.php**: Sửa view và JavaScript
- **cron/event-reminders.php**: Cập nhật cron job

---

## Ma trận phân quyền

### System Roles

| Quyền | Admin | Manager | Member | Guest |
|-------|-------|---------|--------|-------|
| users.view | ✅ | ✅ | ✅ | ❌ |
| users.create | ✅ | ❌ | ❌ | ❌ |
| users.edit | ✅ | ❌ | ❌ | ❌ |
| users.delete | ✅ | ❌ | ❌ | ❌ |
| projects.view | ✅ | ✅ | ✅ | ✅ |
| projects.create | ✅ | ✅ | ✅ | ❌ |
| projects.edit | ✅ | ✅ | ❌ | ❌ |
| projects.delete | ✅ | ❌ | ❌ | ❌ |
| tasks.view | ✅ | ✅ | ✅ | ✅ |
| tasks.create | ✅ | ✅ | ✅ | ❌ |
| tasks.edit | ✅ | ✅ | ✅ | ❌ |
| tasks.delete | ✅ | ✅ | ❌ | ❌ |
| documents.view | ✅ | ✅ | ✅ | ✅ |
| documents.create | ✅ | ✅ | ✅ | ❌ |
| documents.edit | ✅ | ✅ | ✅ | ❌ |
| documents.delete | ✅ | ✅ | ❌ | ❌ |
| admin.access | ✅ | ❌ | ❌ | ❌ |

### Project Roles

| Quyền | Owner | Manager | Member | Viewer |
|-------|-------|---------|--------|--------|
| project.edit | ✅ | ✅ | ❌ | ❌ |
| project.delete | ✅ | ❌ | ❌ | ❌ |
| project.members.manage | ✅ | ✅ | ❌ | ❌ |
| tasks.create | ✅ | ✅ | ✅ | ❌ |
| tasks.edit | ✅ | ✅ | own | ❌ |
| tasks.delete | ✅ | ✅ | ❌ | ❌ |

### Quy tắc đặc biệt

1. **Task Edit**: Người dùng có thể edit task nếu:
   - Có quyền `tasks.edit` (admin, manager, member)
   - Là người tạo task
   - Được giao task (assignee)

2. **Task Delete**: Người dùng có thể xóa task nếu:
   - Có quyền `tasks.delete` (admin, manager)
   - Là người tạo task

3. **Calendar Event**: Chỉ người tạo hoặc admin mới có thể sửa/xóa

4. **Document Delete**: Người dùng có thể xóa document nếu:
   - Có quyền `documents.delete`
   - Là người upload
   - Là admin

---

*Cập nhật bởi Kiro AI - 20/12/2024*
