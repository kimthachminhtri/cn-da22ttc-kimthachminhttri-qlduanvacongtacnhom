<?php
/**
 * Task Detail View
 */
use Core\View;
use Core\Session;
use Core\Permission;

$userRole = Session::get('user_role', 'guest');
$userId = Session::get('user_id');

// Check permission
$isAssigned = false;
foreach ($task['assignees'] ?? [] as $assignee) {
    if ($assignee['id'] === $userId) {
        $isAssigned = true;
        break;
    }
}

// Full edit: Admin, Manager (general or project), Creator
// Note: Project Manager check here is simplified via userRole 'manager' or 'admin'
// For stricter project-level check, we rely on API. Here it's mainly for UX.
$hasFullEdit = Permission::can($userRole, 'tasks.edit') || ($task['created_by'] ?? '') === $userId;

$canEdit = $hasFullEdit || $isAssigned;
$canDelete = Permission::can($userRole, 'tasks.delete') || ($task['created_by'] ?? '') === $userId;

View::section('content');
?>

<div class="max-w-4xl mx-auto space-y-6">
    <!-- Task Header -->
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <div class="flex items-center gap-4 mb-4">
            <a href="/php/tasks.php" class="text-gray-400 hover:text-gray-600">
                <i data-lucide="arrow-left" class="h-5 w-5"></i>
            </a>
            <div class="flex-1">
                <h1 class="text-xl font-semibold text-gray-900"><?= View::e($task['title'] ?? '') ?></h1>
                <div class="flex items-center gap-3 mt-2">
                    <?php
                    $statusClasses = [
                        'backlog' => 'bg-gray-100 text-gray-800',
                        'todo' => 'bg-blue-100 text-blue-800',
                        'in_progress' => 'bg-yellow-100 text-yellow-800',
                        'in_review' => 'bg-purple-100 text-purple-800',
                        'done' => 'bg-green-100 text-green-800',
                    ];
                    $priorityClasses = [
                        'low' => 'bg-gray-100 text-gray-800',
                        'medium' => 'bg-blue-100 text-blue-800',
                        'high' => 'bg-orange-100 text-orange-800',
                        'urgent' => 'bg-red-100 text-red-800',
                    ];
                    ?>
                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium <?= $statusClasses[$task['status'] ?? 'todo'] ?>">
                        <?= View::e(getStatusName($task['status'] ?? 'todo')) ?>
                    </span>
                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium <?= $priorityClasses[$task['priority'] ?? 'medium'] ?>">
                        <?= View::e(getPriorityName($task['priority'] ?? 'medium')) ?>
                    </span>
                    <?php if (!empty($project)): ?>
                    <a href="/php/project-detail.php?id=<?= View::e($project['id']) ?>" class="flex items-center gap-1 text-sm text-gray-500 hover:text-primary">
                        <span class="h-2 w-2 rounded-full" style="background-color: <?= View::e($project['color'] ?? '#6366f1') ?>"></span>
                        <?= View::e($project['name']) ?>
                    </a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <?php if ($canEdit): ?>
                <button onclick="openModal('edit-task-modal')" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50">
                    <i data-lucide="edit" class="h-4 w-4 inline mr-1"></i>
                    Chỉnh sửa
                </button>
                <?php endif; ?>
                <?php if ($canDelete): ?>
                <button onclick="confirmDeleteTask()" class="px-4 py-2 text-sm font-medium text-red-600 bg-white border border-red-200 rounded-lg hover:bg-red-50">
                    <i data-lucide="trash-2" class="h-4 w-4 inline mr-1"></i>
                    Xóa
                </button>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- ... (No changes here) -->
    
    <div class="grid gap-6 lg:grid-cols-3">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Description -->
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h3 class="font-semibold text-gray-900 mb-3">Mô tả</h3>
                <p class="text-gray-600"><?= View::e($task['description'] ?? 'Chưa có mô tả') ?></p>
            </div>
            
            <!-- Checklist -->
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-semibold text-gray-900">Checklist</h3>
                    <?php 
                    $checklist = $task['checklist'] ?? [];
                    $completedCount = count(array_filter($checklist, fn($c) => $c['is_completed'] ?? false));
                    ?>
                    <span class="text-sm text-gray-500"><?= $completedCount ?>/<?= count($checklist) ?></span>
                </div>
                
                <div class="h-2 rounded-full bg-gray-100 mb-4 overflow-hidden">
                    <div class="h-full rounded-full bg-green-500" style="width: <?= count($checklist) > 0 ? round($completedCount / count($checklist) * 100) : 0 ?>%"></div>
                </div>
                
                <div class="space-y-2" id="checklist-items">
                    <?php foreach ($checklist as $item): ?>
                    <div class="checklist-item group flex items-center gap-3 p-2 rounded-lg hover:bg-gray-50" data-item-id="<?= View::e($item['id']) ?>">
                        <input type="checkbox" <?= ($item['is_completed'] ?? false) ? 'checked' : '' ?>
                               data-item-id="<?= View::e($item['id']) ?>"
                               class="checklist-checkbox h-4 w-4 rounded border-gray-300 text-primary cursor-pointer">
                        <span class="checklist-title flex-1 text-sm <?= ($item['is_completed'] ?? false) ? 'text-gray-400 line-through' : 'text-gray-900' ?>" 
                              ondblclick="editChecklistItem('<?= View::e($item['id']) ?>', this)">
                            <?= View::e($item['title'] ?? '') ?>
                        </span>
                        <input type="text" class="checklist-edit-input hidden flex-1 px-2 py-1 text-sm border border-gray-300 rounded focus:border-primary focus:ring-1 focus:ring-primary"
                               value="<?= View::e($item['title'] ?? '') ?>"
                               onblur="saveChecklistTitle('<?= View::e($item['id']) ?>', this)"
                               onkeydown="handleChecklistEditKey(event, '<?= View::e($item['id']) ?>', this)">
                        <button onclick="editChecklistItem('<?= View::e($item['id']) ?>', this.parentElement.querySelector('.checklist-title'))" 
                                class="opacity-0 group-hover:opacity-100 p-1 text-gray-400 hover:text-blue-500 hover:bg-blue-50 rounded transition-opacity">
                            <i data-lucide="pencil" class="h-3 w-3"></i>
                        </button>
                        <button onclick="deleteChecklistItem('<?= View::e($item['id']) ?>')" 
                                class="opacity-0 group-hover:opacity-100 p-1 text-red-500 hover:bg-red-50 rounded transition-opacity">
                            <i data-lucide="x" class="h-3 w-3"></i>
                        </button>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="mt-4 flex items-center gap-2">
                    <input type="text" placeholder="Thêm mục mới..." id="checklist-input"
                           class="checklist-input flex-1 px-3 py-2 text-sm border border-gray-300 rounded-lg focus:border-primary focus:ring-1 focus:ring-primary">
                    <button class="checklist-add-btn px-3 py-2 text-sm font-medium text-white bg-primary rounded-lg hover:bg-primary/90">
                        <i data-lucide="plus" class="h-4 w-4"></i>
                    </button>
                </div>
            </div>
            
            <!-- Comments Section - Redesigned -->
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="font-semibold text-gray-900 flex items-center gap-2">
                        <i data-lucide="message-circle" class="h-5 w-5 text-gray-400"></i>
                        Bình luận
                        <?php 
                        $commentCount = count(array_filter($task['comments'] ?? [], fn($c) => empty($c['parent_id'])));
                        if ($commentCount > 0): 
                        ?>
                        <span class="text-xs font-normal text-gray-500 bg-gray-100 px-2 py-0.5 rounded-full"><?= $commentCount ?></span>
                        <?php endif; ?>
                    </h3>
                </div>
                
                <!-- Comment Input - Đặt ở đầu -->
                <div class="mb-6 pb-6 border-b border-gray-100">
                    <div class="flex gap-3">
                        <div class="h-10 w-10 rounded-full bg-primary/10 flex items-center justify-center flex-shrink-0">
                            <span class="text-sm font-medium text-primary"><?= strtoupper(substr(Session::get('user_name', 'U'), 0, 1)) ?></span>
                        </div>
                        <div class="flex-1">
                            <textarea placeholder="Viết bình luận của bạn..." rows="3" id="comment-input"
                                      class="comment-input w-full px-4 py-3 text-sm border border-gray-200 rounded-xl focus:border-primary focus:ring-2 focus:ring-primary/20 resize-none transition-all"></textarea>
                            <div class="flex justify-end mt-3">
                                <button class="comment-submit-btn px-5 py-2.5 text-sm font-medium text-white bg-primary rounded-lg hover:bg-primary/90 transition-colors flex items-center gap-2">
                                    <i data-lucide="send" class="h-4 w-4"></i>
                                    Gửi bình luận
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Comments List -->
                <div class="space-y-0" id="comments-list">
                    <?php 
                    // Hàm đệ quy render nested comments
                    function renderNestedComments($comments, $userId, $depth = 0, $maxDepth = 10) {
                        if (empty($comments) || $depth > $maxDepth) return;
                        
                        $totalComments = count($comments);
                        $currentIndex = 0;
                        
                        // Màu gradient cho các cấp độ khác nhau
                        $depthColors = [
                            0 => ['bg' => 'from-gray-100 to-gray-200', 'text' => 'text-gray-600', 'border' => 'border-primary/20'],
                            1 => ['bg' => 'from-blue-50 to-blue-100', 'text' => 'text-blue-600', 'border' => 'border-blue-200'],
                            2 => ['bg' => 'from-green-50 to-green-100', 'text' => 'text-green-600', 'border' => 'border-green-200'],
                            3 => ['bg' => 'from-purple-50 to-purple-100', 'text' => 'text-purple-600', 'border' => 'border-purple-200'],
                            4 => ['bg' => 'from-orange-50 to-orange-100', 'text' => 'text-orange-600', 'border' => 'border-orange-200'],
                        ];
                        $colorIndex = min($depth, 4);
                        $colors = $depthColors[$colorIndex];
                        
                        foreach ($comments as $comment):
                            $currentIndex++;
                            $isLast = ($currentIndex === $totalComments);
                            $avatarSize = $depth === 0 ? 'h-10 w-10' : ($depth === 1 ? 'h-8 w-8' : 'h-7 w-7');
                            $textSize = $depth === 0 ? 'text-sm' : 'text-xs';
                            $paddingClass = $depth === 0 ? 'px-4 py-3' : 'px-3 py-2';
                    ?>
                    <div class="comment-item <?= $depth === 0 && !$isLast ? 'border-b border-gray-100 pb-5 mb-5' : ($depth > 0 ? 'mt-3' : '') ?>" 
                         data-comment-id="<?= View::e($comment['id']) ?>" 
                         data-depth="<?= $depth ?>">
                        <div class="flex gap-<?= $depth === 0 ? '4' : '3' ?>">
                            <!-- Avatar -->
                            <div class="<?= $avatarSize ?> rounded-full bg-gradient-to-br <?= $colors['bg'] ?> flex items-center justify-center flex-shrink-0 shadow-sm">
                                <span class="<?= $textSize ?> font-semibold <?= $colors['text'] ?>"><?= strtoupper(substr($comment['full_name'] ?? 'U', 0, 1)) ?></span>
                            </div>
                            
                            <div class="flex-1 min-w-0">
                                <!-- Header -->
                                <div class="flex items-center justify-between mb-1">
                                    <div class="flex items-center gap-2">
                                        <span class="font-semibold <?= $textSize ?> text-gray-900"><?= View::e($comment['full_name'] ?? '') ?></span>
                                        <span class="text-xs text-gray-400"><?= date('d/m/Y H:i', strtotime($comment['created_at'] ?? 'now')) ?></span>
                                        <?php if ($depth > 0): ?>
                                        <span class="text-xs text-gray-300">• Cấp <?= $depth + 1 ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <?php if (($comment['created_by'] ?? '') === $userId): ?>
                                        <button onclick="editComment('<?= View::e($comment['id']) ?>', this)" 
                                                class="p-1 text-gray-400 hover:text-blue-500 hover:bg-blue-50 rounded transition-all" title="Chỉnh sửa">
                                            <i data-lucide="pencil" class="h-3 w-3"></i>
                                        </button>
                                        <button onclick="deleteComment('<?= View::e($comment['id']) ?>')" 
                                                class="p-1 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded transition-all" title="Xóa">
                                            <i data-lucide="trash-2" class="h-3 w-3"></i>
                                        </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <!-- Content -->
                                <div class="comment-content <?= $textSize ?> text-gray-700 leading-relaxed <?= $depth === 0 ? 'bg-gray-50 rounded-xl' : '' ?> <?= $paddingClass ?>">
                                    <?= nl2br(preg_replace('/@(\S+)/', '<span class="text-primary font-medium bg-primary/10 px-1 rounded">@$1</span>', View::e($comment['content'] ?? ''))) ?>
                                </div>
                                
                                <!-- Reply button -->
                                <?php if ($depth < $maxDepth): ?>
                                <button onclick="showReplyForm('<?= View::e($comment['id']) ?>')" 
                                        class="mt-1.5 text-xs text-gray-400 hover:text-primary flex items-center gap-1 transition-colors">
                                    <i data-lucide="corner-down-right" class="h-3 w-3"></i>
                                    Trả lời
                                </button>
                                <?php endif; ?>
                                
                                <!-- Edit form -->
                                <div class="comment-edit-form hidden mt-2">
                                    <textarea class="edit-comment-input w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:border-primary focus:ring-2 focus:ring-primary/20" rows="2"><?= View::e($comment['content'] ?? '') ?></textarea>
                                    <div class="flex justify-end gap-2 mt-2">
                                        <button onclick="cancelEditComment(this)" class="px-3 py-1.5 text-xs text-gray-600 hover:bg-gray-100 rounded-lg">Hủy</button>
                                        <button onclick="saveComment('<?= View::e($comment['id']) ?>', this)" class="px-3 py-1.5 text-xs text-white bg-primary hover:bg-primary/90 rounded-lg">Lưu</button>
                                    </div>
                                </div>
                                
                                <!-- Reply form -->
                                <?php if ($depth < $maxDepth): ?>
                                <div class="reply-form hidden mt-3" id="reply-form-<?= View::e($comment['id']) ?>">
                                    <div class="flex gap-2 bg-gray-50 rounded-lg p-3">
                                        <div class="h-6 w-6 rounded-full bg-primary/10 flex items-center justify-center flex-shrink-0">
                                            <span class="text-xs font-medium text-primary"><?= strtoupper(substr(\Core\Session::get('user_name', 'U'), 0, 1)) ?></span>
                                        </div>
                                        <div class="flex-1">
                                            <textarea placeholder="Trả lời @<?= View::e($comment['full_name'] ?? '') ?>..." rows="2" 
                                                      class="reply-input w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:border-primary focus:ring-2 focus:ring-primary/20 bg-white"></textarea>
                                            <div class="flex justify-end gap-2 mt-2">
                                                <button onclick="hideReplyForm('<?= View::e($comment['id']) ?>')" class="px-3 py-1.5 text-xs text-gray-600 hover:bg-gray-200 rounded-lg transition-colors">Hủy</button>
                                                <button onclick="submitReply('<?= View::e($comment['id']) ?>', '<?= View::e($comment['full_name'] ?? '') ?>')" class="px-3 py-1.5 text-xs text-white bg-primary hover:bg-primary/90 rounded-lg transition-colors">Gửi</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>
                                
                                <!-- Nested Replies (đệ quy) -->
                                <?php 
                                $replies = $comment['replies'] ?? [];
                                if (!empty($replies)): 
                                ?>
                                <div class="replies mt-3 ml-2 pl-3 border-l-2 <?= $colors['border'] ?> space-y-0">
                                    <?php if ($depth === 0): ?>
                                    <div class="text-xs text-gray-500 font-medium flex items-center gap-1 mb-2">
                                        <i data-lucide="corner-down-right" class="h-3 w-3"></i>
                                        <?= count($replies) ?> trả lời
                                    </div>
                                    <?php endif; ?>
                                    <?php renderNestedComments($replies, $userId, $depth + 1, $maxDepth); ?>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php 
                        endforeach;
                    }
                    
                    // Build comment tree từ flat array
                    function buildCommentTreeFromFlat($comments) {
                        $commentsById = [];
                        $rootComments = [];
                        
                        // Index tất cả comments
                        foreach ($comments as $comment) {
                            $comment['replies'] = [];
                            $commentsById[$comment['id']] = $comment;
                        }
                        
                        // Build tree
                        foreach ($commentsById as $id => &$comment) {
                            if (!empty($comment['parent_id']) && isset($commentsById[$comment['parent_id']])) {
                                $commentsById[$comment['parent_id']]['replies'][] = &$comment;
                            } elseif (empty($comment['parent_id'])) {
                                $rootComments[] = &$comment;
                            }
                        }
                        
                        // Sort: root mới nhất trước, replies cũ nhất trước
                        usort($rootComments, fn($a, $b) => strtotime($b['created_at']) - strtotime($a['created_at']));
                        
                        // Sort replies recursively
                        function sortReplies(&$comments) {
                            foreach ($comments as &$comment) {
                                if (!empty($comment['replies'])) {
                                    usort($comment['replies'], fn($a, $b) => strtotime($a['created_at']) - strtotime($b['created_at']));
                                    sortReplies($comment['replies']);
                                }
                            }
                        }
                        sortReplies($rootComments);
                        
                        return $rootComments;
                    }
                    
                    if (!empty($task['comments'])): 
                        $commentTree = buildCommentTreeFromFlat($task['comments']);
                        renderNestedComments($commentTree, $userId, 0, 10);
                    else: 
                    ?>
                    <div class="text-center py-8 text-gray-500">
                        <i data-lucide="message-circle" class="h-12 w-12 mx-auto mb-3 text-gray-300"></i>
                        <p class="text-sm">Chưa có bình luận nào</p>
                        <p class="text-xs text-gray-400 mt-1">Hãy là người đầu tiên bình luận!</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Details -->
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h3 class="font-semibold text-gray-900 mb-4">Chi tiết</h3>
                <dl class="space-y-4 text-sm">
                    <div>
                        <dt class="text-gray-500 mb-1">Người phụ trách</dt>
                        <dd>
                            <?php if (!empty($task['assignees'])): ?>
                            <div class="flex items-center gap-2">
                                <div class="h-6 w-6 rounded-full bg-gray-200"></div>
                                <span class="font-medium"><?= View::e($task['assignees'][0]['full_name'] ?? '') ?></span>
                            </div>
                            <?php else: ?>
                            <span class="text-gray-400">Chưa giao</span>
                            <?php endif; ?>
                        </dd>
                    </div>
                    
                    <div>
                        <dt class="text-gray-500 mb-1">Hạn hoàn thành</dt>
                        <dd class="font-medium">
                            <?php if (!empty($task['due_date'])): ?>
                            <?= date('d/m/Y', strtotime($task['due_date'])) ?>
                            <?php else: ?>
                            <span class="text-gray-400">Chưa đặt</span>
                            <?php endif; ?>
                        </dd>
                    </div>
                    
                    <div>
                        <dt class="text-gray-500 mb-1">Ngày tạo</dt>
                        <dd class="font-medium"><?= date('d/m/Y', strtotime($task['created_at'] ?? 'now')) ?></dd>
                    </div>
                </dl>
            </div>
            
            <!-- Actions -->
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h3 class="font-semibold text-gray-900 mb-3">Hành động</h3>
                <div class="space-y-2">
                    <button class="w-full flex items-center gap-2 px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-lg">
                        <i data-lucide="copy" class="h-4 w-4"></i>
                        Sao chép
                    </button>
                    <button class="w-full flex items-center gap-2 px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-lg">
                        <i data-lucide="archive" class="h-4 w-4"></i>
                        Lưu trữ
                    </button>
                    <?php if ($canDelete): ?>
                    <button onclick="confirmDeleteTask()" class="w-full flex items-center gap-2 px-3 py-2 text-sm text-red-600 hover:bg-red-50 rounded-lg">
                        <i data-lucide="trash-2" class="h-4 w-4"></i>
                        Xóa
                    </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Task Modal -->
