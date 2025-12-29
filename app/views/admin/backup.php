<?php
/**
 * Admin Backup & Restore View
 */
use Core\View;

View::section('content');
?>

<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Backup & Restore</h1>
        <p class="text-gray-500 mt-1">Sao lưu và khôi phục dữ liệu hệ thống</p>
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
                    <p class="text-sm text-gray-500">Tải xuống bản sao lưu SQL</p>
                </div>
            </div>
            
            <div class="space-y-4">
                <div class="p-4 bg-gray-50 rounded-xl">
                    <p class="text-sm text-gray-600 mb-2">Bản sao lưu sẽ bao gồm:</p>
                    <ul class="text-sm text-gray-500 space-y-1">
                        <li class="flex items-center gap-2">
                            <i data-lucide="check" class="h-4 w-4 text-green-500"></i>
                            Tất cả bảng dữ liệu
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
                
                <a href="/php/admin/backup.php?download=1" 
                   class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 bg-green-600 text-white rounded-xl hover:bg-green-700 transition-colors">
                    <i data-lucide="download" class="h-5 w-5"></i>
                    Tải xuống Backup SQL
                </a>
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
                
                <form action="/php/api/admin-restore.php" method="POST" enctype="multipart/form-data" 
                      onsubmit="return confirm('Bạn có chắc muốn khôi phục? Dữ liệu hiện tại sẽ bị ghi đè!')">
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

async function clearCache() {
    if (!confirm('Xóa tất cả file cache?')) return;
    showToast('Đang xóa cache...', 'info');
    try {
        const response = await fetch('/php/api/admin-maintenance.php?action=clear_cache');
        const data = await response.json();
        if (data.success) {
            showToast(data.message, 'success');
        } else {
            showToast(data.error || 'Có lỗi xảy ra', 'error');
        }
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
        if (data.success) {
            showToast(data.message, 'success');
        } else {
            showToast(data.error || 'Có lỗi xảy ra', 'error');
        }
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
        if (data.success) {
            showToast(data.message, 'success');
        } else {
            showToast(data.error || 'Có lỗi xảy ra', 'error');
        }
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
            const statusType = health.overall === 'ok' ? 'success' : (health.overall === 'warning' ? 'warning' : 'error');
            showToast(statusText, statusType);
            
            // Show detailed modal
            showHealthModal(health);
        } else {
            showToast(data.error || 'Có lỗi xảy ra', 'error');
        }
    } catch (e) {
        showToast('Có lỗi xảy ra', 'error');
    }
}

function showHealthModal(health) {
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 z-50 flex items-center justify-center p-4';
    modal.innerHTML = `
        <div class="fixed inset-0 bg-black/50" onclick="this.parentElement.remove()"></div>
        <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-lg p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Kết quả kiểm tra hệ thống</h3>
                <button onclick="this.closest('.fixed').remove()" class="text-gray-400 hover:text-gray-600">
                    <i data-lucide="x" class="h-5 w-5"></i>
                </button>
            </div>
            <div class="space-y-3">
                ${Object.entries(health.checks).map(([key, check]) => `
                    <div class="flex items-center gap-3 p-3 rounded-lg ${check.status === 'ok' ? 'bg-green-50' : (check.status === 'warning' ? 'bg-yellow-50' : 'bg-red-50')}">
                        <i data-lucide="${check.status === 'ok' ? 'check-circle' : (check.status === 'warning' ? 'alert-triangle' : 'x-circle')}" 
                           class="h-5 w-5 ${check.status === 'ok' ? 'text-green-600' : (check.status === 'warning' ? 'text-yellow-600' : 'text-red-600')}"></i>
                        <div>
                            <p class="text-sm font-medium text-gray-900 capitalize">${key}</p>
                            <p class="text-xs text-gray-600">${check.message}</p>
                        </div>
                    </div>
                `).join('')}
            </div>
            <p class="text-xs text-gray-400 mt-4 text-right">Kiểm tra lúc: ${health.timestamp}</p>
        </div>
    `;
    document.body.appendChild(modal);
    lucide.createIcons();
}
</script>

<?php View::endSection(); ?>
