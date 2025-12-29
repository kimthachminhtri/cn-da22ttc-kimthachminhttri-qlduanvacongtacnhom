<?php
/**
 * Core Rate Limiter
 * Prevent brute force attacks
 */

namespace Core;

class RateLimiter
{
    private static string $cacheDir = '';
    
    private static function getCacheDir(): string
    {
        if (empty(self::$cacheDir)) {
            self::$cacheDir = BASE_PATH . '/storage/rate_limits';
            if (!is_dir(self::$cacheDir)) {
                mkdir(self::$cacheDir, 0755, true);
            }
        }
        return self::$cacheDir;
    }
    
    /**
     * Check if action is rate limited
     * 
     * @param string $key Unique identifier (e.g., "login:192.168.1.1")
     * @param int $maxAttempts Maximum attempts allowed
     * @param int $decayMinutes Time window in minutes
     * @return bool True if rate limited (blocked), false if allowed
     */
    public static function tooManyAttempts(string $key, int $maxAttempts = 5, int $decayMinutes = 1): bool
    {
        $attempts = self::getAttempts($key, $decayMinutes);
        return $attempts >= $maxAttempts;
    }
    
    /**
     * Record an attempt
     */
    public static function hit(string $key): int
    {
        $file = self::getFile($key);
        $data = self::loadData($file);
        
        $data['attempts'][] = time();
        
        self::saveData($file, $data);
        
        return count($data['attempts']);
    }
    
    /**
     * Get number of attempts in time window
     */
    public static function getAttempts(string $key, int $decayMinutes = 1): int
    {
        $file = self::getFile($key);
        $data = self::loadData($file);
        
        $cutoff = time() - ($decayMinutes * 60);
        $validAttempts = array_filter($data['attempts'], fn($t) => $t > $cutoff);
        
        return count($validAttempts);
    }
    
    /**
     * Clear attempts for a key
     */
    public static function clear(string $key): void
    {
        $file = self::getFile($key);
        if (file_exists($file)) {
            unlink($file);
        }
    }
    
    /**
     * Get remaining attempts
     */
    public static function remainingAttempts(string $key, int $maxAttempts = 5, int $decayMinutes = 1): int
    {
        $attempts = self::getAttempts($key, $decayMinutes);
        return max(0, $maxAttempts - $attempts);
    }
    
    /**
     * Get seconds until rate limit resets
     */
    public static function availableIn(string $key, int $decayMinutes = 1): int
    {
        $file = self::getFile($key);
        $data = self::loadData($file);
        
        if (empty($data['attempts'])) {
            return 0;
        }
        
        $oldestAttempt = min($data['attempts']);
        $resetTime = $oldestAttempt + ($decayMinutes * 60);
        
        return max(0, $resetTime - time());
    }
    
    private static function getFile(string $key): string
    {
        $hash = md5($key);
        return self::getCacheDir() . '/' . $hash . '.json';
    }
    
    private static function loadData(string $file): array
    {
        if (!file_exists($file)) {
            return ['attempts' => []];
        }
        
        $content = file_get_contents($file);
        $data = json_decode($content, true);
        
        return $data ?: ['attempts' => []];
    }
    
    private static function saveData(string $file, array $data): void
    {
        // Clean old attempts (older than 1 hour)
        $cutoff = time() - 3600;
        $data['attempts'] = array_filter($data['attempts'], fn($t) => $t > $cutoff);
        $data['attempts'] = array_values($data['attempts']);
        
        file_put_contents($file, json_encode($data), LOCK_EX);
    }
    
    /**
     * Cleanup old rate limit files
     */
    public static function cleanup(): void
    {
        $dir = self::getCacheDir();
        $files = glob($dir . '/*.json');
        $cutoff = time() - 3600;
        
        foreach ($files as $file) {
            if (filemtime($file) < $cutoff) {
                unlink($file);
            }
        }
    }
}
