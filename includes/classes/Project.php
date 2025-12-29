<?php
/**
 * Project Model
 */

class Project extends Model
{
    protected string $table = 'projects';

    /**
     * Get projects with stats
     */
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

    /**
     * Get project with full details
     */
    public function getWithDetails(string $projectId): ?array
    {
        $project = $this->find($projectId);
        if (!$project) return null;

        // Get members
        $project['members'] = $this->getMembers($projectId);
        
        // Get task stats
        $project['task_stats'] = $this->getTaskStats($projectId);
        
        return $project;
    }

    /**
     * Get project members
     */
    public function getMembers(string $projectId): array
    {
        $sql = "SELECT u.*, pm.role as project_role, pm.joined_at
                FROM users u
                JOIN project_members pm ON u.id = pm.user_id
                WHERE pm.project_id = ?
                ORDER BY pm.joined_at ASC";
        return $this->db->fetchAll($sql, [$projectId]);
    }

    /**
     * Add member to project
     */
    public function addMember(string $projectId, string $userId, string $role = 'member'): bool
    {
        $sql = "INSERT INTO project_members (project_id, user_id, role, joined_at) 
                VALUES (?, ?, ?, NOW())
                ON DUPLICATE KEY UPDATE role = ?";
        $this->db->query($sql, [$projectId, $userId, $role, $role]);
        return true;
    }

    /**
     * Remove member from project
     */
    public function removeMember(string $projectId, string $userId): int
    {
        return $this->db->delete('project_members', 'project_id = ? AND user_id = ?', [$projectId, $userId]);
    }

    /**
     * Get task statistics
     */
    public function getTaskStats(string $projectId): array
    {
        $sql = "SELECT 
                    status,
                    COUNT(*) as count
                FROM tasks 
                WHERE project_id = ?
                GROUP BY status";
        $results = $this->db->fetchAll($sql, [$projectId]);
        
        $stats = [
            'backlog' => 0,
            'todo' => 0,
            'in_progress' => 0,
            'in_review' => 0,
            'done' => 0,
            'total' => 0,
        ];
        
        foreach ($results as $row) {
            $stats[$row['status']] = (int) $row['count'];
            $stats['total'] += (int) $row['count'];
        }
        
        return $stats;
    }

    /**
     * Get projects by status
     */
    public function getByStatus(string $status): array
    {
        return $this->findAllBy('status', $status, 'updated_at DESC');
    }

    /**
     * Update project progress
     */
    public function updateProgress(string $projectId): void
    {
        $stats = $this->getTaskStats($projectId);
        $progress = $stats['total'] > 0 
            ? round(($stats['done'] / $stats['total']) * 100) 
            : 0;
        
        $this->update($projectId, ['progress' => $progress]);
    }

    /**
     * Get user's projects
     */
    public function getUserProjects(string $userId): array
    {
        $sql = "SELECT p.* FROM {$this->table} p
                JOIN project_members pm ON p.id = pm.project_id
                WHERE pm.user_id = ?
                ORDER BY p.updated_at DESC";
        return $this->db->fetchAll($sql, [$userId]);
    }
}
