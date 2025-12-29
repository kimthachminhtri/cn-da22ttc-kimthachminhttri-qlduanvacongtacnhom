<?php
/**
 * Seed Database - Tạo dữ liệu mẫu hoàn chỉnh
 * Chạy file này một lần để tạo dữ liệu mẫu
 * 
 * URL: http://localhost/php/database/seed-users.php
 */

require_once __DIR__ . '/../includes/config.php';

echo "<pre style='font-family: monospace; background: #1e293b; color: #e2e8f0; padding: 20px; border-radius: 8px;'>";
echo "╔══════════════════════════════════════════╗\n";
echo "║     TASKFLOW DATABASE SEEDER            ║\n";
echo "╚══════════════════════════════════════════╝\n\n";

$db = Database::getInstance();

// =============================================
// 1. SEED USERS
// =============================================
echo "<span style='color: #60a5fa;'>📦 Seeding Users...</span>\n";

$users = [
    ['id' => 'user-1', 'email' => 'admin@taskflow.com', 'password' => 'password123', 'full_name' => 'Nguyễn Văn An', 'role' => 'admin', 'department' => 'Quản lý', 'position' => 'Project Manager', 'phone' => '0901234567'],
    ['id' => 'user-2', 'email' => 'manager@taskflow.com', 'password' => 'password123', 'full_name' => 'Trần Thị Bình', 'role' => 'manager', 'department' => 'Kỹ thuật', 'position' => 'Tech Lead', 'phone' => '0912345678'],
    ['id' => 'user-3', 'email' => 'designer@taskflow.com', 'password' => 'password123', 'full_name' => 'Lê Minh Châu', 'role' => 'member', 'department' => 'Thiết kế', 'position' => 'UI/UX Designer', 'phone' => '0923456789'],
    ['id' => 'user-4', 'email' => 'frontend@taskflow.com', 'password' => 'password123', 'full_name' => 'Phạm Hồng Đào', 'role' => 'member', 'department' => 'Kỹ thuật', 'position' => 'Frontend Developer', 'phone' => '0934567890'],
    ['id' => 'user-5', 'email' => 'backend@taskflow.com', 'password' => 'password123', 'full_name' => 'Hoàng Văn Em', 'role' => 'member', 'department' => 'Kỹ thuật', 'position' => 'Backend Developer', 'phone' => '0945678901'],
    ['id' => 'user-6', 'email' => 'qa@taskflow.com', 'password' => 'password123', 'full_name' => 'Đỗ Thị Fương', 'role' => 'member', 'department' => 'Kiểm thử', 'position' => 'QA Engineer', 'phone' => '0956789012'],
    ['id' => 'user-7', 'email' => 'devops@taskflow.com', 'password' => 'password123', 'full_name' => 'Vũ Văn Giang', 'role' => 'member', 'department' => 'Kỹ thuật', 'position' => 'DevOps Engineer', 'phone' => '0967890123'],
    ['id' => 'user-8', 'email' => 'ba@taskflow.com', 'password' => 'password123', 'full_name' => 'Ngô Thị Hương', 'role' => 'guest', 'department' => 'Phân tích', 'position' => 'Business Analyst', 'phone' => '0978901234'],
];

$userCount = 0;
foreach ($users as $userData) {
    try {
        $existing = $db->fetchOne("SELECT id FROM users WHERE email = ?", [$userData['email']]);
        if ($existing) {
            echo "   ⏭️  {$userData['email']} (exists)\n";
            continue;
        }
        
        $password = $userData['password'];
        unset($userData['password']);
        $userData['password_hash'] = password_hash($password, PASSWORD_DEFAULT);
        $userData['is_active'] = 1;
        $userData['created_at'] = date('Y-m-d H:i:s');
        
        $db->insert('users', $userData);
        echo "   <span style='color: #4ade80;'>✓</span> {$userData['email']}\n";
        $userCount++;
    } catch (Exception $e) {
        echo "   <span style='color: #f87171;'>✗</span> {$userData['email']}: {$e->getMessage()}\n";
    }
}
echo "   → Created {$userCount} users\n\n";

// =============================================
// 2. SEED LABELS
// =============================================
echo "<span style='color: #60a5fa;'>🏷️  Seeding Labels...</span>\n";

