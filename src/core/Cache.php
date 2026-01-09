<?php
/**
 * Simple File-based Cache
 * Caching layer đơn giản không cần Redis/Memcached
 * Phù hợp cho small-medium applications
 */

namespace Core;

class Cache
{
    private static ?Cache $instance = null;
    private string $cachePath;
    private bool $enabled = true;

    private function __construct()
    {
        $this->cachePath = BASE_PATH . '/storage/cache';
        
        // Tạo thư mục cache nếu chưa có
        if (!is_dir($this->cachePath)) {
            mkdir($this->cachePath, 0755, true);
        }
    }

    public static function getInstance(): Cache
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Lấy giá trị từ cache
     */
    public function get(string $key, mixed $default = null): mixed
    {
        if (!$this->enabled) {
            return $default;
        }

        $file = $this->getCacheFile($key);
        
        if (!file_exists($file)) {
            return $default;
        }

        $data = unserialize(file_get_contents($file));
        
        // Kiểm tra expiry
        if ($data['expiry'] !== 0 && $data['expiry'] < time()) {
            $this->forget($key);
            return $default;
        }

        return $data['value'];
    }

    /**
     * Lưu giá trị vào cache
     * @param int $ttl Time to live in seconds (0 = forever)
     */
    public function set(string $key, mixed $value, int $ttl = 3600): bool
    {
        if (!$this->enabled) {
            return false;
        }

        $file = $this->getCacheFile($key);
        $data = [
            'value' => $value,
            'expiry' => $ttl > 0 ? time() + $ttl : 0,
            'created' => time(),
        ];

        return file_put_contents($file, serialize($data), LOCK_EX) !== false;
    }

    /**
     * Kiểm tra key có tồn tại trong cache không
     */
    public function has(string $key): bool
    {
        return $this->get($key) !== null;
    }

    /**
     * Xóa một key khỏi cache
     */
    public function forget(string $key): bool
    {
        $file = $this->getCacheFile($key);
        
        if (file_exists($file)) {
            return unlink($file);
        }

        return true;
    }

    /**
     * Xóa tất cả cache
     */
    public function flush(): bool
    {
        $files = glob($this->cachePath . '/*.cache');
        
        foreach ($files as $file) {
            unlink($file);
        }

        return true;
    }

    /**
     * Xóa cache theo pattern (prefix)
     */
    public function forgetByPrefix(string $prefix): int
    {
        $pattern = $this->cachePath . '/' . md5($prefix) . '*.cache';
        $files = glob($pattern);
        $count = 0;

        // Fallback: scan all files
        if (empty($files)) {
            $allFiles = glob($this->cachePath . '/*.cache');
            foreach ($allFiles as $file) {
                $key = basename($file, '.cache');
                // Check if original key starts with prefix
                $data = @unserialize(file_get_contents($file));
                if ($data && isset($data['key']) && str_starts_with($data['key'], $prefix)) {
                    unlink($file);
                    $count++;
                }
            }
        } else {
            foreach ($files as $file) {
                unlink($file);
                $count++;
            }
        }

        return $count;
    }

    /**
     * Lấy hoặc set cache (remember pattern)
     */
    public function remember(string $key, int $ttl, callable $callback): mixed
    {
        $value = $this->get($key);

        if ($value !== null) {
            return $value;
        }

        $value = $callback();
        $this->set($key, $value, $ttl);

        return $value;
    }

    /**
     * Lấy hoặc set cache vĩnh viễn
     */
    public function rememberForever(string $key, callable $callback): mixed
    {
        return $this->remember($key, 0, $callback);
    }

    /**
     * Increment giá trị số
     */
    public function increment(string $key, int $value = 1): int
    {
        $current = (int) $this->get($key, 0);
        $new = $current + $value;
        $this->set($key, $new);
        return $new;
    }

    /**
     * Decrement giá trị số
     */
    public function decrement(string $key, int $value = 1): int
    {
        return $this->increment($key, -$value);
    }

    /**
     * Enable/disable cache
     */
    public function setEnabled(bool $enabled): void
    {
        $this->enabled = $enabled;
    }

    /**
     * Lấy đường dẫn file cache
     */
    private function getCacheFile(string $key): string
    {
        return $this->cachePath . '/' . md5($key) . '.cache';
    }

    /**
     * Dọn dẹp cache hết hạn
     */
    public function gc(): int
    {
        $files = glob($this->cachePath . '/*.cache');
        $count = 0;

        foreach ($files as $file) {
            $data = @unserialize(file_get_contents($file));
            
            if ($data && $data['expiry'] !== 0 && $data['expiry'] < time()) {
                unlink($file);
                $count++;
            }
        }

        return $count;
    }

    /**
     * Lấy thống kê cache
     */
    public function stats(): array
    {
        $files = glob($this->cachePath . '/*.cache');
        $totalSize = 0;
        $expired = 0;
        $valid = 0;

        foreach ($files as $file) {
            $totalSize += filesize($file);
            $data = @unserialize(file_get_contents($file));
            
            if ($data && $data['expiry'] !== 0 && $data['expiry'] < time()) {
                $expired++;
            } else {
                $valid++;
            }
        }

        return [
            'total_files' => count($files),
            'valid' => $valid,
            'expired' => $expired,
            'total_size' => $totalSize,
            'total_size_human' => $this->formatBytes($totalSize),
        ];
    }

    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, 2) . ' ' . $units[$i];
    }
}
