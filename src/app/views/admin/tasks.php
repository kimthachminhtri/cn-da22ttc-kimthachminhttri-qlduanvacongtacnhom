<?php
/**
 * Admin Tasks Management View - Enhanced
 */
use Core\View;

View::section('content');
?>

<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Quản lý công việc</h1>
            <p class="text-gray-500 mt-1">Xem và quản lý tất cả công việc trong hệ thống</p>
        </div>
        <div class="flex items-center gap-2">
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-colors">
                    <i data-lucide="download" class="h-5 w-5"></i>
                    Export
                    <i data-lucide="chevron-down" class="h-4 w-4"></i>
                </button>
                <div x-show="open" @click.away="open = false" x-cloak
                     class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-lg border border-gray-200 py-1 z-50">
                    <div class="px-3 py-2 border-b border-gray-100">
                        <p class="text-xs font-semibold text-gray-400 uppercase">Danh sách công việc</p>
                    </div>
                    <a href="/php/api/admin-export.php?type=tasks&format=excel" target="_blank"
                       class="flex items-center gap-2 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50">
                        <i data-lucide="file-spreadsheet" class="h-4 w-4 text-green-600"></i>
                        Excel (.xlsx)
                    </a>
                    <a href="/php/api/admin-export.php?type=tasks&format=pdf" target="_blank"
                       class="flex items-center gap-2 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50">
                        <i data-lucide="file-text" class="h-4 w-4 text-red-600"></i>
                        PDF
                    </a>
                    <a href="/php/api/admin-export.php?type=tasks&format=csv" target="_blank"
                       class="flex items-center gap-2 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50">
                        <i data-lucide="file" class="h-4 w-4 text-blue-600"></i>
                        CSV
                    </a>
                    
                    <div class="px-3 py-2 border-t border-b border-gray-100 mt-1">
                        <p class="text-xs font-semibold text-gray-400 uppercase">Báo cáo tổng hợp</p>
                    </div>
                    <a href="/php/api/admin-export.php?type=tasks_summary&format=excel" target="_blank"
                       class="flex items-center gap-2 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50">
                        <i data-lucide="bar-chart-3" class="h-4 w-4 text-purple-600"></i>
                        Tổng hợp theo dự án
                    </a>
                    <a href="/php/api/admin-export.php?type=team_performance&format=excel" target="_blank"
                       class="flex items-center gap-2 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50">
                        <i data-lucide="users" class="h-4 w-4 text-indigo-600"></i>
                        Hiệu suất nhân viên
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        <a href="/php/admin/tasks.php" class="bg-white rounded-xl p-4 border border-gray-100 hover:border-blue-300 hover:shadow-md transition-all cursor-pointer <?= empty($filters['status']) && empty($filters['project']) ? 'ring-2 ring-blue-500' : '' ?>">
            <p class="text-2xl font-bold text-gray-900"><?= $taskStats['total'] ?? 0 ?></p>
            <p class="text-sm text-gray-500">Tổng cộng</p>
        </a>
        <a href="/php/admin/tasks.php?status=done" class="bg-white rounded-xl p-4 border border-gray-100 hover:border-green-300 hover:shadow-md transition-all cursor-pointer <?= ($filters['status'] ?? '') === 'done' ? 'ring-2 ring-green-500' : '' ?>">
            <p class="text-2xl font-bold text-green-600"><?= $taskStats['done'] ?? 0 ?></p>
            <p class="text-sm text-gray-500">Hoàn thành</p>
        </a>
        <a href="/php/admin/tasks.php?status=in_progress" class="bg-white rounded-xl p-4 border border-gray-100 hover:border-blue-300 hover:shadow-md transition-all cursor-pointer <?= ($filters['status'] ?? '') === 'in_progress' ? 'ring-2 ring-blue-500' : '' ?>">
            <p class="text-2xl font-bold text-blue-600"><?= $taskStats['in_progress'] ?? 0 ?></p>
            <p class="text-sm text-gray-500">Đang làm</p>
        </a>
        <a href="/php/admin/tasks.php?status=todo" class="bg-white rounded-xl p-4 border border-gray-100 hover:border-yellow-300 hover:shadow-md transition-all cursor-pointer <?= ($filters['status'] ?? '') === 'todo' ? 'ring-2 ring-yellow-500' : '' ?>">
            <p class="text-2xl font-bold text-yellow-600"><?= $taskStats['todo'] ?? 0 ?></p>
            <p class="text-sm text-gray-500">Chờ xử lý</p>
        </a>
        <a href="/php/admin/tasks.php?status=overdue" class="block bg-white rounded-xl p-4 border border-gray-100 hover:border-red-400 hover:shadow-md transition-all cursor-pointer <?= ($filters['status'] ?? '') === 'overdue' ? 'ring-2 ring-red-500 border-red-300' : '' ?>">
            <p class="text-2xl font-bold text-red-600"><?= $taskStats['overdue'] ?? 0 ?></p>
            <p class="text-sm text-gray-500">Quá hạn</p>
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl p-4 border border-gray-100">
        <form method="GET" class="flex flex-wrap items-center gap-4">
            <!-- Search -->
            <div class="flex-1 min-w-[200px]">
                <div class="relative">
                    <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400"></i>
                    <input type="text" name="search" value="<?= View::e($filters['search'] ?? '') ?>" 
                           placeholder="Tìm kiếm theo tiêu đề..."
                           class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
            </div>
            
            <!-- Project Filter -->
            <div class="min-w-[180px]">
                <select name="project" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 bg-white">
                    <option value="">Tất cả dự án</option>
                    <?php foreach ($projects ?? [] as $project): ?>
                    <option value="<?= $project['id'] ?>" <?= ($filters['project'] ?? '') === $project['id'] ? 'selected' : '' ?>>
                        <?= View::e($project['name']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <!-- Status Filter -->
            <div class="min-w-[150px]">
                <select name="status" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 bg-white">
                    <option value="">Tất cả trạng thái</option>
                    <option value="todo" <?= ($filters['status'] ?? '') === 'todo' ? 'selected' : '' ?>>Chờ xử lý</option>
                    <option value="in_progress" <?= ($filters['status'] ?? '') === 'in_progress' ? 'selected' : '' ?>>Đang làm</option>
                    <option value="done" <?= ($filters['status'] ?? '') === 'done' ? 'selected' : '' ?>>Hoàn thành</option>
                    <option value="overdue" <?= ($filters['status'] ?? '') === 'overdue' ? 'selected' : '' ?>>Quá hạn</option>
                </select>
            </div>
            
            <!-- Priority Filter -->
            <div class="min-w-[140px]">
                <select name="priority" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 bg-white">
                    <option value="">Tất cả ưu tiên</option>
                    <option value="urgent" <?= ($filters['priority'] ?? '') === 'urgent' ? 'selected' : '' ?>>Khẩn cấp</option>
                    <option value="high" <?= ($filters['priority'] ?? '') === 'high' ? 'selected' : '' ?>>Cao</option>
                    <option value="medium" <?= ($filters['priority'] ?? '') === 'medium' ? 'selected' : '' ?>>Trung bình</option>
                    <option value="low" <?= ($filters['priority'] ?? '') === 'low' ? 'selected' : '' ?>>Thấp</option>
                </select>
            </div>
            
            <!-- Submit Button -->
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                <i data-lucide="filter" class="h-5 w-5"></i>
            </button>
            
            <!-- Clear Filters -->
            <?php if (!empty($filters['search']) || !empty($filters['project']) || !empty($filters['status']) || !empty($filters['priority'])): ?>
            <a href="/php/admin/tasks.php" class="px-4 py-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                Xóa bộ lọc
            </a>
            <?php endif; ?>
        </form>
        
        <!-- Active Filters Display -->
        <?php if (!empty($filters['project']) || !empty($filters['status']) || !empty($filters['priority']) || !empty($filters['search'])): ?>
        <div class="flex flex-wrap items-center gap-2 mt-3 pt-3 border-t border-gray-100">
            <span class="text-sm text-gray-500">Đang lọc:</span>
            
            <?php if (!empty($filters['search'])): ?>
            <span class="inline-flex items-center gap-1 px-2 py-1 bg-blue-100 text-blue-700 rounded-full text-xs">
                <i data-lucide="search" class="h-3 w-3"></i>
                "<?= View::e($filters['search']) ?>"
            </span>
            <?php endif; ?>
            
            <?php if (!empty($filters['project'])): 
                $projectName = '';
                foreach ($projects ?? [] as $p) {
                    if ($p['id'] === $filters['project']) {
                        $projectName = $p['name'];
                        break;
                    }
                }
            ?>
            <span class="inline-flex items-center gap-1 px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs">
                <i data-lucide="folder" class="h-3 w-3"></i>
                <?= View::e($projectName) ?>
            </span>
            <?php endif; ?>
            
            <?php if (!empty($filters['status'])): 
                $statusLabels = ['todo' => 'Chờ xử lý', 'in_progress' => 'Đang làm', 'done' => 'Hoàn thành', 'overdue' => 'Quá hạn'];
            ?>
            <span class="inline-flex items-center gap-1 px-2 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs">
                <i data-lucide="circle" class="h-3 w-3"></i>
                <?= $statusLabels[$filters['status']] ?? $filters['status'] ?>
            </span>
            <?php endif; ?>
            
            <?php if (!empty($filters['priority'])): 
                $priorityLabels = ['urgent' => 'Khẩn cấp', 'high' => 'Cao', 'medium' => 'Trung bình', 'low' => 'Thấp'];
            ?>
            <span class="inline-flex items-center gap-1 px-2 py-1 bg-red-100 text-red-700 rounded-full text-xs">
                <i data-lucide="flag" class="h-3 w-3"></i>
                <?= $priorityLabels[$filters['priority']] ?? $filters['priority'] ?>
            </span>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>

    <!-- Bulk Actions Toolbar -->
    <div id="bulk-toolbar" class="hidden items-center gap-4 bg-blue-50 border border-blue-200 rounded-xl p-4 mb-4">
        <div class="flex items-center gap-2">
            <span class="text-sm font-medium text-blue-700">
                Đã chọn: <span id="selected-count" class="font-bold">0</span> mục
            </span>
        </div>
        <div class="flex items-center gap-2 ml-auto">
            <!-- Status Update -->
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" class="inline-flex items-center gap-2 px-3 py-1.5 bg-white border border-gray-200 rounded-lg text-sm hover:bg-gray-50">
                    <i data-lucide="circle" class="h-4 w-4"></i>
                    Trạng thái
                    <i data-lucide="chevron-down" class="h-3 w-3"></i>
                </button>
                <div x-show="open" @click.away="open = false" x-cloak class="absolute left-0 mt-1 w-40 bg-white rounded-lg shadow-lg border z-50">
                    <button onclick="bulkOps.updateStatus('todo')" class="w-full text-left px-3 py-2 text-sm hover:bg-gray-50">Chờ xử lý</button>
                    <button onclick="bulkOps.updateStatus('in_progress')" class="w-full text-left px-3 py-2 text-sm hover:bg-gray-50">Đang làm</button>
                    <button onclick="bulkOps.updateStatus('in_review')" class="w-full text-left px-3 py-2 text-sm hover:bg-gray-50">Review</button>
                    <button onclick="bulkOps.updateStatus('done')" class="w-full text-left px-3 py-2 text-sm hover:bg-gray-50">Hoàn thành</button>
                </div>
            </div>
            
            <!-- Priority Update -->
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" class="inline-flex items-center gap-2 px-3 py-1.5 bg-white border border-gray-200 rounded-lg text-sm hover:bg-gray-50">
                    <i data-lucide="flag" class="h-4 w-4"></i>
                    Ưu tiên
                    <i data-lucide="chevron-down" class="h-3 w-3"></i>
                </button>
                <div x-show="open" @click.away="open = false" x-cloak class="absolute left-0 mt-1 w-36 bg-white rounded-lg shadow-lg border z-50">
                    <button onclick="bulkOps.updatePriority('urgent')" class="w-full text-left px-3 py-2 text-sm text-red-600 hover:bg-gray-50">Khẩn cấp</button>
                    <button onclick="bulkOps.updatePriority('high')" class="w-full text-left px-3 py-2 text-sm text-orange-600 hover:bg-gray-50">Cao</button>
                    <button onclick="bulkOps.updatePriority('medium')" class="w-full text-left px-3 py-2 text-sm text-yellow-600 hover:bg-gray-50">Trung bình</button>
                    <button onclick="bulkOps.updatePriority('low')" class="w-full text-left px-3 py-2 text-sm text-gray-600 hover:bg-gray-50">Thấp</button>
                </div>
            </div>
            
            <!-- Delete -->
            <button onclick="bulkOps.delete()" class="inline-flex items-center gap-2 px-3 py-1.5 bg-red-50 text-red-600 border border-red-200 rounded-lg text-sm hover:bg-red-100">
                <i data-lucide="trash-2" class="h-4 w-4"></i>
                Xóa
            </button>
            
            <!-- Clear Selection -->
            <button onclick="bulkOps.clearSelection()" class="p-1.5 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg">
                <i data-lucide="x" class="h-4 w-4"></i>
            </button>
        </div>
    </div>

    <!-- Tasks Table -->
    <div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-4 py-4 text-left">
                            <input type="checkbox" id="select-all" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Công việc</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Dự án</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Trạng thái</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Ưu tiên</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Deadline</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Người tạo</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php if (!empty($tasks)): ?>
                        <?php foreach ($tasks as $task): ?>
                        <?php
                        $statusColors = [
                            'done' => 'bg-green-100 text-green-700',
                            'in_progress' => 'bg-blue-100 text-blue-700',
                            'todo' => 'bg-gray-100 text-gray-700',
                            'backlog' => 'bg-yellow-100 text-yellow-700',
                            'in_review' => 'bg-purple-100 text-purple-700',
                        ];
                        $statusNames = [
                            'done' => 'Hoàn thành',
                            'in_progress' => 'Đang làm',
                            'todo' => 'Chờ xử lý',
                            'backlog' => 'Backlog',
                            'in_review' => 'Review',
                        ];
                        $priorityColors = [
                            'urgent' => 'text-red-600 bg-red-50',
                            'high' => 'text-orange-600 bg-orange-50',
                            'medium' => 'text-yellow-600 bg-yellow-50',
                            'low' => 'text-gray-600 bg-gray-50',
                        ];
                        $isOverdue = !empty($task['due_date']) && $task['due_date'] < date('Y-m-d') && $task['status'] !== 'done';
                        ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-4">
                                <input type="checkbox" class="bulk-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500" data-id="<?= $task['id'] ?>">
                            </td>
                            <td class="px-6 py-4">
                                <a href="/php/task-detail.php?id=<?= $task['id'] ?>" class="font-medium text-gray-900 hover:text-blue-600">
                                    <?= View::e($task['title']) ?>
                                </a>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                <?php if ($task['project_name']): ?>
                                <a href="/php/admin/tasks.php?project=<?= $task['project_id'] ?>" 
                                   class="inline-flex items-center gap-1.5 hover:text-blue-600">
                                    <span class="h-2.5 w-2.5 rounded-full" style="background-color: <?= $task['project_color'] ?? '#3b82f6' ?>"></span>
                                    <?= View::e($task['project_name']) ?>
                                </a>
                                <?php else: ?>
                                <span class="text-gray-400">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 rounded-full text-xs font-medium <?= $statusColors[$task['status']] ?? 'bg-gray-100' ?>">
                                    <?= $statusNames[$task['status']] ?? $task['status'] ?>
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 rounded text-xs font-medium <?= $priorityColors[$task['priority']] ?? '' ?>">
                                    <?= ucfirst($task['priority']) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm <?= $isOverdue ? 'text-red-600 font-medium' : 'text-gray-500' ?>">
                                <?php if ($task['due_date']): ?>
                                    <?= date('d/m/Y', strtotime($task['due_date'])) ?>
                                    <?php if ($isOverdue): ?>
                                    <span class="text-xs">(Quá hạn)</span>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="text-gray-400">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                <?= View::e($task['creator_name'] ?? '-') ?>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="/php/task-detail.php?id=<?= $task['id'] ?>" 
                                       class="p-2 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors">
                                        <i data-lucide="eye" class="h-4 w-4"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                                <i data-lucide="check-square" class="h-12 w-12 mx-auto mb-3 text-gray-300"></i>
                                <p>Chưa có công việc nào</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <?php if (isset($pagination)): ?>
        <?php View::partial('components/admin-pagination', ['pagination' => $pagination]); ?>
        <?php endif; ?>
    </div>
</div>

<?php View::endSection(); ?>

<!-- Bulk Operations Script -->
<script src="/php/public/js/bulk-operations.js"></script>
<script>
    const bulkOps = new BulkOperations({ entity: 'tasks' });
</script>
