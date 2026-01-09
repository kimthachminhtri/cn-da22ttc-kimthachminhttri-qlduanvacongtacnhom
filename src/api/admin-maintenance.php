<?php
/**
 * API: Admin Maintenance Tools
 * Clear cache, logs, optimize database, health check
 */
require_once __DIR__ . '/../bootstrap.php';
require_once BASE_PATH . '/includes/csrf.php';
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    csrf_require();
}

use App\Middleware\AuthMiddleware;
use App\Middleware\PermissionMiddleware;
use Core\Database;
use Core\Logger;
use Core\Session;

header('Content-Type: application/json');

AuthMiddleware::handle();
PermissionMiddleware::requireAdmin();

$action = $_GET['action'] ?? $_POST['action'] ?? '';

// Handle JSON input
$input = json_decode(file_get_contents('php://input'), true) ?? [];
if (isset($input['action'])) {
    $action = $input['action'];
}

try {
    $db = Database::getInstance();
    
    switch ($action) {
        case 'clear_cache':
            $cleared = clearCacheFiles();
            Logger::activity('maintenance', 'system', null, 'Cleared cache files');
            echo json_encode([
                'success' => true,
                'message' => "Đã xóa {$cleared} file cache",
            ]);
            break;
            
        case 'clear_logs':
            $deleted = $db->query(
                "DELETE FROM activity_logs WHERE created_at < DATE_SUB(NOW(), INTERVAL 30 DAY)"
            );
            $count = $deleted->rowCount();
            Logger::activity('maintenance', 'system', null, "Cleared {$count} old logs");
            echo json_encode([
                'success' => true,
                'message' => "Đã xóa {$count} logs cũ hơn 30 ngày",
            ]);
            break;
            
        case 'optimize_db':
            $tables = $db->fetchAll("SHOW TABLES");
            $optimized = 0;
            foreach ($tables as $table) {
                $tableName = array_values($table)[0];
                $db->query("OPTIMIZE TABLE `{$tableName}`");
                $optimized++;
            }
            Logger::activity('maintenance', 'system', null, "Optimized {$optimized} tables");
            echo json_encode([
                'success' => true,
                'message' => "Đã tối ưu {$optimized} bảng",
            ]);
            break;
            
        case 'health_check':
            $health = performHealthCheck($db);
            echo json_encode([
                'success' => true,
                'data' => $health,
            ]);
            break;
        
        case 'create_backup':
            $result = createBackup($db);
            if ($result['success']) {
                Session::flash('success', $result['message']);
            } else {
                Session::flash('error', $result['message']);
            }
            header('Location: /php/admin/backup.php');
            exit;
            
        case 'download_backup':
            $filename = $_GET['file'] ?? null;
            downloadBackup($db, $filename);
            break;
            
        case 'delete_backup':
            $filename = $input['filename'] ?? $_POST['filename'] ?? null;
            $result = deleteBackup($filename);
            echo json_encode($result);
            break;
            
        case 'restore_backup':
            // Restore from uploaded file
            if (empty($_FILES['backup_file'])) {
                Session::flash('error', 'Vui lòng chọn file backup');
                header('Location: /php/admin/backup.php');
                exit;
            }
            $result = restoreFromUpload($db, $_FILES['backup_file']);
            Session::flash($result['success'] ? 'success' : 'error', $result['message']);
            header('Location: /php/admin/backup.php');
            exit;
            
        case 'restore_from_file':
            $filename = $_POST['filename'] ?? null;
            $result = restoreFromFile($db, $filename);
            Session::flash($result['success'] ? 'success' : 'error', $result['message']);
            header('Location: /php/admin/backup.php');
            exit;
            
        default:
            throw new Exception('Invalid action');
    }
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
    ]);
}

/**
 * Clear cache files from various directories
 */
function clearCacheFiles(): int {
    $cleared = 0;
    $cacheDirs = [
        __DIR__ . '/../cache',
        __DIR__ . '/../storage/cache',
        __DIR__ . '/../tmp',
    ];
    
    foreach ($cacheDirs as $dir) {
        if (is_dir($dir)) {
            $files = glob($dir . '/*');
            foreach ($files as $file) {
                if (is_file($file) && basename($file) !== '.gitkeep') {
                    unlink($file);
                    $cleared++;
                }
            }
        }
    }
    
    return $cleared;
}

/**
 * Perform system health check
 */
