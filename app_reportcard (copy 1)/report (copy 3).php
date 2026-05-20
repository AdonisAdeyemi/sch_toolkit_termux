<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">


<style>
body {
    font-family: "Times New Roman", serif;
    font-size: 13px;
    background: #f5f5f5; /* remove for pdf */
    margin:0;
    padding:10px;
}


*{
    box-sizing:border-box;
}

.report-card {
    width : 100%;
   /* max-width: 800px; */
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
.comments {
    margin-top: 15px;
    border: 1px solid #000;
    padding: 10px;
}

.comments table {
    width: 100%;
}

 .comments td{
    padding: 5px;
}

/* REMARKS */
.remarks {
    margin-top: 10px;
    border-bottom: 1px dashed #000;
    padding-top: 10px;
    font-weight: bold;
}

/* FOOTER */
.footer {
    margin-top: 20px;
    text-align: right;
    font-size: 11px;
}

/* xxxxxxxxxxxxxx */

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


/* xxxxxxxxxxx */

.section-title{
    background:#dcdcdc;
    font-weight:bold;
    text-align:center;
   
    border:1px solid #000;
    
    font-size:12px;
    padding:4px;
}


.summary-box,
.domain-box,
.legend-box{
    margin-top:15px;
    
    background-color : green;
}

.summary-table,
.legend-table{
    width:100%;
    border-collapse:collapse;
}

.summary-table td,
.legend-table td
{
    border:1px solid #000;
    padding:6px;
    text-align:center;
}

/* xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx */
/* DOMAIN */

.domain-table td:first-child,
.legend-table td:last-child{
    text-align:left;
}


.domains-row{
    width:100%;
    margin-top:10px;
    
    background-color : blue;
}


.domains-row::after{
    content:"";
    display:block;
    clear:both;
}


.domain-column:first-child{
    float:left;
}

.domain-column:last-child{
    float:right;
}


    .domain-column{
    width:48.5%;
    float:left;
    vertical-align:top;
    margin:0;
    padding:0;
    border : 5px solid red;

    background-color : pink;
}
    




/* compress table */

.domain-table{
    width:100%;
    border-collapse:collapse;
    /* table-layout:fixed; */
   /* font-size:10px; */
    /* word-wrap:break-word; */ /*may need later if content start clipping */
}

.domain-table th,
.domain-table td{
    border:1px solid #000;
    padding:1px 1px;
    text-align:center;
    line-height:1.1;
}


.domain-table td:first-child{
    text-align:left;
    width:55%; 
white-space:normal;

}

.domain-table th{
    background:#e0e0e0;
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
    <?php include VIEW_PATH . '/reportcard/sections/header.php'; ?>


    <!-- STUDENT INFO -->
    <?php include VIEW_PATH . '/reportcard/sections/student_info.php'; ?>


    <!-- ATTENDANCE -->
    <?php include VIEW_PATH . '/reportcard/sections/attendance.php'; ?>


    <!-- SUBJECT TABLE -->
    <?php include VIEW_PATH . '/reportcard/sections/subject_table.php'; ?>


    <!-- SUMMARY -->
    <?php include VIEW_PATH . '/reportcard/sections/performance_summary.php'; ?>


    <!-- COMMENTS -->
    <?php include VIEW_PATH . '/reportcard/sections/comments.php'; ?>
    
    <div class="domains-row">
    <div class="domain-column">
        <!-- AFFECTIVE -->
    <?php include VIEW_PATH . '/reportcard/sections/affective.php'; ?>
    </div>
    <div class="domain-column">
        <!-- PSYCHOMOTOR -->
    <?php include VIEW_PATH . '/reportcard/sections/psychomotor.php'; ?>
    </div>
</div>

    
<!-- LEGEND -->

<?php include VIEW_PATH . '/reportcard/sections/rating_legend.php'; ?>
     

    <!-- FOOTER -->
    <?php include VIEW_PATH . '/reportcard/sections/footer.php'; ?>

</div>
<?php endforeach; ?>

</body>
</html>




















