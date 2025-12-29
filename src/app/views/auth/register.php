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
            <h1 class="text-2xl font-bold text-gray-900">Đăng ký</h1>
            <p class="text-gray-500 mt-2">Tạo tài khoản TaskFlow mới</p>
        </div>
        
        <!-- Flash Messages -->
        <?php if ($error = Session::flash('error')): ?>
            <div class="mb-4 p-4 bg-red-50 text-red-700 border border-red-200 rounded-lg text-sm">
                <?= View::e($error) ?>
            </div>
        <?php endif; ?>
        
        <!-- Form -->
        <form action="/php/register.php" method="POST" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Họ và tên</label>
                <input type="text" name="full_name" required
                       class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                       placeholder="Nguyễn Văn A">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="email" required
                       class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                       placeholder="email@example.com">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Mật khẩu</label>
                <input type="password" name="password" required minlength="6"
                       class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                       placeholder="Tối thiểu 6 ký tự">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Xác nhận mật khẩu</label>
                <input type="password" name="password_confirm" required
                       class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                       placeholder="Nhập lại mật khẩu">
            </div>
            
            <button type="submit" 
                    class="w-full rounded-lg bg-blue-500 px-4 py-2 text-sm font-medium text-white hover:bg-blue-600 transition-colors">
                Đăng ký
            </button>
        </form>
        
        <p class="mt-6 text-center text-sm text-gray-500">
            Đã có tài khoản? 
            <a href="/php/login.php" class="text-blue-500 hover:underline">Đăng nhập</a>
        </p>
    </div>
</div>
<?php View::endSection(); ?>
