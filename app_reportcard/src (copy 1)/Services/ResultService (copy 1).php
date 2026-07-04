<?php


class ResultService
{
    public function calculate($ca1, $ca2, $exam)
    {
        $total = (int)$ca1 + (int)$ca2 + (int)$exam;

        return [
            'total' => $total,
            'grade' => $this->grade($total),
            'remark' => $this->remark($total)
        ];
    }

    private function grade($total)
    {
        return match (true) {
            $total >= 70 => 'A',
            $total >= 60 => 'B',
            $total >= 50 => 'C',
            $total >= 45 => 'D',
            $total >= 40 => 'E',
            default => 'F'
        };
    }

    private function remark($total)
    {
        return match (true) {
            $total >= 70 => 'Excellent',
            $total >= 60 => 'Very Good',
            $total >= 50 => 'Good',
            $total >= 45 => 'Fair',
            $total >= 40 => 'Pass',
            default => 'Fail'
        };
    }
}



?>
