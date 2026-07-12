<?php

namespace ReportCard\Controllers;

use Core\Controllers\BaseController;
use PDO;
use ReportCard\Models\StudentModel;
use ReportCard\Models\DepartmentModel ;

class StudentRegistryController extends BaseController
{
    private StudentModel $studentModel;
        private DepartmentModel $departmentModel;
    private PDO $pdo ;

    public function __construct(PDO $pdo)
    {
  $this->pdo = $pdo;

        $this->studentModel = new StudentModel($pdo);
                $this->departmentModel = new DepartmentModel($pdo);
    }

    /****************************************************************
     * Student Registry
     ***************************************************************/

    public function index(): void
    {
    
            $appName = $this->appName();
$title = "Student Register";
        $schoolId = (int)$_SESSION['school_id'];

//many redundant data : only schoolId currently ever enters model
//registry works with model
        $search    = trim($_GET['search'] ?? '');
$sex       = trim($_GET['sex'] ?? '');
$passport  = trim($_GET['passport'] ?? '');
$dob       = trim($_GET['dob'] ?? '');
$showDeleted = !empty($_GET['show_deleted']);

  /*   $departments = $this->departmentModel
    ->getAllDepartments();
*/


        $students = $this->studentModel->getRegistryStudents(
$schoolId,
$search,
$sex,
$passport,
$dob,
$showDeleted
 );
 


        $this->render( 'student_registry/index', 
        [
       'appName' => $appName ,
        'title' => $title ,
        'students' => $students,
      'showDeleted' => $showDeleted
       // 'departments' => $departments
        ]
        
        
        );
    }

    /****************************************************************/

    public function table(): void
    {
        $schoolId = (int)$_SESSION['school_id'];

        $search    = trim($_GET['search'] ?? '');
$sex       = trim($_GET['sex'] ?? '');
$passport  = trim($_GET['passport'] ?? '');
$dob       = trim($_GET['dob'] ?? '');
$showDeleted = !empty($_GET['show_deleted']);

/*
$departmentId = 
(empty($_GET['department_id'])
||
((int) $_GET['department_id']) == 0 
)
    ? null
  :  (int) $_GET['department_id'] ;
   */
    

        $students = $this->studentModel->getRegistryStudents(
$schoolId,
$search,
$sex,
$passport,
$dob,
$showDeleted
 );

        require VIEW_PATH . '/student_registry/partials/table.php';
    }

 /*************************************
 ***************************/

    public function save(): void
    {
        header('Content-Type: application/json');

        try {
        
        $schoolId = (int)$_SESSION['school_id'];
     $data = $_POST ;
   $passportFile = null;
        
 
            
        //xxxxxxxxxxxxxxxxxxxxxxx
        
       if (empty($data)) {
         throw new \Exception(
         'Empty data. Nothing to save.'
              );
            }

    /*******  Passport Upload *********/
    
        if (
            isset($_FILES['passport']) &&
            $_FILES['passport']['error'] === UPLOAD_ERR_OK
        ) {
$passportFile = $_FILES['passport'] ;
        }


            $studentId = $this->registerStudent(
      $schoolId,
     $data,
        $this->pdo ,
   $passportFile
            );

            echo json_encode([
                'status'     => 'success',
                'student_id' => $studentId
            ]);

        } catch (\Throwable $e) {

            http_response_code(500);

            echo json_encode([
                'status'  => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    /****************************************************************/
public function update(): void
{
    header('Content-Type: application/json');

    try {

        $schoolId = (int) $_SESSION['school_id'];

        $studentId = (int) ($_POST['student_id'] ?? 0);

        if ($studentId <= 0) {
            throw new \Exception('Invalid student.');
        }

        $studentName = trim($_POST['student_name'] ?? '');
        $admissionNo = trim($_POST['admission_no'] ?? '');
        $sex         = $_POST['sex'] ?? '';
        
    $dateOfBirth = trim($_POST['date_of_birth'] ?? '');

$dateOfBirth =
    $dateOfBirth !== ''
        ? $dateOfBirth
        : null;
        

        if ($studentName === '') {
            throw new \Exception('Student name is required.');
        }



        if (!in_array($sex, ['M', 'F'], true)) {
            throw new \Exception('Invalid sex.');
        }

        /*
        |--------------------------------------------------------------------------
        | Duplicate Admission Number
        |--------------------------------------------------------------------------
        */

        if (
            $admissionNo !== '' &&
            $this->studentModel->admissionNumberExists(
                $schoolId,
                $admissionNo,
                $studentId      // ignore current student
            )
        ) {
            throw new \Exception(
                'Admission number already exists.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Update Student
        |--------------------------------------------------------------------------
        */

        $this->studentModel->updateStudent(
            $schoolId,
            $studentId,
            $studentName,
            $admissionNo !== '' ? $admissionNo : null,
            $sex,
            $dateOfBirth
        );

        /*
        |--------------------------------------------------------------------------
        | Upload Passport (optional)
        |--------------------------------------------------------------------------
        */

        if (
            isset($_FILES['passport']) &&
            $_FILES['passport']['error'] === UPLOAD_ERR_OK
        ) {

            $passportUrl = $this->uploadImage(
                $_FILES['passport'],
                'passport',
                'student_' . $studentId
            );

            if ($passportUrl === false) {
                throw new \Exception(
                    'Passport upload failed.'
                );
            }

            $this->studentModel->updatePassportUrl(
                $studentId,
                $passportUrl
            );
        }

        echo json_encode([
            'status' => 'success'
        ]);

    } catch (\Throwable $e) {

        http_response_code(400);

        echo json_encode([
            'status'  => 'error',
            'message' => $e->getMessage()
        ]);

    }
}

    /****************************************************************/

public function get(): void
{
    header('Content-Type: application/json');

    try {

        $schoolId = (int) $_SESSION['school_id'];

        $studentId = (int) ($_GET['id'] ?? 0);

        if ($studentId <= 0) {
            throw new \Exception('Invalid student.');
        }

        $student = $this->studentModel->getStudentById(
            $schoolId,
            $studentId
        );

        if (!$student) {
            throw new \Exception('Student not found.');
        }

        echo json_encode([
            'status'  => 'success',
            'student' => $student
        ]);

    } catch (\Throwable $e) {

        http_response_code(400);

        echo json_encode([
            'status'  => 'error',
            'message' => $e->getMessage()
        ]);

    }
}

/************************/
public function delete(): never
{
    $schoolId = $_SESSION['school_id'];

    $studentId = (int) ($_POST['student_id'] ?? 0);

    if ($studentId <= 0) {
        throw new ValidationException(
            'Invalid student selected.'
        );
    }

    $this->studentModel->softDelete(
        $studentId,
        [
            'school_id' => $schoolId
        ]
    );

    setFlash(
        'success',
        'Student deleted successfully.'
    );

    redirectBack();
}
/*********************/

public function restore(): never
{
    $schoolId = $_SESSION['school_id'];

    $studentId = (int) ($_POST['student_id'] ?? 0);

    if ($studentId <= 0) {
        throw new ValidationException(
            'Invalid student selected.'
        );
    }

    $this->studentModel->restoreDeleted(
        $studentId,
        [
            'school_id' => $schoolId
        ]
    );

    setFlash(
        'success',
        'Student restored successfully.'
    );

    redirectBack();
}
/********************/


}





