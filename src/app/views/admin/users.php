<?php
/**
 * Admin Users Management - Enhanced with Pagination, Bulk Actions, Export
 */
use Core\View;

View::section('content');
?>

<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Quản lý người dùng</h1>
            <p class="text-gray-500 mt-1">Quản lý tất cả người dùng trong hệ thống</p>
        </div>
        <div class="flex items-center gap-2">
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-colors">
                    <i data-lucide="download" class="h-5 w-5"></i>
                    Export
                    <i data-lucide="chevron-down" class="h-4 w-4"></i>
                </button>
                <div x-show="open" @click.away="open = false" x-cloak
                     class="absolute right-0 mt-2 w-52 bg-white rounded-xl shadow-lg border border-gray-200 py-1 z-50">
                    <a href="/php/api/admin-export.php?type=users&format=excel" target="_blank"
                       class="flex items-center gap-2 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50">
                        <i data-lucide="file-spreadsheet" class="h-4 w-4 text-green-600"></i>
                        Excel (.xlsx)
                    </a>
                    <a href="/php/api/admin-export.php?type=users&format=pdf" target="_blank"
                       class="flex items-center gap-2 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50">
                        <i data-lucide="file-text" class="h-4 w-4 text-red-600"></i>
                        PDF
                    </a>
                    <a href="/php/api/admin-export.php?type=users&format=csv" target="_blank"
                       class="flex items-center gap-2 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50">
                        <i data-lucide="file" class="h-4 w-4 text-blue-600"></i>
                        CSV
                    </a>
                    <a href="/php/api/admin-export.php?type=users&format=json" target="_blank"
                       class="flex items-center gap-2 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50">
                        <i data-lucide="file-json" class="h-4 w-4 text-yellow-600"></i>
                        JSON
                    </a>
                </div>
            </div>
            <button onclick="openModal('create-user-modal')" 
                    class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-colors">
                <i data-lucide="user-plus" class="h-5 w-5"></i>
                Thêm người dùng
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
        <a href="?status=" class="bg-white rounded-xl p-4 border border-gray-100 hover:border-blue-300 transition-colors <?= ($filters['status'] ?? '') === '' && !($filters['role'] ?? '') ? 'ring-2 ring-blue-500' : '' ?>">
            <p class="text-2xl font-bold text-gray-900"><?= $userStats['total'] ?? 0 ?></p>
            <p class="text-sm text-gray-500">Tổng cộng</p>
        </a>
        <a href="?status=1" class="bg-white rounded-xl p-4 border border-gray-100 hover:border-green-300 transition-colors <?= ($filters['status'] ?? '') === '1' ? 'ring-2 ring-green-500' : '' ?>">
            <p class="text-2xl font-bold text-green-600"><?= $userStats['active'] ?? 0 ?></p>
            <p class="text-sm text-gray-500">Hoạt động</p>
        </a>
        <a href="?status=0" class="bg-white rounded-xl p-4 border border-gray-100 hover:border-gray-300 transition-colors <?= ($filters['status'] ?? '') === '0' ? 'ring-2 ring-gray-500' : '' ?>">
            <p class="text-2xl font-bold text-gray-400"><?= $userStats['inactive'] ?? 0 ?></p>
            <p class="text-sm text-gray-500">Vô hiệu</p>
        </a>
        <a href="?role=admin" class="bg-white rounded-xl p-4 border border-gray-100 hover:border-red-300 transition-colors <?= ($filters['role'] ?? '') === 'admin' ? 'ring-2 ring-red-500' : '' ?>">
            <p class="text-2xl font-bold text-red-600"><?= $userStats['admin'] ?? 0 ?></p>
            <p class="text-sm text-gray-500">Admin</p>
        </a>
        <a href="?role=manager" class="bg-white rounded-xl p-4 border border-gray-100 hover:border-blue-300 transition-colors <?= ($filters['role'] ?? '') === 'manager' ? 'ring-2 ring-blue-500' : '' ?>">
            <p class="text-2xl font-bold text-blue-600"><?= $userStats['manager'] ?? 0 ?></p>
            <p class="text-sm text-gray-500">Manager</p>
        </a>
        <a href="?role=member" class="bg-white rounded-xl p-4 border border-gray-100 hover:border-gray-300 transition-colors <?= ($filters['role'] ?? '') === 'member' ? 'ring-2 ring-gray-500' : '' ?>">
            <p class="text-2xl font-bold text-gray-600"><?= $userStats['member'] ?? 0 ?></p>
            <p class="text-sm text-gray-500">Member</p>
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl p-4 border border-gray-100">
        <form method="GET" class="flex flex-wrap items-center gap-4">
            <div class="flex-1 min-w-[200px]">
                <input type="text" name="search" value="<?= View::e($filters['search'] ?? '') ?>" 
                       placeholder="Tìm kiếm theo tên hoặc email..."
                       class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <select name="role" class="px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500">
                <option value="">Tất cả vai trò</option>
                <option value="admin" <?= ($filters['role'] ?? '') === 'admin' ? 'selected' : '' ?>>Admin</option>
                <option value="manager" <?= ($filters['role'] ?? '') === 'manager' ? 'selected' : '' ?>>Manager</option>
                <option value="member" <?= ($filters['role'] ?? '') === 'member' ? 'selected' : '' ?>>Member</option>
                <option value="guest" <?= ($filters['role'] ?? '') === 'guest' ? 'selected' : '' ?>>Guest</option>
            </select>
            <select name="status" class="px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500">
                <option value="">Tất cả trạng thái</option>
                <option value="1" <?= ($filters['status'] ?? '') === '1' ? 'selected' : '' ?>>Hoạt động</option>
                <option value="0" <?= ($filters['status'] ?? '') === '0' ? 'selected' : '' ?>>Vô hiệu</option>
            </select>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                <i data-lucide="search" class="h-5 w-5"></i>
            </button>
            <?php if (!empty($filters['search']) || !empty($filters['role']) || $filters['status'] !== ''): ?>
            <a href="/php/admin/users.php" class="px-4 py-2 text-gray-500 hover:text-gray-700">Xóa bộ lọc</a>
            <?php endif; ?>
        </form>
    </div>

    <!-- Bulk Actions Bar -->
    <div id="bulk-toolbar" class="hidden bg-blue-50 border border-blue-200 rounded-xl p-4 items-center justify-between">
        <div class="flex items-center gap-3">
            <span class="text-sm text-blue-700">
                <span id="selected-count" class="font-bold">0</span> người dùng được chọn
            </span>
        </div>
        <div class="flex items-center gap-2">
            <button onclick="bulkOps.activate()" class="px-3 py-1.5 text-sm bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors">
                <i data-lucide="check" class="h-4 w-4 inline mr-1"></i>Kích hoạt
            </button>
            <button onclick="bulkOps.deactivate()" class="px-3 py-1.5 text-sm bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                <i data-lucide="x" class="h-4 w-4 inline mr-1"></i>Vô hiệu
            </button>
            <!-- Role Change -->
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" class="px-3 py-1.5 text-sm bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors inline-flex items-center gap-1">
                    <i data-lucide="shield" class="h-4 w-4"></i>
                    Đổi vai trò
                    <i data-lucide="chevron-down" class="h-3 w-3"></i>
                </button>
                <div x-show="open" @click.away="open = false" x-cloak class="absolute left-0 mt-1 w-36 bg-white rounded-lg shadow-lg border z-50">
                    <button onclick="bulkOps.changeRole('admin')" class="w-full text-left px-3 py-2 text-sm text-red-600 hover:bg-gray-50">Admin</button>
                    <button onclick="bulkOps.changeRole('manager')" class="w-full text-left px-3 py-2 text-sm text-blue-600 hover:bg-gray-50">Manager</button>
                    <button onclick="bulkOps.changeRole('member')" class="w-full text-left px-3 py-2 text-sm hover:bg-gray-50">Member</button>
                    <button onclick="bulkOps.changeRole('guest')" class="w-full text-left px-3 py-2 text-sm text-gray-500 hover:bg-gray-50">Guest</button>
                </div>
            </div>
            <button onclick="bulkOps.delete()" class="px-3 py-1.5 text-sm bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors">
                <i data-lucide="trash-2" class="h-4 w-4 inline mr-1"></i>Xóa
            </button>
            <button onclick="bulkOps.clearSelection()" class="px-3 py-1.5 text-sm text-gray-600 hover:text-gray-800">
                Bỏ chọn
            </button>
        </div>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-left">
                            <input type="checkbox" id="select-all"
                                   class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Người dùng</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Vai trò</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Phòng ban</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Trạng thái</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Ngày tạo</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php if (!empty($users)): ?>
                        <?php foreach ($users as $user): ?>
                        <tr class="hover:bg-gray-50 transition-colors" data-user-id="<?= $user['id'] ?>">
                            <td class="px-6 py-4">
                                <input type="checkbox" class="bulk-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                       data-id="<?= $user['id'] ?>">
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="h-10 w-10 rounded-full bg-gradient-to-br from-blue-500 to-blue-700 flex items-center justify-center text-white font-medium">
                                        <?= strtoupper(substr($user['full_name'], 0, 1)) ?>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900"><?= View::e($user['full_name']) ?></p>
                                        <p class="text-sm text-gray-500"><?= View::e($user['email']) ?></p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <select onchange="updateUserRole('<?= $user['id'] ?>', this.value)"
                                        class="px-3 py-1.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500
                                        <?= $user['role'] === 'admin' ? 'bg-red-50 text-red-700 border-red-200' : 
                                           ($user['role'] === 'manager' ? 'bg-blue-50 text-blue-700 border-blue-200' : 'bg-gray-50') ?>">
                                    <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                                    <option value="manager" <?= $user['role'] === 'manager' ? 'selected' : '' ?>>Manager</option>
                                    <option value="member" <?= $user['role'] === 'member' ? 'selected' : '' ?>>Member</option>
                                    <option value="guest" <?= $user['role'] === 'guest' ? 'selected' : '' ?>>Guest</option>
                                </select>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                <?= View::e($user['department'] ?? '-') ?>
                            </td>
                            <td class="px-6 py-4">
                                <button onclick="toggleUserStatus('<?= $user['id'] ?>', <?= $user['is_active'] ? 'false' : 'true' ?>)"
                                        class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-sm font-medium transition-colors
                                        <?= $user['is_active'] ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' ?>">
                                    <span class="h-2 w-2 rounded-full <?= $user['is_active'] ? 'bg-green-500' : 'bg-gray-400' ?>"></span>
                                    <?= $user['is_active'] ? 'Hoạt động' : 'Vô hiệu' ?>
                                </button>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                <?= date('d/m/Y H:i', strtotime($user['created_at'])) ?>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    <button onclick="editUser('<?= $user['id'] ?>')" 
                                            class="p-2 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                                            title="Chỉnh sửa">
                                        <i data-lucide="edit" class="h-4 w-4"></i>
                                    </button>
                                    <button onclick="deleteUser('<?= $user['id'] ?>', '<?= View::e($user['full_name']) ?>')" 
                                            class="p-2 text-gray-500 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                            title="Xóa">
                                        <i data-lucide="trash-2" class="h-4 w-4"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                <i data-lucide="users" class="h-12 w-12 mx-auto mb-3 text-gray-300"></i>
                                <p>Không tìm thấy người dùng nào</p>
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

