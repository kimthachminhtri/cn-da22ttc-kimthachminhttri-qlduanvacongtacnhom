<?php
/**
 * Project Detail View
 */
use Core\View;
use Core\Session;
use Core\Permission;

$userRole = Session::get('user_role', 'guest');
$userId = Session::get('user_id');
$activeTab = $_GET['tab'] ?? 'overview';

// Check user's role in project
$canEditProject = false;
$canDeleteProject = false;
$currentUserProjectRole = null;

foreach ($project['members'] ?? [] as $m) {
    if ($m['id'] === $userId) {
        $currentUserProjectRole = $m['project_role'] ?? 'member';
        break;
    }
}

if ($currentUserProjectRole === 'owner' || Permission::isAdmin($userRole)) {
    $canEditProject = true;
    $canDeleteProject = true;
} elseif ($currentUserProjectRole === 'manager' || Permission::isManager($userRole)) {
    $canEditProject = true;
}

View::section('content');
?>

<div class="space-y-6">
    <!-- Project Header -->
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-4">
                <a href="/php/projects.php" class="text-gray-400 hover:text-gray-600">
                    <i data-lucide="arrow-left" class="h-5 w-5"></i>
                </a>
                <div class="h-12 w-12 rounded-xl flex items-center justify-center" 
                     style="background-color: <?= View::e($project['color'] ?? '#6366f1') ?>20">
                    <i data-lucide="folder" class="h-6 w-6" style="color: <?= View::e($project['color'] ?? '#6366f1') ?>"></i>
                </div>
                <div>
                    <h1 class="text-xl font-semibold text-gray-900"><?= View::e($project['name'] ?? '') ?></h1>
                    <div class="flex items-center gap-3 text-sm text-gray-500">
                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium bg-green-100 text-green-800">
                            <?= View::e($project['status'] ?? 'active') ?>
                        </span>
                        <span><?= count($tasks ?? []) ?> công việc</span>
                        <span><?= $project['progress'] ?? 0 ?>% hoàn thành</span>
                    </div>
                </div>
            </div>
            
            <?php if ($canEditProject): ?>
            <div class="flex items-center gap-2">
                <button onclick="openModal('edit-project-modal')" 
                        class="flex items-center gap-2 px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50">
                    <i data-lucide="edit" class="h-4 w-4"></i>
                    Sửa
                </button>
                <?php if ($canDeleteProject): ?>
                <button onclick="confirmDeleteProject()" 
                        class="flex items-center gap-2 px-3 py-2 text-sm font-medium text-red-600 bg-white border border-red-200 rounded-lg hover:bg-red-50">
                    <i data-lucide="trash-2" class="h-4 w-4"></i>
                    Xóa
                </button>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- Tabs -->
        <nav class="flex gap-6 border-t border-gray-200 pt-4">
            <a href="?id=<?= View::e($project['id']) ?>&tab=overview" 
               class="pb-3 text-sm font-medium border-b-2 <?= $activeTab === 'overview' ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-700' ?>">
                Tổng quan
            </a>
            <a href="?id=<?= View::e($project['id']) ?>&tab=tasks" 
               class="pb-3 text-sm font-medium border-b-2 <?= $activeTab === 'tasks' ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-700' ?>">
                Kanban Board
            </a>
            <a href="?id=<?= View::e($project['id']) ?>&tab=documents" 
               class="pb-3 text-sm font-medium border-b-2 <?= $activeTab === 'documents' ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-700' ?>">
                Tài liệu
            </a>
            <a href="?id=<?= View::e($project['id']) ?>&tab=members" 
               class="pb-3 text-sm font-medium border-b-2 <?= $activeTab === 'members' ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-700' ?>">
                Thành viên
            </a>
        </nav>
    </div>

    <?php if ($activeTab === 'overview'): ?>
    <!-- Overview Tab -->
    <div class="grid gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2 space-y-6">
            <!-- Description -->
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h3 class="font-semibold text-gray-900 mb-3">Mô tả</h3>
                <p class="text-gray-600"><?= View::e($project['description'] ?? 'Chưa có mô tả') ?></p>
            </div>
            
            <!-- Progress -->
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h3 class="font-semibold text-gray-900 mb-4">Tiến độ công việc</h3>
                <?php
                $taskStats = $project['task_stats'] ?? ['backlog' => 0, 'todo' => 0, 'in_progress' => 0, 'in_review' => 0, 'done' => 0, 'total' => 0];
                $total = $taskStats['total'] ?: 1;
                $statusLabels = [
                    'done' => ['Hoàn thành', 'bg-green-500'],
                    'in_review' => ['Đang xem xét', 'bg-purple-500'],
                    'in_progress' => ['Đang làm', 'bg-yellow-500'],
                    'todo' => ['Cần làm', 'bg-blue-500'],
                    'backlog' => ['Chờ xử lý', 'bg-gray-400'],
                ];
                ?>
                <div class="space-y-3">
                    <?php foreach ($statusLabels as $status => $info): ?>
                    <div>
                        <div class="flex items-center justify-between text-sm mb-1">
                            <span class="text-gray-600"><?= $info[0] ?></span>
                            <span class="font-medium"><?= $taskStats[$status] ?? 0 ?> (<?= round(($taskStats[$status] ?? 0) / $total * 100) ?>%)</span>
                        </div>
                        <div class="h-2 rounded-full bg-gray-100 overflow-hidden">
                            <div class="h-full rounded-full <?= $info[1] ?>" style="width: <?= round(($taskStats[$status] ?? 0) / $total * 100) ?>%"></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        
        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Info -->
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h3 class="font-semibold text-gray-900 mb-4">Thông tin</h3>
                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Ngày bắt đầu</dt>
                        <dd class="font-medium"><?= $project['start_date'] ? date('d/m/Y', strtotime($project['start_date'])) : '-' ?></dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Ngày kết thúc</dt>
                        <dd class="font-medium"><?= $project['end_date'] ? date('d/m/Y', strtotime($project['end_date'])) : '-' ?></dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Độ ưu tiên</dt>
                        <dd><span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium bg-blue-100 text-blue-800"><?= View::e(getPriorityName($project['priority'] ?? 'medium')) ?></span></dd>
                    </div>
                </dl>
            </div>
            
            <!-- Members -->
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h3 class="font-semibold text-gray-900 mb-4">Thành viên (<?= count($project['members'] ?? []) ?>)</h3>
                <div class="space-y-3">
                    <?php foreach (array_slice($project['members'] ?? [], 0, 5) as $member): ?>
                    <div class="flex items-center gap-3">
                        <div class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center">
                            <span class="text-xs font-medium"><?= strtoupper(substr($member['full_name'] ?? 'U', 0, 1)) ?></span>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900"><?= View::e($member['full_name'] ?? '') ?></p>
                            <p class="text-xs text-gray-500"><?= View::e($member['project_role'] ?? 'member') ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <?php elseif ($activeTab === 'tasks'): ?>
    <!-- Kanban Board -->
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-gray-900">Kanban Board</h3>
        <?php if (Permission::can($userRole, 'tasks.create')): ?>
        <button onclick="openModal('create-task-modal')" 
                class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-primary rounded-lg hover:bg-primary/90">
            <i data-lucide="plus" class="h-4 w-4"></i>
            Tạo công việc
        </button>
        <?php endif; ?>
    </div>
    <div class="flex gap-4 overflow-x-auto pb-4" id="kanban-board">
        <?php 
        $columns = ['backlog' => 'Chờ xử lý', 'todo' => 'Cần làm', 'in_progress' => 'Đang làm', 'in_review' => 'Đang xem xét', 'done' => 'Hoàn thành'];
        $tasksByStatus = [];
        foreach ($columns as $status => $label) {
            $tasksByStatus[$status] = array_filter($tasks ?? [], fn($t) => ($t['status'] ?? '') === $status);
        }
        ?>
        <?php foreach ($columns as $status => $title): ?>
        <div class="flex-shrink-0 w-72 kanban-column" data-status="<?= $status ?>">
            <div class="bg-gray-100 rounded-xl p-4 min-h-[200px]">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-semibold text-gray-900"><?= $title ?></h3>
                    <span class="task-count bg-gray-200 text-gray-600 text-xs font-medium px-2 py-0.5 rounded-full">
                        <?= count($tasksByStatus[$status]) ?>
                    </span>
                </div>
                <div class="kanban-tasks space-y-3 min-h-[100px]" data-status="<?= $status ?>">
                    <?php foreach ($tasksByStatus[$status] as $task): ?>
                    <div class="kanban-task bg-white rounded-lg p-4 shadow-sm border border-gray-200 hover:shadow-md transition-shadow cursor-move"
                         draggable="true" data-task-id="<?= View::e($task['id']) ?>">
                        <a href="/php/task-detail.php?id=<?= View::e($task['id']) ?>" class="block">
                            <h4 class="font-medium text-gray-900 mb-2"><?= View::e($task['title']) ?></h4>
                            <div class="flex items-center justify-between text-xs text-gray-500">
                                <?php
                                $priorityColors = [
                                    'low' => 'bg-gray-100 text-gray-800',
                                    'medium' => 'bg-blue-100 text-blue-800',
                                    'high' => 'bg-orange-100 text-orange-800',
                                    'urgent' => 'bg-red-100 text-red-800',
                                ];
                                ?>
                                <span class="inline-flex items-center rounded px-1.5 py-0.5 font-medium <?= $priorityColors[$task['priority'] ?? 'medium'] ?>">
                                    <?= View::e(getPriorityName($task['priority'] ?? 'medium')) ?>
                                </span>
                                <?php if (!empty($task['due_date'])): ?>
                                <span><?= date('d/m', strtotime($task['due_date'])) ?></span>
                                <?php endif; ?>
                            </div>
                        </a>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <?php elseif ($activeTab === 'documents'): ?>
    <!-- Documents Tab -->
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-6">
            <p class="text-sm text-gray-500"><?= count($documents ?? []) ?> tài liệu</p>
            <?php if (Permission::can($userRole, 'documents.create')): ?>
            <div class="flex items-center gap-2">
                <button onclick="openModal('project-folder-modal')" 
                        class="flex items-center gap-2 px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50">
                    <i data-lucide="folder-plus" class="h-4 w-4"></i>
                    Thư mục mới
                </button>
                <button onclick="openModal('project-upload-modal')" 
                        class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-primary rounded-lg hover:bg-primary/90">
                    <i data-lucide="upload" class="h-4 w-4"></i>
                    Tải lên
                </button>
            </div>
            <?php endif; ?>
        </div>
        
        <?php if (!empty($documents)): ?>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            <?php foreach ($documents as $doc): ?>
            <?php
            $isFolder = ($doc['type'] ?? '') === 'folder';
            $ext = strtolower(pathinfo($doc['name'] ?? '', PATHINFO_EXTENSION));
            $fileIcons = ['pdf' => 'file-text', 'doc' => 'file-text', 'docx' => 'file-text', 'xls' => 'file-spreadsheet', 'xlsx' => 'file-spreadsheet'];
            $fileColors = ['pdf' => 'text-red-500 bg-red-100', 'doc' => 'text-blue-500 bg-blue-100', 'docx' => 'text-blue-500 bg-blue-100', 'xls' => 'text-green-500 bg-green-100', 'xlsx' => 'text-green-500 bg-green-100'];
            
            if ($isFolder) {
                $icon = 'folder';
                $colorClass = 'text-amber-500 bg-amber-100';
            } else {
                $icon = $fileIcons[$ext] ?? 'file';
                $colorClass = $fileColors[$ext] ?? 'text-gray-500 bg-gray-100';
            }
            $canDelete = Permission::can($userRole, 'documents.delete') || ($doc['uploaded_by'] ?? '') === $userId;
            ?>
            <div class="group relative bg-gray-50 border border-gray-200 rounded-xl p-4 hover:shadow-md transition-all">
                <?php if ($canDelete): ?>
                <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity flex gap-1">
                    <?php if (!$isFolder && !empty($doc['file_path'])): ?>
                    <a href="/php/<?= View::e($doc['file_path']) ?>" download
                       class="p-1.5 rounded-full hover:bg-blue-100 text-gray-400 hover:text-blue-500">
                        <i data-lucide="download" class="h-4 w-4"></i>
                    </a>
                    <?php endif; ?>
                    <button onclick="deleteProjectDoc('<?= View::e($doc['id']) ?>')" 
                            class="p-1.5 rounded-full hover:bg-red-100 text-gray-400 hover:text-red-500">
                        <i data-lucide="trash-2" class="h-4 w-4"></i>
                    </button>
                </div>
                <?php endif; ?>
                
                <div class="flex items-center justify-center h-12 mb-3">
                    <div class="p-2 rounded-lg <?= $colorClass ?>">
                        <i data-lucide="<?= $icon ?>" class="h-6 w-6"></i>
                    </div>
                </div>
                
                <h3 class="font-medium text-sm text-gray-900 truncate text-center" title="<?= View::e($doc['name'] ?? '') ?>">
                    <?= View::e($doc['name'] ?? '') ?>
                </h3>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="text-center py-12">
            <i data-lucide="folder-open" class="h-12 w-12 mx-auto text-gray-300 mb-4"></i>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Chưa có tài liệu</h3>
            <p class="text-gray-500 mb-4">Tải lên tài liệu đầu tiên cho dự án này</p>
            <?php if (Permission::can($userRole, 'documents.create')): ?>
            <button onclick="openModal('project-upload-modal')" class="px-4 py-2 text-sm font-medium text-white bg-primary rounded-lg hover:bg-primary/90">
                <i data-lucide="upload" class="h-4 w-4 inline mr-1"></i>
                Tải lên ngay
            </button>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>

    <?php elseif ($activeTab === 'members'): ?>
    <!-- Members Tab -->
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-6">
            <p class="text-sm text-gray-500"><?= count($project['members'] ?? []) ?> thành viên</p>
            <?php if ($canEditProject): ?>
            <button onclick="openModal('add-member-modal')" 
                    class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-primary rounded-lg hover:bg-primary/90">
                <i data-lucide="user-plus" class="h-4 w-4"></i>
                Mời thành viên
            </button>
            <?php endif; ?>
        </div>
        
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <?php foreach ($project['members'] ?? [] as $member): ?>
            <div class="flex items-center gap-4 p-4 border border-gray-200 rounded-lg group">
                <div class="h-12 w-12 rounded-full bg-gray-200 flex items-center justify-center">
                    <span class="text-lg font-medium"><?= strtoupper(substr($member['full_name'] ?? 'U', 0, 1)) ?></span>
                </div>
                <div class="flex-1">
                    <p class="font-medium text-gray-900"><?= View::e($member['full_name'] ?? '') ?></p>
                    <p class="text-sm text-gray-500"><?= View::e($member['email'] ?? '') ?></p>
                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium bg-blue-100 text-blue-800 mt-1">
                        <?= View::e($member['project_role'] ?? 'member') ?>
                    </span>
                </div>
                <?php if ($canEditProject && ($member['project_role'] ?? '') !== 'owner'): ?>
                <div class="opacity-0 group-hover:opacity-100 transition-opacity flex gap-1">
                    <?php if ($currentUserProjectRole === 'owner'): ?>
                    <button onclick="changeRole('<?= View::e($member['id']) ?>', '<?= View::e($member['full_name']) ?>', '<?= View::e($member['project_role'] ?? 'member') ?>')" 
                            class="p-2 text-gray-500 hover:bg-gray-50 rounded-lg" title="Thay đổi vai trò">
                        <i data-lucide="settings" class="h-4 w-4"></i>
                    </button>
                    <button onclick="transferOwnership('<?= View::e($member['id']) ?>', '<?= View::e($member['full_name']) ?>')" 
                            class="p-2 text-blue-500 hover:bg-blue-50 rounded-lg" title="Chuyển quyền sở hữu">
                        <i data-lucide="crown" class="h-4 w-4"></i>
                    </button>
                    <?php endif; ?>
                    <button onclick="removeMember('<?= View::e($member['id']) ?>', '<?= View::e($member['full_name']) ?>')" 
                            class="p-2 text-red-500 hover:bg-red-50 rounded-lg" title="Xóa khỏi dự án">
                        <i data-lucide="user-minus" class="h-4 w-4"></i>
                    </button>
                </div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
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
            <form id="create-task-form" class="p-6 space-y-4">
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
                        <label class="block text-sm font-medium text-gray-700 mb-1">Trạng thái</label>
                        <select name="status" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                            <option value="backlog">Backlog</option>
                            <option value="todo" selected>Cần làm</option>
                            <option value="in_progress">Đang làm</option>
                            <option value="in_review">Review</option>
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
                        <input type="date" name="due_date"
                               class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Giao cho</label>
                        <select name="assignee_id" id="task-assignee-select" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                            <option value="">-- Chưa giao --</option>
                            <?php foreach ($project['members'] ?? [] as $member): ?>
                            <option value="<?= View::e($member['id']) ?>"><?= View::e($member['full_name']) ?></option>
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

