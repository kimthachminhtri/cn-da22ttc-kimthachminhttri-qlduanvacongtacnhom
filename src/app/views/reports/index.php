<?php
/**
 * Reports View - Enhanced
 */
use Core\View;
use Core\Session;
use Core\Permission;

$userRole = Session::get('user_role', 'guest');
$isManager = in_array($userRole, ['admin', 'manager']);
$period = $period ?? 'month';
$stats = $stats ?? [];

View::section('content');
?>

<div class="space-y-6">
    <!-- Header with Filters -->
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <p class="text-sm text-gray-500">
                Thống kê và báo cáo <?= $isManager ? 'các dự án của bạn' : 'cá nhân' ?>
                <span class="font-medium text-gray-700">· <?= $dateRange['label'] ?? 'Tháng này' ?></span>
            </p>
        </div>
        <div class="flex items-center gap-3">
            <!-- Period Filter -->
            <div class="flex items-center bg-gray-100 rounded-lg p-1">
                <a href="?period=week" class="px-3 py-1.5 text-sm font-medium rounded-md <?= $period === 'week' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-600 hover:text-gray-900' ?>">
                    Tuần
                </a>
                <a href="?period=month" class="px-3 py-1.5 text-sm font-medium rounded-md <?= $period === 'month' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-600 hover:text-gray-900' ?>">
                    Tháng
                </a>
                <a href="?period=quarter" class="px-3 py-1.5 text-sm font-medium rounded-md <?= $period === 'quarter' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-600 hover:text-gray-900' ?>">
                    Quý
                </a>
                <a href="?period=year" class="px-3 py-1.5 text-sm font-medium rounded-md <?= $period === 'year' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-600 hover:text-gray-900' ?>">
                    Năm
                </a>
            </div>
            
            <!-- Export Button -->
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" 
                        class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-primary rounded-lg hover:bg-primary/90">
                    <i data-lucide="download" class="h-4 w-4"></i>
                    Xuất báo cáo
                    <i data-lucide="chevron-down" class="h-4 w-4"></i>
                </button>
                <div x-show="open" @click.away="open = false" x-cloak
                     class="absolute right-0 mt-2 w-64 bg-white rounded-xl shadow-xl border border-gray-200 py-2 z-20">
                    <div class="px-3 py-2 border-b border-gray-100">
                        <p class="text-xs font-semibold text-gray-400 uppercase">Công việc của tôi</p>
                    </div>
                    <button onclick="exportReport('my-tasks', 'excel')" class="w-full px-4 py-2.5 text-left text-sm text-gray-700 hover:bg-gray-50 flex items-center gap-3">
                        <i data-lucide="file-spreadsheet" class="h-4 w-4 text-green-600"></i>
                        Xuất Excel (.xlsx)
                    </button>
                    <button onclick="exportReport('my-tasks', 'pdf')" class="w-full px-4 py-2.5 text-left text-sm text-gray-700 hover:bg-gray-50 flex items-center gap-3">
                        <i data-lucide="file-text" class="h-4 w-4 text-red-600"></i>
                        Xuất PDF
                    </button>
                    <button onclick="exportReport('my-tasks', 'csv')" class="w-full px-4 py-2.5 text-left text-sm text-gray-700 hover:bg-gray-50 flex items-center gap-3">
                        <i data-lucide="file" class="h-4 w-4 text-blue-600"></i>
                        Xuất CSV
                    </button>
                    
                    <div class="px-3 py-2 border-t border-b border-gray-100 mt-1">
                        <p class="text-xs font-semibold text-gray-400 uppercase">Tổng hợp</p>
                    </div>
                    <button onclick="exportReport('project-summary', 'excel')" class="w-full px-4 py-2.5 text-left text-sm text-gray-700 hover:bg-gray-50 flex items-center gap-3">
                        <i data-lucide="folder" class="h-4 w-4 text-amber-600"></i>
                        Tổng hợp dự án (Excel)
                    </button>
                    <button onclick="exportReport('overdue-tasks', 'excel')" class="w-full px-4 py-2.5 text-left text-sm text-gray-700 hover:bg-gray-50 flex items-center gap-3">
                        <i data-lucide="alert-triangle" class="h-4 w-4 text-rose-600"></i>
                        Công việc quá hạn (Excel)
                    </button>
                    <button onclick="exportReport('weekly-report', 'pdf')" class="w-full px-4 py-2.5 text-left text-sm text-gray-700 hover:bg-gray-50 flex items-center gap-3">
                        <i data-lucide="calendar" class="h-4 w-4 text-purple-600"></i>
                        Báo cáo tuần (PDF)
                    </button>
                    
                    <?php if ($isManager): ?>
                    <div class="px-3 py-2 border-t border-b border-gray-100 mt-1">
                        <p class="text-xs font-semibold text-gray-400 uppercase">Quản lý</p>
                    </div>
                    <button onclick="exportReport('team-workload', 'excel')" class="w-full px-4 py-2.5 text-left text-sm text-gray-700 hover:bg-gray-50 flex items-center gap-3">
                        <i data-lucide="users" class="h-4 w-4 text-indigo-600"></i>
                        Hiệu suất nhân viên (Excel)
                    </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Overview -->
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Tổng dự án</p>
                    <p class="text-2xl font-bold text-gray-900"><?= $stats['total_projects'] ?? count($projects ?? []) ?></p>
                </div>
                <div class="h-12 w-12 rounded-lg bg-blue-100 flex items-center justify-center">
                    <i data-lucide="folder" class="h-6 w-6 text-blue-600"></i>
                </div>
            </div>
            <p class="mt-2 text-xs text-gray-500">
                <span class="text-green-600 font-medium"><?= $stats['active_projects'] ?? 0 ?></span> đang hoạt động
            </p>
        </div>
        
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Tổng công việc</p>
                    <p class="text-2xl font-bold text-gray-900"><?= $stats['total_tasks'] ?? count($tasks ?? []) ?></p>
                </div>
                <div class="h-12 w-12 rounded-lg bg-green-100 flex items-center justify-center">
                    <i data-lucide="check-square" class="h-6 w-6 text-green-600"></i>
                </div>
            </div>
            <p class="mt-2 text-xs text-gray-500">
                <span class="text-blue-600 font-medium"><?= $stats['in_progress_tasks'] ?? 0 ?></span> đang thực hiện
            </p>
        </div>
        
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Tỷ lệ hoàn thành</p>
                    <p class="text-2xl font-bold text-gray-900"><?= $stats['completion_rate'] ?? 0 ?>%</p>
                </div>
                <div class="h-12 w-12 rounded-lg bg-purple-100 flex items-center justify-center">
                    <i data-lucide="trending-up" class="h-6 w-6 text-purple-600"></i>
                </div>
            </div>
            <p class="mt-2 text-xs text-gray-500">
                <span class="text-green-600 font-medium"><?= $stats['completed_tasks'] ?? 0 ?></span> / <?= $stats['total_tasks'] ?? 0 ?> công việc
            </p>
        </div>
        
        <div class="bg-white rounded-xl border <?= ($stats['overdue_tasks'] ?? 0) > 0 ? 'border-red-300 bg-red-50' : 'border-gray-200' ?> p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Quá hạn</p>
                    <p class="text-2xl font-bold <?= ($stats['overdue_tasks'] ?? 0) > 0 ? 'text-red-600' : 'text-gray-900' ?>"><?= $stats['overdue_tasks'] ?? 0 ?></p>
                </div>
                <div class="h-12 w-12 rounded-lg <?= ($stats['overdue_tasks'] ?? 0) > 0 ? 'bg-red-100' : 'bg-gray-100' ?> flex items-center justify-center">
                    <i data-lucide="alert-triangle" class="h-6 w-6 <?= ($stats['overdue_tasks'] ?? 0) > 0 ? 'text-red-600' : 'text-gray-400' ?>"></i>
                </div>
            </div>
            <p class="mt-2 text-xs <?= ($stats['overdue_tasks'] ?? 0) > 0 ? 'text-red-600' : 'text-gray-500' ?>">
                <?= ($stats['overdue_tasks'] ?? 0) > 0 ? 'Cần xử lý ngay!' : 'Không có task quá hạn' ?>
            </p>
        </div>
        
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Đến hạn tuần này</p>
                    <p class="text-2xl font-bold text-orange-600"><?= $stats['due_this_week'] ?? 0 ?></p>
                </div>
                <div class="h-12 w-12 rounded-lg bg-orange-100 flex items-center justify-center">
                    <i data-lucide="clock" class="h-6 w-6 text-orange-600"></i>
                </div>
            </div>
            <p class="mt-2 text-xs text-gray-500">Trong 7 ngày tới</p>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid gap-6 lg:grid-cols-2">
        <!-- Task Status Doughnut Chart -->
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h3 class="font-semibold text-gray-900 mb-4">
                <i data-lucide="pie-chart" class="inline h-5 w-5 mr-2 text-gray-400"></i>
                Trạng thái công việc
            </h3>
            <?php
            $statusCounts = [
                'backlog' => 0, 'todo' => 0, 'in_progress' => 0, 'in_review' => 0, 'done' => 0
            ];
            foreach ($tasks ?? [] as $task) {
                $status = $task['status'] ?? 'todo';
                if (isset($statusCounts[$status])) {
                    $statusCounts[$status]++;
                }
            }
            $total = array_sum($statusCounts) ?: 1;
            ?>
            <div class="flex items-center gap-6">
                <div class="w-48 h-48">
                    <canvas id="statusChart"></canvas>
                </div>
                <div class="flex-1 space-y-2">
                    <div class="flex items-center justify-between text-sm">
                        <span class="flex items-center gap-2"><span class="h-3 w-3 rounded-full bg-gray-400"></span> Chờ xử lý</span>
                        <span class="font-medium"><?= $statusCounts['backlog'] ?></span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="flex items-center gap-2"><span class="h-3 w-3 rounded-full bg-blue-500"></span> Cần làm</span>
                        <span class="font-medium"><?= $statusCounts['todo'] ?></span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="flex items-center gap-2"><span class="h-3 w-3 rounded-full bg-yellow-500"></span> Đang làm</span>
                        <span class="font-medium"><?= $statusCounts['in_progress'] ?></span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="flex items-center gap-2"><span class="h-3 w-3 rounded-full bg-purple-500"></span> Review</span>
                        <span class="font-medium"><?= $statusCounts['in_review'] ?></span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="flex items-center gap-2"><span class="h-3 w-3 rounded-full bg-green-500"></span> Hoàn thành</span>
                        <span class="font-medium"><?= $statusCounts['done'] ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Priority Bar Chart -->
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h3 class="font-semibold text-gray-900 mb-4">
                <i data-lucide="bar-chart-3" class="inline h-5 w-5 mr-2 text-gray-400"></i>
                Phân bố độ ưu tiên
            </h3>
            <?php
            $priorityCounts = ['low' => 0, 'medium' => 0, 'high' => 0, 'urgent' => 0];
            foreach ($tasks ?? [] as $task) {
                $priority = $task['priority'] ?? 'medium';
                if (isset($priorityCounts[$priority])) {
                    $priorityCounts[$priority]++;
                }
            }
            ?>
            <div class="h-48">
                <canvas id="priorityChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Completion Trend Chart (for Manager) -->
    <?php if ($isManager): ?>
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h3 class="font-semibold text-gray-900 mb-4">
            <i data-lucide="trending-up" class="inline h-5 w-5 mr-2 text-gray-400"></i>
            Xu hướng hoàn thành công việc
        </h3>
        <div class="h-64">
            <canvas id="trendChart"></canvas>
        </div>
    </div>
    <?php endif; ?>

    <!-- Project Progress -->
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h3 class="font-semibold text-gray-900 mb-4">Tiến độ dự án</h3>
        <?php if (!empty($projects)): ?>
        <div class="space-y-4">
            <?php foreach (array_slice($projects, 0, 6) as $project): 
                $progress = $project['progress'] ?? 0;
                $progressColor = $progress >= 80 ? 'bg-green-500' : ($progress >= 50 ? 'bg-blue-500' : ($progress >= 25 ? 'bg-yellow-500' : 'bg-gray-400'));
            ?>
            <div>
                <div class="flex items-center justify-between text-sm mb-1">
                    <a href="/php/project-detail.php?id=<?= View::e($project['id']) ?>" class="text-gray-900 hover:text-primary font-medium truncate max-w-[200px]">
                        <?= View::e($project['name']) ?>
                    </a>
                    <div class="flex items-center gap-2">
                        <span class="text-xs text-gray-500"><?= $project['total_tasks'] ?? 0 ?> công việc</span>
                        <span class="font-medium"><?= $progress ?>%</span>
                    </div>
                </div>
                <div class="h-2 rounded-full bg-gray-100 overflow-hidden">
                    <div class="h-full rounded-full <?= $progressColor ?> transition-all" style="width: <?= $progress ?>%"></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php if (count($projects) > 6): ?>
        <a href="/php/projects.php" class="block text-center text-sm text-primary hover:underline mt-4">
            Xem tất cả <?= count($projects) ?> dự án →
        </a>
        <?php endif; ?>
        <?php else: ?>
        <div class="text-center text-gray-500 py-4">Chưa có dự án nào</div>
        <?php endif; ?>
    </div>

    <!-- Team Productivity & Overdue Tasks -->
    <div class="grid gap-6 lg:grid-cols-2">
        <!-- Team Productivity - Enhanced -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="border-b border-gray-200 px-6 py-4 flex items-center justify-between">
                <h3 class="font-semibold text-gray-900">
                    <i data-lucide="bar-chart-2" class="inline h-5 w-5 mr-2 text-indigo-500"></i>
                    Năng suất thành viên
                </h3>
                <a href="/php/team.php" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">Xem tất cả</a>
            </div>
            <?php if (!empty($users)): ?>
            <div class="divide-y divide-gray-100">
                <?php 
                // Sort users by completion rate (highest first), then by total tasks
                $sortedUsers = $users;
                usort($sortedUsers, function($a, $b) {
                    $totalA = ($a['active_tasks'] ?? 0) + ($a['completed_tasks'] ?? 0);
                    $totalB = ($b['active_tasks'] ?? 0) + ($b['completed_tasks'] ?? 0);
                    $rateA = $totalA > 0 ? ($a['completed_tasks'] ?? 0) / $totalA : 0;
                    $rateB = $totalB > 0 ? ($b['completed_tasks'] ?? 0) / $totalB : 0;
                    if ($rateA === $rateB) return $totalB <=> $totalA;
                    return $rateB <=> $rateA;
                });
                
                foreach (array_slice($sortedUsers, 0, 6) as $index => $user): 
                    $activeTasks = $user['active_tasks'] ?? 0;
                    $completedTasks = $user['completed_tasks'] ?? 0;
                    $userOverdue = $user['overdue_tasks'] ?? 0;
                    $userTotal = $activeTasks + $completedTasks;
                    $userRate = $userTotal > 0 ? round($completedTasks / $userTotal * 100) : 0;
                    
                    // Professional color scheme based on performance
                    $barColor = 'bg-gray-300';
                    $rateColor = 'text-gray-500';
                    if ($userOverdue > 0) {
                        $barColor = 'bg-rose-500';
                        $rateColor = 'text-rose-600';
                    } elseif ($userTotal > 0) {
                        if ($userRate >= 80) {
                            $barColor = 'bg-emerald-500';
                            $rateColor = 'text-emerald-600';
                        } elseif ($userRate >= 50) {
                            $barColor = 'bg-sky-500';
                            $rateColor = 'text-sky-600';
                        } elseif ($userRate > 0) {
                            $barColor = 'bg-amber-500';
                            $rateColor = 'text-amber-600';
                        }
                    }
                ?>
                <div class="px-6 py-4 hover:bg-slate-50 transition-colors">
                    <div class="flex items-center gap-4">
                        <!-- Avatar with rank badge -->
                        <div class="relative flex-shrink-0">
                            <div class="h-11 w-11 rounded-full bg-gradient-to-br from-slate-100 to-slate-200 flex items-center justify-center overflow-hidden ring-2 ring-white shadow-sm">
                                <?php if (!empty($user['avatar_url'])): ?>
                                <img src="/php/<?= View::e($user['avatar_url']) ?>" class="h-full w-full object-cover">
                                <?php else: ?>
                                <span class="text-sm font-semibold text-slate-600"><?= strtoupper(substr($user['full_name'] ?? 'U', 0, 1)) ?></span>
                                <?php endif; ?>
                            </div>
                            <?php if ($index < 3 && $userTotal > 0): ?>
                            <span class="absolute -top-1 -right-1 h-5 w-5 rounded-full flex items-center justify-center text-[10px] font-bold shadow-sm
                                <?= $index === 0 ? 'bg-gradient-to-br from-amber-400 to-yellow-500 text-amber-900' : ($index === 1 ? 'bg-gradient-to-br from-slate-300 to-gray-400 text-slate-700' : 'bg-gradient-to-br from-orange-300 to-amber-400 text-orange-800') ?>">
                                <?= $index + 1 ?>
                            </span>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Info -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between mb-1.5">
                                <span class="font-semibold text-slate-800 truncate"><?= View::e($user['full_name']) ?></span>
                                <div class="flex items-center gap-3">
                                    <?php if ($userOverdue > 0): ?>
                                    <span class="inline-flex items-center gap-1 text-xs text-rose-600 font-medium bg-rose-50 px-2 py-0.5 rounded-full">
                                        <i data-lucide="alert-circle" class="h-3 w-3"></i>
                                        <?= $userOverdue ?> quá hạn
                                    </span>
                                    <?php endif; ?>
                                    <span class="text-base font-bold <?= $rateColor ?>">
                                        <?= $userRate ?>%
                                    </span>
                                </div>
                            </div>
                            
                            <!-- Progress bar -->
                            <div class="flex items-center gap-3">
                                <div class="flex-1 h-2.5 rounded-full bg-slate-100 overflow-hidden">
                                    <div class="h-full rounded-full <?= $barColor ?> transition-all duration-300" style="width: <?= max($userRate, 2) ?>%"></div>
                                </div>
                                <span class="text-xs font-medium text-slate-500 w-10 text-right"><?= $completedTasks ?>/<?= $userTotal ?></span>
                            </div>
                            
                            <!-- Task breakdown -->
                            <div class="flex items-center gap-4 mt-2">
                                <span class="flex items-center gap-1.5 text-xs text-slate-500">
                                    <span class="h-2 w-2 rounded-full bg-sky-400"></span>
                                    <?= $activeTasks ?> đang làm
                                </span>
                                <span class="flex items-center gap-1.5 text-xs text-slate-500">
                                    <span class="h-2 w-2 rounded-full bg-emerald-400"></span>
                                    <?= $completedTasks ?> hoàn thành
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="px-6 py-10 text-center">
                <div class="h-12 w-12 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-3">
                    <i data-lucide="users" class="h-6 w-6 text-slate-400"></i>
                </div>
                <p class="text-slate-500">Chưa có thành viên</p>
            </div>
            <?php endif; ?>
        </div>

        <!-- Overdue Tasks List -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="border-b border-gray-200 px-6 py-4 flex items-center justify-between">
                <h3 class="font-semibold text-gray-900">
                    <i data-lucide="clock" class="inline h-5 w-5 mr-2 text-rose-500"></i>
                    Công việc quá hạn
                </h3>
                <?php if (!empty($overdueTasks)): ?>
                <span class="inline-flex items-center gap-1 text-sm text-rose-600 font-medium bg-rose-50 px-2.5 py-1 rounded-full">
                    <?= count($overdueTasks) ?> công việc
                </span>
                <?php endif; ?>
            </div>
            <div class="divide-y divide-gray-100 max-h-[400px] overflow-y-auto">
                <?php if (!empty($overdueTasks)): ?>
                    <?php foreach (array_slice($overdueTasks, 0, 6) as $task): 
                        $daysOverdue = ceil((time() - strtotime($task['due_date'])) / 86400);
                        $urgencyClass = $daysOverdue > 7 ? 'bg-rose-100 text-rose-700' : ($daysOverdue > 3 ? 'bg-amber-100 text-amber-700' : 'bg-orange-100 text-orange-700');
                    ?>
                    <div class="px-6 py-4 hover:bg-rose-50/50 transition-colors">
                        <div class="flex items-start gap-3">
                            <div class="flex-shrink-0 mt-0.5">
                                <span class="inline-flex items-center justify-center h-8 w-8 rounded-lg <?= $urgencyClass ?>">
                                    <i data-lucide="alert-circle" class="h-4 w-4"></i>
                                </span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <a href="/php/task-detail.php?id=<?= View::e($task['id']) ?>" class="text-sm font-semibold text-slate-800 hover:text-indigo-600 line-clamp-1">
                                    <?= View::e($task['title']) ?>
                                </a>
                                <div class="flex items-center gap-2 mt-1.5">
                                    <span class="inline-flex items-center gap-1 text-xs text-slate-500">
                                        <i data-lucide="calendar" class="h-3 w-3"></i>
                                        <?= date('d/m/Y', strtotime($task['due_date'])) ?>
                                    </span>
                                    <?php if (!empty($task['project_name'])): ?>
                                    <span class="text-slate-300">•</span>
                                    <span class="text-xs text-slate-500 truncate max-w-[120px]"><?= View::e($task['project_name']) ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <span class="flex-shrink-0 inline-flex items-center px-2 py-1 rounded-md text-xs font-bold <?= $urgencyClass ?>">
                                -<?= $daysOverdue ?> ngày
                            </span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php if (count($overdueTasks) > 6): ?>
                    <div class="px-6 py-4 bg-slate-50">
                        <a href="/php/tasks.php?filter=overdue" class="flex items-center justify-center gap-2 text-sm font-medium text-indigo-600 hover:text-indigo-800">
                            Xem tất cả <?= count($overdueTasks) ?> công việc quá hạn
                            <i data-lucide="arrow-right" class="h-4 w-4"></i>
                        </a>
                    </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="px-6 py-12 text-center">
                        <div class="h-14 w-14 rounded-full bg-emerald-100 flex items-center justify-center mx-auto mb-4">
                            <i data-lucide="check-circle" class="h-7 w-7 text-emerald-500"></i>
                        </div>
                        <p class="text-slate-600 font-medium">Tuyệt vời!</p>
                        <p class="text-sm text-slate-400 mt-1">Không có công việc nào quá hạn</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
