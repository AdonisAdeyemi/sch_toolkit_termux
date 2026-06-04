<?php

namespace ReportCard\Models\Report;

use PDO;

class ReportScoreQuery
{
    public function __construct(private PDO $pdo) {}

    public function getScores(int $classId, int $periodId): array
    {
        $sql = "
        SELECT 
            r.student_id,
            r.class_subject_id,

            r.ca1_score,
            r.ca2_score,
            r.exam_score,

            (COALESCE(r.ca1_score,0)
            + COALESCE(r.ca2_score,0)
            + COALESCE(r.exam_score,0)) AS total_score

        FROM report_results r
        WHERE r.class_id = :class_id
          AND r.period_id = :period_id
        ";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            'class_id' => $classId,
            'period_id' => $periodId
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
