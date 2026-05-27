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
        
        file_put_contents(
    'debug-repo.log',
    "\n>=== in generateClassReport ===\n".
    print_r($rows, true),
    FILE_APPEND
);

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
        
      //  echo "students[studentId] ".$students[$studentId] ;
        
              file_put_contents(
    'debug-student.log',
   date('Y-m-d H:i:s') . 
    "=== in ReportServices :: buildStudentsData ===\n".
    "studentId \n".
    print_r($studentId, true),
    FILE_APPEND
);

        // initialize student bucket
        if (!isset($students[$studentId])) {
            $students[$studentId] = [
                'student_info' => [
                    'id' => $studentId,
                    'name' => $row['student_name'],
                    'class' => $row['class_name'] ?? '',
                    'all_subjects_total' => 0 ,
                    'total_obtainable' => 0,
                    'average' => 0,
               'average_remark' => '',
                    
                    'position_in_class' => null,
                    
                    'position_text' => $row['position_text'] ?? '',
                    
                    'class_size' => 0,
                    
            'teacher_exam_comment' =>
             $row['teacher_exam_comment']?? null,
            'principal_exam_comment' =>
             $row['principal_exam_comment'] ?? null,
    'days_present' => $row["days_present"] ?? null,
        'days_open' => $row["days_open"] ?? null ,
    'days_absent' => ($row["days_open"] ?? 0) -  ($row["days_present"] ?? 0) ,
                    
                    
                    'session' => $row['session'] ?? '',
                    'term' => $row['term'] ?? '',
                ],
                'subjects' => [],
                'affective' => [],
                'psychomotor' => [],
                'summary' => []
            ];
        }
        
    /*
      'student_id' => $sid,
                    'name' => $row['student_name'],
                    'subjects' => [],
                    'domains' => [],
                    'all_subjects_total' => 0,
                    'total_obtainable' => 0,
                    'average' => 0,
                    'position' => 0,
                    'position_text' => '',
                   'class_size' => 0,
                    'remark' => '',
            'teacher_exam_comment' =>
             $row['teacher_exam_comment'],
            'principal_exam_comment' =>
             $row['principal_exam_comment'],
    'days_present' => $row["days_present"],
        'days_open' => $row["days_open"] ,
    'days_absent' => $row["days_open"] -  $row["days_present"]
                ];
    */    
        
        

        // SUBJECT DATA (UPDATED)
     //   if (!empty($row['subject_id']) || !empty($row['class_subject_id'])) {
     
  if (!empty($row['class_subject_id'])) {

            $students[$studentId]['subjects'][] = [
                'subject_name' => $row['subject_name'] ?? $row['base_subject_name'] ?? '',

                // NEW STRUCTURE FIELDS
                'ca1' => $row['ca1_score'] ?? null,
                'ca2' => $row['ca2_score'] ?? null,
                'exam' => $row['exam_score'] ?? null,

                'subject_total' => $row['total_score'] ?? (
                    ($row['ca1_score'] ?? 0) +
                    ($row['ca2_score'] ?? 0) +
                    ($row['exam_score'] ?? 0)
                ),

                'subject_grade' => $row['grade'] ?? '',
                'subject_remark' => $row['remark'] ?? '',

                'subject_position' => $row['subject_position'] ?? '',
            
       'subject_position_text' => '' 
       ];
        }

        // AFFECTIVE DOMAIN (UNCHANGED LOGIC)
        if (!empty($row['affective_name'])) {
            $students[$studentId]['affective'][] = [
                'domain_name' => $row['affective_name'],
                'rating' => $row['affective_rating']
            ];
        }

        // PSYCHOMOTOR DOMAIN (UNCHANGED LOGIC)
        if (!empty($row['psychomotor_name'])) {
            $students[$studentId]['psychomotor'][] = [
                'domain_name' => $row['psychomotor_name'],
                'rating' => $row['psychomotor_rating']
            ];
        }
    }

    // single student mode
if ($singleStudent) {
    foreach ($students as $student) {
        return $student; //returns at first student & loop stops
    }
    return null;
}

    return $students;
}



 // ---------------- TOTALS ----------------
    private function computeTotals(&$students)
    {
        foreach ($students as &$student) {

            $all_subj_total = 0;
            $count = 0;


            foreach ($student['subjects'] as &$sub) {

                $sub['one_subject_total'] = $sub['ca1'] + $sub['ca2'] + $sub['exam'];

                $all_subj_total += $sub['one_subject_total'];
                $count++;

            }
     unset($sub);

         $student['all_subjects_total'] = $all_subj_total;
            $student['average'] = $count ? round($all_subj_total / $count, 2) : 0;
      $student['total_obtainable'] = $count ? $count * 100 : 0;

            // sort subjects once
            uasort($student['subjects'], fn($a, $b) => $a['order'] <=> $b['order']);
           
        }
        unset($student);
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












