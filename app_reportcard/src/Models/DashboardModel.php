<?php

namespace ReportCard\Models;

use PDO;
use Core\Models\BaseModel;
use ReportCard\Models\SchoolPeriodSettingsModel;

/*

use ReportCard\Models\SchoolPeriodSettingsModel;
use ReportCard\Models\AcademicPeriodModel;
use Core\Controllers\BaseController;
use PDO;

class SchoolPeriodSettingsController extends BaseController
{
    private SchoolPeriodSettingsModel $schoolPeriodSettingsModel;
*/

class DashboardModel extends BaseModel
{
    private SchoolPeriodSettingsModel $schoolPeriodSettingsModel;
    
 public function __construct($pdo)
{
    parent::__construct($pdo);
    
    $this->schoolPeriodSettingsModel = new SchoolPeriodSettingsModel($pdo);

}
    
 /***********
 MESSAGE FROM PASTME
Hi
many db calls here are scope creeping 
refactor into logical models : keep it clean hon
 **************/
    
   

 /*************************/
    
    public function getDashboardStats(int $schoolId): array
{
    $activePeriod = $this->schoolPeriodSettingsModel
        ->getActivePeriod($schoolId);

    $periodId = $activePeriod['period_id'] ?? 0;
    $sessionId = $activePeriod['session_id'] ?? 0;

    return [
        'totalStudents' => $this->getTotalStudents($schoolId),
        'totalClasses' => $this->getTotalClasses($schoolId),
        'totalSubjects' => $this->getTotalSubjects($schoolId),
        'activePeriod' => $activePeriod,
        'studentsAwaitingEnrollment' =>
            $this->getStudentsCountAwaitingEnrollment(
                $schoolId,
                $sessionId
            ),
        'studentsWithIncompleteResults' =>
            $this->getStudentsWithIncompleteResults(
                $schoolId,
                $periodId
            ),
    ];
}
    
   /**********************/
   
    private function getTotalStudents(int $schoolId): int
{
    $row = $this->fetch(
        "
        SELECT COUNT(*) AS total
        FROM report_students
        WHERE school_id = ?
          AND is_deleted = 0
        ",
        [$schoolId]
    );

    return (int) ($row['total'] ?? 0);
}
   
   
 
 /*************************/
    
    private function getTotalClasses(int $schoolId): int
{
    $row = $this->fetch(
        "
        SELECT COUNT(*) AS total
        FROM report_classes
        WHERE school_id = ?
          AND is_deleted = 0
          AND is_active = 1
        ",
        [$schoolId]
    );

    return (int) ($row['total'] ?? 0);
}
    
    
 /*************************/
    
    private function getTotalSubjects(int $schoolId): int
{
    $row = $this->fetch(
        "
        SELECT COUNT(*) AS total
        FROM report_subjects
        WHERE school_id = ?
          AND is_deleted = 0
        ",
        [$schoolId]
    );

    return (int) ($row['total'] ?? 0);
}
    
    
 /*************************/
    
    
    private function getStudentsCountAwaitingEnrollment(
    int $schoolId,
    int $sessionId
): int
{
    $row = $this->fetch(
        "
        SELECT COUNT(*) AS total
        FROM report_students s
        WHERE s.school_id = ?
          AND s.is_deleted = 0
          AND NOT EXISTS (
              SELECT 1
              FROM report_student_enrollments e
              WHERE e.student_id = s.id
                AND e.session_id = ?
          )
        ",
        [$schoolId, $sessionId]
    );

    return (int) ($row['total'] ?? 0);
}
    
 /*************************/
    
    private function getStudentsWithIncompleteResults(
    int $schoolId,
    int $periodId
): int
{
    $row = $this->fetch(
        "
        SELECT COUNT(DISTINCT r.student_id) AS total
        FROM report_results r
        INNER JOIN report_students s
            ON s.id = r.student_id
        WHERE s.school_id = ?
          AND r.period_id = ?
          AND r.grade = '-'
        ",
        [$schoolId, $periodId]
    );

    return (int) ($row['total'] ?? 0);
}
    
 
    
    
    
 /*************************/
    
    
    
    
 /*************************/
    
    
    
    
 /*************************/
    
    
    
    
 /*************************/
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
}












