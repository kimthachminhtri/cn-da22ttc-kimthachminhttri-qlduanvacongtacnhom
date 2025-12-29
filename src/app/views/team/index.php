<?php
/**
 * Team View - Enhanced for Manager
 */
use Core\View;
use Core\Session;
use Core\Permission;

$userRole = Session::get('user_role', 'guest');
$isManager = $isManager ?? false;
$teamStats = $teamStats ?? [];
$statusFilter = $statusFilter ?? 'active';

View::section('content');
?>

<div class="space-y-6">
    <!-- Header with Stats -->
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <p class="text-gray-500"><?= $teamStats['total'] ?? count($users ?? []) ?> thành viên</p>
        </div>
        
        <div class="flex items-center gap-3">
            <!-- Filters -->
            <div class="flex items-center bg-gray-100 rounded-lg p-1">
                <a href="?status=active" class="px-3 py-1.5 text-sm font-medium rounded-md <?= $statusFilter === 'active' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-600 hover:text-gray-900' ?>">
                    Hoạt động
                </a>
                <a href="?status=all" class="px-3 py-1.5 text-sm font-medium rounded-md <?= $statusFilter === 'all' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-600 hover:text-gray-900' ?>">
                    Tất cả
                </a>
                <?php if (Permission::can($userRole, 'users.edit')): ?>
                <a href="?status=inactive" class="px-3 py-1.5 text-sm font-medium rounded-md <?= $statusFilter === 'inactive' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-600 hover:text-gray-900' ?>">
                    Vô hiệu
                </a>
                <?php endif; ?>
            </div>
            
            <?php if (Permission::can($userRole, 'users.create')): ?>
            <button onclick="openModal('add-member-modal')" 
                    class="flex items-center gap-2 rounded-lg bg-primary px-4 py-2 text-sm font-medium text-white hover:bg-primary/90">
                <i data-lucide="user-plus" class="h-4 w-4"></i>
                Thêm thành viên
            </button>
            <?php endif; ?>
        </div>
    </div>
    
    <?php if ($isManager): ?>
    <!-- Team Stats Cards -->
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Tổng thành viên</p>
                    <p class="text-2xl font-bold text-gray-900"><?= $teamStats['total'] ?? 0 ?></p>
                </div>
                <div class="h-11 w-11 rounded-lg bg-blue-100 flex items-center justify-center">
                    <i data-lucide="users" class="h-5 w-5 text-blue-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Đang hoạt động</p>
                    <p class="text-2xl font-bold text-green-600"><?= $teamStats['active'] ?? 0 ?></p>
                </div>
                <div class="h-11 w-11 rounded-lg bg-green-100 flex items-center justify-center">
                    <i data-lucide="user-check" class="h-5 w-5 text-green-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl border <?= ($teamStats['overloaded'] ?? 0) > 0 ? 'border-orange-300 bg-orange-50' : 'border-gray-200' ?> p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Quá tải (>8 việc)</p>
                    <p class="text-2xl font-bold <?= ($teamStats['overloaded'] ?? 0) > 0 ? 'text-orange-600' : 'text-gray-900' ?>"><?= $teamStats['overloaded'] ?? 0 ?></p>
                </div>
                <div class="h-11 w-11 rounded-lg <?= ($teamStats['overloaded'] ?? 0) > 0 ? 'bg-orange-100' : 'bg-gray-100' ?> flex items-center justify-center">
                    <i data-lucide="alert-circle" class="h-5 w-5 <?= ($teamStats['overloaded'] ?? 0) > 0 ? 'text-orange-600' : 'text-gray-400' ?>"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Ít việc (<2 việc)</p>
                    <p class="text-2xl font-bold text-blue-600"><?= $teamStats['idle'] ?? 0 ?></p>
                </div>
                <div class="h-11 w-11 rounded-lg bg-blue-100 flex items-center justify-center">
                    <i data-lucide="coffee" class="h-5 w-5 text-blue-600"></i>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Team Grid -->
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
        <?php if (!empty($users)): ?>
            <?php foreach ($users as $user): 
                $activeTasks = $user['active_tasks'] ?? $user['task_count'] ?? 0;
                $completedTasks = $user['completed_tasks'] ?? 0;
                $overdueTasks = $user['overdue_tasks'] ?? 0;
                $isInactive = ($user['is_active'] ?? 1) == 0;
                
                // Workload status
                $workloadStatus = 'normal';
                if ($activeTasks > 8) $workloadStatus = 'overloaded';
                elseif ($activeTasks < 2) $workloadStatus = 'idle';
                if ($overdueTasks > 0) $workloadStatus = 'warning';
            ?>
            <div class="bg-white rounded-xl border <?= $isInactive ? 'border-gray-300 opacity-60' : ($workloadStatus === 'overloaded' ? 'border-orange-300' : ($workloadStatus === 'warning' ? 'border-red-300' : 'border-gray-200')) ?> p-5 hover:shadow-md transition-shadow">
                <div class="flex items-center gap-4 mb-4">
                    <div class="relative">
                        <div class="h-12 w-12 rounded-full bg-gray-200 flex items-center justify-center overflow-hidden">
                            <?php if (!empty($user['avatar_url'])): ?>
                                <img src="/php/<?= View::e($user['avatar_url']) ?>" alt="" class="h-full w-full object-cover">
                            <?php else: ?>
                                <span class="text-lg font-medium text-gray-600">
                                    <?= strtoupper(substr($user['full_name'] ?? 'U', 0, 1)) ?>
                                </span>
                            <?php endif; ?>
                        </div>
                        <?php if ($isInactive): ?>
                        <span class="absolute -bottom-1 -right-1 h-4 w-4 rounded-full bg-gray-400 border-2 border-white"></span>
                        <?php elseif ($workloadStatus === 'overloaded'): ?>
                        <span class="absolute -bottom-1 -right-1 h-4 w-4 rounded-full bg-orange-500 border-2 border-white"></span>
                        <?php elseif ($workloadStatus === 'warning'): ?>
                        <span class="absolute -bottom-1 -right-1 h-4 w-4 rounded-full bg-red-500 border-2 border-white"></span>
                        <?php else: ?>
                        <span class="absolute -bottom-1 -right-1 h-4 w-4 rounded-full bg-green-500 border-2 border-white"></span>
                        <?php endif; ?>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="font-medium text-gray-900 truncate"><?= View::e($user['full_name'] ?? '') ?></h3>
                        <p class="text-sm text-gray-500 truncate"><?= View::e($user['position'] ?? 'Thành viên') ?></p>
                    </div>
                </div>

                <?php if ($isManager): ?>
                <!-- Workload Stats -->
                <div class="grid grid-cols-3 gap-2 mb-4 text-center">
                    <div class="bg-blue-50 rounded-lg p-2">
                        <p class="text-lg font-bold text-blue-600"><?= $activeTasks ?></p>
                        <p class="text-xs text-gray-500">Đang làm</p>
                    </div>
                    <div class="bg-green-50 rounded-lg p-2">
                        <p class="text-lg font-bold text-green-600"><?= $completedTasks ?></p>
                        <p class="text-xs text-gray-500">Xong</p>
                    </div>
                    <div class="<?= $overdueTasks > 0 ? 'bg-red-50' : 'bg-gray-50' ?> rounded-lg p-2">
                        <p class="text-lg font-bold <?= $overdueTasks > 0 ? 'text-red-600' : 'text-gray-400' ?>"><?= $overdueTasks ?></p>
                        <p class="text-xs text-gray-500">Quá hạn</p>
                    </div>
                </div>
                <?php endif; ?>
                
                <div class="space-y-2 text-sm">
                    <div class="flex items-center gap-2 text-gray-500">
                        <i data-lucide="mail" class="h-4 w-4"></i>
                        <span class="truncate"><?= View::e($user['email'] ?? '') ?></span>
                    </div>
                    <?php if (!empty($user['department'])): ?>
                    <div class="flex items-center gap-2 text-gray-500">
                        <i data-lucide="building" class="h-4 w-4"></i>
                        <span><?= View::e($user['department']) ?></span>
                    </div>
                    <?php endif; ?>
                </div>
                
                <div class="mt-4 pt-4 border-t border-gray-100 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium 
                            <?php
                            $roleClasses = [
                                'admin' => 'bg-red-100 text-red-800',
                                'manager' => 'bg-purple-100 text-purple-800',
                                'member' => 'bg-blue-100 text-blue-800',
                                'guest' => 'bg-gray-100 text-gray-800',
                            ];
                            echo $roleClasses[$user['role'] ?? 'member'] ?? 'bg-gray-100 text-gray-800';
                            ?>">
                            <?= ucfirst($user['role'] ?? 'member') ?>
                        </span>
                        <?php if ($isInactive): ?>
                        <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium bg-gray-100 text-gray-600">
                            Vô hiệu
                        </span>
                        <?php elseif ($workloadStatus === 'overloaded'): ?>
                        <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium bg-orange-100 text-orange-700">
                            Quá tải
                        </span>
                        <?php elseif ($workloadStatus === 'warning'): ?>
                        <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium bg-red-100 text-red-700">
                            Cần hỗ trợ
                        </span>
                        <?php endif; ?>
                    </div>
                    
                    <?php if (Permission::can($userRole, 'users.edit')): ?>
                    <div class="flex items-center gap-1">
                        <button onclick="viewMemberDetail('<?= View::e($user['id']) ?>')" 
                                class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded" title="Xem chi tiết">
                            <i data-lucide="eye" class="h-4 w-4"></i>
                        </button>
                        <button onclick="editMember('<?= View::e($user['id']) ?>')" 
                                class="p-1.5 text-gray-400 hover:text-green-600 hover:bg-green-50 rounded" title="Sửa">
                            <i data-lucide="edit" class="h-4 w-4"></i>
                        </button>
                        <?php if (Permission::can($userRole, 'users.delete') && $user['id'] !== Session::get('user_id')): ?>
                            <?php if ($isInactive): ?>
                            <button onclick="activateMember('<?= View::e($user['id']) ?>', '<?= View::e($user['full_name']) ?>')" 
                                    class="p-1.5 text-gray-400 hover:text-green-600 hover:bg-green-50 rounded" title="Kích hoạt">
                                <i data-lucide="user-check" class="h-4 w-4"></i>
                            </button>
                            <?php else: ?>
                            <button onclick="deleteMember('<?= View::e($user['id']) ?>', '<?= View::e($user['full_name']) ?>')" 
                                    class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded" title="Vô hiệu hóa">
                                <i data-lucide="user-x" class="h-4 w-4"></i>
                            </button>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-span-full text-center py-12">
                <i data-lucide="users" class="h-12 w-12 mx-auto text-gray-300 mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Chưa có thành viên</h3>
                <p class="text-gray-500">Thêm thành viên để bắt đầu làm việc nhóm</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Add Member Modal -->
