<?php
/**
 * Projects List View - Enhanced for Manager
 */
use Core\View;
use Core\Session;
use Core\Permission;

Session::start();

$userRole = Session::get('user_role', 'guest');
$isManager = $isManager ?? false;
$statusFilter = $_GET['status'] ?? 'all';
$projectStats = $projectStats ?? [];

View::section('content');
?>

<div class="space-y-6">
    <?php if ($isManager && !empty($projectStats)): ?>
    <!-- Project Stats for Manager -->
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Tổng dự án</p>
                    <p class="text-2xl font-bold text-gray-900"><?= $projectStats['total_projects'] ?? 0 ?></p>
                </div>
                <div class="h-11 w-11 rounded-lg bg-blue-100 flex items-center justify-center">
                    <i data-lucide="folder-kanban" class="h-5 w-5 text-blue-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Đang hoạt động</p>
                    <p class="text-2xl font-bold text-green-600"><?= $projectStats['active_projects'] ?? 0 ?></p>
                </div>
                <div class="h-11 w-11 rounded-lg bg-green-100 flex items-center justify-center">
                    <i data-lucide="play-circle" class="h-5 w-5 text-green-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Hoàn thành</p>
                    <p class="text-2xl font-bold text-purple-600"><?= $projectStats['completed_projects'] ?? 0 ?></p>
                </div>
                <div class="h-11 w-11 rounded-lg bg-purple-100 flex items-center justify-center">
                    <i data-lucide="check-circle" class="h-5 w-5 text-purple-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Tạm dừng</p>
                    <p class="text-2xl font-bold text-orange-600"><?= $projectStats['on_hold_projects'] ?? 0 ?></p>
                </div>
                <div class="h-11 w-11 rounded-lg bg-orange-100 flex items-center justify-center">
                    <i data-lucide="pause-circle" class="h-5 w-5 text-orange-600"></i>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Filters -->
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div class="flex items-center gap-2 flex-wrap">
            <a href="?status=all" 
               class="px-4 py-2 rounded-lg text-sm font-medium <?= $statusFilter === 'all' ? 'bg-primary text-white' : 'bg-white text-gray-700 border border-gray-200 hover:bg-gray-50' ?>">
                Tất cả
            </a>
            <a href="?status=active" 
               class="px-4 py-2 rounded-lg text-sm font-medium <?= $statusFilter === 'active' ? 'bg-primary text-white' : 'bg-white text-gray-700 border border-gray-200 hover:bg-gray-50' ?>">
                Đang hoạt động
            </a>
            <a href="?status=planning" 
               class="px-4 py-2 rounded-lg text-sm font-medium <?= $statusFilter === 'planning' ? 'bg-primary text-white' : 'bg-white text-gray-700 border border-gray-200 hover:bg-gray-50' ?>">
                Lên kế hoạch
            </a>
            <a href="?status=on_hold" 
               class="px-4 py-2 rounded-lg text-sm font-medium <?= $statusFilter === 'on_hold' ? 'bg-primary text-white' : 'bg-white text-gray-700 border border-gray-200 hover:bg-gray-50' ?>">
                Tạm dừng
            </a>
            <a href="?status=completed" 
               class="px-4 py-2 rounded-lg text-sm font-medium <?= $statusFilter === 'completed' ? 'bg-primary text-white' : 'bg-white text-gray-700 border border-gray-200 hover:bg-gray-50' ?>">
                Hoàn thành
            </a>
        </div>
        
        <?php if (Permission::can($userRole, 'projects.create')): ?>
        <button onclick="openModal('create-project-modal')" 
                class="flex items-center gap-2 rounded-lg bg-primary px-4 py-2 text-sm font-medium text-white hover:bg-primary/90">
            <i data-lucide="plus" class="h-4 w-4"></i>
            Tạo dự án
        </button>
        <?php endif; ?>
    </div>
    
    <!-- Projects Grid -->
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
        <?php 
        $filteredProjects = $projects ?? [];
        if ($statusFilter !== 'all') {
            $filteredProjects = array_filter($projects ?? [], fn($p) => ($p['status'] ?? '') === $statusFilter);
        }
        ?>
        
        <?php if (!empty($filteredProjects)): ?>
            <?php foreach ($filteredProjects as $project): 
                $progress = $project['progress'] ?? 0;
                $overdueTasks = $project['overdue_tasks'] ?? 0;
                $progressColor = $progress >= 80 ? 'bg-green-500' : ($progress >= 50 ? 'bg-blue-500' : ($progress >= 25 ? 'bg-yellow-500' : 'bg-gray-400'));
            ?>
            <a href="/php/project-detail.php?id=<?= View::e($project['id']) ?>" 
               class="block rounded-xl border <?= $overdueTasks > 0 ? 'border-red-200' : 'border-gray-200' ?> bg-white p-5 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-start gap-3 mb-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg" 
                         style="background-color: <?= View::e($project['color'] ?? '#6366f1') ?>20">
                        <i data-lucide="folder" class="h-5 w-5" style="color: <?= View::e($project['color'] ?? '#6366f1') ?>"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="font-medium text-gray-900 truncate"><?= View::e($project['name']) ?></h3>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium 
                                <?php
                                $statusClasses = [
                                    'active' => 'bg-green-100 text-green-800',
                                    'planning' => 'bg-gray-100 text-gray-800',
                                    'on_hold' => 'bg-orange-100 text-orange-800',
                                    'completed' => 'bg-blue-100 text-blue-800',
                                ];
                                echo $statusClasses[$project['status'] ?? 'active'] ?? 'bg-gray-100 text-gray-800';
                                ?>">
                                <?= View::e($project['status'] ?? 'active') ?>
                            </span>
                            <?php if ($isManager && isset($project['my_role'])): ?>
                            <span class="text-xs text-gray-400"><?= ucfirst($project['my_role']) ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <p class="text-sm text-gray-500 line-clamp-2 mb-3"><?= View::e($project['description'] ?? 'Chưa có mô tả') ?></p>
                
                <div class="mb-3">
                    <div class="flex items-center justify-between text-xs text-gray-500 mb-1">
                        <span><?= $project['completed_tasks'] ?? 0 ?>/<?= $project['total_tasks'] ?? 0 ?> tasks</span>
                        <span class="font-medium"><?= $progress ?>%</span>
                    </div>
                    <div class="h-2 rounded-full bg-gray-100 overflow-hidden">
                        <div class="h-full rounded-full <?= $progressColor ?> transition-all" style="width: <?= $progress ?>%"></div>
                    </div>
                </div>
                
                <div class="flex items-center justify-between text-xs text-gray-500">
                    <span><?= $project['member_count'] ?? 0 ?> thành viên</span>
                    <?php if ($overdueTasks > 0): ?>
                    <span class="text-red-600 font-medium"><?= $overdueTasks ?> quá hạn</span>
                    <?php endif; ?>
                </div>
            </a>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-span-full text-center py-12">
                <i data-lucide="folder-open" class="h-16 w-16 mx-auto text-gray-300 mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Không có dự án nào</h3>
                <p class="text-gray-500 mb-4">Bắt đầu bằng cách tạo dự án đầu tiên của bạn</p>
                <?php if (Permission::can($userRole, 'projects.create')): ?>
                <button onclick="openModal('create-project-modal')" 
                        class="inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2 text-sm font-medium text-white hover:bg-primary/90">
                    <i data-lucide="plus" class="h-4 w-4"></i>
                    Tạo dự án
                </button>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Create Project Modal -->
