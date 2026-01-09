# TaskFlow - Hệ thống Quản lý Dự án và Công tác Nhóm

[![Version](https://img.shields.io/badge/version-2.0.0-blue.svg)](https://github.com/your-repo/taskflow)
[![PHP](https://img.shields.io/badge/PHP-8.x-777BB4.svg)](https://php.net)
[![License](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE)
[![Completion](https://img.shields.io/badge/completion-91%25-brightgreen.svg)](docs/BAO_CAO_KIEM_DINH_HE_THONG.md)

TaskFlow là ứng dụng web quản lý dự án được xây dựng bằng PHP thuần với kiến trúc MVC, hỗ trợ quản lý dự án, công việc, tài liệu và cộng tác nhóm.

## ✨ Tính năng chính

### Quản lý Dự án
- Tạo, chỉnh sửa, xóa dự án với thông tin đầy đủ
- Kanban board với 5 trạng thái (Backlog, Todo, In Progress, Review, Done)
- Theo dõi tiến độ với thanh progress bar
- Quản lý thành viên và phân quyền trong dự án
- Chuyển quyền sở hữu dự án

### Quản lý Công việc
- Tạo task với độ ưu tiên, deadline, người thực hiện
- Checklist cho từng task với progress tracking
- Bình luận và thảo luận trên task
- Đính kèm file vào task
- Drag & drop trên Kanban board

### Lịch và Timeline
- Calendar view hiển thị task và sự kiện theo tháng
- Gantt chart cho cái nhìn timeline
- Tạo sự kiện với nhắc nhở
- Xem task sắp đến hạn và quá hạn

### Quản lý Tài liệu
- Upload file với nhiều định dạng (PDF, Word, Excel, ảnh, ZIP)
- Tổ chức theo thư mục
- Tìm kiếm và lọc tài liệu
- Đánh dấu sao file quan trọng

### Báo cáo và Thống kê
- Dashboard với thống kê tổng quan
- Biểu đồ trạng thái công việc, độ ưu tiên
- Báo cáo năng suất thành viên
- Xuất báo cáo CSV/JSON/PDF

### 🔔 Real-time Notifications (MỚI)
- Server-Sent Events (SSE) cho thông báo real-time
- Browser notifications với quyền người dùng
- Auto-reconnect khi mất kết nối
- Badge hiển thị số thông báo chưa đọc
- Notification sound

### 📧 Email System (MỚI)
- Hỗ trợ nhiều driver: mail(), SMTP, log
- Email templates đẹp với HTML
- Gửi email reset password
- Dễ dàng cấu hình qua .env

### 💾 Backup & Restore (MỚI)
- Tạo backup database tự động
- Download backup files
- Restore từ backup
- Quản lý backup trong Admin Panel

### Hệ thống phân quyền
- 4 vai trò: Admin, Manager, Member, Guest
- Phân quyền chi tiết theo từng chức năng
- Admin Panel riêng cho quản trị viên

## 🛠 Công nghệ sử dụng

- **Backend:** PHP 8.x với kiến trúc MVC
- **Database:** MySQL/MariaDB
- **Frontend:** Tailwind CSS, Alpine.js
- **Icons:** Lucide Icons
- **Charts:** Chart.js
- **Real-time:** Server-Sent Events (SSE)
- **Email:** Native mail() / SMTP


## 📁 Cấu trúc thư mục

```
taskflow/
├── app/
│   ├── controllers/          # Controllers xử lý logic
│   ├── models/               # Models tương tác database
│   ├── views/                # Views hiển thị giao diện
│   │   ├── layouts/          # Layout templates
│   │   ├── components/       # Reusable components
│   │   ├── dashboard/
│   │   ├── projects/
│   │   ├── tasks/
│   │   ├── calendar/
│   │   ├── documents/
│   │   ├── reports/
│   │   ├── settings/
│   │   └── admin/
│   └── middleware/           # Middleware (Auth, Permission)
├── api/                      # API endpoints
│   ├── sse-notifications.php # Real-time notifications (SSE)
│   ├── admin-export.php      # Export CSV/JSON/PDF
│   └── ...
├── config/                   # Cấu hình hệ thống
│   ├── app.php
│   ├── mail.php              # Email configuration
│   └── permissions.php
├── core/                     # Core classes
│   ├── Database.php
│   ├── Session.php
│   ├── View.php
│   ├── Permission.php
│   ├── Logger.php
│   ├── Validator.php
│   ├── Mailer.php            # Email service
│   └── RateLimiter.php
├── cron/                     # Cron jobs
│   └── event-reminders.php   # Event & task reminders
├── database/                 # Database files
│   ├── taskflow2.sql         # Schema chính
│   ├── seed-professional-v2.sql  # Dữ liệu mẫu chuyên nghiệp (khuyến nghị)
│   ├── seed-professional.sql # Dữ liệu mẫu chuyên nghiệp v1
│   ├── seed.sql              # Dữ liệu mẫu cơ bản
│   ├── SEED_DATA_DOCUMENTATION.md # Tài liệu dữ liệu mẫu
│   └── migrate-fix-critical-issues.sql
├── docs/                     # Tài liệu
├── public/
│   ├── js/
│   │   └── realtime-notifications.js  # SSE client
│   └── css/
├── includes/                 # Legacy includes
├── logs/                     # Log files
├── uploads/                  # Uploaded files
├── storage/
│   ├── backups/              # Database backups
│   └── rate_limits/          # Rate limiter cache
├── bootstrap.php             # Application bootstrap
└── index.php                 # Entry point
```

## 🚀 Cài đặt

### Yêu cầu hệ thống
- PHP 8.0 trở lên
- MySQL 5.7 / MariaDB 10.3 trở lên
- Apache hoặc Nginx
- Composer (tùy chọn)

### Bước 1: Clone repository
```bash
git clone https://github.com/your-repo/taskflow.git
cd taskflow
```

### Bước 2: Tạo database
```bash
# Tạo database taskflow2
mysql -u root -p -e "CREATE DATABASE taskflow2 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"

# Import schema
mysql -u root -p taskflow2 < database/taskflow2.sql
```

Hoặc sử dụng phpMyAdmin:
1. Tạo database mới tên `taskflow2`
2. Import file `database/taskflow2.sql`

### Bước 3: Cấu hình
Sao chép file cấu hình mẫu và chỉnh sửa:
```bash
cp .env.example .env
```

Chỉnh sửa file `.env`:
```env
# Database
DB_HOST=localhost
DB_NAME=taskflow2
DB_USER=root
DB_PASS=

# Application
APP_ENV=development
APP_DEBUG=true
APP_URL=http://localhost:81/php

# Email (tùy chọn)
MAIL_DRIVER=log          # mail, smtp, hoặc log
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@taskflow.com
MAIL_FROM_NAME=TaskFlow
```

### Bảo mật và triển khai (.env)

- Sử dụng file `.env` để lưu các thông tin nhạy cảm (DB credentials, APP_ENV). Đã cung cấp file mẫu `.env.example`.
- Trên máy chủ production, đặt `APP_ENV=production` và đảm bảo `display_errors` bị tắt.
- Không commit file `.env` vào kho mã nguồn (thêm `/.env` vào `.gitignore` nếu cần).

Ví dụ nhanh:
```bash
# Sao chép file mẫu và chỉnh giá trị
cp .env.example .env
# Chỉnh .env: DB_HOST, DB_NAME, DB_USER, DB_PASS, APP_ENV=production
```

### Ngăn thực thi file trong `uploads/`

- Đã thêm `.htaccess` trong `uploads/`, `uploads/documents/` và `uploads/avatars/` để ngăn thực thi PHP và tắt indexing. Nếu bạn dùng Nginx, đảm bảo cấu hình server chặn thực thi PHP trong thư mục uploads.

Ví dụ (Nginx):
```nginx
location ~* /uploads/ {
	deny all;
}
```

### Tùy chọn: đọc `.env` tự động

- Để đọc `.env` tự động trong PHP local, cân nhắc sử dụng `vlucas/phpdotenv` (cài bằng Composer). Sau đó, load `Dotenv\Dotenv::createImmutable(__DIR__)->load()` trong `bootstrap.php`.


Hoặc chỉnh sửa trực tiếp `includes/config.php`:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'taskflow2');
define('DB_USER', 'root');
define('DB_PASS', '');
```

### Bước 4: Tạo dữ liệu mẫu (tùy chọn)

**Khuyến nghị:** Sử dụng bộ dữ liệu chuyên nghiệp v2
```bash
mysql -u root -p taskflow2 < database/seed-professional-v2.sql
```

Hoặc sử dụng các file khác:
```bash
# Dữ liệu mẫu cơ bản
mysql -u root -p taskflow2 < database/seed.sql

# Dữ liệu chuyên nghiệp v1
mysql -u root -p taskflow2 < database/seed-professional.sql
```

**Qua giao diện web:**
Truy cập: `http://localhost:81/php/database/seed-web.php`

> 📖 Xem chi tiết dữ liệu mẫu: [database/SEED_DATA_DOCUMENTATION.md](database/SEED_DATA_DOCUMENTATION.md)

### Bước 5: Chạy ứng dụng

**Với XAMPP/WAMP:**
Truy cập: `http://localhost:81/php`

**Với PHP built-in server:**
```bash
php -S localhost:8000
```
Truy cập: `http://localhost:8000`

## 👥 Tài khoản Demo

### Dữ liệu mẫu chuyên nghiệp v2 (Khuyến nghị)

Mật khẩu cho tất cả tài khoản: `password`

| Email | Vai trò | Chức vụ | Mô tả |
|-------|---------|---------|-------|
| ceo@saigontech.vn | Admin | CEO - Giám đốc điều hành | Toàn quyền hệ thống |
| cto@saigontech.vn | Admin | CTO - Giám đốc công nghệ | Toàn quyền hệ thống |
| pm.hung@saigontech.vn | Manager | Senior Project Manager | Quản lý dự án VinMart, HRMS |
| pm.linh@saigontech.vn | Manager | Project Manager | Quản lý dự án FPT Bank, MedCare |
| tech.lead@saigontech.vn | Manager | Technical Lead | Quản lý kỹ thuật |
| scrum@saigontech.vn | Manager | Scrum Master | Điều phối Agile |
| backend.tuan@saigontech.vn | Member | Senior Backend Developer | Lập trình viên chính |
| frontend.hoa@saigontech.vn | Member | Senior Frontend Developer | Lập trình viên chính |
| mobile.an@saigontech.vn | Member | Mobile Developer (iOS) | Lập trình mobile |
| qa.hanh@saigontech.vn | Member | Senior QA Engineer | Kiểm thử |
| devops@saigontech.vn | Member | DevOps Engineer | Vận hành hệ thống |
| client.vingroup@gmail.com | Guest | Product Owner | Khách hàng VinGroup |
| client.fpt@gmail.com | Guest | Technical Manager | Khách hàng FPT |

> 💡 **Gợi ý:** Đăng nhập với `ceo@saigontech.vn` để trải nghiệm đầy đủ tính năng Admin.

### Dữ liệu mẫu cơ bản (seed.sql)

Mật khẩu: `password123`

| Email | Vai trò |
|-------|---------|
| ceo@techviet.vn | Admin |
| pm.linh@techviet.vn | Manager |
| dev.khanh@techviet.vn | Member |

## 🔐 Hệ thống phân quyền

| Vai trò | Quyền hạn |
| **Admin** | Toàn quyền: quản lý users, cài đặt hệ thống, xem logs, backup |
| **Manager** | Tạo/quản lý dự án, giao việc, xem báo cáo team, quản lý thành viên |
| **Member** | Xem dự án tham gia, tạo/cập nhật task được giao, upload tài liệu |
| **Guest** | Chỉ xem, không thể tạo/sửa/xóa |

Chi tiết: [docs/PHAN_QUYEN.md](docs/PHAN_QUYEN.md)


## 📖 Hướng dẫn sử dụng

### Dashboard
Sau khi đăng nhập, người dùng được chuyển đến Dashboard hiển thị:
- Thống kê nhanh: số dự án, công việc, tỷ lệ hoàn thành
- Danh sách dự án đang tham gia
- Công việc được giao
- Hoạt động gần đây

Manager có thêm: khối lượng công việc team, thành viên xuất sắc, task quá hạn theo người.

### Quản lý Dự án
- Vào menu **Dự án** để xem danh sách
- Click **Tạo dự án** để tạo mới
- Click vào dự án để xem chi tiết với Kanban board
- Kéo thả task giữa các cột để thay đổi trạng thái

### Quản lý Công việc
- Vào menu **Công việc** để xem tất cả task
- Lọc theo trạng thái hoặc độ ưu tiên
- Click vào task để xem chi tiết, thêm checklist, comment

### Lịch
- Vào menu **Lịch** để xem calendar
- Chuyển đổi giữa Calendar view và Gantt chart
- Click vào ngày để tạo sự kiện mới

### Tài liệu
- Vào menu **Tài liệu** để quản lý file
- Tạo thư mục để tổ chức
- Upload file bằng drag & drop hoặc click chọn

### Báo cáo
- Vào menu **Báo cáo** để xem thống kê
- Chọn khoảng thời gian: Tuần/Tháng/Quý/Năm
- Xuất báo cáo CSV nếu cần

### Admin Panel (chỉ Admin)
- Click vào avatar > **Admin Panel**
- Quản lý người dùng, xem logs, backup dữ liệu

## 🔌 API Endpoints

Hệ thống cung cấp các API endpoint cho các thao tác AJAX:

```
POST /api/create-project.php     # Tạo dự án
POST /api/update-project.php     # Cập nhật dự án
POST /api/create-task.php        # Tạo task
POST /api/update-task.php        # Cập nhật task
POST /api/checklist.php          # Quản lý checklist
POST /api/comments.php           # Quản lý comments
POST /api/upload-document.php    # Upload file
POST /api/calendar.php           # Quản lý sự kiện
GET  /api/search.php             # Tìm kiếm
GET  /api/notifications.php      # Lấy thông báo
GET  /api/sse-notifications.php  # Real-time notifications (SSE)
POST /api/project-members.php    # Quản lý thành viên dự án
GET  /api/admin-export.php       # Export báo cáo (CSV/JSON/PDF)
POST /api/admin-maintenance.php  # Backup/Restore
```

## 📚 Tài liệu

- [Hướng dẫn cài đặt chi tiết](docs/HUONG_DAN_CAI_DAT.md)
- [Hướng dẫn triển khai Production](docs/DEPLOYMENT.md)
- [API Documentation](docs/API_DOCUMENTATION.md)
- [Cấu trúc hệ thống](docs/STRUCTURE.md)
- [Kiến trúc hệ thống](docs/KIEN_TRUC_HE_THONG.md)
- [Hệ thống phân quyền](docs/PHAN_QUYEN.md)
- [Mô tả giao diện và chức năng](docs/MO_TA_GIAO_DIEN_CHUC_NANG.md)
- [Báo cáo kiểm thử](docs/BAO_CAO_KIEM_THU_HE_THONG.md)
- [Báo cáo kiểm định hệ thống](docs/BAO_CAO_KIEM_DINH_HE_THONG.md) ⭐
- [Đánh giá hệ thống](docs/DANH_GIA_DU_AN.md)
- [Tài liệu dữ liệu mẫu](database/SEED_DATA_DOCUMENTATION.md) 📊

## ⌨️ Phím tắt

| Phím | Chức năng |
|------|-----------|
| `Ctrl + K` | Mở tìm kiếm |
| `Ctrl + N` | Tạo task mới |
| `Ctrl + Shift + P` | Tạo project mới |
| `Escape` | Đóng modal/dialog |
| `?` | Hiển thị trợ giúp phím tắt |

## 🤝 Đóng góp

Xem [CONTRIBUTING.md](CONTRIBUTING.md) để biết cách đóng góp vào dự án.

## 📝 Changelog

Xem [CHANGELOG.md](CHANGELOG.md) để biết lịch sử thay đổi.

## 📄 License

MIT License - Xem file [LICENSE](LICENSE) để biết chi tiết.

---

<p align="center">
  <b>TaskFlow</b> - Quản lý dự án hiệu quả, cộng tác nhóm dễ dàng.
  <br><br>
  <a href="docs/BAO_CAO_KIEM_DINH_HE_THONG.md">📊 Mức độ hoàn thiện: 91%</a>
</p>
