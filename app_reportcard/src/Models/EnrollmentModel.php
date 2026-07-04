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


public function getEnrollments(
    int $schoolId,
    int $sessionId,
    int $classId = 0,
    int $departmentId = 0,
    string $search = ''
): array {

    $sql = "
        SELECT
            e.id,
            e.student_id,
            e.session_id,
            e.class_id,

            s.student_name,
            s.sex,
            s.passport_url,
            s.admission_no,

            sd.department_id,

            d.name AS department_name,

            ct.label AS class_name

        FROM report_student_enrollments e

        INNER JOIN report_students s
            ON s.id = e.student_id

        LEFT JOIN report_student_departments sd
            ON sd.student_id = s.id
           AND sd.session_id = ?

        LEFT JOIN report_departments d
            ON d.id = sd.department_id

        INNER JOIN report_classes c
            ON c.id = e.class_id

        INNER JOIN report_class_templates ct
            ON ct.id = c.class_template_id

        WHERE
            e.school_id = ?
        AND e.session_id = ?
    ";

    $params = [
        $sessionId,
        $schoolId,
        $sessionId
    ];

    // Optional class filter
    if ($classId > 0) {

        $sql .= "
            AND e.class_id = ?
        ";

        $params[] = $classId;
    }

    // Optional department filter
    if ($departmentId > 0) {

        $sql .= "
            AND sd.department_id = ?
        ";

        $params[] = $departmentId;
    }

    // Optional search
    if (!empty($search)) {

        $sql .= "
            AND (
                s.student_name LIKE ?
                OR s.admission_no LIKE ?
            )
        ";

        $params[] = "%{$search}%";
        $params[] = "%{$search}%";
    }

    $sql .= "
        ORDER BY
            ct.label,
            s.student_name
    ";

    return $this->fetchAll($sql, $params);
}


/*****************/

/*
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
*/

/**********************************/

/*
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
*/

/**********************************/

public function enrollStudent(
    int $schoolId,
    int $studentId,
    int $sessionId,
    int $classId,
    int $departmentId
): array {

    try {

writeLog ("debug-enrlmntMdl.php", "\n ================== \n checkpoint 111");

        // Enrollment
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

        $stmt->execute([
            $schoolId,
            $studentId,
            $sessionId,
            $classId
        ]);
writeLog ("debug-enrlmntMdl.php", "\n checkpoint 222");
        // Department
        $sql = "
            INSERT INTO report_student_departments
            (
                student_id,
                department_id,
                class_id,
                session_id
            )
            VALUES
            (
                ?,
                ?,
                ?,
                ?
            )
            ON DUPLICATE KEY UPDATE
                department_id = VALUES(department_id),
                class_id      = VALUES(class_id)
        ";
writeLog ("debug-enrlmntMdl.php", "\n checkpoint 333");
        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            $studentId,
            $departmentId,
            $classId,
            $sessionId
        ]);
writeLog ("debug-enrlmntMdl.php", "\n checkpoint 444");

return [
        'success' => true,
        'message' => 'Operation completed successfully.'
    ];
    
    } catch (\Throwable $e) {

writeLog ("debug-enrlmntMdl.php", "\n checkpoint 555 {$e->getMessage()}");

return [
        'success' => false,
        'message' => $e->getMessage()
    ];

    }
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

public function bulkPromote(
    int $schoolId,
    int $fromSessionId,
    int $toSessionId,
    int $fromClassId,
    int $toClassId
): bool {

    $sql = "
        INSERT INTO report_student_enrollments
        (
            school_id,
            student_id,
            session_id,
            class_id
        )

        SELECT
            school_id,
            student_id,
            ?,
            ?
        FROM report_student_enrollments
        WHERE school_id = ?
          AND session_id = ?
          AND class_id = ?

        ON DUPLICATE KEY UPDATE
            class_id = VALUES(class_id)
    ";

    $stmt = $this->pdo->prepare($sql);

    return $stmt->execute([
        $toSessionId,
        $toClassId,
        $schoolId,
        $fromSessionId,
        $fromClassId
    ]);
}

/***************/
/*
public function getStudentsNotEnrolledInSession(
    int $schoolId,
    int $sessionId
): array {

    return $this->fetchAll(
        "
        SELECT
            s.*,
            e.class_id AS last_class_id,
            c.class_name AS last_class_name,
            sess.session_name AS last_session_name

        FROM report_students s

        LEFT JOIN report_student_enrollments e
            ON e.id = (
                SELECT e2.id
                FROM report_student_enrollments e2
                WHERE e2.student_id = s.id
                  AND e2.school_id = s.school_id
                  AND e2.session_id <> ?
                ORDER BY e2.session_id DESC
                LIMIT 1
            )

        LEFT JOIN report_classes c
            ON c.id = e.class_id
            
       LEFT JOIN report_class_templates ct 
ON ct.id = c.class_template_id  

        LEFT JOIN report_academic_sessions sess
            ON sess.id = e.session_id

        WHERE s.school_id = ?

          AND NOT EXISTS (
                SELECT 1
                FROM report_student_enrollments x
                WHERE x.student_id = s.id
                  AND x.school_id = s.school_id
                  AND x.session_id = ?
          )

        ORDER BY
            e.session_id DESC,
            ct.sort_order,
            s.student_name
        ",
        [
            $sessionId,
            $schoolId,
            $sessionId
        ]
    );
}
*/

