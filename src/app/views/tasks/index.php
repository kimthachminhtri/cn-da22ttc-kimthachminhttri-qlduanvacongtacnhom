<?php
/**
 * Tasks List View with Gantt Chart
 */
use Core\View;
use Core\Session;
use Core\Permission;

Session::start();
$userRole = Session::get('user_role', 'guest');
$statusFilter = $_GET['status'] ?? 'all';
$priorityFilter = $_GET['priority'] ?? 'all';
$currentView = $_GET['view'] ?? 'list';

// Get current month for Gantt
$ganttMonth = isset($_GET['month']) ? (int)$_GET['month'] : (int)date('n');
$ganttYear = isset($_GET['year']) ? (int)$_GET['year'] : (int)date('Y');
if ($ganttMonth < 1) { $ganttMonth = 12; $ganttYear--; }
if ($ganttMonth > 12) { $ganttMonth = 1; $ganttYear++; }
$firstDayOfMonth = mktime(0, 0, 0, $ganttMonth, 1, $ganttYear);
$daysInMonth = (int)date('t', $firstDayOfMonth);
$monthName = date('F Y', $firstDayOfMonth);

$prevMonth = $ganttMonth - 1; $prevYear = $ganttYear;
if ($prevMonth < 1) { $prevMonth = 12; $prevYear--; }
$nextMonth = $ganttMonth + 1; $nextYear = $ganttYear;
if ($nextMonth > 12) { $nextMonth = 1; $nextYear++; }

if (!function_exists('getPriorityName')) {
    function getPriorityName(string $priority): string {
        return ['urgent' => 'Khẩn cấp', 'high' => 'Cao', 'medium' => 'Trung bình', 'low' => 'Thấp'][$priority] ?? $priority;
    }
}

View::section('content');
?>