<?php if ($canEdit): ?>
<div id="edit-task-modal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-black/50" onclick="closeModal('edit-task-modal')"></div>
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-lg" onclick="event.stopPropagation()">
            <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4">
                <h2 class="text-lg font-semibold text-gray-900">Chỉnh sửa công việc</h2>
                <button onclick="closeModal('edit-task-modal')" class="text-gray-400 hover:text-gray-600">
                    <i data-lucide="x" class="h-5 w-5"></i>
                </button>
            </div>
            <form id="edit-task-form" class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tiêu đề *</label>
                    <input type="text" name="title" required value="<?= View::e($task['title'] ?? '') ?>"
                           <?= !$hasFullEdit ? 'disabled' : '' ?>
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary disabled:bg-gray-100 disabled:text-gray-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mô tả</label>
                    <textarea name="description" rows="3"
                              <?= !$hasFullEdit ? 'disabled' : '' ?>
                              class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary disabled:bg-gray-100 disabled:text-gray-500"><?= View::e($task['description'] ?? '') ?></textarea>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Trạng thái</label>
                        <select name="status" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                            <option value="backlog" <?= ($task['status'] ?? '') === 'backlog' ? 'selected' : '' ?>>Chờ xử lý</option>
                            <option value="todo" <?= ($task['status'] ?? '') === 'todo' ? 'selected' : '' ?>>Cần làm</option>
                            <option value="in_progress" <?= ($task['status'] ?? '') === 'in_progress' ? 'selected' : '' ?>>Đang làm</option>
                            <option value="in_review" <?= ($task['status'] ?? '') === 'in_review' ? 'selected' : '' ?>>Đang xem xét</option>
                            <option value="done" <?= ($task['status'] ?? '') === 'done' ? 'selected' : '' ?>>Hoàn thành</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Độ ưu tiên</label>
                        <select name="priority" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm disabled:bg-gray-100 disabled:text-gray-500" <?= !$hasFullEdit ? 'disabled' : '' ?>>
                            <option value="low" <?= ($task['priority'] ?? '') === 'low' ? 'selected' : '' ?>>Thấp</option>
                            <option value="medium" <?= ($task['priority'] ?? '') === 'medium' ? 'selected' : '' ?>>Trung bình</option>
                            <option value="high" <?= ($task['priority'] ?? '') === 'high' ? 'selected' : '' ?>>Cao</option>
                            <option value="urgent" <?= ($task['priority'] ?? '') === 'urgent' ? 'selected' : '' ?>>Khẩn cấp</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ngày hết hạn</label>
                    <input type="date" name="due_date" value="<?= View::e($task['due_date'] ?? '') ?>"
                           <?= !$hasFullEdit ? 'disabled' : '' ?>
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm disabled:bg-gray-100 disabled:text-gray-500">
                </div>
                <div class="flex justify-end gap-3 pt-4">
                    <button type="button" onclick="closeModal('edit-task-modal')" 
                            class="px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-lg">Hủy</button>
                    <button type="submit" 
                            class="px-4 py-2 text-sm font-medium text-white bg-primary hover:bg-primary/90 rounded-lg">Lưu thay đổi</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<script>
