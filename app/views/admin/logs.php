<?php
/**
 * Admin Activity Logs View
 */
use Core\View;

View::section('content');
// timeAgo() is already defined in includes/functions.php
?>

<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Activity Logs</h1>
            <p class="text-gray-500 mt-1">Tổng cộng <?= number_format($total ?? 0) ?> hoạt động</p>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
        <div class="divide-y divide-gray-100">
            <?php if (!empty($logs)): ?>
                <?php foreach ($logs as $log): ?>
                <div class="px-6 py-4 hover:bg-gray-50 transition-colors">
                    <div class="flex items-start gap-4">
                        <div class="h-10 w-10 rounded-full bg-gray-100 flex items-center justify-center flex-shrink-0">
                            <?php
                            $icons = [
                                'create' => 'plus-circle',
                                'update' => 'edit',
                                'delete' => 'trash-2',
                                'login' => 'log-in',
                                'logout' => 'log-out',
                            ];
                            $icon = $icons[$log['action']] ?? 'activity';
                            ?>
                            <i data-lucide="<?= $icon ?>" class="h-5 w-5 text-gray-600"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-gray-900">
                                <span class="font-medium"><?= View::e($log['full_name'] ?? 'System') ?></span>
                                <span class="text-gray-500">(<?= View::e($log['email'] ?? '') ?>)</span>
                            </p>
                            <p class="text-sm text-gray-600 mt-0.5">
                                <?= View::e($log['action']) ?> 
                                <span class="text-gray-500"><?= View::e($log['entity_type']) ?></span>
                                <?php if ($log['entity_id']): ?>
                                <span class="text-gray-400">#<?= View::e(substr($log['entity_id'], 0, 8)) ?></span>
                                <?php endif; ?>
                            </p>
                            <div class="flex items-center gap-4 mt-2 text-xs text-gray-400">
                                <span><?= timeAgo($log['created_at']) ?></span>
                                <?php if ($log['ip_address']): ?>
                                <span>IP: <?= View::e($log['ip_address']) ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="text-right text-xs text-gray-400">
                            <?= date('d/m/Y', strtotime($log['created_at'])) ?><br>
                            <?= date('H:i:s', strtotime($log['created_at'])) ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="px-6 py-12 text-center text-gray-500">
                    <i data-lucide="scroll-text" class="h-12 w-12 mx-auto mb-3 text-gray-300"></i>
                    <p>Chưa có hoạt động nào</p>
                </div>
            <?php endif; ?>
        </div>
        
        <?php if (($totalPages ?? 1) > 1): ?>
        <?php View::partial('components/admin-pagination', ['pagination' => [
            'current' => $page,
            'total' => $totalPages,
            'perPage' => 50,
            'totalItems' => $total,
            'baseUrl' => '?',
        ]]); ?>
        <?php endif; ?>
    </div>
</div>

<?php View::endSection(); ?>