<?php if (Permission::can($userRole, 'users.create')): ?>
<div id="add-member-modal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-black/50" onclick="closeModal('add-member-modal')"></div>
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-lg" onclick="event.stopPropagation()">
            <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4">
                <h2 class="text-lg font-semibold text-gray-900">Thêm thành viên mới</h2>
                <button onclick="closeModal('add-member-modal')" class="text-gray-400 hover:text-gray-600">
                    <i data-lucide="x" class="h-5 w-5"></i>
                </button>
            </div>
            <form action="/php/api/create-member.php" method="POST" class="p-6 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Họ tên *</label>
                        <input type="text" name="full_name" required
                               class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                        <input type="email" name="email" required
                               class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Mật khẩu *</label>
                        <input type="password" name="password" required minlength="6"
                               class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Vai trò</label>
                        <select name="role" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                            <option value="member">Thành viên</option>
                            <option value="manager">Quản lý</option>
                            <?php if ($userRole === 'admin'): ?>
                            <option value="admin">Admin</option>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Phòng ban</label>
                        <input type="text" name="department"
                               class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Chức vụ</label>
                        <input type="text" name="position"
                               class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary">
                    </div>
                </div>
                <div class="flex justify-end gap-3 pt-4">
                    <button type="button" onclick="closeModal('add-member-modal')" 
                            class="px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-lg">Hủy</button>
                    <button type="submit" 
                            class="px-4 py-2 text-sm font-medium text-white bg-primary hover:bg-primary/90 rounded-lg">Thêm thành viên</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Edit Member Modal -->
