<?php

namespace ReportCard\Models;

use Core\Models\BaseModel;

class EnrollmentModel extends BaseModel
{

public function getEnrollment(
    int $studentId,
    int $sessionId
): ?array {

    return $this->fetch(
        "
        SELECT *
        FROM report_student_enrollments
        WHERE student_id = ?
          AND session_id = ?
        ",
        [$studentId, $sessionId]
    );
}

/**********************************/


public function getStudentClassInSession(
    int $studentId,
    int $sessionId
): ?int {

    $sql = "
        SELECT class_id
        FROM report_student_enrollments
        WHERE student_id = ?
          AND session_id = ?
        LIMIT 1
    ";

    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([$studentId, $sessionId]);

    $classId = $stmt->fetchColumn();

    return $classId ? (int)$classId : null;
}


/**********************************/

public function getStudentsInClass(
    int $schoolId,
    int $sessionId,
    int $classId
): array {

    return $this->fetchAll(
        "
        SELECT
            s.*
        FROM report_student_enrollments e
        JOIN report_students s
            ON s.id = e.student_id
        WHERE e.school_id = ?
          AND e.session_id = ?
          AND e.class_id = ?
        ORDER BY s.student_name
        ",
        [
            $schoolId,
            $sessionId,
            $classId
        ]
    );
}


/**********************************/

public function getStudentsInSession(
    int $schoolId,
    int $sessionId
): array {

    return $this->fetchAll(
        "
        SELECT
            s.*
        FROM report_student_enrollments e
        JOIN report_students s
            ON s.id = e.student_id
        WHERE e.school_id = ?
          AND e.session_id = ?
        ORDER BY s.student_name
        ",
        [
            $schoolId,
            $sessionId
        ]
    );
}


/**********************************/


public function enrollStudent(
    int $schoolId,
    int $studentId,
    int $sessionId,
    int $classId
): bool {

    $sql = "
        INSERT INTO report_student_enrollments
        (
            school_id,
            student_id,
            session_id,
            class_id
        )
        VALUES
        (
            ?,
            ?,
            ?,
            ?
        )
        ON DUPLICATE KEY UPDATE
            class_id = VALUES(class_id)
    ";

    $stmt = $this->pdo->prepare($sql);

    return $stmt->execute([
        $schoolId,
        $studentId,
        $sessionId,
        $classId
    ]);
}

/**********************************/


public function updateEnrollmentClass(
    int $studentId,
    int $sessionId,
    int $classId
): bool {

    $sql = "
        UPDATE report_student_enrollments
        SET class_id = ?
        WHERE student_id = ?
          AND session_id = ?
    ";

    $stmt = $this->pdo->prepare($sql);

    return $stmt->execute([
        $classId,
        $studentId,
        $sessionId
    ]);
}

/**********************************/

public function removeEnrollment(
    int $studentId,
    int $sessionId
): bool {

    $sql = "
        DELETE
        FROM report_student_enrollments
        WHERE student_id = ?
          AND session_id = ?
    ";

    $stmt = $this->pdo->prepare($sql);

    return $stmt->execute([
        $studentId,
        $sessionId
    ]);
}


/**********************************/









}
