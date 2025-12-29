# CHƯƠNG 3: HIỆN THỰC HÓA NGHIÊN CỨU

## 3.1. Tổng Quan Hệ Thống TaskFlow

### 3.1.1. Giới thiệu hệ thống

TaskFlow là hệ thống quản lý dự án và công việc được phát triển dựa trên nền tảng web, sử dụng kiến trúc MVC (Model-View-Controller) với PHP làm ngôn ngữ backend chính. Hệ thống được thiết kế để đáp ứng nhu cầu quản lý dự án của các doanh nghiệp vừa và nhỏ tại Việt Nam.

**Các tính năng chính:**
- Quản lý dự án với nhiều trạng thái và độ ưu tiên
- Quản lý công việc theo phương pháp Kanban
- Quản lý tài liệu với hệ thống thư mục phân cấp
- Hệ thống bình luận đa cấp (nested comments)
- Lịch và sự kiện với nhắc nhở
- Báo cáo và thống kê trực quan
- Hệ thống phân quyền RBAC 4 cấp
- Giao diện responsive hỗ trợ đa thiết bị

### 3.1.2. Kiến trúc tổng thể

```
┌─────────────────────────────────────────────────────────────────────────┐
│                           CLIENT LAYER                                   │
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐    │
│  │   Browser   │  │   Mobile    │  │   Tablet    │  │   Desktop   │    │
│  └──────┬──────┘  └──────┬──────┘  └──────┬──────┘  └──────┬──────┘    │
└─────────┼────────────────┼────────────────┼────────────────┼────────────┘
          │                │                │                │
          └────────────────┴────────────────┴────────────────┘
                                    │
                                    ▼
┌─────────────────────────────────────────────────────────────────────────┐
│                        PRESENTATION LAYER                                │
│  ┌─────────────────────────────────────────────────────────────────┐    │
│  │                    Tailwind CSS + Alpine.js                      │    │
│  │  • Responsive Design    • Dark Mode    • Interactive Components  │    │
│  └─────────────────────────────────────────────────────────────────┘    │
└─────────────────────────────────────────────────────────────────────────┘
                                    │
                                    ▼
┌─────────────────────────────────────────────────────────────────────────┐
│                        APPLICATION LAYER (PHP 8.x)                       │
│  ┌───────────────┐  ┌───────────────┐  ┌───────────────┐               │
│  │  Controllers  │  │   Middleware  │  │     Core      │               │
│  │  • Auth       │  │  • Auth       │  │  • Database   │               │
│  │  • Project    │  │  • Permission │  │  • Session    │               │
│  │  • Task       │  │               │  │  • View       │               │
│  │  • Document   │  │               │  │  • Permission │               │
│  └───────────────┘  └───────────────┘  └───────────────┘               │
│  ┌───────────────┐  ┌───────────────┐  ┌───────────────┐               │
│  │    Models     │  │     Views     │  │      API      │               │
│  │  • User       │  │  • Layouts    │  │  • REST API   │               │
│  │  • Project    │  │  • Components │  │  • JSON       │               │
│  │  • Task       │  │  • Pages      │  │  • AJAX       │               │
│  └───────────────┘  └───────────────┘  └───────────────┘               │
└─────────────────────────────────────────────────────────────────────────┘
                                    │
                                    ▼
┌─────────────────────────────────────────────────────────────────────────┐
│                          DATA LAYER                                      │
│  ┌─────────────────────────────────────────────────────────────────┐    │
│  │                      MySQL 8.0 Database                          │    │
│  │  • 16 Tables    • UUID Primary Keys    • Foreign Key Constraints │    │
│  └─────────────────────────────────────────────────────────────────┘    │
└─────────────────────────────────────────────────────────────────────────┘
```

**Hình 3.1: Kiến trúc tổng thể hệ thống TaskFlow**


---

## 3.2. Cấu Trúc Thư Mục Dự Án

### 3.2.1. Tổ chức thư mục theo MVC

```
taskflow/
├── app/                          # Application Layer
│   ├── controllers/              # Xử lý logic điều hướng
│   │   ├── BaseController.php    # Controller cơ sở
│   │   ├── AuthController.php    # Xác thực người dùng
│   │   ├── ProjectController.php # Quản lý dự án
│   │   ├── TaskController.php    # Quản lý công việc
│   │   ├── DocumentController.php# Quản lý tài liệu
│   │   ├── CalendarController.php# Quản lý lịch
│   │   ├── AdminController.php   # Quản trị hệ thống
│   │   └── ManagerController.php # Chức năng quản lý
│   │
│   ├── models/                   # Tương tác cơ sở dữ liệu
│   │   ├── BaseModel.php         # Model cơ sở
│   │   ├── User.php              # Model người dùng
│   │   ├── Project.php           # Model dự án
│   │   ├── Task.php              # Model công việc
│   │   ├── Document.php          # Model tài liệu
│   │   └── CalendarEvent.php     # Model sự kiện
│   │
│   ├── views/                    # Giao diện người dùng
│   │   ├── layouts/              # Layout chung
│   │   │   ├── main.php          # Layout chính
│   │   │   ├── admin.php         # Layout admin
│   │   │   └── guest.php         # Layout khách
│   │   ├── components/           # Components tái sử dụng
│   │   │   ├── header.php        # Header
│   │   │   ├── sidebar.php       # Sidebar
│   │   │   └── search-modal.php  # Modal tìm kiếm
│   │   ├── auth/                 # Trang xác thực
│   │   ├── dashboard/            # Dashboard
│   │   ├── projects/             # Trang dự án
│   │   ├── tasks/                # Trang công việc
│   │   ├── documents/            # Trang tài liệu
│   │   ├── calendar/             # Trang lịch
│   │   ├── admin/                # Trang quản trị
│   │   └── manager/              # Trang quản lý
│   │
│   └── middleware/               # Middleware xử lý trung gian
│       ├── AuthMiddleware.php    # Kiểm tra đăng nhập
│       └── PermissionMiddleware.php # Kiểm tra quyền
│
├── api/                          # RESTful API Endpoints
│   ├── create-project.php        # Tạo dự án
│   ├── update-project.php        # Cập nhật dự án
│   ├── create-task.php           # Tạo công việc
│   ├── update-task.php           # Cập nhật công việc
│   ├── comments.php              # API bình luận
│   ├── checklist.php             # API checklist
│   ├── upload-document.php       # Upload tài liệu
│   ├── calendar.php              # API lịch
│   ├── notifications.php         # API thông báo
│   ├── search.php                # API tìm kiếm
│   └── reports.php               # API báo cáo
│
├── core/                         # Core Framework
│   ├── Database.php              # Kết nối CSDL (Singleton)
│   ├── Session.php               # Quản lý session
│   ├── View.php                  # Render view
│   ├── Permission.php            # Xử lý phân quyền
│   ├── Validator.php             # Validation dữ liệu
│   ├── Logger.php                # Ghi log
│   └── RateLimiter.php           # Giới hạn request
│
├── config/                       # Cấu hình hệ thống
│   ├── app.php                   # Cấu hình ứng dụng
│   ├── database.php              # Cấu hình CSDL
│   └── permissions.php           # Cấu hình phân quyền
│
├── database/                     # Database scripts
│   ├── taskflow2.sql             # Schema chính
│   ├── seed.sql                  # Dữ liệu mẫu
│   └── migrate-*.php             # Migration scripts
│
├── public/                       # Public assets
│   └── assets/                   # CSS, JS, Images
│
├── storage/                      # File storage
│   ├── logs/                     # Application logs
│   ├── cache/                    # Cache files
│   └── rate_limits/              # Rate limit data
│
├── uploads/                      # User uploads
│   ├── avatars/                  # Avatar người dùng
│   └── documents/                # Tài liệu upload
│
├── bootstrap.php                 # Bootstrap application
├── index.php                     # Entry point
└── .env.example                  # Environment template
```

**Hình 3.2: Cấu trúc thư mục chi tiết của TaskFlow**

### 3.2.2. Giải thích các thành phần

| Thư mục | Chức năng | Số file |
|---------|-----------|---------|
| `app/controllers` | Xử lý request, điều hướng logic | 10 files |
| `app/models` | Tương tác database, business logic | 6 files |
| `app/views` | Template HTML, giao diện | 35+ files |
| `app/middleware` | Xử lý trung gian (auth, permission) | 2 files |
| `api/` | RESTful API endpoints | 30+ files |
| `core/` | Framework core classes | 8 files |
| `config/` | Configuration files | 3 files |
| `database/` | SQL scripts, migrations | 15+ files |


---

## 3.3. Hiện Thực Core Framework

### 3.3.1. Database Class - Singleton Pattern

Lớp `Database` được thiết kế theo Singleton Pattern để đảm bảo chỉ có một kết nối database duy nhất trong toàn bộ ứng dụng.

```php
<?php
namespace Core;

class Database
{
    private static ?Database $instance = null;
    private ?PDO $connection = null;
    private array $config;

    // Private constructor - không cho phép tạo instance từ bên ngoài
    private function __construct()
    {
        $this->config = require BASE_PATH . '/config/database.php';
    }

    // Singleton getInstance
    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    // Lazy connection - chỉ kết nối khi cần
    public function getConnection(): PDO
    {
        if ($this->connection === null) {
            $this->connect();
        }
        return $this->connection;
    }

    private function connect(): void
    {
        $dsn = sprintf(
            "%s:host=%s;port=%s;dbname=%s;charset=%s",
            $this->config['driver'],
            $this->config['host'],
            $this->config['port'],
            $this->config['database'],
            $this->config['charset']
        );

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false, // Bảo mật SQL Injection
        ];

        $this->connection = new PDO(
            $dsn,
            $this->config['username'],
            $this->config['password'],
            $options
        );
    }

    // Các phương thức CRUD
    public function fetchAll(string $sql, array $params = []): array
    {
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function fetchOne(string $sql, array $params = []): ?array
    {
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function insert(string $table, array $data): string|false
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        
        $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";
        
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->execute(array_values($data));
        
        return $this->getConnection()->lastInsertId();
    }

    public function update(string $table, array $data, string $where, array $whereParams = []): int
    {
        $set = implode(' = ?, ', array_keys($data)) . ' = ?';
        $sql = "UPDATE {$table} SET {$set} WHERE {$where}";
        
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->execute(array_merge(array_values($data), $whereParams));
        
        return $stmt->rowCount();
    }

    public function delete(string $table, string $where, array $params = []): int
    {
        $sql = "DELETE FROM {$table} WHERE {$where}";
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount();
    }

    // Transaction support
    public function beginTransaction(): bool { return $this->getConnection()->beginTransaction(); }
    public function commit(): bool { return $this->getConnection()->commit(); }
    public function rollback(): bool { return $this->getConnection()->rollBack(); }

    // Ngăn clone và unserialize
    private function __clone() {}
    public function __wakeup() { throw new Exception("Cannot unserialize singleton"); }
}
```

**Đặc điểm nổi bật:**
- **Singleton Pattern**: Đảm bảo một instance duy nhất
- **Lazy Loading**: Chỉ kết nối khi thực sự cần
- **Prepared Statements**: Chống SQL Injection
- **Transaction Support**: Hỗ trợ giao dịch ACID

### 3.3.2. Session Management

