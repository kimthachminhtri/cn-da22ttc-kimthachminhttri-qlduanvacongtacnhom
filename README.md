# TaskFlow - PHP Version

Phiên bản PHP của ứng dụng quản lý dự án TaskFlow với cấu trúc MVC chuyên nghiệp.

## 🆕 Cấu trúc mới (MVC)

```
taskflow/
├── config/                    # Cấu hình hệ thống
│   ├── app.php               # Cấu hình ứng dụng
│   ├── database.php          # Cấu hình DB
│   └── permissions.php       # 4 roles: admin, manager, member, guest
│
├── core/                      # Core hệ thống
│   ├── Database.php          # Database connection
│   ├── Router.php            # URL Router
│   ├── Session.php           # Session management
│   ├── View.php              # View renderer
│   └── Permission.php        # Permission handler
│
├── app/
│   ├── controllers/          # Controllers
│   │   ├── AuthController.php
│   │   ├── DashboardController.php
│   │   ├── ProjectController.php
│   │   ├── TaskController.php
│   │   ├── DocumentController.php
│   │   ├── TeamController.php
│   │   └── AdminController.php
│   ├── models/               # Models
│   │   ├── User.php
│   │   ├── Project.php
│   │   ├── Task.php
│   │   └── Document.php
│   ├── views/                # Views
│   │   ├── layouts/          # main.php, admin.php, guest.php
│   │   ├── dashboard/
│   │   ├── projects/
│   │   ├── tasks/
│   │   ├── team/
│   │   ├── documents/
│   │   └── admin/
│   └── middleware/           # AuthMiddleware, PermissionMiddleware
│
├── public/                    # Web root
│   ├── index.php             # Entry point
│   └── assets/               # css/, js/, images/, uploads/
│
├── routes/web.php            # Route definitions
├── bootstrap.php             # Application bootstrap
└── docs/STRUCTURE.md         # Chi tiết cấu trúc
```

## Hệ thống phân quyền (4 Roles)

| Role | Quyền |
|------|-------|
| **Admin** | Toàn quyền, quản lý users, truy cập Admin Panel |
| **Manager** | Quản lý projects, tasks, documents, team |
| **Member** | Xem projects, tạo/sửa tasks và documents của mình |
| **Guest** | Chỉ xem, không thể tạo/sửa/xóa |

Xem chi tiết: `docs/PHAN_QUYEN.md`

## Cấu trúc cũ (vẫn hoạt động)

```
php/
├── index.php              # Dashboard
├── projects.php           # Danh sách dự án
├── tasks.php              # Công việc
├── documents.php          # Tài liệu
├── team.php               # Nhóm
├── includes/              # Config, functions, classes
└── components/            # UI components
```

## Cài đặt

### 1. Import Database Schema
```bash
# Mở phpMyAdmin: http://localhost/phpmyadmin
# Tạo database: taskflow2
# Import file: database/taskflow2.sql
```

### 2. Import Dữ liệu mẫu
Có 2 cách:
- **Cách 1:** Import file `database/seed.sql` vào phpMyAdmin
- **Cách 2:** Truy cập http://localhost/php/database/seed-web.php và click "Tạo dữ liệu Demo"

### 3. Tài khoản Demo
Password cho tất cả: **password123**
- admin@taskflow.com (Admin)
- manager@taskflow.com (Manager)
- designer@taskflow.com (Member)
- frontend@taskflow.com (Member)
- backend@taskflow.com (Member)

### 4. Chạy ứng dụng
Với XAMPP: Truy cập http://localhost/php

Hoặc với PHP built-in server:
```bash
cd php
php -S localhost:8000
```
Truy cập: http://localhost:8000

## Tính năng đã chuyển đổi

### Dashboard
- ✅ Thống kê (dự án, công việc hoàn thành, đang làm, quá hạn)
- ✅ Danh sách dự án đang thực hiện
- ✅ Công việc của bạn
- ✅ Hoạt động gần đây

### Quản lý dự án
- ✅ Danh sách với filter (All, Active, Planning, On Hold)
- ✅ Chi tiết dự án với 5 tab:
  - Overview (mô tả, tiến độ, thông tin, thành viên)
  - Kanban Board
  - Timeline (deadline theo tháng)
  - Tệp đính kèm
  - Thành viên
- ✅ Modal tạo dự án mới

