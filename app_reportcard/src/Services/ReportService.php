<?php
namespace ReportCard\Services;

use ReportCard\Models\ReportScoreRepository;
use ReportCard\Models\CardPreferencesModel;
use ReportCard\Models\SchoolPeriodSettingsModel;

use ReportCard\Core\View;
use PDO;

class ReportService
{
 private ReportScoreRepository $repo;
 private CardPreferencesModel $cardPreferencesModel;
 private SchoolPeriodSettingsModel $schoolPeriodSettingsModel;
    private $pdo;

    public function __construct($pdo)
    {
    $this->pdo = $pdo;
        $this->repo = new ReportScoreRepository($pdo);
        $this->cardPreferencesModel = new CardPreferencesModel($pdo);        
        $this->schoolPeriodSettingsModel = new SchoolPeriodSettingsModel($pdo);
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
    print_r($rows, true)
);


$domains = $this->fetchDomainData($schoolId, $classId, $periodId);

$studentsData = $this->computeAllStudentsData($rows, $domains);






        // 2. Get report settings (school preferences, grading, watermark etc.)
        $cardPreferences = $this->cardPreferencesModel-> getCardPreferences ($schoolId);
        
        //2b. get period (session/term) settings :: dys open, vacation, resume. term start, term end DATES
$periodSettings = $this->schoolPeriodSettingsModel ->getSchoolPeriodSettings($schoolId, $periodId);
     
     
     
     

        // 4. Render HTML view
        return $this->renderView($studentsData, $cardPreferences, $periodSettings);
    }


    /**
     * SINGLE STUDENT REPORT
     */
    public function generateStudentReport($schoolId, $studentId, $classId, $periodId)
    {
    
        $rows = $this->repo->getStudentResults($schoolId, $studentId, $classId, $periodId);
        
//include $domains later for single student :: 
//idea =just add a condition to WHERE clause ::
// empty string if no studentId, else put clause
//$domains = $this->fetchDomainData($schoolId, $classId, $periodId);

 $periodSettings = $this->schoolPeriodSettingsModel->getSchoolPeriodSettings($schoolId,$periodId);
 
 $domains = $this->fetchDomainData($schoolId, $classId, $periodId);

        $studentsData = $this-> computeAllStudentsData($rows, $domains);
        
        
         $cardPreferences = $this->cardPreferencesModel-> getCardPreferences ($schoolId);
         

     return $this->renderView($studentsData, $cardPreferences, $periodSettings);
        
    }

/************
**********************/
    public function generatePreview($schoolId)
    {

/*NOTE : Dummy data contains :
$studentsData, periodSettings
*/
require __DIR__ . "/../../../public/reportcard/assets/data/dummy_data.php"; 

  $cardPreferences = $this->cardPreferencesModel-> getCardPreferences ($schoolId);

     return $this->renderView($studentsData, $cardPreferences, $periodSettings);
        
    }


    /**
    xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
    xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
     * CORE TRANSFORMATION ENGINE
     * Converts flat SQL rows → nested student structure
     */
private function buildStudentsData(
    array $rows,
    array $domains,
    bool $singleStudent = false
)
{
    $students = [];

    foreach ($rows as $row) {

        $studentId = $row['student_id'];

        $this->buildStudentBucket(
            $students,
            $studentId,
            $row
        );

        $this->attachSubject(
            $students,
            $studentId,
            $row
        );
    }

    $this->attachDomains(
        $students,
        $domains
    );

    $this->attachClassSize(
        $students
    );

if ($singleStudent) {
    return !empty($students) ? reset($students) : null;
}

    return $students;
}

/*****************************/

