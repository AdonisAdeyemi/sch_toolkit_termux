<?php

namespace ReportCard\Models;

class ResultModel extends BaseModel
{
    protected string $table = 'report_results';

    /**
     * Get result for a student in a subject for a period
     */
    public function getStudentSubjectResult(int $studentId, int $classSubjectId, int $periodId): ?array
    {
        return $this->db->fetch(
            "SELECT *
             FROM {$this->table}
             WHERE student_id = ?
             AND class_subject_id = ?
             AND period_id = ?",
            [$studentId, $classSubjectId, $periodId]
        );
    }

    /**
     * Get all results for a student (full report raw data)
     */
    public function getByStudent(int $studentId, int $periodId): array
    {
        return $this->db->fetchAll(
            "SELECT r.*,
                    cs.alias_name,
                    s.name AS subject_name
             FROM {$this->table} r
             JOIN report_class_subjects cs ON cs.id = r.class_subject_id
             JOIN report_subjects s ON s.id = cs.report_subject_id
             WHERE r.student_id = ?
             AND r.period_id = ?",
            [$studentId, $periodId]
        );
    }

    /**
     * Get all results for a class subject (used for ranking)
     */
    public function getByClassSubject(int $classSubjectId, int $periodId): array
    {
        return $this->db->fetchAll(
            "SELECT *
             FROM {$this->table}
             WHERE class_subject_id = ?
             AND period_id = ?",
            [$classSubjectId, $periodId]
        );
    }

    /**
     * Get all results for a class (for full report generation)
     */
    public function getByClass(int $classId, int $periodId): array
    {
        return $this->db->fetchAll(
            "SELECT r.*,
                    cs.class_id
             FROM {$this->table} r
             JOIN report_class_subjects cs ON cs.id = r.class_subject_id
             WHERE cs.class_id = ?
             AND r.period_id = ?",
            [$classId, $periodId]
        );
    }

    /**
     * Create or update result (UPSERT style logic handled in service later)
     */
    public function saveResult(array $data): int
    {
        return $this->insert($data);
    }

    /**
     * Update scores only (STRICT CONTROL)
     */
    public function updateScores(int $id, array $data): bool
    {
        unset(
            $data['student_id'],
            $data['class_subject_id'],
            $data['period_id']
        );

        return $this->update($id, $data);
    }

    /**
     * Check if result exists (prevents duplicates)
     */
    public function exists(int $studentId, int $classSubjectId, int $periodId): bool
    {
        $row = $this->db->fetch(
            "SELECT id
             FROM {$this->table}
             WHERE student_id = ?
             AND class_subject_id = ?
             AND period_id = ?
             LIMIT 1",
            [$studentId, $classSubjectId, $periodId]
        );

        return $row !== false;
    }
}
