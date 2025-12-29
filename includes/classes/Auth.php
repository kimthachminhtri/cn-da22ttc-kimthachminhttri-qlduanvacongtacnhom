<?php
/**
 * Auth Class - Xử lý xác thực và phân quyền
 */

class Auth
{
    private static ?Auth $instance = null;
    private ?array $user = null;
    private User $userModel;

    // Định nghĩa các roles
    public const ROLE_ADMIN = 'admin';
    public const ROLE_MANAGER = 'manager';
    public const ROLE_MEMBER = 'member';
    public const ROLE_GUEST = 'guest';

    // Quyền theo role
    private array $permissions = [
        'admin' => [
            'users.view', 'users.create', 'users.edit', 'users.delete',
            'projects.view', 'projects.create', 'projects.edit', 'projects.delete',
            'tasks.view', 'tasks.create', 'tasks.edit', 'tasks.delete',
            'documents.view', 'documents.create', 'documents.edit', 'documents.delete',
            'settings.view', 'settings.edit',
            'team.manage',
        ],
        'manager' => [
            'users.view',
            'projects.view', 'projects.create', 'projects.edit',
            'tasks.view', 'tasks.create', 'tasks.edit', 'tasks.delete',
            'documents.view', 'documents.create', 'documents.edit', 'documents.delete',
            'team.manage',
        ],
        'member' => [
            'users.view',
            'projects.view',
            'tasks.view', 'tasks.create', 'tasks.edit',
            'documents.view', 'documents.create', 'documents.edit',
        ],
        'guest' => [
            'projects.view',
            'tasks.view',
            'documents.view',
        ],
    ];

    private function __construct()
    {
        $this->userModel = new User();
        $this->loadUserFromSession();
    }

    public static function getInstance(): Auth
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Load user từ session
     */
    private function loadUserFromSession(): void
    {
        if (isset($_SESSION['user_id'])) {
            $this->user = $this->userModel->find($_SESSION['user_id']);
        }
    }

    /**
     * Đăng ký user mới
     */
    public function register(array $data): array
    {
        // Validate
        $errors = $this->validateRegistration($data);
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        // Check email exists
        if ($this->userModel->findByEmail($data['email'])) {
            return ['success' => false, 'errors' => ['email' => 'Email đã được sử dụng']];
        }

        // Create user
        $userId = $this->userModel->createUser([
            'email' => $data['email'],
            'full_name' => $data['full_name'],
            'password' => $data['password'],
            'role' => self::ROLE_MEMBER, // Default role
            'is_active' => 1,
        ]);

        if ($userId) {
            // Create default user settings
            try {
                db()->insert('user_settings', ['user_id' => $userId]);
            } catch (Exception $e) {
                // Ignore if user_settings table doesn't exist
            }
            return ['success' => true, 'user_id' => $userId];
        }

        return ['success' => false, 'errors' => ['general' => 'Không thể tạo tài khoản']];
    }

    /**
     * Đăng nhập
     */
    public function login(string $email, string $password, bool $remember = false): array
    {
        $user = $this->userModel->verifyPassword($email, $password);

        if (!$user) {
            return ['success' => false, 'error' => 'Email hoặc mật khẩu không đúng'];
        }

        if (!$user['is_active']) {
            return ['success' => false, 'error' => 'Tài khoản đã bị vô hiệu hóa'];
        }

        // Set session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_role'] = $user['role'];
        $this->user = $user;

        // Update last login
        $this->userModel->updateLastLogin($user['id']);

        // Remember me
        if ($remember) {
            $this->setRememberToken($user['id']);
        }

        return ['success' => true, 'user' => $user];
    }

    /**
     * Đăng xuất
     */
    public function logout(): void
    {
        $this->clearRememberToken();
        
        $_SESSION = [];
        session_destroy();
        
        $this->user = null;
    }

    /**
     * Kiểm tra đã đăng nhập chưa
     */
    public function check(): bool
    {
        if ($this->user) {
            return true;
        }

        // Check remember token
        if (isset($_COOKIE['remember_token'])) {
            return $this->loginWithRememberToken($_COOKIE['remember_token']);
        }

        return false;
    }

    /**
     * Lấy user hiện tại
     */
    public function user(): ?array
    {
        return $this->user;
    }

    /**
     * Lấy user ID
     */
    public function id(): ?string
    {
        return $this->user['id'] ?? null;
    }

    /**
     * Lấy role của user
     */
    public function role(): string
    {
        return $this->user['role'] ?? self::ROLE_GUEST;
    }

