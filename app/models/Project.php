<?php
/**
 * Project Model
 */

namespace App\Models;

class Project extends BaseModel
{
    protected string $table = 'projects';

    public function getAllWithStats(): array
    {
        $sql = "SELECT p.*, 
                (SELECT COUNT(*) FROM tasks t WHERE t.project_id = p.id) as total_tasks,
                (SELECT COUNT(*) FROM tasks t WHERE t.project_id = p.id AND t.status = 'done') as completed_tasks,
                (SELECT COUNT(DISTINCT pm.user_id) FROM project_members pm WHERE pm.project_id = p.id) as member_count
                FROM {$this->table} p 
                ORDER BY p.updated_at DESC";
        return $this->db->fetchAll($sql);
    }

    public function getWithDetails(string $projectId): ?array
    {
        $project = $this->find($projectId);
        if (!$project) return null;

        $project['members'] = $this->getMembers($projectId);
        $project['task_stats'] = $this->getTaskStats($projectId);
        
        return $project;
    }

    public function getMembers(string $projectId): array
    {
        $sql = "SELECT u.*, pm.role as project_role, pm.joined_at
                FROM users u
                JOIN project_members pm ON u.id = pm.user_id
                WHERE pm.project_id = ?
                ORDER BY pm.joined_at ASC";
        return $this->db->fetchAll($sql, [$projectId]);
    }

    public function addMember(string $projectId, string $userId, string $role = 'member'): bool
    {
        $sql = "INSERT INTO project_members (project_id, user_id, role, joined_at) 
                VALUES (?, ?, ?, NOW())
                ON DUPLICATE KEY UPDATE role = ?";
        $this->db->query($sql, [$projectId, $userId, $role, $role]);
        return true;
    }

    public function removeMember(string $projectId, string $userId): int
    {
        return $this->db->delete('project_members', 'project_id = ? AND user_id = ?', [$projectId, $userId]);
    }

    public function getTaskStats(string $projectId): array
    {
        $sql = "SELECT status, COUNT(*) as count FROM tasks WHERE project_id = ? GROUP BY status";
        $results = $this->db->fetchAll($sql, [$projectId]);
        
        $stats = ['backlog' => 0, 'todo' => 0, 'in_progress' => 0, 'in_review' => 0, 'done' => 0, 'total' => 0];
        
        foreach ($results as $row) {
            $stats[$row['status']] = (int) $row['count'];
            $stats['total'] += (int) $row['count'];
        }
        
        return $stats;
    }

    public function getUserProjects(string $userId): array
    {
        $sql = "SELECT p.* FROM {$this->table} p
                JOIN project_members pm ON p.id = pm.project_id
                WHERE pm.user_id = ?
                ORDER BY p.updated_at DESC";
        return $this->db->fetchAll($sql, [$userId]);
    }

    public function getUserProjectsWithStats(string $userId): array
    {
        $sql = "SELECT p.*, 
                (SELECT COUNT(*) FROM tasks t WHERE t.project_id = p.id) as total_tasks,
                (SELECT COUNT(*) FROM tasks t WHERE t.project_id = p.id AND t.status = 'done') as completed_tasks,
                (SELECT COUNT(DISTINCT pm2.user_id) FROM project_members pm2 WHERE pm2.project_id = p.id) as member_count
                FROM {$this->table} p
                JOIN project_members pm ON p.id = pm.project_id
                WHERE pm.user_id = ?
                ORDER BY p.updated_at DESC";
        return $this->db->fetchAll($sql, [$userId]);
    }

    public function getUserProjectIds(string $userId): array
    {
        $sql = "SELECT project_id FROM project_members WHERE user_id = ?";
        $results = $this->db->fetchAll($sql, [$userId]);
        return array_column($results, 'project_id');
    }

    /**
     * Get projects where user is owner or manager
     */
    public function getManagedProjects(string $managerId): array
    {
        $sql = "SELECT p.*, pm.role as my_role,
                (SELECT COUNT(*) FROM tasks t WHERE t.project_id = p.id) as total_tasks,
                (SELECT COUNT(*) FROM tasks t WHERE t.project_id = p.id AND t.status = 'done') as completed_tasks,
                (SELECT COUNT(*) FROM tasks t WHERE t.project_id = p.id AND t.due_date < CURDATE() AND t.status != 'done') as overdue_tasks,
                (SELECT COUNT(DISTINCT pm2.user_id) FROM project_members pm2 WHERE pm2.project_id = p.id) as member_count,
                ROUND((SELECT COUNT(*) FROM tasks t WHERE t.project_id = p.id AND t.status = 'done') * 100.0 / 
                      NULLIF((SELECT COUNT(*) FROM tasks t WHERE t.project_id = p.id), 0), 0) as progress
                FROM {$this->table} p
                JOIN project_members pm ON p.id = pm.project_id
                WHERE pm.user_id = ? AND pm.role IN ('owner', 'manager')
                ORDER BY p.updated_at DESC";
        return $this->db->fetchAll($sql, [$managerId]);
    }

    /**
     * Get project statistics for Manager dashboard
     */
    public function getManagerProjectStats(string $managerId): array
    {
        $sql = "SELECT 
                COUNT(DISTINCT p.id) as total_projects,
                COUNT(DISTINCT CASE WHEN p.status = 'active' THEN p.id END) as active_projects,
                COUNT(DISTINCT CASE WHEN p.status = 'completed' THEN p.id END) as completed_projects,
                COUNT(DISTINCT CASE WHEN p.status = 'on_hold' THEN p.id END) as on_hold_projects
                FROM {$this->table} p
                JOIN project_members pm ON p.id = pm.project_id
                WHERE pm.user_id = ? AND pm.role IN ('owner', 'manager')";
        return $this->db->fetchOne($sql, [$managerId]) ?: [
            'total_projects' => 0, 'active_projects' => 0, 
            'completed_projects' => 0, 'on_hold_projects' => 0
        ];
    }
}
