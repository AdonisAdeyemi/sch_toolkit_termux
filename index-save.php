<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
    
    
// Determine which app from the URL
$uri = trim($_SERVER['REQUEST_URI'], '/');  // e.g., "app1/dashboard"
$parts = explode('/', $uri);

// First part is the app name
$appName = $parts[0] ?? 'app1'; // default app

$appPath = __DIR__ . "/{$appName}";
if (!is_dir($appPath)) {
    http_response_code(404);
    exit("App '{$appName}' not foundd.");
}

/*
getting uri
getting the appName... use php funtion

exiting if appNmae dir not found

getting basePath (using appName)
then other paths

using paths... getting autoload+db+helpers

hhtps redirecting

cutting off appName .... for request
rtrim only if not /
getting method 


no .php
check user login

sending to routes

*/

// Load the app's routes
echo "hey";
// require __DIR__ . "/core/Router.php";
require __DIR__ . "/_{$appName}/routes.php";



/*****
*******
*********
*******/

<?php
declare(strict_types=1);
// ------------------------------------------------------------
// Front Controller — handles all web requests
// ------------------------------------------------------------
require_once __DIR__ . '/../lib/helper_functions.php';
report_error(true) ;



// start or resume session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


// 3️⃣ Define constants
define('BASE_PATH', dirname(__DIR__));
define('SRC_PATH', BASE_PATH . '/src');
define('VIEW_PATH', SRC_PATH . '/Views');
define('ROUTES_PATH', BASE_PATH . '/routes');
define('API_PATH', SRC_PATH . '/api');
define('AUTH_PATH', SRC_PATH . '/auth');


// 2️⃣ Autoload (composer)
require_once BASE_PATH . '/vendor/autoload.php';
/* db pdo */
require_once BASE_PATH . '/config/config_db.php'; /* your PDO setup file */
// 4️⃣ Include connection + helpers (if any)
require_once BASE_PATH . '/lib/lib_db.php';
require_once BASE_PATH . '/lib/Router.php';






// 3️⃣b Import (use) the controllers you’ll route to
use App\Controllers\UserController;
use App\Controllers\DashboardController;
use App\Controllers\CompilationController;
use App\Controllers\QuestionController;
use App\Controllers\AuthController;


use App\Models\AdminCompilationModel;
use App\Models\User;
use App\Controllers\AdminCompilationController;


if (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === 'off') {
    $redirect = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    header('Location: ' . $redirect);
    exit;
}

// 5️⃣ Parse and normalize request --- use for removing appNmae
$basePath = $_ENV['BASE_PATH'];

$request = str_replace($basePath, '', $_SERVER['REQUEST_URI']);

/*
$request = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
*/
$method = $_SERVER['REQUEST_METHOD'];


if ($request !== '/' && str_ends_with($request, '/')) {
    $request = rtrim($request, '/');
}


// Reject requests ending with .php so they do not hit API branches 
if (substr($request, -4) === '.php') {
    $request = substr($request, 0, -4); // strip .php extension
}


echo '<pre>';
echo "REQUEST_URI: " . $_SERVER['REQUEST_URI'] . "\n";
echo "SCRIPT_NAME: " . $_SERVER['SCRIPT_NAME'] . "\n";
//echo "Base Path: " . $scriptName . "\n";
echo "Calculated route: -" . $request . "-\n";
echo '</pre>';
// exit;

/*
// --- API routes ---
if (str_starts_with($request, '/api')) {
    require_once ROUTES_PATH . '/api.php';
    exit;
}

// --- Auth routes ---
if (str_starts_with($request, '/auth')) {
    require_once ROUTES_PATH . '/auth.php';
    exit;
}

*/


/****  var dump - debug *&********/
/*****&********/
/*****&********/
/*****&********/

/*   VAR_DUMPs for debug
try {

echo "pdo show<br>";
var_dump ($pdo) ;

    $stmt = $pdo->query("SELECT 1");
    $result = $stmt->fetch();
    var_dump($result);
} catch (PDOException $e) {
    echo "PDO error: " . $e->getMessage();
}

echo "<br>frontController session vardump<b>";
var_dump ($_SESSION);

*/




// Check if user is logged in
if (empty($_SESSION['user_id']) || empty($_SESSION['school_id'])) {

 header("Location: {$basePath}/auth/login.php");
        exit;
}



?>






