    /**
     * Kiểm tra có phải admin không
     */
    public function isAdmin(): bool
    {
        return $this->role() === self::ROLE_ADMIN;
    }

    /**
     * Kiểm tra có phải manager không
     */
    public function isManager(): bool
    {
        return in_array($this->role(), [self::ROLE_ADMIN, self::ROLE_MANAGER]);
    }

    /**
     * Kiểm tra quyền
     */
    public function can(string $permission): bool
    {
        $role = $this->role();
        return in_array($permission, $this->permissions[$role] ?? []);
    }

    /**
     * Kiểm tra nhiều quyền (cần tất cả)
     */
    public function canAll(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if (!$this->can($permission)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Kiểm tra nhiều quyền (cần ít nhất 1)
     */
    public function canAny(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if ($this->can($permission)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Yêu cầu đăng nhập
     */
    public function requireLogin(string $redirect = '/php/login.php'): void
    {
        if (!$this->check()) {
            $_SESSION['intended_url'] = $_SERVER['REQUEST_URI'];
            header("Location: $redirect");
            exit;
        }
    }

    /**
     * Yêu cầu quyền
     */
    public function requirePermission(string $permission, string $redirect = '/php/403.php'): void
    {
        $this->requireLogin();
        
        if (!$this->can($permission)) {
            header("Location: $redirect");
            exit;
        }
    }

    /**
     * Yêu cầu role
     */
    public function requireRole(array $roles, string $redirect = '/php/403.php'): void
    {
        $this->requireLogin();
        
        if (!in_array($this->role(), $roles)) {
            header("Location: $redirect");
            exit;
        }
    }

    /**
     * Validate dữ liệu đăng ký
     */
    private function validateRegistration(array $data): array
    {
        $errors = [];

        if (empty($data['email'])) {
            $errors['email'] = 'Email là bắt buộc';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Email không hợp lệ';
        }

        if (empty($data['full_name'])) {
            $errors['full_name'] = 'Họ tên là bắt buộc';
        } elseif (strlen($data['full_name']) < 2) {
            $errors['full_name'] = 'Họ tên phải có ít nhất 2 ký tự';
        }

        if (empty($data['password'])) {
            $errors['password'] = 'Mật khẩu là bắt buộc';
        } elseif (strlen($data['password']) < 6) {
            $errors['password'] = 'Mật khẩu phải có ít nhất 6 ký tự';
        }

        if (isset($data['password_confirm']) && $data['password'] !== $data['password_confirm']) {
            $errors['password_confirm'] = 'Mật khẩu xác nhận không khớp';
        }

        return $errors;
    }

    /**
     * Set remember token
     */
    private function setRememberToken(string $userId): void
    {
        $token = bin2hex(random_bytes(32));
        $expiry = time() + (30 * 24 * 60 * 60); // 30 days

        // Save to database
        $this->userModel->update($userId, [
            'remember_token' => hash('sha256', $token),
            'remember_token_expiry' => date('Y-m-d H:i:s', $expiry),
        ]);

        // Set cookie
        setcookie('remember_token', $token, $expiry, '/', '', false, true);
    }

    /**
     * Login with remember token
     */
    private function loginWithRememberToken(string $token): bool
    {
        $hashedToken = hash('sha256', $token);
        
        $user = db()->fetchOne(
            "SELECT * FROM users WHERE remember_token = ? AND remember_token_expiry > NOW() AND is_active = 1",
            [$hashedToken]
        );

        if ($user) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_role'] = $user['role'];
            $this->user = $user;
            return true;
        }

        $this->clearRememberToken();
        return false;
    }

    /**
     * Clear remember token
     */
    private function clearRememberToken(): void
    {
        if ($this->user) {
            $this->userModel->update($this->user['id'], [
                'remember_token' => null,
                'remember_token_expiry' => null,
            ]);
        }
        setcookie('remember_token', '', time() - 3600, '/');
    }

    /**
     * Đổi mật khẩu
     */
    public function changePassword(string $currentPassword, string $newPassword): array
    {
        if (!$this->user) {
            return ['success' => false, 'error' => 'Chưa đăng nhập'];
        }

        // Verify current password
        if (!password_verify($currentPassword, $this->user['password_hash'])) {
            return ['success' => false, 'error' => 'Mật khẩu hiện tại không đúng'];
        }

        // Update password
        $this->userModel->update($this->user['id'], [
            'password_hash' => password_hash($newPassword, PASSWORD_DEFAULT),
        ]);

        return ['success' => true];
    }
}
