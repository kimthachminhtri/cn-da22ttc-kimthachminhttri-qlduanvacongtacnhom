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
            <?php 
            View::partial('components/empty-state', [
                'icon' => 'folder-open',
                'title' => 'Chưa có dự án nào',
                'description' => 'Dự án giúp bạn tổ chức công việc theo nhóm. Tạo dự án đầu tiên để bắt đầu quản lý công việc hiệu quả hơn.',
                'action' => Permission::can($userRole, 'projects.create') ? [
                    'label' => 'Tạo dự án đầu tiên',
                    'onclick' => "openModal('create-project-modal')",
                    'icon' => 'plus'
                ] : null,
                'tips' => [
                    'Mỗi dự án có thể có nhiều thành viên và công việc',
                    'Sử dụng màu sắc để phân biệt các dự án',
                    'Theo dõi tiến độ qua thanh progress'
                ]
            ]);
            ?>
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
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Tên dự án <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" required aria-required="true"
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
// Handle create project form with enhanced UX (loading states + inline errors)
document.querySelector('#create-project-modal form')?.addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const form = this;
    const submitBtn = form.querySelector('[type="submit"]');
    
    // Clear previous errors
    form.querySelectorAll('.form-field-error, .form-general-error').forEach(el => el.remove());
    form.querySelectorAll('.border-red-500').forEach(el => {
        el.classList.remove('border-red-500');
        el.classList.add('border-gray-300');
    });
    
    // Show loading state
    if (window.LoadingState) {
        LoadingState.showButton(submitBtn, 'Đang tạo...');
    } else {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<svg class="animate-spin h-4 w-4 mr-2 inline" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Đang tạo...';
    }
    
    try {
        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());
        
        const response = await fetch('/php/api/create-project.php', {
            method: 'POST',
            headers: { 
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.success) {
            if (typeof showToast === 'function') {
                showToast('Tạo dự án thành công!', 'success');
            }
            window.location.href = '/php/project-detail.php?id=' + result.project_id;
        } else {
            // Show inline error
            showFormError(form, result.error || 'Có lỗi xảy ra', result.field);
        }
    } catch (err) {
        showFormError(form, 'Lỗi kết nối. Vui lòng kiểm tra mạng và thử lại.');
    } finally {
        // Hide loading state
        if (window.LoadingState) {
            LoadingState.hideButton(submitBtn);
        } else {
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Tạo dự án';
        }
    }
});

// Helper function to show inline form errors
function showFormError(form, message, fieldName = null) {
    if (fieldName) {
        // Field-specific error
        const field = form.querySelector(`[name="${fieldName}"]`);
        if (field) {
            field.classList.add('border-red-500');
            field.classList.remove('border-gray-300');
            
            const errorEl = document.createElement('div');
            errorEl.className = 'form-field-error mt-1 flex items-center gap-1 text-sm text-red-600';
            errorEl.innerHTML = `
                <svg class="h-4 w-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                <span>${message}</span>
            `;
            field.parentElement.appendChild(errorEl);
            field.focus();
            return;
        }
    }
    
    // General error at top of form
    const errorEl = document.createElement('div');
    errorEl.className = 'form-general-error mb-4 p-3 bg-red-50 border border-red-200 rounded-lg flex items-start gap-2 text-sm';
    errorEl.innerHTML = `
        <svg class="h-5 w-5 text-red-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
        </svg>
        <span class="text-red-700">${message}</span>
    `;
    form.insertBefore(errorEl, form.firstChild);
    errorEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
}
</script>

<?php View::endSection(); ?>
