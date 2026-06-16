<?php
namespace ReportCard\Models;
use Core\Models\BaseModel;


class ResultModel extends BaseModel
{
    protected string $table = 'report_results';

    /**
     * Load students + existing results (SUBJECT GRID)
     */
     /*
    public function getSubjectGrid(
    int    $schoolId ,
        int $classId,
        int $classSubjectId,
        int $periodId
    ): array
    {
        return $this->fetchAll(
            "SELECT
                s.id AS student_id,
                s.student_name,

                r.id AS result_id,
                r.ca1_score,
                r.ca2_score,
                r.exam_score,
                r.total_score,
                r.grade,
                r.remark

            FROM report_students s

            LEFT JOIN report_results r
                ON r.student_id = s.id
                AND r.class_subject_id = ?
                AND r.period_id = ?

            WHERE s.class_id = ?
            AND s.is_deleted = 0           
             AND s.school_id = ?

            ORDER BY s.student_name ASC",
            [
                $classSubjectId,
                $periodId,
                $classId,
                $schoolId 
            ]
        );
    }
    */
    
    
    
    public function getSubjectGrid(
    int $schoolId,
    int $classId,
    int $classSubjectId,
    int $periodId
): array
{
    return $this->fetchAll(
        "SELECT
            s.id AS student_id,
            s.student_name,

            r.id AS result_id,
            r.ca1_score,
            r.ca2_score,
            r.exam_score,
            r.total_score,
            r.grade,
            r.remark

        FROM report_class_subjects cs

        INNER JOIN report_students s
            ON s.class_id = cs.class_id
            AND s.school_id = cs.school_id
            AND s.is_deleted = 0

        LEFT JOIN report_results r
            ON r.student_id = s.id
            AND r.class_subject_id = cs.id
            AND r.period_id = ?

        WHERE cs.id = ?
        AND cs.class_id = ?
        AND cs.school_id = ?",

        [
            $periodId,
            $classSubjectId,
            $classId,
            $schoolId
        ]
    );
}
    
    
    

    /**
     * Insert / update result row
     */
    public function upsert(array $data): bool
    {
        return (bool)$this->execute(
            "INSERT INTO report_results
            (
                student_id,
                class_subject_id,
                period_id,
                ca1_score,
                ca2_score,
                exam_score,
                total_score,
                grade,
                remark
            )
            VALUES
            (
                ?, ?, ?, ?, ?, ?, ?, ?, ?
            )
            ON DUPLICATE KEY UPDATE
                ca1_score = VALUES(ca1_score),
                ca2_score = VALUES(ca2_score),
                exam_score = VALUES(exam_score),
                total_score = VALUES(total_score),
                grade = VALUES(grade),
                remark = VALUES(remark)",
            [
                $data['student_id'],
                $data['class_subject_id'],
                $data['period_id'],
                $data['ca1_score'],
                $data['ca2_score'],
                $data['exam_score'],
                $data['total_score'],
                $data['grade'],
                $data['remark']
            ]
        );
    }
}






