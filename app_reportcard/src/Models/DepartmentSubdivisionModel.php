<?php

namespace ReportCard\Models;

use Core\Models\BaseModel;

class DepartmentSubdivisionModel extends BaseModel
{
    protected string $table = 'report_department_subdivisions';

    /**
     * Get all active subdivisions for a department.
     */
    public function getSubdivisionsByDepartment(
        int $departmentId
    ): array
    {
        $sql = "
            SELECT
                id,
                name
            FROM {$this->table}
            WHERE department_id = ?
              AND (is_deleted = 0 OR is_deleted IS NULL)
            ORDER BY
                display_order,
                name
        ";

        return $this->fetchAll(
            $sql,
            [$departmentId]
        );
    }

    /**
     * Get subdivision ID by name.
     */
    public function getSubdivisionIdByName(
        string $name
    ): ?int
    {
        $sql = "
            SELECT
                id
            FROM {$this->table}
            WHERE name = ?
              AND (is_deleted = 0 OR is_deleted IS NULL)
            LIMIT 1
        ";

        $row = $this->fetch(
            $sql,
            [$name]
        );

        return $row['id'] ?? null;
    }

    /**
     * Get a subdivision by ID.
     */
    public function getSubdivision(
        int $subdivisionId
    ): ?array
    {
        $sql = "
            SELECT
                *
            FROM {$this->table}
            WHERE id = ?
              AND (is_deleted = 0 OR is_deleted IS NULL)
            LIMIT 1
        ";

        return $this->fetch(
            $sql,
            [$subdivisionId]
        );
    }
    
  /***********/
  
  public function getAllGroupedByDepartment(): array
{
    $rows = $this->fetchAll(
        "
        SELECT
            id,
            name,
            department_id
        FROM {$this->table}
        ORDER BY
            department_id,
            name
        "
    );

    $grouped = [];

    foreach ($rows as $row) {

        $grouped[$row['department_id']][] = [
            'id'   => $row['id'],
            'name' => $row['name']
        ];

    }

    return $grouped;
}
    
    
    
    
}









