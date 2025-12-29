<?php
use Core\View;
use Core\Session;

View::section('content');
?>
<div class="w-full max-w-md">
    <div class="bg-white rounded-xl shadow-lg p-8">
        <!-- Logo -->
        <div class="text-center mb-8">
            <div class="inline-flex h-12 w-12 items-center justify-center rounded-xl bg-blue-500 mb-4">
                <i data-lucide="folder-kanban" class="h-6 w-6 text-white"></i>
            </div>
            <h1 class="text-2xl font-bold text-gray-900">Đăng nhập</h1>
            <p class="text-gray-500 mt-2">Chào mừng bạn quay lại TaskFlow</p>
        </div>
        
        <!-- Flash Messages -->
        <?php if ($error = Session::flash('error')): ?>
            <div class="mb-4 p-4 bg-red-50 text-red-700 border border-red-200 rounded-lg text-sm">
                <?= View::e($error) ?>
            </div>
        <?php endif; ?>
        
        <?php if ($success = Session::flash('success')): ?>
            <div class="mb-4 p-4 bg-green-50 text-green-700 border border-green-200 rounded-lg text-sm">
                <?= View::e($success) ?>
            </div>
        <?php endif; ?>
        
        <!-- Form -->
        <form action="/php/login.php" method="POST" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="email" required
                       class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                       placeholder="email@example.com">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Mật khẩu</label>
                <input type="password" name="password" required
                       class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                       placeholder="••••••••">
            </div>
            
            <div class="flex items-center justify-between">
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="remember" class="rounded border-gray-300 text-blue-500 focus:ring-blue-500">
                    <span class="text-sm text-gray-600">Ghi nhớ đăng nhập</span>
                </label>
                <a href="/php/forgot-password.php" class="text-sm text-blue-500 hover:underline">Quên mật khẩu?</a>
            </div>
            
            <button type="submit" 
                    class="w-full rounded-lg bg-blue-500 px-4 py-2 text-sm font-medium text-white hover:bg-blue-600 transition-colors">
                Đăng nhập
            </button>
        </form>
        
        <p class="mt-6 text-center text-sm text-gray-500">
            Chưa có tài khoản? 
            <a href="/php/register.php" class="text-blue-500 hover:underline">Đăng ký ngay</a>
        </p>
    </div>
</div>
<?php View::endSection(); ?>
