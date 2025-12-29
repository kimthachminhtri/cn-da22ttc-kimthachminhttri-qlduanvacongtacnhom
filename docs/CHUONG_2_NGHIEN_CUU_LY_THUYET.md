# CHƯƠNG 2: NGHIÊN CỨU LÝ THUYẾT

## 2.1. Tổng Quan Về Hệ Thống Quản Lý Dự Án

### 2.1.1. Khái niệm hệ thống quản lý dự án

Hệ thống quản lý dự án (Project Management System - PMS) là một phần mềm ứng dụng được thiết kế để hỗ trợ việc lập kế hoạch, tổ chức, theo dõi và kiểm soát các dự án. Hệ thống này giúp các nhóm làm việc cộng tác hiệu quả, quản lý tài nguyên, theo dõi tiến độ và đảm bảo dự án hoàn thành đúng thời hạn.

**Các chức năng cốt lõi của PMS:**
- Quản lý công việc (Task Management)
- Quản lý tài liệu (Document Management)
- Quản lý nhóm (Team Management)
- Theo dõi tiến độ (Progress Tracking)
- Báo cáo và thống kê (Reporting & Analytics)
- Lịch và nhắc nhở (Calendar & Reminders)

### 2.1.2. Phân loại hệ thống quản lý dự án

| Loại hệ thống | Đặc điểm | Ví dụ |
|---------------|----------|-------|
| **Desktop-based** | Cài đặt trên máy tính, không cần internet | Microsoft Project |
| **Web-based** | Truy cập qua trình duyệt, lưu trữ đám mây | Trello, Asana, Jira |
| **Hybrid** | Kết hợp desktop và web | Basecamp |
| **Self-hosted** | Triển khai trên server riêng | TaskFlow (đồ án này) |

---

## 2.2. Kiến Trúc Phần Mềm

### 2.2.1. Mô hình MVC (Model-View-Controller)

MVC là một mẫu kiến trúc phần mềm phổ biến, chia ứng dụng thành ba thành phần chính:

```
┌─────────────────────────────────────────────────────────────┐
│                        USER REQUEST                          │
└─────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────┐
│                       CONTROLLER                             │
│  • Nhận request từ user                                      │
│  • Xử lý logic điều hướng                                    │
│  • Gọi Model để lấy/cập nhật dữ liệu                        │
│  • Chọn View phù hợp để hiển thị                            │
└─────────────────────────────────────────────────────────────┘
                    │                   │
                    ▼                   ▼
┌──────────────────────────┐  ┌──────────────────────────────┐
│         MODEL            │  │           VIEW               │
│  • Quản lý dữ liệu       │  │  • Hiển thị giao diện        │
│  • Business logic        │  │  • Nhận dữ liệu từ Controller│
│  • Tương tác Database    │  │  • Render HTML/JSON          │
└──────────────────────────┘  └──────────────────────────────┘
            │                              │
            ▼                              ▼
┌──────────────────────────┐  ┌──────────────────────────────┐
│       DATABASE           │  │      USER INTERFACE          │
└──────────────────────────┘  └──────────────────────────────┘
```

**Hình 2.1: Sơ đồ kiến trúc MVC**


**Ưu điểm của MVC:**
- Tách biệt rõ ràng giữa logic và giao diện
- Dễ bảo trì và mở rộng
- Hỗ trợ phát triển song song (nhiều developer)
- Tái sử dụng code hiệu quả
- Dễ dàng unit testing

**Nhược điểm:**
- Phức tạp hơn cho ứng dụng nhỏ
- Cần hiểu rõ cấu trúc để triển khai đúng
- Có thể tạo nhiều file hơn cần thiết

### 2.2.2. Ứng dụng MVC trong TaskFlow

Dự án TaskFlow áp dụng mô hình MVC với cấu trúc thư mục rõ ràng:

```
taskflow/
├── app/
│   ├── controllers/          # Controllers xử lý request
│   │   ├── BaseController.php
│   │   ├── AuthController.php
│   │   ├── ProjectController.php
│   │   ├── TaskController.php
│   │   └── ...
│   │
│   ├── models/               # Models tương tác database
│   │   ├── BaseModel.php
│   │   ├── User.php
│   │   ├── Project.php
│   │   ├── Task.php
│   │   └── ...
│   │
│   ├── views/                # Views hiển thị giao diện
│   │   ├── layouts/
│   │   ├── dashboard/
│   │   ├── projects/
│   │   └── ...
│   │
│   └── middleware/           # Middleware xử lý trung gian
│       ├── AuthMiddleware.php
│       └── PermissionMiddleware.php
│
├── core/                     # Core framework
│   ├── Database.php
│   ├── Session.php
│   ├── View.php
│   └── Permission.php
│
└── config/                   # Cấu hình hệ thống
    ├── app.php
    ├── database.php
    └── permissions.php
```

