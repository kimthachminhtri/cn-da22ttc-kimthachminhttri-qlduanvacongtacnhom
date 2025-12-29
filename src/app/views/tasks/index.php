<?php
/**
 * Tasks List View
 */
use Core\View;
use Core\Session;
use Core\Permission;

// Đảm bảo session được start
Session::start();

// Lấy userRole từ session
$userRole = Session::get('user_role', 'guest');
$statusFilter = $_GET['status'] ?? 'all';
$priorityFilter = $_GET['priority'] ?? 'all';

// Use existing getPriorityName from includes/functions.php or define if not exists
if (!function_exists('getPriorityName')) {
    function getPriorityName(string $priority): string {
        $names = [
            'urgent' => 'Khẩn cấp',
            'high' => 'Cao',
            'medium' => 'Trung bình',
            'low' => 'Thấp'
        ];
        return $names[$priority] ?? $priority;
    }
}

View::section('content');
?>

<div class="space-y-6">
    <!-- Filters -->
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-2">
            <select onchange="window.location.href='?status='+this.value+'&priority=<?= $priorityFilter ?>'"
                    class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
                <option value="all" <?= $statusFilter === 'all' ? 'selected' : '' ?>>Tất cả trạng thái</option>
                <option value="todo" <?= $statusFilter === 'todo' ? 'selected' : '' ?>>Cần làm</option>
                <option value="in_progress" <?= $statusFilter === 'in_progress' ? 'selected' : '' ?>>Đang làm</option>
                <option value="done" <?= $statusFilter === 'done' ? 'selected' : '' ?>>Hoàn thành</option>
            </select>
            <select onchange="window.location.href='?status=<?= $statusFilter ?>&priority='+this.value"
                    class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
                <option value="all" <?= $priorityFilter === 'all' ? 'selected' : '' ?>>Tất cả độ ưu tiên</option>
                <option value="urgent" <?= $priorityFilter === 'urgent' ? 'selected' : '' ?>>Khẩn cấp</option>
                <option value="high" <?= $priorityFilter === 'high' ? 'selected' : '' ?>>Cao</option>
                <option value="medium" <?= $priorityFilter === 'medium' ? 'selected' : '' ?>>Trung bình</option>
                <option value="low" <?= $priorityFilter === 'low' ? 'selected' : '' ?>>Thấp</option>
            </select>
        </div>
        
        <?php if (Permission::can($userRole, 'tasks.create')): ?>
        <button onclick="openModal('create-task-modal')" 
                class="flex items-center gap-2 rounded-lg bg-primary px-4 py-2 text-sm font-medium text-white hover:bg-primary/90">
            <i data-lucide="plus" class="h-4 w-4"></i>
            Tạo công việc
        </button>
        <?php endif; ?>
    </div>
    
    <!-- Tasks List -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="divide-y divide-gray-100">
            <?php if (!empty($tasks)): ?>
                <?php foreach ($tasks as $task): ?>
                <div class="px-6 py-4 hover:bg-gray-50 transition-colors">
                    <div class="flex items-start gap-4">
                        <input type="checkbox" <?= ($task['status'] ?? '') === 'done' ? 'checked' : '' ?>
                               onchange="toggleTaskStatus('<?= View::e($task['id']) ?>')"
                               class="mt-1 h-4 w-4 rounded border-gray-300 text-primary cursor-pointer">
                        <div class="flex-1 min-w-0">
                            <a href="/php/task-detail.php?id=<?= View::e($task['id']) ?>" 
                               class="font-medium text-gray-900 hover:text-primary <?= ($task['status'] ?? '') === 'done' ? 'line-through text-gray-400' : '' ?>">
                                <?= View::e($task['title']) ?>
                            </a>
                            <div class="flex flex-wrap items-center gap-2 mt-2">
                                <?php
                                $priorityClasses = [
                                    'urgent' => 'bg-red-100 text-red-800',
                                    'high' => 'bg-orange-100 text-orange-800',
                                    'medium' => 'bg-blue-100 text-blue-800',
                                    'low' => 'bg-gray-100 text-gray-800',
                                ];
                                ?>
                                <span class="inline-flex items-center rounded px-2 py-0.5 text-xs font-medium <?= $priorityClasses[$task['priority'] ?? 'medium'] ?>">
                                    <?= View::e(getPriorityName($task['priority'] ?? 'medium')) ?>
                                </span>
                                <?php if (!empty($task['project_name'])): ?>
                                <span class="inline-flex items-center gap-1 text-xs text-gray-500">
                                    <span class="h-2 w-2 rounded-full" style="background-color: <?= View::e($task['project_color'] ?? '#6366f1') ?>"></span>
                                    <?= View::e($task['project_name']) ?>
                                </span>
                                <?php endif; ?>
                                <?php if (!empty($task['due_date'])): ?>
                                <span class="text-xs text-gray-500">
                                    <i data-lucide="calendar" class="h-3 w-3 inline"></i>
                                    <?= date('d/m/Y', strtotime($task['due_date'])) ?>
                                </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="px-6 py-12 text-center">
                    <i data-lucide="check-square" class="h-12 w-12 mx-auto text-gray-300 mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Không có công việc nào</h3>
                    <p class="text-gray-500">Tạo công việc mới để bắt đầu</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Create Task Modal -->