<!-- Edit Project Modal -->
<?php if ($canEditProject): ?>
<div id="edit-project-modal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-black/50" onclick="closeModal('edit-project-modal')"></div>
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-lg" onclick="event.stopPropagation()">
            <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4">
                <h2 class="text-lg font-semibold text-gray-900">Chỉnh sửa dự án</h2>
                <button onclick="closeModal('edit-project-modal')" class="text-gray-400 hover:text-gray-600">
                    <i data-lucide="x" class="h-5 w-5"></i>
                </button>
            </div>
            <form id="edit-project-form" class="p-6 space-y-4">
                <input type="hidden" name="project_id" value="<?= View::e($project['id']) ?>">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tên dự án *</label>
                    <input type="text" name="name" required value="<?= View::e($project['name'] ?? '') ?>"
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mô tả</label>
                    <textarea name="description" rows="3"
                              class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary"><?= View::e($project['description'] ?? '') ?></textarea>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Trạng thái</label>
                        <select name="status" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                            <option value="planning" <?= ($project['status'] ?? '') === 'planning' ? 'selected' : '' ?>>Lên kế hoạch</option>
                            <option value="active" <?= ($project['status'] ?? '') === 'active' ? 'selected' : '' ?>>Đang hoạt động</option>
                            <option value="on_hold" <?= ($project['status'] ?? '') === 'on_hold' ? 'selected' : '' ?>>Tạm dừng</option>
                            <option value="completed" <?= ($project['status'] ?? '') === 'completed' ? 'selected' : '' ?>>Hoàn thành</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Độ ưu tiên</label>
                        <select name="priority" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                            <option value="low" <?= ($project['priority'] ?? '') === 'low' ? 'selected' : '' ?>>Thấp</option>
                            <option value="medium" <?= ($project['priority'] ?? '') === 'medium' ? 'selected' : '' ?>>Trung bình</option>
                            <option value="high" <?= ($project['priority'] ?? '') === 'high' ? 'selected' : '' ?>>Cao</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Màu sắc</label>
                        <input type="color" name="color" value="<?= View::e($project['color'] ?? '#6366f1') ?>"
                               class="w-full h-10 rounded-lg border border-gray-300">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ngày bắt đầu</label>
                        <input type="date" name="start_date" value="<?= View::e($project['start_date'] ?? '') ?>"
                               class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ngày kết thúc</label>
                        <input type="date" name="end_date" value="<?= View::e($project['end_date'] ?? '') ?>"
                               class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                    </div>
                </div>
                <div class="flex justify-end gap-3 pt-4">
                    <button type="button" onclick="closeModal('edit-project-modal')" 
                            class="px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-lg">Hủy</button>
                    <button type="submit" 
                            class="px-4 py-2 text-sm font-medium text-white bg-primary hover:bg-primary/90 rounded-lg">Lưu thay đổi</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Add Member Modal -->
