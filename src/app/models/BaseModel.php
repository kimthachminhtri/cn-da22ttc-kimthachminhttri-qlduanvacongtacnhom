<?php

declare(strict_types=1);

/**
 * Base Model Class
 * 
 * Abstract base class cho tất cả models trong hệ thống.
 * Cung cấp các phương thức CRUD cơ bản và utilities.
 * 
 * @package App\Models
 */

namespace App\Models;

use Core\Database;
use Core\Cache;

abstract class BaseModel
{
    protected Database $db;
    protected string $table;
    protected string $primaryKey = 'id';
    
    /** @var int Cache TTL in seconds (default 5 minutes) */
    protected int $cacheTtl = 300;
    
    /** @var bool Enable caching for this model */
    protected bool $cacheEnabled = true;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Lấy tất cả records
     * 
     * @param string $orderBy Cột và hướng sắp xếp
     * @return array<int, array<string, mixed>>
     */
    public function all(string $orderBy = 'created_at DESC'): array
    {
        $orderBy = $this->sanitizeOrderBy($orderBy);
        $sql = "SELECT * FROM {$this->table} ORDER BY {$orderBy}";
        return $this->db->fetchAll($sql);
    }

    /**
     * Tìm record theo primary key
     * 
     * @param string|int $id Primary key value
     * @return array<string, mixed>|null
     */
    public function find(string|int $id): ?array
    {
        $cacheKey = $this->getCacheKey('find', $id);
        
        if ($this->cacheEnabled) {
            $cached = Cache::getInstance()->get($cacheKey);
            if ($cached !== null) {
                return $cached;
            }
        }
        
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ?";
        $result = $this->db->fetchOne($sql, [$id]);
        
        if ($this->cacheEnabled && $result) {
            Cache::getInstance()->set($cacheKey, $result, $this->cacheTtl);
        }
        
        return $result;
    }

    /**
     * Tìm record theo một column
     * 
     * @param string $column Tên cột
     * @param mixed $value Giá trị cần tìm
     * @return array<string, mixed>|null
     */
    public function findBy(string $column, mixed $value): ?array
    {
        $column = $this->sanitizeColumn($column);
        $sql = "SELECT * FROM {$this->table} WHERE {$column} = ?";
        return $this->db->fetchOne($sql, [$value]);
    }

    /**
     * Tìm tất cả records theo một column
     * 
     * @param string $column Tên cột
     * @param mixed $value Giá trị cần tìm
     * @param string $orderBy Cột và hướng sắp xếp
     * @return array<int, array<string, mixed>>
     */
    public function findAllBy(string $column, mixed $value, string $orderBy = 'created_at DESC'): array
    {
        $column = $this->sanitizeColumn($column);
        $orderBy = $this->sanitizeOrderBy($orderBy);
        
        $sql = "SELECT * FROM {$this->table} WHERE {$column} = ? ORDER BY {$orderBy}";
        return $this->db->fetchAll($sql, [$value]);
    }

    /**
     * Tạo record mới
     * 
     * @param array<string, mixed> $data Dữ liệu cần tạo
     * @return string|false ID của record mới hoặc false nếu thất bại
     */
    public function create(array $data): string|false
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        
        $result = $this->db->insert($this->table, $data);
        
        // Clear cache khi có thay đổi
        if ($result && $this->cacheEnabled) {
            $this->clearModelCache();
        }
        
        return $result;
    }

    /**
     * Cập nhật record
     * 
     * @param string|int $id Primary key value
     * @param array<string, mixed> $data Dữ liệu cần cập nhật
     * @return int Số rows bị ảnh hưởng
     */
    public function update(string|int $id, array $data): int
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        $result = $this->db->update($this->table, $data, "{$this->primaryKey} = ?", [$id]);
        
        // Clear cache khi có thay đổi
        if ($result > 0 && $this->cacheEnabled) {
            Cache::getInstance()->forget($this->getCacheKey('find', $id));
        }
        
        return $result;
    }

    /**
     * Xóa record
     * 
     * @param string|int $id Primary key value
     * @return int Số rows bị xóa
     */
    public function delete(string|int $id): int
    {
        $result = $this->db->delete($this->table, "{$this->primaryKey} = ?", [$id]);
        
        // Clear cache khi có thay đổi
        if ($result > 0 && $this->cacheEnabled) {
            Cache::getInstance()->forget($this->getCacheKey('find', $id));
        }
        
        return $result;
    }

    /**
     * Đếm số records
     * 
     * @param string $where Điều kiện WHERE
     * @param array<int, mixed> $params Parameters cho prepared statement
     * @return int
     */
    public function count(string $where = '1=1', array $params = []): int
    {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE {$where}";
        return (int) $this->db->fetchColumn($sql, $params);
    }

    /**
     * Kiểm tra record có tồn tại không
     * 
     * @param string|int $id Primary key value
     * @return bool
     */
    public function exists(string|int $id): bool
    {
        $sql = "SELECT 1 FROM {$this->table} WHERE {$this->primaryKey} = ? LIMIT 1";
        return $this->db->fetchColumn($sql, [$id]) !== false;
    }

    /**
     * Generate UUID v4
     * 
     * @return string UUID string
     */
    protected function generateUUID(): string
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }

    /**
     * Sanitize ORDER BY clause để tránh SQL injection
     * 
     * @param string $orderBy
     * @return string
     */
    protected function sanitizeOrderBy(string $orderBy): string
    {
        $allowedOrderBy = [
            'created_at DESC', 'created_at ASC',
            'updated_at DESC', 'updated_at ASC',
            'name ASC', 'name DESC',
            'title ASC', 'title DESC',
            'position ASC', 'position DESC',
            'due_date ASC', 'due_date DESC',
            'priority DESC', 'priority ASC',
            'status ASC', 'status DESC',
            'id ASC', 'id DESC',
        ];
        
        return in_array($orderBy, $allowedOrderBy, true) ? $orderBy : 'created_at DESC';
    }

    /**
     * Sanitize column name để tránh SQL injection
     * 
     * @param string $column
     * @return string
     */
    protected function sanitizeColumn(string $column): string
    {
        // Chỉ cho phép alphanumeric và underscore
        return preg_replace('/[^a-zA-Z0-9_]/', '', $column);
    }

    /**
     * Lấy cache key cho model
     * 
     * @param string $method
     * @param mixed $params
     * @return string
     */
    protected function getCacheKey(string $method, mixed ...$params): string
    {
        return $this->table . ':' . $method . ':' . md5(serialize($params));
    }

    /**
     * Clear tất cả cache của model này
     * 
     * @return void
     */
    protected function clearModelCache(): void
    {
        Cache::getInstance()->forgetByPrefix($this->table . ':');
    }
}