const taskId = '<?= View::e($task['id'] ?? '') ?>';

// Delete task - Using ConfirmDialog
async function confirmDeleteTask() {
    const confirmed = await ConfirmDialog.confirmDelete('công việc này');
    if (!confirmed) return;
    
    LoadingState.showFullPage('Đang xóa...');
    
    try {
        const response = await fetch('/php/api/update-task.php?id=' + taskId, { 
            method: 'DELETE',
            headers: { 'Accept': 'application/json' }
        });
        const data = await response.json();
        
        if (data.success) {
            showToast('Đã xóa công việc thành công', 'success');
            setTimeout(() => window.location.href = '/php/tasks.php', 500);
        } else {
            showToast(data.error || 'Có lỗi xảy ra', 'error');
        }
    } catch (err) {
        showToast('Lỗi kết nối: ' + err.message, 'error');
    } finally {
        LoadingState.hideFullPage();
    }
}

// Edit task form
document.getElementById('edit-task-form')?.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const data = {
        task_id: taskId,
        title: formData.get('title'),
        description: formData.get('description'),
        status: formData.get('status'),
        priority: formData.get('priority'),
        due_date: formData.get('due_date')
    };
    
    fetch('/php/api/update-task.php', {
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
            showToast('Đã cập nhật công việc', 'success');
            setTimeout(() => location.reload(), 500);
        } else {
            showToast(data.error || 'Có lỗi xảy ra', 'error');
        }
    })
    .catch(err => showToast('Lỗi kết nối: ' + err.message, 'error'));
});

