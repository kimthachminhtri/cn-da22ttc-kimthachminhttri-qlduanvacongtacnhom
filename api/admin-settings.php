<?php
/**
 * API: Admin Settings
 * Quản lý cài đặt hệ thống
 */

require_once __DIR__ . '/../includes/config.php';

// Check admin
if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
    http_response_code(403);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'Admin access required']);
    exit;
}

$action = $_GET['action'] ?? null;
$method = $_SERVER['REQUEST_METHOD'];

try {
    $db = Database::getInstance();
    
    // Handle GET actions
    if ($method === 'GET' && $action) {
        switch ($action) {
            case 'clear_cache':
                // Clear multiple cache locations
                $cleared = 0;
                
                // Storage cache
                $cacheDir = BASE_PATH . '/storage/cache';
                if (!is_dir($cacheDir)) {
                    mkdir($cacheDir, 0755, true);
                }
                $files = glob($cacheDir . '/*');
                foreach ($files as $file) {
                    if (is_file($file)) {
                        unlink($file);
                        $cleared++;
                    }
                }
                
                // Logs older than 7 days
                $logsDir = BASE_PATH . '/logs';
                if (is_dir($logsDir)) {
                    $logFiles = glob($logsDir . '/*.log');
                    foreach ($logFiles as $file) {
                        if (is_file($file) && filemtime($file) < strtotime('-7 days')) {
                            unlink($file);
                            $cleared++;
                        }
                    }
                }
                
                // Clear PHP opcache if available
                if (function_exists('opcache_reset')) {
                    opcache_reset();
                }
                
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'message' => "Đã xóa $cleared files cache"]);
                break;
                
            case 'backup':
                // Create database backup
                $backupFile = 'taskflow-backup-' . date('Y-m-d-His') . '.sql';
                $tables = $db->fetchAll("SHOW TABLES");
                
                $sql = "-- TaskFlow Database Backup\n";
                $sql .= "-- Generated: " . date('Y-m-d H:i:s') . "\n";
                $sql .= "-- Server: " . ($_SERVER['SERVER_NAME'] ?? 'localhost') . "\n\n";
                $sql .= "SET FOREIGN_KEY_CHECKS=0;\n\n";
                
                foreach ($tables as $table) {
                    $tableName = array_values($table)[0];
                    
                    // Drop table if exists
                    $sql .= "DROP TABLE IF EXISTS `$tableName`;\n";
                    
                    // Get create table statement
                    $createTable = $db->fetchOne("SHOW CREATE TABLE `$tableName`");
                    $sql .= $createTable['Create Table'] . ";\n\n";
                    
                    // Get data
                    $rows = $db->fetchAll("SELECT * FROM `$tableName`");
                    if (!empty($rows)) {
                        $columns = array_keys($rows[0]);
                        $sql .= "INSERT INTO `$tableName` (`" . implode('`, `', $columns) . "`) VALUES\n";
                        
                        $rowValues = [];
                        foreach ($rows as $row) {
                            $values = array_map(function($v) {
                                if ($v === null) return 'NULL';
                                return "'" . addslashes($v) . "'";
                            }, array_values($row));
                            $rowValues[] = "(" . implode(', ', $values) . ")";
                        }
                        $sql .= implode(",\n", $rowValues) . ";\n\n";
                    }
                }
                
                $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";
                
                header('Content-Type: application/sql; charset=utf-8');
                header('Content-Disposition: attachment; filename="' . $backupFile . '"');
                header('Content-Length: ' . strlen($sql));
                echo $sql;
                exit;
                
            case 'cleanup_logs':
                $deleted = 0;
                
                // Check if activity_logs table exists
                $tableExists = $db->fetchAll("SHOW TABLES LIKE 'activity_logs'");
                if (!empty($tableExists)) {
                    // Delete old activity logs (older than 30 days)
                    $db->query("DELETE FROM activity_logs WHERE created_at < DATE_SUB(NOW(), INTERVAL 30 DAY)");
                    $deleted += $db->getConnection()->affected_rows ?? 0;
                }
                
                // Also clean notifications older than 60 days
                $notifTableExists = $db->fetchAll("SHOW TABLES LIKE 'notifications'");
                if (!empty($notifTableExists)) {
                    $db->query("DELETE FROM notifications WHERE is_read = 1 AND created_at < DATE_SUB(NOW(), INTERVAL 60 DAY)");
                    $deleted += $db->getConnection()->affected_rows ?? 0;
                }
                
                // Clean old sessions (if using database sessions)
                $sessTableExists = $db->fetchAll("SHOW TABLES LIKE 'sessions'");
                if (!empty($sessTableExists)) {
                    $db->query("DELETE FROM sessions WHERE last_activity < DATE_SUB(NOW(), INTERVAL 7 DAY)");
                    $deleted += $db->getConnection()->affected_rows ?? 0;
                }
                
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'deleted' => $deleted, 'message' => "Đã xóa $deleted records"]);
                break;
                
            case 'test_email':
                // Check if PHPMailer exists
                $phpmailerPath = BASE_PATH . '/vendor/autoload.php';
                if (file_exists($phpmailerPath)) {
                    // TODO: Implement actual email test
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'error' => 'Chức năng test email đang phát triển']);
                } else {
                    header('Content-Type: application/json');
                    echo json_encode([
                        'success' => false, 
                        'error' => 'PHPMailer chưa được cài đặt. Chạy: composer require phpmailer/phpmailer'
                    ]);
                }
                break;
                
            case 'system_info':
                // Get system information
                $info = [
                    'php_version' => phpversion(),
                    'server' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
                    'database' => 'MySQL',
                    'memory_usage' => round(memory_get_usage(true) / 1024 / 1024, 2) . ' MB',
                    'disk_free' => round(disk_free_space(BASE_PATH) / 1024 / 1024 / 1024, 2) . ' GB',
                    'upload_max' => ini_get('upload_max_filesize'),
                    'post_max' => ini_get('post_max_size'),
                ];
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'data' => $info]);
                break;
                
            default:
                throw new Exception('Invalid action');
        }
        exit;
    }
    
    // Handle POST - Save settings
    if ($method === 'POST') {
        header('Content-Type: application/json');
        
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input) {
            throw new Exception('Invalid JSON input');
        }
        
        $type = $input['type'] ?? 'general';
        unset($input['type']);
        
        // Check if system_settings table exists
        $tableExists = $db->fetchAll("SHOW TABLES LIKE 'system_settings'");
        if (empty($tableExists)) {
            // Create table if not exists
            $db->query("CREATE TABLE IF NOT EXISTS `system_settings` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `setting_key` VARCHAR(100) NOT NULL UNIQUE,
                `setting_value` TEXT NULL,
                `setting_group` VARCHAR(50) DEFAULT 'general',
                `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
                `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX `idx_settings_key` (`setting_key`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        }
        
        // Save each setting
        $saved = 0;
        foreach ($input as $key => $value) {
            if (empty($key)) continue;
            
            $existing = $db->fetchOne("SELECT * FROM system_settings WHERE setting_key = ?", [$key]);
            
            if ($existing) {
                $db->update('system_settings', [
                    'setting_value' => $value, 
                    'updated_at' => date('Y-m-d H:i:s')
                ], 'setting_key = ?', [$key]);
            } else {
                $db->insert('system_settings', [
                    'setting_key' => $key,
                    'setting_value' => $value,
                    'setting_group' => $type,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            }
            $saved++;
        }
        
        echo json_encode(['success' => true, 'message' => "Đã lưu $saved cài đặt"]);
        exit;
    }
    
    throw new Exception('Invalid request');
    
} catch (Exception $e) {
    http_response_code(400);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
