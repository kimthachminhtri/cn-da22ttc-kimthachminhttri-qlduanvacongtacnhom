<?php
/**
 * Trang quản lý khối lượng công việc
 */
use Core\View;
use Core\Session;

View::section('content');
?>

<div class="space-y-6">
    <!-- Tiêu đề -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Quản lý khối lượng công việc</h1>
            <p class="text-gray-500">Quản lý và phân bổ công việc cho nhóm</p>
        </div>
        <div class="flex items-center gap-2">
            <select id="project-filter" onchange="filterByProject(this.value)" 
                    class="px-3 py-2 text-sm border border-gray-200 rounded-lg">
                <option value="">Tất cả dự án</option>
                <?php foreach ($projects ?? [] as $project): ?>
                <option value="<?= View::e($project['id']) ?>"><?= View::e($project['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <!-- Tổng quan khối lượng -->
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Tổng thành viên</p>
                    <p class="text-2xl font-bold text-gray-900"><?= count($teamMembers ?? []) ?></p>
                </div>
                <div class="h-11 w-11 rounded-lg bg-blue-100 flex items-center justify-center">
                    <i data-lucide="users" class="h-5 w-5 text-blue-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Tổng công việc</p>
                    <p class="text-2xl font-bold text-gray-900"><?= count($teamTasks ?? []) ?></p>
                </div>
                <div class="h-11 w-11 rounded-lg bg-green-100 flex items-center justify-center">
                    <i data-lucide="check-square" class="h-5 w-5 text-green-600"></i>
                </div>
            </div>
        </div>

        <?php 
        $overloadedCount = 0;
        foreach ($teamMembers ?? [] as $m) {
            if (($m['active_tasks'] ?? 0) > 8) $overloadedCount++;
        }
        ?>
        <div class="bg-white rounded-xl border <?= $overloadedCount > 0 ? 'border-orange-300' : 'border-gray-200' ?> p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Quá tải</p>
                    <p class="text-2xl font-bold <?= $overloadedCount > 0 ? 'text-orange-600' : 'text-gray-900' ?>"><?= $overloadedCount ?></p>
                </div>
                <div class="h-11 w-11 rounded-lg <?= $overloadedCount > 0 ? 'bg-orange-100' : 'bg-gray-100' ?> flex items-center justify-center">
                    <i data-lucide="alert-circle" class="h-5 w-5 <?= $overloadedCount > 0 ? 'text-orange-600' : 'text-gray-400' ?>"></i>
                </div>
            </div>
        </div>
        
        <?php 
        $idleCount = 0;
        foreach ($teamMembers ?? [] as $m) {
            if (($m['active_tasks'] ?? 0) < 2) $idleCount++;
        }
        ?>
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Ít việc</p>
                    <p class="text-2xl font-bold text-gray-900"><?= $idleCount ?></p>
                </div>
                <div class="h-11 w-11 rounded-lg bg-gray-100 flex items-center justify-center">
                    <i data-lucide="coffee" class="h-5 w-5 text-gray-500"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Team Workload Grid -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="border-b border-gray-200 px-6 py-4">
            <h3 class="font-semibold text-gray-900">Phân bổ công việc theo thành viên</h3>
        </div>
        <div class="p-6">
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <?php foreach ($teamMembers ?? [] as $member): 
                    $activeTasks = $member['active_tasks'] ?? 0;
                    $completedTasks = $member['completed_tasks'] ?? 0;
                    $overdueTasks = $member['overdue_tasks'] ?? 0;
                    $inProgressTasks = $member['in_progress_tasks'] ?? 0;
                    
                    // Workload level
                    $workloadLevel = 'normal';
                    $workloadColor = 'border-gray-200';
                    $workloadBg = 'bg-white';
                    if ($activeTasks > 8) {
                        $workloadLevel = 'overloaded';
                        $workloadColor = 'border-red-300';
                        $workloadBg = 'bg-red-50';
                    } elseif ($activeTasks > 5) {
                        $workloadLevel = 'busy';
                        $workloadColor = 'border-orange-300';
                        $workloadBg = 'bg-orange-50';
                    } elseif ($activeTasks < 2) {
                        $workloadLevel = 'idle';
                        $workloadColor = 'border-blue-300';
                        $workloadBg = 'bg-blue-50';
                    }
                ?>
                <div class="rounded-xl border-2 <?= $workloadColor ?> <?= $workloadBg ?> p-4">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="h-12 w-12 rounded-full bg-gray-200 flex items-center justify-center overflow-hidden">
                            <?php if (!empty($member['avatar_url'])): ?>
                            <img src="/php/<?= View::e($member['avatar_url']) ?>" class="h-full w-full object-cover">
                            <?php else: ?>
                            <span class="text-lg font-medium"><?= strtoupper(substr($member['full_name'] ?? 'U', 0, 1)) ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="font-medium text-gray-900 truncate"><?= View::e($member['full_name']) ?></h4>
                            <p class="text-sm text-gray-500"><?= View::e($member['position'] ?? 'Member') ?></p>
                        </div>
                        <?php if ($workloadLevel === 'overloaded'): ?>
                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-700">Quá tải</span>
                        <?php elseif ($workloadLevel === 'busy'): ?>
                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-orange-100 text-orange-700">Bận</span>
                        <?php elseif ($workloadLevel === 'idle'): ?>
                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-700">Rảnh</span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-3 text-center">
                        <div class="bg-white rounded-lg p-2 border border-gray-100">
                            <p class="text-lg font-bold text-blue-600"><?= $inProgressTasks ?></p>
                            <p class="text-xs text-gray-500">Đang làm</p>
                        </div>
                        <div class="bg-white rounded-lg p-2 border border-gray-100">
                            <p class="text-lg font-bold text-gray-600"><?= $activeTasks - $inProgressTasks ?></p>
                            <p class="text-xs text-gray-500">Chờ xử lý</p>
                        </div>
                        <div class="bg-white rounded-lg p-2 border border-gray-100">
                            <p class="text-lg font-bold text-green-600"><?= $completedTasks ?></p>
                            <p class="text-xs text-gray-500">Hoàn thành</p>
                        </div>
                        <div class="bg-white rounded-lg p-2 border <?= $overdueTasks > 0 ? 'border-red-200' : 'border-gray-100' ?>">
                            <p class="text-lg font-bold <?= $overdueTasks > 0 ? 'text-red-600' : 'text-gray-400' ?>"><?= $overdueTasks ?></p>
                            <p class="text-xs text-gray-500">Quá hạn</p>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Tasks by Member -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="border-b border-gray-200 px-6 py-4">
            <h3 class="font-semibold text-gray-900">Chi tiết công việc</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50 text-left text-sm text-gray-500">
                        <th class="px-6 py-3 font-medium">Công việc</th>
                        <th class="px-6 py-3 font-medium">Người thực hiện</th>
                        <th class="px-6 py-3 font-medium">Dự án</th>
                        <th class="px-6 py-3 font-medium">Trạng thái</th>
                        <th class="px-6 py-3 font-medium">Deadline</th>
                        <th class="px-6 py-3 font-medium">Độ ưu tiên</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php foreach (array_slice($teamTasks ?? [], 0, 20) as $task): 
                        $isOverdue = !empty($task['due_date']) && strtotime($task['due_date']) < strtotime('today') && $task['status'] !== 'done';
                    ?>
                    <tr class="hover:bg-gray-50 <?= $isOverdue ? 'bg-red-50' : '' ?>">
                        <td class="px-6 py-4">
                            <a href="/php/task-detail.php?id=<?= View::e($task['id']) ?>" 
                               class="font-medium text-gray-900 hover:text-primary">
                                <?= View::e($task['title']) ?>
                            </a>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            <?= View::e($task['assignee_names'] ?? 'Chưa giao') ?>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center gap-1 text-sm">
                                <span class="h-2 w-2 rounded-full" style="background-color: <?= View::e($task['project_color'] ?? '#6366f1') ?>"></span>
                                <?= View::e($task['project_name'] ?? '') ?>
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <?php
                            $statusClasses = [
                                'todo' => 'bg-gray-100 text-gray-800',
                                'in_progress' => 'bg-blue-100 text-blue-800',
                                'in_review' => 'bg-purple-100 text-purple-800',
                                'done' => 'bg-green-100 text-green-800',
                            ];
                            $statusLabels = [
                                'todo' => 'Cần làm',
                                'in_progress' => 'Đang làm',
                                'in_review' => 'Review',
                                'done' => 'Hoàn thành',
                            ];
                            ?>
                            <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium <?= $statusClasses[$task['status'] ?? 'todo'] ?? 'bg-gray-100 text-gray-800' ?>">
                                <?= $statusLabels[$task['status'] ?? 'todo'] ?? $task['status'] ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm <?= $isOverdue ? 'text-red-600 font-medium' : 'text-gray-500' ?>">
                            <?= !empty($task['due_date']) ? date('d/m/Y', strtotime($task['due_date'])) : '-' ?>
                            <?php if ($isOverdue): ?>
                            <span class="text-xs">(Quá hạn)</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4">
                            <?php
                            $priorityClasses = [
                                'urgent' => 'bg-red-100 text-red-800',
                                'high' => 'bg-orange-100 text-orange-800',
                                'medium' => 'bg-blue-100 text-blue-800',
                                'low' => 'bg-gray-100 text-gray-800',
                            ];
                            $priorityLabels = ['urgent' => 'Khẩn cấp', 'high' => 'Cao', 'medium' => 'TB', 'low' => 'Thấp'];
                            ?>
                            <span class="inline-flex items-center rounded px-2 py-0.5 text-xs font-medium <?= $priorityClasses[$task['priority'] ?? 'medium'] ?>">
                                <?= $priorityLabels[$task['priority'] ?? 'medium'] ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function filterByProject(projectId) {
    // TODO: Implement AJAX filter
    console.log('Filter by project:', projectId);
}
</script>

<?php View::endSection(); ?>
