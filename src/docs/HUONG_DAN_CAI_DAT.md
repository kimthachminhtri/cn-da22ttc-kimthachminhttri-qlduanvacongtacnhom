# Hướng Dẫn Cài Đặt và Chạy Dự Án TaskFlow

## Yêu Cầu Hệ Thống

- **PHP** >= 7.4 (khuyến nghị PHP 8.x)
- **MySQL** >= 5.7 hoặc MariaDB >= 10.3
- **Web Server**: Apache (với mod_rewrite) hoặc Nginx
- **XAMPP/WAMP/MAMP** (cho Windows/Mac) hoặc LAMP (cho Linux)

---

## Cách 1: Sử Dụng XAMPP (Khuyến Nghị cho Windows)

### Bước 1: Cài đặt XAMPP
1. Tải XAMPP từ: https://www.apachefriends.org/
2. Cài đặt và khởi động **Apache** và **MySQL**

### Bước 2: Copy dự án
```bash
# Copy thư mục dự án vào htdocs
# Windows: C:\xampp\htdocs\php
# Mac: /Applications/XAMPP/htdocs/php
```

### Bước 3: Tạo Database
1. Mở phpMyAdmin: http://localhost/phpmyadmin
2. Tạo database mới tên: `taskflow2`
3. Chọn database `taskflow2`
4. Import file: `database/taskflow2.sql`

### Bước 4: Cấu hình Database
Mở file `config/database.php` và kiểm tra:
```php
return [
    'host' => 'localhost',
    'database' => 'taskflow2',
    'username' => 'root',
    'password' => '',  // Mặc định XAMPP không có password
];
```

### Bước 5: Tạo dữ liệu mẫu
Truy cập: http://localhost/php/database/seed-web.php
- Click **"Tạo dữ liệu Demo"**

### Bước 6: Chạy ứng dụng
Truy cập: http://localhost/php

---

## Cách 2: Sử Dụng PHP Built-in Server

### Bước 1: Mở Terminal/Command Prompt
```bash
cd đường-dẫn-đến-thư-mục-dự-án
```

### Bước 2: Chạy server
```bash
php -S localhost:8000
```

### Bước 3: Truy cập
Mở trình duyệt: http://localhost:8000

---

## Tài Khoản Demo

Sau khi seed dữ liệu, sử dụng các tài khoản sau:

| Email | Mật khẩu | Role | Quyền |
|-------|----------|------|-------|
| admin@taskflow.com | password123 | Admin | Toàn quyền |
| manager@taskflow.com | password123 | Manager | Quản lý dự án, team |
| designer@taskflow.com | password123 | Member | Xem, tạo task |
| frontend@taskflow.com | password123 | Member | Xem, tạo task |
| backend@taskflow.com | password123 | Member | Xem, tạo task |

---

## Cấu Trúc URL

| URL | Mô tả |
|-----|-------|
| `/php/` | Dashboard |
| `/php/login.php` | Đăng nhập |
| `/php/register.php` | Đăng ký |
| `/php/projects.php` | Danh sách dự án |
| `/php/tasks.php` | Danh sách công việc |
| `/php/documents.php` | Tài liệu |
| `/php/team.php` | Quản lý nhóm |
| `/php/calendar.php` | Lịch |
| `/php/reports.php` | Báo cáo |
| `/php/settings.php` | Cài đặt |
| `/php/notifications.php` | Thông báo |
| `/php/admin/users.php` | Admin Panel (chỉ Admin) |

---

## Xử Lý Lỗi Thường Gặp

### Lỗi 1: "Không thể kết nối database"
```
Kiểm tra:
1. MySQL đã chạy chưa
2. Database 'taskflow2' đã tạo chưa
3. Thông tin trong config/database.php đúng chưa
```

### Lỗi 2: "Class not found"
```
Kiểm tra:
1. PHP version >= 7.4
2. File bootstrap.php tồn tại
3. Các file trong core/, app/ đầy đủ
```

### Lỗi 3: "Permission denied" khi upload
```bash
# Linux/Mac: Cấp quyền cho thư mục uploads
chmod -R 755 uploads/
chmod -R 755 public/assets/uploads/
```

### Lỗi 4: Trang trắng không hiển thị gì
```php
// Bật hiển thị lỗi trong bootstrap.php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

### Lỗi 5: "Invalid password" khi đăng nhập
```
Chạy lại seed để reset password:
http://localhost/php/database/seed-web.php
```

---

## Hệ Thống Phân Quyền

### 4 Roles:

| Role | Quyền |
|------|-------|
| **Admin** | Toàn quyền: quản lý users, projects, tasks, documents, settings |
| **Manager** | Quản lý projects, tasks, documents, team. Không xóa projects |
| **Member** | Xem projects, tạo/sửa tasks và documents của mình |
| **Guest** | Chỉ xem, không tạo/sửa/xóa |

### Kiểm tra quyền trong code:
```php
// Trong Controller
if (!$this->can('projects.create')) {
    // Không có quyền
}

// Trong View
<?php if (Permission::can($userRole, 'tasks.delete')): ?>
    <button>Xóa</button>
<?php endif; ?>
```

---

## API Endpoints

Tất cả API trong thư mục `/api/`:

| Method | Endpoint | Mô tả |
|--------|----------|-------|
| POST | `/api/create-project.php` | Tạo dự án |
| PUT | `/api/update-project.php` | Cập nhật dự án |
| DELETE | `/api/update-project.php` | Xóa dự án |
| POST | `/api/create-task.php` | Tạo task |
| PUT | `/api/update-task.php` | Cập nhật task |
| DELETE | `/api/update-task.php` | Xóa task |
| POST | `/api/upload-document.php` | Upload tài liệu |
| DELETE | `/api/delete-document.php` | Xóa tài liệu |

---

## Môi Trường Development vs Production

### Development (mặc định):
```php
// bootstrap.php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

### Production:
```php
// bootstrap.php
error_reporting(0);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', BASE_PATH . '/logs/error.log');
```

---

## Liên Hệ & Hỗ Trợ

- Xem thêm: `docs/STRUCTURE.md` - Cấu trúc dự án
- Xem thêm: `docs/PHAN_QUYEN.md` - Chi tiết phân quyền
- Issues: Tạo issue trên repository

---

## Quick Start (TL;DR)

```bash
# 1. Copy dự án vào htdocs (XAMPP)
# 2. Tạo database 'taskflow2' trong phpMyAdmin
# 3. Import database/taskflow2.sql
# 4. Truy cập http://localhost/php/database/seed-web.php để tạo data
# 5. Đăng nhập: admin@taskflow.com / password123
# 6. Done! 🎉
```