$labels = [
    ['id' => 'label-1', 'name' => 'Bug', 'color' => '#ef4444', 'description' => 'Lỗi cần sửa'],
    ['id' => 'label-2', 'name' => 'Feature', 'color' => '#22c55e', 'description' => 'Tính năng mới'],
    ['id' => 'label-3', 'name' => 'Enhancement', 'color' => '#3b82f6', 'description' => 'Cải tiến'],
    ['id' => 'label-4', 'name' => 'Documentation', 'color' => '#a855f7', 'description' => 'Tài liệu'],
    ['id' => 'label-5', 'name' => 'Urgent', 'color' => '#f97316', 'description' => 'Khẩn cấp'],
    ['id' => 'label-6', 'name' => 'Design', 'color' => '#ec4899', 'description' => 'Thiết kế'],
    ['id' => 'label-7', 'name' => 'Backend', 'color' => '#14b8a6', 'description' => 'Phía server'],
    ['id' => 'label-8', 'name' => 'Frontend', 'color' => '#f59e0b', 'description' => 'Phía client'],
    ['id' => 'label-9', 'name' => 'Testing', 'color' => '#8b5cf6', 'description' => 'Kiểm thử'],
    ['id' => 'label-10', 'name' => 'DevOps', 'color' => '#06b6d4', 'description' => 'Vận hành'],
];

$labelCount = 0;
foreach ($labels as $label) {
    try {
        $existing = $db->fetchOne("SELECT id FROM labels WHERE id = ?", [$label['id']]);
        if ($existing) continue;
        
        $db->insert('labels', $label);
        $labelCount++;
    } catch (Exception $e) {}
}
echo "   → Created {$labelCount} labels\n\n";

// =============================================
// 3. SEED PROJECTS
// =============================================
echo "<span style='color: #60a5fa;'>📁 Seeding Projects...</span>\n";

$projects = [
    ['id' => 'project-1', 'name' => 'Website E-commerce', 'description' => 'Phát triển website thương mại điện tử', 'color' => '#6366f1', 'icon' => 'shopping-cart', 'status' => 'active', 'priority' => 'high', 'progress' => 65, 'start_date' => '2024-01-15', 'end_date' => '2024-06-30', 'budget' => 500000000, 'created_by' => 'user-1'],
    ['id' => 'project-2', 'name' => 'Mobile App', 'description' => 'Ứng dụng di động cho iOS và Android', 'color' => '#22c55e', 'icon' => 'smartphone', 'status' => 'active', 'priority' => 'high', 'progress' => 40, 'start_date' => '2024-02-01', 'end_date' => '2024-08-31', 'budget' => 350000000, 'created_by' => 'user-1'],
    ['id' => 'project-3', 'name' => 'Hệ thống CRM', 'description' => 'Xây dựng hệ thống quản lý khách hàng', 'color' => '#f59e0b', 'icon' => 'users', 'status' => 'planning', 'priority' => 'medium', 'progress' => 15, 'start_date' => '2024-04-01', 'end_date' => '2024-10-31', 'budget' => 400000000, 'created_by' => 'user-1'],
    ['id' => 'project-4', 'name' => 'Redesign Landing Page', 'description' => 'Thiết kế lại trang chủ', 'color' => '#ec4899', 'icon' => 'layout', 'status' => 'active', 'priority' => 'medium', 'progress' => 80, 'start_date' => '2024-03-01', 'end_date' => '2024-04-15', 'budget' => 80000000, 'created_by' => 'user-3'],
    ['id' => 'project-5', 'name' => 'API Gateway', 'description' => 'Xây dựng API Gateway tập trung', 'color' => '#14b8a6', 'icon' => 'git-branch', 'status' => 'on_hold', 'priority' => 'low', 'progress' => 30, 'start_date' => '2024-02-15', 'end_date' => '2024-05-30', 'budget' => 200000000, 'created_by' => 'user-2'],
    ['id' => 'project-6', 'name' => 'Tài liệu kỹ thuật', 'description' => 'Viết tài liệu API và hướng dẫn', 'color' => '#a855f7', 'icon' => 'book-open', 'status' => 'active', 'priority' => 'low', 'progress' => 45, 'start_date' => '2024-01-01', 'end_date' => '2024-12-31', 'budget' => 50000000, 'created_by' => 'user-8'],
];