<?php if ($canEditProject): ?>
<div id="add-member-modal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-black/50" onclick="closeModal('add-member-modal')"></div>
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-md" onclick="event.stopPropagation()">
            <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4">
                <h2 class="text-lg font-semibold text-gray-900">Mời thành viên</h2>
                <button onclick="closeModal('add-member-modal')" class="text-gray-400 hover:text-gray-600">
                    <i data-lucide="x" class="h-5 w-5"></i>
                </button>
            </div>
            <form id="add-member-form" class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Chọn thành viên *</label>
                    <select name="user_id" required id="member-select"
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary">
                        <option value="">-- Chọn thành viên --</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Vai trò</label>
                    <select name="role" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                        <option value="member">Thành viên</option>
                        <option value="manager">Quản lý</option>
                        <option value="viewer">Xem</option>
                    </select>
                </div>
                <div class="flex justify-end gap-3 pt-4">
                    <button type="button" onclick="closeModal('add-member-modal')" 
                            class="px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-lg">Hủy</button>
                    <button type="submit" 
                            class="px-4 py-2 text-sm font-medium text-white bg-primary hover:bg-primary/90 rounded-lg">Thêm</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Project Folder Modal -->
<div id="project-folder-modal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-black/50" onclick="closeModal('project-folder-modal')"></div>
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-md" onclick="event.stopPropagation()">
            <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4">
                <h2 class="text-lg font-semibold text-gray-900">Tạo thư mục mới</h2>
                <button onclick="closeModal('project-folder-modal')" class="text-gray-400 hover:text-gray-600">
                    <i data-lucide="x" class="h-5 w-5"></i>
                </button>
            </div>
            <form action="/php/api/create-folder.php" method="POST" class="p-6 space-y-4">
                <input type="hidden" name="project_id" value="<?= View::e($project['id']) ?>">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tên thư mục *</label>
                    <input type="text" name="name" required placeholder="Nhập tên thư mục..."
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary">
                </div>
                <div class="flex justify-end gap-3 pt-4">
                    <button type="button" onclick="closeModal('project-folder-modal')" 
                            class="px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-lg">Hủy</button>
                    <button type="submit" 
                            class="px-4 py-2 text-sm font-medium text-white bg-primary hover:bg-primary/90 rounded-lg">Tạo thư mục</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Project Upload Modal -->