**Hình 2.2: Cấu trúc thư mục MVC của TaskFlow**

---

## 2.3. Công Nghệ Sử Dụng

### 2.3.1. So sánh các ngôn ngữ Backend

| Tiêu chí | PHP | Node.js | Python | Java |
|----------|-----|---------|--------|------|
| **Hiệu năng** | Trung bình | Cao | Trung bình | Cao |
| **Học tập** | Dễ | Trung bình | Dễ | Khó |
| **Hosting** | Rất phổ biến | Phổ biến | Phổ biến | Đắt |
| **Cộng đồng** | Rất lớn | Lớn | Lớn | Lớn |
| **Framework** | Laravel, Symfony | Express, NestJS | Django, Flask | Spring |
| **Chi phí** | Thấp | Trung bình | Trung bình | Cao |

**Lý do chọn PHP cho TaskFlow:**
1. **Phổ biến và dễ triển khai**: PHP chạy trên hầu hết các hosting
2. **Chi phí thấp**: Không cần server đắt tiền
3. **Cộng đồng lớn**: Nhiều tài liệu và hỗ trợ
4. **Tích hợp tốt với MySQL**: Kết nối database đơn giản
5. **Phù hợp với web application**: Được thiết kế cho web

### 2.3.2. So sánh các hệ quản trị CSDL

| Tiêu chí | MySQL | PostgreSQL | MongoDB | SQLite |
|----------|-------|------------|---------|--------|
| **Loại** | Relational | Relational | NoSQL | Relational |
| **Hiệu năng đọc** | Cao | Cao | Rất cao | Trung bình |
| **Hiệu năng ghi** | Cao | Cao | Cao | Thấp |
| **Tính năng** | Đầy đủ | Rất đầy đủ | Linh hoạt | Cơ bản |
| **Phức tạp** | Trung bình | Cao | Thấp | Thấp |
| **Phù hợp** | Web app | Enterprise | Big data | Mobile/Desktop |

**Lý do chọn MySQL:**
1. **Phổ biến nhất**: Được sử dụng rộng rãi trong web development
2. **Tích hợp tốt với PHP**: Hỗ trợ native trong PHP
3. **Hiệu năng tốt**: Đáp ứng được yêu cầu của ứng dụng
4. **Dễ quản lý**: Có phpMyAdmin và nhiều công cụ GUI
5. **Miễn phí**: Open source, không tốn chi phí license


### 2.3.3. So sánh các CSS Framework

| Tiêu chí | Tailwind CSS | Bootstrap | Bulma | Foundation |
|----------|--------------|-----------|-------|------------|
| **Approach** | Utility-first | Component-based | Component-based | Component-based |
| **Customization** | Rất cao | Trung bình | Cao | Cao |
| **File size** | Nhỏ (purge) | Lớn | Trung bình | Lớn |
| **Learning curve** | Trung bình | Thấp | Thấp | Trung bình |
| **Flexibility** | Rất cao | Trung bình | Trung bình | Cao |

**Lý do chọn Tailwind CSS:**
1. **Utility-first**: Viết CSS trực tiếp trong HTML, không cần file CSS riêng
2. **Highly customizable**: Dễ dàng tùy chỉnh theo design
3. **Responsive**: Hỗ trợ responsive design tốt
4. **Dark mode**: Hỗ trợ dark mode native
5. **Performance**: File size nhỏ sau khi purge unused CSS

### 2.3.4. JavaScript Framework - Alpine.js

Alpine.js là một framework JavaScript nhẹ, phù hợp cho các ứng dụng cần tương tác đơn giản.

**So sánh với các framework khác:**

| Tiêu chí | Alpine.js | Vue.js | React | jQuery |
|----------|-----------|--------|-------|--------|
| **Size** | 15KB | 33KB | 42KB | 87KB |
| **Learning curve** | Rất thấp | Trung bình | Cao | Thấp |
| **Reactivity** | Có | Có | Có | Không |
| **Virtual DOM** | Không | Có | Có | Không |
| **Use case** | Simple interactivity | SPA | SPA | DOM manipulation |