$projectCount = 0;
foreach ($projects as $project) {
    try {
        $existing = $db->fetchOne("SELECT id FROM projects WHERE id = ?", [$project['id']]);
        if ($existing) continue;
        
        $db->insert('projects', $project);
        echo "   <span style='color: #4ade80;'>✓</span> {$project['name']}\n";
        $projectCount++;
    } catch (Exception $e) {
        echo "   <span style='color: #f87171;'>✗</span> {$project['name']}: {$e->getMessage()}\n";
    }
}
echo "   → Created {$projectCount} projects\n\n";

// =============================================
// 4. SEED PROJECT MEMBERS
// =============================================
echo "<span style='color: #60a5fa;'>👥 Seeding Project Members...</span>\n";

$members = [
    ['project-1', 'user-1', 'owner'], ['project-1', 'user-2', 'manager'], ['project-1', 'user-3', 'member'],
    ['project-1', 'user-4', 'member'], ['project-1', 'user-5', 'member'], ['project-1', 'user-6', 'member'],
    ['project-2', 'user-1', 'owner'], ['project-2', 'user-2', 'manager'], ['project-2', 'user-4', 'member'],
    ['project-3', 'user-1', 'owner'], ['project-3', 'user-5', 'member'], ['project-3', 'user-8', 'member'],
    ['project-4', 'user-3', 'owner'], ['project-4', 'user-4', 'member'],
    ['project-5', 'user-2', 'owner'], ['project-5', 'user-5', 'member'], ['project-5', 'user-7', 'member'],
    ['project-6', 'user-8', 'owner'], ['project-6', 'user-2', 'member'],
];

$memberCount = 0;
foreach ($members as $m) {
    try {
        $db->query("INSERT IGNORE INTO project_members (project_id, user_id, role) VALUES (?, ?, ?)", $m);
        $memberCount++;
    } catch (Exception $e) {}
}
echo "   → Added {$memberCount} project members\n\n";

// =============================================
// 5. SEED TASKS
// =============================================
echo "<span style='color: #60a5fa;'>✅ Seeding Tasks...</span>\n";

$tasks = [
    ['id' => 'task-1', 'project_id' => 'project-1', 'title' => 'Tích hợp thanh toán MoMo', 'status' => 'backlog', 'priority' => 'medium', 'due_date' => '2024-05-15', 'estimated_hours' => 16, 'created_by' => 'user-1'],
    ['id' => 'task-2', 'project_id' => 'project-1', 'title' => 'Thiết kế trang chi tiết sản phẩm', 'status' => 'todo', 'priority' => 'high', 'due_date' => '2024-04-10', 'estimated_hours' => 8, 'created_by' => 'user-1'],
    ['id' => 'task-3', 'project_id' => 'project-1', 'title' => 'API quản lý đơn hàng', 'status' => 'todo', 'priority' => 'high', 'due_date' => '2024-04-12', 'estimated_hours' => 20, 'created_by' => 'user-2'],
    ['id' => 'task-4', 'project_id' => 'project-1', 'title' => 'Xây dựng giỏ hàng', 'status' => 'in_progress', 'priority' => 'high', 'start_date' => '2024-03-10', 'due_date' => '2024-04-05', 'estimated_hours' => 16, 'actual_hours' => 10, 'created_by' => 'user-2'],
    ['id' => 'task-5', 'project_id' => 'project-1', 'title' => 'Tích hợp VNPay', 'status' => 'in_progress', 'priority' => 'urgent', 'start_date' => '2024-03-08', 'due_date' => '2024-04-03', 'estimated_hours' => 12, 'actual_hours' => 8, 'created_by' => 'user-1'],
    ['id' => 'task-6', 'project_id' => 'project-1', 'title' => 'Trang đăng ký/đăng nhập', 'status' => 'in_review', 'priority' => 'high', 'start_date' => '2024-03-15', 'due_date' => '2024-03-28', 'estimated_hours' => 8, 'actual_hours' => 7, 'created_by' => 'user-2'],
    ['id' => 'task-7', 'project_id' => 'project-1', 'title' => 'Setup dự án Next.js', 'status' => 'done', 'priority' => 'high', 'start_date' => '2024-01-15', 'due_date' => '2024-01-20', 'estimated_hours' => 4, 'actual_hours' => 3, 'completed_at' => '2024-01-19 17:00:00', 'created_by' => 'user-2'],
    ['id' => 'task-8', 'project_id' => 'project-2', 'title' => 'Màn hình Home', 'status' => 'in_progress', 'priority' => 'high', 'start_date' => '2024-03-10', 'due_date' => '2024-04-05', 'estimated_hours' => 12, 'actual_hours' => 6, 'created_by' => 'user-1'],
    ['id' => 'task-9', 'project_id' => 'project-4', 'title' => 'Hero Section', 'status' => 'in_review', 'priority' => 'high', 'start_date' => '2024-03-14', 'due_date' => '2024-03-25', 'estimated_hours' => 6, 'actual_hours' => 5, 'created_by' => 'user-3'],
    ['id' => 'task-10', 'project_id' => 'project-3', 'title' => 'Phân tích yêu cầu CRM', 'status' => 'in_progress', 'priority' => 'high', 'start_date' => '2024-03-15', 'due_date' => '2024-04-10', 'estimated_hours' => 20, 'actual_hours' => 8, 'created_by' => 'user-1'],
];