<div id="project-upload-modal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-black/50" onclick="closeModal('project-upload-modal')"></div>
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-md" onclick="event.stopPropagation()">
            <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4">
                <h2 class="text-lg font-semibold text-gray-900">Tải lên tài liệu</h2>
                <button onclick="closeModal('project-upload-modal')" class="text-gray-400 hover:text-gray-600">
                    <i data-lucide="x" class="h-5 w-5"></i>
                </button>
            </div>
            <form action="/php/api/upload-document.php" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
                <input type="hidden" name="project_id" value="<?= View::e($project['id']) ?>">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Chọn file *</label>
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-primary transition-colors">
                        <input type="file" name="files[]" multiple required id="project-file-input" class="hidden"
                               accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.jpg,.jpeg,.png,.gif,.zip,.txt">
                        <label for="project-file-input" class="cursor-pointer">
                            <i data-lucide="upload-cloud" class="h-10 w-10 mx-auto text-gray-400 mb-2"></i>
                            <p class="text-sm text-gray-600">Click để chọn file</p>
                            <p class="text-xs text-gray-400 mt-1">PDF, Word, Excel, Ảnh (tối đa 50MB)</p>
                        </label>
                    </div>
                    <div id="project-file-list" class="mt-2 space-y-1 text-sm"></div>
                </div>
                <div class="flex justify-end gap-3 pt-4">
                    <button type="button" onclick="closeModal('project-upload-modal')" 
                            class="px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-lg">Hủy</button>
                    <button type="submit" 
                            class="px-4 py-2 text-sm font-medium text-white bg-primary hover:bg-primary/90 rounded-lg">Tải lên</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
