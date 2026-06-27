<?php

namespace ReportCard\Models;

use Core\Models\BaseModel;
use PDO;

class StudentModel extends BaseModel
{
    protected string $table = 'report_students';


/***********/

public function createStudent(
    int $schoolId,
    string $studentName,
    ?string $admissionNo,
    string $religion,
    string $sex,
    ?string $passportUrl
): int|false
{
    $sql = "
        INSERT INTO report_students
        (
            school_id,
            student_name,
            admission_no,
            religion,
            sex,
            passport_url
        )
        VALUES
        (
            ?,
            ?,
            ?,
            ?,
            ?,
            ?
        )
    ";

    $stmt = $this->pdo->prepare($sql);

    $success = $stmt->execute([
        $schoolId,
        $studentName,
        $admissionNo,
        $religion,
        $sex,
        $passportUrl
    ]);

    if (!$success) {
        return false;
    }

    return (int)$this->pdo->lastInsertId();
}




    /**
     * Get single student
     */
    public function getStudentById(int $studentId): ?array
    {
        return $this->fetch(
            "
            SELECT *
            FROM report_students
            WHERE id = ?
            LIMIT 1
            ",
            [$studentId]
        );
    }



    /**
     * Get all students in a class
     */
    public function getStudentsByClassAndSession(int $classId, int $sessionId): array
    {
        return $this->fetchAll(
            "
            SELECT s.*
            FROM report_students s
            
       INNER JOIN report_student_enrollments se
       ON se.student_id = s.id
       AND se.session_id = ?
            
            WHERE se.class_id = ?
              AND is_deleted = 0
            ORDER BY student_name
            ",
            [$sessionId,$classId]
        );
    }
    
  /**********
  *****/  
    public function getClassIdByStudentAndSession(int $studentId, $sessionId): ?int
{

    if (!$studentId) return null;

    return (int) $this->fetch(
        "
        SELECT se.class_id
        FROM report_students s
        
       INNER JOIN report_student_enrollments se
       ON se.student_id = s.id
       AND se.session_id = ?
        
        WHERE s.id = ?
          AND is_deleted = 0
        LIMIT 1
        ",
        [$sessionId, $studentId]
    );
}

    /**
     * Get student count for class
     */
    public function countStudentsByClassId(int $classId): int
    {
        return (int) $this->fetchColumn(
            "
            SELECT COUNT(*)
            FROM report_students
            WHERE class_id = ?
              AND is_deleted = 0
            ",
            [$classId]
        );
    }

    /**
     * Get student with class details
     */
    public function getStudentWithClass(int $studentId): ?array
    {
        return $this->fetch(
            "
            SELECT
                rs.*,
                rct.label AS class_name,
                rct.code,
                rct.level
            FROM report_students rs
            LEFT JOIN report_classes rc
                ON rc.id = rs.class_id
            LEFT JOIN report_class_templates rct
                ON rct.id = rc.class_template_id
            WHERE rs.id = ?
            LIMIT 1
            ",
            [$studentId]
        );
    }
    
    
 /***********/
 
 
    public function getStudentIdsByClassAndSession(int $classId, int $sessionId): array
    {
        $stmt = $this->pdo->prepare("
            SELECT s.id
            FROM report_students s
            
       INNER JOIN report_student_enrollments se
       ON se.student_id = s.id
       AND se.session_id = ?
            
            WHERE class_id = ?
              AND is_deleted = 0
            ORDER BY student_name ASC
        ");

        $stmt->execute([
        $sessionId,
        $classId
        ]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
    
 /*********/
 
 public function updatePassportUrl(
    int $studentId,
    string $passportUrl
): bool
{
    $sql = "
        UPDATE report_students
        SET passport_url = ?
        WHERE id = ?
    ";

    $stmt = $this->pdo->prepare($sql);

    return $stmt->execute([
        $passportUrl,
        $studentId
    ]);
}
 
 /*****************/
    
    

    /**
     * check duplicate admission number
     */
    public function admissionNumberExists (int $schoolId, string $admissionNo ): ?int
    {
        $res = $this->fetch(
            "
            SELECT COUNT(id) as total
            FROM report_students
            WHERE school_id = ?
            AND admission_no = ?
            LIMIT 1
            ",
            [$schoolId, $admissionNo]
        );
        
  return $res["total"] ;
  
    }

 /**************************/
    
 public function updateStudent(
    int $studentId,
    string $studentName,
    ?string $admissionNo,
    string $religion,
    string $sex
): bool {

    $sql = "
        UPDATE report_students
        SET
            student_name = ?,
            admission_no = ?,
            religion = ?,
            sex = ?
        WHERE id = ?
    ";

    $stmt = $this->pdo->prepare($sql);

    return $stmt->execute([
        $studentName,
        $admissionNo,
        $religion,
        $sex,
        $studentId
    ]);
}


/***********/

public function getRegistryStudents(
    int $schoolId,
    string $search = ''
): array {

    $sql = "
        SELECT
            s.*
        FROM report_students s
        WHERE
            s.school_id = ?
            AND s.is_deleted = 0
    ";

    $params = [$schoolId];

    if (!empty($search)) {

        $sql .= "
            AND (
                s.student_name LIKE ?
                OR s.admission_no LIKE ?
            )
        ";

        $like = "%{$search}%";

        $params[] = $like;
        $params[] = $like;
    }

    $sql .= "
        ORDER BY
            s.student_name
    ";

    return $this->fetchAll(
        $sql,
        $params
    );
}
    
    
 /*****************/
    
}





























