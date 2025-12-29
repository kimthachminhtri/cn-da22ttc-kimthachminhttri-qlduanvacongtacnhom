<?php
/**
 * Migration: Create documents table
 */

require_once __DIR__ . '/../includes/config.php';

try {
    $db = Database::getInstance();
    
    echo "<h2>Migration: Documents Table</h2>";
    
    // Check if table exists
    $tables = $db->fetchAll("SHOW TABLES LIKE 'documents'");
    
    if (empty($tables)) {
        echo "<p>Creating documents table...</p>";
        
        $sql = "CREATE TABLE `documents` (
            `id` VARCHAR(36) PRIMARY KEY,
            `name` VARCHAR(255) NOT NULL,
            `type` ENUM('file', 'folder') NOT NULL DEFAULT 'file',
            `mime_type` VARCHAR(100) NULL,
            `file_size` BIGINT UNSIGNED NULL,
            `file_path` VARCHAR(500) NULL,
            `is_starred` TINYINT(1) NOT NULL DEFAULT 0,
            `parent_id` VARCHAR(36) NULL,
            `project_id` VARCHAR(36) NULL,
            `uploaded_by` VARCHAR(36) NULL,
            `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
            `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            
            INDEX `idx_documents_parent` (`parent_id`),
            INDEX `idx_documents_project` (`project_id`),
            INDEX `idx_documents_type` (`type`),
            INDEX `idx_documents_starred` (`is_starred`),
            INDEX `idx_documents_uploaded_by` (`uploaded_by`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        $db->query($sql);
        
        echo "<p style='color: green;'>✅ Created documents table!</p>";
    } else {
        echo "<p style='color: orange;'>⚠️ Documents table already exists!</p>";
        
        // Check if is_starred column exists
        $columns = $db->fetchAll("SHOW COLUMNS FROM documents LIKE 'is_starred'");
        
        if (empty($columns)) {
            echo "<p>Adding is_starred column...</p>";
            $db->query("ALTER TABLE documents ADD COLUMN is_starred TINYINT(1) NOT NULL DEFAULT 0 AFTER file_path");
            $db->query("CREATE INDEX idx_documents_starred ON documents(is_starred)");
            echo "<p style='color: green;'>✅ Added is_starred column!</p>";
        } else {
            echo "<p>is_starred column already exists.</p>";
        }
    }
    
    // Show current structure
    echo "<h3>Current documents table structure:</h3>";
    $structure = $db->fetchAll("DESCRIBE documents");
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    foreach ($structure as $col) {
        echo "<tr>";
        echo "<td>{$col['Field']}</td>";
        echo "<td>{$col['Type']}</td>";
        echo "<td>{$col['Null']}</td>";
        echo "<td>{$col['Key']}</td>";
        echo "<td>{$col['Default']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Create uploads directory
    $uploadDir = __DIR__ . '/../uploads/documents/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
        echo "<p style='color: green;'>✅ Created uploads/documents/ directory!</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<p><a href='../documents.php'>← Back to Documents</a></p>";