<?php if (Permission::can($userRole, 'tasks.create')): ?>
<div id="create-task-modal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-black/50" onclick="closeModal('create-task-modal')"></div>
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-lg" onclick="event.stopPropagation()">
            <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4">
                <h2 class="text-lg font-semibold text-gray-900">Tạo công việc mới</h2>
                <button onclick="closeModal('create-task-modal')" class="text-gray-400 hover:text-gray-600">
                    <i data-lucide="x" class="h-5 w-5"></i>
                </button>
            </div>
            <form action="/php/api/create-task.php" method="POST" class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tiêu đề *</label>
                    <input type="text" name="title" required
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mô tả</label>
                    <textarea name="description" rows="3"
                              class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary"></textarea>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Dự án</label>
                        <select name="projectId" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                            <option value="">-- Không có --</option>
                            <?php foreach ($projects ?? [] as $project): ?>
                            <option value="<?= View::e($project['id']) ?>"><?= View::e($project['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Độ ưu tiên</label>
                        <select name="priority" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                            <option value="low">Thấp</option>
                            <option value="medium" selected>Trung bình</option>
                            <option value="high">Cao</option>
                            <option value="urgent">Khẩn cấp</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ngày hết hạn</label>
                        <input type="date" name="dueDate"
                               class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Giao cho</label>
                        <select name="assigneeId" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                            <option value="">-- Chưa giao --</option>
                            <?php foreach ($users ?? [] as $user): ?>
                            <option value="<?= View::e($user['id']) ?>"><?= View::e($user['full_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="flex justify-end gap-3 pt-4">
                    <button type="button" onclick="closeModal('create-task-modal')" 
                            class="px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-lg">Hủy</button>
                    <button type="submit" 
                            class="px-4 py-2 text-sm font-medium text-white bg-primary hover:bg-primary/90 rounded-lg">Tạo công việc</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<script>
function toggleTaskStatus(taskId) {
    // Get current checkbox state
    const checkbox = event.target;
    const newStatus = checkbox.checked ? 'done' : 'todo';
    
    fetch('/php/api/update-task.php', {
        method: 'POST',
        headers: { 
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ task_id: taskId, status: newStatus })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            // Revert checkbox state
            checkbox.checked = !checkbox.checked;
            alert(data.error || 'Có lỗi xảy ra');
        }
    })
    .catch(err => {
        checkbox.checked = !checkbox.checked;
        alert('Lỗi kết nối: ' + err.message);
    });
}

// Handle create task form with AJAX
document.querySelector('#create-task-modal form')?.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const data = Object.fromEntries(formData.entries());
    
    fetch('/php/api/create-task.php', {
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
            window.location.href = '/php/task-detail.php?id=' + result.task_id;
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
