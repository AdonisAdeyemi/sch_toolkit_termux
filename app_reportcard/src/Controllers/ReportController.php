<?php
namespace ReportCard\Controllers;

use ReportCard\Services\ReportService;
// use Core\Lib\PdfService; currently not working yet (composer issue)

require_once __DIR__ . '/../../../core/lib/PdfService.php';

use Core\Lib\PdfService;


class ReportController
{
    private ReportService $reportService;
    private PdfService $pdfService;

    public function __construct($pdo)
    {
        $this->reportService = new ReportService($pdo);
        $this->pdfService = new PdfService();
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
    $studentId = $request['get']['student_id'] ?? null;
   $periodId = $request['get']['period_id'] ?? null;
          
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

//THIS is single StuDEnt

      $html = $this->reportService->generateStudentReport($studentId, $periodId);

//THIS is single StuDEnt

      return $this->pdfService->stream($html, "student-report-$studentId.pdf");
    }
}









