<?php

namespace ReportCard\Models;

use Core\Models\BaseModel;
use PDO;

class ClassSubjectModel extends BaseModel
{
    protected string $table = 'report_class_subjects';
    
    public function getByClass(int $schoolId, int $classId): array
{
    $stmt = $this->pdo->prepare(
        "SELECT
            cs.*,
            s.id as report_subject_id,
            s.subject_name
         FROM {$this->table} cs
         INNER JOIN report_subjects s
            ON s.id = cs.report_subject_id
         WHERE cs.school_id = ?
         AND cs.class_id = ?
         AND s.is_deleted = 0
         ORDER BY s.display_order ASC"
    );

    $stmt->execute([$schoolId, $classId]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
    
    
    
    
    
  public function getClassSubjectRowById(int $schoolId, int $classSubjectId): array
{
    $stmt = $this->pdo->prepare(
"SELECT
    cs.*,
    s.id AS report_subject_id,
    s.subject_name,

    d.name AS department_name,
    ds.name AS dept_subdivision_name

FROM {$this->table} cs

INNER JOIN report_subjects s
    ON s.id = cs.report_subject_id

LEFT JOIN report_departments d
    ON d.id = cs.department_id

LEFT JOIN report_department_subdivisions ds
    ON ds.id = cs.department_subdivision_id

WHERE
    cs.school_id = ?
    AND cs.id = ?
    AND s.is_deleted = 0

ORDER BY
    s.display_order ASC

LIMIT 1"
    );

    $stmt->execute([$schoolId, $classSubjectId]);

    return $stmt->fetch(PDO::FETCH_ASSOC);
}
    
    
    
    
    
    
    
    
    public function getSubjectAssignmentsForClass(
    int $schoolId,
    int $classId
): array
{
    $sql = "
        SELECT
            rs.id AS report_subject_id,
           rs.subject_name,
           rs.subject_group,

            cs.id AS class_subject_id,
            cs.department_id,

            CASE
                WHEN cs.id IS NULL THEN 0
                ELSE 1
            END AS is_assigned

        FROM report_subjects rs

        LEFT JOIN report_class_subjects cs
            ON cs.report_subject_id = rs.id
           AND cs.class_id = ?
           AND cs.school_id = ?
           AND cs.is_deleted = 0

        WHERE (rs.school_id = ?
          AND rs.is_deleted = 0)
          OR rs.is_custom = 0

        ORDER BY rs.is_custom, rs.display_order, subject_name
    ";

    $stmt = $this->pdo->prepare($sql);

    $stmt->execute([
        $classId,
        $schoolId,
        $schoolId
    ]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
    
    
    /**************/
public function getAssignmentsMap(
    int $schoolId,
    int $classId
): array
{
    $stmt = $this->pdo->prepare(
        "SELECT
            report_subject_id,
            id,
            department_id
         FROM report_class_subjects
         WHERE school_id = ?
           AND class_id = ?
           AND is_deleted = 0"
    );

    $stmt->execute([
        $schoolId,
        $classId
    ]);

    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $map = [];

    foreach ($rows as $row) {

        $map[(int)$row['report_subject_id']] = [
            'class_subject_id' => (int)$row['id'],
            'department_id' => (int)$row['department_id']
        ];
    }

    return $map;
}

/***********************/


    /**
     * Get class_subject_id from class + subject
     */
    public function getClassSubjectId(
        int $schoolId,
        int $classId,
        int $reportSubjectId
    ): ?int
    {
        $row = $this->fetch(
            "SELECT id
             FROM report_class_subjects
             WHERE class_id = ?
             AND report_subject_id = ?
             AND school_id = ?
             LIMIT 1",  
         [$classId, $reportSubjectId, $schoolId]
        );

        return $row ? (int)$row['id'] : null;
    }



/******************************/

public function updateDepartment(
    int $classSubjectId,
    int $departmentId
): bool
{
    $stmt = $this->pdo->prepare(
        "UPDATE report_class_subjects
         SET department_id = ?
         WHERE id = ?"
    );

    return $stmt->execute([
        $departmentId,
        $classSubjectId
    ]);
}

/***************/

public function deleteById(
    int $classSubjectId
): bool
{
    $stmt = $this->pdo->prepare(
        "DELETE FROM report_class_subjects
         WHERE id = ?"
    );

    return $stmt->execute([
        $classSubjectId
    ]);
}


/*******************/    
    
    

    public function getClassSubjectIds(int $schoolId, int $classId): array
    {
        $rows = $this->fetchAll(
            "SELECT report_subject_id
             FROM {$this->table}
             WHERE school_id = ?
             AND class_id = ?",
            [$schoolId, $classId]
        );

        return array_column($rows, 'report_subject_id');
    }

    public function insertSubject(int $schoolId, int $classId, int $subjectId): bool
    {
        return $this->insert([
            'school_id'  => $schoolId,
            'class_id'   => $classId,
            'report_subject_id' => $subjectId
        ]);
    }

    public function deleteSubject(int $schoolId, int $classId, int $subjectId): bool
    {
        return $this->deleteWhere([
            'school_id'  => $schoolId,
            'class_id'   => $classId,
            'report_subject_id' => $subjectId
        ]);
    }
}






