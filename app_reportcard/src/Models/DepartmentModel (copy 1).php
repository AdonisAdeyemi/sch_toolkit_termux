<?php

namespace ReportCard\Models;

use PDO;
use Core\Models\BaseModel;

class DepartmentModel extends BaseModel
{
    protected string $table = 'report_departments';



//getAllBySchool >> currently unused
    public function getAllBySchool(int $schoolId): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT *
             FROM {$this->table}
             WHERE school_id = ?
             ORDER BY name"
        );

        $stmt->execute([$schoolId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findDepartmentBySchoolId(int $schoolId, int $departmentId): ?array
    {
        $stmt = $this->pdo->prepare(
            "SELECT *
             FROM {$this->table}
             WHERE school_id = ?
             AND id = ?
             LIMIT 1"
        );

        $stmt->execute([
            $schoolId,
            $departmentId
        ]);

        $department = $stmt->fetch(PDO::FETCH_ASSOC);

        return $department ?: null;
    }

    public function exists(int $schoolId, int $departmentId): bool
    {
        $stmt = $this->pdo->prepare(
            "SELECT 1
             FROM {$this->table}
             WHERE school_id = ?
             AND id = ?
             LIMIT 1"
        );

        $stmt->execute([
            $schoolId,
            $departmentId
        ]);

        return (bool) $stmt->fetchColumn();
    }
}