// Chart.js - Status Doughnut Chart
const statusCtx = document.getElementById('statusChart');
if (statusCtx) {
    new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: ['Chờ xử lý', 'Cần làm', 'Đang làm', 'Review', 'Hoàn thành'],
            datasets: [{
                data: [<?= $statusCounts['backlog'] ?>, <?= $statusCounts['todo'] ?>, <?= $statusCounts['in_progress'] ?>, <?= $statusCounts['in_review'] ?>, <?= $statusCounts['done'] ?>],
                backgroundColor: ['#9ca3af', '#3b82f6', '#eab308', '#a855f7', '#22c55e'],
                borderWidth: 0,
                cutout: '65%'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { display: false }
            }
        }
    });
}

// Chart.js - Priority Bar Chart
const priorityCtx = document.getElementById('priorityChart');
if (priorityCtx) {
    new Chart(priorityCtx, {
        type: 'bar',
        data: {
            labels: ['Thấp', 'Trung bình', 'Cao', 'Khẩn cấp'],
            datasets: [{
                label: 'Số lượng',
                data: [<?= $priorityCounts['low'] ?>, <?= $priorityCounts['medium'] ?>, <?= $priorityCounts['high'] ?>, <?= $priorityCounts['urgent'] ?>],
                backgroundColor: ['#9ca3af', '#3b82f6', '#f97316', '#ef4444'],
                borderRadius: 6,
                barThickness: 40
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1 }
                }
            }
        }
    });
}

