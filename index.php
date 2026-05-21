<?php
//script name - qpicker index.php
    
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
    
    require_once __DIR__ . '/core/lib/dompdf/autoload.inc.php'; //future AB , hi :) >>> refactor : is this suposed to be with PROJECT_ROOT's composer?

require_once __DIR__ . '/core/lib/helper_functions.php';
report_error(true) ;


// start or resume session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

//var_dump($_SESSION); //it is breaker json parser


$request = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];



//get appName
// Determine which app from the URL
$uriForAppName = trim($request, '/');  
$parts = explode('/', $uriForAppName); // First part is the app name
$appName = $parts[0] ;

//exiting if dir notFound
$appPath = __DIR__ . "/app_{$appName}";

/*
 var_dump ("request b4 appName replace:", $request);
echo "<br>";
echo "appName : ".$appName ;
echo "<br>";
*/


if (!is_dir($appPath)) {
    http_response_code(404);
    exit("App '{$appName}' not foundd.");
}


$_SESSION['appName'] = $appName;
//echo "session_appname : " . $_SESSION['appName'] ;
//echo "<br><br>";

//  removing appNmae from request - myb get php helper function to be separating and returning both AppName & requestStripedOfAppName
if (strpos($request, "/{$appName}") === 0)
{
 //  echo "appname is at start <br><br>";
 $request = str_replace("/{$appName}", '', $request);
}

/*    
 var_dump ("request after appName replace:", $request);
echo "<br><br>";
*/



//cleaning ending slash
if ($request !== '/' && str_ends_with($request, '/')) {
    $request = rtrim($request, '/');
}



// 3️⃣ Define constants (dependent on $appName)
// refactor : since src is autoloaded, why do I need SRC_PATH VIEW_PATH (am I wrong?)
define('PROJECT_ROOT', __DIR__);
define('APP_PATH', "{$appPath}");
define('SRC_PATH', APP_PATH . '/src');
define('VIEW_PATH', SRC_PATH . '/Views');
define('ROUTES_PATH', APP_PATH . '/routes');
define('API_PATH', SRC_PATH . '/api'); //refactor : this seems unsed
define('AUTH_PATH', SRC_PATH . '/auth'); //refactor : this seems unsed
define('LIB_PATH', PROJECT_ROOT ."/core/lib");

// 2️⃣ Autoload (composer)
require_once PROJECT_ROOT.'/vendor/autoload.php';

/* db pdo */
// 4️⃣ Include connection + helpers (if any)
/* interesting  : __DIR__.'/core/lib/lib_db.php';
is somehow not same 
'/core/lib/lib_db.php';
-> myb cos of schoolkit domain? myb not? do research 
*/
 
require_once LIB_PATH .'/lib_db.php';
// require_once APP_PATH . '/config/config_db.php'; /* your PDO setup file + .env */



// refactored config (1env getter, 2 array maker, 3 array user 4 pdo
require_once PROJECT_ROOT."/core/config/env.php";
require_once PROJECT_ROOT . '/core/config/config.php';
require_once PROJECT_ROOT . '/core/database/connection.php';

//use appName to access related folder (for .env)
$app_folder = "app_" . $appName ;
//Env::load(PROJECT_ROOT . "/{$app_folder}/");
Env::load(PROJECT_ROOT );

$config = Config::make();
$pdo = Connection::make($config['db']);

require_once LIB_PATH . '/Router.php';

/*
these seems to belong to relevant routes

// 3️⃣b Import (use) the controllers you’ll route to
use App\Controllers\UserController;
use App\Controllers\DashboardController;
use App\Controllers\CompilationController;
use App\Controllers\QuestionController;
use App\Controllers\AuthController;


use App\Models\AdminCompilationModel;
use App\Models\User;
use App\Controllers\AdminCompilationController;
*/

$isLocal = in_array($_SERVER['HTTP_HOST'], [
    'localhost:8080',
    '127.0.0.1:8080'
]);

if (!$isLocal && (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === 'off')) {
    $redirect = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    header('Location: ' . $redirect);
    exit;
}
// Reject requests ending with .php so they do not hit API branches 
if (substr($request, -4) === '.php') {
    $request = substr($request, 0, -4); // strip .php extension
}

/*
//get basePath from .env (after loading env)
$basePath = $_ENV['APP_PATH'] ; //eg. /myapp
$_SESSION['basePath'] = $basePath;
*/

// Check if user is logged in - hopefully site engr will be maintaining this pattern ie. all sites having $_SESSION['user_id']
if (empty($_SESSION['user_id']) || empty($_SESSION['school_id'])
   ) {
    
if( !str_starts_with($request, '/auth'))   {
    /*
    var_dump ("request",$request);
    echo "<br><br>";
    
    echo "session sch-user empty / no auth uri";
    echo "<br><br>";
    */

// header("Location: " .  "/{$appName}/auth/login.php");
 header("Location: " .  "/qpicker/auth/login.php"); //refactor later : av general route file ie.  project_root_routes for general auth
        exit;
    }
    /*
    echo "session sch-user empty / yes auth uri";
    echo "<br><br>";
    */
}
else
    {
   // echo "session user ok";
    }


// Load the app's routes
// require __DIR__ . "/core/Router.php";
require APP_PATH . "/routes/routes.php";















/*
%%%]%
%%%%%
%%%%%%
*/


/*
ahapMYBubi ...
1pic=1000words.. archi of fate


lib helping functions ... put core functions in htdocs/core

getting uri
getring method
getting the appName... use php funtion

exiting if appNmae dir not found

getting basePath (using appName)
then other paths

using paths... getting autoload+db+helpers
getring app controller

hhtps redirecting

cutting off appName .... for request


no .php
check user login

sending to routes

*/



?>