```php
<?php
namespace Core;

class Session
{
    private static bool $started = false;

    public static function start(): void
    {
        if (self::$started) return;

        if (session_status() === PHP_SESSION_NONE) {
            $config = require BASE_PATH . '/config/app.php';
            $lifetime = $config['session']['lifetime'] ?? 120;
            
            session_set_cookie_params([
                'lifetime' => $lifetime * 60,
                'path' => '/',
                'secure' => isset($_SERVER['HTTPS']),  // HTTPS only
                'httponly' => true,                     // Chống XSS
                'samesite' => 'Lax',                   // Chống CSRF
            ]);
            
            session_start();
        }
        
        self::$started = true;
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        self::start();
        return $_SESSION[$key] ?? $default;
    }

    public static function set(string $key, mixed $value): void
    {
        self::start();
        $_SESSION[$key] = $value;
    }

    public static function has(string $key): bool
    {
        self::start();
        return isset($_SESSION[$key]);
    }

    public static function remove(string $key): void
    {
        self::start();
        unset($_SESSION[$key]);
    }

    // Flash messages - hiển thị một lần rồi xóa
    public static function flash(string $key, mixed $value = null): mixed
    {
        self::start();
        
        if ($value !== null) {
            $_SESSION['_flash'][$key] = $value;
            return $value;
        }
        
        $flashValue = $_SESSION['_flash'][$key] ?? null;
        unset($_SESSION['_flash'][$key]);
        return $flashValue;
    }

    public static function destroy(): void
    {
        self::start();
        $_SESSION = [];
        
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, 
                $params['path'], $params['domain'], 
                $params['secure'], $params['httponly']);
        }
        
        session_destroy();
        self::$started = false;
    }

    public static function regenerate(): void
    {
        self::start();
        session_regenerate_id(true); // Chống Session Fixation
    }
}
```

**Bảo mật Session:**
| Cấu hình | Giá trị | Mục đích |
|----------|---------|----------|
| `secure` | true (HTTPS) | Chỉ gửi cookie qua HTTPS |
| `httponly` | true | Ngăn JavaScript truy cập cookie |
| `samesite` | Lax | Chống CSRF attacks |
| `regenerate_id` | true | Chống Session Fixation |


### 3.3.3. Permission System - RBAC Implementation

```php
<?php
namespace Core;

class Permission
{
    private static ?array $config = null;
    
    private static function loadConfig(): array
    {
        self::$config = require BASE_PATH . '/config/permissions.php';
        return self::$config;
    }

    /**
     * Kiểm tra quyền của role
     */
    public static function can(string $role, string $permission): bool
    {
        $config = self::loadConfig();
        $permissions = $config['permissions'][$role] ?? [];
        return in_array($permission, $permissions);
    }

    /**
     * Kiểm tra tất cả quyền
     */
    public static function canAll(string $role, array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if (!self::can($role, $permission)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Kiểm tra bất kỳ quyền nào
     */
    public static function canAny(string $role, array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if (self::can($role, $permission)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Kiểm tra quyền cấp dự án
     */
    public static function canInProject(string $projectRole, string $permission): bool
    {
        $permissions = self::getProjectRolePermissions($projectRole);
        return in_array('*', $permissions) || in_array($permission, $permissions);
    }

    public static function isAdmin(string $role): bool
    {
        return $role === 'admin';
    }

    public static function isManager(string $role): bool
    {
        return in_array($role, ['admin', 'manager']);
    }
}
```

**Cấu hình phân quyền (config/permissions.php):**

```php
<?php
return [
    'roles' => [
        'admin' => [
            'name' => 'Quản trị viên',
            'description' => 'Toàn quyền hệ thống',
            'level' => 100,
        ],
        'manager' => [
            'name' => 'Quản lý',
            'description' => 'Quản lý dự án và nhóm',
            'level' => 50,
        ],
        'member' => [
            'name' => 'Thành viên',
            'description' => 'Thành viên dự án',
            'level' => 10,
        ],
        'guest' => [
            'name' => 'Khách',
            'description' => 'Chỉ xem',
            'level' => 0,
        ],
    ],
    
    'permissions' => [
        'admin' => [
            // Users
            'users.view', 'users.create', 'users.edit', 'users.delete',
            // Projects
            'projects.view', 'projects.create', 'projects.edit', 'projects.delete',
            // Tasks
            'tasks.view', 'tasks.create', 'tasks.edit', 'tasks.delete',
            // Documents
            'documents.view', 'documents.create', 'documents.edit', 'documents.delete',
            // Admin
            'admin.access', 'admin.settings', 'admin.logs', 'admin.backup',
            // Reports
            'reports.view', 'reports.export',
        ],
        'manager' => [
            'users.view',
            'projects.view', 'projects.create', 'projects.edit',
            'tasks.view', 'tasks.create', 'tasks.edit', 'tasks.delete',
            'documents.view', 'documents.create', 'documents.edit', 'documents.delete',
            'team.manage',
            'reports.view', 'reports.export',
        ],
        'member' => [
            'users.view',
            'projects.view', 'projects.create',
            'tasks.view', 'tasks.create', 'tasks.edit',
            'documents.view', 'documents.create', 'documents.edit',
        ],
        'guest' => [
            'projects.view',
            'tasks.view',
            'documents.view',
        ],
    ],
    
    'project_roles' => [
        'owner' => [
            'name' => 'Chủ sở hữu',
            'permissions' => ['*'], // Toàn quyền trong dự án
        ],
        'manager' => [
            'name' => 'Quản lý dự án',
            'permissions' => ['tasks.*', 'documents.*', 'members.manage'],
        ],
        'member' => [
            'name' => 'Thành viên',
            'permissions' => ['tasks.view', 'tasks.create', 'tasks.edit', 'documents.view'],
        ],
        'viewer' => [
            'name' => 'Người xem',
            'permissions' => ['tasks.view', 'documents.view'],
        ],
    ],
];
```

### 3.3.4. View Rendering System

```php
<?php
namespace Core;

class View
{
    private static ?string $layout = 'main';
    private static array $sections = [];
    private static ?string $currentSection = null;

    /**
     * Render view với layout
     */
    public static function render(string $view, array $data = [], ?string $layout = null): void
    {
        self::$sections = [];
        $GLOBALS['_view_data'] = $data;
        
        extract($data, EXTR_SKIP);
        
        ob_start();
        
        $viewPath = BASE_PATH . '/app/views/' . str_replace('.', '/', $view) . '.php';
        if (!file_exists($viewPath)) {
            throw new \Exception("View not found: {$view}");
        }
        include $viewPath;
        
        $content = ob_get_clean();
        
        $layoutName = $layout ?? self::$layout;
        if ($layoutName) {
            if (!isset(self::$sections['content'])) {
                self::$sections['content'] = $content;
            }
            extract($data, EXTR_SKIP);
            $layoutPath = BASE_PATH . '/app/views/layouts/' . $layoutName . '.php';
            if (file_exists($layoutPath)) {
                include $layoutPath;
            } else {
                echo self::$sections['content'];
            }
        } else {
            echo $content;
        }
        
        unset($GLOBALS['_view_data']);
    }

    /**
     * Bắt đầu section
     */
    public static function section(string $name): void
    {
        self::$currentSection = $name;
        ob_start();
    }

    /**
     * Kết thúc section
     */
    public static function endSection(): void
    {
        if (self::$currentSection) {
            self::$sections[self::$currentSection] = ob_get_clean();
            self::$currentSection = null;
        }
    }

    /**
     * Xuất section trong layout
     */
    public static function yield(string $name, string $default = ''): string
    {
        return self::$sections[$name] ?? $default;
    }

    /**
     * Include partial view
     */
    public static function partial(string $partial, array $data = []): void
    {
        extract($data);
        $partialPath = BASE_PATH . '/app/views/' . str_replace('.', '/', $partial) . '.php';
        if (file_exists($partialPath)) {
            include $partialPath;
        }
    }

    /**
     * Escape HTML - chống XSS
     */
    public static function e(mixed $value): string
    {
        return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
    }
}
```

**Sử dụng trong View:**

```php
<!-- app/views/projects/index.php -->
<?php 
use Core\View;
View::section('content'); 
?>

<div class="container">
    <h1><?= View::e($title) ?></h1>
    
    <?php foreach ($projects as $project): ?>
        <div class="project-card">
            <h3><?= View::e($project['name']) ?></h3>
            <p><?= View::e($project['description']) ?></p>
        </div>
    <?php endforeach; ?>
</div>

<?php View::endSection(); ?>
```


---

## 3.4. Hiện Thực Model Layer

### 3.4.1. BaseModel - Repository Pattern

```php
<?php
namespace App\Models;

use Core\Database;

abstract class BaseModel
{
    protected Database $db;
    protected string $table;
    protected string $primaryKey = 'id';

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Lấy tất cả records
     */
    public function all(string $orderBy = 'created_at DESC'): array
    {
        $sql = "SELECT * FROM {$this->table} ORDER BY {$orderBy}";
        return $this->db->fetchAll($sql);
    }

    /**
     * Tìm theo ID
     */
    public function find(string|int $id): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ?";
        return $this->db->fetchOne($sql, [$id]);
    }

    /**
     * Tìm theo điều kiện
     */
    public function findBy(string $column, mixed $value): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$column} = ?";
        return $this->db->fetchOne($sql, [$value]);
    }

    /**
     * Tìm nhiều theo điều kiện
     */
    public function findAllBy(string $column, mixed $value, string $orderBy = 'created_at DESC'): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$column} = ? ORDER BY {$orderBy}";
        return $this->db->fetchAll($sql, [$value]);
    }

    /**
     * Tạo mới record
     */
    public function create(array $data): string|false
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $this->db->insert($this->table, $data);
    }

    /**
     * Cập nhật record
     */
    public function update(string|int $id, array $data): int
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $this->db->update($this->table, $data, "{$this->primaryKey} = ?", [$id]);
    }

    /**
     * Xóa record
     */
    public function delete(string|int $id): int
    {
        return $this->db->delete($this->table, "{$this->primaryKey} = ?", [$id]);
    }

    /**
     * Đếm số records
     */
    public function count(string $where = '1=1', array $params = []): int
    {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE {$where}";
        return (int) $this->db->fetchColumn($sql, $params);
    }

    /**
     * Kiểm tra tồn tại
     */
    public function exists(string|int $id): bool
    {
        $sql = "SELECT 1 FROM {$this->table} WHERE {$this->primaryKey} = ? LIMIT 1";
        return $this->db->fetchColumn($sql, [$id]) !== false;
    }

    /**
     * Tạo UUID
     */
    protected function generateUUID(): string
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }
}
```

### 3.4.2. Project Model

