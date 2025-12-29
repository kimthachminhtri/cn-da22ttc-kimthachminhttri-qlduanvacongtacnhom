<?php
/**
 * Base Model Class
 */

namespace App\Models;

use Core\Database;

abstract class BaseModel
{
    protected Database $db;
    protected string $table;
    protected string $primaryKey = 'id';

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function all(string $orderBy = 'created_at DESC'): array
    {
        $sql = "SELECT * FROM {$this->table} ORDER BY {$orderBy}";
        return $this->db->fetchAll($sql);
    }

    public function find(string|int $id): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ?";
        return $this->db->fetchOne($sql, [$id]);
    }

    public function findBy(string $column, mixed $value): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$column} = ?";
        return $this->db->fetchOne($sql, [$value]);
    }

    public function findAllBy(string $column, mixed $value, string $orderBy = 'created_at DESC'): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$column} = ? ORDER BY {$orderBy}";
        return $this->db->fetchAll($sql, [$value]);
    }

    public function create(array $data): string|false
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $this->db->insert($this->table, $data);
    }

    public function update(string|int $id, array $data): int
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $this->db->update($this->table, $data, "{$this->primaryKey} = ?", [$id]);
    }

    public function delete(string|int $id): int
    {
        return $this->db->delete($this->table, "{$this->primaryKey} = ?", [$id]);
    }

    public function count(string $where = '1=1', array $params = []): int
    {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE {$where}";
        return (int) $this->db->fetchColumn($sql, $params);
    }

    public function exists(string|int $id): bool
    {
        $sql = "SELECT 1 FROM {$this->table} WHERE {$this->primaryKey} = ? LIMIT 1";
        return $this->db->fetchColumn($sql, [$id]) !== false;
    }

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
}
