<?php
/**
 * API: Export Report - Xuất báo cáo cho tất cả user
 * 
 * Hỗ trợ xuất:
 * - Excel (.xlsx)
 * - CSV
 * - PDF (HTML print)
 * 
 * Các loại báo cáo:
 * - my-tasks: Công việc của tôi
 * - project-tasks: Công việc theo dự án
 * - project-summary: Tổng hợp dự án
 * - team-workload: Khối lượng công việc team
 */

require_once __DIR__ . '/../bootstrap.php';

use App\Middleware\AuthMiddleware;
use Core\Database;
use Core\Session;
use Core\Permission;
use Core\ExcelExport;
use Core\PdfExport;

AuthMiddleware::handle();

$userId = Session::get('user_id');
$userRole = Session::get('user_role', 'member');
$type = $_GET['type'] ?? 'my-tasks';
$format = $_GET['format'] ?? 'excel'; // excel, csv, pdf
$projectId = $_GET['project_id'] ?? null;

$db = Database::getInstance();

try {
    switch ($type) {
        // ========================================
        // MY TASKS - Công việc của tôi
        // ========================================
        case 'my-tasks':
            $data = $db->fetchAll(
                "SELECT t.title, t.status, t.priority, t.due_date, 
                        p.name as project_name, t.created_at,
                        CASE WHEN t.status = 'done' THEN 'Đã xong' 
                             WHEN t.due_date < CURDATE() AND t.status != 'done' THEN 'Quá hạn'
                             ELSE 'Đang làm' END as progress_status
                 FROM tasks t
                 LEFT JOIN task_assignees ta ON t.id = ta.task_id
                 LEFT JOIN projects p ON t.project_id = p.id
                 WHERE ta.user_id = ? OR t.created_by = ?
                 ORDER BY t.due_date ASC, t.priority DESC",
                [$userId, $userId]
            );
            $filename = 'cong_viec_cua_toi_' . date('Y-m-d');
            $title = 'Báo cáo Công việc của tôi';
            $headers = ['Tiêu đề', 'Trạng thái', 'Ưu tiên', 'Deadline', 'Dự án', 'Ngày tạo', 'Tình trạng'];
            break;

        // ========================================
        // PROJECT TASKS - Công việc theo dự án
        // ========================================
        case 'project-tasks':
            if (!$projectId) {
                throw new Exception('project_id is required');
            }
            
            // Check if user has access to project
            $isMember = $db->fetchOne(
                "SELECT 1 FROM project_members WHERE project_id = ? AND user_id = ?",
                [$projectId, $userId]
            );
            
            if (!$isMember && $userRole !== 'admin') {
                throw new Exception('Bạn không có quyền truy cập dự án này');
            }
            
            $project = $db->fetchOne("SELECT name FROM projects WHERE id = ?", [$projectId]);
            
            $data = $db->fetchAll(
                "SELECT t.title, t.status, t.priority, t.due_date, t.created_at,
                        GROUP_CONCAT(u.full_name SEPARATOR ', ') as assignees,
                        COALESCE(t.estimated_hours, 0) as estimated_hours,
                        COALESCE(t.actual_hours, 0) as actual_hours
                 FROM tasks t
                 LEFT JOIN task_assignees ta ON t.id = ta.task_id
                 LEFT JOIN users u ON ta.user_id = u.id
                 WHERE t.project_id = ?
                 GROUP BY t.id
                 ORDER BY t.position ASC, t.due_date ASC",
                [$projectId]
            );
            $filename = 'du_an_' . preg_replace('/[^a-zA-Z0-9]/', '_', $project['name'] ?? 'unknown') . '_' . date('Y-m-d');
            $title = 'Báo cáo Công việc - ' . ($project['name'] ?? 'Dự án');
            $headers = ['Tiêu đề', 'Trạng thái', 'Ưu tiên', 'Deadline', 'Ngày tạo', 'Người thực hiện', 'Giờ ước tính', 'Giờ thực tế'];
            break;

        // ========================================
        // PROJECT SUMMARY - Tổng hợp dự án
        // ========================================
        case 'project-summary':
            // Get projects user has access to
            if ($userRole === 'admin') {
                $data = $db->fetchAll(
                    "SELECT p.name, p.status, p.priority, p.progress, p.start_date, p.end_date,
                            (SELECT COUNT(*) FROM project_members WHERE project_id = p.id) as members,
                            (SELECT COUNT(*) FROM tasks WHERE project_id = p.id) as total_tasks,
                            (SELECT COUNT(*) FROM tasks WHERE project_id = p.id AND status = 'done') as completed,
                            (SELECT COUNT(*) FROM tasks WHERE project_id = p.id AND due_date < CURDATE() AND status != 'done') as overdue
                     FROM projects p
                     ORDER BY p.status, p.name"
                );
            } else {
                $data = $db->fetchAll(
                    "SELECT p.name, p.status, p.priority, p.progress, p.start_date, p.end_date,
                            (SELECT COUNT(*) FROM project_members WHERE project_id = p.id) as members,
                            (SELECT COUNT(*) FROM tasks WHERE project_id = p.id) as total_tasks,
                            (SELECT COUNT(*) FROM tasks WHERE project_id = p.id AND status = 'done') as completed,
                            (SELECT COUNT(*) FROM tasks WHERE project_id = p.id AND due_date < CURDATE() AND status != 'done') as overdue
                     FROM projects p
                     JOIN project_members pm ON p.id = pm.project_id
                     WHERE pm.user_id = ?
                     ORDER BY p.status, p.name",
                    [$userId]
                );
            }
            $filename = 'tong_hop_du_an_' . date('Y-m-d');
            $title = 'Báo cáo Tổng hợp Dự án';
            $headers = ['Tên dự án', 'Trạng thái', 'Ưu tiên', 'Tiến độ (%)', 'Ngày bắt đầu', 'Ngày kết thúc', 'Thành viên', 'Tổng task', 'Hoàn thành', 'Quá hạn'];
            break;

        // ========================================
        // TEAM WORKLOAD - Khối lượng công việc team (Manager+)
        // ========================================
        case 'team-workload':
            if (!Permission::isManager($userRole)) {
                throw new Exception('Chỉ Manager trở lên mới có quyền xem báo cáo này');
            }
            
            if ($userRole === 'admin') {
                $data = $db->fetchAll(
                    "SELECT u.full_name, u.department, u.position,
                            COUNT(DISTINCT ta.task_id) as assigned,
                            SUM(CASE WHEN t.status = 'done' THEN 1 ELSE 0 END) as completed,
                            SUM(CASE WHEN t.status = 'in_progress' THEN 1 ELSE 0 END) as in_progress,
                            SUM(CASE WHEN t.due_date < CURDATE() AND t.status != 'done' THEN 1 ELSE 0 END) as overdue,
                            ROUND(SUM(CASE WHEN t.status = 'done' THEN 1 ELSE 0 END) * 100.0 / NULLIF(COUNT(DISTINCT ta.task_id), 0), 1) as completion_rate
                     FROM users u
                     LEFT JOIN task_assignees ta ON u.id = ta.user_id
                     LEFT JOIN tasks t ON ta.task_id = t.id
                     WHERE u.is_active = 1
                     GROUP BY u.id
                     ORDER BY completion_rate DESC, u.full_name"
                );
            } else {
                // Manager: only team members in their projects
                $data = $db->fetchAll(
                    "SELECT u.full_name, u.department, u.position,
                            COUNT(DISTINCT ta.task_id) as assigned,
                            SUM(CASE WHEN t.status = 'done' THEN 1 ELSE 0 END) as completed,
                            SUM(CASE WHEN t.status = 'in_progress' THEN 1 ELSE 0 END) as in_progress,
                            SUM(CASE WHEN t.due_date < CURDATE() AND t.status != 'done' THEN 1 ELSE 0 END) as overdue,
                            ROUND(SUM(CASE WHEN t.status = 'done' THEN 1 ELSE 0 END) * 100.0 / NULLIF(COUNT(DISTINCT ta.task_id), 0), 1) as completion_rate
                     FROM users u
                     JOIN project_members pm ON u.id = pm.user_id
                     LEFT JOIN task_assignees ta ON u.id = ta.user_id
                     LEFT JOIN tasks t ON ta.task_id = t.id
                     WHERE u.is_active = 1
                       AND pm.project_id IN (SELECT project_id FROM project_members WHERE user_id = ? AND role IN ('owner', 'manager'))
                     GROUP BY u.id
                     ORDER BY completion_rate DESC, u.full_name",
                    [$userId]
                );
            }
            $filename = 'hieu_suat_nhan_vien_' . date('Y-m-d');
            $title = 'Báo cáo Hiệu suất Nhân viên';
            $headers = ['Họ tên', 'Phòng ban', 'Chức vụ', 'Được giao', 'Hoàn thành', 'Đang làm', 'Quá hạn', 'Tỷ lệ (%)'];
            break;

        // ========================================
        // OVERDUE TASKS - Công việc quá hạn
        // ========================================
        case 'overdue-tasks':
            if ($userRole === 'admin') {
                $data = $db->fetchAll(
                    "SELECT t.title, t.due_date, DATEDIFF(CURDATE(), t.due_date) as days_overdue,
                            t.priority, p.name as project_name,
                            GROUP_CONCAT(u.full_name SEPARATOR ', ') as assignees
                     FROM tasks t
                     LEFT JOIN projects p ON t.project_id = p.id
                     LEFT JOIN task_assignees ta ON t.id = ta.task_id
                     LEFT JOIN users u ON ta.user_id = u.id
                     WHERE t.due_date < CURDATE() AND t.status != 'done'
                     GROUP BY t.id
                     ORDER BY days_overdue DESC"
                );
            } elseif (Permission::isManager($userRole)) {
                $data = $db->fetchAll(
                    "SELECT t.title, t.due_date, DATEDIFF(CURDATE(), t.due_date) as days_overdue,
                            t.priority, p.name as project_name,
                            GROUP_CONCAT(u.full_name SEPARATOR ', ') as assignees
                     FROM tasks t
                     JOIN projects p ON t.project_id = p.id
                     JOIN project_members pm ON p.id = pm.project_id
                     LEFT JOIN task_assignees ta ON t.id = ta.task_id
                     LEFT JOIN users u ON ta.user_id = u.id
                     WHERE t.due_date < CURDATE() AND t.status != 'done'
                       AND pm.user_id = ? AND pm.role IN ('owner', 'manager')
                     GROUP BY t.id
                     ORDER BY days_overdue DESC",
                    [$userId]
                );
            } else {
                $data = $db->fetchAll(
                    "SELECT t.title, t.due_date, DATEDIFF(CURDATE(), t.due_date) as days_overdue,
                            t.priority, p.name as project_name
                     FROM tasks t
                     LEFT JOIN projects p ON t.project_id = p.id
                     LEFT JOIN task_assignees ta ON t.id = ta.task_id
                     WHERE t.due_date < CURDATE() AND t.status != 'done'
                       AND (ta.user_id = ? OR t.created_by = ?)
                     ORDER BY days_overdue DESC",
                    [$userId, $userId]
                );
                $headers = ['Tiêu đề', 'Deadline', 'Số ngày quá hạn', 'Ưu tiên', 'Dự án'];
            }
            $filename = 'cong_viec_qua_han_' . date('Y-m-d');
            $title = 'Báo cáo Công việc Quá hạn';
            $headers = $headers ?? ['Tiêu đề', 'Deadline', 'Số ngày quá hạn', 'Ưu tiên', 'Dự án', 'Người thực hiện'];
            break;

        // ========================================
        // WEEKLY REPORT - Báo cáo tuần
        // ========================================
        case 'weekly-report':
            $startOfWeek = date('Y-m-d', strtotime('monday this week'));
            $endOfWeek = date('Y-m-d', strtotime('sunday this week'));
            
            $data = $db->fetchAll(
                "SELECT t.title, t.status, t.priority, t.due_date, p.name as project_name,
                        t.completed_at
                 FROM tasks t
                 LEFT JOIN task_assignees ta ON t.id = ta.task_id
                 LEFT JOIN projects p ON t.project_id = p.id
                 WHERE (ta.user_id = ? OR t.created_by = ?)
                   AND (t.due_date BETWEEN ? AND ? 
                        OR (t.completed_at BETWEEN ? AND ?))
                 ORDER BY t.status DESC, t.due_date ASC",
                [$userId, $userId, $startOfWeek, $endOfWeek, $startOfWeek . ' 00:00:00', $endOfWeek . ' 23:59:59']
            );
            $filename = 'bao_cao_tuan_' . date('Y-m-d');
            $title = 'Báo cáo Tuần (' . date('d/m', strtotime($startOfWeek)) . ' - ' . date('d/m/Y', strtotime($endOfWeek)) . ')';
            $headers = ['Tiêu đề', 'Trạng thái', 'Ưu tiên', 'Deadline', 'Dự án', 'Hoàn thành lúc'];
            break;

        default:
            throw new Exception('Loại báo cáo không hợp lệ');
    }

    // ========================================
    // EXPORT BASED ON FORMAT
    // ========================================
    
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
            ->setSubtitle('Xuất từ hệ thống TaskFlow')
            ->setOptions([
                'orientation' => count($headers) > 6 ? 'landscape' : 'portrait',
            ])
            ->output();
            
    } else {
        // CSV format
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
