<?php

namespace ReportCard\Controllers;

use Core\Controllers\BaseController; 

use ReportCard\Models\StudentModel;
use ReportCard\Models\EnrollmentModel;
use ReportCard\Models\ClassModel;
use ReportCard\Models\AcademicSessionModel;
use ReportCard\Models\DepartmentModel ;
use PDO;

class StudentManagementController extends BaseController
{

    private StudentModel $studentModel;
    private EnrollmentModel $enrollmentModel;
    private ClassModel $classModel;
    private AcademicSessionModel $academicSessionModel;
        private DepartmentModel $departmentModel;
    
        private PDO $pdo;



    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        
        $this->studentModel = new StudentModel($pdo);
        $this->enrollmentModel = new EnrollmentModel($pdo);
        $this->classModel = new ClassModel($pdo);
        $this->academicSessionModel = new AcademicSessionModel($pdo);
                $this->departmentModel = new DepartmentModel($pdo);
    }

    /**********************************/

    /**
     * Student Management
     */
    public function index()
    {
    
    $appName = $this->appName();
    
        $schoolId = $_SESSION['school_id'];

        $sessionId = (int)($_GET['session_id'] ?? 0);
        $classId   = (int)($_GET['class_id'] ?? 0);
        
      $searchTerm   =trim( ($_GET['search'] ?? ""));


        $sessions = $this->academicSessionModel
            ->getAllSessions();

        $classes = $this->classModel
            ->getClassesBySchool($schoolId);

/*
        $students = [];


        if ($sessionId > 0) {

            $students = $this->enrollmentModel
                ->getEnrollments(
                    $schoolId,
                    $sessionId,
                    $classId,
 $departmentId,
                    $searchTerm
                );

        }
        */
        

$referenceData = [

    'classes' => $this->classModel
        ->getClassesWithLevels($schoolId),

    'departments' => $this->departmentModel
        ->getAllGroupedByClassLevel()

];

        

        $this->render(
            'student_management/index',
            [
            'appName' => $appName,
            'title' => "Class Students",
                'sessions'  => $sessions,
                'classes'   => $classes,
            //    'students'  => $students,
                'sessionId' => $sessionId,
                'classId'   => $classId,
        'referenceData' => $referenceData
            ]
        );
    }

/**********************************/



/****************************/



/**********************************/

public function save()
{
    header('Content-Type: application/json');

    $schoolId = $_SESSION['school_id'] ?? null;

    if (!$schoolId) {

        echo json_encode([
            'status' => 'error',
            'message' => 'You are not logged in.'
        ]);

        return;
    }
    /*
    var_dump ("<br><br>","> in StudentCntrlr > POST ", $_POST , "<br><br>");
    */

    $studentName = trim($_POST['student_name'] ?? '');
    $admissionNo = trim($_POST['admission_no'] ?? '');
    $sex         = trim($_POST['sex'] ?? '');

    $sessionId = (int)($_POST['session_id'] ?? 0);
    $classId   = (int)($_POST['class_id'] ?? 0);
    $departmentId   = (int)($_POST['department_id'] ?? 0);

    if (
        $studentName === '' ||
        $sex === '' ||
        $sessionId <= 0 ||
        $classId <= 0 ||
        $departmentId <= 0
    ) {

        echo json_encode([
            'status' => 'error',
            'message' => 'Please complete all required fields.'
        ]);

        return;
    }





try {

    $this->pdo->beginTransaction();

    $studentId = $this->createStudentFromRequest($this->pdo);

    if (!$studentId) {

        throw new \Exception(
            'Error registering student.'
        );

    }
    
    
   $enrollResult = $this->enrollmentModel->enrollStudent(
            $schoolId,
            $studentId,
            $sessionId,
            $classId,
            $departmentId
        );

    $isEnrollSuccess =$enrollResult['success'] ;

    if (!$isEnrollSuccess) {

        throw new \Exception(
            'Error enrolling student. '.$enrollResult['message']
        );

    }

    $this->pdo->commit();

    echo json_encode([
        'status'  => 'success',
        'message' => 'Student created successfully.'
    ]);

} catch (\Throwable $e) {

    $this->pdo->rollBack();

    http_response_code(500);

    echo json_encode([
        'status'  => 'error',
        'message' => $e->getMessage()
    ]);
}





}
    
    
    




/***********/

public function table(): void
{

$schoolId = (int)$_SESSION['school_id'];
    $sessionId = (int)($_GET['filter_session_id'] ?? 0);
    $classId   = (int)($_GET['filter_class_id'] ?? 0);
    $searchTerm    = trim($_GET['search'] ?? '');
    $departmentId    = trim($_GET['filter_department_id'] ?? '');

if(!$sessionId || !$classId ) {

        echo json_encode([
            'status'  => 'error',
            'message' => "Error. Select both session AND class"
        ]);
        
        return ;
    }

        writeLog(
            ">debug-stdnt-Cntrlr-Index.php",
 "warning",">in RprtCntrl > schoolId - $schoolId - sessionId - $sessionId - classId - $classId"
        );
        

    
$students = $this->enrollmentModel
        ->getEnrollments(
                    $schoolId,
                    $sessionId,
                    $classId,
                    $departmentId,
                    $searchTerm
                );
    
   // var_dump(">in stdtCntlr > students : ", $students);

    require VIEW_PATH . '/student_management/partials/table.php';
    exit;
}

/***************************/



public function removeFromClass(): void
{
    header('Content-Type: application/json');

    try {
       $schoolId = $_SESSION['school_id'];

        $sessionId = (int) ($_POST['session_id'] ?? 0);
        $studentId = (int) ($_POST['student_id'] ?? 0);

        if ($sessionId <= 0 || $studentId <= 0) {
            throw new \Exception('Invalid request.');
        }

        /*
        |--------------------------------------------------------------------------
        | Verify enrollment exists
        |--------------------------------------------------------------------------
        */

        if (
            !$this->enrollmentModel->isStudentEnrolled(
                $sessionId,
                $studentId,
                $schoolId
            )
        ) {
            throw new \Exception(
                'Student is not enrolled in this class.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Prevent removal if results exist
        |--------------------------------------------------------------------------
        */

        if (
            $this->enrollmentModel->enrollmentHasResults(
                $sessionId,
                $studentId,
                $schoolId
            )
        ) {
            throw new \Exception(
                'Student already has recorded results and cannot be removed from this class.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Remove from class
        |--------------------------------------------------------------------------
        */

        $success = $this->enrollmentModel->removeFromClass(
            $sessionId,
            $studentId,
            $schoolId
        );

        if (!$success) {
            throw new \Exception(
                'Unable to remove student from class.'
            );
        }

        echo json_encode([
            'status' => 'success',
            'message' => 'Student removed from class.'
        ]);

    } catch (\Throwable $e) {

        http_response_code(400);

        echo json_encode([
            'status'  => 'error',
            'message' => $e->getMessage()
        ]);
    }
}



/***************/

}