```php
<?php
namespace App\Models;

class Project extends BaseModel
{
    protected string $table = 'projects';

    /**
     * Lấy dự án với thông tin chi tiết
     */
    public function getWithDetails(string $id): ?array
    {
        $sql = "SELECT p.*, u.full_name as creator_name,
                       (SELECT COUNT(*) FROM tasks WHERE project_id = p.id) as task_count,
                       (SELECT COUNT(*) FROM tasks WHERE project_id = p.id AND status = 'done') as completed_tasks,
                       (SELECT COUNT(*) FROM project_members WHERE project_id = p.id) as member_count
                FROM projects p
                LEFT JOIN users u ON p.created_by = u.id
                WHERE p.id = ?";
        return $this->db->fetchOne($sql, [$id]);
    }

    /**
     * Lấy dự án của user
     */
    public function getByUser(string $userId): array
    {
        $sql = "SELECT DISTINCT p.*, pm.role as user_role
                FROM projects p
                LEFT JOIN project_members pm ON p.id = pm.project_id AND pm.user_id = ?
                WHERE p.created_by = ? OR pm.user_id = ?
                ORDER BY p.updated_at DESC";
        return $this->db->fetchAll($sql, [$userId, $userId, $userId]);
    }

    /**
     * Thêm thành viên vào dự án
     */
    public function addMember(string $projectId, string $userId, string $role = 'member'): bool
    {
        $sql = "INSERT INTO project_members (project_id, user_id, role, joined_at) 
                VALUES (?, ?, ?, NOW())
                ON DUPLICATE KEY UPDATE role = VALUES(role)";
        $this->db->query($sql, [$projectId, $userId, $role]);
        return true;
    }

    /**
     * Lấy danh sách thành viên
     */
    public function getMembers(string $projectId): array
    {
        $sql = "SELECT u.id, u.full_name, u.email, u.avatar_url, pm.role, pm.joined_at
                FROM project_members pm
                JOIN users u ON pm.user_id = u.id
                WHERE pm.project_id = ?
                ORDER BY pm.role, u.full_name";
        return $this->db->fetchAll($sql, [$projectId]);
    }

    /**
     * Cập nhật tiến độ dự án
     */
    public function updateProgress(string $projectId): void
    {
        $sql = "UPDATE projects p SET progress = (
                    SELECT IFNULL(
                        ROUND(COUNT(CASE WHEN status = 'done' THEN 1 END) * 100.0 / NULLIF(COUNT(*), 0)),
                        0
                    )
                    FROM tasks WHERE project_id = p.id
                )
                WHERE p.id = ?";
        $this->db->query($sql, [$projectId]);
    }

    /**
     * Thống kê dự án
     */
    public function getStatistics(): array
    {
        $sql = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'planning' THEN 1 ELSE 0 END) as planning,
                    SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active,
                    SUM(CASE WHEN status = 'on_hold' THEN 1 ELSE 0 END) as on_hold,
                    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed,
                    SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled
                FROM projects";
        return $this->db->fetchOne($sql) ?? [];
    }
}
```

### 3.4.3. Task Model

```php
<?php
namespace App\Models;

class Task extends BaseModel
{
    protected string $table = 'tasks';

    /**
     * Lấy task với thông tin chi tiết
     */
    public function getWithDetails(string $id): ?array
    {
        $task = $this->db->fetchOne(
            "SELECT t.*, p.name as project_name, p.color as project_color,
                    u.full_name as creator_name
             FROM tasks t
             LEFT JOIN projects p ON t.project_id = p.id
             LEFT JOIN users u ON t.created_by = u.id
             WHERE t.id = ?",
            [$id]
        );

        if ($task) {
            // Lấy assignees
            $task['assignees'] = $this->db->fetchAll(
                "SELECT u.id, u.full_name, u.email, u.avatar_url
                 FROM task_assignees ta
                 JOIN users u ON ta.user_id = u.id
                 WHERE ta.task_id = ?",
                [$id]
            );

            // Lấy checklist
            $task['checklist'] = $this->db->fetchAll(
                "SELECT * FROM task_checklists WHERE task_id = ? ORDER BY position",
                [$id]
            );

            // Lấy comments
            $task['comments'] = $this->db->fetchAll(
                "SELECT c.*, u.full_name, u.avatar_url
                 FROM comments c
                 JOIN users u ON c.created_by = u.id
                 WHERE c.entity_type = 'task' AND c.entity_id = ?
                 ORDER BY c.created_at DESC",
                [$id]
            );
        }

        return $task;
    }

    /**
     * Lấy tasks theo project
     */
    public function getByProject(string $projectId): array
    {
        $sql = "SELECT t.*, 
                       (SELECT GROUP_CONCAT(u.full_name) 
                        FROM task_assignees ta 
                        JOIN users u ON ta.user_id = u.id 
                        WHERE ta.task_id = t.id) as assignee_names
                FROM tasks t
                WHERE t.project_id = ?
                ORDER BY t.position, t.created_at DESC";
        return $this->db->fetchAll($sql, [$projectId]);
    }

    /**
     * Lấy tasks theo trạng thái (Kanban)
     */
    public function getByStatus(string $status, ?string $projectId = null): array
    {
        $sql = "SELECT t.*, p.name as project_name, p.color as project_color
                FROM tasks t
                LEFT JOIN projects p ON t.project_id = p.id
                WHERE t.status = ?";
        $params = [$status];

        if ($projectId) {
            $sql .= " AND t.project_id = ?";
            $params[] = $projectId;
        }

        $sql .= " ORDER BY t.position, t.created_at DESC";
        return $this->db->fetchAll($sql, $params);
    }

    /**
     * Giao task cho user
     */
    public function assignUser(string $taskId, string $userId, string $assignedBy): bool
    {
        $sql = "INSERT INTO task_assignees (task_id, user_id, assigned_by, assigned_at)
                VALUES (?, ?, ?, NOW())
                ON DUPLICATE KEY UPDATE assigned_by = VALUES(assigned_by)";
        $this->db->query($sql, [$taskId, $userId, $assignedBy]);
        return true;
    }

    /**
     * Cập nhật vị trí (drag & drop)
     */
    public function updatePosition(string $taskId, int $position, ?string $status = null): void
    {
        $data = ['position' => $position];
        if ($status) {
            $data['status'] = $status;
            if ($status === 'done') {
                $data['completed_at'] = date('Y-m-d H:i:s');
            }
        }
        $this->update($taskId, $data);
    }

    /**
     * Thống kê tasks
     */
    public function getStatistics(?string $userId = null): array
    {
        $where = $userId ? "WHERE t.created_by = ? OR ta.user_id = ?" : "";
        $params = $userId ? [$userId, $userId] : [];

        $sql = "SELECT 
                    COUNT(DISTINCT t.id) as total,
                    SUM(CASE WHEN t.status = 'backlog' THEN 1 ELSE 0 END) as backlog,
                    SUM(CASE WHEN t.status = 'todo' THEN 1 ELSE 0 END) as todo,
                    SUM(CASE WHEN t.status = 'in_progress' THEN 1 ELSE 0 END) as in_progress,
                    SUM(CASE WHEN t.status = 'in_review' THEN 1 ELSE 0 END) as in_review,
                    SUM(CASE WHEN t.status = 'done' THEN 1 ELSE 0 END) as done,
                    SUM(CASE WHEN t.due_date < CURDATE() AND t.status != 'done' THEN 1 ELSE 0 END) as overdue
                FROM tasks t
                LEFT JOIN task_assignees ta ON t.id = ta.task_id
                {$where}";
        return $this->db->fetchOne($sql, $params) ?? [];
    }
}
```


---

## 3.5. Hiện Thực Controller Layer

### 3.5.1. BaseController

```php
<?php
namespace App\Controllers;

use Core\View;
use Core\Session;
use App\Middleware\AuthMiddleware;
use App\Middleware\PermissionMiddleware;

abstract class BaseController
{
    /**
     * Render view
     */
    protected function view(string $view, array $data = [], ?string $layout = null): void
    {
        View::render($view, $data, $layout);
    }

    /**
     * Trả về JSON response
     */
    protected function json(array $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    /**
     * Redirect
     */
    protected function redirect(string $url): void
    {
        header("Location: $url");
        exit;
    }

    /**
     * Quay lại trang trước
     */
    protected function back(): void
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? '/php/index.php';
        $this->redirect($referer);
    }

    /**
     * Flash messages
     */
    protected function success(string $message): void
    {
        Session::flash('success', $message);
    }

    protected function error(string $message): void
    {
        Session::flash('error', $message);
    }

    /**
     * Lấy thông tin user hiện tại
     */
    protected function userId(): ?string
    {
        return AuthMiddleware::userId();
    }

    protected function userRole(): string
    {
        return AuthMiddleware::userRole();
    }

    /**
     * Kiểm tra quyền
     */
    protected function can(string $permission): bool
    {
        return PermissionMiddleware::can($permission);
    }

    protected function isAdmin(): bool
    {
        return $this->userRole() === 'admin';
    }

    protected function isManager(): bool
    {
        return in_array($this->userRole(), ['admin', 'manager']);
    }

    /**
     * Lấy input
     */
    protected function input(string $key, mixed $default = null): mixed
    {
        return $_POST[$key] ?? $_GET[$key] ?? $default;
    }

    /**
     * Validate input
     */
    protected function validate(array $rules): array
    {
        $errors = [];
        
        foreach ($rules as $field => $rule) {
            $value = $this->input($field);
            $ruleList = explode('|', $rule);
            
            foreach ($ruleList as $r) {
                if ($r === 'required' && empty($value)) {
                    $errors[$field] = "Trường {$field} là bắt buộc";
                    break;
                }
                
                if (str_starts_with($r, 'min:')) {
                    $min = (int) substr($r, 4);
                    if (strlen($value) < $min) {
                        $errors[$field] = "Trường {$field} phải có ít nhất {$min} ký tự";
                        break;
                    }
                }
                
                if ($r === 'email' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $errors[$field] = "Email không hợp lệ";
                    break;
                }
            }
        }
        
        return $errors;
    }
}
```

### 3.5.2. AuthController - Xác thực người dùng

