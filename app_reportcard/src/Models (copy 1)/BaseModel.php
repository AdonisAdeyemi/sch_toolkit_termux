<?php

namespace ReportCard\Models;

use Core\Database;

abstract class BaseModel
{

    protected string $table;
    protected  $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function findById(int $id): ?array
    {
        return $this->pdo->fetch(
            "SELECT * FROM {$this->table} WHERE id = ?",
            [$id]
        );
    }

    public function getAll(): array
    {
        return $this->pdo->fetchAll(
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

        $this->pdo->query($sql, array_values($data));

        return $this->pdo->lastInsertId();
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

        return $this->pdo->query($sql, $params);
    }

    public function delete(int $id): bool
    {
        return $this->pdo->query(
            "DELETE FROM {$this->table} WHERE id = ?",
            [$id]
        );
    }
    
    public function softDelete(int $id, string $column = 'is_deleted'): bool
{
    return $this->update($id, [
        $column => 1
    ]);
}


public function restoreDeleted(int $id, string $column = 'is_deleted'): bool
{
    return $this->update($id, [
        $column => 0
    ]);
}


}