<?php if (Permission::can($userRole, 'users.edit')): ?>
<div id="edit-member-modal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-black/50" onclick="closeModal('edit-member-modal')"></div>
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-lg" onclick="event.stopPropagation()">
            <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4">
                <h2 class="text-lg font-semibold text-gray-900">Chỉnh sửa thành viên</h2>
                <button onclick="closeModal('edit-member-modal')" class="text-gray-400 hover:text-gray-600">
                    <i data-lucide="x" class="h-5 w-5"></i>
                </button>
            </div>
            <form id="edit-member-form" class="p-6 space-y-4">
                <input type="hidden" name="user_id" id="edit-user-id">
                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Họ tên *</label>
                        <input type="text" name="full_name" id="edit-full-name" required
                               class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                        <input type="email" name="email" id="edit-email" required
                               class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Vai trò</label>
                        <select name="role" id="edit-role" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                            <option value="member">Thành viên</option>
                            <option value="manager">Quản lý</option>
                            <?php if ($userRole === 'admin'): ?>
                            <option value="admin">Admin</option>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Trạng thái</label>
                        <select name="is_active" id="edit-is-active" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                            <option value="1">Hoạt động</option>
                            <option value="0">Vô hiệu hóa</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Phòng ban</label>
                        <input type="text" name="department" id="edit-department"
                               class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Chức vụ</label>
                        <input type="text" name="position" id="edit-position"
                               class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary">
                    </div>
                </div>
                <div class="flex justify-end gap-3 pt-4">
                    <button type="button" onclick="closeModal('edit-member-modal')" 
                            class="px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-lg">Hủy</button>
                    <button type="submit" 
                            class="px-4 py-2 text-sm font-medium text-white bg-primary hover:bg-primary/90 rounded-lg">Lưu thay đổi</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Member Detail Modal -->
