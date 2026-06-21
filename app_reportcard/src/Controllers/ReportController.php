<?php
namespace ReportCard\Controllers;

use ReportCard\Services\ReportService;
use ReportCard\Models\StudentModel;
// use Core\Lib\PdfService; currently not working yet (composer issue)

//require_once __DIR__ . '/../../../core/lib/PdfService.php';

use Core\lib\PdfService;


class ReportController
{
    private ReportService $reportService;
    private PdfService $pdfService;
    private StudentModel $studentModel;

    public function __construct($pdo)
    {
        $this->reportService = new ReportService($pdo);
        $this->pdfService = new PdfService();
        $this->studentModel = new StudentModel($pdo);
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
       

        // 1. Build HTML via service
        $html = $this->reportService->generateClassReport($schoolId, $classId, $periodId);

echo $html;

        // 2. Send to PDF
//return $this->pdfService->stream($html, "class-report-$classId.pdf");
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
  $classId = $this->studentModel->getClassIdByStudentId($studentId);
 $periodId = $request['get']['period_id'] ?? null;
 
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

      $html = $this->reportService->generateStudentReport($schoolId, $studentId, $classId, $periodId);

//THIS is single StuDEnt
}

//echo $html;

    return $this->pdfService->stream($html, "student-report-$studentId.pdf");
    }
}









