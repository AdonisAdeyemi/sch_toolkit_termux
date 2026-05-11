<?php

class ReportBuilder
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function build($class_id, $period_id)
    {
        $rows = $this->fetchData($class_id, $period_id);


      echo "<pre>";
  // var_dump ("in build - report rows", $rows) ;
      echo "</pre>";

        $students = $this->structureData($rows);
       
       
 
     //   //xxx var_dump ("report build students", $students) ;

        $this->computeTotals($students);

        $this->computeRanking($students);

        $this->computeSubjectPositions($students);

        $this->applyGrades($students);

        $this->applyRemarks($students);
        
      echo "<pre>";
      var_dump ("report build final", $students) ;
      echo "</pre>";
                    
echo "<br><br><br>";
     echo "<pre>";
   //  //xxx var_dump ("array value students", array_values($students)) ;
          echo "</pre>";



        return array_values($students);
    }

    // ---------------- FETCH ----------------
    private function fetchData($class_id, $period_id)
    {
    
    require_once "report_sql.php"; //contains string $report_sql

        $sql = $report_sql ; "/* your full_report_query */";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$period_id, $class_id]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ---------------- STRUCTURE ----------------
    private function structureData($rows)
    {
    
    
      echo "<pre>";
  // //xxx var_dump ("report rows in structure data", $rows) ;
      echo "</pre>";   
      
      
        $students = [];

        foreach ($rows as $row) {

            $sid = $row['student_id'];
            $subId = $row['class_subject_id'];
            $examType = $row['exam_type'];

            if (!isset($students[$sid])) {
                $students[$sid] = [
                    'student_id' => $sid,
                    'name' => $row['student_name'],
                    'subjects' => [],
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
            }

            if (!isset($students[$sid]['subjects'][$subId])) {
            
     
      echo "<pre>";
   // //xxx var_dump ("report row_not_set", $row) ;
      echo "</pre>";   
            
            
                $students[$sid]['subjects'][$subId] = [
                    'subject_id' => $subId,
                    'name' =>  $row['alias_name'] ?: $row['subject_name']  ?: $row['base_subject_name'] ,
                    'order' => $row['subject_order'],
                    'ca1' => 0,
                    'ca2' => 0,
                    'exam' => 0,
                    'one_subject_total' => 0,
                    'grade' => '',
                    'grade_remark' => '',
                    'position' => 0
                ];
            }

            $score = (int)($row['score'] ?? 0);

            if ($examType === 'ca1') {
                $students[$sid]['subjects'][$subId]['ca1'] = $score;
            } elseif ($examType === 'ca2') {
                $students[$sid]['subjects'][$subId]['ca2'] = $score;
            } elseif ($examType === 'exam') {
                $students[$sid]['subjects'][$subId]['exam'] = $score;
            }
        }
        
  //students arr fully built - NOW add class size
  foreach ($students as &$s)
  {
    $s['class_size'] = count($students) ;
}
  unset ($s);

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

    // ---------------- OVERALL RANKING ----------------
    private function computeRanking(&$students)
    {
        $list = array_values($students);

        usort($list, fn($a, $b) => $b['all_subjects_total'] <=> $a['all_subjects_total']);

        $rank = 1;
        $prev = null;
        $same = 0;

        foreach ($list as &$s) {

            if ($s['all_subjects_total'] === $prev) {
                $s['position'] = $rank;
                $same++;
            } else {
                $rank += $same;
                $same = 1;
                $s['position'] = $rank;
            }

            $s['position_text'] = $this->ordinal($s['position']);
            $prev = $s['all_subjects_total'];
        }
        unset($s);

        // map back
        $mapped = [];
        foreach ($list as $s) {
            $mapped[$s['student_id']] = $s;
        }

        $students = $mapped;
    }

    // ---------------- SUBJECT POSITIONS ----------------
    private function computeSubjectPositions(&$students)
    {
    
         echo "<pre>";
         /*
    //xxx var_dump 
    ( 
    "students['1']",
    $students["1"],
    
    "students[1]",
    $students[1],
    "xxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"
    );
    */
    //xxx var_dump("> students", $students );
    
         echo "</pre>";
    
        $subjectBuckets = [];

        // group scores per subject
        foreach ($students as $std_index => $student) {
        
    
         echo "<pre>";
    // //xxx var_dump ("computeSubPos - student index",$std_index);
        echo "<pre>";    
        
            foreach ($student['subjects'] as $sbj_index => $sub) {
            
            
         echo "<pre>";
         
    // //xxx var_dump ("computeSubPos - sub index",$sbj_index);
    // //xxx var_dump ("computeSubPos - sub for each",$sub);
        echo "<pre>";
            
            
   $subjectBuckets[$sub['subject_id']][] = [
                    'student_id' => $student['student_id'],
               'student_name' => $student['name'],
               'subject_name' => $sub['name'],
                    'one_subject_total' => $sub['one_subject_total']
                ] ;
                         
   /*                         
 //xxx var_dump(
 "> subject_id",
 $sub['subject_id'],
 "> student_id",
 $student['student_id'],
 "  ",
 
         );
   */
                
            }
        }
   echo "xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx<br>";
   /*
    //xxx var_dump 
    (
    "subjectBuckets",
    $subjectBuckets
    );
    */

        // rank per subject
        foreach ($subjectBuckets as $subject_id => $list) {

            usort($list, fn($a, $b) => $b['one_subject_total'] <=> $a['one_subject_total']);

            $rank = 1;
            $prev = null;
            $same = 0;

            foreach ($list as $i => &$item) {
                if ($item['one_subject_total'] === $prev) {
                    $item['position'] = $rank;
                    $same++;
                } else {
                    $rank += $same;
                    $same = 1;
                    $item['position'] = $rank;
                }
                $prev = $item['one_subject_total'];
            }
            unset ($item);
        
        /*    
            var_dump
            (
            "> subject_id",
            $subject_id,
            "> list of cards",
            $list
            );
	*/
             
            // assign back
            foreach ($list as $item) {
           // var_dump (">> item in ", $item );
      
      
$student_id = $item['student_id'];      
      
      /*
      if(
    !isset($students["$student_id"]['subjects']["$subject_id"])
    ) echo "error : null or no array key <br>"  ;
     
     
  if ( !array_key_exists( "$subject_id" , $students["$student_id"]['subjects'])) 
  {
   echo "error : subject_id $subject_id not found in student subjects list <br>"  ;
  
   
      //xxx var_dump("item in ",$item) ;
         
   
   }
     
    if(!is_array($students["$student_id"]['subjects']["$subject_id"]))  echo "error : subject_id not an array <br>"     ;
    
  
  if ( !array_key_exists('position', $students["$student_id"]['subjects']["$subject_id"])
      )   echo "error : no position key <br>"      ;
      
      
      {
      //xxx var_dump("list's subject_id ",$subject_id) ;
   
      //xxx var_dump("item in ",$item) ;
         
      
      //xxx var_dump("no [position] : key error ", $subject_id, $students[$item['student_id']]['subjects'] );
       continue ;
       }
       */
       
       //xxx var_dump("yes [position] : ", $students[$student_id]['subjects'][$subject_id] );
      
      
  // var_dump("> item['position']", $item['position'] );
      
                $students[$student_id]['subjects'][$subject_id]['position'] = $item['position'];
            }
        }
    }

    // ---------------- GRADING ----------------
    private function applyGrades(&$students)
    {
    
    
        foreach ($students as $std_index => &$student) {
        
        
         echo "<pre>";
    // //xxx var_dump ("ApplyGrade - student index",$std_index);
        echo "<pre>";
        
            foreach ($student['subjects'] as $sbj_index => &$sub) {
            
         echo "<pre>";
         
   //  //xxx var_dump ("applyGrade - sub index",$sbj_index);
    // //xxx var_dump ("applyGrade - sub for each",$sub);
        echo "<pre>";
            
    $gradeData = $this->grade($sub['one_subject_total']);

$sub['grade'] = $gradeData['grade'];
$sub['grade_remark'] = $gradeData['remark'];

/*        
     $sub['grade'] = $this->grade($sub['one_subject_total'] );
     
     $sub['grade_remark'] = $this->grade($sub['one_subject_total'] );
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
            $avg = $student['average'];
            
            if ($avg >= 75) {
                $student['remark'] = 'Excellent performance';
            } elseif ($avg >= 65) {
                $student['remark'] = 'Very good performance';
            } elseif ($avg >= 50) {
                $student['remark'] = 'Good effort';
            } elseif ($avg >= 40) {
                $student['remark'] = 'Needs improvement';
            } else {
                $student['remark'] = 'Poor performance';
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
}



