<div class="space-y-6">
    <!-- Header with View Toggle -->
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <!-- View Toggle -->
            <div class="flex items-center bg-gray-100 rounded-lg p-1">
                <a href="?view=list&status=<?= $statusFilter ?>&priority=<?= $priorityFilter ?>" 
                   class="flex items-center gap-2 px-3 py-1.5 rounded-md text-sm font-medium transition-colors <?= $currentView === 'list' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-600 hover:text-gray-900' ?>">
                    <i data-lucide="list" class="h-4 w-4"></i>
                    Danh sách
                </a>
                <a href="?view=gantt&month=<?= $ganttMonth ?>&year=<?= $ganttYear ?>" 
                   class="flex items-center gap-2 px-3 py-1.5 rounded-md text-sm font-medium transition-colors <?= $currentView === 'gantt' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-600 hover:text-gray-900' ?>">
                    <i data-lucide="gantt-chart" class="h-4 w-4"></i>
                    Gantt
                </a>
            </div>

            <?php if ($currentView === 'gantt'): ?>
            <!-- Gantt Month Navigation -->
            <div class="h-6 w-px bg-gray-300"></div>
            <h2 class="text-xl font-semibold text-gray-900"><?= $monthName ?></h2>
            <div class="flex items-center gap-1">
                <a href="?view=gantt&month=<?= $prevMonth ?>&year=<?= $prevYear ?>" class="p-2 rounded-lg hover:bg-gray-100">
                    <i data-lucide="chevron-left" class="h-5 w-5"></i>
                </a>
                <a href="?view=gantt&month=<?= date('n') ?>&year=<?= date('Y') ?>" class="px-3 py-1 text-sm font-medium text-primary hover:bg-primary/10 rounded-lg">Hôm nay</a>
                <a href="?view=gantt&month=<?= $nextMonth ?>&year=<?= $nextYear ?>" class="p-2 rounded-lg hover:bg-gray-100">
                    <i data-lucide="chevron-right" class="h-5 w-5"></i>
                </a>
            </div>
            <?php endif; ?>
        </div>
        
        <div class="flex items-center gap-2">
            <?php if ($currentView === 'list'): ?>
            <!-- Filters for List View -->
            <select onchange="window.location.href='?view=list&status='+this.value+'&priority=<?= $priorityFilter ?>'" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
                <option value="all" <?= $statusFilter === 'all' ? 'selected' : '' ?>>Tất cả trạng thái</option>
                <option value="todo" <?= $statusFilter === 'todo' ? 'selected' : '' ?>>Cần làm</option>
                <option value="in_progress" <?= $statusFilter === 'in_progress' ? 'selected' : '' ?>>Đang làm</option>
                <option value="done" <?= $statusFilter === 'done' ? 'selected' : '' ?>>Hoàn thành</option>
            </select>
            <select onchange="window.location.href='?view=list&status=<?= $statusFilter ?>&priority='+this.value" class="rounded-lg border border-gray-300 px-3 py-2 text-sm">
                <option value="all" <?= $priorityFilter === 'all' ? 'selected' : '' ?>>Tất cả độ ưu tiên</option>
                <option value="urgent" <?= $priorityFilter === 'urgent' ? 'selected' : '' ?>>Khẩn cấp</option>
                <option value="high" <?= $priorityFilter === 'high' ? 'selected' : '' ?>>Cao</option>
                <option value="medium" <?= $priorityFilter === 'medium' ? 'selected' : '' ?>>Trung bình</option>
                <option value="low" <?= $priorityFilter === 'low' ? 'selected' : '' ?>>Thấp</option>
            </select>
            <div class="relative">
                <input type="text" placeholder="Tìm kiếm..." id="task-search" oninput="filterTasks(this.value)"
                       class="rounded-lg border border-gray-300 px-3 py-2 text-sm pl-9 w-48 focus:border-primary focus:ring-1 focus:ring-primary">
                <i data-lucide="search" class="h-4 w-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
            </div>
            <?php endif; ?>
            
            <!-- Export Dropdown -->
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" class="flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    <i data-lucide="download" class="h-4 w-4"></i>
                    Xuất
                </button>
                <div x-show="open" @click.away="open = false" x-cloak
                     class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-20">
                    <a href="/php/api/export-report.php?type=my-tasks&format=excel" target="_blank"
                       class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                        <i data-lucide="file-spreadsheet" class="h-4 w-4 text-green-600"></i>
                        Excel (.xlsx)
                    </a>
                    <a href="/php/api/export-report.php?type=my-tasks&format=pdf" target="_blank"
                       class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                        <i data-lucide="file-text" class="h-4 w-4 text-red-600"></i>
                        PDF
                    </a>
                    <a href="/php/api/export-report.php?type=my-tasks&format=csv" target="_blank"
                       class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                        <i data-lucide="file" class="h-4 w-4 text-blue-600"></i>
                        CSV
                    </a>
                </div>
            </div>
            
            <?php if (Permission::can($userRole, 'tasks.create')): ?>
            <button onclick="openModal('create-task-modal')" class="flex items-center gap-2 rounded-lg bg-primary px-4 py-2 text-sm font-medium text-white hover:bg-primary/90">
                <i data-lucide="plus" class="h-4 w-4"></i>
                Tạo công việc
            </button>
            <?php endif; ?>
        </div>
    </div>