private function buildStudentBucket(
    array &$students,
    int $studentId,
    array $row
): void
{
    if (isset($students[$studentId])) {
        return;
    }

    $students[$studentId] = [

        'student_info' => [

            'id' => $studentId,

            'name' => $row['student_name'],

            'class' => $row['class_name'] ?? '',

            'department_name' =>
                $row['department_name'],

            'all_subjects_total' => 0,

            'total_obtainable' => 0,

            'average' => 0,

            'average_remark' => null,

            'position_in_class' => null,

            'position_in_class_text' => null,

            'class_size' => 0,

            'teacher_exam_comment' =>
                $row['teacher_exam_comment'] ?? null,

            'principal_exam_comment' =>
                $row['principal_exam_comment'] ?? null,

            'days_present' =>
                $row['days_present'] ?? null,

            'days_open' =>
                $row['days_open'] ?? null,

            'days_absent' =>
                ($row['days_open'] ?? 0)
                -
                ($row['days_present'] ?? 0),

            'session' =>
                $row['session'] ?? '',

            'term' =>
                $row['term'] ?? null,
        ],

        'subjects' => [],

        'affective' => [],

        'psychomotor' => [],

        'summary' => [],

        'passport_url' =>
            $row['passport_url']
    ];
}
/******************/


private function attachSubject(
    array &$students,
    int $studentId,
    array $row
): void
{
    if (empty($row['class_subject_id'])) {
        return;
    }

    $classSubjectId =
        $row['class_subject_id'];

    $students[$studentId]['subjects']
        [$classSubjectId] = [

        'subject_id' =>
            $classSubjectId,

        'subject_name' =>
            $row['subject_name']
            ??
            $row['base_subject_name']
            ??
            '',

        'subject_order' =>
            $row['subject_rc_order'],

        'ca1' =>
            $row['ca1_score'],

        'ca2' =>
            $row['ca2_score'],

        'exam' =>
            $row['exam_score'],

        'subject_total' =>
            $row['total_score'],

        'subject_grade' => '',

        'subject_grade_remark' => '',

        'position_in_subject' => '',

        'position_in_subject_text' => ''
    ];
}


/**************************************/

private function attachDomains(
    array &$students,
    array $domains
): void
{
    foreach ($domains as $domain) {

        $studentId =
            $domain['student_id'];

        if (
            !isset(
                $students[$studentId]
            )
        ) {
            continue;
        }

        if (
            $domain['domain_type']
            === 'affective'
        ) {

            $students[$studentId]
            ['affective'][] = [

                'domain_name' =>
                    $domain['domain_name'],

                'rating' =>
                    $domain['rating']
            ];
        }

        if (
            $domain['domain_type']
            === 'psychomotor'
        ) {

            $students[$studentId]
            ['psychomotor'][] = [

                'domain_name' =>
                    $domain['domain_name'],

                'rating' =>
                    $domain['rating']
            ];
        }
    }
}

/*******************************************/

private function attachClassSize(
    array &$students
): void
{
    $classSize =
        count($students);

    foreach ($students as &$student) {

        $student['student_info']
        ['class_size']
            = $classSize;
    }

    unset($student);
}





    /**
     * VIEW RENDERING
     */
   
