<?php
/**
 * Trang lịch nhóm
 */
use Core\View;
use Core\Session;

$currentMonth = $currentMonth ?? (int)date('n');
$currentYear = $currentYear ?? (int)date('Y');

$firstDay = mktime(0, 0, 0, $currentMonth, 1, $currentYear);
$daysInMonth = date('t', $firstDay);
$startDayOfWeek = date('N', $firstDay);

// Tên tháng tiếng Việt
$monthNames = ['', 'Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5', 'Tháng 6', 
               'Tháng 7', 'Tháng 8', 'Tháng 9', 'Tháng 10', 'Tháng 11', 'Tháng 12'];
$monthName = $monthNames[$currentMonth] . ' ' . $currentYear;

$prevMonth = $currentMonth - 1;
$prevYear = $currentYear;
if ($prevMonth < 1) { $prevMonth = 12; $prevYear--; }

$nextMonth = $currentMonth + 1;
$nextYear = $currentYear;
if ($nextMonth > 12) { $nextMonth = 1; $nextYear++; }

View::section('content');
?>

<div class="space-y-6">
    <!-- Tiêu đề -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <h1 class="text-2xl font-bold text-gray-900">Lịch nhóm</h1>
            <div class="h-6 w-px bg-gray-300"></div>
            <h2 class="text-xl font-semibold text-gray-700"><?= $monthName ?></h2>
            <div class="flex items-center gap-1">
                <a href="?month=<?= $prevMonth ?>&year=<?= $prevYear ?>" class="p-2 rounded-lg hover:bg-gray-100">
                    <i data-lucide="chevron-left" class="h-5 w-5"></i>
                </a>
                <a href="?month=<?= date('n') ?>&year=<?= date('Y') ?>" 
                   class="px-3 py-1 text-sm font-medium text-primary hover:bg-primary/10 rounded-lg">
                    Hôm nay
                </a>
                <a href="?month=<?= $nextMonth ?>&year=<?= $nextYear ?>" class="p-2 rounded-lg hover:bg-gray-100">
                    <i data-lucide="chevron-right" class="h-5 w-5"></i>
                </a>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <select id="member-filter" class="px-3 py-2 text-sm border border-gray-200 rounded-lg">
                <option value="">Tất cả thành viên</option>
                <?php foreach ($teamMembers ?? [] as $member): ?>
                <option value="<?= View::e($member['id']) ?>"><?= View::e($member['full_name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <!-- Team Members Legend -->
    <div class="bg-white rounded-xl border border-gray-200 p-4">
        <div class="flex flex-wrap items-center gap-4">
            <span class="text-sm font-medium text-gray-500">Thành viên:</span>
            <?php 
            $colors = ['#ef4444', '#f97316', '#eab308', '#22c55e', '#06b6d4', '#3b82f6', '#8b5cf6', '#ec4899'];
            foreach ($teamMembers ?? [] as $i => $member): 
                $color = $colors[$i % count($colors)];
            ?>
            <div class="flex items-center gap-2">
                <div class="h-3 w-3 rounded-full" style="background-color: <?= $color ?>"></div>
                <span class="text-sm text-gray-700"><?= View::e($member['full_name']) ?></span>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

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
            <div class="min-h-[120px] p-2 border-b border-r border-gray-100 bg-gray-50"></div>
            <?php endfor; ?>
            
            <?php
            // Days of month
            for ($day = 1; $day <= $daysInMonth; $day++):
                $isToday = ($day == date('j') && $currentMonth == date('n') && $currentYear == date('Y'));
                $dateStr = sprintf('%04d-%02d-%02d', $currentYear, $currentMonth, $day);
                
                // Get tasks for this day grouped by assignee
                $dayTasks = array_filter($teamTasks ?? [], function($t) use ($dateStr) {
                    return ($t['due_date'] ?? '') === $dateStr;
                });
                
                // Group by assignee
                $tasksByAssignee = [];
                foreach ($dayTasks as $task) {
                    $assignees = explode(',', $task['assignee_ids'] ?? '');
                    foreach ($assignees as $assigneeId) {
                        if (!empty($assigneeId)) {
                            if (!isset($tasksByAssignee[$assigneeId])) {
                                $tasksByAssignee[$assigneeId] = [];
                            }
                            $tasksByAssignee[$assigneeId][] = $task;
                        }
                    }
                }
            ?>
            <div class="min-h-[120px] p-2 border-b border-r border-gray-100 hover:bg-gray-50 transition-colors">
                <div class="flex items-center justify-between mb-2">
                    <span class="<?= $isToday ? 'h-7 w-7 flex items-center justify-center rounded-full bg-primary text-white text-sm font-medium' : 'text-sm text-gray-900' ?>">
                        <?= $day ?>
                    </span>
                    <?php if (count($dayTasks) > 0): ?>
                    <span class="text-xs text-gray-400"><?= count($dayTasks) ?> việc</span>
                    <?php endif; ?>
                </div>
                
                <div class="space-y-1">
                    <?php 
                    $shown = 0;
                    foreach ($tasksByAssignee as $assigneeId => $tasks):
                        if ($shown >= 3) break;
                        $shown++;
                        
                        // Find member color
                        $memberIndex = 0;
                        foreach ($teamMembers ?? [] as $idx => $m) {
                            if ($m['id'] === $assigneeId) {
                                $memberIndex = $idx;
                                break;
                            }
                        }
                        $color = $colors[$memberIndex % count($colors)];
                    ?>
                    <div class="flex items-center gap-1 px-1.5 py-0.5 rounded text-xs truncate"
                         style="background-color: <?= $color ?>20; color: <?= $color ?>">
                        <span class="h-1.5 w-1.5 rounded-full flex-shrink-0" style="background-color: <?= $color ?>"></span>
                        <span class="truncate"><?= View::e($tasks[0]['title']) ?></span>
                        <?php if (count($tasks) > 1): ?>
                        <span class="text-xs opacity-70">+<?= count($tasks) - 1 ?></span>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                    
                    <?php if (count($tasksByAssignee) > 3): ?>
                    <span class="text-xs text-gray-400">+<?= count($tasksByAssignee) - 3 ?> người khác</span>
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
            <div class="min-h-[120px] p-2 border-b border-r border-gray-100 bg-gray-50"></div>
            <?php 
                endfor;
            endif;
            ?>
        </div>
    </div>

    <!-- Upcoming Deadlines by Member -->
    <div class="grid gap-6 lg:grid-cols-2">
        <!-- This Week -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="border-b border-gray-200 px-6 py-4">
                <h3 class="font-semibold text-gray-900">
                    <i data-lucide="clock" class="inline h-5 w-5 mr-2 text-orange-500"></i>
                    Deadline tuần này
                </h3>
            </div>
            <div class="divide-y divide-gray-100 max-h-[300px] overflow-y-auto">
                <?php 
                $thisWeekTasks = array_filter($allTasks ?? [], function($t) {
                    if (empty($t['due_date'])) return false;
                    $dueDate = strtotime($t['due_date']);
                    $today = strtotime('today');
                    $weekEnd = strtotime('+7 days');
                    return $dueDate >= $today && $dueDate <= $weekEnd && ($t['status'] ?? '') !== 'done';
                });
                usort($thisWeekTasks, fn($a, $b) => strtotime($a['due_date']) - strtotime($b['due_date']));
                ?>
                
                <?php if (!empty($thisWeekTasks)): ?>
                    <?php foreach (array_slice($thisWeekTasks, 0, 8) as $task): 
                        $daysUntil = ceil((strtotime($task['due_date']) - strtotime('today')) / 86400);
                    ?>
                    <div class="px-6 py-3 hover:bg-gray-50">
                        <div class="flex items-center justify-between">
                            <div class="min-w-0 flex-1">
                                <a href="/php/task-detail.php?id=<?= View::e($task['id']) ?>" 
                                   class="font-medium text-gray-900 hover:text-primary truncate block">
                                    <?= View::e($task['title']) ?>
                                </a>
                                <p class="text-xs text-gray-500 mt-1">
                                    <?= View::e($task['assignee_names'] ?? 'Chưa giao') ?>
                                </p>
                            </div>
                            <span class="ml-2 inline-flex items-center rounded-full px-2 py-1 text-xs font-medium 
                                <?= $daysUntil <= 1 ? 'bg-red-100 text-red-800' : 'bg-orange-100 text-orange-800' ?>">
                                <?= $daysUntil == 0 ? 'Hôm nay' : ($daysUntil == 1 ? 'Ngày mai' : $daysUntil . ' ngày') ?>
                            </span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="px-6 py-8 text-center text-gray-500">
                        <i data-lucide="calendar-check" class="h-8 w-8 mx-auto mb-2 text-gray-300"></i>
                        <p>Không có deadline tuần này</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Khối lượng công việc -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="border-b border-gray-200 px-6 py-4">
                <h3 class="font-semibold text-gray-900">
                    <i data-lucide="bar-chart-3" class="inline h-5 w-5 mr-2 text-blue-500"></i>
                    Khối lượng tháng này
                </h3>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <?php foreach ($teamMembers ?? [] as $i => $member): 
                        $color = $colors[$i % count($colors)];
                        $memberTasks = array_filter($teamTasks ?? [], function($t) use ($member) {
                            return strpos($t['assignee_ids'] ?? '', $member['id']) !== false;
                        });
                        $taskCount = count($memberTasks);
                        $maxTasks = 15; // Assume max for visualization
                        $barWidth = min(100, ($taskCount / $maxTasks) * 100);
                    ?>
                    <div class="flex items-center gap-3">
                        <div class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center overflow-hidden flex-shrink-0">
                            <?php if (!empty($member['avatar_url'])): ?>
                            <img src="/php/<?= View::e($member['avatar_url']) ?>" class="h-full w-full object-cover">
                            <?php else: ?>
                            <span class="text-xs font-medium"><?= strtoupper(substr($member['full_name'] ?? 'U', 0, 1)) ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between text-sm mb-1">
                                <span class="text-gray-900 truncate"><?= View::e($member['full_name']) ?></span>
                                <span class="text-gray-500"><?= $taskCount ?> tasks</span>
                            </div>
                            <div class="h-2 rounded-full bg-gray-100 overflow-hidden">
                                <div class="h-full rounded-full" style="width: <?= $barWidth ?>%; background-color: <?= $color ?>"></div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php View::endSection(); ?>
