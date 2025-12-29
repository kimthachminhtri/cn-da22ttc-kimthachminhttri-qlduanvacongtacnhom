<?php
/**
 * User Model
 */

namespace App\Models;

class User extends BaseModel
{
    protected string $table = 'users';

    public function findByEmail(string $email): ?array
    {
        return $this->findBy('email', $email);
    }

    public function getActive(): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE is_active = 1 ORDER BY full_name ASC";
        return $this->db->fetchAll($sql);
    }

    public function createUser(array $data): string|false
    {
        if (isset($data['password'])) {
            $data['password_hash'] = password_hash($data['password'], PASSWORD_DEFAULT);
            unset($data['password']);
        }
        
        $userId = $this->generateUUID();
        $data['id'] = $userId;
        
        // Remove timestamps - let BaseModel handle them
        unset($data['created_at'], $data['updated_at']);
        
        try {
            $this->create($data);
            return $userId;
        } catch (\Exception $e) {
            error_log("Create user error: " . $e->getMessage());
            return false;
        }
    }

    public function verifyPassword(string $email, string $password): ?array
    {
        $user = $this->findByEmail($email);
        if ($user && password_verify($password, $user['password_hash'])) {
            return $user;
        }
        return null;
    }

    public function updateLastLogin(string $userId): void
    {
        $sql = "UPDATE {$this->table} SET last_login_at = NOW() WHERE id = ?";
        $this->db->query($sql, [$userId]);
    }

    public function getAllWithTaskCounts(): array
    {
        $sql = "SELECT u.*, 
                (SELECT COUNT(*) FROM task_assignees ta WHERE ta.user_id = u.id) as task_count
                FROM {$this->table} u 
                WHERE u.is_active = 1 
                ORDER BY u.full_name ASC";
        return $this->db->fetchAll($sql);
    }

    public function getUserSettings(string $userId): array
    {
        $sql = "SELECT * FROM user_settings WHERE user_id = ?";
        $row = $this->db->fetchOne($sql, [$userId]);
        
        $defaults = [
            'theme' => 'system',
            'language' => 'vi',
            'timezone' => 'Asia/Ho_Chi_Minh',
            'notification_email' => '1',
            'notification_push' => '1',
            'notification_task_assigned' => '1',
            'notification_task_due' => '1',
            'notification_comment' => '1',
            'notification_mention' => '1',
        ];
        
        return $row ? array_merge($defaults, $row) : $defaults;
    }

    public function updateUserSettings(string $userId, array $settings): bool
    {
        // Check if settings exist
        $existing = $this->db->fetchOne("SELECT user_id FROM user_settings WHERE user_id = ?", [$userId]);
        
        if ($existing) {
            $this->db->update('user_settings', $settings, 'user_id = ?', [$userId]);
        } else {
            $settings['user_id'] = $userId;
            $this->db->insert('user_settings', $settings);
        }
        
        return true;
    }

    public function changePassword(string $userId, string $currentPassword, string $newPassword): array
    {
        $user = $this->find($userId);
        
        if (!$user) {
            return ['success' => false, 'message' => 'Người dùng không tồn tại'];
        }
        
        if (!password_verify($currentPassword, $user['password_hash'])) {
            return ['success' => false, 'message' => 'Mật khẩu hiện tại không đúng'];
        }
        
        $this->update($userId, [
            'password_hash' => password_hash($newPassword, PASSWORD_DEFAULT)
        ]);
        
        return ['success' => true, 'message' => 'Đổi mật khẩu thành công'];
    }

    public function uploadAvatar(string $userId, array $file): array
    {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        $maxSize = 2 * 1024 * 1024; // 2MB
        
        if (!in_array($file['type'], $allowedTypes)) {
            return ['success' => false, 'message' => 'Chỉ chấp nhận file ảnh JPG, PNG, WebP, GIF'];
        }
        
        if ($file['size'] > $maxSize) {
            return ['success' => false, 'message' => 'File ảnh không được vượt quá 2MB'];
        }
        
        $uploadDir = BASE_PATH . '/uploads/avatars/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = $userId . '_' . time() . '.' . $ext;
        $filepath = $uploadDir . $filename;
        
        if (!move_uploaded_file($file['tmp_name'], $filepath)) {
            return ['success' => false, 'message' => 'Không thể lưu file'];
        }
        
        // Delete old avatar
        $user = $this->find($userId);
        if (!empty($user['avatar_url'])) {
            $oldPath = BASE_PATH . '/' . $user['avatar_url'];
            if (file_exists($oldPath)) {
                unlink($oldPath);
            }
        }
        
        $avatarUrl = 'uploads/avatars/' . $filename;
        $this->update($userId, ['avatar_url' => $avatarUrl]);
        
        return ['success' => true, 'url' => $avatarUrl];
    }

    public function getAll(): array
    {
        $sql = "SELECT * FROM {$this->table} ORDER BY created_at DESC";
        return $this->db->fetchAll($sql);
    }

    public function getTeamMembersForUser(string $userId): array
    {
        // Get all users who are in the same projects as the given user
        $sql = "SELECT DISTINCT u.*, 
                (SELECT COUNT(*) FROM task_assignees ta WHERE ta.user_id = u.id) as task_count
                FROM {$this->table} u
                JOIN project_members pm ON u.id = pm.user_id
                WHERE pm.project_id IN (
                    SELECT project_id FROM project_members WHERE user_id = ?
                )
                AND u.is_active = 1
                ORDER BY u.full_name ASC";
        return $this->db->fetchAll($sql, [$userId]);
    }

    /**
     * Get team members with detailed workload stats for Manager
     */
    public function getTeamWithWorkload(string $managerId): array
    {
        $sql = "SELECT DISTINCT u.id, u.full_name, u.email, u.avatar_url, u.role, u.position, u.department,
                (SELECT COUNT(*) FROM task_assignees ta 
                 JOIN tasks t ON ta.task_id = t.id 
                 WHERE ta.user_id = u.id AND t.status != 'done') as active_tasks,
                (SELECT COUNT(*) FROM task_assignees ta 
                 JOIN tasks t ON ta.task_id = t.id 
                 WHERE ta.user_id = u.id AND t.status = 'done') as completed_tasks,
                (SELECT COUNT(*) FROM task_assignees ta 
                 JOIN tasks t ON ta.task_id = t.id 
                 WHERE ta.user_id = u.id AND t.due_date < CURDATE() AND t.status != 'done') as overdue_tasks,
                (SELECT COUNT(*) FROM task_assignees ta 
                 JOIN tasks t ON ta.task_id = t.id 
                 WHERE ta.user_id = u.id AND t.status = 'in_progress') as in_progress_tasks
                FROM {$this->table} u
                JOIN project_members pm ON u.id = pm.user_id
                WHERE pm.project_id IN (
                    SELECT project_id FROM project_members WHERE user_id = ? AND role IN ('owner', 'manager')
                )
                AND u.is_active = 1
                ORDER BY u.full_name ASC";
        return $this->db->fetchAll($sql, [$managerId]);
    }

    /**
     * Get top performers based on completed tasks
     */
    public function getTopPerformers(string $managerId, int $limit = 5): array
    {
        $sql = "SELECT u.id, u.full_name, u.avatar_url, u.position,
                COUNT(DISTINCT CASE WHEN t.status = 'done' THEN t.id END) as completed_count,
                COUNT(DISTINCT t.id) as total_assigned,
                ROUND(COUNT(DISTINCT CASE WHEN t.status = 'done' THEN t.id END) * 100.0 / 
                      NULLIF(COUNT(DISTINCT t.id), 0), 1) as completion_rate
                FROM {$this->table} u
                JOIN project_members pm ON u.id = pm.user_id
                LEFT JOIN task_assignees ta ON u.id = ta.user_id
                LEFT JOIN tasks t ON ta.task_id = t.id
                WHERE pm.project_id IN (
                    SELECT project_id FROM project_members WHERE user_id = ? AND role IN ('owner', 'manager')
                )
                AND u.is_active = 1
                GROUP BY u.id
                HAVING total_assigned > 0
                ORDER BY completion_rate DESC, completed_count DESC
                LIMIT ?";
        return $this->db->fetchAll($sql, [$managerId, $limit]);
    }

    /**
     * Get members who need attention (many overdue tasks)
     */
    public function getMembersNeedingAttention(string $managerId, int $limit = 5): array
    {
        $sql = "SELECT u.id, u.full_name, u.avatar_url, u.position,
                COUNT(DISTINCT CASE WHEN t.due_date < CURDATE() AND t.status != 'done' THEN t.id END) as overdue_count,
                COUNT(DISTINCT CASE WHEN t.status = 'in_progress' THEN t.id END) as in_progress_count
                FROM {$this->table} u
                JOIN project_members pm ON u.id = pm.user_id
                LEFT JOIN task_assignees ta ON u.id = ta.user_id
                LEFT JOIN tasks t ON ta.task_id = t.id
                WHERE pm.project_id IN (
                    SELECT project_id FROM project_members WHERE user_id = ? AND role IN ('owner', 'manager')
                )
                AND u.is_active = 1
                GROUP BY u.id
                HAVING overdue_count > 0
                ORDER BY overdue_count DESC
                LIMIT ?";
        return $this->db->fetchAll($sql, [$managerId, $limit]);
    }

    /**
     * Get all users with workload stats (for Admin)
     */
    public function getAllWithWorkload(): array
    {
        $sql = "SELECT u.id, u.full_name, u.email, u.avatar_url, u.role, u.position, u.department, u.is_active,
                (SELECT COUNT(*) FROM task_assignees ta 
                 JOIN tasks t ON ta.task_id = t.id 
                 WHERE ta.user_id = u.id AND t.status != 'done') as active_tasks,
                (SELECT COUNT(*) FROM task_assignees ta 
                 JOIN tasks t ON ta.task_id = t.id 
                 WHERE ta.user_id = u.id AND t.status = 'done') as completed_tasks,
                (SELECT COUNT(*) FROM task_assignees ta 
                 JOIN tasks t ON ta.task_id = t.id 
                 WHERE ta.user_id = u.id AND t.due_date < CURDATE() AND t.status != 'done') as overdue_tasks,
                (SELECT COUNT(*) FROM task_assignees ta 
                 JOIN tasks t ON ta.task_id = t.id 
                 WHERE ta.user_id = u.id AND t.status = 'in_progress') as in_progress_tasks
                FROM {$this->table} u
                WHERE u.is_active = 1
                ORDER BY u.full_name ASC";
        return $this->db->fetchAll($sql);
    }
}
