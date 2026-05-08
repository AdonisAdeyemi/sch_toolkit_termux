<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>

body {
    font-family: Arial, sans-serif;
    font-size: 12px;
}

.report-card {
    width: 100%;
    margin-bottom: 20px;
    page-break-after: always;
}

.header {
    text-align: center;
    margin-bottom: 10px;
}

.school-name {
    font-size: 18px;
    font-weight: bold;
}

.sub-info {
    font-size: 12px;
}

.student-info {
    margin: 10px 0;
}

.student-info table {
    width: 100%;
}

.student-info td {
    padding: 4px;
}

.subject-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
}

.subject-table th,
.subject-table td {
    border: 1px solid #000;
    padding: 5px;
    text-align: center;
}

.subject-table th {
    background-color: #eee;
}

.summary {
    margin-top: 10px;
}

.summary table {
    width: 100%;
}

.summary td {
    padding: 5px;
}

.remarks {
    margin-top: 10px;
}

.footer {
    margin-top: 20px;
    text-align: right;
    font-size: 11px;
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

    <!-- HEADER -->
    <div class="header">
        <div class="school-name"><?= $settings['printed_name'] ?></div>
        <div class="sub-info">
            <?= $settings['address'] ?><br>
            <?= $class_name ?> | <?= $session ?> | Term <?= $term ?>
        </div>
    </div>

    <!-- STUDENT INFO -->
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
                <td><?=  $sub['position'] ?></td>
            </tr>
        <?php endforeach; ?>

        </tbody>
    </table>

    <!-- SUMMARY -->
    <div class="summary">
        <table>
            <tr>
                <td><strong>Redundant Total Score:</strong> <?= $student['all_subjects_total'] ?></td>
                <td><strong>Redundant Average:</strong> <?= $student['average'] ?></td>
            </tr>
        </table>
    </div>

    <!-- REMARKS -->
    <div class="remarks">
        <strong>Remark:</strong> <?= $student['remark'] ?>
    </div>

    <!-- FOOTER -->
    <div class="footer">
        Generated on <?= date('Y-m-d') ?>
    </div>

</div>

<?php endforeach; ?>

</body>
</html>




