// Checklist - Toggle item
document.querySelectorAll('.checklist-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', function() {
        const itemId = this.dataset.itemId;
        if (itemId) {
            toggleChecklistItem(itemId);
        }
    });
});

function toggleChecklistItem(itemId) {
    fetch('/php/api/checklist.php', {
        method: 'PUT',
        headers: { 
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ item_id: itemId, action: 'toggle' })
    })
    .then(r => r.json())
    .then(data => {
        if (!data.success) {
            showToast(data.error || 'Có lỗi xảy ra', 'error');
            location.reload();
        }
    })
    .catch(err => console.error('Error:', err));
}

// Checklist - Add new item
document.querySelector('.checklist-add-btn')?.addEventListener('click', addChecklistItem);
document.querySelector('.checklist-input')?.addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        addChecklistItem();
    }
});

function addChecklistItem() {
    const input = document.querySelector('.checklist-input');
    const title = input?.value.trim();
    if (!title) return;
    
    fetch('/php/api/checklist.php', {
        method: 'POST',
        headers: { 
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ task_id: taskId, title: title })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            showToast(data.error || 'Có lỗi xảy ra', 'error');
        }
    })
    .catch(err => showToast('Lỗi kết nối: ' + err.message, 'error'));
}

async function deleteChecklistItem(itemId) {
    const confirmed = await ConfirmDialog.show({
        title: 'Xóa mục checklist',
        message: 'Bạn có chắc muốn xóa mục này?',
        confirmText: 'Xóa',
        type: 'danger'
    });
    if (!confirmed) return;
    
    fetch('/php/api/checklist.php?item_id=' + itemId, {
        method: 'DELETE',
        headers: { 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            // Remove from DOM
            const item = document.querySelector(`[data-item-id="${itemId}"]`);
            if (item) {
                item.remove();
                showToast('Đã xóa mục', 'success');
            } else {
                location.reload();
            }
        } else {
            showToast(data.error || 'Có lỗi xảy ra', 'error');
        }
    })
    .catch(err => showToast('Lỗi kết nối: ' + err.message, 'error'));
}

