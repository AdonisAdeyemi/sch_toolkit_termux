<?php

namespace ReportCard\Models\Report;

use PDO;

class ReportDomainQuery
{
    public function __construct(private PDO $pdo) {}

    public function getDomains(int $schoolId, int $classId, int $periodId): array
    {
        $sql = "
        SELECT
            s.id AS student_id,
            d.id AS domain_id,
            d.domain_name,
            d.domain_type,
            ds.rating

        FROM report_students s
        CROSS JOIN report_domains d

        LEFT JOIN report_domain_scores ds
            ON ds.student_id = s.id
            AND ds.domain_id = d.id
            AND ds.period_id = :period_id

        WHERE s.school_id = :school_id
          AND s.class_id = :class_id

        ORDER BY s.id, d.domain_type, d.sort_order
        ";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            'school_id' => $schoolId,
            'class_id'  => $classId,
            'period_id' => $periodId
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