<?php if ($currentView === 'gantt'): ?>
    <!-- ==================== GANTT CHART VIEW ==================== -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <div class="min-w-[1200px]">
                <!-- Gantt Header -->
                <div class="flex border-b border-gray-200 sticky top-0 bg-white z-10">
                    <div class="w-72 flex-shrink-0 px-4 py-3 bg-gray-50 border-r border-gray-200 font-medium text-sm text-gray-700">
                        Công việc
                    </div>
                    <div class="flex-1 flex">
                        <?php for ($day = 1; $day <= $daysInMonth; $day++): 
                            $dateStr = sprintf('%04d-%02d-%02d', $ganttYear, $ganttMonth, $day);
                            $isToday = ($day == date('j') && $ganttMonth == date('n') && $ganttYear == date('Y'));
                            $isWeekend = in_array(date('N', strtotime($dateStr)), [6, 7]);
                            $dayNames = ['CN','T2','T3','T4','T5','T6','T7'];
                        ?>
                        <div class="flex-1 min-w-[36px] px-0.5 py-2 text-center text-xs border-r border-gray-100 <?= $isWeekend ? 'bg-gray-50' : '' ?> <?= $isToday ? 'bg-primary/10' : '' ?>">
                            <div class="font-semibold <?= $isToday ? 'text-primary' : 'text-gray-900' ?>"><?= $day ?></div>
                            <div class="text-gray-400 text-[10px]"><?= $dayNames[date('w', strtotime($dateStr))] ?></div>
                        </div>
                        <?php endfor; ?>
                    </div>
                </div>
                
                <!-- Gantt Rows -->
                <div class="divide-y divide-gray-100">
                    <?php 
                    // Filter tasks for Gantt (only those with due_date)
                    $ganttTasks = array_filter($tasks ?? [], function($t) use ($ganttYear, $ganttMonth, $daysInMonth) {
                        if (empty($t['due_date'])) return false;
                        $dueMonth = (int)date('n', strtotime($t['due_date']));
                        $dueYear = (int)date('Y', strtotime($t['due_date']));
                        // Show if due date is in this month OR task spans into this month
                        return ($dueYear == $ganttYear && $dueMonth == $ganttMonth) ||
                               (!empty($t['start_date']) && strtotime($t['start_date']) <= strtotime("$ganttYear-$ganttMonth-$daysInMonth"));
                    });
                    
                    // Sort by due date
                    usort($ganttTasks, fn($a, $b) => strtotime($a['due_date']) - strtotime($b['due_date']));
                    
                    if (empty($ganttTasks)):
                    ?>
                    <div class="px-6 py-12 text-center text-gray-500">
                        <i data-lucide="calendar-x" class="h-12 w-12 mx-auto mb-4 text-gray-300"></i>
                        <p class="font-medium">Không có công việc nào trong tháng này</p>
                        <p class="text-sm mt-1">Tạo công việc mới hoặc chuyển sang tháng khác</p>
                    </div>
                    <?php else: ?>
                        <?php foreach ($ganttTasks as $task): 
                            // Màu theo độ ưu tiên
                            $priorityColors = [
                                'urgent' => '#DC2626', // red-600
                                'high' => '#EA580C',   // orange-600
                                'medium' => '#2563EB', // blue-600
                                'low' => '#6B7280'     // gray-500
                            ];
                            
                            // Màu theo trạng thái (ưu tiên cao hơn)
                            $statusColors = [
                                'done' => '#16A34A',      // green-600
                                'in_review' => '#7C3AED', // violet-600
                                'in_progress' => '#0891B2', // cyan-600
                            ];
                            
                            // Chọn màu: done/in_review có màu riêng, còn lại theo priority
                            $taskStatus = $task['status'] ?? 'todo';
                            if (isset($statusColors[$taskStatus])) {
                                $barColor = $statusColors[$taskStatus];
                            } else {
                                $barColor = $priorityColors[$task['priority'] ?? 'medium'];
                            }
                            
                            $statusIcons = ['done' => 'check-circle', 'in_progress' => 'loader', 'in_review' => 'eye', 'todo' => 'circle', 'backlog' => 'inbox'];
                        ?>
                        <div class="flex hover:bg-gray-50 group">
                            <!-- Task Info -->
                            <div class="w-72 flex-shrink-0 px-4 py-3 border-r border-gray-200">
                                <div class="flex items-start gap-3">
                                    <i data-lucide="<?= $statusIcons[$task['status'] ?? 'todo'] ?>" class="h-4 w-4 mt-0.5 text-gray-400 flex-shrink-0"></i>
                                    <div class="min-w-0 flex-1">
                                        <a href="/php/task-detail.php?id=<?= View::e($task['id']) ?>" 
                                           class="block text-sm font-medium text-gray-900 truncate hover:text-primary <?= ($task['status'] ?? '') === 'done' ? 'line-through text-gray-400' : '' ?>">
                                            <?= View::e($task['title']) ?>
                                        </a>
                                        <div class="flex items-center gap-2 mt-1">
                                            <?php if (!empty($task['project_name'])): ?>
                                            <span class="inline-flex items-center gap-1 text-xs text-gray-500">
                                                <span class="h-2 w-2 rounded-full" style="background-color: <?= View::e($task['project_color'] ?? '#6366f1') ?>"></span>
                                                <?= View::e($task['project_name']) ?>
                                            </span>
                                            <?php endif; ?>
                                            <span class="text-xs text-gray-400"><?= date('d/m', strtotime($task['due_date'])) ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Gantt Bar Area -->
                            <div class="flex-1 flex relative py-2">
                                <?php
                                // Calculate bar position
                                $monthStart = strtotime(sprintf('%04d-%02d-01', $ganttYear, $ganttMonth));
                                $monthEnd = strtotime(sprintf('%04d-%02d-%02d', $ganttYear, $ganttMonth, $daysInMonth));
                                
                                // Task start: use start_date if available, otherwise created_at or 3 days before due
                                $taskStart = !empty($task['start_date']) ? strtotime($task['start_date']) : 
                                            (!empty($task['created_at']) ? strtotime($task['created_at']) : strtotime($task['due_date'] . ' -3 days'));
                                $taskEnd = strtotime($task['due_date']);
                                
                                // Clamp to month
                                $barStart = max($taskStart, $monthStart);
                                $barEnd = min($taskEnd, $monthEnd);
                                
                                $startDay = (int)date('j', $barStart);
                                $endDay = (int)date('j', $barEnd);
                                $duration = max(1, $endDay - $startDay + 1);
                                
                                $showBar = ($barStart <= $monthEnd && $barEnd >= $monthStart);
                                $leftPercent = (($startDay - 1) / $daysInMonth) * 100;
                                $widthPercent = ($duration / $daysInMonth) * 100;
                                
                                // Check if overdue
                                $isOverdue = $taskEnd < strtotime('today') && ($task['status'] ?? '') !== 'done';
                                ?>
                                
                                <!-- Day columns background -->
                                <?php for ($day = 1; $day <= $daysInMonth; $day++): 
                                    $dateStr = sprintf('%04d-%02d-%02d', $ganttYear, $ganttMonth, $day);
                                    $isWeekend = in_array(date('N', strtotime($dateStr)), [6, 7]);
                                    $isToday = ($day == date('j') && $ganttMonth == date('n') && $ganttYear == date('Y'));
                                ?>
                                <div class="flex-1 min-w-[36px] border-r border-gray-100 <?= $isWeekend ? 'bg-gray-50' : '' ?> <?= $isToday ? 'bg-primary/5' : '' ?>"></div>
                                <?php endfor; ?>
                                
                                <!-- Gantt Bar -->
                                <?php if ($showBar): ?>
                                <a href="/php/task-detail.php?id=<?= View::e($task['id']) ?>"
                                   class="absolute top-1/2 -translate-y-1/2 h-7 rounded-full flex items-center px-2 text-xs text-white font-medium shadow-sm hover:shadow-md transition-shadow cursor-pointer gantt-bar"
                                   style="left: calc(<?= $leftPercent ?>% + 2px); width: calc(<?= $widthPercent ?>% - 4px); min-width: 20px; background-color: <?= $isOverdue ? '#EF4444' : View::e($barColor) ?>;"
                                   title="<?= View::e($task['title']) ?>&#10;<?= date('d/m', $taskStart) ?> - <?= date('d/m', $taskEnd) ?><?= $isOverdue ? '&#10;⚠️ Quá hạn!' : '' ?>">
                                    <?php if ($duration > 2): ?>
                                    <span class="truncate"><?= View::e($task['title']) ?></span>
                                    <?php endif; ?>
                                    <?php if ($isOverdue): ?>
                                    <i data-lucide="alert-circle" class="h-3 w-3 ml-auto flex-shrink-0"></i>
                                    <?php endif; ?>
                                </a>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Gantt Legend -->
    <div class="flex flex-wrap items-center gap-4 text-sm text-gray-600 bg-white rounded-lg border border-gray-200 p-3">
        <span class="font-medium text-gray-700">Chú thích:</span>
        <div class="flex items-center gap-1.5">
            <div class="w-3 h-3 rounded" style="background-color: #EF4444"></div>
            <span>Quá hạn</span>
        </div>
        <div class="flex items-center gap-1.5">
            <div class="w-3 h-3 rounded" style="background-color: #16A34A"></div>
            <span>Hoàn thành</span>
        </div>
        <div class="flex items-center gap-1.5">
            <div class="w-3 h-3 rounded" style="background-color: #0891B2"></div>
            <span>Đang làm</span>
        </div>
        <div class="flex items-center gap-1.5">
            <div class="w-3 h-3 rounded" style="background-color: #7C3AED"></div>
            <span>Đang review</span>
        </div>
        <div class="flex items-center gap-1.5">
            <div class="w-3 h-3 rounded" style="background-color: #DC2626"></div>
            <span>Khẩn cấp</span>
        </div>
        <div class="flex items-center gap-1.5">
            <div class="w-3 h-3 rounded" style="background-color: #EA580C"></div>
            <span>Ưu tiên cao</span>
        </div>
        <div class="flex items-center gap-1.5">
            <div class="w-3 h-3 rounded" style="background-color: #2563EB"></div>
            <span>Trung bình</span>
        </div>
        <div class="flex items-center gap-1.5">
            <div class="w-3 h-3 rounded" style="background-color: #6B7280"></div>
            <span>Thấp</span>
        </div>
        <div class="h-4 w-px bg-gray-300"></div>
        <div class="flex items-center gap-1.5">
            <div class="w-3 h-3 rounded bg-primary/20 border border-primary/40"></div>
            <span>Hôm nay</span>
        </div>
        <div class="flex items-center gap-1.5">
            <div class="w-3 h-3 rounded bg-gray-100 border border-gray-200"></div>
            <span>Cuối tuần</span>
        </div>
    </div>

