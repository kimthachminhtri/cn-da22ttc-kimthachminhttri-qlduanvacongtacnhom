<?php
/**
 * Documents View
 */
use Core\View;
use Core\Session;
use Core\Permission;

$userRole = Session::get('user_role', 'guest');
$userId = Session::get('user_id');
$viewMode = $_GET['view'] ?? 'grid';
$currentFolder = $_GET['folder'] ?? null;
$searchQuery = $_GET['q'] ?? '';

// Breadcrumbs được truyền từ controller
$breadcrumbs = $breadcrumbs ?? [];

View::section('content');
?>

<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Tài liệu</h1>
            <p class="text-gray-500">Quản lý và chia sẻ tài liệu dự án</p>
        </div>
        <div class="flex items-center gap-2">
            <?php if (Permission::can($userRole, 'documents.create')): ?>
            <button onclick="openModal('create-folder-modal')" 
                    class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50">
                <i data-lucide="folder-plus" class="h-4 w-4"></i>
                Thư mục mới
            </button>
            <button onclick="openModal('upload-modal')" 
                    class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-primary rounded-lg hover:bg-primary/90">
                <i data-lucide="upload" class="h-4 w-4"></i>
                Tải lên
            </button>
            <?php endif; ?>
        </div>
    </div>

    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-sm">
        <a href="/php/documents.php" class="text-gray-500 hover:text-primary flex items-center gap-1">
            <i data-lucide="home" class="h-4 w-4"></i>
            Tài liệu
        </a>
        <?php foreach ($breadcrumbs as $crumb): ?>
        <span class="text-gray-400">/</span>
        <a href="/php/documents.php?folder=<?= View::e($crumb['id']) ?>" class="text-gray-500 hover:text-primary">
            <?= View::e($crumb['name']) ?>
        </a>
        <?php endforeach; ?>
    </nav>

    <!-- Toolbar -->
    <div class="flex items-center justify-between gap-4">
        <form method="GET" class="relative flex-1 max-w-md">
            <?php if ($currentFolder): ?>
            <input type="hidden" name="folder" value="<?= View::e($currentFolder) ?>">
            <?php endif; ?>
            <i data-lucide="search" class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400"></i>
            <input type="text" name="q" placeholder="Tìm kiếm tài liệu..." value="<?= View::e($searchQuery) ?>"
                   class="w-full pl-9 pr-4 py-2 rounded-lg border border-gray-300 text-sm focus:border-primary focus:ring-1 focus:ring-primary">
        </form>
        <div class="flex items-center gap-2">
            <select onchange="filterByType(this.value)" class="px-3 py-2 text-sm border border-gray-200 rounded-lg">
                <option value="">Tất cả loại</option>
                <option value="folder">Thư mục</option>
                <option value="file">Tệp tin</option>
            </select>
            <div class="flex items-center gap-1 border border-gray-200 rounded-lg p-1 bg-white">
                <a href="?view=grid<?= $currentFolder ? '&folder=' . View::e($currentFolder) : '' ?>" 
                   class="p-2 rounded <?= $viewMode === 'grid' ? 'bg-gray-100' : 'hover:bg-gray-50' ?>">
                    <i data-lucide="grid-3x3" class="h-4 w-4"></i>
                </a>
                <a href="?view=list<?= $currentFolder ? '&folder=' . View::e($currentFolder) : '' ?>" 
                   class="p-2 rounded <?= $viewMode === 'list' ? 'bg-gray-100' : 'hover:bg-gray-50' ?>">
                    <i data-lucide="list" class="h-4 w-4"></i>
                </a>
            </div>
        </div>
    </div>
    
    <!-- Documents Grid/List -->
    <?php if (!empty($documents)): ?>
        <?php if ($viewMode === 'grid'): ?>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
            <?php foreach ($documents as $doc): ?>
            <?php
            $isFolder = ($doc['type'] ?? '') === 'folder';
            $ext = strtolower(pathinfo($doc['name'] ?? '', PATHINFO_EXTENSION));
            $fileIcons = ['pdf' => 'file-text', 'doc' => 'file-text', 'docx' => 'file-text', 'xls' => 'file-spreadsheet', 'xlsx' => 'file-spreadsheet', 'ppt' => 'presentation', 'pptx' => 'presentation', 'jpg' => 'image', 'jpeg' => 'image', 'png' => 'image', 'gif' => 'image'];
            $fileColors = ['pdf' => 'text-red-500 bg-red-100', 'doc' => 'text-blue-500 bg-blue-100', 'docx' => 'text-blue-500 bg-blue-100', 'xls' => 'text-green-500 bg-green-100', 'xlsx' => 'text-green-500 bg-green-100', 'ppt' => 'text-orange-500 bg-orange-100', 'pptx' => 'text-orange-500 bg-orange-100', 'jpg' => 'text-purple-500 bg-purple-100', 'jpeg' => 'text-purple-500 bg-purple-100', 'png' => 'text-purple-500 bg-purple-100'];
            
            if ($isFolder) {
                $icon = 'folder';
                $colorClass = 'text-amber-500 bg-amber-100';
            } else {
                $icon = $fileIcons[$ext] ?? 'file';
                $colorClass = $fileColors[$ext] ?? 'text-gray-500 bg-gray-100';
            }
            $canDelete = Permission::can($userRole, 'documents.delete') || ($doc['uploaded_by'] ?? '') === $userId;
            $isStarred = $doc['is_starred'] ?? false;
            ?>
            <div class="group relative bg-white border border-gray-200 rounded-xl p-4 hover:shadow-md transition-all <?= $isFolder ? 'cursor-pointer' : '' ?>"
                 <?= $isFolder ? 'onclick="openFolder(\'' . View::e($doc['id']) . '\')"' : '' ?>>
                
                <!-- Actions -->
                <div class="absolute top-2 right-2 flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                    <button onclick="event.stopPropagation(); toggleStar('<?= View::e($doc['id']) ?>', this)" 
                            class="p-1.5 rounded-full hover:bg-yellow-100 <?= $isStarred ? 'text-yellow-500' : 'text-gray-400' ?>">
                        <i data-lucide="star" class="h-4 w-4 <?= $isStarred ? 'fill-current' : '' ?>"></i>
                    </button>
                    <?php if (!$isFolder && !empty($doc['file_path'])): ?>
                    <a href="/php/<?= View::e($doc['file_path']) ?>" download onclick="event.stopPropagation()"
                       class="p-1.5 rounded-full hover:bg-blue-100 text-gray-400 hover:text-blue-500">
                        <i data-lucide="download" class="h-4 w-4"></i>
                    </a>
                    <?php endif; ?>
                    <?php if ($canDelete): ?>
                    <button onclick="event.stopPropagation(); deleteDocument('<?= View::e($doc['id']) ?>')" 
                            class="p-1.5 rounded-full hover:bg-red-100 text-gray-400 hover:text-red-500">
                        <i data-lucide="trash-2" class="h-4 w-4"></i>
                    </button>
                    <?php endif; ?>
                </div>
                
                <!-- Star indicator -->
                <?php if ($isStarred): ?>
                <div class="absolute top-2 left-2">
                    <i data-lucide="star" class="h-4 w-4 text-yellow-500 fill-current"></i>
                </div>
                <?php endif; ?>
                
                <div class="flex items-center justify-center h-16 mb-3">
                    <div class="p-3 rounded-lg <?= $colorClass ?>">
                        <i data-lucide="<?= $icon ?>" class="h-8 w-8"></i>
                    </div>
                </div>
                
                <h3 class="font-medium text-sm text-gray-900 truncate mb-1" title="<?= View::e($doc['name'] ?? '') ?>">
                    <?= View::e($doc['name'] ?? '') ?>
                </h3>
                <p class="text-xs text-gray-500"><?= View::e($doc['uploader_name'] ?? 'Unknown') ?></p>
                <?php if (!$isFolder && isset($doc['file_size'])): ?>
                <p class="text-xs text-gray-400 mt-1"><?= formatFileSize($doc['file_size']) ?></p>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <!-- List View -->
        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50 text-left text-sm text-gray-500">
                        <th class="px-4 py-3 font-medium">Tên</th>
                        <th class="px-4 py-3 font-medium">Người tạo</th>
                        <th class="px-4 py-3 font-medium">Kích thước</th>
                        <th class="px-4 py-3 font-medium">Cập nhật</th>
                        <th class="px-4 py-3 font-medium w-24">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php foreach ($documents as $doc): ?>
                    <?php
                    $isFolder = ($doc['type'] ?? '') === 'folder';
                    $canDelete = Permission::can($userRole, 'documents.delete') || ($doc['uploaded_by'] ?? '') === $userId;
                    $isStarred = $doc['is_starred'] ?? false;
                    ?>
                    <tr class="hover:bg-gray-50 <?= $isFolder ? 'cursor-pointer' : '' ?>"
                        <?= $isFolder ? 'onclick="openFolder(\'' . View::e($doc['id']) . '\')"' : '' ?>>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <?php if ($isStarred): ?>
                                <i data-lucide="star" class="h-4 w-4 text-yellow-500 fill-current"></i>
                                <?php endif; ?>
                                <i data-lucide="<?= $isFolder ? 'folder' : 'file' ?>" class="h-4 w-4 <?= $isFolder ? 'text-amber-500' : 'text-gray-400' ?>"></i>
                                <span class="font-medium text-gray-900"><?= View::e($doc['name'] ?? '') ?></span>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-500"><?= View::e($doc['uploader_name'] ?? '') ?></td>
                        <td class="px-4 py-3 text-sm text-gray-500">
                            <?= $isFolder ? '-' : formatFileSize($doc['file_size'] ?? 0) ?>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-500"><?= date('d/m/Y', strtotime($doc['updated_at'] ?? 'now')) ?></td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-1">
                                <button onclick="event.stopPropagation(); toggleStar('<?= View::e($doc['id']) ?>', this)" 
                                        class="p-1.5 rounded hover:bg-gray-100 <?= $isStarred ? 'text-yellow-500' : 'text-gray-400' ?>">
                                    <i data-lucide="star" class="h-4 w-4 <?= $isStarred ? 'fill-current' : '' ?>"></i>
                                </button>
                                <?php if (!$isFolder && !empty($doc['file_path'])): ?>
                                <a href="/php/<?= View::e($doc['file_path']) ?>" download onclick="event.stopPropagation()"
                                   class="p-1.5 rounded hover:bg-gray-100 text-gray-400 hover:text-blue-500">
                                    <i data-lucide="download" class="h-4 w-4"></i>
                                </a>
                                <?php endif; ?>
                                <?php if ($canDelete): ?>
                                <button onclick="event.stopPropagation(); deleteDocument('<?= View::e($doc['id']) ?>')" 
                                        class="p-1.5 rounded hover:bg-gray-100 text-gray-400 hover:text-red-500">
                                    <i data-lucide="trash-2" class="h-4 w-4"></i>
                                </button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    <?php else: ?>
        <div class="text-center py-16 bg-white rounded-xl border border-gray-200">
            <i data-lucide="folder-open" class="h-12 w-12 mx-auto text-gray-300 mb-4"></i>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Không có tài liệu</h3>
            <p class="text-gray-500 mb-4">Tải lên tài liệu đầu tiên của bạn</p>
            <?php if (Permission::can($userRole, 'documents.create')): ?>
            <button onclick="openModal('upload-modal')" class="px-4 py-2 text-sm font-medium text-white bg-primary rounded-lg hover:bg-primary/90">
                <i data-lucide="upload" class="h-4 w-4 inline mr-1"></i>
                Tải lên ngay
            </button>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Create Folder Modal -->