```php
<?php
namespace App\Controllers;

use App\Models\User;
use Core\Session;

class AuthController extends BaseController
{
    private User $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    /**
     * Hiển thị form đăng nhập
     */
    public function showLogin(): void
    {
        $this->view('auth/login', [], 'guest');
    }

    /**
     * Xử lý đăng nhập
     */
    public function login(): void
    {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $remember = isset($_POST['remember']);

        // Validate
        if (empty($email) || empty($password)) {
            $this->error('Vui lòng nhập email và mật khẩu');
            $this->redirect('/php/login.php');
            return;
        }

        // Tìm user
        $user = $this->userModel->findBy('email', $email);

        if (!$user) {
            $this->error('Email hoặc mật khẩu không đúng');
            $this->redirect('/php/login.php');
            return;
        }

        // Kiểm tra tài khoản active
        if (!$user['is_active']) {
            $this->error('Tài khoản đã bị khóa');
            $this->redirect('/php/login.php');
            return;
        }

        // Verify password với bcrypt
        if (!password_verify($password, $user['password_hash'])) {
            $this->error('Email hoặc mật khẩu không đúng');
            $this->redirect('/php/login.php');
            return;
        }

        // Đăng nhập thành công
        Session::regenerate(); // Chống Session Fixation
        
        Session::set('user_id', $user['id']);
        Session::set('user_email', $user['email']);
        Session::set('user_name', $user['full_name']);
        Session::set('user_role', $user['role']);
        Session::set('user_avatar', $user['avatar_url']);

        // Cập nhật last_login
        $this->userModel->update($user['id'], [
            'last_login_at' => date('Y-m-d H:i:s')
        ]);

        // Remember me
        if ($remember) {
            $token = bin2hex(random_bytes(32));
            $expiry = date('Y-m-d H:i:s', strtotime('+30 days'));
            
            $this->userModel->update($user['id'], [
                'remember_token' => hash('sha256', $token),
                'remember_token_expiry' => $expiry
            ]);

            setcookie('remember_token', $token, [
                'expires' => strtotime('+30 days'),
                'path' => '/',
                'secure' => isset($_SERVER['HTTPS']),
                'httponly' => true,
                'samesite' => 'Lax'
            ]);
        }

        // Redirect đến intended URL hoặc dashboard
        $intended = Session::get('intended_url', '/php/index.php');
        Session::remove('intended_url');
        
        $this->success('Đăng nhập thành công');
        $this->redirect($intended);
    }

    /**
     * Đăng xuất
     */
    public function logout(): void
    {
        $userId = Session::get('user_id');
        
        if ($userId) {
            // Xóa remember token
            $this->userModel->update($userId, [
                'remember_token' => null,
                'remember_token_expiry' => null
            ]);
        }

        // Xóa cookie
        setcookie('remember_token', '', time() - 3600, '/');
        
        // Destroy session
        Session::destroy();
        
        $this->redirect('/php/login.php');
    }

    /**
     * Đăng ký tài khoản
     */
    public function register(): void
    {
        $fullName = trim($_POST['full_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $passwordConfirm = $_POST['password_confirmation'] ?? '';

        // Validate
        $errors = [];
        
        if (empty($fullName) || strlen($fullName) < 2) {
            $errors['full_name'] = 'Họ tên phải có ít nhất 2 ký tự';
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Email không hợp lệ';
        }
        
        if (strlen($password) < 6) {
            $errors['password'] = 'Mật khẩu phải có ít nhất 6 ký tự';
        }
        
        if ($password !== $passwordConfirm) {
            $errors['password_confirmation'] = 'Xác nhận mật khẩu không khớp';
        }

        // Kiểm tra email tồn tại
        if ($this->userModel->findBy('email', $email)) {
            $errors['email'] = 'Email đã được sử dụng';
        }

        if (!empty($errors)) {
            Session::flash('errors', $errors);
            Session::flash('old', $_POST);
            $this->redirect('/php/register.php');
            return;
        }

        // Tạo user
        $userId = $this->userModel->generateUUID();
        $this->userModel->create([
            'id' => $userId,
            'email' => $email,
            'password_hash' => password_hash($password, PASSWORD_BCRYPT),
            'full_name' => $fullName,
            'role' => 'member',
            'is_active' => 1,
        ]);

        $this->success('Đăng ký thành công! Vui lòng đăng nhập.');
        $this->redirect('/php/login.php');
    }
}
```

### 3.5.3. Middleware - Xử lý trung gian

```php
<?php
namespace App\Middleware;

use Core\Session;

class AuthMiddleware
{
    /**
     * Kiểm tra đăng nhập
     */
    public static function handle(): bool
    {
        Session::start();
        
        if (!Session::has('user_id')) {
            $isApi = self::isApiRequest();
            
            if ($isApi) {
                http_response_code(401);
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'error' => 'Unauthorized']);
                exit;
            }
            
            Session::set('intended_url', $_SERVER['REQUEST_URI']);
            header('Location: /php/login.php');
            exit;
        }
        
        return true;
    }
    
    /**
     * Kiểm tra API request
     */
    private static function isApiRequest(): bool
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '';
        $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
        
        return strpos($uri, '/api/') !== false ||
               strpos($accept, 'application/json') !== false ||
               isset($_SERVER['HTTP_X_REQUESTED_WITH']);
    }

    public static function userId(): ?string
    {
        Session::start();
        return Session::get('user_id');
    }

    public static function userRole(): string
    {
        Session::start();
        return Session::get('user_role', 'guest');
    }
}
```

```php
<?php
namespace App\Middleware;

use Core\Permission;
use Core\Session;

class PermissionMiddleware
{
    /**
     * Yêu cầu quyền cụ thể
     */
    public static function requirePermission(string $permission): void
    {
        $role = Session::get('user_role', 'guest');
        
        if (!Permission::can($role, $permission)) {
            if (self::isApiRequest()) {
                http_response_code(403);
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'error' => 'Forbidden']);
                exit;
            }
            
            header('Location: /php/403.php');
            exit;
        }
    }

    /**
     * Kiểm tra quyền (không redirect)
     */
    public static function can(string $permission): bool
    {
        $role = Session::get('user_role', 'guest');
        return Permission::can($role, $permission);
    }

    /**
     * Yêu cầu role admin
     */
    public static function requireAdmin(): void
    {
        $role = Session::get('user_role', 'guest');
        
        if ($role !== 'admin') {
            header('Location: /php/403.php');
            exit;
        }
    }

    /**
     * Yêu cầu role manager trở lên
     */
    public static function requireManager(): void
    {
        $role = Session::get('user_role', 'guest');
        
        if (!in_array($role, ['admin', 'manager'])) {
            header('Location: /php/403.php');
            exit;
        }
    }
}
```


---

## 3.6. Hiện Thực RESTful API

### 3.6.1. Cấu trúc API Response

TaskFlow sử dụng chuẩn JSON response thống nhất:

```json
// Success Response
{
    "success": true,
    "data": { ... },
    "message": "Thao tác thành công"
}

// Error Response
{
    "success": false,
    "error": "Mô tả lỗi",
    "errors": {
        "field1": "Lỗi field 1",
        "field2": "Lỗi field 2"
    }
}

// Paginated Response
{
    "success": true,
    "data": [ ... ],
    "pagination": {
        "total": 100,
        "per_page": 20,
        "current_page": 1,
        "last_page": 5
    }
}
```

### 3.6.2. API Create Project

```php
<?php
// api/create-project.php
require_once __DIR__ . '/../bootstrap.php';

use App\Models\Project;
use App\Middleware\AuthMiddleware;
use Core\Session;
use Core\Permission;

// Kiểm tra authentication
AuthMiddleware::handle();

// Kiểm tra permission
$userRole = Session::get('user_role', 'guest');
if (!Permission::can($userRole, 'projects.create')) {
    http_response_code(403);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Bạn không có quyền tạo dự án']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

// Parse input - hỗ trợ cả JSON và form data
$contentType = $_SERVER['CONTENT_TYPE'] ?? '';
if (strpos($contentType, 'application/json') !== false) {
    $input = json_decode(file_get_contents('php://input'), true) ?? [];
    $_POST = array_merge($_POST, $input);
}

$name = trim($_POST['name'] ?? '');
$description = trim($_POST['description'] ?? '');
$color = $_POST['color'] ?? '#6366f1';
$priority = $_POST['priority'] ?? 'medium';
$startDate = $_POST['start_date'] ?? null;
$endDate = $_POST['end_date'] ?? null;

// Validate
if (empty($name)) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Tên dự án là bắt buộc']);
    exit;
}

if (strlen($name) < 2) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Tên dự án phải có ít nhất 2 ký tự']);
    exit;
}

try {
    $projectModel = new Project();
    $userId = Session::get('user_id');
    
    // Generate UUID
    $projectId = sprintf(
        '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand(0, 0xffff), mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0x0fff) | 0x4000,
        mt_rand(0, 0x3fff) | 0x8000,
        mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
    );
    
    // Tạo project
    $projectModel->create([
        'id' => $projectId,
        'name' => $name,
        'description' => $description,
        'color' => $color,
        'status' => 'planning',
        'priority' => $priority,
        'progress' => 0,
        'start_date' => $startDate ?: null,
        'end_date' => $endDate ?: null,
        'created_by' => $userId,
    ]);
    
    // Thêm creator làm owner
    $projectModel->addMember($projectId, $userId, 'owner');
    
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'message' => 'Tạo dự án thành công',
        'project_id' => $projectId
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
```

### 3.6.3. API Comments với Nested Replies

```php
<?php
// api/comments.php
require_once __DIR__ . '/../bootstrap.php';

use App\Middleware\AuthMiddleware;
use Core\Session;
use Core\Database;

header('Content-Type: application/json');

AuthMiddleware::handle();

$method = $_SERVER['REQUEST_METHOD'];
$currentUserId = Session::get('user_id');
$db = Database::getInstance();

switch ($method) {
    case 'GET':
        $entityType = $_GET['entity_type'] ?? 'task';
        $entityId = $_GET['entity_id'] ?? null;
        
        if (!$entityId) {
            throw new Exception('entity_id is required');
        }
        
        // Lấy tất cả comments
        $allComments = $db->fetchAll(
            "SELECT c.*, u.full_name, u.avatar_url
             FROM comments c
             JOIN users u ON c.created_by = u.id
             WHERE c.entity_type = ? AND c.entity_id = ?
             ORDER BY c.created_at DESC",
            [$entityType, $entityId]
        );
        
        // Build comment tree (nested replies)
        $commentsById = [];
        foreach ($allComments as &$comment) {
            $comment['time_ago'] = timeAgo($comment['created_at']);
            $comment['replies'] = [];
            $comment['depth'] = 0;
            $commentsById[$comment['id']] = &$comment;
        }
        
        // Tính độ sâu và build tree
        function buildCommentTree(&$commentsById, $parentId = null) {
            $result = [];
            foreach ($commentsById as &$comment) {
                if (($comment['parent_id'] ?? null) === $parentId) {
                    $comment['replies'] = buildCommentTree($commentsById, $comment['id']);
                    usort($comment['replies'], fn($a, $b) => 
                        strtotime($a['created_at']) - strtotime($b['created_at']));
                    $result[] = $comment;
                }
            }
            return $result;
        }
        
        $rootComments = buildCommentTree($commentsById, null);
        usort($rootComments, fn($a, $b) => 
            strtotime($b['created_at']) - strtotime($a['created_at']));
        
        echo json_encode(['success' => true, 'data' => $rootComments]);
        break;
        
    case 'POST':
        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        
        $entityType = $input['entity_type'] ?? 'task';
        $entityId = $input['entity_id'] ?? null;
        $content = trim($input['content'] ?? '');
        $parentId = $input['parent_id'] ?? null;
        
        if (!$entityId || empty($content)) {
            throw new Exception('entity_id and content are required');
        }
        
        // Validate parent comment nếu là reply
        if ($parentId) {
            $parentComment = $db->fetchOne(
                "SELECT id FROM comments WHERE id = ?", 
                [$parentId]
            );
            if (!$parentComment) {
                throw new Exception('Parent comment not found');
            }
        }
        
        $commentId = generateUUID();
        $db->insert('comments', [
            'id' => $commentId,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'content' => $content,
            'parent_id' => $parentId,
            'created_by' => $currentUserId,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        
        $comment = $db->fetchOne(
            "SELECT c.*, u.full_name, u.avatar_url
             FROM comments c
             JOIN users u ON c.created_by = u.id
             WHERE c.id = ?",
            [$commentId]
        );
        $comment['time_ago'] = 'Vừa xong';
        $comment['replies'] = [];
        
        echo json_encode(['success' => true, 'data' => $comment]);
        break;
        
    case 'PUT':
        $input = json_decode(file_get_contents('php://input'), true);
        $commentId = $input['comment_id'] ?? null;
        $content = trim($input['content'] ?? '');
        
        if (!$commentId || empty($content)) {
            throw new Exception('comment_id and content are required');
        }
        
        $comment = $db->fetchOne("SELECT * FROM comments WHERE id = ?", [$commentId]);
        if (!$comment || $comment['created_by'] !== $currentUserId) {
            throw new Exception('Comment not found or permission denied');
        }
        
        $db->update('comments', ['content' => $content], 'id = ?', [$commentId]);
        echo json_encode(['success' => true, 'message' => 'Comment updated']);
        break;
        
    case 'DELETE':
        $commentId = $_GET['comment_id'] ?? null;
        
        if (!$commentId) {
            throw new Exception('comment_id is required');
        }
        
        $comment = $db->fetchOne("SELECT * FROM comments WHERE id = ?", [$commentId]);
        if (!$comment || $comment['created_by'] !== $currentUserId) {
            throw new Exception('Comment not found or permission denied');
        }
        
        // Xóa comment và replies (CASCADE)
        $db->delete('comments', 'id = ?', [$commentId]);
        echo json_encode(['success' => true, 'message' => 'Comment deleted']);
        break;
}
```

