<?php
/**
 * API: Admin Maintenance Tools
 * Clear cache, logs, optimize database, health check
 */
require_once __DIR__ . '/../bootstrap.php';

use App\Middleware\AuthMiddleware;
use App\Middleware\PermissionMiddleware;
use Core\Database;
use Core\Logger;

header('Content-Type: application/json');

AuthMiddleware::handle();
PermissionMiddleware::requireAdmin();

$action = $_GET['action'] ?? $_POST['action'] ?? '';

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
            $count = $db->rowCount();
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
