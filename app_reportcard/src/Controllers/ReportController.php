<?php
namespace ReportCard\Controllers;

use ReportCard\Services\ReportService;
use ReportCard\Core\PdfService;

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
        $classId = $request['get']['id'] ?? null;

        if (!$classId) {
            http_response_code(400);
            echo "Class ID is required";
            return;
        }

        // 1. Build HTML via service
        $html = $this->reportService->generateClassReport($classId);

        // 2. Send to PDF
        return $this->pdfService->stream($html, "class-report-$classId.pdf");
    }

    /**
     * Generate single student report card
     * Route: /reportcard/generate/student?id=15
     */
    public function generateStudent($request)
    {
        $studentId = $request['get']['id'] ?? null;

        if (!$studentId) {
            http_response_code(400);
            echo "Student ID is required";
            return;
        }

        $html = $this->reportService->generateStudentReport($studentId);

        return $this->pdfService->stream($html, "student-report-$studentId.pdf");
    }
}









