<?php
//reportcard index
   
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


// 1. PATHS
define('PROJECT_ROOT', dirname(dirname(__DIR__)));
define('APP_PATH', PROJECT_ROOT . '/app_reportcard');
define('SRC_PATH', APP_PATH . '/src');
define('VIEW_PATH', SRC_PATH . '/View');

// 2. AUTOLOAD
require_once PROJECT_ROOT . '/vendor/autoload.php';

// 3. CORE FILES
require_once SRC_PATH . '/Core/ReportBuilder.php';

// 4. DB CONNECTION (your existing system)
require_once PROJECT_ROOT . '/core/config/env.php';
require_once PROJECT_ROOT . '/core/config/config.php';
require_once PROJECT_ROOT . '/core/database/connection.php';


Env::load(APP_PATH);
$config = Config::make();
$pdo = Connection::make($config['db']);


//xxxxxxxxxxxxxxx

//use ReportCard\Controller\ReportController;







// 5. CONTROLLER
//require_once SRC_PATH . '/Controller/ReportController.php';

use ReportCard\Controller\ReportController;


//require_once SRC_PATH . '/Model/StudentModel.php';



// 6. RUN CONTROLLER
$controller = new ReportController();

// TEST INPUTS
$class_id = 4;
$period_id = 1;

// CALL
$controller->index($pdo, $class_id, $period_id);





?>









