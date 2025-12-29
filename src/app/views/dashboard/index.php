<?php
/**
 * Dashboard View - Enhanced for Manager
 */
use Core\View;
use Core\Session;
use Core\Permission;

$userRole = Session::get('user_role', 'guest');
$isManager = $isManager ?? false;

View::section('content');
?>

<div class="space-y-6">
    
<?php if ($isManager): ?>
    <!-- Manager Dashboard -->
    
    <!-- Quick Stats Cards -->
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
        <!-- Total Projects -->
        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Dự án quản lý</p>
                    <p class="text-2xl font-bold text-gray-900"><?= $projectStats['total_projects'] ?? 0 ?></p>
                </div>
                <div class="flex h-11 w-11 items-center justify-center rounded-lg bg-blue-100">
                    <i data-lucide="folder-kanban" class="h-5 w-5 text-blue-600"></i>
                </div>
            </div>
            <p class="mt-2 text-xs text-gray-500">
                <span class="text-green-600 font-medium"><?= $projectStats['active_projects'] ?? 0 ?></span> đang hoạt động
            </p>
        </div>
        
        <!-- Team Members -->
        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Thành viên</p>
                    <p class="text-2xl font-bold text-gray-900"><?= count($users ?? []) ?></p>
                </div>
                <div class="flex h-11 w-11 items-center justify-center rounded-lg bg-green-100">
                    <i data-lucide="users" class="h-5 w-5 text-green-600"></i>
                </div>
            </div>
            <p class="mt-2 text-xs text-gray-500">Trong các dự án của bạn</p>
        </div>
        
        <!-- Total Tasks -->
        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Tổng công việc</p>
                    <p class="text-2xl font-bold text-gray-900"><?= $taskStats['total_tasks'] ?? 0 ?></p>
                </div>
                <div class="flex h-11 w-11 items-center justify-center rounded-lg bg-purple-100">
                    <i data-lucide="check-square" class="h-5 w-5 text-purple-600"></i>
                </div>
            </div>
            <?php 
            $completionRate = ($taskStats['total_tasks'] ?? 0) > 0 
                ? round(($taskStats['completed_tasks'] ?? 0) / $taskStats['total_tasks'] * 100) 
                : 0;
            ?>
            <p class="mt-2 text-xs text-gray-500">
                <span class="text-green-600 font-medium"><?= $completionRate ?>%</span> hoàn thành
            </p>
        </div>

        <!-- Overdue Tasks -->
        <div class="rounded-xl border <?= ($taskStats['overdue_tasks'] ?? 0) > 0 ? 'border-red-300 bg-red-50' : 'border-gray-200 bg-white' ?> p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Quá hạn</p>
                    <p class="text-2xl font-bold <?= ($taskStats['overdue_tasks'] ?? 0) > 0 ? 'text-red-600' : 'text-gray-900' ?>">
                        <?= $taskStats['overdue_tasks'] ?? 0 ?>
                    </p>
                </div>
                <div class="flex h-11 w-11 items-center justify-center rounded-lg <?= ($taskStats['overdue_tasks'] ?? 0) > 0 ? 'bg-red-100' : 'bg-gray-100' ?>">
                    <i data-lucide="alert-triangle" class="h-5 w-5 <?= ($taskStats['overdue_tasks'] ?? 0) > 0 ? 'text-red-600' : 'text-gray-400' ?>"></i>
                </div>
            </div>
            <p class="mt-2 text-xs <?= ($taskStats['overdue_tasks'] ?? 0) > 0 ? 'text-red-600' : 'text-gray-500' ?>">
                <?= ($taskStats['overdue_tasks'] ?? 0) > 0 ? 'Cần xử lý ngay!' : 'Tuyệt vời!' ?>
            </p>
        </div>
        
        <!-- Due This Week -->
        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Đến hạn tuần này</p>
                    <p class="text-2xl font-bold text-orange-600"><?= $taskStats['due_this_week'] ?? 0 ?></p>
                </div>
                <div class="flex h-11 w-11 items-center justify-center rounded-lg bg-orange-100">
                    <i data-lucide="clock" class="h-5 w-5 text-orange-600"></i>
                </div>
            </div>
            <p class="mt-2 text-xs text-gray-500">Trong 7 ngày tới</p>
        </div>
    </div>

    <!-- Tổng quan nhóm & Hiệu suất -->
    <div class="grid gap-6 lg:grid-cols-3">
        <!-- Khối lượng công việc -->
        <div class="lg:col-span-2 rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4">
                <h3 class="font-semibold text-gray-900">
                    <i data-lucide="users" class="inline h-5 w-5 mr-2 text-gray-400"></i>
                    Khối lượng công việc
                </h3>
                <a href="/php/team.php" class="text-sm text-primary hover:underline">Xem tất cả</a>
            </div>
            <div class="p-4">
                <?php if (!empty($users)): ?>
                <div class="space-y-3">
                    <?php foreach (array_slice($users, 0, 6) as $member): 
                        $totalTasks = ($member['active_tasks'] ?? 0) + ($member['completed_tasks'] ?? 0);
                        $workloadPercent = min(100, ($member['active_tasks'] ?? 0) * 10); // Rough estimate
                        $workloadColor = $workloadPercent > 80 ? 'bg-red-500' : ($workloadPercent > 50 ? 'bg-yellow-500' : 'bg-green-500');
                    ?>
                    <div class="flex items-center gap-4 p-3 rounded-lg hover:bg-gray-50">
                        <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center overflow-hidden flex-shrink-0">
                            <?php if (!empty($member['avatar_url'])): ?>
                            <img src="/php/<?= View::e($member['avatar_url']) ?>" class="h-full w-full object-cover">
                            <?php else: ?>
                            <span class="text-sm font-medium"><?= strtoupper(substr($member['full_name'] ?? 'U', 0, 1)) ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between mb-1">
                                <span class="font-medium text-gray-900 truncate"><?= View::e($member['full_name']) ?></span>
                                <div class="flex items-center gap-2 text-xs">
                                    <?php if (($member['overdue_tasks'] ?? 0) > 0): ?>
                                    <span class="text-red-600 font-medium"><?= $member['overdue_tasks'] ?> quá hạn</span>
                                    <?php endif; ?>
                                    <span class="text-gray-500"><?= $member['active_tasks'] ?? 0 ?> đang làm</span>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="flex-1 h-2 rounded-full bg-gray-100 overflow-hidden">
                                    <div class="h-full rounded-full <?= $workloadColor ?>" style="width: <?= $workloadPercent ?>%"></div>
                                </div>
                                <span class="text-xs text-gray-400 w-16 text-right"><?= $member['completed_tasks'] ?? 0 ?> xong</span>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <div class="text-center py-8 text-gray-500">Chưa có thành viên</div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Hiệu suất & Cảnh báo -->
        <div class="space-y-6">
            <!-- Thành viên xuất sắc -->
            <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-200 px-5 py-3">
                    <h3 class="font-semibold text-gray-900 text-sm">
                        <i data-lucide="trophy" class="inline h-4 w-4 mr-1 text-yellow-500"></i>
                        Thành viên xuất sắc
                    </h3>
                </div>
                <div class="divide-y divide-gray-100">
                    <?php if (!empty($topPerformers)): ?>
                        <?php foreach (array_slice($topPerformers, 0, 3) as $i => $performer): ?>
                        <div class="px-5 py-3 flex items-center gap-3">
                            <span class="text-lg font-bold <?= $i === 0 ? 'text-yellow-500' : ($i === 1 ? 'text-gray-400' : 'text-orange-400') ?>">
                                #<?= $i + 1 ?>
                            </span>
                            <div class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center overflow-hidden">
                                <?php if (!empty($performer['avatar_url'])): ?>
                                <img src="/php/<?= View::e($performer['avatar_url']) ?>" class="h-full w-full object-cover">
                                <?php else: ?>
                                <span class="text-xs font-medium"><?= strtoupper(substr($performer['full_name'] ?? 'U', 0, 1)) ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate"><?= View::e($performer['full_name']) ?></p>
                                <p class="text-xs text-gray-500"><?= $performer['completed_count'] ?> hoàn thành</p>
                            </div>
                            <span class="text-sm font-bold text-green-600"><?= $performer['completion_rate'] ?? 0 ?>%</span>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="px-5 py-4 text-center text-gray-500 text-sm">Chưa có dữ liệu</div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Needs Attention -->
            <div class="rounded-xl border <?= !empty($needsAttention) ? 'border-red-200 bg-red-50' : 'border-gray-200 bg-white' ?> shadow-sm">
                <div class="border-b <?= !empty($needsAttention) ? 'border-red-200' : 'border-gray-200' ?> px-5 py-3">
                    <h3 class="font-semibold <?= !empty($needsAttention) ? 'text-red-800' : 'text-gray-900' ?> text-sm">
                        <i data-lucide="alert-circle" class="inline h-4 w-4 mr-1 <?= !empty($needsAttention) ? 'text-red-500' : 'text-gray-400' ?>"></i>
                        Cần hỗ trợ
                    </h3>
                </div>
                <div class="divide-y <?= !empty($needsAttention) ? 'divide-red-100' : 'divide-gray-100' ?>">
                    <?php if (!empty($needsAttention)): ?>
                        <?php foreach (array_slice($needsAttention, 0, 3) as $member): ?>
                        <div class="px-5 py-3 flex items-center gap-3">
                            <div class="h-8 w-8 rounded-full bg-red-100 flex items-center justify-center overflow-hidden">
                                <?php if (!empty($member['avatar_url'])): ?>
                                <img src="/php/<?= View::e($member['avatar_url']) ?>" class="h-full w-full object-cover">
                                <?php else: ?>
                                <span class="text-xs font-medium text-red-600"><?= strtoupper(substr($member['full_name'] ?? 'U', 0, 1)) ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate"><?= View::e($member['full_name']) ?></p>
                                <p class="text-xs text-red-600"><?= $member['overdue_count'] ?> task quá hạn</p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="px-5 py-4 text-center text-green-600 text-sm">
                            <i data-lucide="check-circle" class="inline h-4 w-4 mr-1"></i>
                            Tất cả đều ổn!
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Projects Progress -->
    <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
        <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4">
            <h3 class="font-semibold text-gray-900">
                <i data-lucide="folder-kanban" class="inline h-5 w-5 mr-2 text-gray-400"></i>
                Tiến độ dự án
            </h3>
            <a href="/php/projects.php" class="text-sm text-primary hover:underline">Xem tất cả</a>
        </div>
        <div class="p-6">
            <?php if (!empty($projects)): ?>
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <?php foreach (array_slice($projects, 0, 6) as $project): 
                    $progress = $project['progress'] ?? 0;
                    $progressColor = $progress >= 80 ? 'bg-green-500' : ($progress >= 50 ? 'bg-blue-500' : ($progress >= 25 ? 'bg-yellow-500' : 'bg-gray-400'));
                ?>
                <a href="/php/project-detail.php?id=<?= View::e($project['id']) ?>" 
                   class="block p-4 rounded-lg border border-gray-200 hover:shadow-md transition-shadow">
                    <div class="flex items-start gap-3 mb-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-lg" 
                             style="background-color: <?= View::e($project['color'] ?? '#6366f1') ?>20">
                            <i data-lucide="folder" class="h-5 w-5" style="color: <?= View::e($project['color'] ?? '#6366f1') ?>"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="font-medium text-gray-900 truncate"><?= View::e($project['name']) ?></h4>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="text-xs text-gray-500"><?= $project['member_count'] ?? 0 ?> thành viên</span>
                                <?php if (($project['overdue_tasks'] ?? 0) > 0): ?>
                                <span class="text-xs text-red-600"><?= $project['overdue_tasks'] ?> quá hạn</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="space-y-1">
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-gray-500"><?= $project['completed_tasks'] ?? 0 ?>/<?= $project['total_tasks'] ?? 0 ?> tasks</span>
                            <span class="font-medium"><?= $progress ?>%</span>
                        </div>
                        <div class="h-2 rounded-full bg-gray-100 overflow-hidden">
                            <div class="h-full rounded-full <?= $progressColor ?>" style="width: <?= $progress ?>%"></div>
                        </div>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="text-center py-8 text-gray-500">
                <i data-lucide="folder-open" class="h-12 w-12 mx-auto mb-3 text-gray-300"></i>
                <p>Chưa có dự án nào</p>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Tasks Due This Week & Overdue -->
    <div class="grid gap-6 lg:grid-cols-2">
        <!-- Due This Week -->
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4">
                <h3 class="font-semibold text-gray-900">
                    <i data-lucide="clock" class="inline h-5 w-5 mr-2 text-orange-500"></i>
                    Đến hạn tuần này
                </h3>
                <span class="text-sm text-orange-600 font-medium"><?= count($dueThisWeek ?? []) ?> tasks</span>
            </div>
            <div class="divide-y divide-gray-100 max-h-[320px] overflow-y-auto">
                <?php if (!empty($dueThisWeek)): ?>
                    <?php foreach (array_slice($dueThisWeek, 0, 6) as $task): 
                        $daysUntil = ceil((strtotime($task['due_date']) - strtotime('today')) / 86400);
                    ?>
                    <div class="px-6 py-3 hover:bg-gray-50">
                        <div class="flex items-center justify-between">
                            <div class="min-w-0 flex-1">
                                <a href="/php/task-detail.php?id=<?= View::e($task['id']) ?>" 
                                   class="font-medium text-gray-900 hover:text-primary truncate block">
                                    <?= View::e($task['title']) ?>
                                </a>
                                <p class="text-xs text-gray-500 mt-1">
                                    <?= View::e($task['assignee_names'] ?? 'Chưa giao') ?>
                                    · <?= View::e($task['project_name'] ?? '') ?>
                                </p>
                            </div>
                            <span class="ml-2 inline-flex items-center rounded-full px-2 py-1 text-xs font-medium 
                                <?= $daysUntil <= 1 ? 'bg-red-100 text-red-800' : 'bg-orange-100 text-orange-800' ?>">
                                <?= $daysUntil == 0 ? 'Hôm nay' : ($daysUntil == 1 ? 'Ngày mai' : $daysUntil . ' ngày') ?>
                            </span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="px-6 py-8 text-center text-gray-500">
                        <i data-lucide="calendar-check" class="h-8 w-8 mx-auto mb-2 text-gray-300"></i>
                        <p>Không có task nào đến hạn tuần này</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Overdue Tasks by Person -->
        <div class="rounded-xl border <?= !empty($overdueTasks) ? 'border-red-200' : 'border-gray-200' ?> bg-white shadow-sm">
            <div class="flex items-center justify-between border-b <?= !empty($overdueTasks) ? 'border-red-200 bg-red-50' : 'border-gray-200' ?> px-6 py-4">
                <h3 class="font-semibold <?= !empty($overdueTasks) ? 'text-red-800' : 'text-gray-900' ?>">
                    <i data-lucide="alert-triangle" class="inline h-5 w-5 mr-2 text-red-500"></i>
                    Tasks quá hạn
                </h3>
                <span class="text-sm text-red-600 font-medium"><?= count($overdueTasks ?? []) ?> tasks</span>
            </div>
            <div class="divide-y divide-gray-100 max-h-[320px] overflow-y-auto">
                <?php if (!empty($overdueTasks)): ?>
                    <?php 
                    // Group by user
                    $groupedOverdue = [];
                    foreach ($overdueTasks as $task) {
                        $userId = $task['user_id'];
                        if (!isset($groupedOverdue[$userId])) {
                            $groupedOverdue[$userId] = [
                                'user_name' => $task['full_name'],
                                'avatar_url' => $task['avatar_url'],
                                'tasks' => []
                            ];
                        }
                        $groupedOverdue[$userId]['tasks'][] = $task;
                    }
                    ?>
                    <?php foreach ($groupedOverdue as $userId => $group): ?>
                    <div class="px-6 py-3">
                        <div class="flex items-center gap-2 mb-2">
                            <div class="h-6 w-6 rounded-full bg-red-100 flex items-center justify-center overflow-hidden">
                                <?php if (!empty($group['avatar_url'])): ?>
                                <img src="/php/<?= View::e($group['avatar_url']) ?>" class="h-full w-full object-cover">
                                <?php else: ?>
                                <span class="text-xs font-medium text-red-600"><?= strtoupper(substr($group['user_name'] ?? 'U', 0, 1)) ?></span>
                                <?php endif; ?>
                            </div>
                            <span class="text-sm font-medium text-gray-900"><?= View::e($group['user_name']) ?></span>
                            <span class="text-xs text-red-600">(<?= count($group['tasks']) ?> tasks)</span>
                        </div>
                        <div class="pl-8 space-y-1">
                            <?php foreach (array_slice($group['tasks'], 0, 2) as $task): ?>
                            <a href="/php/task-detail.php?id=<?= View::e($task['id']) ?>" 
                               class="block text-sm text-gray-600 hover:text-primary truncate">
                                • <?= View::e($task['title']) ?> 
                                <span class="text-red-500">(-<?= $task['days_overdue'] ?> ngày)</span>
                            </a>
                            <?php endforeach; ?>
                            <?php if (count($group['tasks']) > 2): ?>
                            <span class="text-xs text-gray-400">+<?= count($group['tasks']) - 2 ?> tasks khác</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="px-6 py-8 text-center text-green-600">
                        <i data-lucide="check-circle" class="h-8 w-8 mx-auto mb-2 text-green-400"></i>
                        <p>Không có task nào quá hạn 🎉</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

