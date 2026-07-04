<?php

namespace ReportCard\Controllers;
use Core\Controllers\BaseController;
use ReportCard\Models\AcademicSessionModel;
use ReportCard\Models\ClassModel;
use ReportCard\Models\EnrollmentModel;
use ReportCard\Models\DepartmentModel ;

use PDO;

class ExistingEnrollmentController extends BaseController
{

    private AcademicSessionModel $academicSessionModel;
        private EnrollmentModel $enrollmentModel;
        private ClassModel $classModel;
        private DepartmentModel $departmentModel;        
        private PDO $pdo ;
        

    public function __construct(PDO $pdo)
    {
            $this->pdo = $pdo;
        $this->academicSessionModel = new AcademicSessionModel($pdo);
                $this->classModel = new ClassModel($pdo);
                $this->departmentModel = new DepartmentModel($pdo);
        $this->enrollmentModel = new EnrollmentModel($pdo);                
    }

    /*
    |--------------------------------------------------------------------------
    | Page
    |--------------------------------------------------------------------------
    */

/*
    public function index(): void
    {
      
        $this->render(
            'existing_enrollment/index',
            [
            'appName' => $this->appName(),
            'title' => "Enroll Students"
            ]
        );
    }
*/


public function index(): void
{

    $appName = $this->appName();
        $schoolId = (int)$_SESSION['school_id'];

    $sessionId = (int) ($_GET['session_id'] ?? 0);
    $classId   = (int) ($_GET['class_id'] ?? 0);


    if ($sessionId <= 0 || $classId <= 0) {
    
   setFlash( "danger","Session and Class required") ;    
   
     header(
  "Location: /$appName/student_manage"
        );

        exit;
    }

    $session = $this->academicSessionModel
        ->getSessionById(
            $sessionId
        );

    $class = $this->classModel
        ->getClassBySchoolAndId(
            $schoolId,
            $classId
        );

    if (!$session || !$class) {

   setFlash( "danger","Session or Class not found") ;    
        header(
            "Location: /$appName/student_manage"
        );

        exit;
    }

        $classes = $this->classModel
            ->getClassesBySchool($schoolId);


$referenceData = [

    'classes' => $this->classModel
        ->getClassesWithLevels($schoolId),

    'departments' => $this->departmentModel
        ->getAllGroupedByClassLevel()

];

    $this->render(
        'existing_enrollment/index',
        [
        'appName' => $this->appName(),
        'title' => "Enroll Existing Students",
            'session'   => $session,
            'class'     => $class,
            'sessionId' => $sessionId,
            'classId'   => $classId,
            'classes' => $classes,
       'referenceData' => $referenceData
        ]
    );
}


    /*
    |--------------------------------------------------------------------------
    | Student Table
    |--------------------------------------------------------------------------
    */

public function table(): void
{
    $schoolId = $this->schoolId();

    $sessionId = (int) ($_GET['session_id'] ?? 0);
    $classId   = (int) ($_GET['class_id'] ?? 0);

    $search   = trim($_GET['search'] ?? '');

    $sex      = trim($_GET['sex'] ?? '');
    
    $previousClassId = (int) ($_GET['previous_class_id'] ?? 0);
    
    



    $students = $this->enrollmentModel
        ->getStudentsNotEnrolledInSession(
            $schoolId,
            $sessionId,
            $search,
            $sex,
            $previousClassId
        );



    require VIEW_PATH . '/existing_enrollment/partials/table.php';
}

    /*
    |--------------------------------------------------------------------------
    | Enroll Existing Student
    |--------------------------------------------------------------------------
    */

    public function enroll(): void
{

    $schoolId = $this->schoolId();

    $sessionId = (int) ($_POST['session_id'] ?? 0);
    $classId   = (int) ($_POST['class_id'] ?? 0);
    $studentId = (int) ($_POST['student_id'] ?? 0);
    $departmentId = (int) ($_POST['department_id'] ?? 0);
    /*
    |--------------------------------------------------------------------------
    | Validate
    |--------------------------------------------------------------------------
    */

    if (
        $sessionId <= 0 ||
        $classId <= 0 ||
        $studentId <= 0 ||
        $departmentId <= 0         
    ) {

        echo json_encode([
            'status'  => 'error',
            'message' => 'Invalid request. Input all required data.'
        ]);

        return;
    }

    /*
    |--------------------------------------------------------------------------
    | Already Enrolled?
    |--------------------------------------------------------------------------
    */

    if (
        $this->enrollmentModel->isStudentEnrolled(
            $sessionId,
            $studentId,
            $schoolId
        )
    ) {

        echo json_encode([
            'status'  => 'error',
            'message' => 'Student is already enrolled for this session.'
        ]);

        return;
    }

    /*
    |--------------------------------------------------------------------------
    | Enroll Student
    |--------------------------------------------------------------------------
    */
    
    /*
    public function enrollStudent(
    int $schoolId,
    int $studentId,
    int $sessionId,
    int $classId,
    int $departmentId
): array {

    */

    $success = $this->enrollmentModel->enrollStudent(
        $schoolId,
        $studentId,
        $sessionId,
        $classId,
        $departmentId
    );

    if (!$success) {

        echo json_encode([
            'status'  => 'error',
            'message' => 'Unable to enroll student.'
        ]);

        return;
    }

    /*
    |--------------------------------------------------------------------------
    | Success
    |--------------------------------------------------------------------------
    */

    echo json_encode([
        'status'  => 'success',
        'message' => 'Student enrolled successfully.'
    ]);
}

/*****************/


/*****************/


}