const projectId = '<?= View::e($project['id'] ?? '') ?>';
const existingMemberIds = <?= json_encode(array_column($project['members'] ?? [], 'id')) ?>;

function confirmDeleteProject() {
    if (!confirm('Bạn có chắc muốn xóa dự án này? Tất cả công việc và tài liệu liên quan sẽ bị xóa.')) return;
    
    fetch('/php/api/update-project.php?project_id=' + projectId, { 
        method: 'DELETE',
        headers: { 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            window.location.href = '/php/projects.php';
        } else {
            alert(data.error || 'Có lỗi xảy ra');
        }
    })
    .catch(err => alert('Lỗi kết nối: ' + err.message));
}

// Edit project form
document.getElementById('edit-project-form')?.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const data = Object.fromEntries(formData.entries());
    
    fetch('/php/api/update-project.php', {
        method: 'POST',
        headers: { 
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.error || 'Có lỗi xảy ra');
        }
    })
    .catch(err => alert('Lỗi kết nối: ' + err.message));
});

// Add member form
document.getElementById('add-member-form')?.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const data = {
        project_id: projectId,
        user_id: formData.get('user_id'),
        role: formData.get('role')
    };
    
    fetch('/php/api/project-members.php', {
        method: 'POST',
        headers: { 
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.error || 'Có lỗi xảy ra');
        }
    })
    .catch(err => alert('Lỗi kết nối: ' + err.message));
});

