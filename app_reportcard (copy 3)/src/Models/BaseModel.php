<?php

namespace ReportCard\Models;

use Core\Database;

abstract class BaseModel
{
    protected Database $db;
    protected string $table;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function findById(int $id): ?array
    {
        return $this->db->fetch(
            "SELECT * FROM {$this->table} WHERE id = ?",
            [$id]
        );
    }

    public function getAll(): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM {$this->table}"
        );
    }

    public function insert(array $data): int
    {
        $columns = array_keys($data);

        $sql = sprintf(
            "INSERT INTO %s (%s) VALUES (%s)",
            $this->table,
            implode(',', $columns),
            implode(',', array_fill(0, count($columns), '?'))
        );

        $this->db->query($sql, array_values($data));

        return $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $set = [];

        foreach ($data as $column => $value) {
            $set[] = "{$column} = ?";
        }

        $sql = sprintf(
            "UPDATE %s SET %s WHERE id = ?",
            $this->table,
            implode(',', $set)
        );

        $params = array_values($data);
        $params[] = $id;

        return $this->db->query($sql, $params);
    }

    public function delete(int $id): bool
    {
        return $this->db->query(
            "DELETE FROM {$this->table} WHERE id = ?",
            [$id]
        );
    }
}








