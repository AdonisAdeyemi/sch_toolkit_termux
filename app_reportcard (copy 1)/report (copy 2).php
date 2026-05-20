<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">


<style>
body {
    font-family: "Times New Roman", serif;
    font-size: 13px;
    background: #f5f5f5;
    padding: 20px;
}

.report-card {
    width: 800px;
    margin: 0 auto 30px auto;
    background: #fff;
    border: 2px solid #000;
    padding: 20px;
    page-break-after: always;
}

/* HEADER */
.header {
    text-align: center;
    border-bottom: 2px solid #000;
    padding-bottom: 10px;
    margin-bottom: 15px;
}

.school-name {
    font-size: 20px;
    font-weight: bold;
    text-transform: uppercase;
}

.sub-info {
    font-size: 12px;
    margin-top: 5px;
}

/* STUDENT INFO BOX */
.student-info {
    border: 1px solid #000;
    padding: 10px;
    margin-bottom: 15px;
}

.student-info table {
    width: 100%;
}

.student-info td {
    padding: 5px;
}

/* SUBJECT TABLE */
.subject-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
}

.subject-table th,
.subject-table td {
    border: 1px solid #000;
    padding: 6px;
    text-align: center;
}

.subject-table th {
    background: #e0e0e0;
    font-weight: bold;
}

/* SUMMARY */
.summary {
    margin-top: 15px;
    border: 1px solid #000;
    padding: 10px;
}

.summary table {
    width: 100%;
}

.summary td {
    padding: 5px;
}

/* REMARKS */
.remarks {
    margin-top: 10px;
    border-top: 1px dashed #000;
    padding-top: 10px;
    font-weight: bold;
}

/* FOOTER */
.footer {
    margin-top: 20px;
    text-align: right;
    font-size: 11px;
}

//xxxxxxxxxxxxxx

.attendance-box{
    margin-top:15px;
    border:1px solid #000;
    padding:10px;
}

.attendance-title{
    font-weight:bold;
    margin-bottom:8px;
    text-align:center;
    background:#e0e0e0;
    padding:5px;
}

.attendance-table{
    width:100%;
    border-collapse:collapse;
}

.attendance-table td{
    border:1px solid #000;
    padding:6px;
}

.attendance-table td:last-child{
    width:80px;
    text-align:center;
    font-weight:bold;
}

</style>



</head>
<body>


<?php



// setup
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
    
  //  require_once __DIR__ . '/core/lib/dompdf/autoload.inc.php';



// start or resume session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}





//  Define constants
define('PROJECT_ROOT', dirname(__DIR__));
define('APP_PATH', __DIR__);
define('SRC_PATH', APP_PATH . '/src');
define('VIEW_PATH', SRC_PATH . '/Views');
define('LIB_PATH',  PROJECT_ROOT ."/core/lib");
/*
define('ROUTES_PATH', APP_PATH . '/routes');
define('API_PATH', SRC_PATH . '/api');
define('AUTH_PATH', SRC_PATH . '/auth');
*/

// 2️⃣ Autoload (composer)
require_once PROJECT_ROOT . '/vendor/autoload.php';


require_once PROJECT_ROOT.'/core/lib/helper_functions.php';
require_once LIB_PATH .'/lib_db.php';




//require_once APP_PATH . '/config/config_db.php'; /* your PDO setup file + .env */



// temporary for quick iteration : bypassing frontCntrlr 4 now --> config : env/array/connection >>> 
require_once PROJECT_ROOT."/core/config/env.php";
require_once PROJECT_ROOT . '/core/config/config.php';
require_once PROJECT_ROOT . '/core/database/connection.php';

$appName = "reportcard";
//use appName to access related folder (for .env)
$app_folder = "app_" . $appName ;
Env::load(PROJECT_ROOT . "/{$app_folder}/");

$config = Config::make();
$pdo = Connection::make($config['db']);




//make pdo - done in /config/config_db.php
//xxx

report_error(true) ; //dependent on helper_functions.php



// include report_class.php
require_once "report_builder_class.php";

$builder = new ReportBuilder($pdo);
$class_id = 4 ;
$period_id = 1;


$stmt = $pdo->query("SELECT class_name FROM report_classes WHERE id = $class_id");
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

//var_dump("class_name db", $result);

