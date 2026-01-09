<?php
/**
 * Calendar View
 */
use Core\View;
use Core\Session;

$currentMonth = isset($_GET['month']) ? (int)$_GET['month'] : (int)date('n');
$currentYear = isset($_GET['year']) ? (int)$_GET['year'] : (int)date('Y');

// Validate month/year
if ($currentMonth < 1) { $currentMonth = 12; $currentYear--; }
if ($currentMonth > 12) { $currentMonth = 1; $currentYear++; }

$firstDay = mktime(0, 0, 0, $currentMonth, 1, $currentYear);
$daysInMonth = date('t', $firstDay);
$startDayOfWeek = date('N', $firstDay); // 1=Monday, 7=Sunday
$monthName = date('F Y', $firstDay);

$prevMonth = $currentMonth - 1;
$prevYear = $currentYear;
if ($prevMonth < 1) { $prevMonth = 12; $prevYear--; }

$nextMonth = $currentMonth + 1;
$nextYear = $currentYear;
if ($nextMonth > 12) { $nextMonth = 1; $nextYear++; }

View::section('content');
?>

<?php
$currentView = $_GET['view'] ?? 'calendar';
?>

<div class="space-y-6">
    <!-- View Tabs & Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <!-- View Toggle -->
            <div class="flex items-center bg-gray-100 rounded-lg p-1">
                <a href="?view=calendar&month=<?= $currentMonth ?>&year=<?= $currentYear ?>" 
                   class="flex items-center gap-2 px-3 py-1.5 rounded-md text-sm font-medium transition-colors <?= $currentView === 'calendar' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-600 hover:text-gray-900' ?>">
                    <i data-lucide="calendar" class="h-4 w-4"></i>
                    Lịch
                </a>
                <a href="?view=gantt&month=<?= $currentMonth ?>&year=<?= $currentYear ?>" 
                   class="flex items-center gap-2 px-3 py-1.5 rounded-md text-sm font-medium transition-colors <?= $currentView === 'gantt' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-600 hover:text-gray-900' ?>">
                    <i data-lucide="gantt-chart" class="h-4 w-4"></i>
                    Gantt
                </a>
            </div>
            
            <div class="h-6 w-px bg-gray-300"></div>
            
            <h2 class="text-xl font-semibold text-gray-900"><?= $monthName ?></h2>
            <div class="flex items-center gap-1">
                <a href="?view=<?= $currentView ?>&month=<?= $prevMonth ?>&year=<?= $prevYear ?>" 
                   class="p-2 rounded-lg hover:bg-gray-100">
                    <i data-lucide="chevron-left" class="h-5 w-5"></i>
                </a>
                <a href="?view=<?= $currentView ?>&month=<?= date('n') ?>&year=<?= date('Y') ?>" 
                   class="px-3 py-1 text-sm font-medium text-primary hover:bg-primary/10 rounded-lg">
                    Hôm nay
                </a>
                <a href="?view=<?= $currentView ?>&month=<?= $nextMonth ?>&year=<?= $nextYear ?>" 
                   class="p-2 rounded-lg hover:bg-gray-100">
                    <i data-lucide="chevron-right" class="h-5 w-5"></i>
                </a>
            </div>
        </div>
        <button onclick="openModal('create-event-modal')" 
                class="flex items-center gap-2 rounded-lg bg-primary px-4 py-2 text-sm font-medium text-white hover:bg-primary/90">
            <i data-lucide="plus" class="h-4 w-4"></i>
            Thêm sự kiện
        </button>
    </div>

