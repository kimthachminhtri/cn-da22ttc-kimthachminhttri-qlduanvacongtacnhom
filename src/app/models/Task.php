<?php

declare(strict_types=1);

/**
 * Task Model
 * 
 * Model quản lý tasks trong hệ thống.
 * 
 * @package App\Models
 */

namespace App\Models;

class Task extends BaseModel
{
    protected string $table = 'tasks';

    /**
     * Lấy tasks theo project với assignees (optimized - tránh N+1)
     * 
     * @param string $projectId
     * @return array<int, array<string, mixed>>
     */
    public function getByProject(string $projectId): array
    {
        // Optimized query to avoid N+1 problem
        // Get tasks with assignees in a single query using GROUP_CONCAT
        $sql = "SELECT t.*, 
                GROUP_CONCAT(DISTINCT ta.user_id) as assignee_ids,
                GROUP_CONCAT(DISTINCT u.full_name SEPARATOR ', ') as assignee_names,
                GROUP_CONCAT(DISTINCT CONCAT(u.id, ':', u.full_name, ':', COALESCE(u.avatar_url, '')) SEPARATOR '|') as assignee_data
                FROM {$this->table} t
                LEFT JOIN task_assignees ta ON t.id = ta.task_id
                LEFT JOIN users u ON ta.user_id = u.id
                WHERE t.project_id = ?
                GROUP BY t.id
                ORDER BY t.position ASC, t.created_at DESC";
        
        $tasks = $this->db->fetchAll($sql, [$projectId]);
        
        // Parse assignee_data into structured array
        foreach ($tasks as &$task) {
            $task['assignees'] = [];
            if (!empty($task['assignee_data'])) {
                $assigneeStrings = explode('|', $task['assignee_data']);
                foreach ($assigneeStrings as $str) {
                    $parts = explode(':', $str, 3);
                    if (count($parts) >= 2) {
                        $task['assignees'][] = [
                            'id' => $parts[0],
                            'full_name' => $parts[1],
                            'avatar_url' => $parts[2] ?? ''
                        ];
                    }
                }
            }
            unset($task['assignee_data']);
        }
        
        return $tasks;
    }

    /**
     * Lấy task với đầy đủ chi tiết (assignees, checklist, comments)
     * 
     * @param string $taskId
     * @return array<string, mixed>|null
     */
    public function getWithDetails(string $taskId): ?array
    {
        $task = $this->find($taskId);
        if (!$task) return null;

        $task['assignees'] = $this->getAssignees($taskId);
        $task['checklist'] = $this->getChecklist($taskId);
        $task['comments'] = $this->getComments($taskId);
        
        return $task;
    }

    /**
     * Lấy danh sách assignees của task
     * 
     * @param string $taskId
     * @return array<int, array<string, mixed>>
     */
    public function getAssignees(string $taskId): array
    {
        $sql = "SELECT u.id, u.full_name, u.email, u.avatar_url
                FROM users u
                JOIN task_assignees ta ON u.id = ta.user_id
                WHERE ta.task_id = ?";
        return $this->db->fetchAll($sql, [$taskId]);
    }

    /**
     * Gán user vào task
     * 
     * @param string $taskId
     * @param string $userId
     * @param string $assignedBy
     * @return bool
     */
    public function assignUser(string $taskId, string $userId, string $assignedBy): bool
    {
        $sql = "INSERT INTO task_assignees (task_id, user_id, assigned_by, assigned_at) 
                VALUES (?, ?, ?, NOW())
                ON DUPLICATE KEY UPDATE assigned_at = NOW()";
        $this->db->query($sql, [$taskId, $userId, $assignedBy]);
        return true;
    }

    /**
     * Bỏ gán user khỏi task
     * 
     * @param string $taskId
     * @param string $userId
     * @return int Số rows bị xóa
     */
    public function unassignUser(string $taskId, string $userId): int
    {
        return $this->db->delete('task_assignees', 'task_id = ? AND user_id = ?', [$taskId, $userId]);
    }

    /**
     * Lấy checklist của task
     * 
     * @param string $taskId
     * @return array<int, array<string, mixed>>
     */
    public function getChecklist(string $taskId): array
    {
        $sql = "SELECT * FROM task_checklists WHERE task_id = ? ORDER BY position ASC";
        return $this->db->fetchAll($sql, [$taskId]);
    }

    /**
     * Lấy comments của task
     * 
     * @param string $taskId
     * @return array<int, array<string, mixed>>
     */
    public function getComments(string $taskId): array
    {
        // Lấy comments mới nhất trước (DESC)
        // Replies sẽ được sắp xếp theo thời gian tăng dần trong view
        $sql = "SELECT c.*, u.full_name, u.avatar_url
                FROM comments c
                JOIN users u ON c.created_by = u.id
                WHERE c.entity_type = 'task' AND c.entity_id = ?
                ORDER BY c.created_at DESC";
        return $this->db->fetchAll($sql, [$taskId]);
    }