// Create task form
document.getElementById('create-task-form')?.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const data = {
        project_id: projectId,
        title: formData.get('title'),
        description: formData.get('description'),
        status: formData.get('status'),
        priority: formData.get('priority'),
        due_date: formData.get('due_date'),
        assignee_id: formData.get('assignee_id')
    };
    
    fetch('/php/api/create-task.php', {
        method: 'POST',
        headers: { 
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.error || 'Có lỗi xảy ra');
        }
    })
    .catch(err => alert('Lỗi kết nối: ' + err.message));
});

// Change member role
function changeRole(userId, userName, currentRole) {
    const roles = {
        'manager': 'Quản lý',
        'member': 'Thành viên',
        'viewer': 'Người xem'
    };
    
    const newRole = prompt(`Chọn vai trò mới cho ${userName}:\n- manager: Quản lý\n- member: Thành viên\n- viewer: Người xem\n\nVai trò hiện tại: ${currentRole}`, currentRole);
    
    if (!newRole || newRole === currentRole) return;
    if (!['manager', 'member', 'viewer'].includes(newRole)) {
        alert('Vai trò không hợp lệ. Vui lòng chọn: manager, member, hoặc viewer');
        return;
    }
    
    fetch('/php/api/project-members.php', {
        method: 'PUT',
        headers: { 
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            project_id: projectId,
            user_id: userId,
            role: newRole
        })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.error || 'Có lỗi xảy ra');
        }
    })
    .catch(err => alert('Lỗi kết nối: ' + err.message));
}