<!-- Create User Modal -->
<div id="create-user-modal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-black/50" onclick="closeModal('create-user-modal')"></div>
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg" onclick="event.stopPropagation()">
            <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4">
                <h2 class="text-lg font-semibold text-gray-900">Thêm người dùng mới</h2>
                <button onclick="closeModal('create-user-modal')" class="text-gray-400 hover:text-gray-600">
                    <i data-lucide="x" class="h-5 w-5"></i>
                </button>
            </div>
            <form action="/php/api/admin-users.php?action=create" method="POST" class="p-6 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Họ và tên *</label>
                        <input type="text" name="full_name" required
                               class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                        <input type="email" name="email" required
                               class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Mật khẩu *</label>
                        <input type="password" name="password" required minlength="6"
                               class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Vai trò</label>
                        <select name="role" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="member">Member</option>
                            <option value="manager">Manager</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Phòng ban</label>
                        <input type="text" name="department"
                               class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Chức vụ</label>
                        <input type="text" name="position"
                               class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                </div>
                <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                    <button type="button" onclick="closeModal('create-user-modal')" 
                            class="px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">Hủy</button>
                    <button type="submit" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">Tạo người dùng</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="/php/public/js/bulk-operations.js"></script>
<script>
const bulkOps = new BulkOperations({ entity: 'users' });