<?php else: ?>
    <!-- Member Dashboard (Original) -->
    
    <!-- Stats Cards -->
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Tổng dự án</p>
                    <p class="text-2xl font-bold text-gray-900"><?= count($projects ?? []) ?></p>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-blue-100">
                    <i data-lucide="folder-kanban" class="h-6 w-6 text-blue-600"></i>
                </div>
            </div>
        </div>
        
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Công việc của tôi</p>
                    <p class="text-2xl font-bold text-gray-900"><?= count($tasks ?? []) ?></p>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-yellow-100">
                    <i data-lucide="check-square" class="h-6 w-6 text-yellow-600"></i>
                </div>
            </div>
        </div>
        
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Thành viên</p>
                    <p class="text-2xl font-bold text-gray-900"><?= count($users ?? []) ?></p>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-green-100">
                    <i data-lucide="users" class="h-6 w-6 text-green-600"></i>
                </div>
            </div>
        </div>
        
        <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Tỷ lệ hoàn thành</p>
                    <?php 
                    $doneTasks = array_filter($tasks ?? [], fn($t) => ($t['status'] ?? '') === 'done');
                    $rate = count($tasks ?? []) > 0 ? round(count($doneTasks) / count($tasks) * 100) : 0;
                    ?>
                    <p class="text-2xl font-bold text-gray-900"><?= $rate ?>%</p>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-purple-100">
                    <i data-lucide="trending-up" class="h-6 w-6 text-purple-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Projects Section -->
    <div>
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-900">Dự án của tôi</h2>
            <a href="/php/projects.php" class="text-sm text-primary hover:underline">Xem tất cả</a>
        </div>
        
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            <?php if (!empty($projects)): ?>
                <?php foreach (array_slice($projects, 0, 8) as $project): ?>
                <a href="/php/project-detail.php?id=<?= View::e($project['id']) ?>" 
                   class="block rounded-xl border border-gray-200 bg-white p-5 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex items-start gap-3 mb-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-lg" 
                             style="background-color: <?= View::e($project['color'] ?? '#6366f1') ?>20">
                            <i data-lucide="folder" class="h-5 w-5" style="color: <?= View::e($project['color'] ?? '#6366f1') ?>"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="font-medium text-gray-900 truncate"><?= View::e($project['name']) ?></h3>
                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium bg-green-100 text-green-800">
                                <?= View::e($project['status'] ?? 'active') ?>
                            </span>
                        </div>
                    </div>
                    <p class="text-sm text-gray-500 line-clamp-2 mb-3"><?= View::e($project['description'] ?? '') ?></p>
                    <div class="h-2 rounded-full bg-gray-100 overflow-hidden">
                        <div class="h-full rounded-full bg-primary" style="width: <?= $project['progress'] ?? 0 ?>%"></div>
                    </div>
                    <p class="text-xs text-gray-500 mt-2"><?= $project['progress'] ?? 0 ?>% hoàn thành</p>
                </a>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-span-full text-center py-12 text-gray-500">
                    <i data-lucide="folder-open" class="h-12 w-12 mx-auto mb-3 text-gray-300"></i>
                    <p>Chưa có dự án nào</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Tasks and Activity -->
    <div class="grid gap-6 lg:grid-cols-2">
        <!-- Recent Tasks -->
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4">
                <h3 class="font-semibold text-gray-900">Công việc của tôi</h3>
                <a href="/php/tasks.php" class="text-sm text-primary hover:underline">Xem tất cả</a>
            </div>
            <div class="divide-y divide-gray-100">
                <?php if (!empty($tasks)): ?>
                    <?php foreach (array_slice($tasks, 0, 5) as $task): ?>
                    <div class="px-6 py-4 hover:bg-gray-50">
                        <div class="flex items-start gap-3">
                            <input type="checkbox" <?= ($task['status'] ?? '') === 'done' ? 'checked' : '' ?>
                                   class="mt-1 h-4 w-4 rounded border-gray-300 text-primary">
                            <div class="flex-1 min-w-0">
                                <a href="/php/task-detail.php?id=<?= View::e($task['id']) ?>" 
                                   class="font-medium text-gray-900 hover:text-primary <?= ($task['status'] ?? '') === 'done' ? 'line-through text-gray-400' : '' ?>">
                                    <?= View::e($task['title']) ?>
                                </a>
                                <div class="flex items-center gap-2 mt-1">
                                    <?php if (!empty($task['due_date'])): ?>
                                    <span class="text-xs text-gray-500"><?= date('d/m', strtotime($task['due_date'])) ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="px-6 py-8 text-center text-gray-500">
                        <p>Chưa có công việc nào</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Recent Activity -->
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4">
                <h3 class="font-semibold text-gray-900">Hoạt động gần đây</h3>
            </div>
            <div class="divide-y divide-gray-100">
                <?php if (!empty($activities)): ?>
                    <?php foreach (array_slice($activities, 0, 5) as $activity): ?>
                    <div class="px-6 py-4">
                        <div class="flex items-start gap-3">
                            <div class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center">
                                <span class="text-xs font-medium"><?= substr($activity['user_name'] ?? 'U', 0, 1) ?></span>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm text-gray-900"><?= View::e($activity['description'] ?? '') ?></p>
                                <p class="text-xs text-gray-500 mt-1"><?= $activity['time_ago'] ?? '' ?></p>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="px-6 py-8 text-center text-gray-500">
                        <p>Chưa có hoạt động nào</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php endif; ?>

</div>

<?php View::endSection(); ?>
