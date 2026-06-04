<?php

namespace ReportCard\Models\Report;

use PDO;

class ReportSubjectQuery
{
    public function __construct(private PDO $pdo) {}

    public function getClassSubjects(int $classId): array
    {
        $sql = "
        SELECT 
            cs.id AS class_subject_id,
            cs.class_id,
            cs.subject_order,
            cs.alias_name,
            cs.department_id,

            rs.id AS subject_id,
            rs.name AS subject_name,

            sb.name AS base_subject_name

        FROM report_class_subjects cs
        JOIN report_subjects rs ON rs.id = cs.report_subject_id
        LEFT JOIN subjects sb ON sb.id = rs.base_subject_id

        WHERE cs.class_id = :class_id
          AND (cs.is_deleted = 0 OR cs.is_deleted IS NULL)

        ORDER BY cs.subject_order
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['class_id' => $classId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
