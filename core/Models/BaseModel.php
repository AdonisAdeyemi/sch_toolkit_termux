<?php
namespace Core\Models;

use PDO;

abstract class BaseModel
{
    protected PDO $pdo;
    protected string $table;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM {$this->table} WHERE id = ?"
        );

        $stmt->execute([$id]);

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function getAll(): array
    {
        $stmt = $this->pdo->query(
            "SELECT * FROM {$this->table}"
        );

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(array_values($data));

        return (int) $this->pdo->lastInsertId();
    }


/*
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

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute($params);
    }
*/


public function update(
    int $id,
    array $data,
    array $where = []
): bool
{
    $fields = [];

    foreach ($data as $column => $value) {
        $fields[] = "{$column} = :{$column}";
    }

    $sql = "
        UPDATE {$this->table}
        SET " . implode(', ', $fields) . "
        WHERE id = :id
    ";

    $params = $data;
    $params['id'] = $id;

    foreach ($where as $column => $value) {

        $sql .= " AND {$column} = :where_{$column}";

        $params["where_{$column}"] = $value;
    }

    $stmt = $this->pdo->prepare($sql);

    return $stmt->execute($params);
}


/****************/

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare(
            "DELETE FROM {$this->table} WHERE id = ?"
        );

        return $stmt->execute([$id]);
    }

public function softDelete(
    int $id,
    array $where = [],
    string $column = 'is_deleted'
): bool
{
    return $this->update(
        $id,
        [
            $column => 1
        ],
        $where
    );
}

public function restoreDeleted(
    int $id,
    array $where = [],
    string $column = 'is_deleted'
): bool
{
    return $this->update(
        $id,
        [
            $column => 0
        ],
        $where
    );
}
    
    
    protected function fetch(string $sql, array $params = []): ?array
{
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute($params);

    $result = $stmt->fetch(\PDO::FETCH_ASSOC);

    return $result ?: null;
}

protected function fetchAll(string $sql, array $params = []): array
{
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
}

protected function execute(string $sql, array $params = []): bool
{
    $stmt = $this->pdo->prepare($sql);

    return $stmt->execute($params);
}
    
    
    
    
    
}