$class_name = $result[0]['class_name'];
 
$stmt = $pdo->query("SELECT session, term FROM report_academic_periods WHERE id = 1");
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
$session = $result[0]['session'];
$term =  $result[0]['term'];

$report = $builder->build($class_id , $period_id );

$stmt = $pdo->query("SELECT * FROM report_card_settings WHERE school_id = 1");
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
$settings = $result[0];
//var_dump ("settings", $settings) ;


echo "<br><br>yyyxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx<br>";

?>


<?php foreach ($report as $student): ?>


<div class="report-card">


<!-- xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx -->
GET AI TO MARK ALL SECTION NAMES <br>
USE MMLC DESIGN TO DO TEMPLATE DESIGN WITH AI <br>
GIVE AI THE TWO - DATA html + STRUCTURE html : LET AI SWAP IT (& label it)
    <div class="header">
        <div class="school-name"><?= $settings['printed_name'] ?></div>
        <div class="sub-info">
            <?= $settings['address'] ?><br>
            <?= $class_name ?> | <?= $session ?> | Term <?= $term ?>
        </div>
    </div>

<!-- xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx -->
    <div class="student-info">
        <table>
            <tr>
                <td><strong>Name:</strong> <?= $student['name'] ?></td>
                <td><strong>Position:</strong> <?= $student['position_text'] ?></td>
            </tr>
            <tr>
                <td><strong>Total:</strong> <?= $student['all_subjects_total'] ?></td>
                <td><strong>Average:</strong> <?= $student['average'] ?></td>
            </tr>
        </table>
    </div>
    
 <!-- xxxxxxxxxxxxxxxxxxxxxxxxxxxxxx -->   
    
 <!-- ATTENDANCE SECTION -->
<div class="attendance-box">

    <div class="attendance-title">
        ATTENDANCE RECORD
    </div>

    <table class="attendance-table">
        <tr>
            <td><strong>Days School Opened</strong></td>
            <td><?= $student['days_open'] ?? 0 ?></td>
        </tr>

        <tr>
            <td><strong>Days Present</strong></td>
            <td><?= $student['days_present'] ?? 0 ?></td>
        </tr>

        <tr>
            <td><strong>Days Absent</strong></td>
            <td><?= $student['days_absent'] ?? 0 ?></td>
        </tr>
    </table>

</div>

    
<!-- xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx -->
    
    <!-- SUBJECT TABLE -->
    <table class="subject-table">
        <thead>
            <tr>
                <th>Subject</th>
                <th>CA1</th>
                <th>CA2</th>
                <th>Exam</th>
                <th>Total</th>
                <th>Grade</th>                     
                <th>Remark</th>
                <th>Pos</th>
            </tr>
        </thead>
        <tbody>

        <?php foreach ($student['subjects'] as $sub): ?>
            <tr>
                <td style="text-align:left"><?= $sub['name'] ?></td>
                <td><?= $sub['ca1'] ?? 0 ?></td>
                <td><?= $sub['ca2'] ?? 0 ?></td>
                <td><?= $sub['exam'] ?? 0 ?></td>
                <td><?= $sub['one_subject_total'] ?? 0 ?></td>
                <td><?= $sub['grade']  ?></td>
                <td><?=  $sub['grade_remark'] ?></td>
                <td><?=  $sub['position'] ?></td>
            </tr>
        <?php endforeach; ?>

        </tbody>
    </table>









    <!-- xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx -->
    <!-- SUMMARY -->
    <div class="summary">
        <table>
            <tr>
                <td><strong>Redundant Total Score:</strong> <?= $student['all_subjects_total'] ?></td>
                <td><strong>Redundant Average:</strong> <?= $student['average'] ?></td>
           </tr>
                
              <tr>
                <td><strong>Class Teacher's Comment:</strong> <?= $student['teacher_exam_comment'] ?></td>
                </tr>
                <tr>
                <td><strong>Principal's Comment:</strong> <?= $student['principal_exam_comment'] ?></td>
            </tr>
        </table>
    </div>


    <div class="remarks">
        Remark: <?= $student['remark'] ?>
    </div>

    <div class="footer">
        Generated on <?= date('Y-m-d') ?>
    </div>

</div>

</div>

<?php endforeach; ?>

</body>
</html>




