**Lý do chọn Alpine.js:**
1. **Nhẹ**: Chỉ 15KB, load nhanh
2. **Dễ học**: Cú pháp đơn giản, giống Vue.js
3. **Không cần build**: Sử dụng trực tiếp qua CDN
4. **Phù hợp với PHP**: Kết hợp tốt với server-side rendering

---

## 2.4. Thiết Kế Cơ Sở Dữ Liệu

### 2.4.1. Mô hình quan hệ (Relational Model)

Mô hình quan hệ là mô hình dữ liệu phổ biến nhất, tổ chức dữ liệu thành các bảng (table) với các hàng (row) và cột (column).

**Các khái niệm cơ bản:**
- **Entity (Thực thể)**: Đối tượng cần lưu trữ (User, Project, Task)
- **Attribute (Thuộc tính)**: Đặc điểm của thực thể (name, email, status)
- **Primary Key (Khóa chính)**: Định danh duy nhất cho mỗi bản ghi
- **Foreign Key (Khóa ngoại)**: Liên kết giữa các bảng
- **Relationship (Quan hệ)**: Mối liên hệ giữa các thực thể

### 2.4.2. Sơ đồ ERD (Entity Relationship Diagram)

```
┌─────────────────┐       ┌─────────────────┐       ┌─────────────────┐
│     USERS       │       │    PROJECTS     │       │     TASKS       │
├─────────────────┤       ├─────────────────┤       ├─────────────────┤
│ id (PK)         │       │ id (PK)         │       │ id (PK)         │
│ email           │       │ name            │       │ title           │
│ password_hash   │       │ description     │       │ description     │
│ full_name       │       │ status          │       │ status          │
│ role            │       │ priority        │       │ priority        │
│ avatar_url      │       │ progress        │       │ due_date        │
│ is_active       │       │ start_date      │       │ project_id (FK) │
│ created_at      │       │ end_date        │       │ created_by (FK) │
└────────┬────────┘       │ created_by (FK) │       └────────┬────────┘
         │                └────────┬────────┘                │
         │                         │                         │
         │    ┌────────────────────┼────────────────────┐    │
         │    │                    │                    │    │
         ▼    ▼                    ▼                    ▼    ▼
┌─────────────────────┐   ┌─────────────────────┐   ┌─────────────────────┐
│  PROJECT_MEMBERS    │   │     DOCUMENTS       │   │   TASK_ASSIGNEES    │
├─────────────────────┤   ├─────────────────────┤   ├─────────────────────┤
│ project_id (FK)     │   │ id (PK)             │   │ task_id (FK)        │
│ user_id (FK)        │   │ name                │   │ user_id (FK)        │
│ role                │   │ type                │   │ assigned_by (FK)    │
│ joined_at           │   │ file_path           │   │ assigned_at         │
└─────────────────────┘   │ project_id (FK)     │   └─────────────────────┘
                          │ uploaded_by (FK)    │
                          └─────────────────────┘
```

**Hình 2.3: Sơ đồ ERD của TaskFlow (đơn giản hóa)**


### 2.4.3. Các bảng chính trong TaskFlow

| Bảng | Mô tả | Quan hệ |
|------|-------|---------|
| `users` | Thông tin người dùng | 1-N với projects, tasks |
| `projects` | Thông tin dự án | 1-N với tasks, documents |
| `project_members` | Thành viên dự án | N-N giữa users và projects |
| `tasks` | Công việc | N-1 với projects, N-N với users |
| `task_assignees` | Người được giao task | N-N giữa tasks và users |
| `task_checklists` | Checklist của task | N-1 với tasks |
| `documents` | Tài liệu | N-1 với projects |
| `comments` | Bình luận | N-1 với tasks/documents |
| `notifications` | Thông báo | N-1 với users |
| `calendar_events` | Sự kiện lịch | N-1 với users, projects |
| `activity_logs` | Lịch sử hoạt động | N-1 với users |

### 2.4.4. Chuẩn hóa dữ liệu (Normalization)

TaskFlow áp dụng chuẩn hóa đến dạng chuẩn 3 (3NF):

**Dạng chuẩn 1 (1NF):**
- Mỗi cột chứa giá trị nguyên tử (atomic)
- Không có nhóm lặp

**Dạng chuẩn 2 (2NF):**
- Đạt 1NF
- Mọi thuộc tính không khóa phụ thuộc hoàn toàn vào khóa chính

