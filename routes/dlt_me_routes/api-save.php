<?php
use App\Controllers\QuestionController;
use App\Controllers\AuthController;

require_once __DIR__ . '/../lib/Router.php';

/** this is the working duplocate ****/


// Create controllers
$questionController = new QuestionController($pdo);
$authController = new AuthController($pdo);

$method = $_SERVER['REQUEST_METHOD'];
$uri = rtrim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');


// Reject requests ending with .php so they do not hit API branches -- condider DRY... routes/auth.php shares this code too
if (substr($uri, -4) === '.php') {
    $uri = substr($uri, 0, -4); // strip .php extension
}



/*************~~~~~~!~~~****/
/*** --- Questions API --- **&**/
if ($method === 'GET' )
{
switch ($uri) {
	case "/api/questions" : 
		 $questionController->index();
    exit;
    
 /****&*****/
  case '/api/questions/search':
  	  $questionController->search();
    exit;
 
 /*****************/
 
 default :
		// --- Catch-all for unknown API endpoints ---
http_response_code(404);
echo json_encode(['error' => 'API endpoint not fOund']);
exit;


}
}
 
/********************/
/********************/
// --- Auth API ---


if ($method === 'POST')
{
switch ($uri) {
	case '/api/login':
		require API_PATH . '/login.php';
		exit;
		
/****(******/

	case '/api/signup':
		require API_PATH . '/signup.php';
		exit;
		
/****(******/

	case '/api/logout':
		require API_PATH . 'logout.php';
		exit;
/*****~~~~~~~~~~~****/

	default :
		// --- Catch-all for unknown API endpoints ---
http_response_code(404);
echo json_encode(['error' => 'API endpoint nOt Found']);
exit;

} // switch closer
} //post closser


/*************/

/******************/

  // 2️⃣ Create a new compilation
    case '/compilation/create':
    $controller = new CompilationController($pdo);
        $data = $_POST;
    $controller->createCompilation($data);
        exit;

  

/******************/

  // 3️⃣ Get compilations by query (school_id, user_id, review_state)
    case '/api/compilation/get_compilations':
    echo "cccccc";
    $controller = new CompilationController($pdo);
        
        $query = $_GET;
        $school_id = $_SESSION['school_id'] ;
    $controller->getCompilationsByQuery($school_id,$query);
        exit;

  

/******************/

  // 4️⃣ Save compilation items (replace all items for a compilation)
    case '/api/compilation/save_items':
    $controller = new CompilationController($pdo);
        
    //controller uses json in header... not post (info has object data)    
       $controller->saveItems() ;
        exit;

  

/******************/

  // 5️⃣ Delete compilation
    case '/api/compilation/delete':
    $controller = new CompilationController($pdo);
        
        $compilation_id = $_POST['compilation_id'];
      $controller->deleteCompilation($compilation_id);
        exit;

  

/******************/

  // 6️⃣ Update compilation meta-data
    case '/api/compilation/update':
    $controller = new CompilationController($pdo);
        
  $data = $_POST ;
      
   $controller->updateCompilation( $data);
    exit;

  

/******************/

  // 7️⃣ Get compilation items by compilation id
    case '/api/compilation/get_items':
    $controller = new CompilationController($pdo);
          $compilation_id = $_GET['compilation_id'];
    $controller->getCompilationItems($compilation_id);
        exit;

/******************/





?>