<?php if (Permission::can($userRole, 'projects.create')): ?>
<div id="create-project-modal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-black/50" onclick="closeModal('create-project-modal')"></div>
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-lg" onclick="event.stopPropagation()">
            <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4">
                <h2 class="text-lg font-semibold text-gray-900">Tạo dự án mới</h2>
                <button onclick="closeModal('create-project-modal')" class="text-gray-400 hover:text-gray-600">
                    <i data-lucide="x" class="h-5 w-5"></i>
                </button>
            </div>
            <form action="/php/api/create-project.php" method="POST" class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tên dự án *</label>
                    <input type="text" name="name" required
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mô tả</label>
                    <textarea name="description" rows="3"
                              class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary"></textarea>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Độ ưu tiên</label>
                        <select name="priority" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                            <option value="low">Thấp</option>
                            <option value="medium" selected>Trung bình</option>
                            <option value="high">Cao</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Màu sắc</label>
                        <input type="color" name="color" value="#6366f1"
                               class="w-full h-10 rounded-lg border border-gray-300">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ngày bắt đầu</label>
                        <input type="date" name="startDate"
                               class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ngày kết thúc</label>
                        <input type="date" name="endDate"
                               class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                    </div>
                </div>
                <div class="flex justify-end gap-3 pt-4">
                    <button type="button" onclick="closeModal('create-project-modal')" 
                            class="px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-lg">Hủy</button>
                    <button type="submit" 
                            class="px-4 py-2 text-sm font-medium text-white bg-primary hover:bg-primary/90 rounded-lg">Tạo dự án</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<script>
// Handle create project form with AJAX for better UX
document.querySelector('#create-project-modal form')?.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const data = Object.fromEntries(formData.entries());
    
    fetch('/php/api/create-project.php', {
        method: 'POST',
        headers: { 
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(r => r.json())
    .then(result => {
        if (result.success) {
            window.location.href = '/php/project-detail.php?id=' + result.project_id;
        } else {
            alert(result.error || 'Có lỗi xảy ra');
        }
    })
    .catch(err => {
        alert('Lỗi kết nối: ' + err.message);
    });
});
</script>

<?php View::endSection(); ?>
