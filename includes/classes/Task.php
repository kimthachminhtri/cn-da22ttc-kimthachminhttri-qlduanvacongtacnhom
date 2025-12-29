<?php
/**
 * Task Model
 */

class Task extends Model
{
    protected string $table = 'tasks';

    /**
     * Get tasks with details
     */
    public function getAllWithDetails(): array
    {
        $sql = "SELECT t.*, 
                p.name as project_name, p.color as project_color,
                u.full_name as creator_name
                FROM {$this->table} t
                LEFT JOIN projects p ON t.project_id = p.id
                LEFT JOIN users u ON t.created_by = u.id
                ORDER BY t.position ASC, t.created_at DESC";
        
        $tasks = $this->db->fetchAll($sql);
        
        // Get assignees for each task
        foreach ($tasks as &$task) {
            $task['assignees'] = $this->getAssignees($task['id']);
        }
        
        return $tasks;
    }

    /**
     * Get task with full details
     */
    public function getWithDetails(string $taskId): ?array
    {
        $sql = "SELECT t.*, 
                p.name as project_name, p.color as project_color,
                u.full_name as creator_name, u.avatar_url as creator_avatar
                FROM {$this->table} t
                LEFT JOIN projects p ON t.project_id = p.id
                LEFT JOIN users u ON t.created_by = u.id
                WHERE t.id = ?";
        
        $task = $this->db->fetchOne($sql, [$taskId]);
        if (!$task) return null;

        $task['assignees'] = $this->getAssignees($taskId);
        $task['checklist'] = $this->getChecklist($taskId);
        $task['comments'] = $this->getComments($taskId);
        $task['labels'] = $this->getLabels($taskId);
        
        return $task;
    }

    /**
     * Get tasks by project
     */
    public function getByProject(string $projectId): array
    {
        $sql = "SELECT t.* FROM {$this->table} t
                WHERE t.project_id = ?
                ORDER BY t.position ASC, t.created_at DESC";
        
        $tasks = $this->db->fetchAll($sql, [$projectId]);
        
        foreach ($tasks as &$task) {
            $task['assignees'] = $this->getAssignees($task['id']);
        }
        
        return $tasks;
    }

    /**
     * Get tasks by status
     */
    public function getByStatus(string $status, ?string $projectId = null): array
    {
        $sql = "SELECT t.*, p.name as project_name, p.color as project_color
                FROM {$this->table} t
                LEFT JOIN projects p ON t.project_id = p.id
                WHERE t.status = ?";
        $params = [$status];
        
        if ($projectId) {
            $sql .= " AND t.project_id = ?";
            $params[] = $projectId;
        }
        
        $sql .= " ORDER BY t.position ASC";
        
        $tasks = $this->db->fetchAll($sql, $params);
        
        foreach ($tasks as &$task) {
            $task['assignees'] = $this->getAssignees($task['id']);
        }
        
        return $tasks;
    }

    /**
     * Get task assignees
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
     * Assign user to task
     */
    public function assignUser(string $taskId, string $userId, ?string $assignedBy = null): bool
    {
        $sql = "INSERT INTO task_assignees (task_id, user_id, assigned_by, assigned_at) 
                VALUES (?, ?, ?, NOW())
                ON DUPLICATE KEY UPDATE assigned_at = NOW()";
        $this->db->query($sql, [$taskId, $userId, $assignedBy]);
        return true;
    }

    /**
     * Unassign user from task
     */
    public function unassignUser(string $taskId, string $userId): int
    {
        return $this->db->delete('task_assignees', 'task_id = ? AND user_id = ?', [$taskId, $userId]);
    }

    /**
     * Get task checklist
     */
    public function getChecklist(string $taskId): array
    {
        $sql = "SELECT * FROM task_checklists WHERE task_id = ? ORDER BY position ASC";
        return $this->db->fetchAll($sql, [$taskId]);
    }

