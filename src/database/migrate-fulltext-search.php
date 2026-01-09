<?php
/**
 * Migration: Add FULLTEXT indexes for advanced search
 * 
 * Chạy: php database/migrate-fulltext-search.php
 */

require_once __DIR__ . '/../bootstrap.php';

echo "===========================================\n";
echo "TASKFLOW - Add FULLTEXT Indexes Migration\n";
echo "===========================================\n\n";

$db = \Core\Database::getInstance();
$pdo = $db->getConnection();

$indexes = [
    [
        'table' => 'tasks',
        'name' => 'ft_tasks_search',
        'columns' => 'title, description',
        'description' => 'Full-text search cho tasks'
    ],
    [
        'table' => 'projects',
        'name' => 'ft_projects_search',
        'columns' => 'name, description',
        'description' => 'Full-text search cho projects'
    ],
    [
        'table' => 'documents',
        'name' => 'ft_documents_search',
        'columns' => 'name, description',
        'description' => 'Full-text search cho documents'
    ],
    [
        'table' => 'comments',
        'name' => 'ft_comments_search',
        'columns' => 'content',
        'description' => 'Full-text search cho comments'
    ],
    [
        'table' => 'users',
        'name' => 'ft_users_search',
        'columns' => 'full_name, email, department, position',
        'description' => 'Full-text search cho users'
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
    
    echo "Creating FULLTEXT index: {$name} on {$table}...\n";
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
        
        // Create FULLTEXT index
        $sql = "ALTER TABLE `{$table}` ADD FULLTEXT INDEX `{$name}` ({$columns})";
        $pdo->exec($sql);
        
        echo "  ✅ SUCCESS\n\n";
        $success++;
        
    } catch (PDOException $e) {
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
    echo "\n🎉 Full-text search indexes created successfully!\n";
} else {
    echo "\n⚠️  Some indexes failed. Check errors above.\n";
}
