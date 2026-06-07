<?php
namespace ReportCard\Models;

use PDO;

class PeriodSettingsModel
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function getSchoolPeriodSettings(int $schoolId, int $periodId): ?array
    {
        $sql = "
            SELECT *
            FROM report_school_period_settings
            WHERE school_id = :school_id
              AND period_id = :period_id
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':school_id' => $schoolId,
            ':period_id' => $periodId
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }
}


?>