private function renderView($studentsData, $cardPreferences, $periodSettings)
{
    return View::render('reportcard/report_card', [
        'students' => $studentsData,
        'card_preferences' => $cardPreferences,
        'period_settings' => $periodSettings
    ]);
}
    

    
 
    // ---------------- TOTALS ----------------
    private function computeTotals(&$students)
    {
        foreach ($students as &$student) {

            $all_subj_total = 0;
            $count = 0;


            foreach ($student['subjects'] as &$sub) {
                $all_subj_total += $sub['subject_total'];
                $count++;

            }
     unset($sub);

  $student['student_info']['all_subjects_total'] = $all_subj_total;
         
  $student['student_info']['average'] = $count ? round($all_subj_total / $count, 2) : 0;
            
 $student['student_info']['total_obtainable'] = $count ? $count * 100 : 0;

            // sort subjects once
            uasort($student['subjects'], fn($a, $b) => $a['subject_order'] <=> $b['subject_order']);
           
        }
        unset($student);
    }

    // ---------------- OVERALL RANKING ----------------
    private function computeRanking(&$students)
    {
        $list = array_values($students);

        usort($list, fn($a, $b) => $b['student_info']['all_subjects_total']
  <=>
$a['student_info']['all_subjects_total']);

        $rank = 1;
        $prev = null;
        $same = 0;

        foreach ($list as &$s) {

            if ($s['student_info']['all_subjects_total'] === $prev) {
                $s['student_info']['position_in_class'] = $rank;
                $same++;
            } else {
                $rank += $same;
                $same = 1;
                $s['student_info']['position_in_class'] = $rank;
            }

            $s['student_info']['position_in_class_text'] = $this->ordinal($s['student_info']['position_in_class']);
            $prev = $s['student_info']['all_subjects_total'];
        }
        unset($s);

        // map back
        $mapped = [];
        foreach ($list as $s) {
            $mapped[$s['student_info']['id']] = $s;
        }

        $students = $mapped;
    }

    // ---------------- SUBJECT POSITIONS ----------------
    private function computeSubjectPositions(&$students)
    {
    
         // echo "<pre>";
         /*
    //xxx //// var_dump 
    ( 
    "students['1']",
    $students["1"],
    
    "students[1]",
    $students[1],
    "xxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"
    );
    */
    //xxx //// var_dump("> students", $students );
    
         // echo "</pre>";
    
        $subjectBuckets = [];

        // group scores per subject
        foreach ($students as $std_index => $student) {
        
    
         // echo "<pre>";
    // //xxx //// var_dump ("computeSubPos - student index",$std_index);
        // echo "<pre>";    
        
            foreach ($student['subjects'] as $sbj_index => $sub) {
            
            
         // echo "<pre>";
         
    // //xxx //// var_dump ("computeSubPos - sub index",$sbj_index);
    // //xxx //// var_dump ("computeSubPos - sub for each",$sub);
        // echo "<pre>";
            
            
   $subjectBuckets[$sub['subject_id']][] = [
                    'student_id' => $student['student_info']['id'],
               'student_name' => $student['student_info']['name'],
               'subject_name' => $sub['subject_name'],
                    'subject_total' => $sub['subject_total']
                ] ;
                         
   /*                         
 //xxx //// var_dump(
 "> subject_id",
 $sub['subject_id'],
 "> student_id",
 $student['student_id'],
 "  ",
 
         );
   */
                
            }
        }
   // echo "xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx<br>";
   /*
    //xxx //// var_dump 
    (
    "subjectBuckets",
    $subjectBuckets
    );
    */

        // rank per subject
        foreach ($subjectBuckets as $subject_id => $list) {

            usort($list, fn($a, $b) => $b['subject_total'] <=> $a['subject_total']);

            $rank = 1;
            $prev = null;
            $same = 0;

            foreach ($list as $i => &$item) {
                if ($item['subject_total'] === $prev) {
                    $item['position'] = $rank;
                    $same++;
                } else {
                    $rank += $same;
                    $same = 1;
                    $item['position'] = $rank;
                }
                $prev = $item['subject_total'];
            }
            unset ($item);
        
        /*    
            //// var_dump
            (
            "> subject_id",
            $subject_id,
            "> list of cards",
            $list
            );
	*/
             
            // assign back
            foreach ($list as $item) {
           // //// var_dump (">> item in ", $item );
      
      
$student_id = $item['student_id'];      
      
      /*
      if(
    !isset($students["$student_id"]['subjects']["$subject_id"])
    ) // echo "error : null or no array key <br>"  ;
     
     
  if ( !array_key_exists( "$subject_id" , $students["$student_id"]['subjects'])) 
  {
   // echo "error : subject_id $subject_id not found in student subjects list <br>"  ;
  
   
      //xxx //// var_dump("item in ",$item) ;
         
   
   }
     
    if(!is_array($students["$student_id"]['subjects']["$subject_id"]))  // echo "error : subject_id not an array <br>"     ;
    
  
  if ( !array_key_exists('position', $students["$student_id"]['subjects']["$subject_id"])
      )   // echo "error : no position key <br>"      ;
      
      
      {
      //xxx //// var_dump("list's subject_id ",$subject_id) ;
   
      //xxx //// var_dump("item in ",$item) ;
         
      
      //xxx //// var_dump("no [position] : key error ", $subject_id, $students[$item['student_id']]['subjects'] );
       continue ;
       }
       */
       
       //xxx //// var_dump("yes [position] : ", $students[$student_id]['subjects'][$subject_id] );
      
      
  // //// var_dump("> item['position']", $item['position'] );
      
                $students[$student_id]['subjects'][$subject_id]['position_in_subject'] = $item['position'];
                
      $students[$student_id]['subjects'][$subject_id]['position_in_subject_text'] = $this->ordinal( $item['position']);
                          
                
            }
        }
    }
    
    
    // ---------------- GRADING ----------------
    private function applyGrades(&$students)
    {
    
    
        foreach ($students as $std_index => &$student) {
        
        
         // echo "<pre>";
    // //xxx //// var_dump ("ApplyGrade - student index",$std_index);
        // echo "<pre>";
        
            foreach ($student['subjects'] as $sbj_index => &$sub) {
            
         // echo "<pre>";
         
   //  //xxx //// var_dump ("applyGrade - sub index",$sbj_index);
    // //xxx //// var_dump ("applyGrade - sub for each",$sub);
        // echo "<pre>";
            
    $gradeData = $this->grade($sub['subject_total']);

$sub['subject_grade'] = $gradeData['grade'];
$sub['subject_grade_remark'] = $gradeData['remark'];

/*        
     $sub['grade'] = $this->grade($sub['subject_total'] );
     
     $sub['grade_remark'] = $this->grade($sub['subject_total'] );
   */
            }
         unset ($sub);
        }
        unset($student);
    }

