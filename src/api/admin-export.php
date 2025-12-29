<?php
/**
 * API: Admin Export - CSV/JSON
 */
require_once __DIR__ . '/../bootstrap.php';

use App\Middleware\AuthMiddleware;
use App\Middleware\PermissionMiddleware;
use Core\Database;

AuthMiddleware::handle();
PermissionMiddleware::requireAdmin();

$type = $_GET['type'] ?? '';
$format = $_GET['format'] ?? 'csv';
$db = Database::getInstance();

try {
    switch ($type) {
        case 'users':
            $data = $db->fetchAll(
                "SELECT id, full_name, email, role, department, position, is_active, created_at, last_login_at 
                 FROM users ORDER BY created_at DESC"
            );
            $filename = 'users_' . date('Y-m-d');
            $headers = ['ID', 'Họ tên', 'Email', 'Vai trò', 'Phòng ban', 'Chức vụ', 'Trạng thái', 'Ngày tạo', 'Đăng nhập cuối'];
            break;
            
        case 'projects':
            $data = $db->fetchAll(
                "SELECT p.id, p.name, p.status, p.priority, p.progress, p.start_date, p.end_date, p.created_at,
                        u.full_name as creator_name,
                        (SELECT COUNT(*) FROM project_members WHERE project_id = p.id) as member_count,
                        (SELECT COUNT(*) FROM tasks WHERE project_id = p.id) as task_count
                 FROM projects p
                 LEFT JOIN users u ON p.created_by = u.id
                 ORDER BY p.created_at DESC"
            );
            $filename = 'projects_' . date('Y-m-d');
            $headers = ['ID', 'Tên dự án', 'Trạng thái', 'Ưu tiên', 'Tiến độ', 'Ngày bắt đầu', 'Ngày kết thúc', 'Ngày tạo', 'Người tạo', 'Thành viên', 'Công việc'];
            break;
            
        case 'tasks':
            $data = $db->fetchAll(
                "SELECT t.id, t.title, t.status, t.priority, t.due_date, t.created_at,
                        p.name as project_name, u.full_name as creator_name
                 FROM tasks t
                 LEFT JOIN projects p ON t.project_id = p.id
                 LEFT JOIN users u ON t.created_by = u.id
                 ORDER BY t.created_at DESC"
            );
            $filename = 'tasks_' . date('Y-m-d');
            $headers = ['ID', 'Tiêu đề', 'Trạng thái', 'Ưu tiên', 'Deadline', 'Ngày tạo', 'Dự án', 'Người tạo'];
            break;
            
        default:
            throw new Exception('Invalid export type');
    }
    
    if ($format === 'json') {
        header('Content-Type: application/json; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '.json"');
        echo json_encode(['data' => $data, 'exported_at' => date('Y-m-d H:i:s')], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    } else {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '.csv"');
        
        // UTF-8 BOM for Excel
        echo "\xEF\xBB\xBF";
        
        $output = fopen('php://output', 'w');
        fputcsv($output, $headers);
        
        foreach ($data as $row) {
            // Format data for CSV
            $csvRow = array_values($row);
            
            // Convert status/boolean values
            foreach ($csvRow as &$value) {
                if ($value === '1' || $value === 1) $value = 'Có';
                elseif ($value === '0' || $value === 0) $value = 'Không';
            }
            
            fputcsv($output, $csvRow);
        }
        
        fclose($output);
    }
    
} catch (Exception $e) {
    http_response_code(400);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
