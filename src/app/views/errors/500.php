<?php
/**
 * 500 Internal Server Error Page
 */
use Core\View;

View::section('content');
?>

<div class="min-h-[80vh] flex items-center justify-center">
    <div class="text-center px-4">
        <!-- Error Icon -->
        <div class="mb-8">
            <div class="inline-flex items-center justify-center w-24 h-24 rounded-full bg-red-100">
                <i data-lucide="alert-triangle" class="h-12 w-12 text-red-500"></i>
            </div>
        </div>
        
        <!-- Error Code -->
        <h1 class="text-8xl font-bold text-gray-200 mb-4">500</h1>
        
        <!-- Message -->
        <h2 class="text-2xl font-semibold text-gray-800 mb-2">Lỗi hệ thống</h2>
        <p class="text-gray-500 mb-8 max-w-md mx-auto">
            <?= View::e($message ?? 'Đã xảy ra lỗi không mong muốn. Vui lòng thử lại sau hoặc liên hệ quản trị viên.') ?>
        </p>
        
        <!-- Actions -->
        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a href="/php/index.php" 
               class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-primary text-white font-medium rounded-lg hover:bg-primary/90 transition-colors">
                <i data-lucide="home" class="h-4 w-4"></i>
                Về trang chủ
            </a>
            <button onclick="location.reload()" 
                    class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-gray-100 text-gray-700 font-medium rounded-lg hover:bg-gray-200 transition-colors">
                <i data-lucide="refresh-cw" class="h-4 w-4"></i>
                Thử lại
            </button>
        </div>
        
        <!-- Support Info -->
        <p class="mt-8 text-sm text-gray-400">
            Nếu lỗi tiếp tục xảy ra, vui lòng liên hệ: <span class="text-primary">support@taskflow.com</span>
        </p>
    </div>
</div>

<?php View::endSection(); ?>
