
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












</style>
</head>
<body>

<?=

foreach ($report as $student) {
    echo renderReportCard($student);
}

?>

</body>
</html>




