### 3.6.4. Danh sách API Endpoints

| Endpoint | Method | Mô tả | Auth |
|----------|--------|-------|------|
| `/api/create-project.php` | POST | Tạo dự án mới | ✅ |
| `/api/update-project.php` | POST | Cập nhật dự án | ✅ |
| `/api/create-task.php` | POST | Tạo công việc | ✅ |
| `/api/update-task.php` | POST/DELETE | Cập nhật/xóa task | ✅ |
| `/api/comments.php` | GET/POST/PUT/DELETE | CRUD comments | ✅ |
| `/api/checklist.php` | POST/PUT/DELETE | Quản lý checklist | ✅ |
| `/api/upload-document.php` | POST | Upload tài liệu | ✅ |
| `/api/calendar.php` | GET/POST/PUT/DELETE | Quản lý sự kiện | ✅ |
| `/api/notifications.php` | GET/PUT | Quản lý thông báo | ✅ |
| `/api/search.php` | GET | Tìm kiếm toàn cục | ✅ |
| `/api/reports.php` | GET | Lấy báo cáo | ✅ |
| `/api/project-members.php` | GET/POST/DELETE | Quản lý thành viên | ✅ |
| `/api/admin-users.php` | GET/POST/PUT/DELETE | Quản lý users (Admin) | ✅ |
| `/api/admin-settings.php` | GET/POST | Cài đặt hệ thống | ✅ |


---

## 3.7. Hiện Thực Cơ Sở Dữ Liệu

### 3.7.1. Sơ đồ ERD chi tiết

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                              TASKFLOW DATABASE                               │
│                           16 Tables - MySQL 8.0                              │
└─────────────────────────────────────────────────────────────────────────────┘

┌─────────────────┐       ┌─────────────────┐       ┌─────────────────┐
│     USERS       │       │    PROJECTS     │       │     TASKS       │
├─────────────────┤       ├─────────────────┤       ├─────────────────┤
│ id (PK, UUID)   │◄──┐   │ id (PK, UUID)   │◄──┐   │ id (PK, UUID)   │
│ email (UNIQUE)  │   │   │ name            │   │   │ title           │
│ password_hash   │   │   │ description     │   │   │ description     │
│ full_name       │   │   │ color           │   │   │ status          │
│ avatar_url      │   │   │ icon            │   │   │ priority        │
│ role (ENUM)     │   │   │ status (ENUM)   │   │   │ position        │
│ department      │   │   │ priority (ENUM) │   │   │ due_date        │
│ position        │   │   │ progress        │   │   │ completed_at    │
│ phone           │   │   │ start_date      │   │   │ estimated_hours │
│ is_active       │   │   │ end_date        │   │   │ actual_hours    │
│ last_login_at   │   │   │ budget          │   │   │ project_id (FK) │──┐
│ created_at      │   │   │ created_by (FK) │───┘   │ created_by (FK) │──┤
│ updated_at      │   │   │ created_at      │       │ created_at      │  │
└────────┬────────┘   │   │ updated_at      │       │ updated_at      │  │
         │            │   └────────┬────────┘       └────────┬────────┘  │
         │            │            │                         │           │
         │            │            │                         │           │
         ▼            │            ▼                         ▼           │
┌─────────────────────┴───┐  ┌─────────────────┐  ┌─────────────────────┴─┐
│   PROJECT_MEMBERS       │  │   DOCUMENTS     │  │   TASK_ASSIGNEES      │
├─────────────────────────┤  ├─────────────────┤  ├───────────────────────┤
│ project_id (PK, FK)     │  │ id (PK, UUID)   │  │ task_id (PK, FK)      │
│ user_id (PK, FK)        │  │ name            │  │ user_id (PK, FK)      │
│ role (ENUM)             │  │ description     │  │ assigned_by (FK)      │
│ joined_at               │  │ type (ENUM)     │  │ assigned_at           │
└─────────────────────────┘  │ mime_type       │  └───────────────────────┘
                             │ file_size       │
                             │ file_path       │  ┌─────────────────────────┐
                             │ parent_id (FK)  │  │   TASK_CHECKLISTS      │
                             │ project_id (FK) │  ├─────────────────────────┤
                             │ is_starred      │  │ id (PK, UUID)          │
                             │ download_count  │  │ task_id (FK)           │
                             │ uploaded_by(FK) │  │ title                  │
                             │ created_at      │  │ is_completed           │
                             │ updated_at      │  │ position               │
                             └─────────────────┘  │ completed_at           │
                                                  │ completed_by (FK)      │
┌─────────────────┐       ┌─────────────────┐     │ created_at             │
│    COMMENTS     │       │  NOTIFICATIONS  │     └─────────────────────────┘
├─────────────────┤       ├─────────────────┤
│ id (PK, UUID)   │       │ id (PK, UUID)   │     ┌─────────────────────────┐
│ entity_type     │       │ user_id (FK)    │     │   CALENDAR_EVENTS      │
│ entity_id       │       │ type            │     ├─────────────────────────┤
│ content         │       │ title           │     │ id (PK, UUID)          │
│ parent_id (FK)  │◄──┐   │ message         │     │ title                  │
│ created_by (FK) │   │   │ data (JSON)     │     │ description            │
│ created_at      │   │   │ is_read         │     │ type (ENUM)            │
│ updated_at      │   │   │ read_at         │     │ color                  │
└────────┬────────┘   │   │ created_at      │     │ start_time             │
         │            │   └─────────────────┘     │ end_time               │
         └────────────┘                           │ is_all_day             │
                                                  │ location               │
┌─────────────────┐       ┌─────────────────┐     │ project_id (FK)        │
│  ACTIVITY_LOGS  │       │  USER_SETTINGS  │     │ task_id (FK)           │
├─────────────────┤       ├─────────────────┤     │ created_by (FK)        │
│ id (PK, UUID)   │       │ user_id (PK,FK) │     │ created_at             │
│ user_id (FK)    │       │ theme (ENUM)    │     │ updated_at             │
│ entity_type     │       │ language        │     └─────────────────────────┘
│ entity_id       │       │ timezone        │
│ action          │       │ notification_*  │     ┌─────────────────────────┐
│ old_values(JSON)│       │ updated_at      │     │   EVENT_ATTENDEES      │
│ new_values(JSON)│       └─────────────────┘     ├─────────────────────────┤
│ ip_address      │                               │ event_id (PK, FK)      │
│ user_agent      │       ┌─────────────────┐     │ user_id (PK, FK)       │
│ created_at      │       │     LABELS      │     │ status (ENUM)          │
└─────────────────┘       ├─────────────────┤     │ responded_at           │
                          │ id (PK, UUID)   │     └─────────────────────────┘
                          │ name            │
                          │ color           │     ┌─────────────────────────┐
                          │ description     │     │   DOCUMENT_SHARES      │
                          │ created_at      │     ├─────────────────────────┤
                          └─────────────────┘     │ document_id (PK, FK)   │
                                                  │ user_id (PK, FK)       │