// Comments - Add new comment
document.querySelector('.comment-submit-btn')?.addEventListener('click', addComment);

function addComment() {
    const textarea = document.querySelector('.comment-input');
    const content = textarea?.value.trim();
    if (!content) return;
    
    const submitBtn = document.querySelector('.comment-submit-btn');
    if (window.LoadingState) LoadingState.showButton(submitBtn, 'Đang gửi...');
    
    fetch('/php/api/comments.php', {
        method: 'POST',
        headers: { 
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ task_id: taskId, entity_type: 'task', entity_id: taskId, content: content })
    })
    .then(r => {
        // Kiểm tra response có phải JSON không
        const contentType = r.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            return r.text().then(text => {
                throw new Error('Server trả về HTML thay vì JSON: ' + text.substring(0, 200));
            });
        }
        return r.json();
    })
    .then(data => {
        if (data.success) {
            showToast('Đã thêm bình luận', 'success');
            setTimeout(() => location.reload(), 500);
        } else {
            showToast(data.error || 'Có lỗi xảy ra', 'error');
        }
    })
    .catch(err => showToast('Lỗi kết nối: ' + err.message, 'error'))
    .finally(() => {
        if (window.LoadingState) LoadingState.hideButton(submitBtn);
    });
}

// Comments - Edit comment
function editComment(commentId, btn) {
    const commentItem = btn.closest('.comment-item');
    const contentEl = commentItem.querySelector('.comment-content');
    const editForm = commentItem.querySelector('.comment-edit-form');
    
    contentEl.classList.add('hidden');
    editForm.classList.remove('hidden');
    editForm.querySelector('textarea').focus();
}