<?php if ($isManager): ?>
// Chart.js - Trend Line Chart
const trendCtx = document.getElementById('trendChart');
if (trendCtx) {
    // Generate last 7 days labels
    const labels = [];
    const completedData = [];
    const createdData = [];
    
    for (let i = 6; i >= 0; i--) {
        const date = new Date();
        date.setDate(date.getDate() - i);
        labels.push(date.toLocaleDateString('vi-VN', { day: '2-digit', month: '2-digit' }));
        // Placeholder data - in real app, this would come from backend
        completedData.push(Math.floor(Math.random() * 5) + 1);
        createdData.push(Math.floor(Math.random() * 5) + 2);
    }
    
    new Chart(trendCtx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Hoàn thành',
                data: completedData,
                borderColor: '#22c55e',
                backgroundColor: 'rgba(34, 197, 94, 0.1)',
                fill: true,
                tension: 0.4
            }, {
                label: 'Tạo mới',
                data: createdData,
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    align: 'end'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1 }
                }
            }
        }
    });
}
<?php endif; ?>

// Export report
function exportReport(type, format) {
    const url = `/php/api/export-report.php?type=${type}&format=${format}`;
    window.open(url, '_blank');
    if (typeof showToast === 'function') {
        showToast('Đang xuất báo cáo...', 'info');
    }
}
</script>

<?php View::endSection(); ?>
