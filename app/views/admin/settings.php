<?php
/**
 * Admin Settings View
 */
use Core\View;

View::section('content');
?>

<div class="max-w-4xl space-y-6">
    <!-- System Info -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="border-b border-gray-200 px-6 py-4">
            <h3 class="font-semibold text-gray-900">Thông tin hệ thống</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="p-4 bg-gray-50 rounded-lg">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Phiên bản</p>
                    <p class="font-semibold text-gray-900 mt-1">TaskFlow v2.0.0</p>
                </div>
                <div class="p-4 bg-gray-50 rounded-lg">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">PHP Version</p>
                    <p class="font-semibold text-gray-900 mt-1"><?= phpversion() ?></p>
                </div>
                <div class="p-4 bg-gray-50 rounded-lg">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Server</p>
                    <p class="font-semibold text-gray-900 mt-1 truncate"><?= $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown' ?></p>
                </div>
                <div class="p-4 bg-gray-50 rounded-lg">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Database</p>
                    <p class="font-semibold text-gray-900 mt-1">MySQL</p>
                </div>
            </div>
            
            <!-- Storage Info -->
            <div class="mt-4 p-4 bg-blue-50 rounded-lg">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-blue-900">Dung lượng uploads</span>
                    <?php
                    $uploadDir = BASE_PATH . '/uploads';
                    $totalSize = 0;
                    if (is_dir($uploadDir)) {
                        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($uploadDir));
                        foreach ($iterator as $file) {
                            if ($file->isFile()) $totalSize += $file->getSize();
                        }
                    }
                    $sizeMB = round($totalSize / 1024 / 1024, 2);
                    ?>
                    <span class="text-sm text-blue-700"><?= $sizeMB ?> MB</span>
                </div>
                <div class="h-2 bg-blue-200 rounded-full overflow-hidden">
                    <div class="h-full bg-blue-600 rounded-full" style="width: <?= min($sizeMB / 100 * 100, 100) ?>%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- General Settings -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="border-b border-gray-200 px-6 py-4">
            <h3 class="font-semibold text-gray-900">Cài đặt chung</h3>
        </div>
        <form id="general-settings-form" class="p-6 space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tên ứng dụng</label>
                    <input type="text" name="app_name" value="<?= View::e($settings['app_name'] ?? 'TaskFlow') ?>" 
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">URL ứng dụng</label>
                    <input type="url" name="app_url" value="<?= View::e($settings['app_url'] ?? 'http://localhost/php') ?>" 
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Múi giờ</label>
                    <select name="timezone" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                        <option value="Asia/Ho_Chi_Minh" <?= ($settings['timezone'] ?? '') === 'Asia/Ho_Chi_Minh' ? 'selected' : '' ?>>Asia/Ho_Chi_Minh (GMT+7)</option>
                        <option value="Asia/Bangkok" <?= ($settings['timezone'] ?? '') === 'Asia/Bangkok' ? 'selected' : '' ?>>Asia/Bangkok (GMT+7)</option>
                        <option value="UTC" <?= ($settings['timezone'] ?? '') === 'UTC' ? 'selected' : '' ?>>UTC</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ngôn ngữ mặc định</label>
                    <select name="language" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                        <option value="vi" <?= ($settings['language'] ?? 'vi') === 'vi' ? 'selected' : '' ?>>Tiếng Việt</option>
                        <option value="en" <?= ($settings['language'] ?? '') === 'en' ? 'selected' : '' ?>>English</option>
                    </select>
                </div>
            </div>
            <div class="flex items-center gap-4 pt-2">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="allow_registration" value="1" <?= ($settings['allow_registration'] ?? '1') === '1' ? 'checked' : '' ?>
                           class="rounded border-gray-300 text-primary focus:ring-primary">
                    <span class="text-sm text-gray-700">Cho phép đăng ký tài khoản mới</span>
                </label>
            </div>
            <div class="pt-4">
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-primary hover:bg-primary/90 rounded-lg">
                    Lưu cài đặt
                </button>
            </div>
        </form>
    </div>

    <!-- Email Settings -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="border-b border-gray-200 px-6 py-4 flex items-center justify-between">
            <h3 class="font-semibold text-gray-900">Cài đặt Email (SMTP)</h3>
            <span class="px-2 py-1 text-xs font-medium rounded-full <?= !empty($settings['smtp_host']) ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' ?>">
                <?= !empty($settings['smtp_host']) ? 'Đã cấu hình' : 'Chưa cấu hình' ?>
            </span>
        </div>
        <form id="email-settings-form" class="p-6 space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">SMTP Host</label>
                    <input type="text" name="smtp_host" value="<?= View::e($settings['smtp_host'] ?? '') ?>" 
                           placeholder="smtp.gmail.com"
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">SMTP Port</label>
                    <input type="number" name="smtp_port" value="<?= View::e($settings['smtp_port'] ?? '587') ?>" 
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">SMTP Username</label>
                    <input type="text" name="smtp_username" value="<?= View::e($settings['smtp_username'] ?? '') ?>" 
                           placeholder="your-email@gmail.com"
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">SMTP Password</label>
                    <input type="password" name="smtp_password" value="<?= View::e($settings['smtp_password'] ?? '') ?>" 
                           placeholder="••••••••"
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email gửi đi (From)</label>
                    <input type="email" name="mail_from" value="<?= View::e($settings['mail_from'] ?? '') ?>" 
                           placeholder="noreply@taskflow.com"
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tên hiển thị</label>
                    <input type="text" name="mail_from_name" value="<?= View::e($settings['mail_from_name'] ?? 'TaskFlow') ?>" 
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                </div>
            </div>
            <div class="flex items-center gap-3 pt-4">
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-primary hover:bg-primary/90 rounded-lg">
                    Lưu cài đặt email
                </button>
                <button type="button" onclick="testEmail()" class="px-4 py-2 text-sm font-medium text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50">
                    Gửi email test
                </button>
            </div>
        </form>
    </div>

    <!-- Maintenance -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="border-b border-gray-200 px-6 py-4">
            <h3 class="font-semibold text-gray-900">Bảo trì hệ thống</h3>
        </div>
        <div class="p-6 space-y-4">
            <div class="flex items-center justify-between py-3 border-b border-gray-100">
                <div>
                    <h4 class="font-medium text-gray-900">Xóa cache</h4>
                    <p class="text-sm text-gray-500">Xóa tất cả file cache của hệ thống</p>
                </div>
                <button onclick="clearCache()" class="px-4 py-2 text-sm font-medium text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50">
                    <i data-lucide="trash-2" class="inline h-4 w-4 mr-1"></i>
                    Xóa cache
                </button>
            </div>
            <div class="flex items-center justify-between py-3 border-b border-gray-100">
                <div>
                    <h4 class="font-medium text-gray-900">Backup database</h4>
                    <p class="text-sm text-gray-500">Tạo bản sao lưu database (SQL)</p>
                </div>
                <button onclick="backupDatabase()" class="px-4 py-2 text-sm font-medium text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50">
                    <i data-lucide="database" class="inline h-4 w-4 mr-1"></i>
                    Tạo backup
                </button>
            </div>
            <div class="flex items-center justify-between py-3">
                <div>
                    <h4 class="font-medium text-gray-900">Xóa logs cũ</h4>
                    <p class="text-sm text-gray-500">Xóa activity logs cũ hơn 30 ngày</p>
                </div>
                <button onclick="cleanupLogs()" class="px-4 py-2 text-sm font-medium text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50">
                    <i data-lucide="file-x" class="inline h-4 w-4 mr-1"></i>
                    Dọn dẹp
                </button>
            </div>
        </div>
    </div>

    <!-- Danger Zone -->
    <div class="bg-white rounded-xl border border-red-200 shadow-sm">
        <div class="border-b border-red-200 px-6 py-4 bg-red-50">
            <h3 class="font-semibold text-red-900">Vùng nguy hiểm</h3>
        </div>
        <div class="p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h4 class="font-medium text-gray-900">Reset toàn bộ dữ liệu</h4>
                    <p class="text-sm text-gray-500">Xóa tất cả dữ liệu và khôi phục về trạng thái ban đầu. Hành động này không thể hoàn tác!</p>
                </div>
                <button onclick="resetSystem()" class="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg">
                    Reset hệ thống
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// General settings form
document.getElementById('general-settings-form')?.addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const data = Object.fromEntries(formData.entries());
    data.type = 'general';
    
    saveSettings(data);
});

