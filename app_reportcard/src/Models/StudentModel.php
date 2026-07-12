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
    string $sex,
    ?string $passportUrl,
     ?string $dateOfBirth
): int|false
{
    $sql = "
        INSERT INTO report_students
        (
            school_id,
            student_name,
            admission_no,
            sex,
            passport_url,
            date_of_birth
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
        $sex,
        $passportUrl,
        $dateOfBirth
    ]);

    if (!$success) {
        return false;
    }

    return (int)$this->pdo->lastInsertId();
}




    /**
     * Get single student
     */
     /*
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
  */
  
  public function getStudentById(
    int $schoolId,
    int $studentId
): ?array
{
    return $this->fetch(

        "SELECT
            id,
            student_name,
            admission_no,
            sex,
            passport_url,
            date_of_birth
         FROM report_students
         WHERE
            school_id = ?
            AND id = ?
            AND is_deleted = 0",

        [
            $schoolId,
            $studentId
        ]

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
    public function getClassIdByStudentAndSession(int $studentId, int $sessionId): ?int
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
 public function admissionNumberExists(
    int $schoolId,
    string $admissionNo,
    ?int $studentIdToIgnore = null
): ?int
    {
    
$sql = "
            SELECT COUNT(id) as total
            FROM report_students
            WHERE school_id = ?
            AND admission_no = ? ";

$params =   [$schoolId, $admissionNo] ;

if($studentIdToIgnore)
{
$sql .= " AND id <> ?";
$params[] = $studentIdToIgnore;
}

$sql .= " LIMIT 1 ";
            
            
        $res = $this->fetch(
           $sql , $params

        );

 
        
  return $res["total"] ;
  
    }

 /**************************/
    
public function updateStudent(
    int $schoolId,
    int $studentId,
    string $studentName,
    ?string $admissionNo,
    string $sex,
     ?string $dateOfBirth
): bool
{
    $sql = "
        UPDATE report_students
        SET
            student_name = ?,
            admission_no = ?,
            sex = ?,
            date_of_birth = ?
            
        WHERE
            school_id = ?
            AND id = ?
            AND is_deleted = 0
    ";

    $stmt = $this->pdo->prepare($sql);

    return $stmt->execute([
        $studentName,
        $admissionNo,
        $sex,
        $dateOfBirth,
        $schoolId,
        $studentId
    ]);
}

/***********/
/*
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
    */
    
 /*****************/
 
 public function getRegistryStudents(
    int $schoolId,
    string $search = '',
    string $sex = '',
    string $passport = '',
    string $dob = '',
    bool $showDeleted = false
): array
{

$showDeleted = $showDeleted ? 1 : 0 ;

    $sql = "
        SELECT *
        FROM report_students
        WHERE
            school_id = ?
            AND is_deleted = ? 
    ";

    $params = [$schoolId, $showDeleted];

    /*
    |--------------------------------------------------------------------------
    | Search
    |--------------------------------------------------------------------------
    */

    if ($search !== '') {

        $sql .= "
            AND (
                student_name LIKE ?
                OR admission_no LIKE ? 
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

        $sql .= " AND sex = ?";

        $params[] = $sex;
    }

    /*
    |--------------------------------------------------------------------------
    | Passport
    |--------------------------------------------------------------------------
    */

    if ($passport === '1') {

        $sql .= "
            AND passport_url IS NOT NULL
            AND passport_url <> ''
        ";

    } elseif ($passport === '0') {

        $sql .= "
            AND (
                passport_url IS NULL
                OR passport_url = ''
            )
        ";
    }

    /*
    |--------------------------------------------------------------------------
    | Date of Birth
    |--------------------------------------------------------------------------
    */

    if ($dob === '1') {

        $sql .= "
            AND date_of_birth IS NOT NULL
        ";

    } elseif ($dob === '0') {

        $sql .= "
            AND date_of_birth IS NULL
        ";
    }
    /*
    |--------------------------------------------------------------------------
    | Sort
    |--------------------------------------------------------------------------
    */

    $sql .= "
      ORDER BY
    date_of_birth ASC,
    student_name ASC ";

    $stmt = $this->pdo->prepare($sql);

    $stmt->execute($params);

    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
}
 
 
 /********************/
 
 public function getAvailableStudentsForEnrollment(
    int $schoolId,
    int $sessionId,
    int $classId,
    string $search = '',
    string $sex = ''
): array
{

}
 
 
 
 
 
 /************/
 
public function findByAdmissionNo(
    int $schoolId,
    string $admissionNo
): ?array
{
    return $this->fetch(
        "
        SELECT *
        FROM {$this->table}
        WHERE school_id = ?
        AND admission_no = ?
        LIMIT 1
        ",
        [
            $schoolId,
            $admissionNo
        ]
    );
}
 /********************/
 
 
 
 /****************/
    
}

































