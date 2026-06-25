<?php

namespace ReportCard\Controllers;

use ReportCard\Models\ReportRemarksModel;
use ReportCard\Models\SchoolPeriodSettingsModel;
use ReportCard\Models\AcademicPeriodModel;
use ReportCard\Models\ClassModel;

use Core\Controllers\BaseController;
use PDO;

class ReportRemarksController extends BaseController
{
    private ReportRemarksModel $reportRemarksModel;
    private SchoolPeriodSettingsModel $schoolPeriodSettingsModel; 
    private AcademicPeriodModel $academicPeriodModel;
    private ClassModel $classModel;
    
    private PDO $pdo;
    private ?string $appName;

    public function __construct(PDO $pdo)
    {
    
        $this->reportRemarksModel = new ReportRemarksModel($pdo);
      $this->schoolPeriodSettingsModel = new SchoolPeriodSettingsModel($pdo);
      $this->academicPeriodModel = new AcademicPeriodModel($pdo);
      $this->classModel = new ClassModel($pdo);
       
        $this->pdo = $pdo;
        $this->appName = $_SESSION['appName'] ?? null;
    }

    /*
    |-----------------------------------------
    | MAIN PAGE
    |-----------------------------------------
    */
    public function index(): void
    {
        $schoolId = $_SESSION['school_id'];

        $classId  = isset($_GET['class_id']) ? (int) $_GET['class_id'] : 0;
        $periodId = isset($_GET['period_id']) ? (int) $_GET['period_id'] : 0;

        $studentIndex = isset($_GET['index']) ? (int) $_GET['index'] : 0;

        // Load dropdowns
       // $classes = $this->reportRemarksModel->getClasses($schoolId);
$classes = $this->classModel->getClassesBySchool($schoolId);
$periods = $this->academicPeriodModel->getPeriodsList();
        
        
           //2b. get period (session/term) settings :: for dys open = max attendance,
$periodSettings = $this->schoolPeriodSettingsModel ->getBySchoolAndPeriod($schoolId, $periodId);
$max_attendance = $periodSettings['days_open'] ?? 0;
     
        

        $students = [];
        $currentStudent = null;

        $results = [];
        $attendance = null;
        $comments = [];
        $domains = [];
        $domainScores = [];

        $studentIds = [];
        //for navigation (pre-set)
 $isFirstStudent = false;
$isLastStudent  = false;

        if ($classId && $periodId) {

            // Load students in class
            $students = $this->reportRemarksModel->getStudentsByClass($classId);
            $studentIds = $this->reportRemarksModel->getStudentIdsByClass($classId);
  $totalStudents = count($studentIds) ;

            // Clamp index
            if ($studentIndex < 0) {
                $studentIndex = 0;
            }

            if ($studentIndex >= $totalStudents) {
                $studentIndex = 0;
            }
            
//for navigation            
 $isFirstStudent = ($studentIndex <= 0) ;
$isLastStudent  = ($studentIndex >= ($totalStudents - 1)) ;

            $currentStudentId = $studentIds[$studentIndex] ?? null;

            if ($currentStudentId) {

                // Current student info
                foreach ($students as $s) {
                    if ($s['id'] == $currentStudentId) {
                        $currentStudent = $s;
                        break;
                    }
                }

                // Load all data for student
   //REFACTOR : reportRemarksModel is creeping on scope (other model's scope)
   
                $results = $this->reportRemarksModel->getStudentResults($currentStudentId, $periodId);
                $attendance = $this->reportRemarksModel->getAttendance($currentStudentId, $periodId);
                $comments = $this->reportRemarksModel->getComments($currentStudentId, $periodId);
                $domains = $this->reportRemarksModel->getDomains($schoolId);
                $domainScores = $this->reportRemarksModel->getDomainScores($currentStudentId, $periodId);
            }
            
            
        }

        // Navigation
        $totalStudents = count($studentIds);

        $prevIndex = $studentIndex > 0 ? $studentIndex - 1 : 0;
        $nextIndex = ($studentIndex + 1 < $totalStudents)
            ? $studentIndex + 1
            : $studentIndex;
            
    $appName = $this->appName();
    $title = "Report Remarks";
    
    
  var_dump ("in remarksCntrlr > comments",$comments);

        // Render view
        $this->render('report_remarks/index', [
        'title' => $title,
        'appName' => $appName,
        
            'classes' => $classes,
            'periods' => $periods,

            'classId' => $classId,
            'periodId' => $periodId,
            
            'max_attendance' => $max_attendance,

            'students' => $students,
            'currentStudent' => $currentStudent,
            
       'isFirstStudent' => $isFirstStudent,
       'isLastStudent' => $isLastStudent,

            'results' => $results,
            'attendance' => $attendance,
            'comments' => $comments,
            'domains' => $domains,
            'domainScores' => $domainScores,

            'studentIndex' => $studentIndex,
            'totalStudents' => $totalStudents,
            'prevIndex' => $prevIndex,
            'nextIndex' => $nextIndex,
        ]);
    }

    /*
    |-----------------------------------------
    | AJAX SAVE (ALL DATA FOR ONE STUDENT)
    |-----------------------------------------
    */
    public function save(): void
    {
    
        header('Content-Type: application/json');

        $schoolId = $_SESSION['school_id'];

        $studentId = (int) ($_POST['student_id'] ?? 0);
        $periodId  = (int) ($_POST['period_id'] ?? 0);

/*******************
 CHECK LOCK STATUS 
 ******************/

$isAdmin = ($_SESSION['role'] ?? '') === 'admin'
||
 ($_SESSION['role'] ?? '') === 'creator'
;

$error = $this->canEditPeriod(
    $schoolId,
    $periodId,
    $isAdmin,
    $this->pdo
);

if ($error) {

    echo json_encode([
        'status' => 'error',
        'message' => $error
    ]);

    return;
}

/**********/

        if (!$studentId || !$periodId) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Invalid request'
            ]);
            return;
        }
        
         //2b. get period (session/term) settings :: for dys open = max attendance,
$periodSettings = $this->schoolPeriodSettingsModel ->getBySchoolAndPeriod($schoolId, $periodId);
$max_attendance = $periodSettings['days_open'] ?? 0;
$attendance = $_POST['attendance'] ?? 0;
        
       if ($attendance > $max_attendance) {
            echo json_encode([
                'status' => 'error',
                'message' => "Error! Max Attendance ($max_attendance) is exceeded"
            ]);
            return;
        }

        try {

            // Attendance
            if (isset($_POST['attendance'])) {
                $this->reportRemarksModel->saveAttendance(
                    $studentId,
                    $periodId,
                    (int) $_POST['attendance']
                );
            }

            // Comments
            if (isset($_POST['comments']['class_teacher'])) {
                $this->reportRemarksModel->saveComment(
                    $studentId,
                    $periodId,
                    'class_teacher',
                    $_POST['comments']['class_teacher']
                );
            }

            if (isset($_POST['comments']['principal'])) {
                $this->reportRemarksModel->saveComment(
                    $studentId,
                    $periodId,
                    'principal',
                    $_POST['comments']['principal']
                );
            }

            // Domain scores
            if (isset($_POST['domains']) && is_array($_POST['domains'])) {
                foreach ($_POST['domains'] as $domainId => $rating) {

                    $this->reportRemarksModel->saveDomainScore(
                        $studentId,
                        $periodId,
                        (int) $domainId,
                        (int) $rating
                    );
                }
            }

            echo json_encode([
                'status' => 'success',
                'message' => 'Saved successfully'
            ]);

        } catch (\Throwable $e) {

            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }
        
    
}












