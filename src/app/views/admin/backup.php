<?php
/**
 * Admin Backup & Restore View
 */
use Core\View;

View::section('content');

$backups = $backups ?? [];
$dbStats = $dbStats ?? ['tables' => 0, 'size' => 0];
$uploadSize = $uploadSize ?? 0;
?>

<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Backup & Restore</h1>
        <p class="text-gray-500 mt-1">Sao lưu và khôi phục dữ liệu hệ thống</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid gap-4 sm:grid-cols-3">
        <div class="bg-white rounded-xl border border-gray-100 p-4">
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-lg bg-blue-100 flex items-center justify-center">
                    <i data-lucide="database" class="h-5 w-5 text-blue-600"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Database</p>
                    <p class="text-lg font-semibold text-gray-900"><?= $dbStats['tables'] ?> bảng / <?= $dbStats['size'] ?> MB</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-4">
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-lg bg-green-100 flex items-center justify-center">
                    <i data-lucide="hard-drive" class="h-5 w-5 text-green-600"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Uploads</p>
                    <p class="text-lg font-semibold text-gray-900"><?= round($uploadSize / 1024 / 1024, 2) ?> MB</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-4">
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-lg bg-purple-100 flex items-center justify-center">
                    <i data-lucide="archive" class="h-5 w-5 text-purple-600"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Backups</p>
                    <p class="text-lg font-semibold text-gray-900"><?= count($backups) ?> bản</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <!-- Backup Section -->
        <div class="bg-white rounded-2xl border border-gray-100 p-6">
            <div class="flex items-center gap-4 mb-6">
                <div class="h-12 w-12 rounded-xl bg-green-100 flex items-center justify-center">
                    <i data-lucide="download" class="h-6 w-6 text-green-600"></i>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Sao lưu Database</h2>
                    <p class="text-sm text-gray-500">Tạo bản sao lưu SQL</p>
                </div>
            </div>
            
            <div class="space-y-4">
                <div class="p-4 bg-gray-50 rounded-xl">
                    <p class="text-sm text-gray-600 mb-2">Bản sao lưu sẽ bao gồm:</p>
                    <ul class="text-sm text-gray-500 space-y-1">
                        <li class="flex items-center gap-2">
                            <i data-lucide="check" class="h-4 w-4 text-green-500"></i>
                            Tất cả <?= $dbStats['tables'] ?> bảng dữ liệu
                        </li>
                        <li class="flex items-center gap-2">
                            <i data-lucide="check" class="h-4 w-4 text-green-500"></i>
                            Cấu trúc bảng (CREATE TABLE)
                        </li>
                        <li class="flex items-center gap-2">
                            <i data-lucide="check" class="h-4 w-4 text-green-500"></i>
                            Dữ liệu (INSERT statements)
                        </li>
                    </ul>
                </div>
                
                <div class="flex gap-3">
                    <form action="/php/api/admin-maintenance.php" method="POST" class="flex-1">
                        <input type="hidden" name="action" value="create_backup">
                        <input type="hidden" name="_csrf_token" value="<?= \Core\CSRF::generate() ?>">
                        <button type="submit" 
                                class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 bg-green-600 text-white rounded-xl hover:bg-green-700 transition-colors">
                            <i data-lucide="plus" class="h-5 w-5"></i>
                            Tạo Backup mới
                        </button>
                    </form>
                    
                    <a href="/php/api/admin-maintenance.php?action=download_backup" 
                       class="inline-flex items-center justify-center gap-2 px-4 py-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-colors">
                        <i data-lucide="download" class="h-5 w-5"></i>
                        Tải xuống
                    </a>
                </div>
            </div>
        </div>

        <!-- Restore Section -->
        <div class="bg-white rounded-2xl border border-gray-100 p-6">
            <div class="flex items-center gap-4 mb-6">
                <div class="h-12 w-12 rounded-xl bg-blue-100 flex items-center justify-center">
                    <i data-lucide="upload" class="h-6 w-6 text-blue-600"></i>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Khôi phục Database</h2>
                    <p class="text-sm text-gray-500">Khôi phục từ file SQL</p>
                </div>
            </div>
            
            <div class="space-y-4">
                <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-xl">
                    <div class="flex items-start gap-3">
                        <i data-lucide="alert-triangle" class="h-5 w-5 text-yellow-600 flex-shrink-0 mt-0.5"></i>
                        <div>
                            <p class="text-sm font-medium text-yellow-800">Cảnh báo</p>
                            <p class="text-sm text-yellow-700 mt-1">
                                Khôi phục sẽ ghi đè toàn bộ dữ liệu hiện tại. Hãy chắc chắn bạn đã sao lưu trước khi thực hiện.
                            </p>
                        </div>
                    </div>
                </div>
                
                <form action="/php/api/admin-maintenance.php" method="POST" enctype="multipart/form-data" 
                      onsubmit="return confirm('Bạn có chắc muốn khôi phục? Dữ liệu hiện tại sẽ bị ghi đè!')">
                    <input type="hidden" name="action" value="restore_backup">
                    <input type="hidden" name="_csrf_token" value="<?= \Core\CSRF::generate() ?>">
                    <div class="border-2 border-dashed border-gray-200 rounded-xl p-6 text-center hover:border-blue-400 transition-colors">
                        <input type="file" name="backup_file" accept=".sql" required
                               class="hidden" id="backup-file" onchange="updateFileName(this)">
                        <label for="backup-file" class="cursor-pointer">
                            <i data-lucide="upload-cloud" class="h-10 w-10 mx-auto text-gray-400 mb-3"></i>
                            <p class="text-sm text-gray-600" id="file-name">Chọn file SQL để khôi phục</p>
                            <p class="text-xs text-gray-400 mt-1">Chỉ chấp nhận file .sql</p>
                        </label>
                    </div>
                    
                    <button type="submit" 
                            class="w-full mt-4 inline-flex items-center justify-center gap-2 px-4 py-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-colors">
                        <i data-lucide="upload" class="h-5 w-5"></i>
                        Khôi phục Database
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Backup History -->
    <?php if (!empty($backups)): ?>
    <div class="bg-white rounded-2xl border border-gray-100 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Lịch sử Backup</h2>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-100">
                        <th class="text-left py-3 px-4 text-sm font-medium text-gray-500">Tên file</th>
                        <th class="text-left py-3 px-4 text-sm font-medium text-gray-500">Kích thước</th>
                        <th class="text-left py-3 px-4 text-sm font-medium text-gray-500">Ngày tạo</th>
                        <th class="text-right py-3 px-4 text-sm font-medium text-gray-500">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($backups as $backup): ?>
                    <tr class="border-b border-gray-50 hover:bg-gray-50">
                        <td class="py-3 px-4">
                            <div class="flex items-center gap-2">
                                <i data-lucide="file-text" class="h-4 w-4 text-gray-400"></i>
                                <span class="text-sm text-gray-900"><?= htmlspecialchars($backup['filename']) ?></span>
                            </div>
                        </td>
                        <td class="py-3 px-4 text-sm text-gray-600">
                            <?= round($backup['size'] / 1024, 2) ?> KB
                        </td>
                        <td class="py-3 px-4 text-sm text-gray-600">
                            <?= $backup['created_at'] ?>
                        </td>
                        <td class="py-3 px-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="/php/api/admin-maintenance.php?action=download_backup&file=<?= urlencode($backup['filename']) ?>" 
                                   class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg" title="Tải xuống">
                                    <i data-lucide="download" class="h-4 w-4"></i>
                                </a>
                                <form action="/php/api/admin-maintenance.php" method="POST" class="inline"
                                      onsubmit="return confirm('Khôi phục từ backup này?')">
                                    <input type="hidden" name="action" value="restore_from_file">
                                    <input type="hidden" name="filename" value="<?= htmlspecialchars($backup['filename']) ?>">
                                    <input type="hidden" name="_csrf_token" value="<?= \Core\CSRF::generate() ?>">
                                    <button type="submit" class="p-2 text-green-600 hover:bg-green-50 rounded-lg" title="Khôi phục">
                                        <i data-lucide="rotate-ccw" class="h-4 w-4"></i>
                                    </button>
                                </form>
                                <button onclick="deleteBackup('<?= htmlspecialchars($backup['filename']) ?>')" 
                                        class="p-2 text-red-600 hover:bg-red-50 rounded-lg" title="Xóa">
                                    <i data-lucide="trash-2" class="h-4 w-4"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>

    <!-- Maintenance Tools -->
    <div class="bg-white rounded-2xl border border-gray-100 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-6">Công cụ bảo trì</h2>
        
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <button onclick="clearCache()" 
                    class="flex items-center gap-3 p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors text-left">
                <div class="h-10 w-10 rounded-lg bg-purple-100 flex items-center justify-center">
                    <i data-lucide="trash" class="h-5 w-5 text-purple-600"></i>
                </div>
                <div>
                    <p class="font-medium text-gray-900">Xóa Cache</p>
                    <p class="text-xs text-gray-500">Xóa file cache tạm</p>
                </div>
            </button>
            
            <button onclick="clearLogs()" 
                    class="flex items-center gap-3 p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors text-left">
                <div class="h-10 w-10 rounded-lg bg-orange-100 flex items-center justify-center">
                    <i data-lucide="file-x" class="h-5 w-5 text-orange-600"></i>
                </div>
                <div>
                    <p class="font-medium text-gray-900">Xóa Logs cũ</p>
                    <p class="text-xs text-gray-500">Xóa logs > 30 ngày</p>
                </div>
            </button>
            
            <button onclick="optimizeDb()" 
                    class="flex items-center gap-3 p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors text-left">
                <div class="h-10 w-10 rounded-lg bg-green-100 flex items-center justify-center">
                    <i data-lucide="zap" class="h-5 w-5 text-green-600"></i>
                </div>
                <div>
                    <p class="font-medium text-gray-900">Tối ưu Database</p>
                    <p class="text-xs text-gray-500">OPTIMIZE tables</p>
                </div>
            </button>
            
            <button onclick="checkHealth()" 
                    class="flex items-center gap-3 p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors text-left">
                <div class="h-10 w-10 rounded-lg bg-blue-100 flex items-center justify-center">
                    <i data-lucide="activity" class="h-5 w-5 text-blue-600"></i>
                </div>
                <div>
                    <p class="font-medium text-gray-900">Kiểm tra hệ thống</p>
                    <p class="text-xs text-gray-500">Health check</p>
                </div>
            </button>
        </div>
    </div>
