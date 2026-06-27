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

        // TODO
    }

    /****************************************************************/

    public function get(): void
    {
        header('Content-Type: application/json');

        try {

            $studentId = (int)($_GET['student_id'] ?? 0);

            $student = $this->studentModel->getStudentById(
                $studentId
            );

            echo json_encode([
                'status'  => 'success',
                'student' => $student
            ]);

        } catch (\Throwable $e) {

            http_response_code(500);

            echo json_encode([
                'status'  => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }
}