<?php if ($currentView === 'gantt'): ?>
    <!-- Gantt Chart View -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <div class="min-w-[1200px]">
                <!-- Gantt Header - Days -->
                <div class="flex border-b border-gray-200">
                    <div class="w-64 flex-shrink-0 px-4 py-3 bg-gray-50 border-r border-gray-200 font-medium text-sm text-gray-700">
                        Công việc / Sự kiện
                    </div>
                    <div class="flex-1 flex">
                        <?php for ($day = 1; $day <= $daysInMonth; $day++): 
                            $dateStr = sprintf('%04d-%02d-%02d', $currentYear, $currentMonth, $day);
                            $isToday = ($day == date('j') && $currentMonth == date('n') && $currentYear == date('Y'));
                            $isWeekend = in_array(date('N', strtotime($dateStr)), [6, 7]);
                        ?>
                        <div class="flex-1 min-w-[40px] px-1 py-3 text-center text-xs border-r border-gray-100 <?= $isWeekend ? 'bg-gray-50' : '' ?> <?= $isToday ? 'bg-primary/10' : '' ?>">
                            <div class="font-medium <?= $isToday ? 'text-primary' : 'text-gray-900' ?>"><?= $day ?></div>
                            <div class="text-gray-400"><?= ['CN','T2','T3','T4','T5','T6','T7'][date('w', strtotime($dateStr))] ?></div>
                        </div>
                        <?php endfor; ?>
                    </div>
                </div>
                
                <!-- Gantt Rows -->
                <div class="divide-y divide-gray-100">
                    <?php 
                    // Combine tasks and events for Gantt
                    $ganttItems = [];
                    
                    // Add tasks
                    foreach ($tasks ?? [] as $task) {
                        if (!empty($task['due_date'])) {
                            $ganttItems[] = [
                                'id' => $task['id'],
                                'title' => $task['title'],
                                'start' => $task['created_at'] ?? $task['due_date'],
                                'end' => $task['due_date'],
                                'type' => 'task',
                                'color' => $task['project_color'] ?? '#ef4444',
                                'status' => $task['status'] ?? 'todo',
                                'project' => $task['project_name'] ?? '',
                                'link' => '/php/task-detail.php?id=' . $task['id'],
                            ];
                        }
                    }
                    
                    // Add events - sử dụng start_time theo database schema
                    foreach ($events ?? [] as $event) {
                        $ganttItems[] = [
                            'id' => $event['id'],
                            'title' => $event['title'],
                            'start' => $event['start_time'] ?? $event['start_date'] ?? date('Y-m-d'),
                            'end' => $event['end_time'] ?? $event['end_date'] ?? $event['start_time'] ?? date('Y-m-d'),
                            'type' => 'event',
                            'color' => $event['color'] ?? '#6366f1',
                            'status' => 'event',
                            'project' => '',
                            'link' => '#',
                        ];
                    }
                    
                    // Sort by start date
                    usort($ganttItems, fn($a, $b) => strtotime($a['start']) - strtotime($b['start']));
                    
                    if (empty($ganttItems)):
                    ?>
                    <div class="px-6 py-12 text-center text-gray-500">
                        <i data-lucide="calendar-x" class="h-12 w-12 mx-auto mb-4 text-gray-300"></i>
                        <p>Không có công việc hoặc sự kiện nào trong tháng này</p>
                    </div>
                    <?php else: ?>
                        <?php foreach ($ganttItems as $item): ?>
                        <div class="flex hover:bg-gray-50">
                            <!-- Task/Event Name -->
                            <div class="w-64 flex-shrink-0 px-4 py-3 border-r border-gray-200">
                                <div class="flex items-center gap-2">
                                    <?php if ($item['type'] === 'task'): ?>
                                    <i data-lucide="check-square" class="h-4 w-4 text-gray-400"></i>
                                    <?php else: ?>
                                    <i data-lucide="calendar" class="h-4 w-4 text-gray-400"></i>
                                    <?php endif; ?>
                                    <div class="min-w-0">
                                        <a href="<?= View::e($item['link']) ?>" 
                                           <?= $item['type'] === 'event' ? 'onclick="event.preventDefault(); viewEvent(\'' . View::e($item['id']) . '\')"' : '' ?>
                                           class="block text-sm font-medium text-gray-900 truncate hover:text-primary">
                                            <?= View::e($item['title']) ?>
                                        </a>
                                        <?php if (!empty($item['project'])): ?>
                                        <p class="text-xs text-gray-500 truncate"><?= View::e($item['project']) ?></p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Gantt Bar -->
                            <div class="flex-1 flex relative py-2">
                                <?php
                                $monthStart = strtotime(sprintf('%04d-%02d-01', $currentYear, $currentMonth));
                                $monthEnd = strtotime(sprintf('%04d-%02d-%02d', $currentYear, $currentMonth, $daysInMonth));
                                
                                $itemStart = strtotime($item['start']);
                                $itemEnd = strtotime($item['end']);
                                
                                // Clamp to month boundaries
                                $barStart = max($itemStart, $monthStart);
                                $barEnd = min($itemEnd, $monthEnd);
                                
                                // Calculate position
                                $startDay = (int)date('j', $barStart);
                                $endDay = (int)date('j', $barEnd);
                                $duration = $endDay - $startDay + 1;
                                
                                // Only show if within this month
                                $showBar = ($barStart <= $monthEnd && $barEnd >= $monthStart);
                                
                                // Calculate left position and width as percentage
                                $leftPercent = (($startDay - 1) / $daysInMonth) * 100;
                                $widthPercent = ($duration / $daysInMonth) * 100;
                                ?>
                                
                                <?php for ($day = 1; $day <= $daysInMonth; $day++): 
                                    $dateStr = sprintf('%04d-%02d-%02d', $currentYear, $currentMonth, $day);
                                    $isWeekend = in_array(date('N', strtotime($dateStr)), [6, 7]);
                                    $isToday = ($day == date('j') && $currentMonth == date('n') && $currentYear == date('Y'));
                                ?>
                                <div class="flex-1 min-w-[40px] border-r border-gray-100 <?= $isWeekend ? 'bg-gray-50' : '' ?> <?= $isToday ? 'bg-primary/5' : '' ?>"></div>
                                <?php endfor; ?>
                                
                                <?php if ($showBar): ?>
                                <div class="absolute top-1/2 -translate-y-1/2 h-6 rounded-full flex items-center px-2 text-xs text-white font-medium shadow-sm cursor-pointer gantt-bar"
                                     style="left: calc(<?= $leftPercent ?>% + 4px); width: calc(<?= $widthPercent ?>% - 8px); min-width: 24px; background-color: <?= View::e($item['color']) ?>;"
                                     title="<?= View::e($item['title']) ?> (<?= date('d/m', $itemStart) ?> - <?= date('d/m', $itemEnd) ?>)">
                                    <span class="truncate"><?= $duration > 2 ? View::e($item['title']) : '' ?></span>
                                </div>
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
    <div class="flex items-center gap-6 text-sm text-gray-600">
        <div class="flex items-center gap-2">
            <div class="w-4 h-4 rounded bg-red-500"></div>
            <span>Task</span>
        </div>
        <div class="flex items-center gap-2">
            <div class="w-4 h-4 rounded bg-indigo-500"></div>
            <span>Sự kiện</span>
        </div>
        <div class="flex items-center gap-2">
            <div class="w-4 h-4 rounded bg-primary/10 border border-primary/30"></div>
            <span>Hôm nay</span>
        </div>
        <div class="flex items-center gap-2">
            <div class="w-4 h-4 rounded bg-gray-100"></div>
            <span>Cuối tuần</span>
        </div>
    </div>

