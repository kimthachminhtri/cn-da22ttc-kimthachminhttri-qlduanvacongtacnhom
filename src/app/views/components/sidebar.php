<?php
use Core\View;
use Core\Session;
use Core\Permission;

$currentPage = basename($_SERVER['PHP_SELF'], '.php');
$userRole = Session::get('user_role', 'guest');
$isManager = in_array($userRole, ['admin', 'manager']);

$navigation = [
    ['name' => 'Tổng quan', 'href' => 'index.php', 'icon' => 'layout-dashboard'],
    ['name' => 'Dự án', 'href' => 'projects.php', 'icon' => 'folder-kanban'],
    ['name' => 'Công việc', 'href' => 'tasks.php', 'icon' => 'check-square'],
    ['name' => 'Lịch', 'href' => 'calendar.php', 'icon' => 'calendar'],
    ['name' => 'Tài liệu', 'href' => 'documents.php', 'icon' => 'file-text'],
    ['name' => 'Nhóm', 'href' => 'team.php', 'icon' => 'users'],
    ['name' => 'Báo cáo', 'href' => 'reports.php', 'icon' => 'bar-chart-2'],
    ['name' => 'Cài đặt', 'href' => 'settings.php', 'icon' => 'settings'],
];

// Manager-specific navigation
$managerNav = [
    ['name' => 'Khối lượng CV', 'href' => 'manager/workload.php', 'icon' => 'user-cog'],
    ['name' => 'Lịch nhóm', 'href' => 'manager/team-calendar.php', 'icon' => 'calendar-range'],
    ['name' => 'Hiệu suất', 'href' => 'manager/performance.php', 'icon' => 'trending-up'],
];
?>

<aside class="flex h-screen w-64 flex-col bg-sidebar text-sidebar-foreground">
    <!-- Logo -->
    <div class="flex h-16 items-center gap-2 px-4 border-b border-sidebar-border">
        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-primary">
            <i data-lucide="folder-kanban" class="h-4 w-4 text-white"></i>
        </div>
        <span class="text-lg font-semibold text-white">TaskFlow</span>
        <?php if ($isManager): ?>
        <span class="ml-auto text-xs px-1.5 py-0.5 rounded bg-purple-500/20 text-purple-300 font-medium">
            <?= ucfirst($userRole) ?>
        </span>
        <?php endif; ?>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 space-y-1 px-3 py-4 overflow-y-auto">
        <?php foreach ($navigation as $item): ?>
            <?php 
            $isActive = $currentPage === basename($item['href'], '.php');
            $activeClass = $isActive 
                ? 'bg-sidebar-primary text-sidebar-primary-foreground' 
                : 'text-sidebar-foreground hover:bg-sidebar-accent hover:text-sidebar-accent-foreground';
            ?>
            <a href="/php/<?= View::e($item['href']) ?>" 
               class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors <?= $activeClass ?>">
                <i data-lucide="<?= View::e($item['icon']) ?>" class="h-4 w-4"></i>
                <?= View::e($item['name']) ?>
            </a>
        <?php endforeach; ?>
        
        <?php if ($isManager): ?>
            <hr class="border-sidebar-border my-4">
            <div class="px-3 mb-2">
                <span class="text-xs font-medium text-gray-400 uppercase tracking-wider">Manager Tools</span>
            </div>
            <?php foreach ($managerNav as $item): ?>
                <?php 
                $isActive = strpos($_SERVER['REQUEST_URI'], $item['href']) !== false;
                $activeClass = $isActive 
                    ? 'bg-purple-500/20 text-purple-300' 
                    : 'text-sidebar-foreground hover:bg-sidebar-accent hover:text-sidebar-accent-foreground';
                ?>
                <a href="/php/<?= View::e($item['href']) ?>" 
                   class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors <?= $activeClass ?>">
                    <i data-lucide="<?= View::e($item['icon']) ?>" class="h-4 w-4"></i>
                    <?= View::e($item['name']) ?>
                </a>
            <?php endforeach; ?>
        <?php endif; ?>
        
        <?php if (Permission::isAdmin($userRole)): ?>
            <hr class="border-sidebar-border my-4">
            <a href="/php/admin/index.php" 
               class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium text-orange-400 hover:bg-sidebar-accent">
                <i data-lucide="shield" class="h-4 w-4"></i>
                Admin Panel
            </a>
        <?php endif; ?>
    </nav>

    <!-- Projects Section -->
    <div class="px-3 py-4">
        <div class="flex items-center justify-between mb-2">
            <span class="text-xs font-medium text-gray-400 uppercase tracking-wider">Dự án</span>
            <?php if (Permission::can($userRole, 'projects.create')): ?>
            <button onclick="openModal('create-project-modal')" 
                    class="h-5 w-5 flex items-center justify-center text-gray-400 hover:text-white transition-colors">
                <i data-lucide="plus" class="h-3 w-3"></i>
            </button>
            <?php endif; ?>
        </div>
        <div class="space-y-1 text-sm text-gray-400">
            <p>Xem tất cả dự án →</p>
        </div>
    </div>

    <!-- User -->
    <?php
    $currentUser = null;
    $userId = Session::get('user_id');
    if ($userId) {
        $userModel = new \App\Models\User();
        $currentUser = $userModel->find($userId);
    }
    ?>
    <div class="border-t border-sidebar-border p-3" x-data="{ open: false }">
        <div class="relative">
            <div class="flex items-center gap-3 rounded-lg px-2 py-2 hover:bg-sidebar-accent transition-colors cursor-pointer"
                 @click="open = !open">
                <div class="h-8 w-8 rounded-full bg-gray-600 flex items-center justify-center overflow-hidden">
                    <?php if (!empty($currentUser['avatar_url'])): ?>
                        <img src="/php/<?= View::e($currentUser['avatar_url']) ?>" class="h-full w-full object-cover">
                    <?php else: ?>
                        <span class="text-sm font-medium text-white"><?= strtoupper(substr($currentUser['full_name'] ?? 'U', 0, 1)) ?></span>
                    <?php endif; ?>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-sidebar-foreground truncate"><?= View::e($currentUser['full_name'] ?? 'User') ?></p>
                    <p class="text-xs text-gray-400 truncate capitalize"><?= View::e($userRole) ?></p>
                </div>
                <i data-lucide="chevron-down" class="h-4 w-4 text-gray-400"></i>
            </div>
            
            <!-- Dropdown Menu -->
            <div x-show="open" 
                 @click.away="open = false"
                 x-cloak
                 class="absolute bottom-full left-0 right-0 mb-2 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-50">
                <a href="/php/settings.php" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                    <i data-lucide="user" class="h-4 w-4"></i>
                    Hồ sơ
                </a>
                <a href="/php/settings.php?tab=security" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                    <i data-lucide="settings" class="h-4 w-4"></i>
                    Cài đặt
                </a>
                <hr class="my-1 border-gray-200">
                <a href="/php/logout.php" class="flex items-center gap-2 px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                    <i data-lucide="log-out" class="h-4 w-4"></i>
                    Đăng xuất
                </a>
            </div>
        </div>
    </div>
</aside>
