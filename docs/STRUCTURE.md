# Cấu Trúc Dự Án TaskFlow

## Tổng Quan

```
taskflow/
├── config/                    # Cấu hình hệ thống
│   ├── app.php               # Cấu hình ứng dụng
│   ├── database.php          # Cấu hình kết nối DB
│   └── permissions.php       # Định nghĩa 4 quyền: admin, manager, member, guest
│
├── core/                      # Core hệ thống
│   ├── Database.php          # Database connection (Singleton)
│   ├── Router.php            # URL Router
│   ├── Session.php           # Session management
│   ├── View.php              # View renderer
│   └── Permission.php        # Permission handler (4 roles)
│
├── app/                       # Logic ứng dụng
│   ├── controllers/          # Controllers xử lý request
│   │   ├── BaseController.php
│   │   ├── AuthController.php
│   │   ├── ProjectController.php
│   │   ├── TaskController.php
│   │   ├── TeamController.php
│   │   └── DocumentController.php
│   │
│   ├── models/               # Models tương tác DB
│   │   ├── BaseModel.php
│   │   ├── User.php
│   │   ├── Project.php
│   │   ├── Task.php
│   │   └── Document.php
│   │
│   ├── views/                # Templates giao diện
│   │   ├── layouts/
│   │   │   ├── main.php      # Layout cho member/manager
│   │   │   ├── admin.php     # Layout cho admin
│   │   │   └── guest.php     # Layout cho guest
│   │   ├── auth/
│   │   │   ├── login.php
│   │   │   └── register.php
│   │   ├── components/
│   │   │   ├── sidebar.php
│   │   │   └── header.php
│   │   └── errors/
│   │       ├── 403.php
│   │       └── 404.php
│   │
│   └── middleware/           # Middleware xử lý request
│       ├── AuthMiddleware.php
│       └── PermissionMiddleware.php
│
├── public/                    # Thư mục public (web root)
│   ├── index.php             # Điểm vào chính
│   ├── assets/               # Tài nguyên tĩnh
│   │   ├── css/
│   │   ├── js/
│   │   ├── images/
│   │   └── uploads/          # Upload tài liệu (public)
│   └── .htaccess             # Rewrite rules
│
├── routes/                    # Route definitions
│   └── web.php               # Web routes
│
├── database/                  # Database files
│   ├── taskflow2.sql         # Schema
│   └── seed-web.php          # Seed data
│
├── includes/                  # Legacy files (backward compatibility)
│   ├── classes/              # Old classes
│   ├── config.php            # Old config
│   └── functions.php         # Helper functions
│
├── logs/                      # Log files
├── docs/                      # Documentation
├── bootstrap.php              # Application bootstrap
├── .env.example              # Environment example
└── README.md
```

## Hệ Thống Phân Quyền (4 Roles)

### 1. Admin
- Toàn quyền trong hệ thống
- Quản lý users, projects, tasks, documents
- Truy cập Admin Panel
- Xem báo cáo và cài đặt hệ thống

### 2. Manager
- Quản lý projects và team
- Tạo, sửa, xóa tasks
- Quản lý documents
- Không thể xóa projects hoặc quản lý users

### 3. Member
- Xem projects
- Tạo và sửa tasks (của mình)
- Upload và sửa documents (của mình)
- Không thể xóa tasks/documents của người khác

### 4. Guest
- Chỉ xem projects, tasks, documents
- Không thể tạo, sửa, xóa bất kỳ thứ gì

## Cách Sử Dụng

### Kiểm tra quyền trong Controller
```php
use App\Middleware\PermissionMiddleware;

// Kiểm tra một quyền
if (!PermissionMiddleware::can('projects.create')) {
    return; // Tự động redirect 403
}

// Kiểm tra admin
if (!PermissionMiddleware::requireAdmin()) {
    return;
}
```

### Kiểm tra quyền trong View
```php
use Core\Permission;
use Core\Session;

$role = Session::get('user_role', 'guest');

if (Permission::can($role, 'projects.create')): ?>
    <button>Tạo dự án</button>
<?php endif; ?>
```

### Helper functions
```php
// Kiểm tra quyền
if (can('tasks.delete')) { ... }

// Lấy user ID
$userId = userId();

// Lấy role
$role = userRole();

// Kiểm tra admin/manager
if (isAdmin()) { ... }
if (isManager()) { ... }
```

## Migration từ Cấu Trúc Cũ

Dự án vẫn giữ backward compatibility với cấu trúc cũ:
- Files trong `includes/classes/` vẫn hoạt động
- Files PHP ở root (login.php, projects.php...) vẫn hoạt động
- Có thể migrate dần sang cấu trúc mới

## Chạy Ứng Dụng

1. Copy `.env.example` thành `.env` và cấu hình
2. Import database từ `database/taskflow2.sql`
3. Chạy seed: `php database/seed-web.php`
4. Truy cập: `http://localhost/php/`
