<?php
namespace ReportCard\Services;


class ResultService
{
    /**
     * Validate + calculate result
     * CA1 = 10, CA2 = 20, EXAM = 70
     */
    public function calculate(?int $ca1, ?int $ca2, ?int $exam): array
    {
    
$resultIsComplete = true;
if($ca1 == -1)
{
$resultIsComplete = false;
$ca1 = 0;
}
if($ca2 == -1)
{
$resultIsComplete = false;
$ca2 = 0;
}
if($exam == -1)
{
$resultIsComplete = false;
$exam = 0;
}





        $ca1 = (int)$ca1;
        $ca2 = (int)$ca2;
        $exam = (int)$exam;

        // -----------------------
        // VALIDATION
        // -----------------------
        $this->validate($ca1, $ca2, $exam);

        // -----------------------
        // CALCULATION
        // -----------------------
        $total = $ca1 + $ca2 + $exam;

        return [
            'ca1_score'   => $ca1,
            'ca2_score'   => $ca2,
            'exam_score'  => $exam,
            'total_score' => $total,
            'grade'       => $resultIsComplete ?  $this->grade($total) : "-",
            'remark'      => $resultIsComplete ? $this->remark($total) : "-"
        ];
    }

    /**
     * Validation rules
     */
    private function validate(int $ca1, int $ca2, int $exam): void
    {
        if ($ca1 < 0 || $ca1 > 10) {
            throw new Exception("CA1 must be 0–10");
        }

        if ($ca2 < 0 || $ca2 > 20) {
            throw new Exception("CA2 must be 0–20");
        }

        if ($exam < 0 || $exam > 70) {
            throw new Exception("Exam must be 0–70");
        }
    }

    /**
     * Grade logic
     */
    public function grade(int $total): string
    {
        return match (true) {
            $total >= 70 => 'A',
            $total >= 60 => 'B',
            $total >= 50 => 'C',
            $total >= 45 => 'D',
            $total >= 40 => 'E',
            default      => 'F',
        };
    }

    /**
     * Remark logic
     */
    public function remark(int $total): string
    {
        return match (true) {
            $total >= 70 => 'Excellent',
            $total >= 60 => 'Very Good',
            $total >= 50 => 'Good',
            $total >= 45 => 'Fair',
            $total >= 40 => 'Pass',
            default      => 'Fail',
        };
    }

    /**
     * Prepare full row for DB insert/update
     */
    public function buildPayload(
        int $studentId,
        int $classSubjectId,
        int $periodId,
        int $ca1,
        int $ca2,
        int $exam
    ): array {
        $calc = $this->calculate($ca1, $ca2, $exam);

        return [
            'student_id'       => $studentId,
            'class_subject_id' => $classSubjectId,
            'period_id'        => $periodId,
            'ca1_score'        => $calc['ca1_score'],
            'ca2_score'        => $calc['ca2_score'],
            'exam_score'       => $calc['exam_score'],
            'total_score'      => $calc['total_score'],
            'grade'            => $calc['grade'],
            'remark'           => $calc['remark'],
        ];
    }
}







