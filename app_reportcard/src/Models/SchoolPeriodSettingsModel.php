<?php

namespace ReportCard\Models;

use Core\Models\BaseModel;
use PDO;

class SchoolPeriodSettingsModel extends BaseModel
{
    protected string $table = 'report_school_period_settings';

    public function getBySchoolAndPeriod(int $schoolId, int $periodId): ?array
    {
        $stmt = $this->pdo->prepare("
            SELECT *
            FROM {$this->table}
            WHERE school_id = :school_id
              AND period_id = :period_id
            LIMIT 1
        ");

        $stmt->execute([
            'school_id' => $schoolId,
            'period_id' => $periodId
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function upsert(int $schoolId, int $periodId, array $data): bool
    {
        $existing = $this->getBySchoolAndPeriod($schoolId, $periodId);

        if ($existing) {

            $stmt = $this->pdo->prepare("
                UPDATE {$this->table}
                SET
                    days_open = :days_open,
                    date_of_vacation = :date_of_vacation,
                    date_of_resumption = :date_of_resumption,
                    term_start_date = :term_start_date,
                    updated_at = NOW()
                WHERE school_id = :school_id
                  AND period_id = :period_id
            ");

        } else {

            $stmt = $this->pdo->prepare("
                INSERT INTO {$this->table}
                (
                    school_id,
                    period_id,
                    days_open,
                    date_of_vacation,
                    date_of_resumption,
                    term_start_date
                )
                VALUES
                (
                    :school_id,
                    :period_id,
                    :days_open,
                    :date_of_vacation,
                    :date_of_resumption,
                    :term_start_date
                )
            ");
        }

        return $stmt->execute([
            'school_id' => $schoolId,
            'period_id' => $periodId,
            'days_open' => $data['days_open'] ?? 124,
            'date_of_vacation' => $data['date_of_vacation'] ?? null,
            'date_of_resumption' => $data['date_of_resumption'] ?? null,
            'term_start_date' => $data['term_start_date'] ?? null,
        ]);
    }
    
    /**
     * Toggle lock state for a period
     */
    public function updateLockStatus(int $schoolId, int $periodId, $lockStatus): bool
    {
        $stmt = $this->pdo->prepare("
            UPDATE {$this->table}
            SET lock_status = :lock_status
            WHERE school_id = :school_id
              AND period_id = :period_id
        ");

        return $stmt->execute([
            ':lock_status' => $lockStatus,
            ':school_id' => $schoolId,
            ':period_id' => $periodId
        ]);
    }

    /**
     * Get lock status
     */
    public function getLockStatus(int $schoolId, int $periodId): int
    {
    
        $stmt = $this->pdo->prepare("
            SELECT lock_status
            FROM {$this->table}
            WHERE school_id = :school_id
              AND period_id = :period_id
            LIMIT 1
        ");

        $stmt->execute([
            ':school_id' => $schoolId,
            ':period_id' => $periodId
        ]);

        return (int) ($stmt->fetchColumn() ?? 0);
    }

    
    /************************/
private function clearActivePeriods(int $schoolId): void
{
    $this->query(
        "
        UPDATE report_school_period_settings
        SET is_active = 0
        WHERE school_id = ?
        ",
        [$schoolId]
    );
}

/****************/

public function setActivePeriod(
    int $schoolId,
    int $periodId
): array
{
    try {

        $this->pdo->beginTransaction();

        $this->clearActivePeriods($schoolId);

        $existing = $this->fetch(
            "
            SELECT id
            FROM report_school_period_settings
            WHERE school_id = ?
              AND period_id = ?
            ",
            [$schoolId, $periodId]
        );

        if ($existing) {

            $this->query(
                "
                UPDATE report_school_period_settings
                SET is_active = 1
                WHERE id = ?
                ",
                [$existing['id']]
            );

        } else {

            $this->query(
                "
                INSERT INTO report_school_period_settings
                (
                    school_id,
                    period_id,
                    is_active
                )
                VALUES
                (
                    ?, ?, 1
                )
                ",
                [$schoolId, $periodId]
            );

        }

        $this->pdo->commit();

        return [
            'success' => true,
            'message' => 'Current academic period updated successfully.'
        ];

    } catch (\Throwable $e) {

        if ($this->pdo->inTransaction()) {
            $this->pdo->rollBack();
        }

        writeLog(
            'debug-period-settings.php',
            $e->getMessage()
        );

        return [
            'success' => false,
            'message' => $e->getMessage()
        ];
    }
}

/***************/

public function getActivePeriod(int $schoolId): ?array
{
    return $this->fetch(
        "
        SELECT
            ps.*,
            p.term,
            p.session_id,
            s.session_name
        FROM report_school_period_settings ps
        INNER JOIN report_academic_periods p
            ON p.id = ps.period_id
        INNER JOIN report_academic_sessions s
            ON s.id = p.session_id
        WHERE ps.school_id = ?
          AND ps.is_active = 1
        LIMIT 1
        ",
        [$schoolId]
    );
}

/**********************/

    
    
    
    
    
    
    
}





