    /**
     * Lấy tasks được gán cho user hoặc do user tạo
     * Nếu là Manager/Admin: lấy thêm tasks trong các dự án họ quản lý
     * 
     * @param string $userId
     * @param string $userRole
     * @return array<int, array<string, mixed>>
     */
    public function getUserTasks(string $userId, string $userRole = 'member'): array
    {
        // Manager/Admin: lấy tất cả tasks trong dự án họ quản lý
        if (in_array($userRole, ['admin', 'manager'])) {
            $sql = "SELECT DISTINCT t.*, p.name as project_name, p.color as project_color
                    FROM {$this->table} t
                    LEFT JOIN projects p ON t.project_id = p.id
                    LEFT JOIN project_members pm ON p.id = pm.project_id
                    LEFT JOIN task_assignees ta ON t.id = ta.task_id
                    WHERE ta.user_id = ? 
                       OR t.created_by = ?
                       OR (pm.user_id = ? AND pm.role IN ('owner', 'manager'))
                    ORDER BY t.due_date ASC, t.priority DESC";
            return $this->db->fetchAll($sql, [$userId, $userId, $userId]);
        }
        
        // Member: chỉ lấy tasks được gán hoặc tự tạo
        $sql = "SELECT DISTINCT t.*, p.name as project_name, p.color as project_color
                FROM {$this->table} t
                LEFT JOIN task_assignees ta ON t.id = ta.task_id
                LEFT JOIN projects p ON t.project_id = p.id
                WHERE ta.user_id = ? OR t.created_by = ?
                ORDER BY t.due_date ASC, t.priority DESC";
        return $this->db->fetchAll($sql, [$userId, $userId]);
    }

    /**
     * Lấy tasks theo status
     * 
     * @param string $status
     * @return array<int, array<string, mixed>>
     */
    public function getByStatus(string $status): array
    {
        return $this->findAllBy('status', $status, 'position ASC');
    }

    /**
     * Lấy tasks trong khoảng thời gian (cho Calendar/Gantt)
     * 
     * @param string $userId
     * @param string $startDate Format: Y-m-d
     * @param string $endDate Format: Y-m-d
     * @return array<int, array<string, mixed>>
     */
    public function getByDateRange(string $userId, string $startDate, string $endDate): array
    {
        // Get tasks that have due_date in range OR created_at in range (for Gantt)
        $sql = "SELECT t.*, p.name as project_name, p.color as project_color,
                DATE(t.created_at) as start_date
                FROM {$this->table} t
                LEFT JOIN task_assignees ta ON t.id = ta.task_id
                LEFT JOIN projects p ON t.project_id = p.id
                WHERE (
                    t.due_date BETWEEN ? AND ?
                    OR (DATE(t.created_at) BETWEEN ? AND ? AND t.due_date IS NOT NULL)
                )
                AND (ta.user_id = ? OR t.created_by = ?)
                GROUP BY t.id
                ORDER BY t.due_date ASC";
        return $this->db->fetchAll($sql, [$startDate, $endDate, $startDate, $endDate, $userId, $userId]);
    }

