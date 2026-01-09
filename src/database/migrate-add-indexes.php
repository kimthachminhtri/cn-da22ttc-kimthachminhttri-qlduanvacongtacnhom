<?php
/**
 * Migration: Add composite indexes for performance optimization
 * 
 * Chạy: php database/migrate-add-indexes.php
 */

require_once __DIR__ . '/../bootstrap.php';

echo "===========================================\n";
echo "TASKFLOW - Add Composite Indexes Migration\n";
echo "===========================================\n\n";

$db = \Core\Database::getInstance();
$pdo = $db->getConnection();

$indexes = [
    // Tasks indexes
    [
        'table' => 'tasks',
        'name' => 'idx_tasks_project_status',
        'columns' => 'project_id, status, position',
        'description' => 'Kanban board queries'
    ],
    [
        'table' => 'tasks',
        'name' => 'idx_tasks_due_date_status',
        'columns' => 'due_date, status',
        'description' => 'Calendar/My Tasks queries'
    ],
    [
        'table' => 'tasks',
        'name' => 'idx_tasks_overdue',
        'columns' => 'status, due_date, priority',
        'description' => 'Overdue tasks queries'
    ],
    
    // Project members indexes
    [
        'table' => 'project_members',
        'name' => 'idx_pm_user_role',
        'columns' => 'user_id, role',
        'description' => 'Permission check queries'
    ],
    
    // Task assignees indexes
    [
        'table' => 'task_assignees',
        'name' => 'idx_ta_user_task',
        'columns' => 'user_id, task_id',
        'description' => 'User tasks queries'
    ],
    
    // Comments indexes
    [
        'table' => 'comments',
        'name' => 'idx_comments_entity_created',
        'columns' => 'entity_type, entity_id, created_at DESC',
        'description' => 'Comments loading'
    ],
    
    // Notifications indexes
    [
        'table' => 'notifications',
        'name' => 'idx_notif_user_unread',
        'columns' => 'user_id, is_read, created_at DESC',
        'description' => 'Unread notifications queries'
    ],
    
    // Documents indexes
    [
        'table' => 'documents',
        'name' => 'idx_docs_project_type',
        'columns' => 'project_id, type, name',
        'description' => 'Documents by project queries'
    ],
    [
        'table' => 'documents',
        'name' => 'idx_docs_starred_user',
        'columns' => 'uploaded_by, is_starred',
        'description' => 'Starred documents queries'
    ],
    
    // Activity logs indexes
    [
        'table' => 'activity_logs',
        'name' => 'idx_activity_entity_time',
        'columns' => 'entity_type, entity_id, created_at DESC',
        'description' => 'Activity by entity queries'
    ],
    
    // Calendar events indexes
    [
        'table' => 'calendar_events',
        'name' => 'idx_events_user_dates',
        'columns' => 'created_by, start_time, end_time',
        'description' => 'User events in date range'
    ],
];

$success = 0;
$skipped = 0;
$failed = 0;

foreach ($indexes as $index) {
    $table = $index['table'];
    $name = $index['name'];
    $columns = $index['columns'];
    $desc = $index['description'];
    
    echo "Creating index: {$name} on {$table}...\n";
    echo "  Purpose: {$desc}\n";
    
    try {
        // Check if index already exists
        $checkSql = "SHOW INDEX FROM `{$table}` WHERE Key_name = ?";
        $existing = $pdo->prepare($checkSql);
        $existing->execute([$name]);
        
        if ($existing->rowCount() > 0) {
            echo "  ⏭️  SKIPPED (already exists)\n\n";
            $skipped++;
            continue;
        }
        
        // Create index
        $sql = "CREATE INDEX `{$name}` ON `{$table}` ({$columns})";
        $pdo->exec($sql);
        
        echo "  ✅ SUCCESS\n\n";
        $success++;
        
    } catch (PDOException $e) {
        // Check if it's a "duplicate key" error (index exists)
        if (strpos($e->getMessage(), 'Duplicate') !== false) {
            echo "  ⏭️  SKIPPED (already exists)\n\n";
            $skipped++;
        } else {
            echo "  ❌ FAILED: " . $e->getMessage() . "\n\n";
            $failed++;
        }
    }
}

echo "===========================================\n";
echo "SUMMARY\n";
echo "===========================================\n";
echo "✅ Created: {$success}\n";
echo "⏭️  Skipped: {$skipped}\n";
echo "❌ Failed:  {$failed}\n";
echo "===========================================\n";

if ($failed === 0) {
    echo "\n🎉 Migration completed successfully!\n";
    echo "Database performance has been optimized.\n";
} else {
    echo "\n⚠️  Some indexes failed to create.\n";
    echo "Please check the errors above.\n";
}
