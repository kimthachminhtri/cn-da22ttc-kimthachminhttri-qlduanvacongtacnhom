<?php
/**
 * Reset Password Page
 */
require_once __DIR__ . '/bootstrap.php';

use App\Models\User;
use Core\Session;
use Core\Database;
use Core\Logger;

// Redirect if already logged in
if (Session::has('user_id')) {
    header('Location: /php/index.php');
    exit;
}

$token = $_GET['token'] ?? '';
$message = '';
$messageType = '';
$validToken = false;
$userId = null;

// Validate token
if (!empty($token)) {
    $hashedToken = hash('sha256', $token);
    $db = Database::getInstance();
    
    $user = $db->fetchOne(
        "SELECT id, email FROM users WHERE reset_token = ? AND reset_token_expiry > NOW()",
        [$hashedToken]
    );
    
    if ($user) {
        $validToken = true;
        $userId = $user['id'];
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $validToken) {
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    
    if (empty($password)) {
        $message = 'Vui lòng nhập mật khẩu mới';
        $messageType = 'error';
    } elseif (strlen($password) < 6) {
        $message = 'Mật khẩu phải có ít nhất 6 ký tự';
        $messageType = 'error';
    } elseif ($password !== $confirmPassword) {
        $message = 'Mật khẩu xác nhận không khớp';
        $messageType = 'error';
    } else {
        try {
            $db = Database::getInstance();
            
            // Update password and clear token
            $db->update('users', [
                'password_hash' => password_hash($password, PASSWORD_DEFAULT),
                'reset_token' => null,
                'reset_token_expiry' => null,
            ], 'id = ?', [$userId]);
            
            Logger::info('Password reset successful', ['user_id' => $userId]);
            
            $message = 'Đặt lại mật khẩu thành công!';
            $messageType = 'success';
            $validToken = false; // Prevent form resubmission
            
        } catch (Exception $e) {
            Logger::error('Password reset failed', ['user_id' => $userId, 'error' => $e->getMessage()]);
            $message = 'Có lỗi xảy ra. Vui lòng thử lại.';
            $messageType = 'error';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt lại mật khẩu - TaskFlow</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="min-h-screen bg-gray-50 flex items-center justify-center py-12 px-4">
    <div class="max-w-md w-full">
        <div class="bg-white rounded-xl shadow-lg p-8">
            <!-- Logo -->
            <div class="text-center mb-8">
                <div class="inline-flex h-12 w-12 items-center justify-center rounded-xl bg-blue-500 mb-4">
                    <i data-lucide="lock" class="h-6 w-6 text-white"></i>
                </div>
                <h1 class="text-2xl font-bold text-gray-900">Đặt lại mật khẩu</h1>
                <p class="text-gray-500 mt-2">Nhập mật khẩu mới cho tài khoản của bạn</p>
            </div>
            
            <?php if ($message): ?>
                <div class="mb-4 p-4 <?= $messageType === 'success' ? 'bg-green-50 text-green-700 border-green-200' : 'bg-red-50 text-red-700 border-red-200' ?> border rounded-lg text-sm">
                    <?= htmlspecialchars($message) ?>
                    <?php if ($messageType === 'success'): ?>
                        <div class="mt-2">
                            <a href="/php/login.php" class="font-medium underline">Đăng nhập ngay →</a>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            
            <?php if (!$validToken && empty($message)): ?>
                <!-- Invalid/Expired Token -->
                <div class="p-4 bg-red-50 text-red-700 border border-red-200 rounded-lg text-sm">
                    <p class="font-medium">Link không hợp lệ hoặc đã hết hạn</p>
                    <p class="mt-1">Vui lòng yêu cầu link đặt lại mật khẩu mới.</p>
                    <a href="/php/forgot-password.php" class="inline-block mt-3 text-blue-500 hover:underline">
                        Yêu cầu link mới →
                    </a>
                </div>
            <?php elseif ($validToken): ?>
                <!-- Reset Form -->
                <form method="POST" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Mật khẩu mới</label>
                        <input type="password" name="password" required minlength="6"
                               class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                               placeholder="Ít nhất 6 ký tự">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Xác nhận mật khẩu</label>
                        <input type="password" name="confirm_password" required
                               class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                               placeholder="Nhập lại mật khẩu">
                    </div>
                    
                    <button type="submit" 
                            class="w-full rounded-lg bg-blue-500 px-4 py-2 text-sm font-medium text-white hover:bg-blue-600 transition-colors">
                        Đặt lại mật khẩu
                    </button>
                </form>
            <?php endif; ?>
            
            <p class="mt-6 text-center text-sm text-gray-500">
                <a href="/php/login.php" class="text-blue-500 hover:underline">← Quay lại đăng nhập</a>
            </p>
        </div>
    </div>
    <script>lucide.createIcons();</script>
</body>
</html>