// Email settings form
document.getElementById('email-settings-form')?.addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const data = Object.fromEntries(formData.entries());
    data.type = 'email';
    
    saveSettings(data);
});

function saveSettings(data) {
    fetch('/php/api/admin-settings.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
    })
    .then(r => r.json())
    .then(result => {
        if (result.success) {
            showToast('Đã lưu cài đặt', 'success');
        } else {
            showToast(result.error || 'Có lỗi xảy ra', 'error');
        }
    })
    .catch(err => showToast('Lỗi kết nối', 'error'));
}

function testEmail() {
    showToast('Đang gửi email test...', 'info');
    fetch('/php/api/admin-settings.php?action=test_email')
        .then(r => r.json())
        .then(result => {
            if (result.success) {
                showToast('Đã gửi email test thành công!', 'success');
            } else {
                showToast(result.error || 'Không thể gửi email', 'error');
            }
        });
}

function clearCache() {
    if (!confirm('Bạn có chắc muốn xóa cache?')) return;
    fetch('/php/api/admin-settings.php?action=clear_cache')
        .then(r => r.json())
        .then(result => {
            showToast(result.success ? 'Đã xóa cache' : 'Có lỗi xảy ra', result.success ? 'success' : 'error');
        });
}

function backupDatabase() {
    showToast('Đang tạo backup...', 'info');
    
    // Create hidden iframe for download
    const iframe = document.createElement('iframe');
    iframe.style.display = 'none';
    iframe.src = '/php/api/admin-settings.php?action=backup';
    document.body.appendChild(iframe);
    
    // Remove iframe after download starts
    setTimeout(() => {
        document.body.removeChild(iframe);
        showToast('Backup đang được tải xuống', 'success');
    }, 2000);
}

function cleanupLogs() {
    if (!confirm('Xóa tất cả logs cũ hơn 30 ngày?')) return;
    fetch('/php/api/admin-settings.php?action=cleanup_logs')
        .then(r => r.json())
        .then(result => {
            showToast(result.success ? `Đã xóa ${result.deleted || 0} logs` : 'Có lỗi xảy ra', result.success ? 'success' : 'error');
        });
}

function resetSystem() {
    const confirm1 = prompt('Nhập "RESET" để xác nhận xóa toàn bộ dữ liệu:');
    if (confirm1 !== 'RESET') return;
    
    if (!confirm('LẦN CUỐI: Bạn có CHẮC CHẮN muốn xóa TẤT CẢ dữ liệu?')) return;
    
    showToast('Tính năng này đã bị vô hiệu hóa vì lý do an toàn', 'warning');
}
</script>

<?php View::endSection(); ?>