<?php else: ?>

    <!-- Calendar Grid -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <!-- Days of week header -->
        <div class="grid grid-cols-7 bg-gray-50 border-b border-gray-200">
            <?php 
            $days = ['T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'CN'];
            foreach ($days as $day): 
            ?>
            <div class="px-4 py-3 text-center text-sm font-medium text-gray-500"><?= $day ?></div>
            <?php endforeach; ?>
        </div>
        
        <!-- Calendar days -->
        <div class="grid grid-cols-7">
            <?php
            // Empty cells before first day
            for ($i = 1; $i < $startDayOfWeek; $i++):
            ?>
            <div class="min-h-[100px] p-2 border-b border-r border-gray-100 bg-gray-50"></div>
            <?php endfor; ?>
            
            <?php
            // Days of month
            for ($day = 1; $day <= $daysInMonth; $day++):
                $isToday = ($day == date('j') && $currentMonth == date('n') && $currentYear == date('Y'));
                $dateStr = sprintf('%04d-%02d-%02d', $currentYear, $currentMonth, $day);
                
                // Get tasks for this day
                $dayTasks = array_filter($tasks ?? [], function($t) use ($dateStr) {
                    return ($t['due_date'] ?? '') === $dateStr;
                });
                
                // Get events for this day - hỗ trợ cả start_time và start_date
                $dayEvents = array_filter($events ?? [], function($e) use ($dateStr) {
                    $eventDate = $e['start_time'] ?? $e['start_date'] ?? '';
                    // Lấy phần date từ datetime nếu có
                    if (strlen($eventDate) > 10) {
                        $eventDate = substr($eventDate, 0, 10);
                    }
                    return $eventDate === $dateStr;
                });
                
                $totalItems = count($dayTasks) + count($dayEvents);
            ?>
            <div class="min-h-[100px] p-2 border-b border-r border-gray-100 hover:bg-gray-50 transition-colors calendar-day" 
                 data-date="<?= $dateStr ?>" onclick="openCreateEventForDate('<?= $dateStr ?>')">
                <div class="flex items-center justify-between mb-2">
                    <span class="<?= $isToday ? 'h-7 w-7 flex items-center justify-center rounded-full bg-primary text-white text-sm font-medium' : 'text-sm text-gray-900' ?>">
                        <?= $day ?>
                    </span>
                </div>
                
                <div class="space-y-1">
                    <?php 
                    $shown = 0;
                    // Show events first
                    foreach (array_slice($dayEvents, 0, 2) as $event): 
                        $shown++;
                    ?>
                    <div class="px-2 py-1 text-xs rounded truncate cursor-pointer event-item"
                         style="background-color: <?= View::e($event['color'] ?? '#6366f1') ?>20; color: <?= View::e($event['color'] ?? '#6366f1') ?>"
                         onclick="event.stopPropagation(); viewEvent('<?= View::e($event['id']) ?>')"
                         title="<?= View::e($event['title']) ?>">
                        <i data-lucide="calendar" class="inline h-3 w-3"></i>
                        <?= View::e($event['title']) ?>
                    </div>
                    <?php endforeach; ?>
                    
                    <?php 
                    // Show tasks
                    foreach (array_slice($dayTasks, 0, 3 - $shown) as $task): 
                        $shown++;
                    ?>
                    <a href="/php/task-detail.php?id=<?= View::e($task['id']) ?>" 
                       onclick="event.stopPropagation()"
                       class="block px-2 py-1 text-xs rounded bg-red-100 text-red-700 truncate hover:bg-red-200"
                       title="<?= View::e($task['title']) ?>">
                        <i data-lucide="check-square" class="inline h-3 w-3"></i>
                        <?= View::e($task['title']) ?>
                    </a>
                    <?php endforeach; ?>
                    
                    <?php if ($totalItems > 3): ?>
                    <span class="text-xs text-gray-500">+<?= $totalItems - 3 ?> khác</span>
                    <?php endif; ?>
                </div>
            </div>
            <?php endfor; ?>
            
            <?php
            // Empty cells after last day
            $totalCells = $startDayOfWeek - 1 + $daysInMonth;
            $remainingCells = 7 - ($totalCells % 7);
            if ($remainingCells < 7):
                for ($i = 0; $i < $remainingCells; $i++):
            ?>
            <div class="min-h-[100px] p-2 border-b border-r border-gray-100 bg-gray-50"></div>
            <?php 
                endfor;
            endif;
            ?>
        </div>
    </div>

    <!-- Upcoming Events & Tasks -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Upcoming Events -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="border-b border-gray-200 px-6 py-4">
                <h3 class="font-semibold text-gray-900">
                    <i data-lucide="calendar" class="inline h-4 w-4 mr-2"></i>
                    Sự kiện sắp tới
                </h3>
            </div>
            <div class="divide-y divide-gray-100">
                <?php 
                $upcomingEvents = array_filter($events ?? [], function($e) {
                    $startDate = $e['start_time'] ?? $e['start_date'] ?? null;
                    if (!$startDate) return false;
                    // Lấy phần date từ datetime nếu có
                    if (strlen($startDate) > 10) {
                        $startDate = substr($startDate, 0, 10);
                    }
                    $diff = strtotime($startDate) - strtotime('today');
                    return $diff >= 0 && $diff < 14 * 24 * 60 * 60; // Next 14 days
                });
                usort($upcomingEvents, function($a, $b) {
                    $aDate = $a['start_time'] ?? $a['start_date'] ?? '';
                    $bDate = $b['start_time'] ?? $b['start_date'] ?? '';
                    return strtotime($aDate) - strtotime($bDate);
                });
                ?>
                
                <?php if (!empty($upcomingEvents)): ?>
                    <?php foreach (array_slice($upcomingEvents, 0, 5) as $event): ?>
                    <div class="px-6 py-4 hover:bg-gray-50 cursor-pointer" onclick="viewEvent('<?= View::e($event['id']) ?>')">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-3 h-3 rounded-full" style="background-color: <?= View::e($event['color'] ?? '#6366f1') ?>"></div>
                                <div>
                                    <p class="font-medium text-gray-900"><?= View::e($event['title']) ?></p>
                                    <?php 
                                    $eventStartDate = $event['start_time'] ?? $event['start_date'] ?? '';
                                    if (strlen($eventStartDate) > 10) $eventStartDate = substr($eventStartDate, 0, 10);
                                    ?>
                                    <p class="text-sm text-gray-500">
                                        <?= date('d/m/Y', strtotime($eventStartDate)) ?>
                                        <?php if (!empty($event['location'])): ?>
                                        · <?= View::e($event['location']) ?>
                                        <?php endif; ?>
                                    </p>
                                </div>
                            </div>
                            <?php 
                            $daysUntil = ceil((strtotime($eventStartDate) - strtotime('today')) / 86400);
                            ?>
                            <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800">
                                <?= $daysUntil == 0 ? 'Hôm nay' : ($daysUntil == 1 ? 'Ngày mai' : $daysUntil . ' ngày') ?>
                            </span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="px-6 py-8 text-center text-gray-500">
                        <i data-lucide="calendar-x" class="h-8 w-8 mx-auto mb-2 text-gray-300"></i>
                        Không có sự kiện nào sắp tới
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Upcoming Tasks -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="border-b border-gray-200 px-6 py-4">
                <h3 class="font-semibold text-gray-900">
                    <i data-lucide="check-square" class="inline h-4 w-4 mr-2"></i>
                    Công việc sắp đến hạn
                </h3>
            </div>
            <div class="divide-y divide-gray-100">
                <?php 
                $upcomingTasks = array_filter($tasks ?? [], function($t) {
                    $dueDate = $t['due_date'] ?? null;
                    if (!$dueDate) return false;
                    $diff = strtotime($dueDate) - strtotime('today');
                    return $diff >= 0 && $diff < 7 * 24 * 60 * 60; // Next 7 days
                });
                usort($upcomingTasks, fn($a, $b) => strtotime($a['due_date']) - strtotime($b['due_date']));
                ?>
                
                <?php if (!empty($upcomingTasks)): ?>
                    <?php foreach (array_slice($upcomingTasks, 0, 5) as $task): ?>
                    <div class="px-6 py-4 hover:bg-gray-50">
                        <div class="flex items-center justify-between">
                            <div>
                                <a href="/php/task-detail.php?id=<?= View::e($task['id']) ?>" 
                                   class="font-medium text-gray-900 hover:text-primary">
                                    <?= View::e($task['title']) ?>
                                </a>
                                <p class="text-sm text-gray-500">
                                    <?= date('d/m/Y', strtotime($task['due_date'])) ?>
                                    <?php if (!empty($task['project_name'])): ?>
                                    · <?= View::e($task['project_name']) ?>
                                    <?php endif; ?>
                                </p>
                            </div>
                            <?php 
                            $daysUntil = ceil((strtotime($task['due_date']) - strtotime('today')) / 86400);
                            $badgeClass = $daysUntil <= 1 ? 'bg-red-100 text-red-800' : 'bg-orange-100 text-orange-800';
                            ?>
                            <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium <?= $badgeClass ?>">
                                <?= $daysUntil == 0 ? 'Hôm nay' : ($daysUntil == 1 ? 'Ngày mai' : $daysUntil . ' ngày') ?>
                            </span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="px-6 py-8 text-center text-gray-500">
                        <i data-lucide="check-circle" class="h-8 w-8 mx-auto mb-2 text-gray-300"></i>
                        Không có công việc nào sắp đến hạn
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php endif; // End calendar/gantt view toggle ?>
</div>

<!-- Create Event Modal -->
<div id="create-event-modal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-black/50" onclick="closeModal('create-event-modal')"></div>
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-lg" onclick="event.stopPropagation()">
            <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4">
                <h2 class="text-lg font-semibold text-gray-900">Thêm sự kiện mới</h2>
                <button onclick="closeModal('create-event-modal')" class="text-gray-400 hover:text-gray-600">
                    <i data-lucide="x" class="h-5 w-5"></i>
                </button>
            </div>
            <form id="create-event-form" class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tiêu đề *</label>
                    <input type="text" name="title" required
                           class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mô tả</label>
                    <textarea name="description" rows="3"
                              class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary"></textarea>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ngày bắt đầu *</label>
                        <input type="date" name="start_date" required value="<?= date('Y-m-d') ?>"
                               class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Giờ bắt đầu</label>
                        <input type="time" name="start_time" value="09:00"
                               class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ngày kết thúc</label>
                        <input type="date" name="end_date"
                               class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Giờ kết thúc</label>
                        <input type="time" name="end_time" value="10:00"
                               class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Loại sự kiện</label>
                        <select name="type" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                            <option value="meeting">Họp</option>
                            <option value="deadline">Deadline</option>
                            <option value="reminder">Nhắc nhở</option>
                            <option value="other">Khác</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Màu sắc</label>
                        <input type="color" name="color" value="#3b82f6"
                               class="w-full h-10 rounded-lg border border-gray-300">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nhắc nhở trước</label>
                        <select name="reminder_minutes" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                            <option value="">Không nhắc</option>
                            <option value="5">5 phút</option>
                            <option value="15">15 phút</option>
                            <option value="30" selected>30 phút</option>
                            <option value="60">1 giờ</option>
                            <option value="1440">1 ngày</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Địa điểm</label>
                        <input type="text" name="location" placeholder="Phòng họp, link meeting..."
                               class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                    </div>
                </div>
                <div class="flex justify-end gap-3 pt-4">
                    <button type="button" onclick="closeModal('create-event-modal')" 
                            class="px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-lg">Hủy</button>
                    <button type="submit" 
                            class="px-4 py-2 text-sm font-medium text-white bg-primary hover:bg-primary/90 rounded-lg">Tạo sự kiện</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View/Edit Event Modal -->
<div id="view-event-modal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-black/50" onclick="closeModal('view-event-modal')"></div>
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-lg" onclick="event.stopPropagation()">
            <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4">
                <h2 class="text-lg font-semibold text-gray-900">Chi tiết sự kiện</h2>
                <button onclick="closeModal('view-event-modal')" class="text-gray-400 hover:text-gray-600">
                    <i data-lucide="x" class="h-5 w-5"></i>
                </button>
            </div>
            <div id="event-details" class="p-6">
                <!-- Event details will be loaded here -->
            </div>
            <div class="flex justify-between border-t border-gray-200 px-6 py-4">
                <button onclick="deleteEvent()" class="px-4 py-2 text-sm font-medium text-red-600 hover:bg-red-50 rounded-lg">
                    <i data-lucide="trash-2" class="inline h-4 w-4 mr-1"></i>Xóa
                </button>
                <div class="flex gap-2">
                    <button onclick="closeModal('view-event-modal')" 
                            class="px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-lg">Đóng</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let currentEventId = null;

// Open create event modal for specific date
function openCreateEventForDate(dateStr) {
    document.querySelector('input[name="start_date"]').value = dateStr;
    document.querySelector('input[name="end_date"]').value = dateStr;
    openModal('create-event-modal');
}

// View event details
function viewEvent(eventId) {
    currentEventId = eventId;
    
    fetch(`/php/api/calendar.php?action=single&event_id=${eventId}`)
        .then(r => r.json())
        .then(result => {
            if (result.success && result.data) {
                const event = result.data;
                // Hỗ trợ cả start_time và start_date
                const startTime = event.start_time || event.start_date;
                const endTime = event.end_time || event.end_date;
                document.getElementById('event-details').innerHTML = `
                    <div class="space-y-4">
                        <div>
                            <div class="flex items-center gap-2 mb-2">
                                <div class="w-4 h-4 rounded" style="background-color: ${event.color || '#6366f1'}"></div>
                                <h3 class="text-xl font-semibold text-gray-900">${escapeHtml(event.title)}</h3>
                            </div>
                            ${event.description ? `<p class="text-gray-600">${escapeHtml(event.description)}</p>` : ''}
                        </div>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-gray-500">Thời gian bắt đầu:</span>
                                <p class="font-medium">${formatDateTime(startTime)}</p>
                            </div>
                            <div>
                                <span class="text-gray-500">Thời gian kết thúc:</span>
                                <p class="font-medium">${endTime ? formatDateTime(endTime) : '-'}</p>
                            </div>
                            ${event.location ? `
                            <div class="col-span-2">
                                <span class="text-gray-500">Địa điểm:</span>
                                <p class="font-medium">${escapeHtml(event.location)}</p>
                            </div>
                            ` : ''}
                        </div>
                    </div>
                `;
                openModal('view-event-modal');
                if (typeof lucide !== 'undefined') lucide.createIcons();
            }
        })
        .catch(err => console.error('Error loading event:', err));
}

// Delete event
function deleteEvent() {
    if (!currentEventId) return;
    if (!confirm('Bạn có chắc muốn xóa sự kiện này?')) return;
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
    
    fetch(`/php/api/calendar.php?event_id=${currentEventId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-Token': csrfToken
        }
    })
    .then(r => r.json())
    .then(result => {
        if (result.success) {
            closeModal('view-event-modal');
            showToast('Đã xóa sự kiện', 'success');
            location.reload();
        } else {
            showToast(result.error || 'Có lỗi xảy ra', 'error');
        }
    })
    .catch(err => showToast('Lỗi kết nối', 'error'));
}

// Create event form
document.getElementById('create-event-form')?.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const data = Object.fromEntries(formData.entries());
    
    // Combine date and time into datetime format for start_time and end_time
    if (data.start_date) {
        data.start_time = data.start_date + (data.start_time ? ' ' + data.start_time + ':00' : ' 00:00:00');
    }
    if (data.end_date) {
        data.end_time = data.end_date + (data.end_time ? ' ' + data.end_time + ':00' : ' 23:59:59');
    } else if (data.start_time) {
        data.end_time = data.start_time;
    }
    
    // Remove separate date/time fields
    delete data.start_date;
    delete data.end_date;
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
    
    fetch('/php/api/calendar.php', {
        method: 'POST',
        headers: { 
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-Token': csrfToken
        },
        body: JSON.stringify(data)
    })
    .then(r => r.json())
    .then(result => {
        if (result.success) {
            closeModal('create-event-modal');
            showToast('Đã tạo sự kiện', 'success');
            location.reload();
        } else {
            showToast(result.error || 'Có lỗi xảy ra', 'error');
        }
    })
    .catch(err => showToast('Lỗi kết nối: ' + err.message, 'error'));
});

// Helper functions
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function formatDate(dateStr) {
    if (!dateStr) return '';
    const date = new Date(dateStr);
    return date.toLocaleDateString('vi-VN', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
}

function formatDateTime(dateTimeStr) {
    if (!dateTimeStr) return '';
    const date = new Date(dateTimeStr);
    return date.toLocaleDateString('vi-VN', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' });
}

function formatReminder(minutes) {
    if (minutes < 60) return `${minutes} phút trước`;
    if (minutes < 1440) return `${minutes / 60} giờ trước`;
    return `${minutes / 1440} ngày trước`;
}
</script>

<?php View::endSection(); ?>
