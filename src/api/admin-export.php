<?php
/**
 * API: Admin Export - Excel/CSV/JSON/PDF
 * 
 * Hỗ trợ xuất báo cáo dạng:
 * - Excel: Native .xlsx format
 * - CSV: Excel compatible
 * - JSON: Data interchange
 * - PDF: Print-ready HTML (auto print to PDF)
 */
require_once __DIR__ . '/../bootstrap.php';
require_once BASE_PATH . '/includes/csrf.php';
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    csrf_require();
}

use App\Middleware\AuthMiddleware;
use App\Middleware\PermissionMiddleware;
use Core\Database;
use Core\PdfExport;
use Core\ExcelExport;

AuthMiddleware::handle();
PermissionMiddleware::requireAdmin();

$type = $_GET['type'] ?? '';
$format = $_GET['format'] ?? 'csv';
$db = Database::getInstance();

try {
    switch ($type) {
        case 'users':
            $data = $db->fetchAll(
                "SELECT id, full_name, email, role, department, position, 
                        CASE WHEN is_active = 1 THEN 'Hoạt động' ELSE 'Vô hiệu' END as status,
                        DATE_FORMAT(created_at, '%d/%m/%Y') as created_date,
                        CASE WHEN last_login_at IS NOT NULL THEN DATE_FORMAT(last_login_at, '%d/%m/%Y %H:%i') ELSE 'Chưa đăng nhập' END as last_login
                 FROM users ORDER BY created_at DESC"
            );
            $filename = 'users_' . date('Y-m-d');
            $title = 'Báo cáo Người dùng';
            $headers = ['ID', 'Họ tên', 'Email', 'Vai trò', 'Phòng ban', 'Chức vụ', 'Trạng thái', 'Ngày tạo', 'Đăng nhập cuối'];
            break;
            
        case 'projects':
            $data = $db->fetchAll(
                "SELECT p.id, p.name, 
                        CASE p.status 
                            WHEN 'active' THEN 'Đang hoạt động'
                            WHEN 'planning' THEN 'Lên kế hoạch'
                            WHEN 'completed' THEN 'Hoàn thành'
                            WHEN 'on_hold' THEN 'Tạm dừng'
                            WHEN 'cancelled' THEN 'Đã hủy'
                            ELSE p.status
                        END as status,
                        CASE p.priority
                            WHEN 'urgent' THEN 'Khẩn cấp'
                            WHEN 'high' THEN 'Cao'
                            WHEN 'medium' THEN 'Trung bình'
                            WHEN 'low' THEN 'Thấp'
                            ELSE p.priority
                        END as priority,
                        CONCAT(p.progress, '%') as progress,
                        CASE WHEN p.start_date IS NOT NULL THEN DATE_FORMAT(p.start_date, '%d/%m/%Y') ELSE '-' END as start_date,
                        CASE WHEN p.end_date IS NOT NULL THEN DATE_FORMAT(p.end_date, '%d/%m/%Y') ELSE '-' END as end_date,
                        COALESCE(u.full_name, '-') as creator_name,
                        (SELECT COUNT(*) FROM project_members WHERE project_id = p.id) as member_count,
                        (SELECT COUNT(*) FROM tasks WHERE project_id = p.id) as task_count,
                        (SELECT COUNT(*) FROM tasks WHERE project_id = p.id AND status = 'done') as completed_tasks
                 FROM projects p
                 LEFT JOIN users u ON p.created_by = u.id
                 ORDER BY p.created_at DESC"
            );
            $filename = 'projects_' . date('Y-m-d');
            $title = 'Báo cáo Dự án';
            $headers = ['ID', 'Tên dự án', 'Trạng thái', 'Ưu tiên', 'Tiến độ', 'Ngày bắt đầu', 'Ngày kết thúc', 'Người tạo', 'Thành viên', 'Tổng CV', 'CV hoàn thành'];
            break;
            
        case 'tasks':
            $data = $db->fetchAll(
                "SELECT t.id, t.title, 
                        CASE t.status 
                            WHEN 'done' THEN 'Hoàn thành'
                            WHEN 'in_progress' THEN 'Đang làm'
                            WHEN 'in_review' THEN 'Đang review'
                            WHEN 'todo' THEN 'Cần làm'
                            WHEN 'backlog' THEN 'Backlog'
                            ELSE t.status
                        END as status,
                        CASE t.priority
                            WHEN 'urgent' THEN 'Khẩn cấp'
                            WHEN 'high' THEN 'Cao'
                            WHEN 'medium' THEN 'Trung bình'
                            WHEN 'low' THEN 'Thấp'
                            ELSE t.priority
                        END as priority,
                        CASE WHEN t.due_date IS NOT NULL THEN DATE_FORMAT(t.due_date, '%d/%m/%Y') ELSE '-' END as due_date,
                        CASE 
                            WHEN t.due_date < CURDATE() AND t.status != 'done' THEN 'Quá hạn'
                            WHEN t.due_date = CURDATE() AND t.status != 'done' THEN 'Hôm nay'
                            ELSE 'Bình thường'
                        END as deadline_status,
                        COALESCE(p.name, '-') as project_name,
                        COALESCE(u.full_name, '-') as creator_name,
                        (SELECT GROUP_CONCAT(u2.full_name SEPARATOR ', ') FROM task_assignees ta JOIN users u2 ON ta.user_id = u2.id WHERE ta.task_id = t.id) as assignees,
                        DATE_FORMAT(t.created_at, '%d/%m/%Y') as created_date
                 FROM tasks t
                 LEFT JOIN projects p ON t.project_id = p.id
                 LEFT JOIN users u ON t.created_by = u.id
                 ORDER BY t.created_at DESC"
            );
            $filename = 'tasks_' . date('Y-m-d');
            $title = 'Báo cáo Công việc';
            $headers = ['ID', 'Tiêu đề', 'Trạng thái', 'Ưu tiên', 'Deadline', 'Tình trạng', 'Dự án', 'Người tạo', 'Người thực hiện', 'Ngày tạo'];
            break;

        case 'documents':
            $data = $db->fetchAll(
                "SELECT d.id, d.name, 
                        CASE d.type WHEN 'folder' THEN 'Thư mục' ELSE 'Tệp tin' END as type,
                        CASE WHEN d.file_size IS NOT NULL THEN CONCAT(ROUND(d.file_size/1024, 1), ' KB') ELSE '-' END as file_size,
                        COALESCE(p.name, '-') as project_name,
                        COALESCE(u.full_name, '-') as uploader_name,
                        DATE_FORMAT(d.created_at, '%d/%m/%Y %H:%i') as created_date
                 FROM documents d
                 LEFT JOIN projects p ON d.project_id = p.id
                 LEFT JOIN users u ON d.uploaded_by = u.id
                 ORDER BY d.created_at DESC"
            );
            $filename = 'documents_' . date('Y-m-d');
            $title = 'Báo cáo Tài liệu';
            $headers = ['ID', 'Tên', 'Loại', 'Kích thước', 'Dự án', 'Người tải lên', 'Ngày tạo'];
            break;

        case 'tasks_summary':
            $data = $db->fetchAll(
                "SELECT 
                    p.name as project_name,
                    COUNT(t.id) as total_tasks,
                    SUM(CASE WHEN t.status = 'done' THEN 1 ELSE 0 END) as completed,
                    SUM(CASE WHEN t.status = 'in_progress' THEN 1 ELSE 0 END) as in_progress,
                    SUM(CASE WHEN t.status = 'todo' THEN 1 ELSE 0 END) as todo,
                    SUM(CASE WHEN t.due_date < CURDATE() AND t.status != 'done' THEN 1 ELSE 0 END) as overdue,
                    CONCAT(ROUND(SUM(CASE WHEN t.status = 'done' THEN 1 ELSE 0 END) * 100.0 / NULLIF(COUNT(t.id), 0), 1), '%') as completion_rate
                 FROM projects p
                 LEFT JOIN tasks t ON p.id = t.project_id
                 GROUP BY p.id, p.name
                 HAVING total_tasks > 0
                 ORDER BY completion_rate DESC"
            );
            $filename = 'tasks_summary_' . date('Y-m-d');
            $title = 'Báo cáo Tổng hợp Công việc theo Dự án';
            $headers = ['Dự án', 'Tổng', 'Hoàn thành', 'Đang làm', 'Cần làm', 'Quá hạn', 'Tỷ lệ'];
            break;

        case 'team_performance':
            $data = $db->fetchAll(
                "SELECT 
                    u.full_name,
                    COALESCE(u.department, '-') as department,
                    COALESCE(u.position, '-') as position,
                    COUNT(DISTINCT ta.task_id) as assigned_tasks,
                    SUM(CASE WHEN t.status = 'done' THEN 1 ELSE 0 END) as completed_tasks,
                    SUM(CASE WHEN t.status = 'in_progress' THEN 1 ELSE 0 END) as in_progress_tasks,
                    SUM(CASE WHEN t.due_date < CURDATE() AND t.status != 'done' THEN 1 ELSE 0 END) as overdue_tasks,
                    CONCAT(ROUND(SUM(CASE WHEN t.status = 'done' THEN 1 ELSE 0 END) * 100.0 / NULLIF(COUNT(DISTINCT ta.task_id), 0), 1), '%') as completion_rate
                 FROM users u
                 LEFT JOIN task_assignees ta ON u.id = ta.user_id
                 LEFT JOIN tasks t ON ta.task_id = t.id
                 WHERE u.is_active = 1
                 GROUP BY u.id, u.full_name, u.department, u.position
                 HAVING assigned_tasks > 0
                 ORDER BY completion_rate DESC, completed_tasks DESC"
            );
            $filename = 'team_performance_' . date('Y-m-d');
            $title = 'Báo cáo Hiệu suất Nhân viên';
            $headers = ['Họ tên', 'Phòng ban', 'Chức vụ', 'Được giao', 'Hoàn thành', 'Đang làm', 'Quá hạn', 'Tỷ lệ'];
            break;

        case 'activity_logs':
            $data = $db->fetchAll(
                "SELECT 
                    al.id,
                    COALESCE(u.full_name, 'System') as user_name,
                    al.action,
                    al.entity_type,
                    al.entity_id,
                    al.ip_address,
                    DATE_FORMAT(al.created_at, '%d/%m/%Y %H:%i:%s') as created_at
                 FROM activity_logs al
                 LEFT JOIN users u ON al.user_id = u.id
                 ORDER BY al.created_at DESC
                 LIMIT 1000"
            );
            $filename = 'activity_logs_' . date('Y-m-d');
            $title = 'Nhật ký Hoạt động Hệ thống';
            $headers = ['ID', 'Người dùng', 'Hành động', 'Loại', 'Entity ID', 'IP', 'Thời gian'];
            break;

        case 'overdue_tasks':
            $data = $db->fetchAll(
                "SELECT 
                    t.id, t.title,
                    CASE t.priority
                        WHEN 'urgent' THEN 'Khẩn cấp'
                        WHEN 'high' THEN 'Cao'
                        WHEN 'medium' THEN 'Trung bình'
                        WHEN 'low' THEN 'Thấp'
                        ELSE t.priority
                    END as priority,
                    DATE_FORMAT(t.due_date, '%d/%m/%Y') as due_date,
                    DATEDIFF(CURDATE(), t.due_date) as days_overdue,
                    COALESCE(p.name, '-') as project_name,
                    (SELECT GROUP_CONCAT(u2.full_name SEPARATOR ', ') FROM task_assignees ta JOIN users u2 ON ta.user_id = u2.id WHERE ta.task_id = t.id) as assignees
                 FROM tasks t
                 LEFT JOIN projects p ON t.project_id = p.id
                 WHERE t.due_date < CURDATE() AND t.status != 'done'
                 ORDER BY t.due_date ASC"
            );
            $filename = 'overdue_tasks_' . date('Y-m-d');
            $title = 'Báo cáo Công việc Quá hạn';
            $headers = ['ID', 'Tiêu đề', 'Ưu tiên', 'Deadline', 'Số ngày quá hạn', 'Dự án', 'Người thực hiện'];
            break;

        case 'project_members':
            $data = $db->fetchAll(
                "SELECT 
                    p.name as project_name,
                    u.full_name,
                    u.email,
                    CASE pm.role
                        WHEN 'owner' THEN 'Chủ sở hữu'
                        WHEN 'manager' THEN 'Quản lý'
                        WHEN 'member' THEN 'Thành viên'
                        WHEN 'viewer' THEN 'Người xem'
                        ELSE pm.role
                    END as role,
                    DATE_FORMAT(pm.joined_at, '%d/%m/%Y') as joined_date
                 FROM project_members pm
                 JOIN projects p ON pm.project_id = p.id
                 JOIN users u ON pm.user_id = u.id
                 ORDER BY p.name, pm.role"
            );
            $filename = 'project_members_' . date('Y-m-d');
            $title = 'Báo cáo Thành viên Dự án';
            $headers = ['Dự án', 'Họ tên', 'Email', 'Vai trò', 'Ngày tham gia'];
            break;

        case 'dashboard_summary':
            // Dashboard summary - single row with all stats
            $stats = $db->fetchOne("SELECT 
                (SELECT COUNT(*) FROM users WHERE is_active = 1) as total_users,
                (SELECT COUNT(*) FROM projects) as total_projects,
                (SELECT COUNT(*) FROM projects WHERE status = 'active') as active_projects,
                (SELECT COUNT(*) FROM tasks) as total_tasks,
                (SELECT COUNT(*) FROM tasks WHERE status = 'done') as completed_tasks,
                (SELECT COUNT(*) FROM tasks WHERE due_date < CURDATE() AND status != 'done') as overdue_tasks,
                (SELECT COUNT(*) FROM documents) as total_documents,
                (SELECT ROUND(SUM(file_size)/1024/1024, 2) FROM documents WHERE file_size IS NOT NULL) as storage_mb
            ");
            
            $data = [[
                'Người dùng hoạt động' => $stats['total_users'],
                'Tổng dự án' => $stats['total_projects'],
                'Dự án đang hoạt động' => $stats['active_projects'],
                'Tổng công việc' => $stats['total_tasks'],
                'Công việc hoàn thành' => $stats['completed_tasks'],
                'Công việc quá hạn' => $stats['overdue_tasks'],
                'Tổng tài liệu' => $stats['total_documents'],
                'Dung lượng (MB)' => $stats['storage_mb'] ?? 0
            ]];
            $filename = 'dashboard_summary_' . date('Y-m-d');
            $title = 'Tổng quan Hệ thống';
            $headers = array_keys($data[0]);
            break;
            
        default:
            throw new Exception('Invalid export type: ' . $type);
    }
    
    // Export based on format
    if ($format === 'excel') {
        $excel = new ExcelExport($title);
        $excel->addSheet('Báo cáo', $headers, $data, [
            'columnWidths' => array_fill(0, count($headers), 18)
        ]);
        $excel->download($filename);
        
    } elseif ($format === 'pdf') {
        $pdf = new PdfExport($title);
        $pdf->setHeaders($headers)
            ->setData($data)
            ->setSubtitle('Xuất từ hệ thống TaskFlow - ' . date('d/m/Y H:i'))
            ->setOptions([
                'orientation' => count($headers) > 7 ? 'landscape' : 'portrait',
            ])
            ->output();
            
    } elseif ($format === 'json') {
        header('Content-Type: application/json; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '.json"');
        echo json_encode([
            'title' => $title,
            'exported_at' => date('Y-m-d H:i:s'),
            'exported_by' => 'Admin',
            'total' => count($data),
            'data' => $data
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        
    } else {
        // CSV format (default)
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '.csv"');
        
        // UTF-8 BOM for Excel
        echo "\xEF\xBB\xBF";
        
        $output = fopen('php://output', 'w');
        fputcsv($output, $headers);
        
        foreach ($data as $row) {
            $csvRow = array_values($row);
            foreach ($csvRow as &$value) {
                if ($value === null) $value = '';
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
