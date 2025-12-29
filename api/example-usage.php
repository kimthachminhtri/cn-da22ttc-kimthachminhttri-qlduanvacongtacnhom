<?php
/**
 * Example: Cách sử dụng Database và Models
 * 
 * ⚠️ FILE NÀY CHỈ ĐỂ THAM KHẢO - KHÔNG CHẠY TRỰC TIẾP
 * Tất cả code bên dưới được comment để tránh lỗi khi vô tình truy cập
 */

// Ngăn chạy trực tiếp
die('⚠️ File này chỉ để tham khảo. Không chạy trực tiếp.');

/*
============================================
1. SỬ DỤNG DATABASE TRỰC TIẾP
============================================

require_once __DIR__ . '/../includes/config.php';

// Lấy instance
$db = Database::getInstance();
// hoặc dùng helper: $db = db();

// SELECT - Lấy tất cả
$users = $db->fetchAll("SELECT * FROM users WHERE is_active = ?", [1]);

// SELECT - Lấy một record
$user = $db->fetchOne("SELECT * FROM users WHERE id = ?", ['user-123']);

// SELECT - Lấy một giá trị
$count = $db->fetchColumn("SELECT COUNT(*) FROM tasks WHERE status = ?", ['done']);

// INSERT
$newId = $db->insert('users', [
    'email' => 'new@example.com',
    'full_name' => 'New User',
    'password_hash' => password_hash('password', PASSWORD_DEFAULT),
]);

// UPDATE
$affected = $db->update('users', 
    ['full_name' => 'Updated Name'], 
    'id = ?', 
    ['user-123']
);

// DELETE
$deleted = $db->delete('users', 'id = ?', ['user-123']);

// Transaction
try {
    $db->beginTransaction();
    
    $db->insert('projects', ['name' => 'New Project']);
    $db->insert('tasks', ['title' => 'First Task', 'project_id' => $db->getConnection()->lastInsertId()]);
    
    $db->commit();
} catch (Exception $e) {
    $db->rollback();
    throw $e;
}

============================================
2. SỬ DỤNG MODELS
============================================

// --- User Model ---
$userModel = new User();

// Lấy tất cả users
$allUsers = $userModel->all();

// Tìm theo ID
$user = $userModel->find('user-123');

// Tìm theo email
$user = $userModel->findByEmail('test@example.com');

// Tạo user mới
$userId = $userModel->createUser([
    'email' => 'new@example.com',
    'full_name' => 'New User',
    'password' => 'secret123',
]);

// Xác thực đăng nhập
$user = $userModel->verifyPassword('test@example.com', 'password');
if ($user) {
    $userModel->updateLastLogin($user['id']);
}

// --- Project Model ---
$projectModel = new Project();

// Lấy projects với thống kê
$projects = $projectModel->getAllWithStats();

// Lấy project với chi tiết
$project = $projectModel->getWithDetails('project-123');

// Thêm member
$projectModel->addMember('project-123', 'user-456', 'member');

// --- Task Model ---
$taskModel = new Task();

// Lấy tasks với chi tiết
$tasks = $taskModel->getAllWithDetails();

// Lấy task với đầy đủ thông tin (assignees, checklist, comments, labels)
$task = $taskModel->getWithDetails('task-123');

// Lấy tasks theo project
$projectTasks = $taskModel->getByProject('project-123');

// Gán user vào task
$taskModel->assignUser('task-123', 'user-456');

// Thêm comment
$taskModel->addComment('task-123', 'user-456', 'Great work!');

// Lấy tasks quá hạn
$overdueTasks = $taskModel->getOverdue('user-123');

// Lấy tasks sắp đến hạn (7 ngày tới)
$upcomingTasks = $taskModel->getUpcoming(7, 'user-123');

// --- Document Model ---
$docModel = new Document();

// Lấy documents trong folder
$docs = $docModel->getAllWithCreator('folder-123');

// Toggle star
$docModel->toggleStar('doc-123');

// Tạo folder
$folderId = $docModel->createFolder('New Folder', null, 'user-123');

// Tìm kiếm
$results = $docModel->search('design');

============================================
3. PAGINATION
============================================

$page = $_GET['page'] ?? 1;
$perPage = 10;

$result = $taskModel->paginate($page, $perPage, 'status = ?', ['todo']);
// $result = [
//     'data' => [...],
//     'total' => 50,
//     'per_page' => 10,
//     'current_page' => 1,
//     'last_page' => 5,
// ]

============================================
4. TRONG CONTROLLER/PAGE
============================================

// Ví dụ trong tasks.php:

require_once 'includes/config.php';

$taskModel = new Task();
$projectModel = new Project();

// Lấy dữ liệu
$tasks = $taskModel->getAllWithDetails();
$projects = $projectModel->all();

// Group tasks by status cho Kanban
$columns = [
    'backlog' => array_filter($tasks, fn($t) => $t['status'] === 'backlog'),
    'todo' => array_filter($tasks, fn($t) => $t['status'] === 'todo'),
    'in_progress' => array_filter($tasks, fn($t) => $t['status'] === 'in_progress'),
    'in_review' => array_filter($tasks, fn($t) => $t['status'] === 'in_review'),
    'done' => array_filter($tasks, fn($t) => $t['status'] === 'done'),
];

*/
