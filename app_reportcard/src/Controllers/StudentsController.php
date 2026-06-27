<?php

namespace ReportCard\Controllers;

use Core\Controllers\BaseController;

use ReportCard\Models\StudentModel;
use ReportCard\Models\EnrollmentModel;
use ReportCard\Models\ClassModel;
use ReportCard\Models\AcademicSessionModel;
use PDO;

class StudentsController extends BaseController
{

    private StudentModel $studentModel;
    private EnrollmentModel $enrollmentModel;
    private ClassModel $classModel;
    private AcademicSessionModel $academicSessionModel;
        private PDO $pdo;



    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        
        $this->studentModel = new StudentModel($pdo);
        $this->enrollmentModel = new EnrollmentModel($pdo);
        $this->classModel = new ClassModel($pdo);
        $this->academicSessionModel = new AcademicSessionModel($pdo);
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

var_dump ("> in stdntCntrlr > get arr : ", $_GET);
echo "<br><br>";

        $sessions = $this->academicSessionModel
            ->getAllSessions();

        $classes = $this->classModel
            ->getClassesBySchool($schoolId);

        $students = [];

        if ($sessionId > 0) {

            $students = $this->enrollmentModel
                ->getEnrollments(
                    $schoolId,
                    $sessionId,
                    $classId,
                    $searchTerm
                );

        }

        $this->render(
            'students/index',
            [
            'appName' => $appName,
            'title' => "Class Students",
                'sessions'  => $sessions,
                'classes'   => $classes,
                'students'  => $students,
                'sessionId' => $sessionId,
                'classId'   => $classId
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

    $studentName = trim($_POST['student_name'] ?? '');
    $admissionNo = trim($_POST['admission_no'] ?? '');
    $religion    = trim($_POST['religion'] ?? '');
    $sex         = trim($_POST['sex'] ?? '');

    $sessionId = (int)($_POST['session_id'] ?? 0);
    $classId   = (int)($_POST['class_id'] ?? 0);

    if (
        $studentName === '' ||
        $religion === '' ||
        $sex === '' ||
        $sessionId <= 0 ||
        $classId <= 0
    ) {

        echo json_encode([
            'status' => 'error',
            'message' => 'Please complete all required fields.'
        ]);

        return;
    }

    /*
    |--------------------------------------------------------------------------
    | Upload Passport (optional)
    |--------------------------------------------------------------------------
    */

    $passportUrl = null;

    if (
        isset($_FILES['passport']) &&
        $_FILES['passport']['error'] === UPLOAD_ERR_OK
    ) {

        // We'll replace this with your upload helper later.
        $passportUrl = $this->uploadImage(
            $_FILES['passport'],
            'students'
        );

        if ($passportUrl === false) {

            echo json_encode([
                'status' => 'error',
                'message' => 'Passport upload failed.'
            ]);

            return;

        }

    }

    /*
    |--------------------------------------------------------------------------
    | Create Student
    |--------------------------------------------------------------------------
    */

    $studentId = $this->studentModel->createStudent(
        $schoolId,
        $studentName,
        $admissionNo !== '' ? $admissionNo : null,
        $religion,
        $sex,
        $passportUrl
    );

    if (!$studentId) {

        echo json_encode([
            'status' => 'error',
            'message' => 'Unable to create student.'
        ]);

        return;

    }

    /*
    |--------------------------------------------------------------------------
    | Enroll Student
    |--------------------------------------------------------------------------
    */

    $success = $this->enrollmentModel->enrollStudent(
        $schoolId,
        $studentId,
        $sessionId,
        $classId
    );

    if (!$success) {

        echo json_encode([
            'status' => 'error',
            'message' => 'Student created but enrollment failed.'
        ]);

        return;

    }

    /*
    |--------------------------------------------------------------------------
    | Success
    |--------------------------------------------------------------------------
    */

    echo json_encode([

        'status' => 'success',

        'message' => 'Student created successfully.',

        'student' => [

            'student_id'   => $studentId,
            'student_name' => $studentName,
            'admission_no' => $admissionNo,
            'religion'     => $religion,
            'sex'          => $sex,
            'class_id'     => $classId,
            'class_name'   => $this->classModel->getClassNameById($classId),
            'passport_url' => $passportUrl

        ]

    ]);
}


/***********************/



/***************************/

}




















