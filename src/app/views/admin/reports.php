<?php
/**
 * Admin Reports & Statistics View
 */
use Core\View;

View::section('content');

// Helper function
function formatBytes($bytes, $precision = 2) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    return round($bytes / pow(1024, $pow), $precision) . ' ' . $units[$pow];
}

function getGrowthPercent($current, $previous) {
    if ($previous == 0) return $current > 0 ? 100 : 0;
    return round((($current - $previous) / $previous) * 100);
}

$statusColors = [
    'done' => '#22c55e',
    'in_progress' => '#3b82f6',
    'todo' => '#eab308',
    'backlog' => '#9ca3af',
    'in_review' => '#a855f7',
];

$priorityColors = [
    'urgent' => '#ef4444',
    'high' => '#f97316',
    'medium' => '#eab308',
    'low' => '#9ca3af',
];
?>

<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Báo cáo & Thống kê</h1>
            <p class="text-gray-500 mt-1">
                Dữ liệu từ <span class="font-medium text-gray-700"><?= date('d/m/Y', strtotime($range['start'])) ?></span> 
                đến <span class="font-medium text-gray-700"><?= date('d/m/Y', strtotime($range['end'])) ?></span>
            </p>
        </div>
        <div class="flex items-center gap-2">
            <!-- Period Selector -->
            <div class="flex bg-gray-100 rounded-xl p-1">
                <a href="?period=week" class="px-4 py-2 text-sm font-medium rounded-lg transition-colors <?= $period === 'week' ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-600 hover:text-gray-900' ?>">7 ngày</a>
                <a href="?period=month" class="px-4 py-2 text-sm font-medium rounded-lg transition-colors <?= $period === 'month' ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-600 hover:text-gray-900' ?>">30 ngày</a>
                <a href="?period=quarter" class="px-4 py-2 text-sm font-medium rounded-lg transition-colors <?= $period === 'quarter' ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-600 hover:text-gray-900' ?>">90 ngày</a>
                <a href="?period=year" class="px-4 py-2 text-sm font-medium rounded-lg transition-colors <?= $period === 'year' ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-600 hover:text-gray-900' ?>">1 năm</a>
            </div>
            
            <!-- Export Button -->
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-colors">
                    <i data-lucide="download" class="h-5 w-5"></i>
                    Xuất báo cáo
                </button>
                <div x-show="open" @click.away="open = false" x-cloak
                     class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-gray-200 py-1 z-50">
                    <a href="/php/api/admin-reports.php?format=pdf&period=<?= $period ?>" 
                       class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                        <i data-lucide="file-text" class="h-4 w-4"></i>Export PDF
                    </a>
                    <a href="/php/api/admin-reports.php?format=excel&period=<?= $period ?>" 
                       class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                        <i data-lucide="table" class="h-4 w-4"></i>Export Excel
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Overview Stats with Comparison -->
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <?php 
        $usersGrowth = getGrowthPercent($currentMonth['users'] ?? 0, $previousMonth['users'] ?? 0);
        $projectsGrowth = getGrowthPercent($currentMonth['projects'] ?? 0, $previousMonth['projects'] ?? 0);
        $tasksGrowth = getGrowthPercent($currentMonth['tasks_done'] ?? 0, $previousMonth['tasks_done'] ?? 0);
        $periodLabel = ['week' => '7 ngày trước', 'month' => '30 ngày trước', 'quarter' => '90 ngày trước', 'year' => '1 năm trước'];
        $vsLabel = $periodLabel[$period] ?? 'kỳ trước';
        ?>
        
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Người dùng mới</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1"><?= number_format($overview['new_users']) ?></p>
                    <div class="flex items-center gap-1 mt-2">
                        <?php if ($usersGrowth >= 0): ?>
                        <i data-lucide="trending-up" class="h-4 w-4 text-green-500"></i>
                        <span class="text-sm text-green-600">+<?= $usersGrowth ?>%</span>
                        <?php else: ?>
                        <i data-lucide="trending-down" class="h-4 w-4 text-red-500"></i>
                        <span class="text-sm text-red-600"><?= $usersGrowth ?>%</span>
                        <?php endif; ?>
                        <span class="text-xs text-gray-400">vs <?= $vsLabel ?></span>
                    </div>
                </div>
                <div class="h-14 w-14 rounded-2xl bg-blue-100 flex items-center justify-center">
                    <i data-lucide="users" class="h-7 w-7 text-blue-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Dự án mới</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1"><?= number_format($overview['new_projects']) ?></p>
                    <div class="flex items-center gap-1 mt-2">
                        <?php if ($projectsGrowth >= 0): ?>
                        <i data-lucide="trending-up" class="h-4 w-4 text-green-500"></i>
                        <span class="text-sm text-green-600">+<?= $projectsGrowth ?>%</span>
                        <?php else: ?>
                        <i data-lucide="trending-down" class="h-4 w-4 text-red-500"></i>
                        <span class="text-sm text-red-600"><?= $projectsGrowth ?>%</span>
                        <?php endif; ?>
                        <span class="text-xs text-gray-400">vs <?= $vsLabel ?></span>
                    </div>
                </div>
                <div class="h-14 w-14 rounded-2xl bg-green-100 flex items-center justify-center">
                    <i data-lucide="folder-kanban" class="h-7 w-7 text-green-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Tasks hoàn thành</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1"><?= number_format($overview['completed_tasks']) ?></p>
                    <div class="flex items-center gap-1 mt-2">
                        <?php if ($tasksGrowth >= 0): ?>
                        <i data-lucide="trending-up" class="h-4 w-4 text-green-500"></i>
                        <span class="text-sm text-green-600">+<?= $tasksGrowth ?>%</span>
                        <?php else: ?>
                        <i data-lucide="trending-down" class="h-4 w-4 text-red-500"></i>
                        <span class="text-sm text-red-600"><?= $tasksGrowth ?>%</span>
                        <?php endif; ?>
                        <span class="text-xs text-gray-400">vs <?= $vsLabel ?></span>
                    </div>
                </div>
                <div class="h-14 w-14 rounded-2xl bg-yellow-100 flex items-center justify-center">
                    <i data-lucide="check-square" class="h-7 w-7 text-yellow-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Tài liệu mới</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1"><?= number_format($overview['new_documents']) ?></p>
                    <p class="text-sm text-gray-500 mt-2">
                        Tổng: <?= number_format($overview['total_documents']) ?> files
                    </p>
                </div>
                <div class="h-14 w-14 rounded-2xl bg-purple-100 flex items-center justify-center">
                    <i data-lucide="file-text" class="h-7 w-7 text-purple-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid gap-6 lg:grid-cols-2">
        <!-- Task Status Distribution -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-semibold text-gray-900 mb-4">Phân bố trạng thái công việc</h3>
            <div class="flex items-center gap-8">
                <div class="relative h-40 w-40 flex-shrink-0">
                    <?php 
                    $totalTasks = array_sum(array_column($taskDistribution, 'count'));
                    $totalTasks = max($totalTasks, 1);
                    $currentDeg = 0;
                    $gradientParts = [];
                    foreach ($taskDistribution as $item) {
                        $percent = ($item['count'] / $totalTasks) * 100;
                        $deg = $percent * 3.6;
                        $color = $statusColors[$item['status']] ?? '#9ca3af';
                        $gradientParts[] = "{$color} {$currentDeg}deg " . ($currentDeg + $deg) . "deg";
                        $currentDeg += $deg;
                    }
                    ?>
                    <div class="absolute inset-0 rounded-full" style="background: conic-gradient(<?= implode(', ', $gradientParts) ?>);"></div>
                    <div class="absolute inset-4 bg-white rounded-full flex items-center justify-center">
                        <div class="text-center">
                            <span class="text-2xl font-bold text-gray-900"><?= $totalTasks ?></span>
                            <p class="text-xs text-gray-500">Tasks</p>
                        </div>
                    </div>
                </div>
                <div class="flex-1 space-y-2">
                    <?php 
                    $statusNames = ['done' => 'Hoàn thành', 'in_progress' => 'Đang làm', 'todo' => 'Chờ xử lý', 'backlog' => 'Backlog', 'in_review' => 'Review'];
                    foreach ($taskDistribution as $item): 
                        $percent = round(($item['count'] / $totalTasks) * 100);
                    ?>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="h-3 w-3 rounded-full" style="background-color: <?= $statusColors[$item['status']] ?? '#9ca3af' ?>"></span>
                            <span class="text-sm text-gray-600"><?= $statusNames[$item['status']] ?? $item['status'] ?></span>
                        </div>
                        <span class="text-sm font-medium text-gray-900"><?= $item['count'] ?> (<?= $percent ?>%)</span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Priority Distribution -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-semibold text-gray-900 mb-4">Phân bố độ ưu tiên</h3>
            <div class="space-y-4">
                <?php 
                $totalPriority = array_sum(array_column($priorityDistribution, 'count'));
                $totalPriority = max($totalPriority, 1);
                $priorityNames = ['urgent' => 'Khẩn cấp', 'high' => 'Cao', 'medium' => 'Trung bình', 'low' => 'Thấp'];
                foreach ($priorityDistribution as $item): 
                    $percent = round(($item['count'] / $totalPriority) * 100);
                ?>
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-sm text-gray-600"><?= $priorityNames[$item['priority']] ?? $item['priority'] ?></span>
                        <span class="text-sm font-medium text-gray-900"><?= $item['count'] ?> (<?= $percent ?>%)</span>
                    </div>
                    <div class="h-3 bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-full rounded-full transition-all" 
                             style="width: <?= $percent ?>%; background-color: <?= $priorityColors[$item['priority']] ?? '#9ca3af' ?>"></div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Top Users & Project Performance -->
    <div class="grid gap-6 lg:grid-cols-2">
        <!-- Top Active Users -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
            <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4">
                <h3 class="font-semibold text-gray-900">Top người dùng hoạt động</h3>
                <span class="text-xs text-gray-500">Tổng hợp tất cả thời gian</span>
            </div>
            <div class="divide-y divide-gray-100">
                <?php if (!empty($topUsers)): ?>
                    <?php foreach (array_slice($topUsers, 0, 5) as $index => $user): ?>
                    <div class="px-6 py-4 flex items-center gap-4">
                        <span class="text-lg font-bold text-gray-300 w-6"><?= $index + 1 ?></span>
                        <div class="h-10 w-10 rounded-full bg-gradient-to-br from-blue-500 to-blue-700 flex items-center justify-center text-white font-medium">
                            <?= strtoupper(substr($user['full_name'], 0, 1)) ?>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-medium text-gray-900 truncate"><?= View::e($user['full_name']) ?></p>
                            <p class="text-xs text-gray-500"><?= View::e($user['email']) ?></p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-medium text-gray-900"><?= number_format($user['activity_score'] ?? 0) ?></p>
                            <p class="text-xs text-gray-500">điểm hoạt động</p>
                        </div>
                        <div class="hidden sm:flex items-center gap-3 text-xs text-gray-500">
                            <span title="Tasks tạo"><?= $user['tasks_created'] ?? 0 ?> tasks</span>
                            <span title="Tasks hoàn thành" class="text-green-600"><?= $user['tasks_completed'] ?? 0 ?> done</span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="px-6 py-8 text-center text-gray-500">Chưa có dữ liệu</div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Project Performance -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
            <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4">
                <h3 class="font-semibold text-gray-900">Hiệu suất dự án</h3>
                <a href="/php/admin/projects.php" class="text-sm text-blue-600 hover:text-blue-700">Xem tất cả →</a>
            </div>
            <div class="divide-y divide-gray-100">
                <?php if (!empty($projectStats)): ?>
                    <?php foreach (array_slice($projectStats, 0, 5) as $project): 
                        $completionRate = $project['total_tasks'] > 0 
                            ? round(($project['completed_tasks'] / $project['total_tasks']) * 100) 
                            : 0;
                    ?>
                    <div class="px-6 py-4">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center gap-2">
                                <span class="h-3 w-3 rounded-full" style="background-color: <?= $project['color'] ?? '#3b82f6' ?>"></span>
                                <span class="font-medium text-gray-900"><?= View::e($project['name']) ?></span>
                            </div>
                            <span class="text-sm text-gray-500"><?= $completionRate ?>%</span>
                        </div>
                        <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                            <div class="h-full bg-green-500 rounded-full" style="width: <?= $completionRate ?>%"></div>
                        </div>
                        <div class="flex items-center gap-4 mt-2 text-xs text-gray-500">
                            <span><?= $project['completed_tasks'] ?>/<?= $project['total_tasks'] ?> tasks</span>
                            <?php if ($project['overdue_tasks'] > 0): ?>
                            <span class="text-red-500"><?= $project['overdue_tasks'] ?> quá hạn</span>
                            <?php endif; ?>
                            <span><?= $project['member_count'] ?> thành viên</span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="px-6 py-8 text-center text-gray-500">Chưa có dự án</div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Storage by Project -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h3 class="font-semibold text-gray-900 mb-4">Dung lượng theo dự án</h3>
        <?php if (!empty($storageByProject)): ?>
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
            <?php foreach ($storageByProject as $project): ?>
            <div class="p-4 bg-gray-50 rounded-xl">
                <div class="flex items-center gap-2 mb-2">
                    <span class="h-3 w-3 rounded-full" style="background-color: <?= $project['color'] ?? '#3b82f6' ?>"></span>
                    <span class="text-sm font-medium text-gray-900 truncate"><?= View::e($project['name']) ?></span>
                </div>
                <p class="text-2xl font-bold text-gray-900"><?= formatBytes($project['total_size']) ?></p>
                <p class="text-xs text-gray-500"><?= $project['file_count'] ?> files</p>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="text-center text-gray-500 py-8">Chưa có dữ liệu lưu trữ</div>
        <?php endif; ?>
    </div>

    <!-- Member Productivity -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
        <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4">
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-xl bg-indigo-100 flex items-center justify-center">
                    <i data-lucide="bar-chart-2" class="h-5 w-5 text-indigo-600"></i>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-900">Năng suất thành viên</h3>
                    <p class="text-sm text-gray-500">Chi tiết hiệu suất làm việc của từng thành viên</p>
                </div>
            </div>
            <a href="/php/admin/users.php" class="text-sm text-blue-600 hover:text-blue-700">Xem tất cả →</a>
        </div>
        
        <?php if (!empty($memberProductivity)): ?>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Thành viên</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Vai trò</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Tasks được giao</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Hoàn thành</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Đang làm</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Quá hạn</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Tỷ lệ HT</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Dự án</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php foreach ($memberProductivity as $member): 
                        $completionRate = $member['tasks_assigned'] > 0 
                            ? round(($member['tasks_completed'] / $member['tasks_assigned']) * 100) 
                            : 0;
                        $rateColor = $completionRate >= 80 ? 'text-green-600 bg-green-100' : 
                                    ($completionRate >= 50 ? 'text-yellow-600 bg-yellow-100' : 'text-red-600 bg-red-100');
                        $roleColors = [
                            'admin' => 'bg-red-100 text-red-700',
                            'manager' => 'bg-blue-100 text-blue-700',
                            'member' => 'bg-gray-100 text-gray-700',
                            'guest' => 'bg-gray-100 text-gray-500',
                        ];
                    ?>
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 rounded-full bg-gradient-to-br from-blue-500 to-blue-700 flex items-center justify-center text-white font-medium">
                                    <?= strtoupper(substr($member['full_name'], 0, 1)) ?>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900"><?= View::e($member['full_name']) ?></p>
                                    <p class="text-xs text-gray-500"><?= View::e($member['department'] ?? $member['email']) ?></p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-4 text-center">
                            <span class="px-2 py-1 text-xs font-medium rounded-full <?= $roleColors[$member['role']] ?? 'bg-gray-100' ?>">
                                <?= ucfirst($member['role']) ?>
                            </span>
                        </td>
                        <td class="px-4 py-4 text-center">
                            <span class="text-sm font-medium text-gray-900"><?= $member['tasks_assigned'] ?></span>
                        </td>
                        <td class="px-4 py-4 text-center">
                            <span class="text-sm font-medium text-green-600"><?= $member['tasks_completed'] ?></span>
                        </td>
                        <td class="px-4 py-4 text-center">
                            <span class="text-sm font-medium text-blue-600"><?= $member['tasks_in_progress'] ?></span>
                        </td>
                        <td class="px-4 py-4 text-center">
                            <?php if ($member['tasks_overdue'] > 0): ?>
                            <span class="text-sm font-medium text-red-600"><?= $member['tasks_overdue'] ?></span>
                            <?php else: ?>
                            <span class="text-sm text-gray-400">0</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-4 text-center">
                            <span class="px-2 py-1 text-xs font-bold rounded-full <?= $rateColor ?>">
                                <?= $completionRate ?>%
                            </span>
                        </td>
                        <td class="px-4 py-4 text-center">
                            <span class="text-sm text-gray-600"><?= $member['projects_joined'] ?></span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Summary Stats -->
        <div class="border-t border-gray-100 px-6 py-4 bg-gray-50">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <?php 
                $totalAssigned = array_sum(array_column($memberProductivity, 'tasks_assigned'));
                $totalCompleted = array_sum(array_column($memberProductivity, 'tasks_completed'));
                $totalOverdue = array_sum(array_column($memberProductivity, 'tasks_overdue'));
                $avgRate = $totalAssigned > 0 ? round(($totalCompleted / $totalAssigned) * 100) : 0;
                ?>
                <div class="flex items-center gap-6">
                    <div>
                        <p class="text-xs text-gray-500">Tổng tasks được giao</p>
                        <p class="text-lg font-bold text-gray-900"><?= number_format($totalAssigned) ?></p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Đã hoàn thành</p>
                        <p class="text-lg font-bold text-green-600"><?= number_format($totalCompleted) ?></p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Quá hạn</p>
                        <p class="text-lg font-bold text-red-600"><?= number_format($totalOverdue) ?></p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Tỷ lệ HT trung bình</p>
                        <p class="text-lg font-bold <?= $avgRate >= 70 ? 'text-green-600' : 'text-yellow-600' ?>"><?= $avgRate ?>%</p>
                    </div>
                </div>
                <div class="text-xs text-gray-400">
                    Hiển thị <?= count($memberProductivity) ?> thành viên
                </div>
            </div>
        </div>
        <?php else: ?>
        <div class="px-6 py-12 text-center text-gray-500">
            <i data-lucide="users" class="h-12 w-12 mx-auto mb-3 text-gray-300"></i>
            <p>Chưa có dữ liệu năng suất</p>
        </div>
        <?php endif; ?>
    </div>

    <!-- Activity Summary -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h3 class="font-semibold text-gray-900 mb-4">Tổng hợp hệ thống</h3>
        <?php if (!empty($activitySummary)): ?>
        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
            <?php 
            $colorClasses = [
                'blue' => 'text-blue-600 bg-blue-100',
                'green' => 'text-green-600 bg-green-100',
                'yellow' => 'text-yellow-600 bg-yellow-100',
                'red' => 'text-red-600 bg-red-100',
                'purple' => 'text-purple-600 bg-purple-100',
                'gray' => 'text-gray-600 bg-gray-100',
            ];
            
            foreach ($activitySummary as $item): 
                $colorClass = $colorClasses[$item['color']] ?? $colorClasses['gray'];
            ?>
            <div class="flex items-center gap-3 p-4 bg-gray-50 rounded-xl">
                <div class="h-12 w-12 rounded-xl <?= $colorClass ?> flex items-center justify-center">
                    <i data-lucide="<?= $item['icon'] ?>" class="h-6 w-6"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900"><?= number_format($item['total']) ?></p>
                    <p class="text-sm text-gray-500"><?= $item['label'] ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="text-center text-gray-500 py-8">Chưa có dữ liệu</div>
        <?php endif; ?>
    </div>
</div>

<?php View::endSection(); ?>
