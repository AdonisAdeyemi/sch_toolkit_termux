<?php
namespace ReportCard\Controllers;

use ReportCard\Models\ResultModel;
use ReportCard\Services\ResultService;

use ReportCard\Models\ClassModel;
use ReportCard\Models\SubjectModel;
use ReportCard\Models\ClassSubjectModel;
use ReportCard\Models\AcademicPeriodModel;

use PDO;




use Core\Controllers\BaseController;

class ResultController extends BaseController

{
    private ResultModel $resultModel;
   private ClassModel $classModel;
    private SubjectModel $subjectModel;
   private ClassSubjectModel $classSubjectModel;
    private ResultService $service;
  private AcademicPeriodModel $academicPeriodModel;
    private $pdo;

    public function __construct($pdo)
    {
    $this->pdo = $pdo;
     $this->resultModel = new ResultModel($pdo);
     $this->classModel = new classModel($pdo);
     $this->subjectModel = new subjectModel($pdo);
   $this->classSubjectModel = new ClassSubjectModel($pdo);
 $this->academicPeriodModel = new AcademicPeriodModel($pdo);
     $this->service = new ResultService($pdo);
    }

    /**
     * MAIN PAGE (TAB UI)
     */
    public function index($data)
    {
        $schoolId = $_SESSION['school_id'];
        
        
        // refactor to MVC models

//xxxxxxxxxxxxxxxxxxxxxx


$classes = $this->classModel->getClassesBySchool($schoolId);


//xxxxxxxxxxxxxxxxxxxxxx
/*
$stmt = $this->pdo->prepare(
    "SELECT id, subject_name
     FROM report_subjects
     WHERE is_deleted = 0
     ORDER BY display_order ASC"
);

$stmt->execute();

$subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);
*/

//xxxxxxxxxxxxxxxxxxxxxxxxxxxx


$periods = $this->academicPeriodModel->getPeriodsList();


//xxxxxxxxxxxxxxxxxxxxx

$title = 'Student Results';
$css = '/public/shared/assets/css/results.css';
        
                return $this->render('/results/index', [
        'title' => $title,
        'appName' => $this->appName(),
            'classes' => $classes,
         'periods' => $periods,
         'css' => $css
        ]);
    }

    /**
     * LOAD SUBJECT GRID (AJAX)
     */
    public function loadSubjectGrid($data)
    {
    
    //refactor later to use router data
    
        header('Content-Type: application/json');

        try {
      $schoolId = $_SESSION['school_id'];
            $classId        = (int)$_POST['class_id'];
           // $subjectId      = (int)$_POST['subject_id'];
           $classSubjectId      = (int)$_POST['class_subject_id'];
            $periodId       = (int)$_POST['period_id'];
            $sessionId = $this->academicPeriodModel
    ->getSessionIdByPeriodId($periodId);
            

            if (!$classSubjectId) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Class subject mapping not found'
                ]);
                return;
            }

            $grid = $this->resultModel->getSubjectGrid(
                $schoolId ,
                $classId,
                $classSubjectId,
                $periodId,
                $sessionId
            );
            
//   var_dump ("in resultCintroller >> grid : ", $grid);

            echo json_encode([
                'status' => 'success',
                'data'   => $grid
            ]);

        } catch (Exception $e) {

            echo json_encode([
                'status'  => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * SAVE SUBJECT RESULTS (BULK AJAX)
     */
    public function saveSubjectResults($data)
    {
        header('Content-Type: application/json');

        try {
  $schoolId = $_SESSION['school_id'];  
            $classSubjectId = (int)$_POST['class_subject_id'];
            $periodId       = (int)$_POST['period_id'];



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





            $studentIds = $_POST['student_id'] ?? [];
            $ca1List    = $_POST['ca1'] ?? [];
            $ca2List    = $_POST['ca2'] ?? [];
            $examList   = $_POST['exam'] ?? [];

            $saved = [];

            foreach ($studentIds as $i => $studentId) {

                $payload = $this->service->buildPayload(
                    (int)$studentId,
                    $classSubjectId,
                    $periodId,
                    (int)$ca1List[$i] ?? -1,
                    (int)$ca2List[$i] ?? -1,
                    (int)$examList[$i] ?? -1
                );

                $this->resultModel->upsert($payload);

                $saved[] = $studentId;
            }

            echo json_encode([
                'status' => 'success',
                'saved_count' => count($saved),
                'message' => 'Results saved successfully'
            ]);

        } catch (Exception $e) {

            echo json_encode([
                'status'  => 'error',
                'message' => "Sorry,".$e->getMessage()
            ]);
        }
    }
}