public function getStudentsNotEnrolledInSession(
    int $schoolId,
    int $sessionId,
    string $search = '',
    string $sex = '',
    int $previousClassId = 0
): array
{
    $sql = "
        SELECT
            s.*,
            e.class_id AS last_class_id,
            ct.label AS last_class_name,
            sess.session_name AS last_session_name

        FROM report_students s

        LEFT JOIN report_student_enrollments e
            ON e.id = (
                SELECT e2.id
                FROM report_student_enrollments e2
                WHERE e2.student_id = s.id
                  AND e2.school_id = s.school_id
                  AND e2.session_id <> ?
                ORDER BY e2.session_id DESC
                LIMIT 1
            )

        LEFT JOIN report_classes c
            ON c.id = e.class_id

        LEFT JOIN report_class_templates ct
            ON ct.id = c.class_template_id

        LEFT JOIN report_academic_sessions sess
            ON sess.id = e.session_id

        WHERE
            s.school_id = ?

            AND NOT EXISTS (
                SELECT 1
                FROM report_student_enrollments x
                WHERE x.student_id = s.id
                  AND x.school_id = s.school_id
                  AND x.session_id = ?
            )
    ";

    $params = [
        $sessionId,
        $schoolId,
        $sessionId
    ];

    /*
    |--------------------------------------------------------------------------
    | Search
    |--------------------------------------------------------------------------
    */

    if ($search !== '') {

        $sql .= "
            AND (
                s.student_name LIKE ?
                OR s.admission_no LIKE ?
            )
        ";

        $params[] = "%{$search}%";
        $params[] = "%{$search}%";
    }

    /*
     |--------------------------------------------------------------------------
    | Sex
    |--------------------------------------------------------------------------
    */

    if ($sex !== '') {

        $sql .= " AND s.sex = ?";

        $params[] = $sex;
    }
    
    /*
|--------------------------------------------------------------------------
    | Previous class
    |--------------------------------------------------------------------------
    */

    if ($previousClassId !== 0) {
    

        if ($previousClassId == -1) {
            //for students never entolled
        $sql .= " AND e.class_id IS NULL ";       
        }
        else
        {
        $sql .= " AND e.class_id = ?";

        $params[] = $previousClassId;
        }
    }


/***********/

    $sql .= "
        ORDER BY
            e.session_id DESC,
            ct.sort_order,
            s.student_name
    ";

    return $this->fetchAll(
        $sql,
        $params
    );
}

/**********************/

public function isStudentEnrolled(
    int $sessionId,
    int $studentId,
    int $schoolId
): bool
{

    $sql = "
        SELECT 1
        FROM report_student_enrollments
        WHERE
            session_id = ?
            AND student_id = ?
            AND school_id = ?
        LIMIT 1
    ";

    $stmt = $this->pdo->prepare($sql);

    $stmt->execute([
         
        $sessionId,
        $studentId,
        $schoolId
        
    ]);

    return (bool) $stmt->fetchColumn();
}


/**********/
public function enrollmentHasResults(
    int $sessionId,
    int $studentId,
    int $schoolId
): bool
{
    $sql = "
        SELECT 1
        FROM report_results r
        
        JOIN report_students s
        ON s.id = r.student_id
        
        JOIN report_academic_periods  ap
        ON ap.id = r.period_id

        JOIN report_academic_sessions ras
        ON ras.id = ap.session_id        
        
        WHERE
            ap.session_id = ?
            AND r.student_id = ?
            AND s.school_id = ?
        LIMIT 1
    ";

    $stmt = $this->pdo->prepare($sql);

    $stmt->execute([
        $sessionId,
        $studentId,
        $schoolId
    ]);

    return (bool) $stmt->fetchColumn();
}

/************/

public function removeFromClass(
    int $sessionId,
    int $studentId,
    int $schoolId
): bool
{
    $sql = "
        DELETE
        FROM report_student_enrollments
        WHERE
            session_id = ?
            AND student_id = ?
            AND school_id = ?
    ";

    $stmt = $this->pdo->prepare($sql);

    return $stmt->execute([
        $sessionId,
        $studentId,
        $schoolId
    ]);
}

/**********************/





}