**Dạng chuẩn 3 (3NF):**
- Đạt 2NF
- Không có phụ thuộc bắc cầu

**Ví dụ áp dụng trong TaskFlow:**
- Tách `project_members` thay vì lưu danh sách members trong `projects`
- Tách `task_assignees` thay vì lưu assignees trong `tasks`
- Tách `task_labels` để quản lý nhãn riêng biệt

---

## 2.5. Hệ Thống Phân Quyền

### 2.5.1. Mô hình RBAC (Role-Based Access Control)

RBAC là mô hình kiểm soát truy cập dựa trên vai trò, trong đó quyền truy cập được gán cho các vai trò thay vì trực tiếp cho người dùng.

```
┌─────────────────────────────────────────────────────────────┐
│                         USERS                                │
│  ┌─────────┐  ┌─────────┐  ┌─────────┐  ┌─────────┐        │
│  │  Admin  │  │ Manager │  │ Member  │  │  Guest  │        │
│  └────┬────┘  └────┬────┘  └────┬────┘  └────┬────┘        │
└───────┼────────────┼────────────┼────────────┼──────────────┘
        │            │            │            │
        ▼            ▼            ▼            ▼
┌─────────────────────────────────────────────────────────────┐
│                         ROLES                                │
│  ┌─────────────────────────────────────────────────────┐    │
│  │ Admin: Toàn quyền hệ thống                          │    │
│  │ Manager: Quản lý dự án, nhóm                        │    │
│  │ Member: Tạo/sửa task, document của mình             │    │
│  │ Guest: Chỉ xem                                      │    │
│  └─────────────────────────────────────────────────────┘    │
└─────────────────────────────────────────────────────────────┘
        │            │            │            │
        ▼            ▼            ▼            ▼
┌─────────────────────────────────────────────────────────────┐
│                      PERMISSIONS                             │
│  ┌──────────────┐ ┌──────────────┐ ┌──────────────┐        │
│  │ users.*      │ │ projects.*   │ │ tasks.*      │        │
│  │ projects.*   │ │ tasks.*      │ │ documents.*  │        │
│  │ tasks.*      │ │ documents.*  │ │              │        │
│  │ documents.*  │ │ team.manage  │ │              │        │
│  │ admin.access │ │ reports.*    │ │              │        │
│  └──────────────┘ └──────────────┘ └──────────────┘        │
│      ADMIN           MANAGER          MEMBER                │
└─────────────────────────────────────────────────────────────┘
```

**Hình 2.4: Mô hình RBAC trong TaskFlow**

### 2.5.2. Ma trận phân quyền TaskFlow

| Quyền | Admin | Manager | Member | Guest |
|-------|:-----:|:-------:|:------:|:-----:|
| **Users** |
| users.view | ✅ | ✅ | ✅ | ❌ |
| users.create | ✅ | ❌ | ❌ | ❌ |
| users.edit | ✅ | ❌ | ❌ | ❌ |
| users.delete | ✅ | ❌ | ❌ | ❌ |
| **Projects** |
| projects.view | ✅ | ✅ | ✅ | ✅ |
| projects.create | ✅ | ✅ | ✅ | ❌ |
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
| **Admin** |
| admin.access | ✅ | ❌ | ❌ | ❌ |
| reports.view | ✅ | ✅ | ❌ | ❌ |
| reports.export | ✅ | ✅ | ❌ | ❌ |

**Bảng 2.1: Ma trận phân quyền chi tiết**


### 2.5.3. Phân quyền cấp dự án

Ngoài phân quyền hệ thống, TaskFlow còn hỗ trợ phân quyền cấp dự án:

| Vai trò dự án | Quyền hạn |
|---------------|-----------|
| **Owner** | Toàn quyền trong dự án, có thể xóa dự án |
| **Manager** | Quản lý tasks, documents, members |
| **Member** | Tạo/sửa tasks và documents của mình |
| **Viewer** | Chỉ xem, không thể chỉnh sửa |

---

## 2.6. Bảo Mật Ứng Dụng Web

### 2.6.1. Các mối đe dọa phổ biến (OWASP Top 10)

