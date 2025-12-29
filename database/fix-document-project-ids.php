<?php
/**
 * Fix document project_id to match actual project IDs
 */
require_once __DIR__ . '/../bootstrap.php';

$db = \Core\Database::getInstance();

echo "<h2>Fix Document Project IDs</h2>";

// Lấy danh sách projects
$projects = $db->fetchAll("SELECT id, name FROM projects ORDER BY created_at ASC");
echo "<h3>Projects found:</h3><ul>";
foreach ($projects as $p) {
    echo "<li>{$p['id']}: {$p['name']}</li>";
}
echo "</ul>";

// Lấy project đầu tiên (thường là project chính)
$firstProject = $projects[0] ?? null;

if ($firstProject) {
    // Cập nhật tất cả documents có project_id = 'project-1' sang UUID thực
    $count = $db->fetchColumn("SELECT COUNT(*) FROM documents WHERE project_id = 'project-1'");
    
    if ($count > 0) {
        $db->query(
            "UPDATE documents SET project_id = ? WHERE project_id = 'project-1'",
            [$firstProject['id']]
        );
        echo "<p style='color:green'>✓ Updated $count documents from 'project-1' to '{$firstProject['id']}'</p>";
    }
    
    // Cập nhật project-2, project-3 nếu có
    if (isset($projects[1])) {
        $count2 = $db->fetchColumn("SELECT COUNT(*) FROM documents WHERE project_id = 'project-2'");
        if ($count2 > 0) {
            $db->query("UPDATE documents SET project_id = ? WHERE project_id = 'project-2'", [$projects[1]['id']]);
            echo "<p style='color:green'>✓ Updated $count2 documents from 'project-2' to '{$projects[1]['id']}'</p>";
        }
    }
    
    if (isset($projects[2])) {
        $count3 = $db->fetchColumn("SELECT COUNT(*) FROM documents WHERE project_id = 'project-3'");
        if ($count3 > 0) {
            $db->query("UPDATE documents SET project_id = ? WHERE project_id = 'project-3'", [$projects[2]['id']]);
            echo "<p style='color:green'>✓ Updated $count3 documents from 'project-3' to '{$projects[2]['id']}'</p>";
        }
    }
}

// Cũng fix parent_id nếu cần
$db->query("UPDATE documents SET parent_id = NULL WHERE parent_id LIKE 'doc-%'");
echo "<p>✓ Fixed invalid parent_id references</p>";

echo "<h3>Done!</h3>";
echo "<p><a href='/php/documents.php'>Go to Documents</a></p>";
