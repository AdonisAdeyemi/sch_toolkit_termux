<?php
//script name - qpicker index.php

echo "top of index <br>";
    
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

var_dump($_SESSION); //it is breaker json parser



//CONSTANTS NOT dependent on appName 
//>> Find others dependent on appName after appName is extracted
define('PROJECT_ROOT', __DIR__);
define('ROUTES_PATH', PROJECT_ROOT . '/routes');
define('AUTH_PATH', PROJECT_ROOT . '/shared/Views/auth'); //refactor : this seems unsed
define('CORE_PATH', PROJECT_ROOT ."/core");
define('LIB_PATH', CORE_PATH ."/lib");



// 2️⃣ Autoload (composer)
require_once PROJECT_ROOT.'/vendor/autoload.php';

require_once LIB_PATH .'/lib_db.php';

/* for pdo */
// refactored config (1env getter, 2 array maker, 3 array user 4 pdo

//require_once PROJECT_ROOT."/core/config/env.php";

//require_once PROJECT_ROOT . '/core/config/config.php';
require_once PROJECT_ROOT . '/core/database/connection.php';

use Core\config\Env ;
use Core\config\Config ;


/* interesting  : __DIR__.'/core/lib/lib_db.php';
is somehow not same 
'/core/lib/lib_db.php';
-> myb cos of schoolkit domain? myb not? do research 
*/


Env::load(PROJECT_ROOT );
$config = Config::make();
$pdo = Connection::make($config['db']);

echo "in index : app url22a : ". Env::get("APP_URL") ;
echo "<br>";
echo "app url22 : ". $config['app']['url'] ;
echo "<br>" ;

require_once LIB_PATH . '/Router.php';






$request = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$method = $_SERVER['REQUEST_METHOD'];

// Reject requests ending with .php so they do not hit API branches 
if (substr($request, -4) === '.php') {
    $request = substr($request, 0, -4); // strip .php extension
}

//cleaning ending slash
if ($request !== '/' && str_ends_with($request, '/')) {
    $request = rtrim($request, '/'); 
}



echo "<br><br>";
echo "request ccc:  $request";
echo "<br><br>";


//for appName
$appName = "";
$appPath = "";
$_SESSION["appName"] = $appName; //session's appName set below when appPath exists


if ($request !== '/' 
&& 
!str_starts_with($request, '/auth')
)
{
// separate $appName from $request
// 1. Determine which app from the URL
$uriForAppName = trim($request, '/'); //rtrim(request) already used above BUT trim is needed gere

$parts = explode('/', $uriForAppName); // First part is the app name
//2. extract appName
$appName = $parts[0] ;

//3. exiting if dir notFound
$appPath = __DIR__ . "/app_{$appName}";

echo "<br><br>";
echo "appPath ddd:  $appPath";
echo "<br><br>";


if (!is_dir($appPath)) {
    http_response_code(404);
    exit("in frontController - App '{$appName}' not foundd.");
}
else
{
//set appName in $_SESSION
//note : Session's appName was made empty at start of this frontController

$_SESSION["appName"] = $appName; 
}
var_dump("xxxxxx\n\n",$_SESSION); //it is breaker json parser

// 4. removing appNmae from request - myb get php helper function to be separating and returning both AppName & requestStripedOfAppName
if (strpos($request, "/{$appName}") === 0)
{
 //  echo "appname is at start <br><br>";
 $request = str_replace("/{$appName}", '', $request);
}

} //close of >>> if ($request !== '/')>>> block


// 3️⃣ Define constants (dependent on $appName)
// refactor : since src is autoloaded, why do I need SRC_PATH VIEW_PATH (am I wrong?)
//constants dependent on appName
define('APP_PATH', "{$appPath}");
define('SRC_PATH', APP_PATH . '/src');
define('VIEW_PATH', SRC_PATH . '/Views');


//Https/http auto selector (avoids error in localhost
$isLocal = in_array($_SERVER['HTTP_HOST'], [
    'localhost:8080',
    '127.0.0.1:8080'
]);

if (!$isLocal && (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === 'off')) {
    $redirect = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    header('Location: ' . $redirect);
    exit;
}

//START routing

// Check if user is logged in - hopefully site engr will be maintaining this pattern ie. all sites having $_SESSION['user_id']
//if not logged in, send all to auth routes
if (empty($_SESSION['user_id']) || empty($_SESSION['school_id'])
   ) {
    
require ROUTES_PATH . "/auth_routes.php";

        exit;
 }

//if logged in user wants to route to auth again
// all auth goes here
if( str_starts_with($request, '/auth'))   {

require ROUTES_PATH . "/auth_routes.php";
 
        exit;
 }


// all home goes here - welcome page
if ($request == '/' )   {

require ROUTES_PATH . "/home_routes.php";
 
        exit;
 
 }


// Load the app's routes
// require __DIR__ . "/core/Router.php";

$route_prefix = $appName ;
require ROUTES_PATH . "/{$route_prefix}_routes.php";














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













