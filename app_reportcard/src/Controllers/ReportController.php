<?php
namespace ReportCard\Controllers;

use ReportCard\Services\ReportService;
use ReportCard\Models\StudentModel;
use ReportCard\Models\AcademicPeriodModel;


use Core\Controllers\BaseController;

use ReportCard\Models\ClassModel;
use ReportCard\Models\EnrollmentModel;
use ReportCard\Models\SchoolPeriodSettingsModel;
use PDO;

// use Core\Lib\PdfService; currently not working yet (composer issue)

//require_once __DIR__ . '/../../../core/lib/PdfService.php';

use Core\lib\PdfService;

class ReportController extends BaseController
{
    private ReportService $reportService;
    private PdfService $pdfService;
    private StudentModel $studentModel;
    private AcademicPeriodModel $academicPeriodModel;
    
    private ClassModel $classModel;

private EnrollmentModel $enrollmentModel;

private SchoolPeriodSettingsModel $schoolPeriodSettingsModel;
private PDO $pdo ;
    
    

    public function __construct($pdo)
    {
    $this->pdo = $pdo ;
        $this->reportService = new ReportService($pdo);
        $this->pdfService = new PdfService();
        $this->studentModel = new StudentModel($pdo);
        $this->academicPeriodModel = new AcademicPeriodModel($pdo);
       
    $this->classModel =
        new ClassModel($pdo);

    $this->enrollmentModel =
        new EnrollmentModel($pdo);

    $this->schoolPeriodSettingsModel =
        new SchoolPeriodSettingsModel($pdo);
}
        
/*******************/

public function index()
{
    try {

        $schoolId = $_SESSION['school_id'];

        $title =
            $this->appName() .
            " Generate Report Cards";

        $appName =
            $this->appName();

        $classes =
            $this->classModel
                ->getClassesBySchool($schoolId);

$activePeriod = $this->requireActivePeriod($this->pdo);

        $this->render(
            'report_print/index',
            compact(
                'title',
                'appName',
                'classes',
                'activePeriod'
            )
        );

    } catch (\Throwable $e) {

        writeLog(
            ">debug-reportCntrlIndex.php",
             $e->getMessage(),
        );

        setFlash(
            "danger",
            "Unable to load report page."
        );

        header("Location: /{$this->appName()}/dashboard");
        exit;
    }
}
        
 /******************/
 
 public function students($request)
{
    header('Content-Type: application/json');

    try {

        $schoolId =
            $_SESSION['school_id'];

        $classId =
            (int)(
                $request['get']['class_id']
                ?? 0
            );

        if (!$classId) {

            echo json_encode([]);

            return;
        }

        $activePeriod =
            $this->schoolPeriodSettingsModel
                ->getActivePeriod($schoolId);

        if (!$activePeriod) {

            echo json_encode([]);

            return;
        }

        $sessionId =
            $activePeriod['session_id'];


        $students =
            $this->enrollmentModel
                ->getEnrollments(
                    $schoolId,
                    $sessionId,
                    $classId,
                    0,
                    ""
                );

        echo json_encode($students);

    } catch (\Throwable $e) {

        log_debug(
            $e->getMessage(),
            "reportStudents"
        );

        echo json_encode([]);
    }
}   

    /**
     * Generate class report card
     * Route: /reportcard/generate/class?id=2
     */
    public function generateClass($request)
    {
    $schoolId =  $_SESSION['school_id']; //get orig from $_SESSION
    $periodId =  $request['get']['period_id'] ?? null;
        $classId = $request['get']['class_id'] ?? null;
        

        if (!$classId) {
            http_response_code(400);
            echo "Class ID is required";
            return;
        }
        if (!$periodId) {
            http_response_code(400);
            echo "Period ID is required";
            return;
        }
       
       $sessionId = $this->academicPeriodModel
    ->getSessionIdByPeriodId($periodId);

        // 1. Build HTML via service
        $html = $this->reportService->generateClassReport($schoolId, $classId, $periodId, $sessionId);

//echo $html;

        // 2. Send to PDF
return $this->pdfService->stream($html, "class-report-$classId.pdf");
    }

    /**
     * Generate single student report card
     * Route: /reportcard/generate/student?id=15
     */
    public function generateStudent($request)
    {
 
 $isPreview = $request['get']['isPreview'] ?? false;
 

 $schoolId =  $_SESSION['school_id']; 
 $studentId = $request['get']['student_id'] ?? 0;

 $periodId = $request['get']['period_id'] ?? 0;
 
       $sessionId = $this->academicPeriodModel
    ->getSessionIdByPeriodId($periodId) ?? 0;
   $classId = $this->studentModel->getClassIdByStudentAndSession($studentId,$sessionId) ?? 0;

 
 $html = "";
    if( $isPreview )
    {
 $html = $this->reportService->generatePreview($schoolId);
    }
    
    else
    {
          
    //THIS is single StuDEnt
          
        if (!$studentId) {
            http_response_code(400);
            echo "Student ID is required";
            return;
        }
        if (!$periodId) {
            http_response_code(400);
            echo "Period ID is required";
            return;
        }
        if (!$classId) {
            http_response_code(400);
            echo "Class ID is required";
            return;
        }

//THIS is single StuDEnt

$html = $this->reportService->generateStudentReport($schoolId, $studentId, $classId, $periodId, $sessionId);

//THIS is single StuDEnt
}

//echo $html;

$printTitle = "student-report-$studentId.pdf";

if($isPreview)
{
$printTitle = "preview-report.pdf";
}



  return $this->pdfService->stream($html, $printTitle );
    }
}









