<?php

class ResultModel extends BaseModel
{
    protected string $table = 'results';

    public function getStudentResults(
        int $studentId,
        int $periodId
    ): array
    {
        return $this->db->fetchAll(
            "SELECT *
             FROM results
             WHERE student_id = ?
             AND period_id = ?",
            [$studentId, $periodId]
        );
    }

    public function getClassResults(
        int $classId,
        int $periodId
    ): array
    {
        return $this->db->fetchAll(
            "SELECT r.*
             FROM results r
             JOIN students s
                ON s.id = r.student_id
             WHERE s.class_id = ?
             AND r.period_id = ?",
            [$classId, $periodId]
        );
    }

    public function getSubjectResults(
        int $subjectId,
        int $periodId
    ): array
    {
        return $this->db->fetchAll(
            "SELECT *
             FROM results
             WHERE subject_id = ?
             AND period_id = ?",
            [$subjectId, $periodId]
        );
    }

    public function findResult(
        int $studentId,
        int $subjectId,
        int $periodId
    ): ?array
    {
        return $this->db->fetch(
            "SELECT *
             FROM results
             WHERE student_id = ?
             AND subject_id = ?
             AND period_id = ?",
            [$studentId, $subjectId, $periodId]
        );
    }
}
