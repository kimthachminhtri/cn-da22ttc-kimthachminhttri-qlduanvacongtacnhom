<?php
/**
 * User Model
 */

class User extends Model
{
    protected string $table = 'users';

    /**
     * Find by email
     */
    public function findByEmail(string $email): ?array
    {
        return $this->findBy('email', $email);
    }

    /**
     * Get active users
     */
    public function getActive(): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE is_active = 1 ORDER BY full_name ASC";
        return $this->db->fetchAll($sql);
    }

    /**
     * Create user with password hash
     */
    public function createUser(array $data): string|false
    {
        if (isset($data['password'])) {
            $data['password_hash'] = password_hash($data['password'], PASSWORD_DEFAULT);
            unset($data['password']);
        }
        
        // Generate UUID for new user
        $userId = $this->generateUUID();
        $data['id'] = $userId;
        
        // Remove created_at if exists (will be set by Model)
        unset($data['created_at']);
        
        $result = $this->create($data);
        return $result !== false ? $userId : false;
    }
    
    /**
     * Generate UUID v4
     */
    private function generateUUID(): string
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }

    /**
     * Verify password
     */
    public function verifyPassword(string $email, string $password): ?array
    {
        $user = $this->findByEmail($email);
        if ($user && password_verify($password, $user['password_hash'])) {
            return $user;
        }
        return null;
    }

    /**
     * Update last login
     */
    public function updateLastLogin(string $userId): void
    {
        $sql = "UPDATE {$this->table} SET last_login_at = NOW() WHERE id = ?";
        $this->db->query($sql, [$userId]);
    }

    /**
     * Get user with task count
     */
    public function getWithTaskCount(string $userId): ?array
    {
        $sql = "SELECT u.*, 
                (SELECT COUNT(*) FROM task_assignees ta WHERE ta.user_id = u.id) as task_count
                FROM {$this->table} u WHERE u.id = ?";
        return $this->db->fetchOne($sql, [$userId]);
    }

    /**
     * Get all users with task counts
     */
    public function getAllWithTaskCounts(): array
    {
        $sql = "SELECT u.*, 
                (SELECT COUNT(*) FROM task_assignees ta WHERE ta.user_id = u.id) as task_count
                FROM {$this->table} u 
                WHERE u.is_active = 1 
                ORDER BY u.full_name ASC";
        return $this->db->fetchAll($sql);
    }

    /**
     * Update user profile
     */
    public function updateProfile(string $userId, array $data): bool
    {
        $allowed = ['full_name', 'email', 'phone', 'position', 'department', 'avatar_url'];
        $updateData = array_intersect_key($data, array_flip($allowed));
        
        if (empty($updateData)) {
            return false;
        }
        
        return $this->update($userId, $updateData);
    }

    /**
     * Upload avatar
     */
    public function uploadAvatar(string $userId, array $file): array
    {
        // Validate file
        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
        $maxSize = 2 * 1024 * 1024; // 2MB
        
        if (!in_array($file['type'], $allowedTypes)) {
            return ['success' => false, 'message' => 'Chỉ chấp nhận file JPG, PNG hoặc WebP'];
        }
        
        if ($file['size'] > $maxSize) {
            return ['success' => false, 'message' => 'File quá lớn. Tối đa 2MB'];
        }
        
        // Create upload directory
        $uploadDir = __DIR__ . '/../../uploads/avatars/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        // Generate unique filename
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = $userId . '_' . time() . '.' . $ext;
        $filepath = $uploadDir . $filename;
        
        // Move uploaded file
        if (!move_uploaded_file($file['tmp_name'], $filepath)) {
            return ['success' => false, 'message' => 'Không thể lưu file'];
        }
        
        // Update database
        $avatarUrl = 'uploads/avatars/' . $filename;
        $result = $this->update($userId, ['avatar_url' => $avatarUrl]);
        
        if ($result) {
            return ['success' => true, 'url' => $avatarUrl];
        }
        
        // Cleanup on failure
        @unlink($filepath);
        return ['success' => false, 'message' => 'Không thể cập nhật database'];
    }

    /**
     * Change password
     */
    public function changePassword(string $userId, string $currentPassword, string $newPassword): array
    {
        $user = $this->find($userId);
        if (!$user) {
            return ['success' => false, 'message' => 'Không tìm thấy người dùng'];
        }
        
        if (!password_verify($currentPassword, $user['password_hash'])) {
            return ['success' => false, 'message' => 'Mật khẩu hiện tại không đúng'];
        }
        
        if (strlen($newPassword) < 6) {
            return ['success' => false, 'message' => 'Mật khẩu mới phải có ít nhất 6 ký tự'];
        }
        
        $result = $this->update($userId, [
            'password_hash' => password_hash($newPassword, PASSWORD_DEFAULT)
        ]);
        
        return $result 
            ? ['success' => true, 'message' => 'Đổi mật khẩu thành công']
            : ['success' => false, 'message' => 'Có lỗi xảy ra'];
    }

    /**
     * Get user settings from user_settings table
     */
    public function getUserSettings(string $userId): array
    {
        $sql = "SELECT * FROM user_settings WHERE user_id = ?";
        $row = $this->db->fetchOne($sql, [$userId]);
        
        // Default settings if not exists
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
        
        if (!$row) {
            return $defaults;
        }
        
        return [
            'theme' => $row['theme'] ?? 'system',
            'language' => $row['language'] ?? 'vi',
            'timezone' => $row['timezone'] ?? 'Asia/Ho_Chi_Minh',
            'notification_email' => (string)($row['notification_email'] ?? 1),
            'notification_push' => (string)($row['notification_push'] ?? 1),
            'notification_task_assigned' => (string)($row['notification_task_assigned'] ?? 1),
            'notification_task_due' => (string)($row['notification_task_due'] ?? 1),
            'notification_comment' => (string)($row['notification_comment'] ?? 1),
            'notification_mention' => (string)($row['notification_mention'] ?? 1),
        ];
    }

    /**
     * Update user settings
     */
    public function updateUserSettings(string $userId, array $settings): bool
    {
        // Check if settings exist
        $sql = "SELECT user_id FROM user_settings WHERE user_id = ?";
        $exists = $this->db->fetchOne($sql, [$userId]);
        
        // Map settings keys to column names
        $columnMap = [
            'theme' => 'theme',
            'language' => 'language',
            'timezone' => 'timezone',
            'notify_task_assigned' => 'notification_task_assigned',
            'notify_task_completed' => 'notification_task_due', // map to task_due since no completed column
            'notify_task_due' => 'notification_task_due',
            'notify_comment' => 'notification_comment',
            'notify_mention' => 'notification_mention',
        ];
        
        $data = [];
        foreach ($settings as $key => $value) {
            $column = $columnMap[$key] ?? $key;
            $data[$column] = $value;
        }
        
        if ($exists) {
            // Update existing
            $sets = [];
            $params = [];
            foreach ($data as $col => $val) {
                $sets[] = "`$col` = ?";
                $params[] = $val;
            }
            $params[] = $userId;
            $sql = "UPDATE user_settings SET " . implode(', ', $sets) . " WHERE user_id = ?";
            return $this->db->query($sql, $params) !== false;
        } else {
            // Insert new
            $data['user_id'] = $userId;
            $cols = array_keys($data);
            $placeholders = array_fill(0, count($cols), '?');
            $sql = "INSERT INTO user_settings (`" . implode('`, `', $cols) . "`) VALUES (" . implode(', ', $placeholders) . ")";
            return $this->db->query($sql, array_values($data)) !== false;
        }
    }
}