┌─────────────────┐                               │ permission (ENUM)      │
│   TASK_LABELS   │                               │ shared_at              │
├─────────────────┤                               │ shared_by (FK)         │
│ task_id (PK,FK) │                               └─────────────────────────┘
│ label_id (PK,FK)│
│ created_at      │
└─────────────────┘
```

**Hình 3.3: Sơ đồ ERD đầy đủ của TaskFlow**

### 3.7.2. Chi tiết các bảng chính

**Bảng USERS:**
```sql
CREATE TABLE `users` (
    `id` VARCHAR(36) PRIMARY KEY DEFAULT (UUID()),
    `email` VARCHAR(255) NOT NULL UNIQUE,
    `password_hash` VARCHAR(255) NOT NULL,
    `full_name` VARCHAR(100) NOT NULL,
    `avatar_url` VARCHAR(500) NULL,
    `role` ENUM('admin', 'manager', 'member', 'guest') NOT NULL DEFAULT 'member',
    `department` VARCHAR(100) NULL,
    `position` VARCHAR(100) NULL,
    `phone` VARCHAR(20) NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `email_verified_at` DATETIME NULL,
    `remember_token` VARCHAR(64) NULL,
    `remember_token_expiry` DATETIME NULL,
    `last_login_at` DATETIME NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX `idx_users_email` (`email`),
    INDEX `idx_users_role` (`role`),
    INDEX `idx_users_is_active` (`is_active`),
    INDEX `idx_users_department` (`department`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Bảng TASKS:**
```sql
CREATE TABLE `tasks` (
    `id` VARCHAR(36) PRIMARY KEY DEFAULT (UUID()),
    `project_id` VARCHAR(36) NULL,
    `title` VARCHAR(500) NOT NULL,
    `description` TEXT NULL,
    `status` ENUM('backlog', 'todo', 'in_progress', 'in_review', 'done') NOT NULL DEFAULT 'todo',
    `priority` ENUM('low', 'medium', 'high', 'urgent') NOT NULL DEFAULT 'medium',
    `position` INT UNSIGNED NOT NULL DEFAULT 0,
    `start_date` DATE NULL,
    `due_date` DATE NULL,
    `completed_at` DATETIME NULL,
    `estimated_hours` DECIMAL(6, 2) NULL,
    `actual_hours` DECIMAL(6, 2) NULL,
    `created_by` VARCHAR(36) NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    CONSTRAINT `fk_tasks_project` FOREIGN KEY (`project_id`) 
        REFERENCES `projects`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_tasks_created_by` FOREIGN KEY (`created_by`) 
        REFERENCES `users`(`id`) ON DELETE SET NULL,
    
    INDEX `idx_tasks_project` (`project_id`),
    INDEX `idx_tasks_status` (`status`),
    INDEX `idx_tasks_priority` (`priority`),
    INDEX `idx_tasks_due_date` (`due_date`),
    INDEX `idx_tasks_position` (`position`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Bảng COMMENTS (hỗ trợ nested replies):**
```sql
CREATE TABLE `comments` (
    `id` VARCHAR(36) PRIMARY KEY DEFAULT (UUID()),
    `entity_type` ENUM('task', 'document', 'project') NOT NULL DEFAULT 'task',
    `entity_id` VARCHAR(36) NOT NULL,
    `content` TEXT NOT NULL,
    `parent_id` VARCHAR(36) NULL,  -- Self-referencing cho nested replies
    `created_by` VARCHAR(36) NOT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    CONSTRAINT `fk_comments_parent` FOREIGN KEY (`parent_id`) 
        REFERENCES `comments`(`id`) ON DELETE CASCADE,  -- Xóa parent sẽ xóa replies
    CONSTRAINT `fk_comments_user` FOREIGN KEY (`created_by`) 
        REFERENCES `users`(`id`) ON DELETE CASCADE,
    
    INDEX `idx_comments_entity` (`entity_type`, `entity_id`),
    INDEX `idx_comments_parent` (`parent_id`),
    INDEX `idx_comments_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 3.7.3. Indexes và Optimization

| Bảng | Index | Columns | Mục đích |
|------|-------|---------|----------|
| users | idx_users_email | email | Tìm kiếm đăng nhập |
| users | idx_users_role | role | Lọc theo vai trò |
| projects | idx_projects_status | status | Lọc theo trạng thái |
| projects | idx_projects_dates | start_date, end_date | Lọc theo thời gian |
| tasks | idx_tasks_project | project_id | JOIN với projects |
| tasks | idx_tasks_status | status | Kanban board |
| tasks | idx_tasks_due_date | due_date | Lọc deadline |
| comments | idx_comments_entity | entity_type, entity_id | Lấy comments theo entity |
| comments | idx_comments_parent | parent_id | Build comment tree |
| activity_logs | idx_activity_created_at | created_at | Sắp xếp theo thời gian |


---

## 3.8. Hiện Thực Giao Diện Người Dùng

### 3.8.1. Layout System

TaskFlow sử dụng hệ thống layout với 3 template chính:

```
┌─────────────────────────────────────────────────────────────────────────┐
│                           MAIN LAYOUT                                    │
│  ┌─────────────────────────────────────────────────────────────────┐    │
│  │                         HEADER                                   │    │
│  │  Logo | Search | Notifications | User Menu                       │    │
│  └─────────────────────────────────────────────────────────────────┘    │
│  ┌──────────┐  ┌───────────────────────────────────────────────────┐    │
│  │          │  │                                                   │    │
│  │ SIDEBAR  │  │                    CONTENT                        │    │
│  │          │  │                                                   │    │
│  │ • Home   │  │  <?= View::yield('content') ?>                    │    │
│  │ • Tasks  │  │                                                   │    │
│  │ • Proj.  │  │                                                   │    │
│  │ • Docs   │  │                                                   │    │
│  │ • Cal.   │  │                                                   │    │
│  │ • Team   │  │                                                   │    │
│  │          │  │                                                   │    │
│  └──────────┘  └───────────────────────────────────────────────────┘    │
└─────────────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────────────┐
│                           ADMIN LAYOUT                                   │
│  ┌─────────────────────────────────────────────────────────────────┐    │
│  │                      ADMIN HEADER                                │    │
│  │  Admin Panel | System Status | Admin Menu                        │    │
│  └─────────────────────────────────────────────────────────────────┘    │
│  ┌──────────┐  ┌───────────────────────────────────────────────────┐    │
│  │ ADMIN    │  │                                                   │    │
│  │ SIDEBAR  │  │              ADMIN CONTENT                        │    │
│  │          │  │                                                   │    │
│  │ • Dash   │  │                                                   │    │
│  │ • Users  │  │                                                   │    │
│  │ • Proj.  │  │                                                   │    │
│  │ • Tasks  │  │                                                   │    │
│  │ • Logs   │  │                                                   │    │
│  │ • Backup │  │                                                   │    │
│  │ • Config │  │                                                   │    │
│  └──────────┘  └───────────────────────────────────────────────────┘    │
└─────────────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────────────┐
│                           GUEST LAYOUT                                   │
│  ┌─────────────────────────────────────────────────────────────────┐    │
│  │                                                                  │    │
│  │                         CENTERED CONTENT                         │    │
│  │                                                                  │    │
│  │                    ┌─────────────────────┐                       │    │
│  │                    │                     │                       │    │
│  │                    │    LOGIN / REGISTER │                       │    │
│  │                    │        FORM         │                       │    │
│  │                    │                     │                       │    │
│  │                    └─────────────────────┘                       │    │
│  │                                                                  │    │
│  └─────────────────────────────────────────────────────────────────┘    │
└─────────────────────────────────────────────────────────────────────────┘
```

**Hình 3.4: Các layout template của TaskFlow**

### 3.8.2. Responsive Design với Tailwind CSS

```html
<!-- Grid responsive cho danh sách dự án -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
    <?php foreach ($projects as $project): ?>
    <div class="bg-white rounded-xl border border-gray-200 p-6 hover:shadow-lg transition-shadow">
        <div class="flex items-center gap-3 mb-4">
            <div class="h-10 w-10 rounded-lg flex items-center justify-center"
                 style="background-color: <?= $project['color'] ?>20">
                <i data-lucide="folder" class="h-5 w-5" style="color: <?= $project['color'] ?>"></i>
            </div>
            <div>
                <h3 class="font-semibold text-gray-900"><?= View::e($project['name']) ?></h3>
                <p class="text-sm text-gray-500"><?= $project['task_count'] ?> công việc</p>
            </div>
        </div>
        
        <!-- Progress bar -->
        <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
            <div class="h-full rounded-full transition-all" 
                 style="width: <?= $project['progress'] ?>%; background-color: <?= $project['color'] ?>">
            </div>
        </div>
        <p class="text-xs text-gray-500 mt-2"><?= $project['progress'] ?>% hoàn thành</p>
    </div>
    <?php endforeach; ?>
</div>

<!-- Sidebar responsive -->
<aside class="fixed inset-y-0 left-0 z-40 w-64 bg-white border-r border-gray-200 
              transform -translate-x-full lg:translate-x-0 lg:static transition-transform">
    <!-- Sidebar content -->
</aside>

<!-- Mobile menu button -->
<button class="lg:hidden p-2 rounded-lg hover:bg-gray-100" onclick="toggleSidebar()">
    <i data-lucide="menu" class="h-6 w-6"></i>
</button>
```

### 3.8.3. Kanban Board Implementation

```html
<!-- Kanban Board -->
<div class="flex gap-6 overflow-x-auto pb-4">
    <?php 
    $statuses = [
        'backlog' => ['name' => 'Chờ xử lý', 'color' => 'gray'],
        'todo' => ['name' => 'Cần làm', 'color' => 'blue'],
        'in_progress' => ['name' => 'Đang làm', 'color' => 'yellow'],
        'in_review' => ['name' => 'Đang review', 'color' => 'purple'],
        'done' => ['name' => 'Hoàn thành', 'color' => 'green'],
    ];
    ?>
    
    <?php foreach ($statuses as $status => $info): ?>
    <div class="kanban-column flex-shrink-0 w-80 bg-gray-50 rounded-xl p-4"
         data-status="<?= $status ?>"
         ondrop="handleDrop(event)" 
         ondragover="handleDragOver(event)">
        
        <!-- Column Header -->
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-2">
                <span class="h-3 w-3 rounded-full bg-<?= $info['color'] ?>-500"></span>
                <h3 class="font-semibold text-gray-900"><?= $info['name'] ?></h3>
                <span class="text-sm text-gray-500">
                    (<?= count(array_filter($tasks, fn($t) => $t['status'] === $status)) ?>)
                </span>
            </div>
            <button onclick="openCreateTaskModal('<?= $status ?>')" 
                    class="p-1 hover:bg-gray-200 rounded">
                <i data-lucide="plus" class="h-4 w-4"></i>
            </button>
        </div>
        
        <!-- Task Cards -->
        <div class="space-y-3 min-h-[200px]">
            <?php foreach ($tasks as $task): ?>
            <?php if ($task['status'] === $status): ?>
            <div class="task-card bg-white rounded-lg border border-gray-200 p-4 cursor-move
                        hover:shadow-md transition-shadow"
                 draggable="true"
                 data-task-id="<?= $task['id'] ?>"
                 ondragstart="handleDragStart(event)">
                
                <!-- Priority badge -->
                <div class="flex items-center gap-2 mb-2">
                    <?php
                    $priorityColors = [
                        'low' => 'bg-gray-100 text-gray-600',
                        'medium' => 'bg-blue-100 text-blue-600',
                        'high' => 'bg-orange-100 text-orange-600',
                        'urgent' => 'bg-red-100 text-red-600',
                    ];
                    ?>
                    <span class="text-xs px-2 py-0.5 rounded-full <?= $priorityColors[$task['priority']] ?>">
                        <?= ucfirst($task['priority']) ?>
                    </span>
                </div>
                
                <!-- Task title -->
                <h4 class="font-medium text-gray-900 mb-2">
                    <a href="/php/task-detail.php?id=<?= $task['id'] ?>" class="hover:text-primary">
                        <?= View::e($task['title']) ?>
                    </a>
                </h4>
                
                <!-- Task meta -->
                <div class="flex items-center justify-between text-sm text-gray-500">
                    <?php if ($task['due_date']): ?>
                    <span class="flex items-center gap-1">
                        <i data-lucide="calendar" class="h-3 w-3"></i>
                        <?= date('d/m', strtotime($task['due_date'])) ?>
                    </span>
                    <?php endif; ?>
                    
                    <?php if (!empty($task['assignees'])): ?>
                    <div class="flex -space-x-2">
                        <?php foreach (array_slice($task['assignees'], 0, 3) as $assignee): ?>
                        <div class="h-6 w-6 rounded-full bg-gray-200 border-2 border-white 
                                    flex items-center justify-center text-xs">
                            <?= strtoupper(substr($assignee['full_name'], 0, 1)) ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<script>
// Drag & Drop handlers
function handleDragStart(e) {
    e.dataTransfer.setData('text/plain', e.target.dataset.taskId);
    e.target.classList.add('opacity-50');
}

function handleDragOver(e) {
    e.preventDefault();
    e.currentTarget.classList.add('bg-gray-100');
}

function handleDrop(e) {
    e.preventDefault();
    const taskId = e.dataTransfer.getData('text/plain');
    const newStatus = e.currentTarget.dataset.status;
    
    // Update task status via API
    fetch('/php/api/update-task.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ task_id: taskId, status: newStatus })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    });
}
</script>
```

### 3.8.4. Dark Mode Support

```html
<!-- HTML root với dark mode class -->
<html class="dark">
<body class="bg-white dark:bg-gray-900 text-gray-900 dark:text-white">
    
    <!-- Component với dark mode -->
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 
                rounded-xl p-6 shadow-sm">
        <h3 class="text-gray-900 dark:text-white font-semibold">Title</h3>
        <p class="text-gray-600 dark:text-gray-400">Description</p>
    </div>
    
</body>
</html>

<script>
// Dark mode toggle
function toggleDarkMode() {
    document.documentElement.classList.toggle('dark');
    const isDark = document.documentElement.classList.contains('dark');
    localStorage.setItem('theme', isDark ? 'dark' : 'light');
    
    // Sync với server
    fetch('/php/api/update-settings.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ theme: isDark ? 'dark' : 'light' })
    });
}

// Load saved theme
document.addEventListener('DOMContentLoaded', () => {
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme === 'dark') {
        document.documentElement.classList.add('dark');
    }
});
</script>
```


---

## 3.9. Hiện Thực Bảo Mật

### 3.9.1. Chống SQL Injection

TaskFlow sử dụng Prepared Statements cho tất cả các truy vấn database:

```php
// ❌ KHÔNG AN TOÀN - Dễ bị SQL Injection
$sql = "SELECT * FROM users WHERE email = '$email'";
$result = $pdo->query($sql);

// ✅ AN TOÀN - Prepared Statement
$sql = "SELECT * FROM users WHERE email = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$email]);
$result = $stmt->fetch();

// ✅ AN TOÀN - Named parameters
$sql = "SELECT * FROM users WHERE email = :email AND role = :role";
$stmt = $pdo->prepare($sql);
$stmt->execute(['email' => $email, 'role' => $role]);
```

### 3.9.2. Chống XSS (Cross-Site Scripting)

```php
// Output escaping trong View
class View {
    public static function e(mixed $value): string
    {
        return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
    }
}

// Sử dụng trong template
<h1><?= View::e($userInput) ?></h1>
<p><?= View::e($comment['content']) ?></p>

// Escape trong JavaScript
<script>
    const data = <?= json_encode($data, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
</script>
```

### 3.9.3. Chống CSRF (Cross-Site Request Forgery)

```php
// includes/csrf.php
function generateCsrfToken(): string {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCsrfToken(string $token): bool {
    return isset($_SESSION['csrf_token']) && 
           hash_equals($_SESSION['csrf_token'], $token);
}

// Trong form
<form method="POST" action="/api/create-project.php">
    <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
    <!-- form fields -->
</form>

// Trong API
if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
    http_response_code(403);
    echo json_encode(['error' => 'Invalid CSRF token']);
    exit;
}
```

### 3.9.4. Password Security

```php
// Mã hóa password với bcrypt
$passwordHash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

// Verify password
if (password_verify($inputPassword, $storedHash)) {
    // Đăng nhập thành công
    
    // Kiểm tra cần rehash không (nếu cost thay đổi)
    if (password_needs_rehash($storedHash, PASSWORD_BCRYPT, ['cost' => 12])) {
        $newHash = password_hash($inputPassword, PASSWORD_BCRYPT, ['cost' => 12]);
        // Update hash trong database
    }
}
```

### 3.9.5. Rate Limiting

```php
// core/RateLimiter.php
class RateLimiter
{
    private string $storageDir;
    
    public function __construct()
    {
        $this->storageDir = BASE_PATH . '/storage/rate_limits/';
    }
    
    public function check(string $key, int $maxAttempts = 60, int $decayMinutes = 1): bool
    {
        $file = $this->storageDir . md5($key) . '.json';
        
        $data = file_exists($file) ? json_decode(file_get_contents($file), true) : [];
        
        // Xóa attempts cũ
        $cutoff = time() - ($decayMinutes * 60);
        $data['attempts'] = array_filter(
            $data['attempts'] ?? [], 
            fn($t) => $t > $cutoff
        );
        
        // Kiểm tra limit
        if (count($data['attempts']) >= $maxAttempts) {
            return false;
        }
        
        // Thêm attempt mới
        $data['attempts'][] = time();
        file_put_contents($file, json_encode($data));
        
        return true;
    }
    
    public function tooManyAttempts(string $key, int $maxAttempts = 60): bool
    {
        return !$this->check($key, $maxAttempts);
    }
}

// Sử dụng trong login
$rateLimiter = new RateLimiter();
$key = 'login:' . $_SERVER['REMOTE_ADDR'];

if ($rateLimiter->tooManyAttempts($key, 5)) {
    http_response_code(429);
    echo json_encode(['error' => 'Quá nhiều lần thử. Vui lòng đợi 1 phút.']);
    exit;
}
```

### 3.9.6. File Upload Security

```php
// api/upload-document.php
$allowedTypes = [
    'image/jpeg', 'image/png', 'image/gif', 'image/webp',
    'application/pdf',
    'application/msword',
    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    'application/vnd.ms-excel',
    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    'text/plain', 'text/csv',
];

$maxSize = 10 * 1024 * 1024; // 10MB

// Validate file
$file = $_FILES['file'];

// Kiểm tra MIME type thực sự (không tin vào extension)
$finfo = new finfo(FILEINFO_MIME_TYPE);
$mimeType = $finfo->file($file['tmp_name']);

if (!in_array($mimeType, $allowedTypes)) {
    throw new Exception('Loại file không được phép');
}

if ($file['size'] > $maxSize) {
    throw new Exception('File quá lớn (tối đa 10MB)');
}

// Tạo tên file an toàn
$extension = pathinfo($file['name'], PATHINFO_EXTENSION);
$safeName = bin2hex(random_bytes(16)) . '.' . $extension;

// Lưu file ngoài webroot hoặc với .htaccess bảo vệ
$uploadDir = BASE_PATH . '/uploads/documents/';
$filePath = $uploadDir . $safeName;

move_uploaded_file($file['tmp_name'], $filePath);
```

### 3.9.7. Bảng tổng hợp biện pháp bảo mật

| Mối đe dọa | Biện pháp | Implementation |
|------------|-----------|----------------|
| SQL Injection | Prepared Statements | PDO với placeholders |
| XSS | Output Escaping | `htmlspecialchars()`, `View::e()` |
| CSRF | Token Verification | Hidden field + session token |
| Brute Force | Rate Limiting | IP-based throttling |
| Session Hijacking | Secure Cookies | httponly, secure, samesite |
| Session Fixation | Session Regeneration | `session_regenerate_id()` |
| Password Cracking | Bcrypt Hashing | `password_hash()` với cost 12 |
| File Upload Attack | MIME Validation | `finfo_file()`, whitelist |
| Directory Traversal | Path Sanitization | `basename()`, whitelist |


---

## 3.10. Hiện Thực Các Tính Năng Nâng Cao

### 3.10.1. Hệ thống thông báo Real-time

```php
// api/notifications.php
class NotificationService
{
    private Database $db;
    
    public function create(string $userId, string $type, string $title, ?string $message = null, ?array $data = null): void
    {
        $this->db->insert('notifications', [
            'id' => $this->generateUUID(),
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => $data ? json_encode($data) : null,
            'is_read' => 0,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }
    
    public function getUnread(string $userId, int $limit = 10): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM notifications 
             WHERE user_id = ? AND is_read = 0 
             ORDER BY created_at DESC 
             LIMIT ?",
            [$userId, $limit]
        );
    }
    
    public function markAsRead(string $notificationId): void
    {
        $this->db->update('notifications', [
            'is_read' => 1,
            'read_at' => date('Y-m-d H:i:s')
        ], 'id = ?', [$notificationId]);
    }
    
    public function markAllAsRead(string $userId): void
    {
        $this->db->update('notifications', [
            'is_read' => 1,
            'read_at' => date('Y-m-d H:i:s')
        ], 'user_id = ? AND is_read = 0', [$userId]);
    }
}

// Tự động tạo notification khi có sự kiện
// Ví dụ: Khi được giao task
function onTaskAssigned(string $taskId, string $assigneeId, string $assignedBy): void
{
    $task = (new Task())->find($taskId);
    $assigner = (new User())->find($assignedBy);
    
    $notification = new NotificationService();
    $notification->create(
        $assigneeId,
        'task_assigned',
        'Bạn được giao công việc mới',
        "{$assigner['full_name']} đã giao cho bạn: {$task['title']}",
        ['task_id' => $taskId, 'assigner_id' => $assignedBy]
    );
}
```

### 3.10.2. Tìm kiếm toàn cục

```php
// api/search.php
$query = trim($_GET['q'] ?? '');
$type = $_GET['type'] ?? 'all';

if (strlen($query) < 2) {
    echo json_encode(['success' => true, 'data' => []]);
    exit;
}

$results = [];
$searchTerm = "%{$query}%";

// Tìm trong Projects
if ($type === 'all' || $type === 'projects') {
    $projects = $db->fetchAll(
        "SELECT id, name, description, color, 'project' as type
         FROM projects 
         WHERE name LIKE ? OR description LIKE ?
         ORDER BY updated_at DESC
         LIMIT 5",
        [$searchTerm, $searchTerm]
    );
    $results = array_merge($results, $projects);
}

// Tìm trong Tasks
if ($type === 'all' || $type === 'tasks') {
    $tasks = $db->fetchAll(
        "SELECT t.id, t.title as name, t.description, p.color, 'task' as type
         FROM tasks t
         LEFT JOIN projects p ON t.project_id = p.id
         WHERE t.title LIKE ? OR t.description LIKE ?
         ORDER BY t.updated_at DESC
         LIMIT 5",
        [$searchTerm, $searchTerm]
    );
    $results = array_merge($results, $tasks);
}

// Tìm trong Documents
if ($type === 'all' || $type === 'documents') {
    $documents = $db->fetchAll(
        "SELECT id, name, description, '#6366f1' as color, 'document' as type
         FROM documents 
         WHERE name LIKE ? OR description LIKE ?
         ORDER BY updated_at DESC
         LIMIT 5",
        [$searchTerm, $searchTerm]
    );
    $results = array_merge($results, $documents);
}

// Tìm trong Users (chỉ admin/manager)
if (($type === 'all' || $type === 'users') && Permission::isManager($userRole)) {
    $users = $db->fetchAll(
        "SELECT id, full_name as name, email as description, '#10b981' as color, 'user' as type
         FROM users 
         WHERE full_name LIKE ? OR email LIKE ?
         ORDER BY full_name
         LIMIT 5",
        [$searchTerm, $searchTerm]
    );
    $results = array_merge($results, $users);
}

echo json_encode(['success' => true, 'data' => $results]);
```

### 3.10.3. Báo cáo và thống kê

```php
// api/reports.php
class ReportService
{
    private Database $db;
    
    public function getDashboardStats(?string $userId = null): array
    {
        // Thống kê tổng quan
        $stats = [
            'projects' => $this->getProjectStats($userId),
            'tasks' => $this->getTaskStats($userId),
            'team' => $this->getTeamStats(),
            'activity' => $this->getRecentActivity($userId),
        ];
        
        return $stats;
    }
    
    public function getProjectStats(?string $userId = null): array
    {
        $where = $userId ? "WHERE p.created_by = ? OR pm.user_id = ?" : "";
        $params = $userId ? [$userId, $userId] : [];
        
        return $this->db->fetchOne(
            "SELECT 
                COUNT(DISTINCT p.id) as total,
                SUM(CASE WHEN p.status = 'active' THEN 1 ELSE 0 END) as active,
                SUM(CASE WHEN p.status = 'completed' THEN 1 ELSE 0 END) as completed,
                AVG(p.progress) as avg_progress
             FROM projects p
             LEFT JOIN project_members pm ON p.id = pm.project_id
             {$where}",
            $params
        );
    }
    
    public function getTaskStats(?string $userId = null): array
    {
        $where = $userId ? "WHERE t.created_by = ? OR ta.user_id = ?" : "";
        $params = $userId ? [$userId, $userId] : [];
        
        return $this->db->fetchOne(
            "SELECT 
                COUNT(DISTINCT t.id) as total,
                SUM(CASE WHEN t.status = 'done' THEN 1 ELSE 0 END) as completed,
                SUM(CASE WHEN t.status = 'in_progress' THEN 1 ELSE 0 END) as in_progress,
                SUM(CASE WHEN t.due_date < CURDATE() AND t.status != 'done' THEN 1 ELSE 0 END) as overdue,
                SUM(CASE WHEN t.due_date = CURDATE() THEN 1 ELSE 0 END) as due_today
             FROM tasks t
             LEFT JOIN task_assignees ta ON t.id = ta.task_id
             {$where}",
            $params
        );
    }
    
    public function getProductivityChart(string $period = 'week'): array
    {
        $days = $period === 'month' ? 30 : 7;
        
        $data = $this->db->fetchAll(
            "SELECT 
                DATE(completed_at) as date,
                COUNT(*) as completed
             FROM tasks
             WHERE completed_at >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
               AND status = 'done'
             GROUP BY DATE(completed_at)
             ORDER BY date",
            [$days]
        );
        
        // Fill missing dates
        $result = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-{$i} days"));
            $found = array_filter($data, fn($d) => $d['date'] === $date);
            $result[] = [
                'date' => $date,
                'completed' => $found ? reset($found)['completed'] : 0
            ];
        }
        
        return $result;
    }
}
```

### 3.10.4. Activity Logging

```php
// Ghi log hoạt động
class ActivityLogger
{
    private Database $db;
    
    public function log(
        string $entityType,
        string $entityId,
        string $action,
        ?array $oldValues = null,
        ?array $newValues = null
    ): void {
        $userId = Session::get('user_id');
        
        $this->db->insert('activity_logs', [
            'id' => $this->generateUUID(),
            'user_id' => $userId,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'action' => $action,
            'old_values' => $oldValues ? json_encode($oldValues) : null,
            'new_values' => $newValues ? json_encode($newValues) : null,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }
    
    public function getRecent(int $limit = 20): array
    {
        return $this->db->fetchAll(
            "SELECT al.*, u.full_name, u.avatar_url
             FROM activity_logs al
             LEFT JOIN users u ON al.user_id = u.id
             ORDER BY al.created_at DESC
             LIMIT ?",
            [$limit]
        );
    }
}

// Sử dụng
$logger = new ActivityLogger();

// Khi tạo project
$logger->log('project', $projectId, 'created', null, $projectData);

// Khi cập nhật task
$logger->log('task', $taskId, 'updated', $oldTask, $newTask);

// Khi xóa document
$logger->log('document', $documentId, 'deleted', $documentData, null);
```


---

## 3.11. Kết Quả Hiện Thực

### 3.11.1. Thống kê mã nguồn

| Thành phần | Số file | Số dòng code | Ngôn ngữ |
|------------|---------|--------------|----------|
| Controllers | 10 | ~1,500 | PHP |
| Models | 6 | ~800 | PHP |
| Views | 35+ | ~5,000 | PHP/HTML |
| API Endpoints | 30+ | ~3,000 | PHP |
| Core Framework | 8 | ~1,200 | PHP |
| JavaScript | - | ~2,000 | JS |
| CSS (Tailwind) | - | ~500 | CSS |
| SQL Scripts | 15+ | ~800 | SQL |
| **Tổng cộng** | **100+** | **~15,000** | - |

### 3.11.2. Các tính năng đã hoàn thành

| Module | Tính năng | Trạng thái |
|--------|-----------|------------|
| **Authentication** | Đăng nhập/Đăng xuất | ✅ Hoàn thành |
| | Đăng ký tài khoản | ✅ Hoàn thành |
| | Remember me | ✅ Hoàn thành |
| | Quên mật khẩu | ✅ Hoàn thành |
| **Projects** | CRUD dự án | ✅ Hoàn thành |
| | Quản lý thành viên | ✅ Hoàn thành |
| | Theo dõi tiến độ | ✅ Hoàn thành |
| | Chuyển quyền sở hữu | ✅ Hoàn thành |
| **Tasks** | CRUD công việc | ✅ Hoàn thành |
| | Kanban board | ✅ Hoàn thành |
| | Drag & Drop | ✅ Hoàn thành |
| | Checklist | ✅ Hoàn thành |
| | Giao việc | ✅ Hoàn thành |
| **Comments** | Bình luận | ✅ Hoàn thành |
| | Nested replies | ✅ Hoàn thành |
| | Mention (@user) | ✅ Hoàn thành |
| **Documents** | Upload file | ✅ Hoàn thành |
| | Quản lý thư mục | ✅ Hoàn thành |
| | Chia sẻ tài liệu | ✅ Hoàn thành |
| | Đánh dấu sao | ✅ Hoàn thành |
| **Calendar** | Xem lịch | ✅ Hoàn thành |
| | Tạo sự kiện | ✅ Hoàn thành |
| | Nhắc nhở | ✅ Hoàn thành |
| **Notifications** | Thông báo hệ thống | ✅ Hoàn thành |
| | Đánh dấu đã đọc | ✅ Hoàn thành |
| **Reports** | Dashboard thống kê | ✅ Hoàn thành |
| | Biểu đồ tiến độ | ✅ Hoàn thành |
| | Export báo cáo | ✅ Hoàn thành |
| **Admin** | Quản lý users | ✅ Hoàn thành |
| | Quản lý hệ thống | ✅ Hoàn thành |
| | Activity logs | ✅ Hoàn thành |
| | Backup database | ✅ Hoàn thành |
| **UI/UX** | Responsive design | ✅ Hoàn thành |
| | Dark mode | ✅ Hoàn thành |
| | Tìm kiếm toàn cục | ✅ Hoàn thành |

### 3.11.3. Screenshots giao diện

```
┌─────────────────────────────────────────────────────────────────────────┐
│                         DASHBOARD                                        │
│  ┌─────────────────────────────────────────────────────────────────┐    │
│  │  📊 Thống kê tổng quan                                          │    │
│  │  ┌─────────┐ ┌─────────┐ ┌─────────┐ ┌─────────┐               │    │
│  │  │ 8       │ │ 44      │ │ 22      │ │ 156     │               │    │
│  │  │ Dự án   │ │ Công việc│ │ Thành viên│ │ Tài liệu │               │    │
│  │  └─────────┘ └─────────┘ └─────────┘ └─────────┘               │    │
│  └─────────────────────────────────────────────────────────────────┘    │
│  ┌─────────────────────────────────────────────────────────────────┐    │
│  │  📈 Biểu đồ tiến độ tuần                                        │    │
│  │  ████████████████████████████████████████████████████████████   │    │
│  │  Mon  Tue  Wed  Thu  Fri  Sat  Sun                              │    │
│  └─────────────────────────────────────────────────────────────────┘    │
│  ┌─────────────────────────────────────────────────────────────────┐    │
│  │  📋 Công việc sắp đến hạn                                       │    │
│  │  • Hoàn thành API authentication - Hôm nay                      │    │
│  │  • Review code module payment - Ngày mai                        │    │
│  │  • Deploy staging environment - 3 ngày nữa                      │    │
│  └─────────────────────────────────────────────────────────────────┘    │
└─────────────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────────────┐
│                         KANBAN BOARD                                     │
│  ┌───────────┐ ┌───────────┐ ┌───────────┐ ┌───────────┐ ┌───────────┐ │
│  │ BACKLOG   │ │   TODO    │ │IN PROGRESS│ │ IN REVIEW │ │   DONE    │ │
│  │    (5)    │ │    (8)    │ │    (4)    │ │    (2)    │ │   (25)    │ │
│  ├───────────┤ ├───────────┤ ├───────────┤ ├───────────┤ ├───────────┤ │
│  │ ┌───────┐ │ │ ┌───────┐ │ │ ┌───────┐ │ │ ┌───────┐ │ │ ┌───────┐ │ │
│  │ │Task 1 │ │ │ │Task 6 │ │ │ │Task 14│ │ │ │Task 18│ │ │ │Task 20│ │ │
│  │ │🔴 High│ │ │ │🟡 Med │ │ │ │🟢 Low │ │ │ │🔴 High│ │ │ │✅ Done│ │ │
│  │ └───────┘ │ │ └───────┘ │ │ └───────┘ │ │ └───────┘ │ │ └───────┘ │ │
│  │ ┌───────┐ │ │ ┌───────┐ │ │ ┌───────┐ │ │           │ │ ┌───────┐ │ │
│  │ │Task 2 │ │ │ │Task 7 │ │ │ │Task 15│ │ │           │ │ │Task 21│ │ │
│  │ │🟡 Med │ │ │ │🔴 High│ │ │ │🟡 Med │ │ │           │ │ │✅ Done│ │ │
│  │ └───────┘ │ │ └───────┘ │ │ └───────┘ │ │           │ │ └───────┘ │ │
│  └───────────┘ └───────────┘ └───────────┘ └───────────┘ └───────────┘ │
└─────────────────────────────────────────────────────────────────────────┘
```

**Hình 3.5: Giao diện Dashboard và Kanban Board**

---

## 3.12. Tổng Kết Chương

### 3.12.1. Những gì đã đạt được

Chương này đã trình bày chi tiết quá trình hiện thực hóa hệ thống TaskFlow:

1. **Kiến trúc MVC hoàn chỉnh**: Tổ chức code rõ ràng với Controllers, Models, Views tách biệt
2. **Core Framework**: Database (Singleton), Session, Permission, View, Validator
3. **RESTful API**: 30+ endpoints với chuẩn JSON response
4. **Cơ sở dữ liệu**: 16 bảng với quan hệ đầy đủ, indexes tối ưu
5. **Bảo mật**: SQL Injection, XSS, CSRF, Rate Limiting, Password Hashing
6. **Giao diện**: Responsive, Dark mode, Kanban board, Drag & Drop
7. **Tính năng nâng cao**: Notifications, Search, Reports, Activity Logs

### 3.12.2. Công nghệ sử dụng

| Layer | Công nghệ | Version |
|-------|-----------|---------|
| Backend | PHP | 8.x |
| Database | MySQL | 8.0 |
| Frontend CSS | Tailwind CSS | 3.x |
| Frontend JS | Alpine.js | 3.x |
| Icons | Lucide Icons | Latest |
| Server | Apache/Nginx | - |

### 3.12.3. Bài học kinh nghiệm

1. **Thiết kế trước, code sau**: Việc thiết kế database và API trước giúp quá trình phát triển suôn sẻ hơn
2. **Bảo mật từ đầu**: Áp dụng các biện pháp bảo mật ngay từ đầu thay vì bổ sung sau
3. **Code reusable**: BaseModel, BaseController giúp giảm code trùng lặp đáng kể
4. **Responsive first**: Thiết kế mobile-first giúp giao diện hoạt động tốt trên mọi thiết bị
5. **Documentation**: Viết tài liệu song song với code giúp dễ bảo trì

### 3.12.4. Hướng phát triển tiếp theo

- Real-time notifications với WebSocket
- Mobile app (React Native/Flutter)
- Integration với các dịch vụ bên ngoài (Slack, Email)
- AI-powered task suggestions
- Advanced reporting với charts

---

## Tài Liệu Tham Khảo Chương 3

[1] PHP Documentation. (2024). *PHP Manual*. https://www.php.net/manual/

[2] MySQL Documentation. (2024). *MySQL 8.0 Reference Manual*. https://dev.mysql.com/doc/refman/8.0/en/

[3] Tailwind CSS. (2024). *Tailwind CSS Documentation*. https://tailwindcss.com/docs

[4] Alpine.js. (2024). *Alpine.js Documentation*. https://alpinejs.dev/

[5] OWASP. (2024). *OWASP Cheat Sheet Series*. https://cheatsheetseries.owasp.org/

[6] Fowler, M. (2002). *Patterns of Enterprise Application Architecture*. Addison-Wesley.