### Quản lý công việc
- ✅ Kanban Board (5 cột: Backlog, Todo, In Progress, Review, Done)
- ✅ List View (collapsible groups)
- ✅ Chi tiết công việc:
  - Mô tả, Checklist với progress bar
  - Bình luận
  - Tags, Labels
  - Thông tin (assignee, due date, estimated/actual hours)
  - Actions (copy, move, archive, delete)
- ✅ Modal tạo công việc mới

### Lịch
- ✅ Calendar view theo tháng
- ✅ Hiển thị tasks theo ngày deadline
- ✅ Navigation (prev/next month, today)
- ✅ Công việc sắp đến hạn

### Tài liệu
- ✅ Grid View và List View
- ✅ Filter (All, Starred, Folder, PDF, Word, Excel, Image)
- ✅ Sort (Date, Name, Size)
- ✅ Breadcrumbs navigation
- ✅ Star/Unstar documents
- ✅ Modal tạo thư mục
- ✅ Modal upload file

### Nhóm
- ✅ Danh sách thành viên với avatar
- ✅ Role badges (Admin, Member, Viewer)
- ✅ Số công việc được giao
- ✅ Contact (email, phone)
- ✅ Modal mời thành viên mới

### Cài đặt
- ✅ Tab Hồ sơ (avatar, thông tin cá nhân)
- ✅ Tab Bảo mật (đổi mật khẩu)
- ✅ Tab Thông báo (toggle settings)
- ✅ Tab Giao diện (theme, ngôn ngữ)

### Tính năng chung
- ✅ Sidebar navigation với badge
- ✅ Search modal (Ctrl+K / Cmd+K)
- ✅ Notifications dropdown
- ✅ Create button (dự án, công việc)
- ✅ Activity timeline
- ✅ Notifications page

## Công nghệ

- PHP 7.4+ / 8.x
- Tailwind CSS (CDN)
- Alpine.js (interactivity)
- Lucide Icons

## Skeleton Loading

Các trang hỗ trợ skeleton loading để cải thiện UX:
- Dashboard (`index.php?loading`)
- Documents (`documents.php?loading`)
- Team (`team.php?loading`)

Thêm `?loading` vào URL để xem demo skeleton loading.

### Sử dụng Loading trong code:

```javascript
// Show/hide skeleton
Loading.show('skeleton-id', 'content-id');
Loading.hide('skeleton-id', 'content-id');

// Button loading state
Loading.buttonStart(buttonElement);
Loading.buttonStop(buttonElement);
```

## Cài đặt Database

### 1. Tạo database
Mở phpMyAdmin và import file `database/taskflow2.sql`

Hoặc chạy lệnh:
```bash
mysql -u root -p < database/taskflow2.sql
```

### 2. Cấu hình
Chỉnh sửa trong `includes/config.php`:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'taskflow2');
define('DB_USER', 'root');
define('DB_PASS', '');
```

### 3. Tài khoản Demo
Password cho tất cả: **password123**
- admin@taskflow.com (Admin)
- manager@taskflow.com (Manager)
- designer@taskflow.com (Member)
- frontend@taskflow.com (Member)
- backend@taskflow.com (Member)

### 4. Seed dữ liệu (tùy chọn)
```bash
php database/seed-demo.php
```

## Database Classes

### Classes có sẵn:
- `Database` - Singleton PDO wrapper
- `Model` - Base model class
- `User` - User model
- `Project` - Project model  
- `Task` - Task model
- `Document` - Document model
- `Auth` - Authentication & Authorization

### Sử dụng:
```php
// Database trực tiếp
$db = Database::getInstance();
$users = $db->fetchAll("SELECT * FROM users");

// Sử dụng Model
$userModel = new User();
$user = $userModel->find('user-id');
$user = $userModel->findByEmail('test@example.com');

$taskModel = new Task();
$tasks = $taskModel->getByProject('project-id');
$overdue = $taskModel->getOverdue();
```

Xem thêm: `api/example-usage.php`

## Ghi chú

- Dữ liệu mẫu trong `includes/data.php` (mock data)
- Database schema có sẵn trong `scripts/001-database-schema.sql`
- Để dùng database thật, import schema và thay mock data bằng Model queries
#   c n - d a 2 2 t t c - k i m t h a c h m i n h t t r i - q l d u a n v a c o n g t a c n h o m  
 