// Single user actions
async function updateUserRole(userId, role) {
    try {
        const response = await fetch('/php/api/admin-users.php?action=update', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `user_id=${userId}&role=${role}`
        });
        const data = await response.json();
        if (data.success) {
            showToast('Cập nhật vai trò thành công', 'success');
        } else {
            showToast(data.error || 'Có lỗi xảy ra', 'error');
        }
    } catch (e) {
        showToast('Có lỗi xảy ra', 'error');
    }
}

async function toggleUserStatus(userId, activate) {
    try {
        const response = await fetch('/php/api/admin-users.php?action=update', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `user_id=${userId}&is_active=${activate ? 1 : 0}`
        });
        const data = await response.json();
        if (data.success) {
            showToast(activate ? 'Đã kích hoạt người dùng' : 'Đã vô hiệu hóa người dùng', 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            showToast(data.error || 'Có lỗi xảy ra', 'error');
        }
    } catch (e) {
        showToast('Có lỗi xảy ra', 'error');
    }
}

async function deleteUser(userId, userName) {
    if (!confirm(`Bạn có chắc muốn vô hiệu hóa người dùng "${userName}"?`)) return;
    
    try {
        const response = await fetch('/php/api/admin-users.php?action=delete', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `user_id=${userId}`
        });
        const data = await response.json();
        if (data.success) {
            showToast('Đã vô hiệu hóa người dùng', 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            showToast(data.error || 'Có lỗi xảy ra', 'error');
        }
    } catch (e) {
        showToast('Có lỗi xảy ra', 'error');
    }
}

function editUser(userId) {
    showToast('Tính năng đang phát triển', 'info');
}
</script>

<?php View::endSection(); ?>
