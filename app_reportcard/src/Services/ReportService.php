<?php
namespace ReportCard\Services;

use ReportCard\Models\ReportScoreRepository;
use ReportCard\Models\SettingsModel;

use ReportCard\Core\View;

class ReportService
{
    private ReportScoreRepository $repo;
    private SettingsModel $settingsModel;

    public function __construct($pdo)
    {
        $this->repo = new ReportScoreRepository($pdo);
        $this->settingsModel = new SettingsModel($pdo);
    }

    /**
     * CLASS REPORT
     */
    public function generateClassReport($schoolId,$classId, $periodId)
    {
        // 1. Fetch raw rows (flat SQL result)
        $rows = $this->repo->getClassResults($schoolId, $classId, $periodId);

        // 2. Get report settings (school preferences, grading, watermark etc.)
        $settings = $this->settingsModel->getReportSettings($schoolId);

        // 3. Transform raw rows → structured students_data
        $studentsData = $this->buildStudentsData($rows);

        // 4. Render HTML view
        return $this->renderView($studentsData, $settings);
    }


    /**
     * SINGLE STUDENT REPORT
     */
    public function generateStudentReport($studentId, $periodId)
    {
        $rows = $this->repo->getStudentResults($studentId, $periodId);

        $settings = $this->settingsModel->getReportSettings($schoolId);

        $studentsData = $this->buildStudentsData($rows, true);

        return $this->renderView($studentsData, $settings);
    }

    /**
     * CORE TRANSFORMATION ENGINE
     * Converts flat SQL rows → nested student structure
     */
    private function buildStudentsData($rows, $singleStudent = false)
    {
        $students = [];

        foreach ($rows as $row) {

            $studentId = $row['student_id'];

            // initialize student bucket
            if (!isset($students[$studentId])) {
                $students[$studentId] = [
                    'student' => [
                        'id' => $studentId,
                        'name' => $row['student_name'],
                        'class' => $row['class_name'],
                        'position_text' => $row['position_text'] ?? '',
                    ],
                    'subjects' => [],
                    'affective' => [],
                    'psychomotor' => [],
                    'summary' => []
                ];
            }

            // SUBJECT DATA
            if (!empty($row['subject_id'])) {
                $students[$studentId]['subjects'][] = [
                    'subject' => $row['subject_name'],
                    'ca1' => $row['ca1'],
                    'ca2' => $row['ca2'],
                    'exam' => $row['exam'],
                    'total' => $row['total'],
                    'grade' => $row['grade'],
                    'remark' => $row['remark'],
                    'position' => $row['subject_position'] ?? ''
                ];
            }

            // AFFECTIVE DOMAIN
            if (!empty($row['affective_name'])) {
                $students[$studentId]['affective'][] = [
                    'domain_name' => $row['affective_name'],
                    'rating' => $row['affective_rating']
                ];
            }

            // PSYCHOMOTOR DOMAIN
            if (!empty($row['psychomotor_name'])) {
                $students[$studentId]['psychomotor'][] = [
                    'domain_name' => $row['psychomotor_name'],
                    'rating' => $row['psychomotor_rating']
                ];
            }
        }

        // if single student, return only first record
        if ($singleStudent) {
return array_values($students)[0] ?? null;
        }

        return array_values($students);
    }

    /**
     * VIEW RENDERING
     */
   
private function renderView($studentsData, $settings)
{
    return View::render('reportcard/report_card', [
        'students' => $studentsData,
        'report_settings' => $settings
    ]);
}
    

    
    
    
    
    
    
    
    
}