<div id="member-detail-modal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-black/50" onclick="closeModal('member-detail-modal')"></div>
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto" onclick="event.stopPropagation()">
            <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4 sticky top-0 bg-white">
                <h2 class="text-lg font-semibold text-gray-900">Chi tiết thành viên</h2>
                <button onclick="closeModal('member-detail-modal')" class="text-gray-400 hover:text-gray-600">
                    <i data-lucide="x" class="h-5 w-5"></i>
                </button>
            </div>
            <div id="member-detail-content" class="p-6">
                <div class="text-center py-8 text-gray-500">Đang tải...</div>
            </div>
        </div>
    </div>
</div>

<script>
// View member detail
function viewMemberDetail(userId) {
    const content = document.getElementById('member-detail-content');
    content.innerHTML = '<div class="text-center py-8 text-gray-500">Đang tải...</div>';
    openModal('member-detail-modal');
    
    fetch('/php/api/users.php?id=' + userId + '&detail=1')
        .then(r => r.json())
        .then(data => {
            if (data.success && data.data) {
                const user = data.data;
                content.innerHTML = `
                    <div class="flex items-center gap-4 mb-6">
                        <div class="h-16 w-16 rounded-full bg-gray-200 flex items-center justify-center overflow-hidden">
                            ${user.avatar_url 
                                ? `<img src="/php/${user.avatar_url}" class="h-full w-full object-cover">` 
                                : `<span class="text-2xl font-medium text-gray-600">${(user.full_name || 'U').charAt(0).toUpperCase()}</span>`}
                        </div>
                        <div>
                            <h3 class="text-xl font-semibold text-gray-900">${user.full_name || ''}</h3>
                            <p class="text-gray-500">${user.position || 'Thành viên'} · ${user.department || ''}</p>
                            <p class="text-sm text-gray-400">${user.email || ''}</p>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-4 gap-4 mb-6">
                        <div class="bg-blue-50 rounded-lg p-4 text-center">
                            <p class="text-2xl font-bold text-blue-600">${user.active_tasks || 0}</p>
                            <p class="text-sm text-gray-500">Đang làm</p>
                        </div>
                        <div class="bg-green-50 rounded-lg p-4 text-center">
                            <p class="text-2xl font-bold text-green-600">${user.completed_tasks || 0}</p>
                            <p class="text-sm text-gray-500">Hoàn thành</p>
                        </div>
                        <div class="bg-red-50 rounded-lg p-4 text-center">
                            <p class="text-2xl font-bold text-red-600">${user.overdue_tasks || 0}</p>
                            <p class="text-sm text-gray-500">Quá hạn</p>
                        </div>
                        <div class="bg-purple-50 rounded-lg p-4 text-center">
                            <p class="text-2xl font-bold text-purple-600">${user.completion_rate || 0}%</p>
                            <p class="text-sm text-gray-500">Tỷ lệ</p>
                        </div>
                    </div>
                    
                    <div class="border-t border-gray-200 pt-4">
                        <h4 class="font-medium text-gray-900 mb-3">Thông tin</h4>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-gray-500">Vai trò:</span>
                                <span class="ml-2 font-medium capitalize">${user.role || 'member'}</span>
                            </div>
                            <div>
                                <span class="text-gray-500">Trạng thái:</span>
                                <span class="ml-2 font-medium ${user.is_active ? 'text-green-600' : 'text-gray-400'}">${user.is_active ? 'Hoạt động' : 'Vô hiệu'}</span>
                            </div>
                            <div>
                                <span class="text-gray-500">Tham gia:</span>
                                <span class="ml-2 font-medium">${user.created_at ? new Date(user.created_at).toLocaleDateString('vi-VN') : '-'}</span>
                            </div>
                            <div>
                                <span class="text-gray-500">Đăng nhập cuối:</span>
                                <span class="ml-2 font-medium">${user.last_login_at ? new Date(user.last_login_at).toLocaleDateString('vi-VN') : '-'}</span>
                            </div>
                        </div>
                    </div>
                `;
                lucide.createIcons();
            } else {
                content.innerHTML = '<div class="text-center py-8 text-red-500">Không thể tải thông tin</div>';
            }
        })
        .catch(err => {
            content.innerHTML = '<div class="text-center py-8 text-red-500">Lỗi kết nối</div>';
        });
}

