# TaskFlow Database

## Cấu trúc thư mục

```
database/
├── schema.sql      # Schema đầy đủ - tạo tất cả bảng
├── seed.sql        # Dữ liệu mẫu (SQL thuần)
├── seed-users.php  # Script PHP để seed data (khuyên dùng)
└── README.md       # File này
```

## Hướng dẫn cài đặt

### Bước 1: Tạo Database

```sql
CREATE DATABASE taskflow CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### Bước 2: Tạo Schema

**Cách 1: Qua phpMyAdmin**
1. Mở phpMyAdmin
2. Chọn database `taskflow`
3. Tab "Import" → chọn file `schema.sql`
4. Click "Go"

**Cách 2: Qua MySQL CLI**
```bash
mysql -u root -p taskflow < schema.sql
```

### Bước 3: Seed Data

**Cách 1: Qua trình duyệt (khuyên dùng)**
```
http://localhost/php/database/seed-users.php
```

**Cách 2: Qua phpMyAdmin**
- Import file `seed.sql`

## Danh sách bảng

| Bảng | Mô tả |
|------|-------|
| `users` | Người dùng |
| `labels` | Nhãn |
| `projects` | Dự án |
| `project_members` | Thành viên dự án |
| `tasks` | Công việc |
| `task_assignees` | Người được giao task |
| `task_labels` | Nhãn của task |
| `task_checklists` | Checklist của task |
| `documents` | Tài liệu |
| `document_shares` | Chia sẻ tài liệu |
| `comments` | Bình luận |
| `notifications` | Thông báo |
| `activity_logs` | Lịch sử hoạt động |
| `calendar_events` | Sự kiện lịch |
| `event_attendees` | Người tham dự sự kiện |
| `user_settings` | Cài đặt người dùng |

## Tài khoản Demo

| Email | Password | Role |
|-------|----------|------|
| admin@taskflow.com | password123 | Admin |
| manager@taskflow.com | password123 | Manager |
| designer@taskflow.com | password123 | Member |
| frontend@taskflow.com | password123 | Member |
| backend@taskflow.com | password123 | Member |
| qa@taskflow.com | password123 | Member |
| devops@taskflow.com | password123 | Member |
| ba@taskflow.com | password123 | Guest |

## Phân quyền (Roles)

- **Admin**: Toàn quyền hệ thống
- **Manager**: Quản lý dự án, team, xóa tasks/docs
- **Member**: Tạo/sửa tasks, documents
- **Guest**: Chỉ xem

## ERD (Entity Relationship)

```
users ─────────────────┬─────────────────────────────────────┐
  │                    │                                     │
  │ 1:N                │ N:M                                 │ 1:N
  ▼                    ▼                                     ▼
projects ◄──── project_members                          user_settings
  │
  │ 1:N
  ▼
tasks ◄──────── task_assignees ────► users
  │
  ├──── task_labels ────► labels
  │
  ├──── task_checklists
  │
  └──── comments ◄────── users

documents ◄──── document_shares ────► users
  │
  └──── comments

calendar_events ◄──── event_attendees ────► users
```

## Reset Database

```sql
-- Xóa tất cả dữ liệu (giữ schema)
SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE event_attendees;
TRUNCATE TABLE calendar_events;
TRUNCATE TABLE activity_logs;
TRUNCATE TABLE notifications;
TRUNCATE TABLE comments;
TRUNCATE TABLE document_shares;
TRUNCATE TABLE documents;
TRUNCATE TABLE task_checklists;
TRUNCATE TABLE task_labels;
TRUNCATE TABLE task_assignees;
TRUNCATE TABLE tasks;
TRUNCATE TABLE project_members;
TRUNCATE TABLE projects;
TRUNCATE TABLE labels;
TRUNCATE TABLE user_settings;
TRUNCATE TABLE users;
SET FOREIGN_KEY_CHECKS = 1;
```