function performHealthCheck($db): array {
    $checks = [];
    
    // Database connection
    try {
        $db->fetchColumn("SELECT 1");
        $checks['database'] = ['status' => 'ok', 'message' => 'Kết nối database bình thường'];
    } catch (Exception $e) {
        $checks['database'] = ['status' => 'error', 'message' => 'Lỗi kết nối database'];
    }
    
    // Disk space
    $diskFree = disk_free_space('.');
    $diskTotal = disk_total_space('.');
    $diskUsedPercent = round((1 - $diskFree / $diskTotal) * 100);
    if ($diskUsedPercent > 90) {
        $checks['disk'] = ['status' => 'warning', 'message' => "Disk sử dụng {$diskUsedPercent}% - Cần dọn dẹp"];
    } else {
        $checks['disk'] = ['status' => 'ok', 'message' => "Disk sử dụng {$diskUsedPercent}%"];
    }
    
    // Memory
    $memoryUsage = memory_get_usage(true);
    $memoryLimit = ini_get('memory_limit');
    $checks['memory'] = ['status' => 'ok', 'message' => "Memory: " . formatBytes($memoryUsage) . " / {$memoryLimit}"];
    
    // Upload directory
    $uploadDir = __DIR__ . '/../uploads';
    if (is_writable($uploadDir)) {
        $checks['uploads'] = ['status' => 'ok', 'message' => 'Thư mục uploads có thể ghi'];
    } else {
        $checks['uploads'] = ['status' => 'error', 'message' => 'Thư mục uploads không thể ghi'];
    }
    
    // Logs directory
    $logsDir = __DIR__ . '/../logs';
    if (is_writable($logsDir)) {
        $checks['logs'] = ['status' => 'ok', 'message' => 'Thư mục logs có thể ghi'];
    } else {
        $checks['logs'] = ['status' => 'warning', 'message' => 'Thư mục logs không thể ghi'];
    }
    
    // PHP extensions
    $requiredExtensions = ['pdo', 'pdo_mysql', 'json', 'mbstring', 'fileinfo'];
    $missingExtensions = [];
    foreach ($requiredExtensions as $ext) {
        if (!extension_loaded($ext)) {
            $missingExtensions[] = $ext;
        }
    }
    if (empty($missingExtensions)) {
        $checks['extensions'] = ['status' => 'ok', 'message' => 'Tất cả PHP extensions đã cài đặt'];
    } else {
        $checks['extensions'] = ['status' => 'error', 'message' => 'Thiếu extensions: ' . implode(', ', $missingExtensions)];
    }
    
    // Overall status
    $hasError = false;
    $hasWarning = false;
    foreach ($checks as $check) {
        if ($check['status'] === 'error') $hasError = true;
        if ($check['status'] === 'warning') $hasWarning = true;
    }
    
    return [
        'overall' => $hasError ? 'error' : ($hasWarning ? 'warning' : 'ok'),
        'checks' => $checks,
        'timestamp' => date('Y-m-d H:i:s'),
    ];
}

function formatBytes($bytes, $precision = 2) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    return round($bytes / pow(1024, $pow), $precision) . ' ' . $units[$pow];
}

/**
 * Create a new backup
 */
function createBackup($db): array {
    try {
        $backupDir = BASE_PATH . '/storage/backups';
        if (!is_dir($backupDir)) {
            mkdir($backupDir, 0755, true);
        }
        
        $filename = 'taskflow_backup_' . date('Y-m-d_His') . '.sql';
        $filepath = $backupDir . '/' . $filename;
        
        $sql = generateBackupSQL($db);
        
        if (file_put_contents($filepath, $sql) !== false) {
            Logger::activity('backup', 'system', null, "Created backup: {$filename}");
            return ['success' => true, 'message' => 'Tạo backup thành công: ' . $filename];
        } else {
            return ['success' => false, 'message' => 'Không thể tạo file backup'];
        }
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()];
    }
}

/**
 * Download backup (generate on-the-fly or from file)
 */
function downloadBackup($db, ?string $filename = null): void {
    if ($filename) {
        // Download existing backup file
        $filename = basename($filename); // Sanitize
        $filepath = BASE_PATH . '/storage/backups/' . $filename;
        
        if (!file_exists($filepath)) {
            http_response_code(404);
            echo json_encode(['success' => false, 'error' => 'File không tồn tại']);
            exit;
        }
        
        header('Content-Type: application/sql');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . filesize($filepath));
        readfile($filepath);
    } else {
        // Generate and download on-the-fly
        $sql = generateBackupSQL($db);
        $filename = 'taskflow_backup_' . date('Y-m-d_His') . '.sql';
        
        header('Content-Type: application/sql');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . strlen($sql));
        echo $sql;
    }
    exit;
}

