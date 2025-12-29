# Hệ Thống Phân Quyền - TaskFlow

> **Cập nhật:** Hệ thống đã được tái cấu trúc với cấu trúc MVC mới. Xem `docs/STRUCTURE.md` để biết chi tiết.

## Tổng Quan

TaskFlow sử dụng hệ thống phân quyền 2 cấp:
1. **Phân quyền cấp hệ thống (System Roles)** - Quyền toàn cục của user trong hệ thống
2. **Phân quyền cấp dự án (Project Roles)** - Quyền của user trong từng dự án cụ thể

---

## 1. Phân Quyền Cấp Hệ Thống

### Các Role Hệ Thống

| Role | Mô tả |
|------|-------|
| `admin` | Quản trị viên - Toàn quyền trong hệ thống |
| `manager` | Quản lý - Có thể quản lý dự án và nhóm |
| `member` | Thành viên - Người dùng thông thường |
| `guest` | Khách - Chỉ xem, không thể chỉnh sửa |

### Ma Trận Quyền Hệ Thống

| Quyền | Admin | Manager | Member | Guest |
|-------|:-----:|:-------:|:------:|:-----:|
| **Users** |
| users.view | ✅ | ✅ | ✅ | ❌ |
| users.create | ✅ | ❌ | ❌ | ❌ |
| users.edit | ✅ | ❌ | ❌ | ❌ |
| users.delete | ✅ | ❌ | ❌ | ❌ |
| **Projects** |
| projects.view | ✅ | ✅ | ✅ | ✅ |
| projects.create | ✅ | ✅ | ❌ | ❌ |
| projects.edit | ✅ | ✅ | ❌ | ❌ |
| projects.delete | ✅ | ❌ | ❌ | ❌ |
| **Tasks** |
| tasks.view | ✅ | ✅ | ✅ | ✅ |
| tasks.create | ✅ | ✅ | ✅ | ❌ |
| tasks.edit | ✅ | ✅ | ✅ | ❌ |
| tasks.delete | ✅ | ✅ | ❌ | ❌ |
| **Documents** |
| documents.view | ✅ | ✅ | ✅ | ✅ |
| documents.create | ✅ | ✅ | ✅ | ❌ |
| documents.edit | ✅ | ✅ | ✅ | ❌ |
| documents.delete | ✅ | ✅ | ❌ | ❌ |
| **Settings** |
| settings.view | ✅ | ❌ | ❌ | ❌ |
| settings.edit | ✅ | ❌ | ❌ | ❌ |
| **Team** |
| team.manage | ✅ | ✅ | ❌ | ❌ |

---

## 2. Phân Quyền Cấp Dự Án

### Các Role Trong Dự Án

| Role | Mô tả |
|------|-------|
| `owner` | Chủ dự án - Người tạo dự án, toàn quyền |
| `manager` | Quản lý dự án - Quản lý task và thành viên |
| `member` | Thành viên - Làm việc trên task được giao |
| `viewer` | Người xem - Chỉ xem, không chỉnh sửa |

### Ma Trận Quyền Dự Án

| Hành động | Owner | Manager | Member | Viewer |
|-----------|:-----:|:-------:|:------:|:------:|
| Xem dự án | ✅ | ✅ | ✅ | ✅ |
| Chỉnh sửa thông tin dự án | ✅ | ✅ | ❌ | ❌ |
| Xóa dự án | ✅ | ❌ | ❌ | ❌ |
| Thêm thành viên | ✅ | ✅ | ❌ | ❌ |
| Xóa thành viên | ✅ | ✅ | ❌ | ❌ |
| Tạo task | ✅ | ✅ | ✅ | ❌ |
| Chỉnh sửa task | ✅ | ✅ | ✅* | ❌ |
| Xóa task | ✅ | ✅ | ❌ | ❌ |
| Upload tài liệu | ✅ | ✅ | ✅ | ❌ |
| Xóa tài liệu | ✅ | ✅ | ✅** | ❌ |
| Bình luận | ✅ | ✅ | ✅ | ❌ |

