
<?php
ob_start();
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
    padding:2px;
}


*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

p, h1, h2, h3, table, td, tr, div{
    margin:0;
    padding:0;
}


/*
.watermark{
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;

    font-size: 28px;
    font-weight: bold;
    color: rgba(0,0,0,0.06);

    transform: rotate(-35deg);

    background-image: repeating-linear-gradient(
        45deg,
        transparent,
        transparent 80px,
        rgba(0,0,0,0.04) 80px,
        rgba(0,0,0,0.04) 160px
    );

    pointer-events: none;
    z-index: 0;
}
*/


.report-card{
    position: relative;
    z-index: 1;
    overflow: hidden;
}

/* WATERMARK LAYER */
.watermark{
    position:absolute;
    top:0;
    left:0;
    width:400%;
    height:300%;

    z-index:0;
    pointer-events:none;

    transform:rotate(-30deg);

    display:flex;
    flex-wrap:wrap;

    gap:40px;

    opacity:0.1;
}


.wm-tile{
    display:flex;
    align-items:center;
    gap:8px;

    font-size:30px;
    font-weight:bold;
    color:#000;
}

.wm-tile img{
    width: 70px;
    height: 70px;
}


.report-card {
    width : 100%;
   /* max-width: 800px; */
    margin: 0 auto 30px auto;
    background: #fff;
    border: 2px solid #000;
    padding: 2px;
    page-break-after: always;
   
    position: relative;
    z-index: 1;
overflow: hidden;
}

/* HEADER */


/*
.header{
    width:100%;
    margin-bottom:10px;
    overflow:hidden; 
}
*/

.header{
    display:table;
    width:100%;
    
    margin-bottom:1px;
    overflow:hidden; 
    
        border: 1px solid green;
}


.logo-box,
.school-info,
.passport-box{
    display:table-cell;
    vertical-align:top;
}


.logo-box{
  float:left; 
    width:90px;
height:80px;
     /*   background-color : green ; 
     overflow:hidden;
     */
         border:5px solid #999;
line-height:0;
}

.passport-box{
    float:right; 
    width:90px;
height:80px;
    text-align:right;
   /* background-color : pink ; */
   overflow:hidden;
    
       border:5px solid #999;
       line-height:0; 
}

/*
.logo-box,
.school-info,
.passport-box{
    vertical-align: top;
}
*/

.school-info{
    margin-left:100px;
    margin-right:100px;
    text-align:center;
        border: blue solid 5px;
}

.school-logo{
    width:80px;
    height:80px;
    border:none;
display:block;
line-height:0;
}

.student-passport{
    width:80px;
    height:80px;
    border:1px solid #000;
display:block;
line-height:0;
}

.school-name{
    font-size:24px;
    font-weight:bold;
        text-transform: uppercase;
        border:1px solid #999;
}

.sub-info{
    font-size:12px;
    line-height:0px;
    border:1px solid #999;
}

.placeholder{
    width:80px;
    height:80px;
    border:1px solid #999;
}



/* HEADER */
/*
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
*/

/* STUDENT INFO BOX */
.student-info { 
        border: 1px solid green;
    padding: 2px;
    margin-bottom: 0px;
        margin-top:0px;
}

.student-info table {
    width: 100%;
}

.student-info td {
    padding: 2px;
}

/* SUBJECT TABLE */
.subject-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 0px;
}

.subject-table th,
.subject-table td {
    border: 1px solid #000;
    padding: 2px;
    text-align: center;
}

.subject-table th {
    background: #e0e0e0;
    font-weight: bold;
}

/* SUMMARY */
.comments {
    margin-top: 0px;
    /* border: 1px solid #000; original border */
    padding: 2px;
    
    border: 1px solid green;
    
}

.comments table {
    width: 100%;
}

 .comments td{
    padding: 2px;
}

/* REMARKS */
.remarks {
    margin-top: 0px;
    border-bottom: 1px dashed #000;
    padding-top: 2px;
    font-weight: bold;
}

/* FOOTER */
.footer {
    margin-top: 0px;
    text-align: right;
    font-size: 11px;
    
    border: 1px solid green;
}

/* xxxxxxxxxxxxxx */

.attendance-box{
    margin-top:0px;
   /* border:1px solid #000; original border */
    padding:2px;
    
   border: 1px solid green;
}

.attendance-title{
    font-weight:bold;
    margin-bottom:0px;
    text-align:center;
    background:#e0e0e0;
    padding:2px;
}

.attendance-table{
    width:100%;
    border-collapse:collapse;
}

.attendance-table td{
    border:1px solid #000;
    padding:2px;
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
    padding:2px;
}


.summary-box,
.domain-box,
.legend-box{
    margin-top:0px;
    
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
    padding:2px;
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
    margin-top:0px;
    
    background-color : blue;
    
    
   
    display:table; /* from alignment to top sucess in header setting */
    overflow:hidden; 
}

    
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
   /* vertical-align:top; */
    margin:0;
    padding:0;
    border : 5px solid red;

    background-color : pink;
    
    
    display:table-cell; /* from alignment to top sucess in header setting */
    vertical-align:top;
}
    /*
.domain-column + .domain-column{
     margin-left:3%; 
}
*/


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




