function cancelEditComment(btn) {
    const commentItem = btn.closest('.comment-item');
    const contentEl = commentItem.querySelector('.comment-content');
    const editForm = commentItem.querySelector('.comment-edit-form');
    
    contentEl.classList.remove('hidden');
    editForm.classList.add('hidden');
}

function saveComment(commentId, btn) {
    const commentItem = btn.closest('.comment-item');
    const textarea = commentItem.querySelector('.edit-comment-input');
    const content = textarea.value.trim();
    
    if (!content) {
        showToast('Nội dung không được để trống', 'warning');
        return;
    }
    
    fetch('/php/api/comments.php', {
        method: 'PUT',
        headers: { 
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ comment_id: commentId, content: content })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showToast('Đã cập nhật bình luận', 'success');
            setTimeout(() => location.reload(), 500);
        } else {
            showToast(data.error || 'Có lỗi xảy ra', 'error');
        }
    })
    .catch(err => showToast('Lỗi kết nối: ' + err.message, 'error'));
}

// Comments - Delete comment
async function deleteComment(commentId) {
    const confirmed = await ConfirmDialog.confirmDelete('bình luận này');
    if (!confirmed) return;
    
    LoadingState.showFullPage('Đang xóa...');
    
    try {
        const response = await fetch('/php/api/comments.php?comment_id=' + commentId, {
            method: 'DELETE',
            headers: { 'Accept': 'application/json' }
        });
        const data = await response.json();
        
        if (data.success) {
            // Remove comment from DOM without reload
            const commentEl = document.querySelector(`[data-comment-id="${commentId}"]`);
            if (commentEl) {
                commentEl.remove();
                showToast('Đã xóa bình luận', 'success');
            } else {
                location.reload();
            }
        } else {
            showToast(data.error || 'Có lỗi xảy ra', 'error');
        }
    } catch (err) {
        showToast('Lỗi kết nối: ' + err.message, 'error');
    } finally {
        LoadingState.hideFullPage();
    }
}