// Edit member
function editMember(userId) {
    fetch('/php/api/users.php?id=' + userId)
        .then(r => r.json())
        .then(data => {
            if (data.success && data.data) {
                const user = data.data;
                document.getElementById('edit-user-id').value = user.id;
                document.getElementById('edit-full-name').value = user.full_name || '';
                document.getElementById('edit-email').value = user.email || '';
                document.getElementById('edit-role').value = user.role || 'member';
                document.getElementById('edit-is-active').value = user.is_active ? '1' : '0';
                document.getElementById('edit-department').value = user.department || '';
                document.getElementById('edit-position').value = user.position || '';
                openModal('edit-member-modal');
            } else {
                alert(data.error || 'Không thể tải thông tin thành viên');
            }
        })
        .catch(err => alert('Lỗi kết nối: ' + err.message));
}

// Submit edit form
document.getElementById('edit-member-form')?.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const data = Object.fromEntries(formData.entries());
    
    fetch('/php/api/users.php', {
        method: 'PUT',
        headers: { 
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(r => r.json())
    .then(result => {
        if (result.success) {
            location.reload();
        } else {
            alert(result.error || 'Có lỗi xảy ra');
        }
    })
    .catch(err => alert('Lỗi kết nối: ' + err.message));
});

// Delete member
function deleteMember(userId, userName) {
    if (!confirm(`Bạn có chắc muốn vô hiệu hóa thành viên "${userName}"?`)) return;
    
    fetch('/php/api/users.php?id=' + userId, {
        method: 'DELETE',
        headers: { 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(result => {
        if (result.success) {
            location.reload();
        } else {
            alert(result.error || 'Có lỗi xảy ra');
        }
    })
    .catch(err => alert('Lỗi kết nối: ' + err.message));
}

// Activate member
function activateMember(userId, userName) {
    if (!confirm(`Kích hoạt lại thành viên "${userName}"?`)) return;
    
    fetch('/php/api/activate-member.php', {
        method: 'POST',
        headers: { 
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ user_id: userId })
    })
    .then(r => r.json())
    .then(result => {
        if (result.success) {
            location.reload();
        } else {
            alert(result.error || 'Có lỗi xảy ra');
        }
    })
    .catch(err => alert('Lỗi kết nối: ' + err.message));
}
</script>

<?php View::endSection(); ?>