<div id="create-folder-modal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-black/50" onclick="closeModal('create-folder-modal')"></div>
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-md" onclick="event.stopPropagation()">
            <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4">
                <h2 class="text-lg font-semibold text-gray-900">Tạo thư mục mới</h2>
                <button onclick="closeModal('create-folder-modal')" class="text-gray-400 hover:text-gray-600">
                    <i data-lucide="x" class="h-5 w-5"></i>
                </button>
            </div>
            <form action="/php/api/create-folder.php" method="POST" class="p-6 space-y-4">
                <?php if ($currentFolder): ?>
                <input type="hidden" name="parent_id" value="<?= View::e($currentFolder) ?>">
                <?php endif; ?>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tên thư mục *</label>
                    <input type="text" name="name" required placeholder="Nhập tên thư mục..."
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Dự án (tùy chọn)</label>
                    <select name="project_id" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                        <option value="">-- Không thuộc dự án --</option>
                        <?php foreach ($projects ?? [] as $project): ?>
                        <option value="<?= View::e($project['id']) ?>"><?= View::e($project['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="flex justify-end gap-3 pt-4">
                    <button type="button" onclick="closeModal('create-folder-modal')" 
                            class="px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-lg">Hủy</button>
                    <button type="submit" 
                            class="px-4 py-2 text-sm font-medium text-white bg-primary hover:bg-primary/90 rounded-lg">Tạo thư mục</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Upload Modal -->
<div id="upload-modal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-black/50" onclick="closeModal('upload-modal')"></div>
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-md" onclick="event.stopPropagation()">
            <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4">
                <h2 class="text-lg font-semibold text-gray-900">Tải lên tài liệu</h2>
                <button onclick="closeModal('upload-modal')" class="text-gray-400 hover:text-gray-600">
                    <i data-lucide="x" class="h-5 w-5"></i>
                </button>
            </div>
            <form action="/php/api/upload-document.php" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
                <?php if ($currentFolder): ?>
                <input type="hidden" name="parent_id" value="<?= View::e($currentFolder) ?>">
                <?php endif; ?>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Chọn file *</label>
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-primary transition-colors">
                        <input type="file" name="files[]" multiple required id="file-input" class="hidden"
                               accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.jpg,.jpeg,.png,.gif,.zip,.txt">
                        <label for="file-input" class="cursor-pointer">
                            <i data-lucide="upload-cloud" class="h-10 w-10 mx-auto text-gray-400 mb-2"></i>
                            <p class="text-sm text-gray-600">Click để chọn file hoặc kéo thả vào đây</p>
                            <p class="text-xs text-gray-400 mt-1">PDF, Word, Excel, PowerPoint, Ảnh, ZIP (tối đa 50MB)</p>
                        </label>
                    </div>
                    <div id="file-list" class="mt-2 space-y-1 text-sm"></div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Dự án (tùy chọn)</label>
                    <select name="project_id" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                        <option value="">-- Không thuộc dự án --</option>
                        <?php foreach ($projects ?? [] as $project): ?>
                        <option value="<?= View::e($project['id']) ?>"><?= View::e($project['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="flex justify-end gap-3 pt-4">
                    <button type="button" onclick="closeModal('upload-modal')" 
                            class="px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-lg">Hủy</button>
                    <button type="submit" 
                            class="px-4 py-2 text-sm font-medium text-white bg-primary hover:bg-primary/90 rounded-lg">Tải lên</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Open folder
function openFolder(folderId) {
    window.location.href = '/php/documents.php?folder=' + folderId;
}

// Delete document
function deleteDocument(docId) {
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

// Toggle star
function toggleStar(docId, btn) {
    fetch('/php/api/toggle-star.php', {
        method: 'POST',
        headers: { 
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ document_id: docId })
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

// Filter by type
function filterByType(type) {
    const url = new URL(window.location);
    if (type) {
        url.searchParams.set('type', type);
    } else {
        url.searchParams.delete('type');
    }
    window.location = url;
}

// File input preview
document.getElementById('file-input')?.addEventListener('change', function() {
    const fileList = document.getElementById('file-list');
    fileList.innerHTML = '';
    
    for (const file of this.files) {
        const div = document.createElement('div');
        div.className = 'flex items-center gap-2 text-gray-600';
        div.innerHTML = '<i data-lucide="file" class="h-4 w-4"></i>' + file.name + ' (' + formatFileSize(file.size) + ')';
        fileList.appendChild(div);
    }
    
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
});

function formatFileSize(bytes) {
    if (bytes === 0) return '0 B';
    const k = 1024;
    const sizes = ['B', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
}
</script>

<?php 
// Helper function
function formatFileSize($bytes) {
    if ($bytes == 0) return '0 B';
    $k = 1024;
    $sizes = ['B', 'KB', 'MB', 'GB'];
    $i = floor(log($bytes) / log($k));
    return round($bytes / pow($k, $i), 1) . ' ' . $sizes[$i];
}

View::endSection(); 
?>