// Transfer ownership
function transferOwnership(userId, userName) {
    if (!confirm(`Bạn có chắc muốn chuyển quyền sở hữu dự án cho ${userName}? Bạn sẽ trở thành quản lý.`)) return;
    
    fetch('/php/api/transfer-ownership.php', {
        method: 'POST',
        headers: { 
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            project_id: projectId,
            new_owner_id: userId
        })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            alert(data.message || 'Đã chuyển quyền sở hữu thành công');
            location.reload();
        } else {
            alert(data.error || 'Có lỗi xảy ra');
        }
    })
    .catch(err => alert('Lỗi kết nối: ' + err.message));
}

// Remove member from project
function removeMember(userId, userName) {
    if (!confirm(`Bạn có chắc muốn xóa ${userName} khỏi dự án?`)) return;
    
    fetch(`/php/api/project-members.php?project_id=${projectId}&user_id=${userId}`, {
        method: 'DELETE',
        headers: { 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.error || 'Có lỗi xảy ra');
        }
    })
    .catch(err => alert('Lỗi kết nối: ' + err.message));
}

// Load available users for member select
function loadAvailableUsers() {
    fetch('/php/api/search.php?type=users')
        .then(r => r.json())
        .then(data => {
            if (data.success && data.data) {
                const select = document.getElementById('member-select');
                if (!select) return;
                
                data.data.forEach(user => {
                    if (!existingMemberIds.includes(user.id)) {
                        const option = document.createElement('option');
                        option.value = user.id;
                        option.textContent = user.full_name + ' (' + user.email + ')';
                        select.appendChild(option);
                    }
                });
            }
        })
        .catch(err => console.error('Error loading users:', err));
}

