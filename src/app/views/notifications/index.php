<?php
/**
 * Notifications View
 */
use Core\View;
use Core\Session;

View::section('content');
?>

<div class="max-w-3xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <p class="text-gray-500"><?= count($notifications ?? []) ?> thông báo</p>
        </div>
        <?php if (!empty($notifications)): ?>
        <button onclick="markAllAsRead()" class="text-sm text-primary hover:underline">
            Đánh dấu tất cả đã đọc
        </button>
        <?php endif; ?>
    </div>

    <!-- Notifications List -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm divide-y divide-gray-100">
        <?php if (!empty($notifications)): ?>
            <?php foreach ($notifications as $notification): ?>
            <div class="p-4 hover:bg-gray-50 transition-colors <?= ($notification['is_read'] ?? false) ? '' : 'bg-blue-50/50' ?>">
                <div class="flex items-start gap-4">
                    <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center flex-shrink-0">
                        <?php
                        $icons = [
                            'task_assigned' => 'user-plus',
                            'task_completed' => 'check-circle',
                            'comment' => 'message-circle',
                            'mention' => 'at-sign',
                            'due_soon' => 'clock',
                        ];
                        $icon = $icons[$notification['type'] ?? 'default'] ?? 'bell';
                        ?>
                        <i data-lucide="<?= $icon ?>" class="h-5 w-5 text-gray-600"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm text-gray-900"><?= View::e($notification['message'] ?? '') ?></p>
                        <p class="text-xs text-gray-500 mt-1"><?= $notification['time_ago'] ?? '' ?></p>
                    </div>
                    <?php if (!($notification['is_read'] ?? false)): ?>
                    <button onclick="markAsRead('<?= View::e($notification['id'] ?? '') ?>')" 
                            class="p-1 text-gray-400 hover:text-primary">
                        <i data-lucide="check" class="h-4 w-4"></i>
                    </button>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="p-12 text-center">
                <i data-lucide="bell-off" class="h-12 w-12 mx-auto text-gray-300 mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Không có thông báo</h3>
                <p class="text-gray-500">Bạn sẽ nhận được thông báo khi có hoạt động mới</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
function markAsRead(id) {
    fetch('/php/api/notifications.php', {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: id, is_read: true })
    }).then(() => location.reload());
}

function markAllAsRead() {
    fetch('/php/api/notifications.php', {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ mark_all: true })
    }).then(() => location.reload());
}
</script>

<?php View::endSection(); ?>