| Mối đe dọa | Mô tả | Giải pháp trong TaskFlow |
|------------|-------|--------------------------|
| **SQL Injection** | Chèn mã SQL độc hại | Prepared Statements (PDO) |
| **XSS** | Chèn script độc hại | Output escaping, htmlspecialchars() |
| **CSRF** | Giả mạo request | CSRF tokens |
| **Broken Authentication** | Xác thực yếu | Password hashing (bcrypt), session management |
| **Sensitive Data Exposure** | Lộ dữ liệu nhạy cảm | HTTPS, password hashing |
| **Broken Access Control** | Kiểm soát truy cập yếu | RBAC, middleware |

### 2.6.2. Kỹ thuật bảo mật áp dụng

**1. Password Hashing với Bcrypt:**
```php
// Mã hóa password
$hash = password_hash($password, PASSWORD_BCRYPT);

// Xác thực password
if (password_verify($password, $hash)) {
    // Đăng nhập thành công
}
```

**2. Prepared Statements (PDO):**
```php
// KHÔNG AN TOÀN - SQL Injection
$sql = "SELECT * FROM users WHERE email = '$email'";

// AN TOÀN - Prepared Statement
$sql = "SELECT * FROM users WHERE email = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$email]);
```

**3. Output Escaping:**
```php
// Escape HTML để tránh XSS
echo htmlspecialchars($userInput, ENT_QUOTES, 'UTF-8');
```

**4. Session Security:**
```php
session_set_cookie_params([
    'lifetime' => 7200,      // 2 giờ
    'path' => '/',
    'secure' => true,        // Chỉ HTTPS
    'httponly' => true,      // Không truy cập từ JavaScript
    'samesite' => 'Lax',     // Chống CSRF
]);
```

### 2.6.3. Sơ đồ luồng xác thực

```
┌─────────────┐     ┌─────────────┐     ┌─────────────┐
│   CLIENT    │     │   SERVER    │     │  DATABASE   │
└──────┬──────┘     └──────┬──────┘     └──────┬──────┘
       │                   │                   │
       │  1. Login Request │                   │
       │  (email, password)│                   │
       │──────────────────>│                   │
       │                   │                   │
       │                   │  2. Query User    │
       │                   │──────────────────>│
       │                   │                   │
       │                   │  3. User Data     │
       │                   │<──────────────────│
       │                   │                   │
       │                   │  4. Verify Password
       │                   │  (bcrypt compare) │
       │                   │                   │
       │  5. Set Session   │                   │
       │  + CSRF Token     │                   │
       │<──────────────────│                   │
       │                   │                   │
       │  6. Redirect to   │                   │
       │     Dashboard     │                   │
       │<──────────────────│                   │
       │                   │                   │
```

**Hình 2.5: Luồng xác thực người dùng**

---

## 2.7. Design Patterns Sử Dụng

### 2.7.1. Singleton Pattern

Singleton đảm bảo một class chỉ có một instance duy nhất trong toàn bộ ứng dụng.

**Áp dụng trong TaskFlow:** Database connection

```php
class Database {
    private static ?Database $instance = null;
    private ?PDO $connection = null;

    private function __construct() {
        // Private constructor
    }

    public static function getInstance(): Database {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
}

// Sử dụng
$db = Database::getInstance();
```

**Ưu điểm:**
- Tiết kiệm tài nguyên (chỉ 1 connection)
- Đảm bảo tính nhất quán
- Dễ quản lý trạng thái

### 2.7.2. Repository Pattern

Repository tách biệt logic truy cập dữ liệu khỏi business logic.

**Áp dụng trong TaskFlow:** BaseModel và các Model con

```php
abstract class BaseModel {
    protected Database $db;
    protected string $table;

    public function find($id): ?array {
        $sql = "SELECT * FROM {$this->table} WHERE id = ?";
        return $this->db->fetchOne($sql, [$id]);
    }

    public function create(array $data): string|false {
        return $this->db->insert($this->table, $data);
    }
}

class Task extends BaseModel {
    protected string $table = 'tasks';
    
    public function getByProject($projectId): array {
        // Custom query
    }
}
```


### 2.7.3. Middleware Pattern

Middleware xử lý request trước khi đến controller, thường dùng cho authentication và authorization.

```
Request → Middleware 1 → Middleware 2 → Controller → Response
              │              │
              │              └── PermissionMiddleware
              └── AuthMiddleware
```

**Áp dụng trong TaskFlow:**

```php
class AuthMiddleware {
    public static function handle(): bool {
        if (!Session::has('user_id')) {
            header('Location: /login.php');
            exit;
        }
        return true;
    }
}

// Sử dụng trong controller
AuthMiddleware::handle();
PermissionMiddleware::requirePermission('projects.create');
```

