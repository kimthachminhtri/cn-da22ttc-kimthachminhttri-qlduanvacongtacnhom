<?php
use Core\View;
use Core\Session;
use Core\Permission;

$userRole = Session::get('user_role', 'guest');
$isManager = in_array($userRole, ['admin', 'manager']);
?>
<header class="bg-white border-b border-gray-200 px-6 py-4">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <!-- Mobile Menu Toggle -->
            <button @click="sidebarOpen = !sidebarOpen" 
                    class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg md:hidden">
                <i data-lucide="menu" class="h-6 w-6"></i>
            </button>
            <h1 class="text-xl font-semibold text-gray-900"><?= View::e($pageTitle ?? 'Tổng quan') ?></h1>
        </div>
        
        <div class="flex items-center gap-3">
            <!-- Search -->
            <div class="relative hidden md:block">
                <i data-lucide="search" class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400"></i>
                <input type="text" placeholder="Tìm kiếm... (Ctrl+K)" 
                       class="w-64 pl-9 pr-4 py-2 rounded-lg border border-gray-300 text-sm focus:border-primary focus:ring-1 focus:ring-primary"
                       onclick="openSearchModal()">
            </div>
            
            <?php if ($isManager): ?>
            <!-- Quick Create Button -->
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" 
                        class="flex items-center gap-2 px-3 py-2 text-sm font-medium text-white bg-primary rounded-lg hover:bg-primary/90">
                    <i data-lucide="plus" class="h-4 w-4"></i>
                    <span class="hidden sm:inline">Tạo mới</span>
                </button>
                <div x-show="open" @click.away="open = false" x-cloak
                     class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-50">
                    <a href="#" onclick="openModal('create-project-modal'); return false;" 
                       class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        <i data-lucide="folder-plus" class="h-4 w-4 text-blue-500"></i>
                        Dự án mới
                    </a>
                    <a href="#" onclick="openModal('create-task-modal'); return false;" 
                       class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        <i data-lucide="check-square" class="h-4 w-4 text-green-500"></i>
                        Công việc mới
                    </a>
                    <a href="/php/calendar.php" 
                       class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        <i data-lucide="calendar-plus" class="h-4 w-4 text-purple-500"></i>
                        Sự kiện mới
                    </a>
                    <hr class="my-1">
                    <a href="/php/team.php" 
                       class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        <i data-lucide="user-plus" class="h-4 w-4 text-orange-500"></i>
                        Thêm thành viên
                    </a>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Notifications -->
            <a href="/php/notifications.php" class="relative p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100" id="notification-bell">
                <i data-lucide="bell" class="h-5 w-5"></i>
                <span data-notification-count id="notification-badge" class="absolute -top-1 -right-1 h-5 w-5 rounded-full bg-red-500 text-white text-xs flex items-center justify-center hidden">0</span>
            </a>
            
            <?php if ($isManager): ?>
            <!-- Quick Stats -->
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" 
                        class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100">
                    <i data-lucide="bar-chart-2" class="h-5 w-5"></i>
                </button>
                <div x-show="open" @click.away="open = false" x-cloak
                     class="absolute right-0 mt-2 w-64 bg-white rounded-lg shadow-lg border border-gray-200 p-4 z-50">
                    <h4 class="font-medium text-gray-900 mb-3">Thống kê nhanh</h4>
                    <div class="space-y-2 text-sm" id="quick-stats">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Dự án đang quản lý</span>
                            <span class="font-medium" id="qs-projects">-</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Công việc đang thực hiện</span>
                            <span class="font-medium" id="qs-tasks">-</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Công việc quá hạn</span>
                            <span class="font-medium text-red-600" id="qs-overdue">-</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Thành viên</span>
                            <span class="font-medium" id="qs-members">-</span>
                        </div>
                    </div>
                    <a href="/php/reports.php" class="block text-center text-sm text-primary hover:underline mt-3">
                        Xem báo cáo đầy đủ →
                    </a>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</header>

<script>
// Load notification count
fetch('/php/api/notifications.php?action=count')
    .then(r => r.json())
    .then(data => {
        if (data.success && data.count > 0) {
            const badge = document.getElementById('notification-badge');
            badge.textContent = data.count > 99 ? '99+' : data.count;
            badge.classList.remove('hidden');
            badge.style.display = 'flex';
        }
    })
    .catch(() => {});

<?php if ($isManager): ?>
// Load quick stats
fetch('/php/api/admin-stats.php')
    .then(r => {
        if (!r.ok) throw new Error('HTTP ' + r.status);
        return r.json();
    })
    .then(data => {
        console.log('Quick stats response:', data);
        if (data.success && data.stats) {
            document.getElementById('qs-projects').textContent = data.stats.projects ?? 0;
            document.getElementById('qs-tasks').textContent = data.stats.active_tasks ?? 0;
            document.getElementById('qs-overdue').textContent = data.stats.overdue_tasks ?? 0;
            document.getElementById('qs-members').textContent = data.stats.users ?? 0;
        } else if (data.error) {
            console.error('Stats error:', data.error);
        }
    })
    .catch(err => console.error('Quick stats fetch error:', err));
<?php endif; ?>

// Search modal shortcut - handled by search-modal.php Alpine component
document.addEventListener('keydown', function(e) {
    if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
        e.preventDefault();
        openSearchModal();
    }
});

function openSearchModal() {
    const modal = document.getElementById('search-modal');
    if (modal) {
        modal.classList.remove('hidden');
        setTimeout(() => {
            document.getElementById('search-input')?.focus();
        }, 100);
    }
}
</script>
