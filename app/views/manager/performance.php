<?php
/**
 * Trang hiệu suất nhóm
 */
use Core\View;
use Core\Session;

View::section('content');
?>

<div class="space-y-6">
    <!-- Tiêu đề -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Hiệu suất nhóm</h1>
            <p class="text-gray-500">Theo dõi hiệu suất và năng suất của nhóm</p>
        </div>
        <div class="flex items-center gap-2">
            <select class="px-3 py-2 text-sm border border-gray-200 rounded-lg">
                <option>Tháng này</option>
                <option>Tuần này</option>
                <option>Quý này</option>
            </select>
        </div>
    </div>

    <!-- Thống kê tổng quan -->
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Tổng tasks</p>
                    <p class="text-2xl font-bold text-gray-900"><?= $taskStats['total_tasks'] ?? 0 ?></p>
                </div>
                <div class="h-11 w-11 rounded-lg bg-blue-100 flex items-center justify-center">
                    <i data-lucide="check-square" class="h-5 w-5 text-blue-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Hoàn thành</p>
                    <p class="text-2xl font-bold text-green-600"><?= $taskStats['completed_tasks'] ?? 0 ?></p>
                </div>
                <div class="h-11 w-11 rounded-lg bg-green-100 flex items-center justify-center">
                    <i data-lucide="check-circle" class="h-5 w-5 text-green-600"></i>
                </div>
            </div>
            <?php 
            $completionRate = ($taskStats['total_tasks'] ?? 0) > 0 
                ? round(($taskStats['completed_tasks'] ?? 0) / $taskStats['total_tasks'] * 100) 
                : 0;
            ?>
            <p class="mt-2 text-xs text-gray-500">
                <span class="text-green-600 font-medium"><?= $completionRate ?>%</span> tỷ lệ hoàn thành
            </p>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Đang thực hiện</p>
                    <p class="text-2xl font-bold text-blue-600"><?= $taskStats['in_progress_tasks'] ?? 0 ?></p>
                </div>
                <div class="h-11 w-11 rounded-lg bg-blue-100 flex items-center justify-center">
                    <i data-lucide="loader" class="h-5 w-5 text-blue-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl border <?= ($taskStats['overdue_tasks'] ?? 0) > 0 ? 'border-red-300 bg-red-50' : 'border-gray-200' ?> p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Quá hạn</p>
                    <p class="text-2xl font-bold <?= ($taskStats['overdue_tasks'] ?? 0) > 0 ? 'text-red-600' : 'text-gray-900' ?>">
                        <?= $taskStats['overdue_tasks'] ?? 0 ?>
                    </p>
                </div>
                <div class="h-11 w-11 rounded-lg <?= ($taskStats['overdue_tasks'] ?? 0) > 0 ? 'bg-red-100' : 'bg-gray-100' ?> flex items-center justify-center">
                    <i data-lucide="alert-triangle" class="h-5 w-5 <?= ($taskStats['overdue_tasks'] ?? 0) > 0 ? 'text-red-600' : 'text-gray-400' ?>"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Charts -->
    <div class="grid gap-6 lg:grid-cols-3">
        <!-- Completion Rate Gauge -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h3 class="font-semibold text-gray-900 mb-4 text-center">Tỷ lệ hoàn thành</h3>
            <div class="relative w-40 h-40 mx-auto">
                <canvas id="completionGauge"></canvas>
                <div class="absolute inset-0 flex items-center justify-center">
                    <span class="text-3xl font-bold text-gray-900"><?= $completionRate ?>%</span>
                </div>
            </div>
            <p class="text-center text-sm text-gray-500 mt-4">
                <?= $taskStats['completed_tasks'] ?? 0 ?> / <?= $taskStats['total_tasks'] ?? 0 ?> công việc
            </p>
        </div>

        <!-- Thành viên xuất sắc -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="border-b border-gray-200 px-6 py-4">
                <h3 class="font-semibold text-gray-900">
                    <i data-lucide="trophy" class="inline h-5 w-5 mr-2 text-yellow-500"></i>
                    Thành viên xuất sắc
                </h3>
            </div>
            <div class="p-4">
                <?php if (!empty($topPerformers)): ?>
                <div class="space-y-3">
                    <?php foreach (array_slice($topPerformers, 0, 5) as $i => $performer): 
                        $medalColor = $i === 0 ? 'text-yellow-500' : ($i === 1 ? 'text-gray-400' : ($i === 2 ? 'text-orange-400' : 'text-gray-300'));
                    ?>
                    <div class="flex items-center gap-3 p-2 rounded-lg <?= $i < 3 ? 'bg-gray-50' : '' ?>">
                        <span class="text-lg font-bold <?= $medalColor ?> w-6">#<?= $i + 1 ?></span>
                        <div class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center overflow-hidden">
                            <?php if (!empty($performer['avatar_url'])): ?>
                            <img src="/php/<?= View::e($performer['avatar_url']) ?>" class="h-full w-full object-cover">
                            <?php else: ?>
                            <span class="text-xs font-medium"><?= strtoupper(substr($performer['full_name'] ?? 'U', 0, 1)) ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate"><?= View::e($performer['full_name']) ?></p>
                        </div>
                        <span class="text-sm font-bold text-green-600"><?= $performer['completion_rate'] ?? 0 ?>%</span>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <div class="text-center py-6 text-gray-500 text-sm">Chưa có dữ liệu</div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Cần hỗ trợ -->
        <div class="bg-white rounded-xl border <?= !empty($needsAttention) ? 'border-red-200' : 'border-gray-200' ?> shadow-sm">
            <div class="border-b <?= !empty($needsAttention) ? 'border-red-200 bg-red-50' : 'border-gray-200' ?> px-6 py-4">
                <h3 class="font-semibold <?= !empty($needsAttention) ? 'text-red-800' : 'text-gray-900' ?>">
                    <i data-lucide="alert-circle" class="inline h-5 w-5 mr-2 text-red-500"></i>
                    Cần hỗ trợ
                </h3>
            </div>
            <div class="p-4">
                <?php if (!empty($needsAttention)): ?>
                <div class="space-y-3">
                    <?php foreach (array_slice($needsAttention, 0, 5) as $member): ?>
                    <div class="flex items-center gap-3 p-2 rounded-lg bg-red-50 border border-red-100">
                        <div class="h-8 w-8 rounded-full bg-red-100 flex items-center justify-center overflow-hidden">
                            <?php if (!empty($member['avatar_url'])): ?>
                            <img src="/php/<?= View::e($member['avatar_url']) ?>" class="h-full w-full object-cover">
                            <?php else: ?>
                            <span class="text-xs font-medium text-red-600"><?= strtoupper(substr($member['full_name'] ?? 'U', 0, 1)) ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate"><?= View::e($member['full_name']) ?></p>
                            <p class="text-xs text-red-600"><?= $member['overdue_count'] ?> quá hạn</p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <div class="text-center py-6 text-green-600 text-sm">
                    <i data-lucide="check-circle" class="h-8 w-8 mx-auto mb-2 text-green-400"></i>
                    <p>Tất cả đều ổn!</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Team Performance Bar Chart -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <h3 class="font-semibold text-gray-900 mb-4">
            <i data-lucide="bar-chart-3" class="inline h-5 w-5 mr-2 text-gray-400"></i>
            So sánh hiệu suất thành viên
        </h3>
        <div class="h-64">
            <canvas id="teamPerformanceChart"></canvas>
        </div>
    </div>

    <!-- Project Progress -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="border-b border-gray-200 px-6 py-4">
            <h3 class="font-semibold text-gray-900">
                <i data-lucide="folder-kanban" class="inline h-5 w-5 mr-2 text-gray-400"></i>
                Tiến độ dự án
            </h3>
        </div>
        <div class="p-6">
            <?php if (!empty($projects)): ?>
            <div class="space-y-4">
                <?php foreach ($projects as $project): 
                    $progress = $project['progress'] ?? 0;
                    $progressColor = $progress >= 80 ? 'bg-green-500' : ($progress >= 50 ? 'bg-blue-500' : ($progress >= 25 ? 'bg-yellow-500' : 'bg-gray-400'));
                ?>
                <div class="p-4 rounded-lg border border-gray-200 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-3">
                            <div class="h-10 w-10 rounded-lg flex items-center justify-center" 
                                 style="background-color: <?= View::e($project['color'] ?? '#6366f1') ?>20">
                                <i data-lucide="folder" class="h-5 w-5" style="color: <?= View::e($project['color'] ?? '#6366f1') ?>"></i>
                            </div>
                            <div>
                                <a href="/php/project-detail.php?id=<?= View::e($project['id']) ?>" 
                                   class="font-medium text-gray-900 hover:text-primary">
                                    <?= View::e($project['name']) ?>
                                </a>
                                <p class="text-sm text-gray-500"><?= $project['member_count'] ?? 0 ?> thành viên</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-2xl font-bold <?= $progress >= 80 ? 'text-green-600' : ($progress >= 50 ? 'text-blue-600' : 'text-gray-600') ?>">
                                <?= $progress ?>%
                            </p>
                            <p class="text-xs text-gray-500"><?= $project['completed_tasks'] ?? 0 ?>/<?= $project['total_tasks'] ?? 0 ?> tasks</p>
                        </div>
                    </div>
                    <div class="h-2 rounded-full bg-gray-100 overflow-hidden">
                        <div class="h-full rounded-full <?= $progressColor ?> transition-all" style="width: <?= $progress ?>%"></div>
                    </div>
                    <?php if (($project['overdue_tasks'] ?? 0) > 0): ?>
                    <p class="mt-2 text-xs text-red-600">
                        <i data-lucide="alert-triangle" class="inline h-3 w-3"></i>
                        <?= $project['overdue_tasks'] ?> tasks quá hạn
                    </p>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="text-center py-8 text-gray-500">Chưa có dự án nào</div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Team Members Performance Table -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="border-b border-gray-200 px-6 py-4">
            <h3 class="font-semibold text-gray-900">Chi tiết hiệu suất thành viên</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50 text-left text-sm text-gray-500">
                        <th class="px-6 py-3 font-medium">Thành viên</th>
                        <th class="px-6 py-3 font-medium text-center">Đang làm</th>
                        <th class="px-6 py-3 font-medium text-center">Hoàn thành</th>
                        <th class="px-6 py-3 font-medium text-center">Quá hạn</th>
                        <th class="px-6 py-3 font-medium text-center">Tỷ lệ</th>
                        <th class="px-6 py-3 font-medium">Trạng thái</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php foreach ($teamMembers ?? [] as $member): 
                        $total = ($member['active_tasks'] ?? 0) + ($member['completed_tasks'] ?? 0);
                        $rate = $total > 0 ? round(($member['completed_tasks'] ?? 0) / $total * 100) : 0;
                        
                        $status = 'normal';
                        if (($member['overdue_tasks'] ?? 0) > 2) $status = 'warning';
                        if (($member['active_tasks'] ?? 0) > 8) $status = 'overloaded';
                        if (($member['active_tasks'] ?? 0) < 2 && ($member['overdue_tasks'] ?? 0) == 0) $status = 'idle';
                    ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center overflow-hidden">
                                    <?php if (!empty($member['avatar_url'])): ?>
                                    <img src="/php/<?= View::e($member['avatar_url']) ?>" class="h-full w-full object-cover">
                                    <?php else: ?>
                                    <span class="text-xs font-medium"><?= strtoupper(substr($member['full_name'] ?? 'U', 0, 1)) ?></span>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900"><?= View::e($member['full_name']) ?></p>
                                    <p class="text-xs text-gray-500"><?= View::e($member['position'] ?? 'Member') ?></p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center font-medium text-blue-600"><?= $member['active_tasks'] ?? 0 ?></td>
                        <td class="px-6 py-4 text-center font-medium text-green-600"><?= $member['completed_tasks'] ?? 0 ?></td>
                        <td class="px-6 py-4 text-center font-medium <?= ($member['overdue_tasks'] ?? 0) > 0 ? 'text-red-600' : 'text-gray-400' ?>">
                            <?= $member['overdue_tasks'] ?? 0 ?>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="font-bold <?= $rate >= 70 ? 'text-green-600' : ($rate >= 40 ? 'text-blue-600' : 'text-gray-600') ?>">
                                <?= $rate ?>%
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <?php if ($status === 'overloaded'): ?>
                            <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium bg-red-100 text-red-700">Quá tải</span>
                            <?php elseif ($status === 'warning'): ?>
                            <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium bg-orange-100 text-orange-700">Cần hỗ trợ</span>
                            <?php elseif ($status === 'idle'): ?>
                            <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium bg-blue-100 text-blue-700">Rảnh</span>
                            <?php else: ?>
                            <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium bg-green-100 text-green-700">Bình thường</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