<?php else: ?>

    <!-- ==================== LIST VIEW ==================== -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="divide-y divide-gray-100">
            <?php if (!empty($tasks)): ?>
                <?php foreach ($tasks as $task): ?>
                <div class="px-6 py-4 hover:bg-gray-50 transition-colors task-row">
                    <div class="flex items-start gap-4">
                        <input type="checkbox" <?= ($task['status'] ?? '') === 'done' ? 'checked' : '' ?>
                               onchange="toggleTaskStatus('<?= View::e($task['id']) ?>')"
                               class="mt-1 h-4 w-4 rounded border-gray-300 text-primary cursor-pointer">
                        <div class="flex-1 min-w-0">
                            <a href="/php/task-detail.php?id=<?= View::e($task['id']) ?>" 
                               class="font-medium text-gray-900 hover:text-primary <?= ($task['status'] ?? '') === 'done' ? 'line-through text-gray-400' : '' ?>">
                                <?= View::e($task['title']) ?>
                            </a>
                            <div class="flex flex-wrap items-center gap-2 mt-2">
                                <?php
                                $priorityClasses = [
                                    'urgent' => 'bg-red-100 text-red-800',
                                    'high' => 'bg-orange-100 text-orange-800',
                                    'medium' => 'bg-blue-100 text-blue-800',
                                    'low' => 'bg-gray-100 text-gray-800',
                                ];
                                ?>
                                <span class="inline-flex items-center rounded px-2 py-0.5 text-xs font-medium <?= $priorityClasses[$task['priority'] ?? 'medium'] ?>">
                                    <?= View::e(getPriorityName($task['priority'] ?? 'medium')) ?>
                                </span>
                                <?php if (!empty($task['project_name'])): ?>
                                <span class="inline-flex items-center gap-1 text-xs text-gray-500">
                                    <span class="h-2 w-2 rounded-full" style="background-color: <?= View::e($task['project_color'] ?? '#6366f1') ?>"></span>
                                    <?= View::e($task['project_name']) ?>
                                </span>
                                <?php endif; ?>
                                <?php if (!empty($task['due_date'])): 
                                    $isOverdue = strtotime($task['due_date']) < strtotime('today') && ($task['status'] ?? '') !== 'done';
                                ?>
                                <span class="text-xs <?= $isOverdue ? 'text-red-600 font-medium' : 'text-gray-500' ?>">
                                    <i data-lucide="calendar" class="h-3 w-3 inline"></i>
                                    <?= date('d/m/Y', strtotime($task['due_date'])) ?>
                                    <?= $isOverdue ? '(Quá hạn)' : '' ?>
                                </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="px-6">
                <?php 
                View::partial('components/empty-state', [
                    'icon' => 'check-square',
                    'title' => 'Chưa có công việc nào',
                    'description' => 'Tạo công việc đầu tiên để bắt đầu quản lý thời gian hiệu quả.',
                    'action' => Permission::can($userRole, 'tasks.create') ? [
                        'label' => 'Tạo công việc đầu tiên',
                        'onclick' => "openModal('create-task-modal')",
                        'icon' => 'plus'
                    ] : null
                ]);
                ?>
                </div>
            <?php endif; ?>
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
            <form action="/php/api/create-task.php" method="POST" class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tiêu đề <span class="text-red-500">*</span></label>
                    <input type="text" name="title" required class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mô tả</label>
                    <textarea name="description" rows="3" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary"></textarea>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Dự án</label>
                        <select name="projectId" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                            <option value="">-- Không có --</option>
                            <?php foreach ($projects ?? [] as $project): ?>
                            <option value="<?= View::e($project['id']) ?>"><?= View::e($project['name']) ?></option>
                            <?php endforeach; ?>
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
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ngày bắt đầu</label>
                        <input type="date" name="startDate" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ngày hết hạn</label>
                        <input type="date" name="dueDate" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Giao cho</label>
                    <select name="assigneeId" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                        <option value="">-- Chưa giao --</option>
                        <?php foreach ($users ?? [] as $user): ?>
                        <option value="<?= View::e($user['id']) ?>"><?= View::e($user['full_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="flex justify-end gap-3 pt-4">
                    <button type="button" onclick="closeModal('create-task-modal')" class="px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-lg">Hủy</button>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-primary hover:bg-primary/90 rounded-lg">Tạo công việc</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>


<script>
// Toggle task status
async function toggleTaskStatus(taskId) {
    const checkbox = event.target;
    const newStatus = checkbox.checked ? 'done' : 'todo';
    const taskRow = checkbox.closest('.task-row');
    
    taskRow.style.opacity = '0.6';
    taskRow.style.pointerEvents = 'none';
    
    try {
        const response = await fetch('/php/api/update-task.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ task_id: taskId, status: newStatus })
        });
        const data = await response.json();
        
        if (data.success) {
            const titleLink = taskRow.querySelector('a.font-medium');
            if (newStatus === 'done') {
                titleLink.classList.add('line-through', 'text-gray-400');
                showToast?.('Đã hoàn thành công việc!', 'success');
            } else {
                titleLink.classList.remove('line-through', 'text-gray-400');
                showToast?.('Đã mở lại công việc', 'info');
            }
        } else {
            checkbox.checked = !checkbox.checked;
            showToast?.(data.error || 'Có lỗi xảy ra', 'error');
        }
    } catch (err) {
        checkbox.checked = !checkbox.checked;
        showToast?.('Lỗi kết nối', 'error');
    } finally {
        taskRow.style.opacity = '';
        taskRow.style.pointerEvents = '';
    }
}

// Create task form handler
document.querySelector('#create-task-modal form')?.addEventListener('submit', async function(e) {
    e.preventDefault();
    const form = this;
    const submitBtn = form.querySelector('[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<svg class="animate-spin h-4 w-4 mr-2 inline" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>Đang tạo...';
    
    try {
        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());
        
        const response = await fetch('/php/api/create-task.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
        const result = await response.json();
        
        if (result.success) {
            showToast?.('Tạo công việc thành công!', 'success');
            window.location.href = '/php/task-detail.php?id=' + result.task_id;
        } else {
            showToast?.(result.error || 'Có lỗi xảy ra', 'error');
        }
    } catch (err) {
        showToast?.('Lỗi kết nối', 'error');
    } finally {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    }
});

// Filter tasks by search
function filterTasks(query) {
    const tasks = document.querySelectorAll('.task-row');
    const q = query.toLowerCase().trim();
    
    tasks.forEach(task => {
        const title = task.querySelector('a.font-medium')?.textContent.toLowerCase() || '';
        const project = task.querySelector('.text-xs.text-gray-500')?.textContent.toLowerCase() || '';
        task.style.display = (title.includes(q) || project.includes(q)) ? '' : 'none';
    });
}
</script>

<?php View::endSection(); ?>
