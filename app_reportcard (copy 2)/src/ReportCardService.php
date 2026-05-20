<?php

class ReportCardService
{
/*
require_once __DIR__ . '/Data/sample_data.php';
*/
    public function getStudents()
    {
        return require __DIR__ . '/Data/sample_data.php';
    }



    /*
    |--------------------------------------------------------------------------
    | Render All Students
    |--------------------------------------------------------------------------
    */

    public function renderAll($students)
    {
        $html = '';

        foreach ($students as $student) {

            $html .= $this->renderStudent($student);

        }

        return $html;
    }






    /*
    |--------------------------------------------------------------------------
    | Render Single Student
    |--------------------------------------------------------------------------
    */

    public function renderStudent($student)
    {

        /*
        |--------------------------------------------------------------------------
        | Make $student available inside template
        |--------------------------------------------------------------------------
        */

        ob_start();

        include __DIR__ . '/../templates/report_card.php';

        return ob_get_clean();

    }

}