// Completion Rate Gauge Chart
const gaugeCtx = document.getElementById('completionGauge');
if (gaugeCtx) {
    new Chart(gaugeCtx, {
        type: 'doughnut',
        data: {
            datasets: [{
                data: [<?= $completionRate ?>, <?= 100 - $completionRate ?>],
                backgroundColor: ['#22c55e', '#e5e7eb'],
                borderWidth: 0,
                cutout: '75%'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { display: false },
                tooltip: { enabled: false }
            },
            rotation: -90,
            circumference: 180
        }
    });
}

// Team Performance Bar Chart
const teamCtx = document.getElementById('teamPerformanceChart');
if (teamCtx) {
    const teamData = {
        labels: [<?php 
            $names = [];
            foreach (array_slice($teamMembers ?? [], 0, 8) as $m) {
                $names[] = "'" . addslashes($m['full_name'] ?? 'Unknown') . "'";
            }
            echo implode(',', $names);
        ?>],
        datasets: [{
            label: 'Hoàn thành',
            data: [<?php 
                $completed = [];
                foreach (array_slice($teamMembers ?? [], 0, 8) as $m) {
                    $completed[] = $m['completed_tasks'] ?? 0;
                }
                echo implode(',', $completed);
            ?>],
            backgroundColor: '#22c55e',
            borderRadius: 4
        }, {
            label: 'Đang làm',
            data: [<?php 
                $active = [];
                foreach (array_slice($teamMembers ?? [], 0, 8) as $m) {
                    $active[] = $m['active_tasks'] ?? 0;
                }
                echo implode(',', $active);
            ?>],
            backgroundColor: '#3b82f6',
            borderRadius: 4
        }, {
            label: 'Quá hạn',
            data: [<?php 
                $overdue = [];
                foreach (array_slice($teamMembers ?? [], 0, 8) as $m) {
                    $overdue[] = $m['overdue_tasks'] ?? 0;
                }
                echo implode(',', $overdue);
            ?>],
            backgroundColor: '#ef4444',
            borderRadius: 4
        }]
    };
    
    new Chart(teamCtx, {
        type: 'bar',
        data: teamData,
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
                x: {
                    stacked: false
                },
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1 }
                }
            }
        }
    });
}
</script>

<?php View::endSection(); ?>
