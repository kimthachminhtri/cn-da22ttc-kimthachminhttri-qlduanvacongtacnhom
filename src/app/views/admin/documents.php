<?php
/**
 * Admin Documents Management View - Enhanced
 */
use Core\View;

View::section('content');

function formatFileSize($bytes) {
    if ($bytes >= 1073741824) return number_format($bytes / 1073741824, 2) . ' GB';
    if ($bytes >= 1048576) return number_format($bytes / 1048576, 2) . ' MB';
    if ($bytes >= 1024) return number_format($bytes / 1024, 2) . ' KB';
    return $bytes . ' B';
}

function getFileIcon($mimeType) {
    if (strpos($mimeType, 'image') !== false) return 'image';
    if (strpos($mimeType, 'pdf') !== false) return 'file-text';
    if (strpos($mimeType, 'word') !== false || strpos($mimeType, 'document') !== false) return 'file-text';
    if (strpos($mimeType, 'excel') !== false || strpos($mimeType, 'spreadsheet') !== false) return 'file-spreadsheet';
    if (strpos($mimeType, 'zip') !== false || strpos($mimeType, 'archive') !== false) return 'file-archive';
    return 'file';
}
?>

<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Quản lý tài liệu</h1>
            <p class="text-gray-500 mt-1">
                Tổng dung lượng: <span class="font-medium text-blue-600"><?= formatFileSize($totalSize ?? 0) ?></span>
                • <?= $pagination['totalItems'] ?? 0 ?> files
            </p>
        </div>
        <div class="flex items-center gap-2">
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-colors">
                    <i data-lucide="download" class="h-5 w-5"></i>
                    Export
                </button>
                <div x-show="open" @click.away="open = false" x-cloak
                     class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-gray-200 py-1 z-50">
                    <a href="/php/api/admin-export.php?type=documents&format=csv" 
                       class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                        <i data-lucide="file-text" class="h-4 w-4"></i>Export CSV
                    </a>
                    <a href="/php/api/admin-export.php?type=documents&format=json" 
                       class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                        <i data-lucide="file-json" class="h-4 w-4"></i>Export JSON
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        <a href="/php/admin/documents.php" 
           class="bg-white rounded-xl p-4 border border-gray-100 hover:border-blue-300 hover:shadow-md transition-all cursor-pointer <?= empty($filters['type']) ? 'ring-2 ring-blue-500' : '' ?>">
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-lg bg-blue-100 flex items-center justify-center">
                    <i data-lucide="files" class="h-5 w-5 text-blue-600"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900"><?= $docStats['total'] ?? 0 ?></p>
                    <p class="text-sm text-gray-500">Tổng cộng</p>
                </div>
            </div>
        </a>
        <a href="/php/admin/documents.php?type=image" 
           class="bg-white rounded-xl p-4 border border-gray-100 hover:border-purple-300 hover:shadow-md transition-all cursor-pointer <?= ($filters['type'] ?? '') === 'image' ? 'ring-2 ring-purple-500' : '' ?>">
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-lg bg-purple-100 flex items-center justify-center">
                    <i data-lucide="image" class="h-5 w-5 text-purple-600"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-purple-600"><?= $docStats['images'] ?? 0 ?></p>
                    <p class="text-sm text-gray-500">Hình ảnh</p>
                </div>
            </div>
        </a>
        <a href="/php/admin/documents.php?type=pdf" 
           class="bg-white rounded-xl p-4 border border-gray-100 hover:border-red-300 hover:shadow-md transition-all cursor-pointer <?= ($filters['type'] ?? '') === 'pdf' ? 'ring-2 ring-red-500' : '' ?>">
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-lg bg-red-100 flex items-center justify-center">
                    <i data-lucide="file-text" class="h-5 w-5 text-red-600"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-red-600"><?= $docStats['pdfs'] ?? 0 ?></p>
                    <p class="text-sm text-gray-500">PDF</p>
                </div>
            </div>
        </a>
        <a href="/php/admin/documents.php?type=document" 
           class="bg-white rounded-xl p-4 border border-gray-100 hover:border-green-300 hover:shadow-md transition-all cursor-pointer <?= ($filters['type'] ?? '') === 'document' ? 'ring-2 ring-green-500' : '' ?>">
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-lg bg-green-100 flex items-center justify-center">
                    <i data-lucide="file-spreadsheet" class="h-5 w-5 text-green-600"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-green-600"><?= $docStats['documents'] ?? 0 ?></p>
                    <p class="text-sm text-gray-500">Tài liệu</p>
                </div>
            </div>
        </a>
        <a href="/php/admin/documents.php?type=other" 
           class="bg-white rounded-xl p-4 border border-gray-100 hover:border-gray-300 hover:shadow-md transition-all cursor-pointer <?= ($filters['type'] ?? '') === 'other' ? 'ring-2 ring-gray-500' : '' ?>">
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-lg bg-gray-100 flex items-center justify-center">
                    <i data-lucide="file" class="h-5 w-5 text-gray-600"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-600"><?= $docStats['others'] ?? 0 ?></p>
                    <p class="text-sm text-gray-500">Khác</p>
                </div>
            </div>
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl p-4 border border-gray-100">
        <form method="GET" class="flex flex-wrap items-center gap-4">
            <!-- Search -->
            <div class="flex-1 min-w-[250px]">
                <div class="relative">
                    <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400"></i>
                    <input type="text" name="search" value="<?= View::e($filters['search'] ?? '') ?>" 
                           placeholder="Tìm kiếm theo tên file..."
                           class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
            </div>
            
            <!-- Project Filter -->
            <div class="min-w-[180px]">
                <select name="project" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 bg-white">
                    <option value="">Tất cả dự án</option>
                    <?php foreach ($projects ?? [] as $project): ?>
                    <option value="<?= $project['id'] ?>" <?= ($filters['project'] ?? '') == $project['id'] ? 'selected' : '' ?>>
                        <?= View::e($project['name']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <!-- File Type Filter -->
            <div class="min-w-[150px]">
                <select name="type" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 bg-white">
                    <option value="">Tất cả loại</option>
                    <option value="image" <?= ($filters['type'] ?? '') === 'image' ? 'selected' : '' ?>>Hình ảnh</option>
                    <option value="pdf" <?= ($filters['type'] ?? '') === 'pdf' ? 'selected' : '' ?>>PDF</option>
                    <option value="document" <?= ($filters['type'] ?? '') === 'document' ? 'selected' : '' ?>>Tài liệu</option>
                    <option value="archive" <?= ($filters['type'] ?? '') === 'archive' ? 'selected' : '' ?>>File nén</option>
                </select>
            </div>
            
            <!-- Submit Button -->
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                <i data-lucide="filter" class="h-5 w-5"></i>
            </button>
            
            <!-- Clear Filters -->
            <?php if (!empty($filters['search']) || !empty($filters['project']) || !empty($filters['type'])): ?>
            <a href="/php/admin/documents.php" class="px-4 py-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                Xóa bộ lọc
            </a>
            <?php endif; ?>
        </form>
        
        <!-- Active Filters Display -->
        <?php if (!empty($filters['search']) || !empty($filters['project']) || !empty($filters['type'])): ?>
        <div class="flex flex-wrap items-center gap-2 mt-3 pt-3 border-t border-gray-100">
            <span class="text-sm text-gray-500">Đang lọc:</span>
            
            <?php if (!empty($filters['search'])): ?>
            <span class="inline-flex items-center gap-1 px-2 py-1 bg-blue-100 text-blue-700 rounded-full text-xs">
                <i data-lucide="search" class="h-3 w-3"></i>
                "<?= View::e($filters['search']) ?>"
            </span>
            <?php endif; ?>
            
            <?php if (!empty($filters['project'])): 
                $projectName = '';
                foreach ($projects ?? [] as $p) {
                    if ($p['id'] == $filters['project']) {
                        $projectName = $p['name'];
                        break;
                    }
                }
            ?>
            <span class="inline-flex items-center gap-1 px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs">
                <i data-lucide="folder" class="h-3 w-3"></i>
                <?= View::e($projectName) ?>
            </span>
            <?php endif; ?>
            
            <?php if (!empty($filters['type'])): 
                $typeLabels = ['image' => 'Hình ảnh', 'pdf' => 'PDF', 'document' => 'Tài liệu', 'archive' => 'File nén'];
            ?>
            <span class="inline-flex items-center gap-1 px-2 py-1 bg-purple-100 text-purple-700 rounded-full text-xs">
                <i data-lucide="file" class="h-3 w-3"></i>
                <?= $typeLabels[$filters['type']] ?? $filters['type'] ?>
            </span>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>

    <!-- Documents Table -->
    <div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Tên file</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Dự án</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Kích thước</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Người tải</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Ngày tải</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php if (!empty($documents)): ?>
                        <?php foreach ($documents as $doc): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="h-10 w-10 rounded-lg bg-blue-100 flex items-center justify-center">
                                        <i data-lucide="<?= getFileIcon($doc['mime_type'] ?? '') ?>" class="h-5 w-5 text-blue-600"></i>
                                    </div>
                                    <div>
                                        <span class="font-medium text-gray-900"><?= View::e($doc['name']) ?></span>
                                        <p class="text-xs text-gray-500"><?= View::e($doc['mime_type'] ?? 'Unknown') ?></p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                <?php if ($doc['project_name']): ?>
                                <a href="/php/admin/documents.php?project=<?= $doc['project_id'] ?>" 
                                   class="inline-flex items-center gap-1.5 hover:text-blue-600">
                                    <span class="h-2.5 w-2.5 rounded-full" style="background-color: <?= $doc['project_color'] ?? '#3b82f6' ?>"></span>
                                    <?= View::e($doc['project_name']) ?>
                                </a>
                                <?php else: ?>
                                <span class="text-gray-400">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                <?= formatFileSize($doc['file_size'] ?? 0) ?>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                <?= View::e($doc['uploader_name'] ?? '-') ?>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                <?= date('d/m/Y H:i', strtotime($doc['created_at'])) ?>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    <?php if (!empty($doc['file_path'])): ?>
                                    <a href="/php/<?= View::e($doc['file_path']) ?>" download
                                       class="p-2 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                                       title="Tải xuống">
                                        <i data-lucide="download" class="h-4 w-4"></i>
                                    </a>
                                    <?php endif; ?>
                                    <button onclick="deleteDocument('<?= $doc['id'] ?>', '<?= View::e($doc['name']) ?>')"
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
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                <i data-lucide="file-text" class="h-12 w-12 mx-auto mb-3 text-gray-300"></i>
                                <p>Chưa có tài liệu nào</p>
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

<script>
async function deleteDocument(docId, docName) {
    if (!confirm(`Bạn có chắc muốn xóa tài liệu "${docName}"?`)) return;
    
    try {
        const response = await fetch('/php/api/delete-document.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `document_id=${docId}`
        });
        const data = await response.json();
        if (data.success) {
            showToast('Đã xóa tài liệu', 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            showToast(data.error || 'Có lỗi xảy ra', 'error');
        }
    } catch (e) {
        showToast('Có lỗi xảy ra', 'error');
    }
}
</script>

<?php View::endSection(); ?>