    /**
     * Add checklist item
     */
    public function addChecklistItem(string $taskId, string $title): string|false
    {
        return $this->db->insert('task_checklists', [
            'task_id' => $taskId,
            'title' => $title,
            'is_completed' => 0,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Toggle checklist item
     */
    public function toggleChecklistItem(string $itemId, ?string $userId = null): void
    {
        $sql = "UPDATE task_checklists 
                SET is_completed = NOT is_completed,
                    completed_at = IF(is_completed = 0, NOW(), NULL),
                    completed_by = IF(is_completed = 0, ?, NULL)
                WHERE id = ?";
        $this->db->query($sql, [$userId, $itemId]);
    }

    /**
     * Get task comments
     */
    public function getComments(string $taskId): array
    {
        $sql = "SELECT c.*, u.full_name as author_name, u.avatar_url as author_avatar
                FROM comments c
                JOIN users u ON c.created_by = u.id
                WHERE c.entity_type = 'task' AND c.entity_id = ?
                ORDER BY c.created_at ASC";
        return $this->db->fetchAll($sql, [$taskId]);
    }

    /**
     * Add comment
     */
    public function addComment(string $taskId, string $userId, string $content): string|false
    {
        // Generate UUID for comment
        $commentId = sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
        
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

    /**
     * Get task labels
     */
    public function getLabels(string $taskId): array
    {
        $sql = "SELECT l.* FROM labels l
                JOIN task_labels tl ON l.id = tl.label_id
                WHERE tl.task_id = ?";
        return $this->db->fetchAll($sql, [$taskId]);
    }

    /**
     * Update task status
     */
    public function updateStatus(string $taskId, string $status): void
    {
        $data = ['status' => $status];
        
        if ($status === 'done') {
            $data['completed_at'] = date('Y-m-d H:i:s');
        } else {
            $data['completed_at'] = null;
        }
        
        $this->update($taskId, $data);
    }

    /**
     * Get user's tasks
     */
    public function getUserTasks(string $userId, ?string $status = null): array
    {
        $sql = "SELECT t.*, p.name as project_name, p.color as project_color
                FROM {$this->table} t
                JOIN task_assignees ta ON t.id = ta.task_id
                LEFT JOIN projects p ON t.project_id = p.id
                WHERE ta.user_id = ?";
        $params = [$userId];
        
        if ($status) {
            $sql .= " AND t.status = ?";
            $params[] = $status;
        }
        
        $sql .= " ORDER BY t.due_date ASC, t.priority DESC";
        
        return $this->db->fetchAll($sql, $params);
    }

    /**
     * Get overdue tasks
     */
    public function getOverdue(?string $userId = null): array
    {
        $sql = "SELECT t.*, p.name as project_name, p.color as project_color
                FROM {$this->table} t
                LEFT JOIN projects p ON t.project_id = p.id
                WHERE t.due_date < CURDATE() AND t.status != 'done'";
        $params = [];
        
        if ($userId) {
            $sql .= " AND EXISTS (SELECT 1 FROM task_assignees ta WHERE ta.task_id = t.id AND ta.user_id = ?)";
            $params[] = $userId;
        }
        
        $sql .= " ORDER BY t.due_date ASC";
        
        return $this->db->fetchAll($sql, $params);
    }

    /**
     * Get upcoming tasks (due in next N days)
     */
    public function getUpcoming(int $days = 7, ?string $userId = null): array
    {
        $sql = "SELECT t.*, p.name as project_name, p.color as project_color
                FROM {$this->table} t
                LEFT JOIN projects p ON t.project_id = p.id
                WHERE t.due_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL ? DAY)
                AND t.status != 'done'";
        $params = [$days];
        
        if ($userId) {
            $sql .= " AND EXISTS (SELECT 1 FROM task_assignees ta WHERE ta.task_id = t.id AND ta.user_id = ?)";
            $params[] = $userId;
        }
        
        $sql .= " ORDER BY t.due_date ASC";
        
        return $this->db->fetchAll($sql, $params);
    }
}
