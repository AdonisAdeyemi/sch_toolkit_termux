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

        $students = $this->structureData($rows);

        $this->computeTotals($students);

        $this->computeRanking($students);

        $this->computeSubjectPositions($students);

        $this->applyGrades($students);

        $this->applyRemarks($students);

        return array_values($students);
    }

    // ---------------- FETCH ----------------
    private function fetchData($class_id, $period_id)
    {
        $sql = "/* your full_report_query */";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$period_id, $class_id]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ---------------- STRUCTURE ----------------
    private function structureData($rows)
    {
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
                    'total' => 0,
                    'average' => 0,
                    'position' => 0,
                    'position_text' => '',
                    'remark' => ''
                ];
            }

            if (!isset($students[$sid]['subjects'][$subId])) {
                $students[$sid]['subjects'][$subId] = [
                    'subject_id' => $subId,
                    'name' => $row['alias_name'] ?: $row['subject_name'],
                    'order' => $row['subject_order'],
                    'ca1' => 0,
                    'ca2' => 0,
                    'exam' => 0,
                    'total' => 0,
                    'grade' => '',
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

        return $students;
    }

    // ---------------- TOTALS ----------------
    private function computeTotals(&$students)
    {
        foreach ($students as &$student) {

            $total = 0;
            $count = 0;

            foreach ($student['subjects'] as &$sub) {

                $sub['total'] = $sub['ca1'] + $sub['ca2'] + $sub['exam'];

                $total += $sub['total'];
                $count++;
            }

            $student['total'] = $total;
            $student['average'] = $count ? round($total / $count, 2) : 0;

            // sort subjects once
            usort($student['subjects'], fn($a, $b) => $a['order'] <=> $b['order']);
        }
    }

    // ---------------- OVERALL RANKING ----------------
    private function computeRanking(&$students)
    {
        $list = array_values($students);

        usort($list, fn($a, $b) => $b['total'] <=> $a['total']);

        $rank = 1;
        $prev = null;
        $same = 0;

        foreach ($list as &$s) {

            if ($s['total'] === $prev) {
                $s['position'] = $rank;
                $same++;
            } else {
                $rank += $same;
                $same = 1;
                $s['position'] = $rank;
            }

            $s['position_text'] = $this->ordinal($s['position']);
            $prev = $s['total'];
        }

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
        $subjectBuckets = [];

        // group scores per subject
        foreach ($students as $student) {
            foreach ($student['subjects'] as $sub) {
                $subjectBuckets[$sub['subject_id']][] = [
                    'student_id' => $student['student_id'],
                    'total' => $sub['total']
                ];
            }
        }

        // rank per subject
        foreach ($subjectBuckets as $subject_id => $list) {

            usort($list, fn($a, $b) => $b['total'] <=> $a['total']);

            $rank = 1;
            $prev = null;
            $same = 0;

            foreach ($list as $i => &$item) {
                if ($item['total'] === $prev) {
                    $item['position'] = $rank;
                    $same++;
                } else {
                    $rank += $same;
                    $same = 1;
                    $item['position'] = $rank;
                }
                $prev = $item['total'];
            }

            // assign back
            foreach ($list as $item) {
                $students[$item['student_id']]['subjects'][$subject_id]['position'] = $item['position'];
            }
        }
    }

    // ---------------- GRADING ----------------
    private function applyGrades(&$students)
    {
        foreach ($students as &$student) {
            foreach ($student['subjects'] as &$sub) {
                $sub['grade'] = $this->grade($sub['total']);
            }
        }
    }

    private function grade($score)
    {
        if ($score >= 70) return 'A';
        if ($score >= 60) return 'B';
        if ($score >= 50) return 'C';
        if ($score >= 45) return 'D';
        if ($score >= 40) return 'E';
        return 'F';
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
