<?php

namespace ReportCard\Models;

use Core\Models\BaseModel;
use PDO;

class ReportRemarksModel extends BaseModel
{
    protected string $table = 'report_students';

    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo);
    }

    /*
    |-----------------------------------------
    | CLASSES
    |-----------------------------------------
    */
    /*
    public function getClasses(int $schoolId): array
    {
        $stmt = $this->pdo->prepare("
            SELECT c.id, ct.label
            FROM report_classes c
            JOIN report_class_templates ct ON ct.id = c.class_template_id
            WHERE c.school_id = ?
              AND c.is_deleted = 0
              AND c.is_active = 1
            ORDER BY ct.sort_order ASC
        ");

        $stmt->execute([$schoolId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
*/


    /*
    |-----------------------------------------
    | STUDENTS BY CLASS
    |-----------------------------------------
    */
    public function getStudentsByClass(int $classId): array
    {
        $stmt = $this->pdo->prepare("
            SELECT id, student_name, admission_no
            FROM report_students
            WHERE class_id = ?
              AND is_deleted = 0
            ORDER BY student_name ASC
        ");

        $stmt->execute([$classId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /*
    |-----------------------------------------
    | RESULTS (GROUPED BY STUDENT)
    |-----------------------------------------
    */
    public function getStudentResults(int $studentId, int $periodId): array
    {
        $stmt = $this->pdo->prepare("
            SELECT
                s.subject_name,
                rr.ca1_score,
                rr.ca2_score,
                rr.exam_score,
                rr.total_score,
                rr.grade,
                rr.remark
            FROM report_results rr
            JOIN report_class_subjects cs ON cs.id = rr.class_subject_id
            JOIN report_subjects s ON s.id = cs.report_subject_id
            WHERE rr.student_id = ?
              AND rr.period_id = ?
            ORDER BY cs.subject_rc_order ASC
        ");

        $stmt->execute([$studentId, $periodId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /*
    |-----------------------------------------
    | ATTENDANCE
    |-----------------------------------------
    */
    public function getAttendance(int $studentId, int $periodId): ?array
    {
        $stmt = $this->pdo->prepare("
            SELECT days_present
            FROM report_attendance
            WHERE student_id = ?
              AND period_id = ?
            LIMIT 1
        ");

        $stmt->execute([$studentId, $periodId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function saveAttendance(int $studentId, int $periodId, int $daysPresent): bool
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO report_attendance (student_id, period_id, days_present)
            VALUES (?, ?, ?)
            ON DUPLICATE KEY UPDATE days_present = VALUES(days_present)
        ");

        return $stmt->execute([$studentId, $periodId, $daysPresent]);
    }

    /*
    |-----------------------------------------
    | COMMENTS
    |-----------------------------------------
    */
    public function getComments(int $studentId, int $periodId, $assessmentType = 'exam' ): array
    {
        $stmt = $this->pdo->prepare("
            SELECT comment_type, comment
            FROM report_comments
            WHERE student_id = ?
              AND period_id = ?
              AND assessment_type = ?
        ");

        $stmt->execute([$studentId, $periodId, $assessmentType]);
        return $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    }

    public function saveComment(
        int $studentId,
        int $periodId,
        string $commentType,
        string $comment,
       string $assessmentType = 'exam'
    ): bool {
        $stmt = $this->pdo->prepare("
            INSERT INTO report_comments (student_id, period_id, comment_type, assessment_type, comment)
            VALUES (?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE comment = VALUES(comment)
        ");

        return $stmt->execute([$studentId, $periodId, $commentType, $assessmentType, $comment]);
    }

    /*
    |-----------------------------------------
    | DOMAINS
    |-----------------------------------------
    */
    public function getDomains(): array
    {
        $stmt = $this->pdo->prepare("
            SELECT *
            FROM report_domains
            ORDER BY domain_type, sort_order
        ");

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getDomainScores(int $studentId, int $periodId): array
    {
        $stmt = $this->pdo->prepare("
            SELECT domain_id, rating
            FROM report_domain_scores
            WHERE student_id = ?
              AND period_id = ?
        ");

        $stmt->execute([$studentId, $periodId]);
        return $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    }

    public function saveDomainScore(
        int $studentId,
        int $periodId,
        int $domainId,
        int $rating
    ): bool {
        $stmt = $this->pdo->prepare("
            INSERT INTO report_domain_scores (student_id, period_id, domain_id, rating)
            VALUES (?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE rating = VALUES(rating)
        ");

        return $stmt->execute([$studentId, $periodId, $domainId, $rating]);
    }


}









