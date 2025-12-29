<?php
/**
 * Forgot Password Page
 */
require_once __DIR__ . '/bootstrap.php';

use App\Models\User;
use Core\Session;
use Core\View;
use Core\Database;
use Core\Logger;
use Core\RateLimiter;

// Redirect if already logged in
if (Session::has('user_id')) {
    header('Location: /php/index.php');
    exit;
}

$message = '';
$messageType = '';
$email = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $rateLimitKey = "forgot_password:{$ip}";
    
    // Rate limit: 3 attempts per 5 minutes
    if (RateLimiter::tooManyAttempts($rateLimitKey, 3, 5)) {
        $message = 'Quá nhiều yêu cầu. Vui lòng thử lại sau.';
        $messageType = 'error';
    } else {
        $email = trim($_POST['email'] ?? '');
        
        if (empty($email)) {
            $message = 'Vui lòng nhập email';
            $messageType = 'error';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $message = 'Email không hợp lệ';
            $messageType = 'error';
        } else {
            RateLimiter::hit($rateLimitKey);
            
            $userModel = new User();
            $user = $userModel->findByEmail($email);
            
            if ($user) {
                // Generate reset token
                $token = bin2hex(random_bytes(32));
                $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
                
                try {
                    $db = Database::getInstance();
                    $db->update('users', [
                        'reset_token' => hash('sha256', $token),
                        'reset_token_expiry' => $expiry,
                    ], 'id = ?', [$user['id']]);
                    
                    // Log for development (in production, send email)
                    Logger::info('Password reset requested', [
                        'email' => $email,
                        'token' => $token, // Remove in production!
                        'reset_url' => "/php/reset-password.php?token={$token}"
                    ]);
                    
                    $message = 'Nếu email tồn tại trong hệ thống, bạn sẽ nhận được hướng dẫn đặt lại mật khẩu.';
                    $messageType = 'success';
                    
                } catch (Exception $e) {
                    Logger::error('Password reset failed', ['email' => $email, 'error' => $e->getMessage()]);
                    $message = 'Có lỗi xảy ra. Vui lòng thử lại sau.';
                    $messageType = 'error';
                }
            } else {
                // Don't reveal if email exists (security)
                $message = 'Nếu email tồn tại trong hệ thống, bạn sẽ nhận được hướng dẫn đặt lại mật khẩu.';
                $messageType = 'success';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quên mật khẩu - TaskFlow</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="min-h-screen bg-gray-50 flex items-center justify-center py-12 px-4">
    <div class="max-w-md w-full">
        <div class="bg-white rounded-xl shadow-lg p-8">
            <!-- Logo -->
            <div class="text-center mb-8">
                <div class="inline-flex h-12 w-12 items-center justify-center rounded-xl bg-blue-500 mb-4">
                    <i data-lucide="key" class="h-6 w-6 text-white"></i>
                </div>
                <h1 class="text-2xl font-bold text-gray-900">Quên mật khẩu?</h1>
                <p class="text-gray-500 mt-2">Nhập email để nhận hướng dẫn đặt lại mật khẩu</p>
            </div>
            
            <?php if ($message): ?>
                <div class="mb-4 p-4 <?= $messageType === 'success' ? 'bg-green-50 text-green-700 border-green-200' : 'bg-red-50 text-red-700 border-red-200' ?> border rounded-lg text-sm">
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" required
                           value="<?= htmlspecialchars($email) ?>"
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                           placeholder="email@example.com">
                </div>
                
                <button type="submit" 
                        class="w-full rounded-lg bg-blue-500 px-4 py-2 text-sm font-medium text-white hover:bg-blue-600 transition-colors">
                    Gửi hướng dẫn
                </button>
            </form>
            
            <p class="mt-6 text-center text-sm text-gray-500">
                <a href="/php/login.php" class="text-blue-500 hover:underline">← Quay lại đăng nhập</a>
            </p>
        </div>
    </div>
    <script>lucide.createIcons();</script>
</body>
</html>