$taskCount = 0;
foreach ($tasks as $task) {
    try {
        $existing = $db->fetchOne("SELECT id FROM tasks WHERE id = ?", [$task['id']]);
        if ($existing) continue;
        
        $task['position'] = $taskCount;
        $db->insert('tasks', $task);
        echo "   <span style='color: #4ade80;'>✓</span> {$task['title']}\n";
        $taskCount++;
    } catch (Exception $e) {
        echo "   <span style='color: #f87171;'>✗</span> {$task['title']}: {$e->getMessage()}\n";
    }
}
echo "   → Created {$taskCount} tasks\n\n";

// =============================================
// 6. SEED TASK ASSIGNEES
// =============================================
echo "<span style='color: #60a5fa;'>👤 Seeding Task Assignees...</span>\n";

$assignees = [
    ['task-1', 'user-5'], ['task-2', 'user-3'], ['task-3', 'user-5'],
    ['task-4', 'user-4'], ['task-5', 'user-5'], ['task-6', 'user-4'],
    ['task-7', 'user-2'], ['task-8', 'user-4'], ['task-9', 'user-3'],
    ['task-10', 'user-8'],
];

foreach ($assignees as $a) {
    try {
        $db->query("INSERT IGNORE INTO task_assignees (task_id, user_id, assigned_by, assigned_at) VALUES (?, ?, 'user-1', NOW())", $a);
    } catch (Exception $e) {}
}
echo "   → Assigned users to tasks\n\n";

// =============================================
// 7. SEED COMMENTS
// =============================================
echo "<span style='color: #60a5fa;'>💬 Seeding Comments...</span>\n";

$comments = [
    ['entity_type' => 'task', 'entity_id' => 'task-4', 'content' => 'Đã hoàn thành phần UI, đang implement logic add/remove.', 'created_by' => 'user-4'],
    ['entity_type' => 'task', 'entity_id' => 'task-4', 'content' => 'Nhớ handle edge case khi cart rỗng và khi product hết hàng nhé!', 'created_by' => 'user-2'],
    ['entity_type' => 'task', 'entity_id' => 'task-5', 'content' => 'Đã test sandbox thành công, chuẩn bị chuyển production.', 'created_by' => 'user-5'],
];

$commentCount = 0;
foreach ($comments as $comment) {
    try {
        $comment['created_at'] = date('Y-m-d H:i:s');
        $db->insert('comments', $comment);
        $commentCount++;
    } catch (Exception $e) {}
}
echo "   → Created {$commentCount} comments\n\n";

// =============================================
// DONE
// =============================================
echo "╔══════════════════════════════════════════╗\n";
echo "║  <span style='color: #4ade80;'>✓ DATABASE SEEDED SUCCESSFULLY!</span>        ║\n";
echo "╚══════════════════════════════════════════╝\n\n";

echo "<span style='color: #fbbf24;'>📋 Demo Accounts:</span>\n";
echo "   Email: admin@taskflow.com\n";
echo "   Email: manager@taskflow.com\n";
echo "   Email: designer@taskflow.com\n";
echo "   Email: frontend@taskflow.com\n";
echo "   Email: backend@taskflow.com\n";
echo "   <span style='color: #94a3b8;'>Password: password123 (all accounts)</span>\n";

echo "</pre>";
