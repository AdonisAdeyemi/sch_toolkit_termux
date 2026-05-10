<?php
namespace ReportCard\Controller;

use ReportCard\Model\StudentModel;

class ReportController
{
    public function index($pdo, $class_id, $period_id)
    {
   // require_once SRC_PATH . '/Model/StudentModel.php';

        $students = (new StudentModel())->getReportData($pdo, $class_id, $period_id);

        $class_name = MetaModel::getClassName($pdo, $class_id);
        $period = MetaModel::getPeriod($pdo, $period_id);
        $settings = MetaModel::getSettings($pdo);


//temporary
$GLOBALS['student'] = $student;
$GLOBALS['settings'] = $settings;
$GLOBALS['class_name'] = $class_name;
$GLOBALS['period'] = $period;

/* for ideal
$student = [
   'name' => 'John',
   'subjects' => [...],
   'meta' => [
      'settings' => $settings,
      'class_name' => $class_name,
      'period' => $period
   ]
];

xxxxxxxxxxxxxxx

$settings = $student['meta']['settings'];
$class_name = $student['meta']['class_name'];
$period = $student['meta']['period'];


*/


        require VIEW_PATH . "/reportcard/preview.php";
    }
}



?>