</div>

<script>
function updateFileName(input) {
    const fileName = input.files[0]?.name || 'Chọn file SQL để khôi phục';
    document.getElementById('file-name').textContent = fileName;
}

async function deleteBackup(filename) {
    if (!confirm('Xóa backup này?')) return;
    
    try {
        const response = await fetch('/php/api/admin-maintenance.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: 'delete_backup', filename: filename })
        });
        const data = await response.json();
        if (data.success) {
            showToast('Đã xóa backup', 'success');
            location.reload();
        } else {
            showToast(data.error || 'Có lỗi xảy ra', 'error');
        }
    } catch (e) {
        showToast('Có lỗi xảy ra', 'error');
    }
}

async function clearCache() {
    if (!confirm('Xóa tất cả file cache?')) return;
    showToast('Đang xóa cache...', 'info');
    try {
        const response = await fetch('/php/api/admin-maintenance.php?action=clear_cache');
        const data = await response.json();
        showToast(data.success ? data.message : (data.error || 'Có lỗi'), data.success ? 'success' : 'error');
    } catch (e) {
        showToast('Có lỗi xảy ra', 'error');
    }
}

async function clearLogs() {
    if (!confirm('Xóa logs cũ hơn 30 ngày?')) return;
    showToast('Đang xóa logs...', 'info');
    try {
        const response = await fetch('/php/api/admin-maintenance.php?action=clear_logs');
        const data = await response.json();
        showToast(data.success ? data.message : (data.error || 'Có lỗi'), data.success ? 'success' : 'error');
    } catch (e) {
        showToast('Có lỗi xảy ra', 'error');
    }
}

async function optimizeDb() {
    if (!confirm('Tối ưu hóa database?')) return;
    showToast('Đang tối ưu database...', 'info');
    try {
        const response = await fetch('/php/api/admin-maintenance.php?action=optimize_db');
        const data = await response.json();
        showToast(data.success ? data.message : (data.error || 'Có lỗi'), data.success ? 'success' : 'error');
    } catch (e) {
        showToast('Có lỗi xảy ra', 'error');
    }
}

async function checkHealth() {
    showToast('Đang kiểm tra hệ thống...', 'info');
    try {
        const response = await fetch('/php/api/admin-maintenance.php?action=health_check');
        const data = await response.json();
        if (data.success) {
            const health = data.data;
            const statusText = health.overall === 'ok' ? 'Hệ thống hoạt động bình thường' : 
                              (health.overall === 'warning' ? 'Có một số cảnh báo' : 'Có lỗi cần xử lý');
            showToast(statusText, health.overall === 'ok' ? 'success' : (health.overall === 'warning' ? 'warning' : 'error'));
        } else {
            showToast(data.error || 'Có lỗi xảy ra', 'error');
        }
    } catch (e) {
        showToast('Có lỗi xảy ra', 'error');
    }
}
</script>

<?php View::endSection(); ?>
