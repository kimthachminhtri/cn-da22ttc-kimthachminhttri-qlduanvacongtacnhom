<?php
/**
 * Admin Pagination Component
 * Usage: View::partial('components/admin-pagination', ['pagination' => $pagination])
 */
use Core\View;

if (!isset($pagination) || $pagination['total'] <= 1) return;

$current = $pagination['current'];
$total = $pagination['total'];
$perPage = $pagination['perPage'];
$totalItems = $pagination['totalItems'];
$baseUrl = $pagination['baseUrl'] ?? '?';

// Preserve existing query params
$queryParams = $_GET;
unset($queryParams['page']);
$queryString = http_build_query($queryParams);
$baseUrl = $baseUrl . ($queryString ? $queryString . '&' : '');
?>

<div class="flex flex-col sm:flex-row items-center justify-between gap-4 px-6 py-4 bg-white border-t border-gray-100">
    <div class="text-sm text-gray-600">
        Hiển thị <span class="font-medium"><?= number_format(($current - 1) * $perPage + 1) ?></span>
        - <span class="font-medium"><?= number_format(min($current * $perPage, $totalItems)) ?></span>
        trong <span class="font-medium"><?= number_format($totalItems) ?></span> kết quả
    </div>
    
    <div class="flex items-center gap-1">
        <?php if ($current > 1): ?>
        <a href="<?= $baseUrl ?>page=1" 
           class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors" title="Trang đầu">
            <i data-lucide="chevrons-left" class="h-4 w-4"></i>
        </a>
        <a href="<?= $baseUrl ?>page=<?= $current - 1 ?>" 
           class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors" title="Trang trước">
            <i data-lucide="chevron-left" class="h-4 w-4"></i>
        </a>
        <?php else: ?>
        <span class="p-2 text-gray-300 cursor-not-allowed"><i data-lucide="chevrons-left" class="h-4 w-4"></i></span>
        <span class="p-2 text-gray-300 cursor-not-allowed"><i data-lucide="chevron-left" class="h-4 w-4"></i></span>
        <?php endif; ?>
        
        <?php
        $start = max(1, $current - 2);
        $end = min($total, $current + 2);
        
        if ($start > 1): ?>
        <span class="px-2 text-gray-400">...</span>
        <?php endif;
        
        for ($i = $start; $i <= $end; $i++): ?>
        <a href="<?= $baseUrl ?>page=<?= $i ?>" 
           class="min-w-[36px] h-9 flex items-center justify-center text-sm font-medium rounded-lg transition-colors
                  <?= $i === $current ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-gray-100' ?>">
            <?= $i ?>
        </a>
        <?php endfor;
        
        if ($end < $total): ?>
        <span class="px-2 text-gray-400">...</span>
        <?php endif; ?>
        
        <?php if ($current < $total): ?>
        <a href="<?= $baseUrl ?>page=<?= $current + 1 ?>" 
           class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors" title="Trang sau">
            <i data-lucide="chevron-right" class="h-4 w-4"></i>
        </a>
        <a href="<?= $baseUrl ?>page=<?= $total ?>" 
           class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors" title="Trang cuối">
            <i data-lucide="chevrons-right" class="h-4 w-4"></i>
        </a>
        <?php else: ?>
        <span class="p-2 text-gray-300 cursor-not-allowed"><i data-lucide="chevron-right" class="h-4 w-4"></i></span>
        <span class="p-2 text-gray-300 cursor-not-allowed"><i data-lucide="chevrons-right" class="h-4 w-4"></i></span>
        <?php endif; ?>
    </div>
</div>