private function grade($score)
{
    $grades = [
        85 => ['A1', 'Excellent'],
        75 => ['B2', 'Very Good'],
        70 => ['B3', 'Good'],
        65 => ['C4', 'Credit'],
        60 => ['C5', 'Credit'],
        50 => ['C6', 'Credit'],
        45 => ['D7', 'Pass'],
        40 => ['E8', 'Poor'],
        0  => ['F9', 'Fail']
    ];

    foreach ($grades as $min => [$grade, $remark]) {

        if ($score >= $min) {

            return [
                'grade' => $grade,
                'remark' => $remark
            ];
        }
    }
}

    // ---------------- REMARKS ----------------
    private function applyRemarks(&$students)
    {
        foreach ($students as &$student) {
            $avg = $student['student_info']['average'];
            
            if ($avg >= 75) {
                $student['student_info']['remark'] = 'Excellent performance';
            } elseif ($avg >= 65) {
                $student['student_info']['remark'] = 'Very good performance';
            } elseif ($avg >= 50) {
                $student['student_info']['remark'] = 'Good effort';
            } elseif ($avg >= 40) {
                $student['student_info']['remark'] = 'Needs improvement';
            } else {
                $student['student_info']['remark'] = 'Poor performance';
            }

        }
        unset ($student);
    }
    

    // ---------------- HELPERS ----------------
    private function ordinal($n)
    {
        if (!in_array(($n % 100), [11,12,13])) {
            switch ($n % 10) {
                case 1: return $n . 'st';
                case 2: return $n . 'nd';
                case 3: return $n . 'rd';
            }
        }
        return $n . 'th';
    }
    
    //xxxxxxxxxxxxxxxxxxxxxxxxxxxxx
    
 function fetchDomainData ($school_id, $class_id, $period_id)
 {
$stmt =  $this->pdo->prepare("
SELECT

    s.id AS student_id,

    d.domain_name,
    d.domain_type,

    ds.rating

FROM report_students s

CROSS JOIN report_domains d

LEFT JOIN report_domain_scores ds
    ON ds.student_id = s.id
    AND ds.domain_id = d.id
    AND ds.period_id = ?

WHERE s.class_id = ? AND s.school_id = ?

ORDER BY
    s.id,
    d.domain_type,
    d.sort_order

");

$stmt->execute([
    $period_id,
    $class_id,
    $school_id

]);

$domainRows = $stmt->fetchAll(PDO::FETCH_ASSOC);

return $domainRows ;
 }
    
 /*************
 *************************/
public function computeAllStudentsData($rows,$domains)
 {
        // 3. Transform raw rows → structured students_data
        $studentsData = $this->buildStudentsData($rows, $domains);
        
        
              file_put_contents(
    'debug-rprt-cntrlr-stdntData.log',
    "<pre>"
    . "\n>=== >student data buildStudentsData<br> ===\n"
  .  print_r($studentsData, true) 
     . "xxxxxxxxxxxxxxxx \n
       xxxxxxxxxxxxxxxxx \n
       xxxxxxxxxxxxxxxxx \n\n"
   . "</pre>"
);
        
        
   /* var_dump ("<pre>",   ">student data buildStudentsData<br>",   reset($studentsData) ,
       "</pre>",
       "xxxxxxxxxxxxxxxx<br>
       xxxxxxxxxxxxxxxxx<br>
       xxxxxxxxxxxxxxxxx<br><br>");
       */
        
        $this->computeTotals($studentsData);
 
        
                    file_put_contents(
    'debug-rprt-cntrlr-stdntData.log',
    "<pre>"
    . "\n>=== >student data computeTotals<br> ===\n".
    print_r($studentsData, true) 
     . "xxxxxxxxxxxxxxxx \n
       xxxxxxxxxxxxxxxxx \n
       xxxxxxxxxxxxxxxxx \n\n"
   . "</pre>" ,
   FILE_APPEND
);

        /* var_dump ("<pre>",  ">student data computeTotals<br>",   reset($studentsData) ,
       "</pre>",
       "xxxxxxxxxxxxxxxx<br>
       xxxxxxxxxxxxxxxxx<br>
       xxxxxxxxxxxxxxxxx<br><br>");
      */

        $this->computeRanking($studentsData);


                  file_put_contents(
    'debug-rprt-cntrlr-stdntData.log',
    "<pre>"
    . "\n>=== >student data computeRanking<br> ===\n".
    print_r($studentsData, true) 
     . "xxxxxxxxxxxxxxxx \n
       xxxxxxxxxxxxxxxxx \n
       xxxxxxxxxxxxxxxxx \n\n"
   . "</pre>" ,
   FILE_APPEND
);

        /* var_dump ("<pre>",   ">student data computeRanking<br>",   reset($studentsData) ,
       "</pre>",
       "xxxxxxxxxxxxxxxx<br>
       xxxxxxxxxxxxxxxxx<br>
       xxxxxxxxxxxxxxxxx<br><br>");
       */

        $this->computeSubjectPositions($studentsData);


                file_put_contents(
    'debug-rprt-cntrlr-stdntData.log',
    "<pre>"
    . "\n>=== >student data computeSubjectPositions<br> ===\n".
    print_r($studentsData, true) 
     . "xxxxxxxxxxxxxxxx \n
       xxxxxxxxxxxxxxxxx \n
       xxxxxxxxxxxxxxxxx \n\n"
   . "</pre>" ,
   FILE_APPEND
);

        /* var_dump ("<pre>",   ">student data computeSubjectPositions<br>",   reset($studentsData) ,
       "</pre>",
       "xxxxxxxxxxxxxxxx<br>
       xxxxxxxxxxxxxxxxx<br>
       xxxxxxxxxxxxxxxxx<br><br>");
       */

        $this->applyGrades($studentsData);
        
                   file_put_contents(
    'debug-rprt-cntrlr-stdntData.log',
    "<pre>"
    . "\n>=== >student data applyGrades<br> ===\n".
    print_r($studentsData, true) 
     . "xxxxxxxxxxxxxxxx \n
       xxxxxxxxxxxxxxxxx \n
       xxxxxxxxxxxxxxxxx \n\n"
   . "</pre>" ,
   FILE_APPEND
);
        
        /* var_dump ("<pre>",  ">student data applyGrades<br>",  reset($studentsData) ,
       "</pre>",
       "xxxxxxxxxxxxxxxx<br>
       xxxxxxxxxxxxxxxxx<br>
       xxxxxxxxxxxxxxxxx<br><br>");
       */

        $this->applyRemarks($studentsData);
        
              
                   file_put_contents(
    'debug-rprt-cntrlr-stdntData.log',
    "<pre>"
    . "\n>=== >student data applyRemarks<br> ===\n".
    print_r($studentsData, true) 
     . "xxxxxxxxxxxxxxxx \n
       xxxxxxxxxxxxxxxxx \n
       xxxxxxxxxxxxxxxxx \n\n"
   . "</pre>" ,
   FILE_APPEND
);
        
        /* var_dump ("<pre>",">student data applyRemarks<br>", reset($studentsData) ,
       "</pre>",
       "xxxxxxxxxxxxxxxx<br>
       xxxxxxxxxxxxxxxxx<br>
       xxxxxxxxxxxxxxxxx<br><br>");
       */
        
        
  return $studentsData;
 }
    
    
    
    
}
   
    
    
    
    
    
    





