// Comments - Reply functions
function showReplyForm(commentId) {
    // Ẩn tất cả reply forms khác
    document.querySelectorAll('.reply-form').forEach(form => form.classList.add('hidden'));
    // Hiện reply form của comment này
    const replyForm = document.getElementById('reply-form-' + commentId);
    if (replyForm) {
        replyForm.classList.remove('hidden');
        replyForm.querySelector('.reply-input').focus();
    }
}

function hideReplyForm(commentId) {
    const replyForm = document.getElementById('reply-form-' + commentId);
    if (replyForm) {
        replyForm.classList.add('hidden');
        replyForm.querySelector('.reply-input').value = '';
    }
}

function submitReply(parentId, replyToName = null) {
    const replyForm = document.getElementById('reply-form-' + parentId);
    if (!replyForm) {
        // Nếu không tìm thấy form theo parentId, tìm form đang mở
        const openForm = document.querySelector('.reply-form:not(.hidden)');
        if (openForm) {
            parentId = openForm.id.replace('reply-form-', '');
        }
    }
    
    const form = document.getElementById('reply-form-' + parentId);
    const textarea = form.querySelector('.reply-input');
    let content = textarea.value.trim();
    
    if (!content) {
        showToast('Vui lòng nhập nội dung trả lời', 'warning');
        return;
    }
    
    // Thêm mention nếu có tên người được trả lời và chưa có trong content
    if (replyToName && !content.startsWith('@' + replyToName)) {
        content = '@' + replyToName + ' ' + content;
    }
    
    fetch('/php/api/comments.php', {
        method: 'POST',
        headers: { 
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ 
            task_id: taskId, 
            entity_type: 'task', 
            entity_id: taskId, 
            content: content,
            parent_id: parentId  // Trả lời trực tiếp vào comment được chọn (nested)
        })
    })
    .then(r => {
        const contentType = r.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            return r.text().then(text => {
                throw new Error('Server trả về HTML thay vì JSON: ' + text.substring(0, 200));
            });
        }
        return r.json();
    })
    .then(data => {
        if (data.success) {
            showToast('Đã gửi trả lời', 'success');
            setTimeout(() => location.reload(), 500);
        } else {
            showToast(data.error || 'Có lỗi xảy ra', 'error');
        }
    })
    .catch(err => showToast('Lỗi kết nối: ' + err.message, 'error'));
}

