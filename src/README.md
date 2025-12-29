# TaskFlow - Hệ thống Quản lý Dự án và Công tác Nhóm

TaskFlow là ứng dụng web quản lý dự án được xây dựng bằng PHP thuần với kiến trúc MVC, hỗ trợ quản lý dự án, công việc, tài liệu và cộng tác nhóm.

## Tính năng chính

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
- Xuất báo cáo CSV

### Hệ thống phân quyền
- 4 vai trò: Admin, Manager, Member, Guest
- Phân quyền chi tiết theo từng chức năng
- Admin Panel riêng cho quản trị viên

## Công nghệ sử dụng

- **Backend:** PHP 8.x với kiến trúc MVC
- **Database:** MySQL/MariaDB
- **Frontend:** Tailwind CSS, Alpine.js
- **Icons:** Lucide Icons
- **Charts:** Chart.js


## Cấu trúc thư mục

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
├── config/                   # Cấu hình hệ thống
│   ├── app.php
│   └── permissions.php
├── core/                     # Core classes
│   ├── Database.php
│   ├── Session.php
│   ├── View.php
│   ├── Permission.php
│   ├── Logger.php
│   ├── Validator.php
│   └── RateLimiter.php
├── database/                 # Database files
│   ├── taskflow2.sql         # Schema chính
│   ├── seed.sql              # Dữ liệu mẫu
│   └── migrations/           # Migration scripts
├── docs/                     # Tài liệu
├── includes/                 # Legacy includes
├── logs/                     # Log files
├── uploads/                  # Uploaded files
├── bootstrap.php             # Application bootstrap
└── index.php                 # Entry point
```

## Cài đặt

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

Hoặc chỉnh sửa trực tiếp `includes/config.php`:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'taskflow2');
define('DB_USER', 'root');
define('DB_PASS', '');
```

### Bước 4: Tạo dữ liệu mẫu (tùy chọn)
Có 2 cách:

**Cách 1:** Import file SQL
```bash
mysql -u root -p taskflow2 < database/seed.sql
```

**Cách 2:** Sử dụng giao diện web
Truy cập: `http://localhost/php/database/seed-web.php`

### Bước 5: Chạy ứng dụng

**Với XAMPP/WAMP:**
Truy cập: `http://localhost/php`

**Với PHP built-in server:**
```bash
php -S localhost:8000
```
Truy cập: `http://localhost:8000`

## Tài khoản Demo

Mật khẩu cho tất cả tài khoản: `password123`

| Email | Vai trò | Mô tả |
|-------|---------|-------|
| admin@taskflow.com | Admin | Toàn quyền hệ thống |
| manager@taskflow.com | Manager | Quản lý dự án và team |
| designer@taskflow.com | Member | Thành viên |
| frontend@taskflow.com | Member | Thành viên |
| backend@taskflow.com | Member | Thành viên |

## Hệ thống phân quyền

| Vai trò | Quyền hạn |
|---------|-----------|
| **Admin** | Toàn quyền: quản lý users, cài đặt hệ thống, xem logs, backup |
| **Manager** | Tạo/quản lý dự án, giao việc, xem báo cáo team, quản lý thành viên |
| **Member** | Xem dự án tham gia, tạo/cập nhật task được giao, upload tài liệu |
| **Guest** | Chỉ xem, không thể tạo/sửa/xóa |

Chi tiết: [docs/PHAN_QUYEN.md](docs/PHAN_QUYEN.md)


## Hướng dẫn sử dụng

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

## API Endpoints

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
POST /api/project-members.php    # Quản lý thành viên dự án
```

## Tài liệu

- [Hướng dẫn cài đặt chi tiết](docs/HUONG_DAN_CAI_DAT.md)
- [Cấu trúc hệ thống](docs/STRUCTURE.md)
- [Kiến trúc hệ thống](docs/KIEN_TRUC_HE_THONG.md)
- [Hệ thống phân quyền](docs/PHAN_QUYEN.md)
- [Mô tả giao diện và chức năng](docs/MO_TA_GIAO_DIEN_CHUC_NANG.md)
- [Đánh giá hệ thống](docs/DANH_GIA_DU_AN.md)

## Đóng góp

Xem [CONTRIBUTING.md](CONTRIBUTING.md) để biết cách đóng góp vào dự án.

## Changelog

Xem [CHANGELOG.md](CHANGELOG.md) để biết lịch sử thay đổi.

## License

MIT License - Xem file [LICENSE](LICENSE) để biết chi tiết.

---

**TaskFlow** - Quản lý dự án hiệu quả, cộng tác nhóm dễ dàng.
