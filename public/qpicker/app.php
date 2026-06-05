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
// 5️⃣ Parse and normalize request
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



// Instantiate controller
$adminModel = new AdminCompilationModel($pdo);
$adminUserModel = new User($pdo);
$adminController = new AdminCompilationController($adminModel, $adminUserModel);



// 7️⃣ Route map — basic micro-router
switch ($request) {

    // -----------------------------
    // DASHBOARD (smart redirect logic)
    // -----------------------------
    case '/':
    
    /*******************/
/*******************/
    
    case '/dashboard':
	echo "post<br>";
        var_dump ($_POST);
        echo "seasion<br>";
        var_dump ($_SESSION);
        
        $controller = new DashboardController($pdo);
        $controller->show(); // Handles admin vs staff
        exit;

    // -----------------------------
    // USERS (list, create, submit)
    // -----------------------------
    case '/admin/users':
 $controller = new UserController($pdo);
        $controller->index($_SESSION['school_id']);
        exit;

/*******************/
/*******************/

    case '/admin/users/create':
        $controller = new UserController($pdo);
        $controller->createForm(); // Show "Add New Staff" form
        exit;
        
/*******************/
/*******************/

    case '/admin/users/new':
    if ($method  === 'POST') {
       $controller = new UserController($pdo);
$response=  $controller->adminCreateNewUser($_POST); // Handle POST form submit
     
     }
        else 
        {
        echo "<h1>Request denied - Request must be POST method</h1>";
        }
        exit;
        
   /*******************/
/*******************/     
        
    case '/admin/users/delete':
    
 $controller = new UserController($pdo);
 $controller->delete($_POST['id'] ?? null,$_SESSION['user_id']);
    exit;
    
 /*******************/
/*******************/
    
case  '/admin/users/restore' :
	if  ($method  === 'POST') {
    $controller = new App\Controllers\UserController($pdo);
    $controller->restore($_POST);
} 
else 
{
echo "<h1>Request denied - Request must be POST method</h1>";
}
/***###$ ****/
/******$$$$$$****/





/***###$ ********/
/******$$$$$$****/

case  '/script' :

require 'scripts/import_questions.php';

exit;


/***************/
/***************/
/**** compilation routes *****/
/***************/

case  '/view/compilation/search' :

require VIEW_PATH . '/compilation/search_page.php';

exit;

/******************/



/******************/

    // 1️⃣ Render HTML picker page
    case '/compilation/picker':
    $controller = new CompilationController($pdo);
        $controller->showQuestionPickerPage();
        exit;

  


/***************/
  // 1️⃣ show admin's  created compilation 
    case '/compilations/my':
    $controller = new CompilationController($pdo);
        $controller->showPersonalCompilations($_SESSION['user_id']);
        exit;


/******************/
  // 1️⃣ show page to review submited  compilation 
    case '/compilation/review':
    $controller = new CompilationController($pdo);
        $controller->reviewSubmissions($_SESSION['user_id']);
        exit;

/***************/

 // 1️⃣ show all compilations for school
 /*
    case '/admin/compilation/all':
    $userModel = new User($pdo);
   $compilationModel = new AdminCompilationModel($pdo);
    $compilationController = new AdminCompilationController( $compilationModel ,   $userModel );
 
    
        exit;
*/
/******************/




    /* ----------------------------------------------
       GET or POST /admin/compilations/all
    -----------------------------------------------*/
    case $request === '/admin/compilations/list' && in_array($method, ['GET','POST']):
        $adminController->listCompilations();
        break;

    /* ----------------------------------------------
       POST /admin/compilations/update-state
    -----------------------------------------------*/
    case $request === '/admin/compilations/update-state' && $method === 'POST':
        $adminController->updateState();
        break;

    /* ----------------------------------------------
       POST /admin/compilations/saveComment
    -----------------------------------------------*/
    case $request === '/admin/compilations/saveComment' && $method === 'POST':
        $adminController->saveComment();
        break;
 /* ----------------------------------------------
       GET /admin/compilations/getComment
    -----------------------------------------------*/
    case $request === '/admin/compilations/getComment' && $method === 'GET':
        $adminController->getComment();
        break;
    /* ----------------------------------------------
       POST /admin/compilations/clone
    -----------------------------------------------*/
    case $request === '/admin/compilations/clone' && $method === 'POST':
        $adminController->clone();
        break;

    /* ----------------------------------------------
       POST /admin/compilations/submit
    -----------------------------------------------*/
    case $request === '/admin/compilations/submit' && $method === 'POST':
        $adminController->submit();
        break;

    /* ----------------------------------------------
       POST /admin/compilations/delete
    -----------------------------------------------*/
    case $request === '/admin/compilations/delete' && $method === 'POST':
        $adminController->delete();
        break;

    /* ----------------------------------------------
       GET /admin/compilations/print/{id}
    -----------------------------------------------*/
    case preg_match('#^/admin/compilations/print/(\d+)$#', $request, $matches) === 1:
        $id = intval($matches[1]);
        $adminController->print($id);
        break;


  /* ----------------------------------------------
       GET /admin/compilations/get_pdf/{id}
    -----------------------------------------------*/
    case preg_match('#^/admin/compilations/get_pdf/(\d+)$#', $request, $matches) === 1:
        $id = intval($matches[1]);
        $adminController->generateCompilationPDF($id);
        break;


    // -----------------------------
    // DEFAULT — not found
    // -----------------------------
    default:
        http_response_code(404);
        echo "<h1>404 - Page Not Found</h1>";
        echo "<p>Route '{$request}' not handled by router (front-router).</p>";
        exit;
}

?>










