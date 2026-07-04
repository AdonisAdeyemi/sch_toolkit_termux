<?php
namespace ReportCard\Controllers;

use ReportCard\Models\ResultModel;
use ReportCard\Services\ResultService;

use ReportCard\Models\ClassModel;
use ReportCard\Models\SubjectModel;
use PDO;




use Core\Controllers\BaseController;

class ResultController extends BaseController

{
    private ResultModel $model;
    private ResultService $service;
    private $pdo;

    public function __construct($pdo)
    {
    $this->pdo = $pdo;
        $this->model = new ResultModel($pdo);
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
    $stmt = $this->pdo->prepare(
    "SELECT rc.id, ct.label as class_name
     FROM report_classes rc
            JOIN report_class_templates ct
             ON ct.id = rc.class_template_id 
     WHERE school_id = ?
     AND is_deleted = 0"
);

$stmt->execute([$schoolId]);

$classes = $stmt->fetchAll(PDO::FETCH_ASSOC);

//xxxxxxxxxxxxxxxxxxxxxx
$stmt = $this->pdo->prepare(
    "SELECT id, subject_name
     FROM report_subjects
     WHERE is_deleted = 0
     ORDER BY display_order ASC"
);

$stmt->execute();

$subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);

//xxxxxxxxxxxxxxxxxxxxxxxxxxxx

$stmt = $this->pdo->prepare(
    "SELECT id, session, term
     FROM report_academic_periods"
);

$stmt->execute();

$periods = $stmt->fetchAll(PDO::FETCH_ASSOC);

//xxxxxxxxxxxxxxxxxxxxx

$title = 'Student Results';
        
                return $this->render('/results/index', [
        'title' => $title,
        'appName' => $this->appName(),
            'subjects' => $subjects,
            'classes' => $classes,
         'periods' => $periods
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
            $classId        = (int)$_POST['class_id'];
            $subjectId      = (int)$_POST['subject_id'];
            $periodId       = (int)$_POST['period_id'];

            $classSubjectId = $this->model->getClassSubjectId(
                $classId,
                $subjectId
            );

            if (!$classSubjectId) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Class subject mapping not found'
                ]);
                return;
            }

            $grid = $this->model->getSubjectGrid(
                $classId,
                $classSubjectId,
                $periodId
            );

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
            $classSubjectId = (int)$_POST['class_subject_id'];
            $periodId       = (int)$_POST['period_id'];

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
                    $ca1List[$i] ?? 0,
                    $ca2List[$i] ?? 0,
                    $examList[$i] ?? 0
                );

                $this->model->upsert($payload);

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
                'message' => $e->getMessage()
            ]);
        }
    }
}
