<?php
/**
 * Admin Dashboard View - Professional
 */
use Core\View;

View::section('content');
?>

<div class="space-y-6">
    <!-- Welcome Banner -->
    <div class="bg-gradient-to-r from-blue-600 to-blue-800 rounded-2xl p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold">Chào mừng trở lại, Admin! 👋</h1>
                <p class="text-blue-100 mt-1">Đây là tổng quan hệ thống của bạn hôm nay.</p>
            </div>
            <div class="hidden md:block">
                <p class="text-sm text-blue-200"><?= date('l, d/m/Y') ?></p>
                <p class="text-2xl font-bold"><?= date('H:i') ?></p>
            </div>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <!-- Total Users -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Tổng người dùng</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1"><?= number_format($stats['users'] ?? 0) ?></p>
                    <p class="text-sm text-gray-500 mt-2">
                        <span class="inline-flex items-center text-green-600">
                            <i data-lucide="trending-up" class="h-4 w-4 mr-1"></i>
                            <?= $stats['active_users'] ?? 0 ?> đang hoạt động
                        </span>
                    </p>
                </div>
                <div class="h-14 w-14 rounded-2xl bg-blue-100 flex items-center justify-center">
                    <i data-lucide="users" class="h-7 w-7 text-blue-600"></i>
                </div>
            </div>
        </div>
        
        <!-- Total Projects -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Tổng dự án</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1"><?= number_format($stats['projects'] ?? 0) ?></p>
                    <p class="text-sm text-gray-500 mt-2">
                        <span class="inline-flex items-center text-green-600">
                            <i data-lucide="activity" class="h-4 w-4 mr-1"></i>
                            <?= $stats['active_projects'] ?? 0 ?> đang thực hiện
                        </span>
                    </p>
                </div>
                <div class="h-14 w-14 rounded-2xl bg-green-100 flex items-center justify-center">
                    <i data-lucide="folder-kanban" class="h-7 w-7 text-green-600"></i>
                </div>
            </div>
        </div>
        
        <!-- Total Tasks -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Tổng công việc</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1"><?= number_format($stats['tasks'] ?? 0) ?></p>
                    <p class="text-sm text-gray-500 mt-2">
                        <span class="inline-flex items-center text-green-600">
                            <i data-lucide="check-circle" class="h-4 w-4 mr-1"></i>
                            <?= $stats['completed_tasks'] ?? 0 ?> hoàn thành
                        </span>
                    </p>
                </div>
                <div class="h-14 w-14 rounded-2xl bg-yellow-100 flex items-center justify-center">
                    <i data-lucide="check-square" class="h-7 w-7 text-yellow-600"></i>
                </div>
            </div>
        </div>
        
        <!-- Total Documents -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Tổng tài liệu</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1"><?= number_format($stats['documents'] ?? 0) ?></p>
                    <p class="text-sm text-gray-500 mt-2">
                        <span class="inline-flex items-center text-blue-600">
                            <i data-lucide="hard-drive" class="h-4 w-4 mr-1"></i>
                            <?= formatBytes($stats['storage_used'] ?? 0) ?>
                        </span>
                    </p>
                </div>
                <div class="h-14 w-14 rounded-2xl bg-purple-100 flex items-center justify-center">
                    <i data-lucide="file-text" class="h-7 w-7 text-purple-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Thao tác nhanh</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <a href="/php/admin/users.php?action=create" 
               class="flex flex-col items-center gap-2 p-4 rounded-xl bg-blue-50 hover:bg-blue-100 transition-colors group">
                <div class="h-12 w-12 rounded-xl bg-blue-500 flex items-center justify-center text-white group-hover:scale-110 transition-transform">
                    <i data-lucide="user-plus" class="h-6 w-6"></i>
                </div>
                <span class="text-sm font-medium text-gray-700">Thêm người dùng</span>
            </a>
            
            <a href="/php/admin/backup.php" 
               class="flex flex-col items-center gap-2 p-4 rounded-xl bg-green-50 hover:bg-green-100 transition-colors group">
                <div class="h-12 w-12 rounded-xl bg-green-500 flex items-center justify-center text-white group-hover:scale-110 transition-transform">
                    <i data-lucide="database" class="h-6 w-6"></i>
                </div>
                <span class="text-sm font-medium text-gray-700">Backup Database</span>
            </a>
            
            <a href="/php/admin/settings.php" 
               class="flex flex-col items-center gap-2 p-4 rounded-xl bg-yellow-50 hover:bg-yellow-100 transition-colors group">
                <div class="h-12 w-12 rounded-xl bg-yellow-500 flex items-center justify-center text-white group-hover:scale-110 transition-transform">
                    <i data-lucide="settings" class="h-6 w-6"></i>
                </div>
                <span class="text-sm font-medium text-gray-700">Cài đặt hệ thống</span>
            </a>
            
            <a href="/php/admin/logs.php" 
               class="flex flex-col items-center gap-2 p-4 rounded-xl bg-purple-50 hover:bg-purple-100 transition-colors group">
                <div class="h-12 w-12 rounded-xl bg-purple-500 flex items-center justify-center text-white group-hover:scale-110 transition-transform">
                    <i data-lucide="scroll-text" class="h-6 w-6"></i>
                </div>
                <span class="text-sm font-medium text-gray-700">Xem Activity Logs</span>
            </a>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid gap-6 lg:grid-cols-3">
        <!-- Recent Users -->
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100">
            <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4">
                <h3 class="font-semibold text-gray-900">Người dùng mới nhất</h3>
                <a href="/php/admin/users.php" class="text-sm text-blue-600 hover:text-blue-700">Xem tất cả →</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Người dùng</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Vai trò</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Trạng thái</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ngày tạo</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php if (!empty($recentUsers)): ?>
                            <?php foreach ($recentUsers as $user): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="h-10 w-10 rounded-full bg-gradient-to-br from-blue-500 to-blue-700 flex items-center justify-center text-white font-medium">
                                            <?= strtoupper(substr($user['full_name'], 0, 1)) ?>
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-900"><?= View::e($user['full_name']) ?></p>
                                            <p class="text-sm text-gray-500"><?= View::e($user['email']) ?></p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        <?= $user['role'] === 'admin' ? 'bg-red-100 text-red-800' : 
                                           ($user['role'] === 'manager' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') ?>">
                                        <?= ucfirst($user['role']) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <?php if ($user['is_active']): ?>
                                        <span class="inline-flex items-center gap-1 text-green-600 text-sm">
                                            <span class="h-2 w-2 rounded-full bg-green-500"></span>
                                            Hoạt động
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center gap-1 text-gray-500 text-sm">
                                            <span class="h-2 w-2 rounded-full bg-gray-400"></span>
                                            Vô hiệu
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    <?= date('d/m/Y', strtotime($user['created_at'])) ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-gray-500">Chưa có người dùng</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- System Info & Activity -->
        <div class="space-y-6">
            <!-- System Info -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="font-semibold text-gray-900 mb-4">Thông tin hệ thống</h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500">PHP Version</span>
                        <span class="text-sm font-medium text-gray-900"><?= phpversion() ?></span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500">MySQL Version</span>
                        <span class="text-sm font-medium text-gray-900"><?= $systemInfo['mysql_version'] ?? 'N/A' ?></span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500">Server</span>
                        <span class="text-sm font-medium text-gray-900"><?= $_SERVER['SERVER_SOFTWARE'] ?? 'N/A' ?></span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500">Memory Limit</span>
                        <span class="text-sm font-medium text-gray-900"><?= ini_get('memory_limit') ?></span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500">Upload Max</span>
                        <span class="text-sm font-medium text-gray-900"><?= ini_get('upload_max_filesize') ?></span>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
                <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4">
                    <h3 class="font-semibold text-gray-900">Hoạt động gần đây</h3>
                    <a href="/php/admin/logs.php" class="text-sm text-blue-600 hover:text-blue-700">Xem tất cả</a>
                </div>
                <div class="divide-y divide-gray-100 max-h-80 overflow-y-auto">
                    <?php if (!empty($activities)): ?>
                        <?php foreach (array_slice($activities, 0, 8) as $activity): ?>
                        <div class="px-6 py-3 hover:bg-gray-50">
                            <div class="flex items-start gap-3">
                                <div class="h-8 w-8 rounded-full bg-gray-100 flex items-center justify-center flex-shrink-0">
                                    <i data-lucide="<?= getActivityIcon($activity['action']) ?>" class="h-4 w-4 text-gray-600"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm text-gray-900">
                                        <span class="font-medium"><?= View::e($activity['full_name'] ?? 'System') ?></span>
                                        <span class="text-gray-500"><?= getActivityText($activity['action']) ?></span>
                                    </p>
                                    <p class="text-xs text-gray-400 mt-0.5"><?= timeAgo($activity['created_at']) ?></p>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="px-6 py-8 text-center text-gray-500">Chưa có hoạt động</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid gap-6 lg:grid-cols-2">
        <!-- User Roles Distribution -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-semibold text-gray-900 mb-4">Phân bố vai trò người dùng</h3>
            <div class="flex items-center justify-center gap-8">
                <div class="relative h-40 w-40">
                    <!-- Simple CSS Pie Chart -->
                    <div class="absolute inset-0 rounded-full" style="background: conic-gradient(
                        #ef4444 0deg <?= ($roleStats['admin'] ?? 0) * 3.6 ?>deg,
                        #3b82f6 <?= ($roleStats['admin'] ?? 0) * 3.6 ?>deg <?= (($roleStats['admin'] ?? 0) + ($roleStats['manager'] ?? 0)) * 3.6 ?>deg,
                        #22c55e <?= (($roleStats['admin'] ?? 0) + ($roleStats['manager'] ?? 0)) * 3.6 ?>deg <?= (($roleStats['admin'] ?? 0) + ($roleStats['manager'] ?? 0) + ($roleStats['member'] ?? 0)) * 3.6 ?>deg,
                        #9ca3af <?= (($roleStats['admin'] ?? 0) + ($roleStats['manager'] ?? 0) + ($roleStats['member'] ?? 0)) * 3.6 ?>deg 360deg
                    );"></div>
                    <div class="absolute inset-4 bg-white rounded-full flex items-center justify-center">
                        <span class="text-2xl font-bold text-gray-900"><?= $stats['users'] ?? 0 ?></span>
                    </div>
                </div>
                <div class="space-y-3">
                    <div class="flex items-center gap-2">
                        <span class="h-3 w-3 rounded-full bg-red-500"></span>
                        <span class="text-sm text-gray-600">Admin (<?= $roleStats['admin'] ?? 0 ?>%)</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="h-3 w-3 rounded-full bg-blue-500"></span>
                        <span class="text-sm text-gray-600">Manager (<?= $roleStats['manager'] ?? 0 ?>%)</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="h-3 w-3 rounded-full bg-green-500"></span>
                        <span class="text-sm text-gray-600">Member (<?= $roleStats['member'] ?? 0 ?>%)</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="h-3 w-3 rounded-full bg-gray-400"></span>
                        <span class="text-sm text-gray-600">Guest (<?= $roleStats['guest'] ?? 0 ?>%)</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Task Status -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-semibold text-gray-900 mb-4">Trạng thái công việc</h3>
            <div class="space-y-4">
                <?php 
                $taskStatuses = [
                    ['name' => 'Hoàn thành', 'count' => $taskStats['done'] ?? 0, 'color' => 'bg-green-500', 'percent' => $taskStats['done_percent'] ?? 0],
                    ['name' => 'Đang làm', 'count' => $taskStats['in_progress'] ?? 0, 'color' => 'bg-blue-500', 'percent' => $taskStats['in_progress_percent'] ?? 0],
                    ['name' => 'Chờ xử lý', 'count' => $taskStats['todo'] ?? 0, 'color' => 'bg-yellow-500', 'percent' => $taskStats['todo_percent'] ?? 0],
                    ['name' => 'Quá hạn', 'count' => $taskStats['overdue'] ?? 0, 'color' => 'bg-red-500', 'percent' => $taskStats['overdue_percent'] ?? 0],
                ];
                foreach ($taskStatuses as $status): ?>
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-sm text-gray-600"><?= $status['name'] ?></span>
                        <span class="text-sm font-medium text-gray-900"><?= $status['count'] ?></span>
                    </div>
                    <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-full <?= $status['color'] ?> rounded-full transition-all" style="width: <?= $status['percent'] ?>%"></div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- New Widgets Row -->
    <div class="grid gap-6 lg:grid-cols-3">
        <!-- User Growth Chart -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-gray-900">Tăng trưởng người dùng</h3>
                <span class="text-xs text-gray-500">6 tháng gần nhất</span>
            </div>
            <?php if (!empty($userGrowth)): ?>
            <div class="flex items-end justify-between gap-2 h-32">
                <?php 
                $maxCount = max(array_column($userGrowth, 'count'));
                $maxCount = max($maxCount, 1);
                foreach ($userGrowth as $month): 
                    $height = ($month['count'] / $maxCount) * 100;
                ?>
                <div class="flex-1 flex flex-col items-center gap-1">
                    <span class="text-xs font-medium text-gray-700"><?= $month['count'] ?></span>
                    <div class="w-full bg-blue-500 rounded-t transition-all hover:bg-blue-600" 
                         style="height: <?= max($height, 5) ?>%"
                         title="<?= $month['label'] ?>: <?= $month['count'] ?> users"></div>
                    <span class="text-xs text-gray-400"><?= substr($month['label'], 0, 2) ?></span>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="h-32 flex items-center justify-center text-gray-400 text-sm">
                Chưa có dữ liệu
            </div>
            <?php endif; ?>
        </div>

        <!-- Storage Usage -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-gray-900">Dung lượng lưu trữ</h3>
                <span class="text-xs text-gray-500"><?= formatBytes($stats['storage_used'] ?? 0) ?></span>
            </div>
            <?php if (!empty($storageBreakdown)): ?>
            <div class="space-y-3">
                <?php 
                $totalStorage = max($stats['storage_used'] ?? 1, 1);
                $typeColors = [
                    'images' => ['bg' => 'bg-purple-500', 'icon' => 'image'],
                    'pdf' => ['bg' => 'bg-red-500', 'icon' => 'file-text'],
                    'documents' => ['bg' => 'bg-blue-500', 'icon' => 'file'],
                    'other' => ['bg' => 'bg-gray-500', 'icon' => 'file-question'],
                ];
                $typeNames = [
                    'images' => 'Hình ảnh',
                    'pdf' => 'PDF',
                    'documents' => 'Tài liệu',
                    'other' => 'Khác',
                ];
                foreach ($storageBreakdown as $item): 
                    $percent = round(($item['total_size'] / $totalStorage) * 100);
                    $color = $typeColors[$item['file_type']] ?? $typeColors['other'];
                ?>
                <div class="flex items-center gap-3">
                    <div class="h-8 w-8 rounded-lg <?= $color['bg'] ?> bg-opacity-20 flex items-center justify-center">
                        <i data-lucide="<?= $color['icon'] ?>" class="h-4 w-4 <?= str_replace('bg-', 'text-', $color['bg']) ?>"></i>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-700"><?= $typeNames[$item['file_type']] ?? $item['file_type'] ?></span>
                            <span class="text-gray-500"><?= formatBytes($item['total_size']) ?></span>
                        </div>
                        <div class="h-1.5 bg-gray-100 rounded-full mt-1 overflow-hidden">
                            <div class="h-full <?= $color['bg'] ?> rounded-full" style="width: <?= $percent ?>%"></div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="h-24 flex items-center justify-center text-gray-400 text-sm">
                Chưa có tài liệu
            </div>
            <?php endif; ?>
        </div>

        <!-- Error Rate & System Health -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-semibold text-gray-900 mb-4">Sức khỏe hệ thống</h3>
            <div class="space-y-4">
                <!-- Error Rate -->
                <div class="flex items-center justify-between p-3 rounded-xl <?= ($errorCount ?? 0) > 10 ? 'bg-red-50' : 'bg-green-50' ?>">
                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 rounded-lg <?= ($errorCount ?? 0) > 10 ? 'bg-red-100' : 'bg-green-100' ?> flex items-center justify-center">
                            <i data-lucide="<?= ($errorCount ?? 0) > 10 ? 'alert-triangle' : 'check-circle' ?>" 
                               class="h-5 w-5 <?= ($errorCount ?? 0) > 10 ? 'text-red-600' : 'text-green-600' ?>"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">Lỗi 24h qua</p>
                            <p class="text-xs text-gray-500"><?= ($errorCount ?? 0) > 10 ? 'Cần kiểm tra' : 'Bình thường' ?></p>
                        </div>
                    </div>
                    <span class="text-2xl font-bold <?= ($errorCount ?? 0) > 10 ? 'text-red-600' : 'text-green-600' ?>"><?= $errorCount ?? 0 ?></span>
                </div>
                
                <!-- Disk Usage (simulated) -->
                <?php 
                $diskFree = disk_free_space('.') ?: 0;
                $diskTotal = disk_total_space('.') ?: 1;
                $diskUsedPercent = round((1 - $diskFree / $diskTotal) * 100);
                ?>
                <div class="flex items-center justify-between p-3 rounded-xl bg-gray-50">
                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 rounded-lg bg-blue-100 flex items-center justify-center">
                            <i data-lucide="hard-drive" class="h-5 w-5 text-blue-600"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">Disk Usage</p>
                            <p class="text-xs text-gray-500"><?= formatBytes($diskFree) ?> còn trống</p>
                        </div>
                    </div>
                    <span class="text-lg font-bold text-gray-700"><?= $diskUsedPercent ?>%</span>
                </div>
                
                <!-- Memory -->
                <div class="flex items-center justify-between p-3 rounded-xl bg-gray-50">
                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 rounded-lg bg-purple-100 flex items-center justify-center">
                            <i data-lucide="cpu" class="h-5 w-5 text-purple-600"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">Memory Limit</p>
                            <p class="text-xs text-gray-500">PHP Memory</p>
                        </div>
                    </div>
                    <span class="text-lg font-bold text-gray-700"><?= ini_get('memory_limit') ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Upcoming Deadlines -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
        <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4">
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-xl bg-orange-100 flex items-center justify-center">
                    <i data-lucide="clock" class="h-5 w-5 text-orange-600"></i>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-900">Deadline sắp tới</h3>
                    <p class="text-sm text-gray-500">Công việc đến hạn trong 7 ngày</p>
                </div>
            </div>
            <a href="/php/admin/tasks.php" class="text-sm text-blue-600 hover:text-blue-700">Xem tất cả →</a>
        </div>
        <?php if (!empty($upcomingDeadlines)): ?>
        <div class="divide-y divide-gray-100">
            <?php foreach ($upcomingDeadlines as $task): 
                $daysLeft = (strtotime($task['due_date']) - strtotime('today')) / 86400;
                $urgencyClass = $daysLeft <= 1 ? 'bg-red-50 border-red-200' : ($daysLeft <= 3 ? 'bg-yellow-50 border-yellow-200' : 'bg-gray-50 border-gray-200');
                $priorityColors = [
                    'urgent' => 'text-red-600 bg-red-100',
                    'high' => 'text-orange-600 bg-orange-100',
                    'medium' => 'text-yellow-600 bg-yellow-100',
                    'low' => 'text-gray-600 bg-gray-100',
                ];
            ?>
            <div class="px-6 py-4 hover:bg-gray-50 transition-colors">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="h-10 w-10 rounded-lg flex items-center justify-center <?= $urgencyClass ?> border">
                            <?php if ($daysLeft <= 0): ?>
                            <i data-lucide="alert-circle" class="h-5 w-5 text-red-600"></i>
                            <?php elseif ($daysLeft <= 1): ?>
                            <span class="text-sm font-bold text-red-600">1d</span>
                            <?php else: ?>
                            <span class="text-sm font-bold text-gray-600"><?= (int)$daysLeft ?>d</span>
                            <?php endif; ?>
                        </div>
                        <div>
                            <a href="/php/task-detail.php?id=<?= $task['id'] ?>" class="font-medium text-gray-900 hover:text-blue-600">
                                <?= View::e($task['title']) ?>
                            </a>
                            <div class="flex items-center gap-2 mt-1">
                                <?php if ($task['project_name']): ?>
                                <span class="text-xs px-2 py-0.5 rounded-full" 
                                      style="background-color: <?= $task['project_color'] ?? '#3b82f6' ?>20; color: <?= $task['project_color'] ?? '#3b82f6' ?>">
                                    <?= View::e($task['project_name']) ?>
                                </span>
                                <?php endif; ?>
                                <span class="text-xs <?= $priorityColors[$task['priority']] ?? '' ?> px-2 py-0.5 rounded-full">
                                    <?= ucfirst($task['priority']) ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-medium <?= $daysLeft <= 1 ? 'text-red-600' : 'text-gray-700' ?>">
                            <?= date('d/m/Y', strtotime($task['due_date'])) ?>
                        </p>
                        <?php if ($task['assignee_name']): ?>
                        <p class="text-xs text-gray-500"><?= View::e($task['assignee_name']) ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="px-6 py-12 text-center text-gray-500">
            <i data-lucide="calendar-check" class="h-12 w-12 mx-auto mb-3 text-gray-300"></i>
            <p>Không có deadline nào trong 7 ngày tới</p>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php 
// Helper functions
function formatBytes($bytes, $precision = 2) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    return round($bytes / pow(1024, $pow), $precision) . ' ' . $units[$pow];
}

function getActivityIcon($action) {
    $icons = [
        'create' => 'plus-circle',
        'update' => 'edit',
        'delete' => 'trash-2',
        'login' => 'log-in',
        'logout' => 'log-out',
    ];
    return $icons[$action] ?? 'activity';
}

function getActivityText($action) {
    $texts = [
        'create' => 'đã tạo mới',
        'update' => 'đã cập nhật',
        'delete' => 'đã xóa',
        'login' => 'đã đăng nhập',
        'logout' => 'đã đăng xuất',
    ];
    return $texts[$action] ?? $action;
}

// timeAgo() is already defined in includes/functions.php

View::endSection(); 
?>
