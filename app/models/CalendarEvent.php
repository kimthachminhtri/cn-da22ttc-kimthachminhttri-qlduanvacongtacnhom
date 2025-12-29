<?php
/**
 * Calendar Event Model
 */

namespace App\Models;

class CalendarEvent extends BaseModel
{
    protected string $table = 'calendar_events';

    public function getByUser(string $userId, ?string $month = null): array
    {
        $sql = "SELECT ce.*, u.full_name as creator_name
                FROM {$this->table} ce
                LEFT JOIN users u ON ce.created_by = u.id
                WHERE ce.created_by = ?";
        $params = [$userId];
        
        if ($month) {
            $sql .= " AND DATE_FORMAT(ce.start_time, '%Y-%m') = ?";
            $params[] = $month;
        }
        
        $sql .= " ORDER BY ce.start_time ASC";
        
        return $this->db->fetchAll($sql, $params);
    }

    public function getByDateRange(string $userId, string $startDate, string $endDate): array
    {
        $sql = "SELECT ce.*, u.full_name as creator_name
                FROM {$this->table} ce
                LEFT JOIN users u ON ce.created_by = u.id
                WHERE ce.created_by = ?
                AND DATE(ce.start_time) >= ? AND DATE(ce.start_time) <= ?
                ORDER BY ce.start_time ASC";
        
        return $this->db->fetchAll($sql, [$userId, $startDate, $endDate]);
    }

    public function createEvent(array $data): string|false
    {
        $eventId = $this->generateUUID();
        $data['id'] = $eventId;
        
        $result = $this->create($data);
        return $result !== false ? $eventId : false;
    }

    public function getUpcoming(string $userId, int $limit = 5): array
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE created_by = ? AND start_time >= NOW()
                ORDER BY start_time ASC
                LIMIT ?";
        
        return $this->db->fetchAll($sql, [$userId, $limit]);
    }

    public function getTaskDeadlines(string $userId): array
    {
        $sql = "SELECT t.id, t.title, t.due_date as start_time, 
                'deadline' as type, '#ef4444' as color,
                p.name as project_name
                FROM tasks t
                JOIN task_assignees ta ON t.id = ta.task_id
                LEFT JOIN projects p ON t.project_id = p.id
                WHERE ta.user_id = ? AND t.due_date IS NOT NULL AND t.status != 'done'
                ORDER BY t.due_date ASC";
        
        return $this->db->fetchAll($sql, [$userId]);
    }

    public function getByDateRangeAll(string $startDate, string $endDate): array
    {
        $sql = "SELECT ce.*, u.full_name as creator_name
                FROM {$this->table} ce
                LEFT JOIN users u ON ce.created_by = u.id
                WHERE (DATE(ce.start_time) BETWEEN ? AND ?)
                OR (DATE(ce.end_time) BETWEEN ? AND ?)
                ORDER BY ce.start_time ASC";
        
        return $this->db->fetchAll($sql, [$startDate, $endDate, $startDate, $endDate]);
    }
}
