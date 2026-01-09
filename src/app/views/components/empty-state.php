<?php
/**
 * Empty State Component
 * 
 * Usage:
 * View::partial('components/empty-state', [
 *     'icon' => 'folder-open',
 *     'title' => 'Không có dự án nào',
 *     'description' => 'Bắt đầu bằng cách tạo dự án đầu tiên',
 *     'action' => [
 *         'label' => 'Tạo dự án',
 *         'onclick' => "openModal('create-project-modal')",
 *         'icon' => 'plus'
 *     ]
 * ]);
 */

use Core\View;

$icon = $icon ?? 'inbox';
$title = $title ?? 'Không có dữ liệu';
$description = $description ?? 'Chưa có dữ liệu để hiển thị';
$action = $action ?? null;
$size = $size ?? 'default'; // small, default, large

$sizeClasses = [
    'small' => ['icon' => 'h-12 w-12', 'title' => 'text-base', 'desc' => 'text-sm', 'py' => 'py-8'],
    'default' => ['icon' => 'h-16 w-16', 'title' => 'text-lg', 'desc' => 'text-sm', 'py' => 'py-12'],
    'large' => ['icon' => 'h-20 w-20', 'title' => 'text-xl', 'desc' => 'text-base', 'py' => 'py-16'],
];
$s = $sizeClasses[$size] ?? $sizeClasses['default'];

// Illustrations mapping
$illustrations = [
    'folder-open' => '📁',
    'check-square' => '✅',
    'file-text' => '📄',
    'users' => '👥',
    'calendar' => '📅',
    'bell' => '🔔',
    'search' => '🔍',
    'inbox' => '📥',
];
$emoji = $illustrations[$icon] ?? '📋';
?>

<div class="col-span-full flex flex-col items-center justify-center <?= $s['py'] ?> text-center">
    <!-- Illustration -->
    <div class="relative mb-4">
        <div class="<?= $s['icon'] ?> rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center">
            <i data-lucide="<?= View::e($icon) ?>" class="<?= $size === 'small' ? 'h-6 w-6' : ($size === 'large' ? 'h-10 w-10' : 'h-8 w-8') ?> text-gray-400"></i>
        </div>
        <span class="absolute -bottom-1 -right-1 text-2xl"><?= $emoji ?></span>
    </div>
    
    <!-- Title -->
    <h3 class="<?= $s['title'] ?> font-semibold text-gray-900 dark:text-white mb-2">
        <?= View::e($title) ?>
    </h3>
    
    <!-- Description -->
    <p class="<?= $s['desc'] ?> text-gray-500 dark:text-gray-400 max-w-sm mb-6">
        <?= View::e($description) ?>
    </p>
    
    <!-- Action Button -->
    <?php if ($action): ?>
    <button type="button"
            onclick="<?= View::e($action['onclick'] ?? '') ?>"
            class="inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-sm font-medium text-white hover:bg-primary/90 transition-colors shadow-sm">
        <?php if (!empty($action['icon'])): ?>
        <i data-lucide="<?= View::e($action['icon']) ?>" class="h-4 w-4"></i>
        <?php endif; ?>
        <?= View::e($action['label'] ?? 'Bắt đầu') ?>
    </button>
    <?php endif; ?>
    
    <!-- Tips (optional) -->
    <?php if (!empty($tips)): ?>
    <div class="mt-6 text-left bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4 max-w-md">
        <p class="text-sm font-medium text-blue-800 dark:text-blue-300 mb-2">💡 Mẹo:</p>
        <ul class="text-sm text-blue-700 dark:text-blue-400 space-y-1">
            <?php foreach ($tips as $tip): ?>
            <li>• <?= View::e($tip) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>
</div>