// Checklist - Edit title
function editChecklistItem(itemId, titleEl) {
    const item = titleEl.closest('.checklist-item');
    const editInput = item.querySelector('.checklist-edit-input');
    
    titleEl.classList.add('hidden');
    editInput.classList.remove('hidden');
    editInput.focus();
    editInput.select();
}

function saveChecklistTitle(itemId, input) {
    const title = input.value.trim();
    const item = input.closest('.checklist-item');
    const titleEl = item.querySelector('.checklist-title');
    
    if (!title) {
        input.classList.add('hidden');
        titleEl.classList.remove('hidden');
        return;
    }
    
    fetch('/php/api/checklist.php', {
        method: 'PUT',
        headers: { 
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ item_id: itemId, action: 'update', title: title })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            titleEl.textContent = title;
            input.classList.add('hidden');
            titleEl.classList.remove('hidden');
        } else {
            showToast(data.error || 'Có lỗi xảy ra', 'error');
        }
    })
    .catch(err => {
        showToast('Lỗi kết nối: ' + err.message, 'error');
        input.classList.add('hidden');
        titleEl.classList.remove('hidden');
    });
}

function handleChecklistEditKey(event, itemId, input) {
    if (event.key === 'Enter') {
        event.preventDefault();
        input.blur();
    } else if (event.key === 'Escape') {
        const item = input.closest('.checklist-item');
        const titleEl = item.querySelector('.checklist-title');
        input.classList.add('hidden');
        titleEl.classList.remove('hidden');
    }
}
</script>

<?php View::endSection(); ?>
