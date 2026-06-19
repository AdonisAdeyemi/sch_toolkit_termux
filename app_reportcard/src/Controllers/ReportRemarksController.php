<?php

namespace ReportCard\Controllers;

use ReportCard\Models\ReportRemarksModel;
use Core\Controllers\BaseController;
use PDO;

class ReportRemarksController extends BaseController
{
    private ReportRemarksModel $model;
    private PDO $pdo;
    private ?string $appName;

    public function __construct(PDO $pdo)
    {
        $this->model = new ReportRemarksModel($pdo);
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
        $classes = $this->model->getClasses($schoolId);
        $periods = $this->model->getPeriods();

        $students = [];
        $currentStudent = null;

        $results = [];
        $attendance = null;
        $comments = [];
        $domains = [];
        $domainScores = [];

        $studentIds = [];

        if ($classId && $periodId) {

            // Load students in class
            $students = $this->model->getStudentsByClass($classId);
            $studentIds = $this->model->getStudentIdsByClass($classId);
  $totalStudents = count($studentIds) ;

            // Clamp index
            if ($studentIndex < 0) {
                $studentIndex = 0;
            }

            if ($studentIndex >= $totalStudents) {
                $studentIndex = 0;
            }
            
//for navigation            
 $isFirstStudent = ($studentIndex <= 0);
$isLastStudent  = ($studentIndex >= ($totalStudents - 1));

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
                $results = $this->model->getStudentResults($currentStudentId, $periodId);
                $attendance = $this->model->getAttendance($currentStudentId, $periodId);
                $comments = $this->model->getComments($currentStudentId, $periodId);
                $domains = $this->model->getDomains($schoolId);
                $domainScores = $this->model->getDomainScores($currentStudentId, $periodId);
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

        // Render view
        $this->render('report_remarks/index', [
        'title' => $title,
        'appName' => $appName,
        
            'classes' => $classes,
            'periods' => $periods,

            'classId' => $classId,
            'periodId' => $periodId,

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

        $studentId = (int) ($_POST['student_id'] ?? 0);
        $periodId  = (int) ($_POST['period_id'] ?? 0);

        if (!$studentId || !$periodId) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Invalid request'
            ]);
            return;
        }

        try {

            // Attendance
            if (isset($_POST['attendance'])) {
                $this->model->saveAttendance(
                    $studentId,
                    $periodId,
                    (int) $_POST['attendance']
                );
            }

            // Comments
            if (isset($_POST['comments']['class_teacher'])) {
                $this->model->saveComment(
                    $studentId,
                    $periodId,
                    'class_teacher',
                    $_POST['comments']['class_teacher']
                );
            }

            if (isset($_POST['comments']['principal'])) {
                $this->model->saveComment(
                    $studentId,
                    $periodId,
                    'principal',
                    $_POST['comments']['principal']
                );
            }

            // Domain scores
            if (isset($_POST['domains']) && is_array($_POST['domains'])) {
                foreach ($_POST['domains'] as $domainId => $rating) {

                    $this->model->saveDomainScore(
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