### 2.7.4. Factory Pattern

Factory tạo đối tượng mà không cần chỉ định class cụ thể.

**Áp dụng trong TaskFlow:** View rendering

```php
class View {
    public static function render(string $view, array $data = []): void {
        extract($data);
        $viewPath = BASE_PATH . '/app/views/' . $view . '.php';
        include $viewPath;
    }
}

// Sử dụng
View::render('projects/index', ['projects' => $projects]);
```

---

## 2.8. RESTful API Design

### 2.8.1. Nguyên tắc REST

REST (Representational State Transfer) là kiến trúc thiết kế API phổ biến:

| Nguyên tắc | Mô tả |
|------------|-------|
| **Stateless** | Server không lưu trạng thái client |
| **Client-Server** | Tách biệt client và server |
| **Cacheable** | Response có thể cache |
| **Uniform Interface** | Giao diện thống nhất |
| **Layered System** | Hệ thống phân lớp |

### 2.8.2. HTTP Methods và CRUD

| HTTP Method | CRUD | Mô tả | Ví dụ |
|-------------|------|-------|-------|
| **GET** | Read | Lấy dữ liệu | GET /api/tasks |
| **POST** | Create | Tạo mới | POST /api/tasks |
| **PUT** | Update | Cập nhật toàn bộ | PUT /api/tasks/1 |
| **PATCH** | Update | Cập nhật một phần | PATCH /api/tasks/1 |
| **DELETE** | Delete | Xóa | DELETE /api/tasks/1 |

### 2.8.3. API Endpoints trong TaskFlow

| Endpoint | Method | Mô tả |
|----------|--------|-------|
| `/api/projects` | GET | Danh sách dự án |
| `/api/create-project.php` | POST | Tạo dự án mới |
| `/api/update-project.php` | POST | Cập nhật dự án |
| `/api/tasks` | GET | Danh sách công việc |
| `/api/create-task.php` | POST | Tạo công việc |
| `/api/update-task.php` | POST | Cập nhật công việc |
| `/api/comments.php` | GET/POST | Quản lý bình luận |
| `/api/checklist.php` | POST | Quản lý checklist |
| `/api/upload-document.php` | POST | Upload tài liệu |
| `/api/search.php` | GET | Tìm kiếm toàn cục |

### 2.8.4. Response Format

TaskFlow sử dụng JSON format cho API response:

```json
// Success Response
{
    "success": true,
    "data": {
        "id": "uuid-123",
        "title": "Task name",
        "status": "in_progress"
    },
    "message": "Task created successfully"
}

// Error Response
{
    "success": false,
    "error": "Validation failed",
    "errors": {
        "title": "Title is required",
        "due_date": "Invalid date format"
    }
}
```

---

## 2.9. Responsive Web Design

### 2.9.1. Nguyên tắc Mobile-First

Mobile-First là phương pháp thiết kế bắt đầu từ màn hình nhỏ nhất, sau đó mở rộng cho màn hình lớn hơn.

```css
/* Mobile first (default) */
.container {
    width: 100%;
    padding: 1rem;
}

/* Tablet */
@media (min-width: 768px) {
    .container {
        max-width: 720px;
    }
}

/* Desktop */
@media (min-width: 1024px) {
    .container {
        max-width: 960px;
    }
}
```

### 2.9.2. Breakpoints trong Tailwind CSS

| Breakpoint | Min-width | Thiết bị |
|------------|-----------|----------|
| `sm` | 640px | Mobile landscape |
| `md` | 768px | Tablet |
| `lg` | 1024px | Laptop |
| `xl` | 1280px | Desktop |
| `2xl` | 1536px | Large desktop |

**Ví dụ sử dụng trong TaskFlow:**

```html
<!-- Grid responsive -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
    <!-- Cards -->
</div>

<!-- Sidebar responsive -->
<aside class="hidden lg:block w-64">
    <!-- Sidebar content -->
</aside>
```

### 2.9.3. Dark Mode

TaskFlow hỗ trợ dark mode sử dụng Tailwind CSS:

```html
<!-- Toggle dark mode -->
<html class="dark">
    <body class="bg-white dark:bg-gray-900 text-gray-900 dark:text-white">
        <!-- Content -->
    </body>
</html>
```

```javascript
// JavaScript toggle
function toggleDarkMode() {
    document.documentElement.classList.toggle('dark');
    localStorage.setItem('theme', 
        document.documentElement.classList.contains('dark') ? 'dark' : 'light'
    );
}
```