/**
 * Delete a backup file
 */
function deleteBackup(?string $filename): array {
    if (!$filename) {
        return ['success' => false, 'error' => 'Thiếu tên file'];
    }
    
    $filename = basename($filename); // Sanitize
    $filepath = BASE_PATH . '/storage/backups/' . $filename;
    
    if (!file_exists($filepath)) {
        return ['success' => false, 'error' => 'File không tồn tại'];
    }
    
    if (unlink($filepath)) {
        Logger::activity('backup', 'system', null, "Deleted backup: {$filename}");
        return ['success' => true, 'message' => 'Đã xóa backup'];
    } else {
        return ['success' => false, 'error' => 'Không thể xóa file'];
    }
}

/**
 * Restore from uploaded file
 */
function restoreFromUpload($db, array $file): array {
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'Lỗi upload file'];
    }
    
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if ($ext !== 'sql') {
        return ['success' => false, 'message' => 'Chỉ chấp nhận file .sql'];
    }
    
    $sql = file_get_contents($file['tmp_name']);
    return executeRestore($db, $sql, $file['name']);
}

/**
 * Restore from existing backup file
 */
function restoreFromFile($db, ?string $filename): array {
    if (!$filename) {
        return ['success' => false, 'message' => 'Thiếu tên file'];
    }
    
    $filename = basename($filename); // Sanitize
    $filepath = BASE_PATH . '/storage/backups/' . $filename;
    
    if (!file_exists($filepath)) {
        return ['success' => false, 'message' => 'File backup không tồn tại'];
    }
    
    $sql = file_get_contents($filepath);
    return executeRestore($db, $sql, $filename);
}

/**
 * Execute restore SQL
 */
function executeRestore($db, string $sql, string $source): array {
    try {
        // Split SQL into statements
        $statements = array_filter(
            array_map('trim', preg_split('/;[\r\n]+/', $sql)),
            fn($s) => !empty($s) && !str_starts_with(trim($s), '--')
        );
        
        $db->beginTransaction();
        
        foreach ($statements as $statement) {
            $statement = trim($statement);
            if (!empty($statement) && !str_starts_with($statement, '--')) {
                $db->query($statement);
            }
        }
        
        $db->commit();
        Logger::activity('restore', 'system', null, "Restored from: {$source}");
        return ['success' => true, 'message' => 'Khôi phục dữ liệu thành công từ: ' . $source];
        
    } catch (Exception $e) {
        $db->rollback();
        return ['success' => false, 'message' => 'Lỗi khôi phục: ' . $e->getMessage()];
    }
}

/**
 * Generate SQL backup content
 */
function generateBackupSQL($db): string {
    $tables = $db->fetchAll("SHOW TABLES");
    
    $sql = "-- =============================================\n";
    $sql .= "-- TaskFlow Database Backup\n";
    $sql .= "-- Generated: " . date('Y-m-d H:i:s') . "\n";
    $sql .= "-- Database: taskflow2\n";
    $sql .= "-- =============================================\n\n";
    $sql .= "SET NAMES utf8mb4;\n";
    $sql .= "SET FOREIGN_KEY_CHECKS = 0;\n\n";
    
    foreach ($tables as $table) {
        $tableName = array_values($table)[0];
        
        // Get create table statement
        $createTable = $db->fetchOne("SHOW CREATE TABLE `{$tableName}`");
        $sql .= "-- Table: {$tableName}\n";
        $sql .= "DROP TABLE IF EXISTS `{$tableName}`;\n";
        $sql .= $createTable['Create Table'] . ";\n\n";
        
        // Get table data
        $rows = $db->fetchAll("SELECT * FROM `{$tableName}`");
        if (!empty($rows)) {
            $sql .= "-- Data for {$tableName}\n";
            foreach ($rows as $row) {
                $values = array_map(function($v) {
                    if ($v === null) return 'NULL';
                    return "'" . addslashes($v) . "'";
                }, array_values($row));
                $sql .= "INSERT INTO `{$tableName}` VALUES (" . implode(', ', $values) . ");\n";
            }
            $sql .= "\n";
        }
    }
    
    $sql .= "SET FOREIGN_KEY_CHECKS = 1;\n";
    $sql .= "-- End of backup\n";
    
    return $sql;
}
