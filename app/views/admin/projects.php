<?php
/**
 * Admin Projects Management View - Enhanced
 */
use Core\View;

View::section('content');
?>

<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Quản lý dự án</h1>
            <p class="text-gray-500 mt-1">Xem và quản lý tất cả dự án trong hệ thống</p>
        </div>
        <div class="flex items-center gap-2">
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-colors">
                    <i data-lucide="download" class="h-5 w-5"></i>
                    Export
                </button>
                <div x-show="open" @click.away="open = false" x-cloak
                     class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-gray-200 py-1 z-50">
                    <a href="/php/api/admin-export.php?type=projects&format=csv" 
                       class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                        <i data-lucide="file-text" class="h-4 w-4"></i>Export CSV
                    </a>
                    <a href="/php/api/admin-export.php?type=projects&format=json" 
                       class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                        <i data-lucide="file-json" class="h-4 w-4"></i>Export JSON
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        <a href="/php/admin/projects.php" class="bg-white rounded-xl p-4 border border-gray-100 hover:border-blue-300 hover:shadow-md transition-all cursor-pointer <?= empty($filters['status']) ? 'ring-2 ring-blue-500' : '' ?>">
            <p class="text-2xl font-bold text-gray-900"><?= $projectStats['total'] ?? 0 ?></p>
            <p class="text-sm text-gray-500">Tổng cộng</p>
        </a>
        <a href="/php/admin/projects.php?status=active" class="bg-white rounded-xl p-4 border border-gray-100 hover:border-green-300 hover:shadow-md transition-all cursor-pointer <?= ($filters['status'] ?? '') === 'active' ? 'ring-2 ring-green-500' : '' ?>">
            <p class="text-2xl font-bold text-green-600"><?= $projectStats['active'] ?? 0 ?></p>
            <p class="text-sm text-gray-500">Đang thực hiện</p>
        </a>
        <a href="/php/admin/projects.php?status=completed" class="bg-white rounded-xl p-4 border border-gray-100 hover:border-blue-300 hover:shadow-md transition-all cursor-pointer <?= ($filters['status'] ?? '') === 'completed' ? 'ring-2 ring-blue-500' : '' ?>">
            <p class="text-2xl font-bold text-blue-600"><?= $projectStats['completed'] ?? 0 ?></p>
            <p class="text-sm text-gray-500">Hoàn thành</p>
        </a>
        <a href="/php/admin/projects.php?status=on_hold" class="bg-white rounded-xl p-4 border border-gray-100 hover:border-yellow-300 hover:shadow-md transition-all cursor-pointer <?= ($filters['status'] ?? '') === 'on_hold' ? 'ring-2 ring-yellow-500' : '' ?>">
            <p class="text-2xl font-bold text-yellow-600"><?= $projectStats['on_hold'] ?? 0 ?></p>
            <p class="text-sm text-gray-500">Tạm dừng</p>
        </a>
        <a href="/php/admin/projects.php?status=cancelled" class="bg-white rounded-xl p-4 border border-gray-100 hover:border-red-300 hover:shadow-md transition-all cursor-pointer <?= ($filters['status'] ?? '') === 'cancelled' ? 'ring-2 ring-red-500' : '' ?>">
            <p class="text-2xl font-bold text-red-600"><?= $projectStats['cancelled'] ?? 0 ?></p>
            <p class="text-sm text-gray-500">Đã hủy</p>
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl p-4 border border-gray-100">
        <form method="GET" class="flex flex-wrap items-center gap-4">
            <!-- Search -->
            <div class="flex-1 min-w-[250px]">
                <div class="relative">
                    <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400"></i>
                    <input type="text" name="search" value="<?= View::e($filters['search'] ?? '') ?>" 
                           placeholder="Tìm kiếm theo tên hoặc mô tả dự án..."
                           class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
            </div>
            
            <!-- Status Filter -->
            <div class="min-w-[160px]">
                <select name="status" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 bg-white">
                    <option value="">Tất cả trạng thái</option>
                    <option value="planning" <?= ($filters['status'] ?? '') === 'planning' ? 'selected' : '' ?>>Lên kế hoạch</option>
                    <option value="active" <?= ($filters['status'] ?? '') === 'active' ? 'selected' : '' ?>>Đang thực hiện</option>
                    <option value="on_hold" <?= ($filters['status'] ?? '') === 'on_hold' ? 'selected' : '' ?>>Tạm dừng</option>
                    <option value="completed" <?= ($filters['status'] ?? '') === 'completed' ? 'selected' : '' ?>>Hoàn thành</option>
                    <option value="cancelled" <?= ($filters['status'] ?? '') === 'cancelled' ? 'selected' : '' ?>>Đã hủy</option>
                </select>
            </div>
            
            <!-- Submit Button -->
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                <i data-lucide="filter" class="h-5 w-5"></i>
            </button>
            
            <!-- Clear Filters -->
            <?php if (!empty($filters['search']) || !empty($filters['status'])): ?>
            <a href="/php/admin/projects.php" class="px-4 py-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                Xóa bộ lọc
            </a>
            <?php endif; ?>
        </form>
        
        <!-- Active Filters Display -->
        <?php if (!empty($filters['search']) || !empty($filters['status'])): ?>
        <div class="flex flex-wrap items-center gap-2 mt-3 pt-3 border-t border-gray-100">
            <span class="text-sm text-gray-500">Đang lọc:</span>
            
            <?php if (!empty($filters['search'])): ?>
            <span class="inline-flex items-center gap-1 px-2 py-1 bg-blue-100 text-blue-700 rounded-full text-xs">
                <i data-lucide="search" class="h-3 w-3"></i>
                "<?= View::e($filters['search']) ?>"
            </span>
            <?php endif; ?>
            
            <?php if (!empty($filters['status'])): 
                $statusLabels = ['planning' => 'Lên kế hoạch', 'active' => 'Đang thực hiện', 'on_hold' => 'Tạm dừng', 'completed' => 'Hoàn thành', 'cancelled' => 'Đã hủy'];
            ?>
            <span class="inline-flex items-center gap-1 px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs">
                <i data-lucide="circle" class="h-3 w-3"></i>
                <?= $statusLabels[$filters['status']] ?? $filters['status'] ?>
            </span>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>

    <!-- Projects Table -->
    <div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Dự án</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Trạng thái</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Tiến độ</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Thành viên</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Công việc</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Người tạo</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Ngày tạo</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php if (!empty($projects)): ?>
                        <?php foreach ($projects as $project): ?>
                        <?php
                        $statusColors = [
                            'planning' => 'bg-gray-100 text-gray-700',
                            'active' => 'bg-green-100 text-green-700',
                            'on_hold' => 'bg-yellow-100 text-yellow-700',
                            'completed' => 'bg-blue-100 text-blue-700',
                            'cancelled' => 'bg-red-100 text-red-700',
                        ];
                        $statusNames = [
                            'planning' => 'Lên kế hoạch',
                            'active' => 'Đang thực hiện',
                            'on_hold' => 'Tạm dừng',
                            'completed' => 'Hoàn thành',
                            'cancelled' => 'Đã hủy',
                        ];
                        ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="h-10 w-10 rounded-lg flex items-center justify-center" 
                                         style="background-color: <?= View::e($project['color'] ?? '#6366f1') ?>20">
                                        <i data-lucide="folder" class="h-5 w-5" style="color: <?= View::e($project['color'] ?? '#6366f1') ?>"></i>
                                    </div>
                                    <div>
                                        <a href="/php/project-detail.php?id=<?= $project['id'] ?>" class="font-medium text-gray-900 hover:text-blue-600">
                                            <?= View::e($project['name']) ?>
                                        </a>
                                        <?php if ($project['description']): ?>
                                        <p class="text-sm text-gray-500 truncate max-w-xs"><?= View::e(substr($project['description'], 0, 50)) ?>...</p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 rounded-full text-xs font-medium <?= $statusColors[$project['status']] ?? 'bg-gray-100' ?>">
                                    <?= $statusNames[$project['status']] ?? $project['status'] ?>
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <div class="w-24 h-2 bg-gray-100 rounded-full overflow-hidden">
                                        <div class="h-full bg-blue-500 rounded-full" style="width: <?= $project['progress'] ?? 0 ?>%"></div>
                                    </div>
                                    <span class="text-sm text-gray-600"><?= $project['progress'] ?? 0 ?>%</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center gap-1 text-sm text-gray-600">
                                    <i data-lucide="users" class="h-4 w-4"></i>
                                    <?= $project['member_count'] ?? 0 ?>
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm text-gray-600">
                                    <?= $project['completed_tasks'] ?? 0 ?>/<?= $project['task_count'] ?? 0 ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                <?= View::e($project['creator_name'] ?? '-') ?>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                <?= date('d/m/Y', strtotime($project['created_at'])) ?>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="/php/project-detail.php?id=<?= $project['id'] ?>" 
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
                                <i data-lucide="folder-kanban" class="h-12 w-12 mx-auto mb-3 text-gray-300"></i>
                                <p>Chưa có dự án nào</p>
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
