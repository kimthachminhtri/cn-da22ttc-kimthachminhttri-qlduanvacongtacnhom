<?php
/**
 * Core Logger Class
 * Simple file-based logging system
 */

namespace Core;

class Logger
{
    private static string $logPath = '';
    
    private static function getLogPath(): string
    {
        if (empty(self::$logPath)) {
            self::$logPath = BASE_PATH . '/logs';
            if (!is_dir(self::$logPath)) {
                mkdir(self::$logPath, 0755, true);
            }
        }
        return self::$logPath;
    }
    
    private static function write(string $level, string $message, array $context = []): void
    {
        $logFile = self::getLogPath() . '/' . date('Y-m-d') . '.log';
        
        $timestamp = date('Y-m-d H:i:s');
        $contextStr = !empty($context) ? ' ' . json_encode($context, JSON_UNESCAPED_UNICODE) : '';
        $logLine = "[{$timestamp}] [{$level}] {$message}{$contextStr}" . PHP_EOL;
        
        file_put_contents($logFile, $logLine, FILE_APPEND | LOCK_EX);
    }
    
    public static function emergency(string $message, array $context = []): void
    {
        self::write('EMERGENCY', $message, $context);
    }
    
    public static function alert(string $message, array $context = []): void
    {
        self::write('ALERT', $message, $context);
    }
    
    public static function critical(string $message, array $context = []): void
    {
        self::write('CRITICAL', $message, $context);
    }
    
    public static function error(string $message, array $context = []): void
    {
        self::write('ERROR', $message, $context);
    }
    
    public static function warning(string $message, array $context = []): void
    {
        self::write('WARNING', $message, $context);
    }
    
    public static function notice(string $message, array $context = []): void
    {
        self::write('NOTICE', $message, $context);
    }
    
    public static function info(string $message, array $context = []): void
    {
        self::write('INFO', $message, $context);
    }
    
    public static function debug(string $message, array $context = []): void
    {
        self::write('DEBUG', $message, $context);
    }
    
    /**
     * Log exception with full trace
     */
    public static function exception(\Throwable $e, array $context = []): void
    {
        $context = array_merge($context, [
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString(),
        ]);
        
        self::error($e->getMessage(), $context);
    }
    
    /**
     * Log user activity
     */
    public static function activity(string $action, ?string $userId = null, array $data = []): void
    {
        $context = [
            'user_id' => $userId ?? Session::get('user_id'),
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
            'data' => $data,
        ];
        
        self::info("Activity: {$action}", $context);
    }
}
