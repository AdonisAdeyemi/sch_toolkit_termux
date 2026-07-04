<?php

namespace ReportCard\Models;

use PDO;
use Core\Models\BaseModel;

class DepartmentModel extends BaseModel
{
    protected string $table = 'report_departments';



//XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
    public function getAllByClassLevel($class_level): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT *
             FROM {$this->table}
             WHERE class_level IN ('all',?)
             
             ORDER BY id"
        );

        $stmt->execute([$class_level]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

/*******************/
/******* currently unused  *****/
public function getDepartmentsByClassId(
    int $classId
): array
{
    return $this->fetchAll(
        "
        SELECT
            d.id,
            d.department_name
        FROM report_departments d

        INNER JOIN report_classes c
            ON c.id = ?

        INNER JOIN report_class_templates ct
            ON ct.id = c.class_template_id

        WHERE
            d.class_level = ct.class_level
            OR d.class_level = 'ALL'

        ORDER BY
            d.department_name
        ",
        [
            $classId
        ]
    );
}





/**************/

    public function getAllDepartments(): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT *
             FROM {$this->table}
             ORDER BY id"
        );

        $stmt->execute([]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

/******************/

/*
    public function findDepartmentBySchoolId(int $departmentId): ?array
    {
        $stmt = $this->pdo->prepare(
            "SELECT *
             FROM {$this->table}
             AND id = ?
             LIMIT 1"
        );

        $stmt->execute([
            $departmentId
        ]);

        $department = $stmt->fetch(PDO::FETCH_ASSOC);

        return $department ?: null;
    }

*/

    public function exists(int $departmentId): bool
    {
        $stmt = $this->pdo->prepare(
            "SELECT 1
             FROM {$this->table}
             AND id = ?
             LIMIT 1"
        );

        $stmt->execute([
            $departmentId
        ]);

        return (bool) $stmt->fetchColumn();
    }
}










