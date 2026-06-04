<?php

namespace ReportCard\Models\Report;

use PDO;

class ReportClassQuery
{
    public function __construct(private PDO $pdo) {}

    public function getClassStudents(int $schoolId, int $classId, int $periodId): array
    {
        $sql = "
        SELECT 
            s.id AS student_id,
            s.student_name,
            s.religion,
            s.passport_url,

            c.id AS class_id,
            c.class_name,

            ap.id AS period_id,
            ap.session,
            ap.term,

            att.days_present,

            sd.department_id,
            rd.name AS department_name,

            t_comm.comment AS teacher_exam_comment,
            p_comm.comment AS principal_exam_comment

        FROM report_students s
        JOIN report_classes c ON c.id = s.class_id
        JOIN report_academic_periods ap ON ap.id = :period_id

        LEFT JOIN report_student_departments sd 
            ON sd.student_id = s.id AND sd.period_id = ap.id

        LEFT JOIN report_departments rd 
            ON rd.id = sd.department_id

        LEFT JOIN report_attendance att 
            ON att.student_id = s.id AND att.period_id = ap.id

        LEFT JOIN report_comments t_comm 
            ON t_comm.student_id = s.id
            AND t_comm.period_id = ap.id
            AND t_comm.comment_type = 'class_teacher'
            AND t_comm.assessment_type = 'exam'

        LEFT JOIN report_comments p_comm 
            ON p_comm.student_id = s.id
            AND p_comm.period_id = ap.id
            AND p_comm.comment_type = 'principal'
            AND p_comm.assessment_type = 'exam'

        WHERE s.school_id = :school_id
          AND c.id = :class_id
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
