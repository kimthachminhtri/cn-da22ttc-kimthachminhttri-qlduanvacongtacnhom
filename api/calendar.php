<?php
/**
 * API: Calendar Events
 * CRUD cho sự kiện lịch + lấy task deadlines
 */

require_once __DIR__ . '/../includes/config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];
$currentUserId = $_SESSION['user_id'];

try {
    $db = Database::getInstance();
    
    switch ($method) {
        case 'GET':
            $action = $_GET['action'] ?? 'events';
            
            // Get single event
            if ($action === 'single') {
                $eventId = $_GET['event_id'] ?? null;
                if (!$eventId) {
                    throw new Exception('event_id is required');
                }
                
                $event = $db->fetchOne(
                    "SELECT ce.*, u.full_name as creator_name
                     FROM calendar_events ce
                     LEFT JOIN users u ON ce.created_by = u.id
                     WHERE ce.id = ?",
                    [$eventId]
                );
                
                if (!$event) {
                    throw new Exception('Event not found');
                }
                
                echo json_encode(['success' => true, 'data' => $event]);
                break;
            }
            
            $startDate = $_GET['start'] ?? date('Y-m-01');
            $endDate = $_GET['end'] ?? date('Y-m-t');
            
            $events = [];
            
            if ($action === 'all' || $action === 'events') {
                // Lấy calendar events - sử dụng start_time theo schema database
                $calendarEvents = $db->fetchAll(
                    "SELECT ce.*, u.full_name as creator_name
                     FROM calendar_events ce
                     LEFT JOIN users u ON ce.created_by = u.id
                     WHERE DATE(ce.start_time) BETWEEN ? AND ?
                     OR DATE(ce.end_time) BETWEEN ? AND ?
                     ORDER BY ce.start_time ASC",
                    [$startDate, $endDate, $startDate, $endDate]
                );
                
                foreach ($calendarEvents as $event) {
                    $events[] = [
                        'id' => $event['id'],
                        'title' => $event['title'],
                        'description' => $event['description'],
                        'start' => $event['start_time'],
                        'end' => $event['end_time'],
                        'all_day' => (bool)$event['is_all_day'],
                        'color' => $event['color'] ?? '#6366f1',
                        'type' => $event['type'] ?? 'event',
                        'location' => $event['location'],
                        'creator' => $event['creator_name'],
                    ];
                }
            }
            
            if ($action === 'all' || $action === 'tasks') {
                // Lấy task deadlines
                $taskDeadlines = $db->fetchAll(
                    "SELECT t.id, t.title, t.due_date, t.status, t.priority,
                            p.name as project_name, p.color as project_color
                     FROM tasks t
                     LEFT JOIN projects p ON t.project_id = p.id
                     WHERE t.due_date BETWEEN ? AND ?
                     AND t.status != 'done'
                     ORDER BY t.due_date ASC",
                    [$startDate, $endDate]
                );
                
                foreach ($taskDeadlines as $task) {
                    $events[] = [
                        'id' => 'task-' . $task['id'],
                        'task_id' => $task['id'],
                        'title' => $task['title'],
                        'start' => $task['due_date'],
                        'end' => $task['due_date'],
                        'all_day' => true,
                        'color' => $task['project_color'] ?? '#ef4444',
                        'type' => 'task',
                        'status' => $task['status'],
                        'priority' => $task['priority'],
                        'project' => $task['project_name'],
                    ];
                }
            }
            
            // Sort by date
            usort($events, fn($a, $b) => strtotime($a['start']) - strtotime($b['start']));
            
            echo json_encode([
                'success' => true,
                'data' => $events
            ]);
            break;
            
        case 'POST':
            // Tạo sự kiện mới
            $input = json_decode(file_get_contents('php://input'), true);
            
            $title = trim($input['title'] ?? '');
            $description = trim($input['description'] ?? '');
            // Hỗ trợ cả start_date và start_time
            $startTime = $input['start_time'] ?? $input['start_date'] ?? null;
            $endTime = $input['end_time'] ?? $input['end_date'] ?? $startTime;
            $isAllDay = $input['is_all_day'] ?? true;
            $color = $input['color'] ?? '#6366f1';
            $location = trim($input['location'] ?? '');
            $type = $input['type'] ?? 'event';
            $projectId = $input['project_id'] ?? null;
            
            if (empty($title)) {
                throw new Exception('title is required');
            }
            if (empty($startTime)) {
                throw new Exception('start_time is required');
            }
            
            // Generate UUID
            $eventId = sprintf(
                '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
                mt_rand(0, 0xffff), mt_rand(0, 0xffff),
                mt_rand(0, 0xffff),
                mt_rand(0, 0x0fff) | 0x4000,
                mt_rand(0, 0x3fff) | 0x8000,
                mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
            );
            
            $db->insert('calendar_events', [
                'id' => $eventId,
                'title' => $title,
                'description' => $description,
                'type' => $type,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'is_all_day' => $isAllDay ? 1 : 0,
                'color' => $color,
                'location' => $location ?: null,
                'project_id' => $projectId ?: null,
                'created_by' => $currentUserId,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            
            echo json_encode([
                'success' => true,
                'message' => 'Đã tạo sự kiện',
                'data' => ['id' => $eventId]
            ]);
            break;
            
        case 'PUT':
            // Cập nhật sự kiện
            $input = json_decode(file_get_contents('php://input'), true);
            
            $eventId = $input['event_id'] ?? null;
            if (!$eventId) {
                throw new Exception('event_id is required');
            }
            
            // Check exists
            $event = $db->fetchOne("SELECT * FROM calendar_events WHERE id = ?", [$eventId]);
            if (!$event) {
                throw new Exception('Event not found');
            }
            
            // Check permission - only creator or admin can edit
            $userRole = $_SESSION['user_role'] ?? 'member';
            if ($event['created_by'] !== $currentUserId && $userRole !== 'admin') {
                throw new Exception('Bạn không có quyền chỉnh sửa sự kiện này');
            }
            
            $updateData = [];
            if (isset($input['title'])) $updateData['title'] = trim($input['title']);
            if (isset($input['description'])) $updateData['description'] = trim($input['description']);
            if (isset($input['type'])) $updateData['type'] = $input['type'];
            // Hỗ trợ cả start_date và start_time
            if (isset($input['start_time'])) $updateData['start_time'] = $input['start_time'];
            elseif (isset($input['start_date'])) $updateData['start_time'] = $input['start_date'];
            if (isset($input['end_time'])) $updateData['end_time'] = $input['end_time'];
            elseif (isset($input['end_date'])) $updateData['end_time'] = $input['end_date'];
            if (isset($input['is_all_day'])) $updateData['is_all_day'] = $input['is_all_day'] ? 1 : 0;
            if (isset($input['color'])) $updateData['color'] = $input['color'];
            if (isset($input['location'])) $updateData['location'] = trim($input['location']) ?: null;
            
            if (!empty($updateData)) {
                $updateData['updated_at'] = date('Y-m-d H:i:s');
                $db->update('calendar_events', $updateData, 'id = ?', [$eventId]);
            }
            
            echo json_encode([
                'success' => true,
                'message' => 'Đã cập nhật sự kiện'
            ]);
            break;
            
        case 'DELETE':
            $eventId = $_GET['event_id'] ?? null;
            if (!$eventId) {
                throw new Exception('event_id is required');
            }
            
            // Check exists and permission
            $event = $db->fetchOne("SELECT * FROM calendar_events WHERE id = ?", [$eventId]);
            if (!$event) {
                throw new Exception('Event not found');
            }
            
            // Check permission - only creator or admin can delete
            $userRole = $_SESSION['user_role'] ?? 'member';
            if ($event['created_by'] !== $currentUserId && $userRole !== 'admin') {
                throw new Exception('Bạn không có quyền xóa sự kiện này');
            }
            
            $db->delete('calendar_events', 'id = ?', [$eventId]);
            
            echo json_encode([
                'success' => true,
                'message' => 'Đã xóa sự kiện'
            ]);
            break;
            
        default:
            http_response_code(405);
            echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    }
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
