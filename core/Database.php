<?php
/**
 * Core Database Class
 * Singleton pattern for database connection management
 */

namespace Core;

use PDO;
use PDOException;
use PDOStatement;
use Exception;

class Database
{
    private static ?Database $instance = null;
    private ?PDO $connection = null;
    private array $config;

    private function __construct()
    {
        $this->config = require BASE_PATH . '/config/database.php';
    }

    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection(): PDO
    {
        if ($this->connection === null) {
            $this->connect();
        }
        return $this->connection;
    }

    private function connect(): void
    {
        try {
            $dsn = sprintf(
                "%s:host=%s;port=%s;dbname=%s;charset=%s",
                $this->config['driver'],
                $this->config['host'],
                $this->config['port'],
                $this->config['database'],
                $this->config['charset']
            );

            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];

            $this->connection = new PDO(
                $dsn,
                $this->config['username'],
                $this->config['password'],
                $options
            );
        } catch (PDOException $e) {
            throw new Exception("Database connection failed: " . $e->getMessage());
        }
    }

    public function fetchAll(string $sql, array $params = []): array
    {
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function fetchOne(string $sql, array $params = []): ?array
    {
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function fetchColumn(string $sql, array $params = [], int $column = 0): mixed
    {
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn($column);
    }

    public function insert(string $table, array $data): string|false
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        
        $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";
        
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->execute(array_values($data));
        
        return $this->getConnection()->lastInsertId();
    }

    public function update(string $table, array $data, string $where, array $whereParams = []): int
    {
        $set = implode(' = ?, ', array_keys($data)) . ' = ?';
        $sql = "UPDATE {$table} SET {$set} WHERE {$where}";
        
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->execute(array_merge(array_values($data), $whereParams));
        
        return $stmt->rowCount();
    }

    public function delete(string $table, string $where, array $params = []): int
    {
        $sql = "DELETE FROM {$table} WHERE {$where}";
        
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->rowCount();
    }

    public function query(string $sql, array $params = []): PDOStatement
    {
        $stmt = $this->getConnection()->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public function beginTransaction(): bool
    {
        return $this->getConnection()->beginTransaction();
    }

    public function commit(): bool
    {
        return $this->getConnection()->commit();
    }

    public function rollback(): bool
    {
        return $this->getConnection()->rollBack();
    }

    private function __clone() {}
    public function __wakeup() { throw new Exception("Cannot unserialize singleton"); }
}