// Load users when page loads
document.addEventListener('DOMContentLoaded', loadAvailableUsers);

// Delete project document
function deleteProjectDoc(docId) {
    if (!confirm('Bạn có chắc muốn xóa tài liệu này?')) return;
    
    fetch('/php/api/delete-document.php?id=' + docId, { 
        method: 'DELETE',
        headers: { 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.error || 'Có lỗi xảy ra');
        }
    })
    .catch(err => alert('Lỗi kết nối: ' + err.message));
}

// Project file input preview
document.getElementById('project-file-input')?.addEventListener('change', function() {
    const fileList = document.getElementById('project-file-list');
    fileList.innerHTML = '';
    
    for (const file of this.files) {
        const div = document.createElement('div');
        div.className = 'text-gray-600';
        div.textContent = file.name;
        fileList.appendChild(div);
    }
});

// ============================================
// Kanban Drag & Drop
// ============================================
let draggedTask = null;

document.querySelectorAll('.kanban-task').forEach(task => {
    task.addEventListener('dragstart', handleDragStart);
    task.addEventListener('dragend', handleDragEnd);
});

document.querySelectorAll('.kanban-tasks').forEach(column => {
    column.addEventListener('dragover', handleDragOver);
    column.addEventListener('dragenter', handleDragEnter);
    column.addEventListener('dragleave', handleDragLeave);
    column.addEventListener('drop', handleDrop);
});

function handleDragStart(e) {
    draggedTask = this;
    this.classList.add('opacity-50', 'scale-95');
    e.dataTransfer.effectAllowed = 'move';
    e.dataTransfer.setData('text/plain', this.dataset.taskId);
}

function handleDragEnd(e) {
    this.classList.remove('opacity-50', 'scale-95');
    document.querySelectorAll('.kanban-tasks').forEach(col => {
        col.classList.remove('bg-blue-50', 'border-2', 'border-dashed', 'border-blue-300');
    });
    draggedTask = null;
}

function handleDragOver(e) {
    e.preventDefault();
    e.dataTransfer.dropEffect = 'move';
}

function handleDragEnter(e) {
    e.preventDefault();
    this.classList.add('bg-blue-50', 'border-2', 'border-dashed', 'border-blue-300');
}

function handleDragLeave(e) {
    // Only remove highlight if leaving the column entirely
    if (!this.contains(e.relatedTarget)) {
        this.classList.remove('bg-blue-50', 'border-2', 'border-dashed', 'border-blue-300');
    }
}

function handleDrop(e) {
    e.preventDefault();
    this.classList.remove('bg-blue-50', 'border-2', 'border-dashed', 'border-blue-300');
    
    if (!draggedTask) return;
    
    const taskId = e.dataTransfer.getData('text/plain');
    const newStatus = this.dataset.status;
    const oldStatus = draggedTask.closest('.kanban-tasks').dataset.status;
    
    if (newStatus === oldStatus) return;
    
    // Move task visually
    this.appendChild(draggedTask);
    
    // Update task count
    updateTaskCounts();
    
    // Update task status via API
    updateTaskStatus(taskId, newStatus);
}

function updateTaskCounts() {
    document.querySelectorAll('.kanban-column').forEach(column => {
        const count = column.querySelectorAll('.kanban-task').length;
        const countEl = column.querySelector('.task-count');
        if (countEl) countEl.textContent = count;
    });
}

function updateTaskStatus(taskId, newStatus) {
    fetch('/php/api/update-task.php', {
        method: 'POST',
        headers: { 
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            task_id: taskId,
            status: newStatus
        })
    })
    .then(r => r.json())
    .then(data => {
        if (!data.success) {
            alert(data.error || 'Có lỗi xảy ra khi cập nhật task');
            location.reload(); // Reload to restore original state
        }
    })
    .catch(err => {
        console.error('Error updating task:', err);
        location.reload();
    });
}
</script>

<?php View::endSection(); ?>
