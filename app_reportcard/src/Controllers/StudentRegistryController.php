<?php

namespace ReportCard\Controllers;

use Core\Controllers\BaseController;
use PDO;
use ReportCard\Models\StudentModel;

class StudentRegistryController extends BaseController
{
    private StudentModel $studentModel;
    private PDO $pdo ;

    public function __construct(PDO $pdo)
    {
  $this->pdo = $pdo;

        $this->studentModel = new StudentModel($pdo);
    }

    /****************************************************************
     * Student Registry
     ***************************************************************/

    public function index(): void
    {
    
            $appName = $this->appName();
$title = "Student Register";
        $schoolId = (int)$_SESSION['school_id'];

        $search = trim($_GET['search'] ?? '');

        $students = $this->studentModel->getRegistryStudents(
            $schoolId,
            $search
        );
        
  

        $this->render( 'student_registry/index', 
        [
       'appName' => $appName ,
        'title' => $title ,
        'students' => $students
        ]
        
        
        );
    }

    /****************************************************************/

    public function table(): void
    {
        $schoolId = (int)$_SESSION['school_id'];

        $search = trim($_GET['search'] ?? '');

        $students = $this->studentModel->getRegistryStudents(
            $schoolId,
            $search
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
   $passportFile,
   $this->pdo
            
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
        $religion    = $_POST['religion'] ?? '';
        $sex         = $_POST['sex'] ?? '';

        if ($studentName === '') {
            throw new \Exception('Student name is required.');
        }

        if (!in_array($religion, ['CRS', 'IRS'], true)) {
            throw new \Exception('Invalid religion.');
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
            $religion,
            $sex
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



/********/

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






}