    public function addComment(string $taskId, string $userId, string $content): string|false
    {
        $commentId = $this->generateUUID();
        
        $this->db->insert('comments', [
            'id' => $commentId,
            'entity_type' => 'task',
            'entity_id' => $taskId,
            'content' => $content,
            'created_by' => $userId,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        
        return $commentId;
    }

    public function addChecklistItem(string $taskId, string $title): string|false
    {
        $itemId = $this->generateUUID();
        $position = $this->db->fetchColumn(
            "SELECT COALESCE(MAX(position), 0) + 1 FROM task_checklists WHERE task_id = ?",
            [$taskId]
        );
        
        $this->db->insert('task_checklists', [
            'id' => $itemId,
            'task_id' => $taskId,
            'title' => $title,
            'is_completed' => 0,
            'position' => $position,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
        
        return $itemId;
    }

    public function toggleChecklistItem(string $itemId, string $userId): bool
    {
        $item = $this->db->fetchOne("SELECT * FROM task_checklists WHERE id = ?", [$itemId]);
        if (!$item) return false;
        
        $newStatus = $item['is_completed'] ? 0 : 1;
        
        $this->db->update('task_checklists', [
            'is_completed' => $newStatus,
            'completed_at' => $newStatus ? date('Y-m-d H:i:s') : null,
            'completed_by' => $newStatus ? $userId : null,
        ], 'id = ?', [$itemId]);
        
        return true;
    }

    public function getTasksForGantt(string $userId): array
    {
        // Get tasks with due_date for Gantt chart (current and future tasks)
        $sql = "SELECT t.*, p.name as project_name, p.color as project_color,
                DATE(t.created_at) as start_date
                FROM {$this->table} t
                LEFT JOIN task_assignees ta ON t.id = ta.task_id
                LEFT JOIN projects p ON t.project_id = p.id
                WHERE t.due_date IS NOT NULL
                AND t.due_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
                AND (ta.user_id = ? OR t.created_by = ?)
                GROUP BY t.id
                ORDER BY t.due_date ASC
                LIMIT 20";
        return $this->db->fetchAll($sql, [$userId, $userId]);
    }

    /**
     * Get all tasks for projects managed by user (for Manager dashboard)
     */
    public function getTeamTasks(string $managerId): array
    {
        $sql = "SELECT t.*, p.name as project_name, p.color as project_color,
                GROUP_CONCAT(DISTINCT u.full_name) as assignee_names,
                GROUP_CONCAT(DISTINCT u.id) as assignee_ids
                FROM {$this->table} t
                JOIN projects p ON t.project_id = p.id
                JOIN project_members pm ON p.id = pm.project_id
                LEFT JOIN task_assignees ta ON t.id = ta.task_id
                LEFT JOIN users u ON ta.user_id = u.id
                WHERE pm.user_id = ? AND pm.role IN ('owner', 'manager')
                GROUP BY t.id
                ORDER BY t.due_date ASC, t.priority DESC";
        return $this->db->fetchAll($sql, [$managerId]);
    }

    /**
     * Get overdue tasks grouped by assignee
     */
    public function getOverdueTasksByAssignee(string $managerId): array
    {
        $sql = "SELECT u.id as user_id, u.full_name, u.avatar_url,
                t.id, t.title, t.due_date, t.priority, t.status,
                p.name as project_name, p.color as project_color,
                DATEDIFF(CURDATE(), t.due_date) as days_overdue
                FROM {$this->table} t
                JOIN projects p ON t.project_id = p.id
                JOIN project_members pm ON p.id = pm.project_id
                JOIN task_assignees ta ON t.id = ta.task_id
                JOIN users u ON ta.user_id = u.id
                WHERE pm.user_id = ? AND pm.role IN ('owner', 'manager')
                AND t.due_date < CURDATE()
                AND t.status != 'done'
                ORDER BY u.full_name, t.due_date ASC";
        return $this->db->fetchAll($sql, [$managerId]);
    }

    /**
     * Get task statistics for Manager
     */
    public function getTeamTaskStats(string $managerId): array
    {
        $sql = "SELECT 
                COUNT(DISTINCT t.id) as total_tasks,
                COUNT(DISTINCT CASE WHEN t.status = 'done' THEN t.id END) as completed_tasks,
                COUNT(DISTINCT CASE WHEN t.status = 'in_progress' THEN t.id END) as in_progress_tasks,
                COUNT(DISTINCT CASE WHEN t.status = 'todo' THEN t.id END) as todo_tasks,
                COUNT(DISTINCT CASE WHEN t.due_date < CURDATE() AND t.status != 'done' THEN t.id END) as overdue_tasks,
                COUNT(DISTINCT CASE WHEN t.due_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY) AND t.status != 'done' THEN t.id END) as due_this_week
                FROM {$this->table} t
                JOIN projects p ON t.project_id = p.id
                JOIN project_members pm ON p.id = pm.project_id
                WHERE pm.user_id = ? AND pm.role IN ('owner', 'manager')";
        return $this->db->fetchOne($sql, [$managerId]) ?: [
            'total_tasks' => 0, 'completed_tasks' => 0, 'in_progress_tasks' => 0,
            'todo_tasks' => 0, 'overdue_tasks' => 0, 'due_this_week' => 0
        ];
    }

    /**
     * Get tasks due this week for team
     */
    public function getTeamTasksDueThisWeek(string $managerId): array
    {
        $sql = "SELECT t.*, p.name as project_name, p.color as project_color,
                GROUP_CONCAT(DISTINCT u.full_name) as assignee_names
                FROM {$this->table} t
                JOIN projects p ON t.project_id = p.id
                JOIN project_members pm ON p.id = pm.project_id
                LEFT JOIN task_assignees ta ON t.id = ta.task_id
                LEFT JOIN users u ON ta.user_id = u.id
                WHERE pm.user_id = ? AND pm.role IN ('owner', 'manager')
                AND t.due_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)
                AND t.status != 'done'
                GROUP BY t.id
                ORDER BY t.due_date ASC";
        return $this->db->fetchAll($sql, [$managerId]);
    }
}