---

## 2.10. Phương Pháp Quản Lý Dự Án Agile

### 2.10.1. Kanban Board

Kanban là phương pháp quản lý công việc trực quan, sử dụng bảng với các cột trạng thái.

```
┌─────────────┬─────────────┬─────────────┬─────────────┬─────────────┐
│   BACKLOG   │    TODO     │ IN PROGRESS │   REVIEW    │    DONE     │
├─────────────┼─────────────┼─────────────┼─────────────┼─────────────┤
│ ┌─────────┐ │ ┌─────────┐ │ ┌─────────┐ │ ┌─────────┐ │ ┌─────────┐ │
│ │ Task 1  │ │ │ Task 3  │ │ │ Task 5  │ │ │ Task 7  │ │ │ Task 9  │ │
│ └─────────┘ │ └─────────┘ │ └─────────┘ │ └─────────┘ │ └─────────┘ │
│ ┌─────────┐ │ ┌─────────┐ │ ┌─────────┐ │             │ ┌─────────┐ │
│ │ Task 2  │ │ │ Task 4  │ │ │ Task 6  │ │             │ │ Task 10 │ │
│ └─────────┘ │ └─────────┘ │ └─────────┘ │             │ └─────────┘ │
└─────────────┴─────────────┴─────────────┴─────────────┴─────────────┘
```

**Hình 2.6: Kanban Board trong TaskFlow**

**Các trạng thái task trong TaskFlow:**
1. **Backlog**: Công việc chưa được lên kế hoạch
2. **Todo**: Công việc đã lên kế hoạch, chưa bắt đầu
3. **In Progress**: Đang thực hiện
4. **In Review**: Đang review/kiểm tra
5. **Done**: Hoàn thành


### 2.10.2. Gantt Chart

Gantt Chart là biểu đồ hiển thị timeline của các công việc theo thời gian.

```
Task          │ Week 1  │ Week 2  │ Week 3  │ Week 4  │
──────────────┼─────────┼─────────┼─────────┼─────────┤
Design UI     │████████ │         │         │         │
Backend API   │    █████│█████████│         │         │
Frontend      │         │    █████│█████████│         │
Testing       │         │         │    █████│█████    │
Deployment    │         │         │         │    █████│
```

**Hình 2.7: Ví dụ Gantt Chart**

TaskFlow hiển thị Gantt Chart trong Dashboard và Calendar với:
- Timeline 14 ngày (3 ngày trước + 11 ngày tới)
- Màu sắc theo project
- Tooltip hiển thị thông tin chi tiết
- Click để xem chi tiết task

---

## 2.11. So Sánh Với Các Giải Pháp Hiện Có

### 2.11.1. So sánh tính năng

| Tính năng | TaskFlow | Trello | Asana | Jira |
|-----------|:--------:|:------:|:-----:|:----:|
| **Kanban Board** | ✅ | ✅ | ✅ | ✅ |
| **Gantt Chart** | ✅ | ❌ | ✅ | ✅ |
| **Calendar** | ✅ | ✅ | ✅ | ✅ |
| **Document Management** | ✅ | ❌ | ✅ | ✅ |
| **Team Management** | ✅ | ✅ | ✅ | ✅ |
| **Reports** | ✅ | ❌ | ✅ | ✅ |
| **Self-hosted** | ✅ | ❌ | ❌ | ✅ |
| **Free** | ✅ | Giới hạn | Giới hạn | Giới hạn |
| **Open Source** | ✅ | ❌ | ❌ | ❌ |

### 2.11.2. So sánh chi phí

| Giải pháp | Free Tier | Paid Plan | Self-hosted |
|-----------|-----------|-----------|-------------|
| **TaskFlow** | Không giới hạn | N/A | ✅ Miễn phí |
| **Trello** | 10 boards | $5/user/month | ❌ |
| **Asana** | 15 users | $10.99/user/month | ❌ |
| **Jira** | 10 users | $7.75/user/month | $42,000/year |

### 2.11.3. Ưu điểm của TaskFlow

1. **Miễn phí và Open Source**: Không tốn chi phí license
2. **Self-hosted**: Kiểm soát hoàn toàn dữ liệu
3. **Tùy chỉnh cao**: Có thể modify theo nhu cầu
4. **Đơn giản**: Dễ cài đặt và sử dụng
5. **Nhẹ**: Không cần server mạnh

