<?php
/**
 * Web-based Database Seeder
 * Truy cập: http://localhost/php/database/seed-web.php
 * 
 * Tạo dữ liệu demo với password hash đúng
 */

// Try new bootstrap first, fallback to old config
if (file_exists(__DIR__ . '/../bootstrap.php')) {
    require_once __DIR__ . '/../bootstrap.php';
    // Create wrapper for old Database class compatibility
    if (!class_exists('Database', false)) {
        class Database {
            private static $instance = null;
            private $coreDb;
            
            private function __construct() {
                $this->coreDb = \Core\Database::getInstance();
            }
            
            public static function getInstance() {
                if (self::$instance === null) {
                    self::$instance = new self();
                }
                return self::$instance;
            }
            
            public function __call($method, $args) {
                return call_user_func_array([$this->coreDb, $method], $args);
            }
        }
    }
} else {
    require_once __DIR__ . '/../includes/config.php';
}

$message = '';
$error = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    try {
        $db = Database::getInstance();
        
        if ($_POST['action'] === 'seed') {
            // Tạo password hash cho các tài khoản demo
            $passwordHash = password_hash('password123', PASSWORD_DEFAULT);
            
            // Xóa dữ liệu cũ
            $db->query("SET FOREIGN_KEY_CHECKS = 0");
            $tables = ['user_settings', 'event_attendees', 'calendar_events', 'activity_logs', 
                       'notifications', 'comments', 'document_shares', 'documents',
                       'task_checklists', 'task_labels', 'task_assignees', 'tasks', 
                       'project_members', 'projects', 'labels', 'users'];
            foreach ($tables as $table) {
                try { $db->query("TRUNCATE TABLE `$table`"); } catch (Exception $e) {}
            }
            $db->query("SET FOREIGN_KEY_CHECKS = 1");
            
            // Insert users với password hash đúng
            $users = [
                ['user-1', 'admin@taskflow.com', 'Nguyễn Văn An', 'admin', 'Quản lý', 'Project Manager'],
                ['user-2', 'manager@taskflow.com', 'Trần Thị Bình', 'manager', 'Kỹ thuật', 'Tech Lead'],
                ['user-3', 'designer@taskflow.com', 'Lê Minh Châu', 'member', 'Thiết kế', 'UI/UX Designer'],
                ['user-4', 'frontend@taskflow.com', 'Phạm Hồng Đào', 'member', 'Kỹ thuật', 'Frontend Developer'],
                ['user-5', 'backend@taskflow.com', 'Hoàng Văn Em', 'member', 'Kỹ thuật', 'Backend Developer'],
            ];
            
            foreach ($users as $u) {
                $db->query(
                    "INSERT INTO users (id, email, password_hash, full_name, role, department, position, is_active) 
                     VALUES (?, ?, ?, ?, ?, ?, ?, 1)",
                    [$u[0], $u[1], $passwordHash, $u[2], $u[3], $u[4], $u[5]]
                );
            }
            
            // Insert labels
            $labels = [
                ['label-1', 'Bug', '#ef4444'],
                ['label-2', 'Feature', '#22c55e'],
                ['label-3', 'Enhancement', '#3b82f6'],
                ['label-4', 'Documentation', '#a855f7'],
                ['label-5', 'Urgent', '#f97316'],
            ];
            foreach ($labels as $l) {
                $db->query("INSERT INTO labels (id, name, color) VALUES (?, ?, ?)", $l);
            }
            
            // Insert projects
            $projects = [
                ['project-1', 'Website E-commerce', 'Phát triển website thương mại điện tử', '#6366f1', 'shopping-cart', 'active', 'high', 65, 'user-1'],
                ['project-2', 'Mobile App', 'Ứng dụng di động iOS và Android', '#22c55e', 'smartphone', 'active', 'high', 40, 'user-1'],
                ['project-3', 'Hệ thống CRM', 'Xây dựng hệ thống quản lý khách hàng', '#f59e0b', 'users', 'planning', 'medium', 15, 'user-1'],
            ];
            foreach ($projects as $p) {
                $db->query(
                    "INSERT INTO projects (id, name, description, color, icon, status, priority, progress, created_by, start_date, end_date) 
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 6 MONTH))",
                    [$p[0], $p[1], $p[2], $p[3], $p[4], $p[5], $p[6], $p[7], $p[8]]
                );
            }
            
            // Insert project members
            $members = [
                ['project-1', 'user-1', 'owner'], ['project-1', 'user-2', 'manager'], 
                ['project-1', 'user-3', 'member'], ['project-1', 'user-4', 'member'], ['project-1', 'user-5', 'member'],
                ['project-2', 'user-1', 'owner'], ['project-2', 'user-2', 'manager'], ['project-2', 'user-4', 'member'],
                ['project-3', 'user-1', 'owner'], ['project-3', 'user-5', 'member'],
            ];
            foreach ($members as $m) {
                $db->query("INSERT INTO project_members (project_id, user_id, role) VALUES (?, ?, ?)", $m);
            }
            
            // Insert tasks
            $tasks = [
                ['task-1', 'project-1', 'Thiết kế trang chi tiết sản phẩm', 'todo', 'high', 7, 'user-1'],
                ['task-2', 'project-1', 'Xây dựng giỏ hàng', 'in_progress', 'high', 5, 'user-2'],
                ['task-3', 'project-1', 'Tích hợp VNPay', 'in_progress', 'urgent', 3, 'user-1'],
                ['task-4', 'project-1', 'Trang đăng ký/đăng nhập', 'in_review', 'high', -2, 'user-2'],
                ['task-5', 'project-1', 'Setup dự án Next.js', 'done', 'high', -10, 'user-2'],
                ['task-6', 'project-2', 'Màn hình Home', 'in_progress', 'high', 5, 'user-1'],
                ['task-7', 'project-2', 'Setup React Native', 'done', 'high', -7, 'user-2'],
            ];
            foreach ($tasks as $t) {
                $db->query(
                    "INSERT INTO tasks (id, project_id, title, status, priority, due_date, created_by, position) 
                     VALUES (?, ?, ?, ?, ?, DATE_ADD(CURDATE(), INTERVAL ? DAY), ?, 0)",
                    [$t[0], $t[1], $t[2], $t[3], $t[4], $t[5], $t[6]]
                );
            }
            
            // Insert task assignees
            $assignees = [
                ['task-1', 'user-3'], ['task-2', 'user-4'], ['task-3', 'user-5'],
                ['task-4', 'user-4'], ['task-5', 'user-2'], ['task-6', 'user-4'], ['task-7', 'user-2'],
            ];
            foreach ($assignees as $a) {
                $db->query("INSERT INTO task_assignees (task_id, user_id, assigned_by) VALUES (?, ?, 'user-1')", $a);
            }
            
            // Insert user settings
            foreach ($users as $u) {
                $db->query("INSERT INTO user_settings (user_id) VALUES (?)", [$u[0]]);
            }
            
            $success = true;
            $message = 'Đã tạo dữ liệu demo thành công!';
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Kiểm tra trạng thái database
$dbStatus = ['connected' => false];
try {
    $db = Database::getInstance();
    $dbStatus['connected'] = true;
    $dbStatus['users'] = $db->fetchColumn("SELECT COUNT(*) FROM users");
    $dbStatus['projects'] = $db->fetchColumn("SELECT COUNT(*) FROM projects");
    $dbStatus['tasks'] = $db->fetchColumn("SELECT COUNT(*) FROM tasks");
} catch (Exception $e) {
    $dbStatus['error'] = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Seeder - TaskFlow</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen py-12">
    <div class="max-w-2xl mx-auto px-4">
        <div class="bg-white rounded-xl shadow-lg p-8">
            <h1 class="text-2xl font-bold text-gray-900 mb-6">TaskFlow Database Seeder</h1>
            
            <?php if ($message): ?>
                <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg">
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg">
                    <strong>Lỗi:</strong> <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>
            
            <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                <h3 class="font-semibold text-gray-900 mb-3">Trạng thái Database</h3>
                <?php if ($dbStatus['connected']): ?>
                    <p class="text-green-600 mb-2">✓ Kết nối thành công</p>
                    <ul class="text-sm text-gray-600 space-y-1">
                        <li>Users: <?= $dbStatus['users'] ?></li>
                        <li>Projects: <?= $dbStatus['projects'] ?></li>
                        <li>Tasks: <?= $dbStatus['tasks'] ?></li>
                    </ul>
                <?php else: ?>
                    <p class="text-red-600">✗ Không thể kết nối</p>
                    <p class="text-sm text-gray-500 mt-2">
                        Hãy import file <code class="bg-gray-200 px-1">database/taskflow2.sql</code> vào phpMyAdmin trước.
                    </p>
                <?php endif; ?>
            </div>
            
            <?php if ($dbStatus['connected']): ?>
                <form method="POST" class="space-y-4">
                    <input type="hidden" name="action" value="seed">
                    <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                        <p class="text-yellow-800 text-sm">
                            <strong>Cảnh báo:</strong> Thao tác này sẽ xóa toàn bộ dữ liệu hiện có và tạo dữ liệu demo mới.
                        </p>
                    </div>
                    <button type="submit" 
                            class="w-full py-3 px-4 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700"
                            onclick="return confirm('Bạn có chắc muốn xóa dữ liệu cũ và tạo dữ liệu demo mới?')">
                        Tạo dữ liệu Demo
                    </button>
                </form>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="mt-6 p-4 bg-blue-50 rounded-lg">
                    <h3 class="font-semibold text-blue-900 mb-2">Tài khoản Demo</h3>
                    <p class="text-sm text-blue-800 mb-2">Password cho tất cả: <strong>password123</strong></p>
                    <ul class="text-sm text-blue-800 space-y-1">
                        <li>• admin@taskflow.com (Admin)</li>
                        <li>• manager@taskflow.com (Manager)</li>
                        <li>• designer@taskflow.com (Member)</li>
                        <li>• frontend@taskflow.com (Member)</li>
                        <li>• backend@taskflow.com (Member)</li>
                    </ul>
                    <a href="../login.php" class="inline-block mt-4 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Đi đến trang đăng nhập →
                    </a>
                </div>
            <?php endif; ?>
            
            <div class="mt-6 pt-6 border-t border-gray-200 text-sm text-gray-500">
                <p><strong>Hướng dẫn:</strong></p>
                <ol class="list-decimal ml-4 mt-2 space-y-1">
                    <li>Import <code>database/taskflow2.sql</code> vào phpMyAdmin</li>
                    <li>Click "Tạo dữ liệu Demo" ở trên</li>
                    <li>Đăng nhập với tài khoản demo</li>
                </ol>
            </div>
        </div>
    </div>
</body>
</html>