> *Member chỉ có thể chỉnh sửa task được giao cho mình
> **Member chỉ có thể xóa tài liệu do mình upload

---

## 3. Phân Quyền Tài Liệu

### Quyền Chia Sẻ Tài Liệu

| Quyền | Mô tả |
|-------|-------|
| `view` | Chỉ xem và tải xuống |
| `edit` | Xem, tải xuống, chỉnh sửa, xóa |

### Quy Tắc Truy Cập Tài Liệu

1. **Người upload** - Toàn quyền với tài liệu của mình
2. **Được chia sẻ** - Theo quyền được cấp (view/edit)
3. **Thành viên dự án** - Xem tài liệu trong dự án mình tham gia
4. **Admin** - Toàn quyền với mọi tài liệu

---

## 4. Cách Sử Dụng Trong Code

### Kiểm Tra Đăng Nhập

```php
// Yêu cầu đăng nhập
auth()->requireLogin('login.php');

// Kiểm tra đã đăng nhập chưa
if (auth()->check()) {
    $user = auth()->user();
}
```

### Kiểm Tra Quyền

```php
// Kiểm tra một quyền
if (auth()->can('projects.create')) {
    // Cho phép tạo dự án
}

// Kiểm tra nhiều quyền (cần tất cả)
if (auth()->canAll(['tasks.create', 'tasks.edit'])) {
    // Cho phép tạo và sửa task
}

// Kiểm tra nhiều quyền (cần ít nhất 1)
if (auth()->canAny(['projects.edit', 'projects.delete'])) {
    // Cho phép sửa hoặc xóa dự án
}
```

### Kiểm Tra Role

```php
// Kiểm tra admin
if (auth()->isAdmin()) {
    // Là admin
}

// Kiểm tra manager trở lên
if (auth()->isManager()) {
    // Là admin hoặc manager
}

// Yêu cầu role cụ thể
auth()->requireRole(['admin', 'manager'], '403.php');
```

### Yêu Cầu Quyền

```php
// Yêu cầu quyền, redirect nếu không có
auth()->requirePermission('users.delete', '403.php');
```

---

## 5. Cấu Trúc Database

### Bảng `users`

```sql
`role` ENUM('admin', 'manager', 'member', 'guest') NOT NULL DEFAULT 'member'
```

### Bảng `project_members`

```sql
`role` ENUM('owner', 'manager', 'member', 'viewer') NOT NULL DEFAULT 'member'
```

### Bảng `document_shares`

```sql
`permission` ENUM('view', 'edit') NOT NULL DEFAULT 'view'
```

---

## 6. Tài Khoản Demo

| Email | Password | Role |
|-------|----------|------|
| admin@taskflow.com | password123 | Admin |
| manager@taskflow.com | password123 | Manager |
| designer@taskflow.com | password123 | Member |
| frontend@taskflow.com | password123 | Member |
| backend@taskflow.com | password123 | Member |

---

## 7. Lưu Ý Bảo Mật

1. **Luôn kiểm tra quyền** trước khi thực hiện hành động nhạy cảm
2. **Không tin tưởng client** - Kiểm tra quyền ở server-side
3. **Log hoạt động** - Ghi lại các hành động quan trọng vào `activity_logs`
4. **Session timeout** - Tự động đăng xuất sau thời gian không hoạt động
5. **Remember token** - Mã hóa và có thời hạn (30 ngày)

---

## 8. Mở Rộng Phân Quyền

Để thêm quyền mới, chỉnh sửa file `includes/classes/Auth.php`:

```php
private array $permissions = [
    'admin' => [
        // Thêm quyền mới ở đây
        'new_feature.view',
        'new_feature.edit',
        // ...
    ],
    // ...
];
```