### 2.11.4. Nhược điểm của TaskFlow

1. **Tính năng hạn chế**: Ít tính năng hơn các giải pháp enterprise
2. **Không có mobile app**: Chỉ có web interface
3. **Cần tự maintain**: Không có support team
4. **Không có real-time**: Cần refresh để thấy thay đổi

---

## 2.12. Tổng Kết Chương

### 2.12.1. Các lý thuyết đã nghiên cứu

| STT | Lý thuyết/Công nghệ | Ứng dụng trong TaskFlow |
|-----|---------------------|-------------------------|
| 1 | Mô hình MVC | Kiến trúc ứng dụng |
| 2 | Mô hình RBAC | Hệ thống phân quyền |
| 3 | RESTful API | Thiết kế API |
| 4 | Relational Database | Thiết kế CSDL |
| 5 | Design Patterns | Singleton, Repository, Middleware |
| 6 | Web Security | Authentication, Authorization |
| 7 | Responsive Design | Giao diện đa thiết bị |
| 8 | Agile/Kanban | Quản lý công việc |

### 2.12.2. Công nghệ được chọn

| Thành phần | Công nghệ | Lý do |
|------------|-----------|-------|
| Backend | PHP 8.x | Phổ biến, dễ deploy, chi phí thấp |
| Database | MySQL 8.0 | Tích hợp tốt với PHP, hiệu năng cao |
| Frontend | Tailwind CSS | Utility-first, customizable, responsive |
| JavaScript | Alpine.js | Nhẹ, dễ học, không cần build |
| Icons | Lucide Icons | Đẹp, nhẹ, nhiều lựa chọn |

### 2.12.3. Kết luận

Chương này đã trình bày các lý thuyết và công nghệ nền tảng cho việc xây dựng hệ thống quản lý dự án TaskFlow:

1. **Kiến trúc MVC** giúp tổ chức code rõ ràng, dễ bảo trì
2. **Mô hình RBAC** đảm bảo phân quyền linh hoạt và an toàn
3. **RESTful API** cung cấp giao diện thống nhất cho client
4. **Các design patterns** giúp code clean và reusable
5. **Bảo mật web** được áp dụng đầy đủ theo OWASP guidelines
6. **Responsive design** đảm bảo trải nghiệm tốt trên mọi thiết bị

Các lý thuyết này sẽ được áp dụng cụ thể trong chương tiếp theo về phân tích và thiết kế hệ thống.

---

## Tài Liệu Tham Khảo

### Sách và tài liệu học thuật

[1] Fowler, M. (2002). *Patterns of Enterprise Application Architecture*. Addison-Wesley Professional.

[2] Gamma, E., Helm, R., Johnson, R., & Vlissides, J. (1994). *Design Patterns: Elements of Reusable Object-Oriented Software*. Addison-Wesley.

[3] Fielding, R. T. (2000). *Architectural Styles and the Design of Network-based Software Architectures*. Doctoral dissertation, University of California, Irvine.

[4] Elmasri, R., & Navathe, S. B. (2015). *Fundamentals of Database Systems* (7th ed.). Pearson.

[5] OWASP Foundation. (2021). *OWASP Top Ten Web Application Security Risks*. https://owasp.org/Top10/

### Tài liệu kỹ thuật

[6] PHP Documentation. (2024). *PHP Manual*. https://www.php.net/manual/

[7] MySQL Documentation. (2024). *MySQL 8.0 Reference Manual*. https://dev.mysql.com/doc/refman/8.0/en/

[8] Tailwind CSS Documentation. (2024). *Tailwind CSS Docs*. https://tailwindcss.com/docs

[9] Alpine.js Documentation. (2024). *Alpine.js Docs*. https://alpinejs.dev/

[10] PDO Documentation. (2024). *PHP Data Objects*. https://www.php.net/manual/en/book.pdo.php

### Bài báo và nghiên cứu

[11] Sandhu, R. S., Coyne, E. J., Feinstein, H. L., & Youman, C. E. (1996). "Role-Based Access Control Models". *IEEE Computer*, 29(2), 38-47.

[12] Anderson, D. J. (2010). *Kanban: Successful Evolutionary Change for Your Technology Business*. Blue Hole Press.

[13] Beck, K., et al. (2001). *Manifesto for Agile Software Development*. https://agilemanifesto.org/

---

*Chương 2 - Nghiên cứu lý thuyết*
*Đồ án: Hệ thống quản lý dự án TaskFlow*
