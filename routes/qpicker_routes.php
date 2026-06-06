<?php
require_once PROJECT_ROOT . '/core/lib/Router.php';
$router = new Router($pdo);

echo "qpicker router :: uri request  - $request <br>";


// 3️⃣b Import (use) the controllers you’ll route to
use App\Controllers\UserController;
use App\Controllers\DashboardController;
use App\Controllers\CompilationController;
use App\Controllers\QuestionController;
use App\Controllers\AuthController;
use App\Controllers\PaystackController;

use App\Models\AdminCompilationModel;
use App\Models\User;
use App\Controllers\AdminCompilationController;









if (
   str_starts_with($request, '/api')
   )
{ // future AB, cleaning of uri names is adviced

    $router->get('/api/questions/query_and_search', [QuestionController::class , 'queryAndSearchQuestions']) ;


/*************~~~~~~!~~~****/
/*** --- Questions API --- **&**/

    $router->get('/api/questions/query_and_search', [QuestionController::class , 'queryAndSearchQuestions']) ;
    
   
   
    $router->get('/api/questions/dropdown_list', [QuestionController::class , 'get_dropdown_list']) ; 



/*
CLEAN ME PLEASE 

// >>>>>>>>>>>> auth >>>>>>>>>>>

	   $router->post('/auth/api/login', [AuthController::class , 'login']);
		
 $router->post('/auth/api/signup' , [AuthController::class , 'signup']);
		
		
		 $router->get('/api/logout', [AuthController::class , 'logout']); //does logout need to be an api? its not demander of any data
    
    */
		
		
/***** compilations *****/
	
	 $router->post('/api/compilation/create' , [CompilationController::class , 'createCompilation']);
	 
	    $router->get('/api/compilation/get_compilations', [CompilationController::class , 'getCompilationsByQuery'] );

   $router->get('/api/compilation/get_items', [CompilationController::class , 'getCompilationItems'] );
	 
        

        $router->post('/api/compilation/save_items', [CompilationController::class , 'saveItems'] );
        
        
        $router->post('/api/compilation/delete', [CompilationController::class , 'deleteCompilation']);
        
        
        
   $router->get('/api/compilation/meta', [CompilationController::class , 'getCompilationMeta']);

 $router->post('/api/compilation/update_meta', [CompilationController::class , 'updateCompilationMeta']);
 
 $router->post('/api/compilation/duplicate', [CompilationController::class , 'duplicate']);

  
// ------------------------------
// Run the router
// ------------------------------
$router->dispatch($request,$method);

        
 } //end of api block


/*
AUTH NOW HAS SEPARATE FILE

else if (str_starts_with($request, '/auth')) {
    
switch ($request) {
	case "/auth/login" : 
		require AUTH_PATH . '/login.php';
		
    exit;
    
 //xxxxxxxxxxxxxxxx
	case "/auth/signup" : 
		 require AUTH_PATH . '/signup.php';
		exit;
		
//xxxxxxxxxxxxxxcx

default :
echo "qpicker router :: uri request  - $request <br>";
		// --- Catch-all for unknown API endpoints ---
http_response_code(404);
echo json_encode(['error' => 'API endpoint Not found (AUTH)']);
exit;


} //end of switch

    
} //end of auth block
*/

else  { //idealy for views - not apis ... needs refactoring
    


// Instantiate controller
$adminModel = new AdminCompilationModel($pdo);
$adminUserModel = new User($pdo);
$adminController = new AdminCompilationController($pdo,$adminModel, $adminUserModel);



// 7️⃣ Route map — basic micro-router
switch ($request) {

    // -----------------------------
    // DASHBOARD (smart redirect logic)
    // -----------------------------
    case '/dashboard':
    
	echo "in qpicker routes dashboard - post<br>";
        
        $controller = new DashboardController($pdo);
        $controller->show(); // Handles admin vs staff
        exit;

    // -----------------------------
    // USERS (list, create, submit, change_pass)
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
	exit;
} 
else 
{
echo "<h1>Request denied - Request must be POST method</h1>";
}
/***###$ ****/
/******$$$$$$****/

case  '/admin/users/change_role' :
	 $controller = new UserController($pdo);
 $controller->changeRole
 (
 $_POST['id'] ?? null,
 $_POST['role'] ?? null
 );
    exit;
    
    
    
/***###$ ****/
/******$$$$$$****/
/**** PS: it is not '/admin/users/change_password' like the rest cos not only admin can changevpasswword $$$****/

  case  '/user/change_password' :
  
  var_dump("post",$_POST);
	 $controller = new UserController($pdo);
 $controller->change_password
 (
 $_POST
 );
    exit;  
    

/******$$$$$$****/
/******$$$$$$****/
/**** future AB :) , consider changing all webhook/endpoints that rewuest for html TO /view/ >>> ensure future easy comprehension     *****/

case  '/user/view/change_password' :
	 $controller = new UserController($pdo);
 $controller->show_change_password ();
    exit;  
    
 



/***###$ ********/
/******$$$$$$****/

case  '/script' :

require BASE_PATH.'/public/scripts/import_questions.php';

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
        $adminController->getCompilationPDF($id);
        break;
        
        
   /**** paystack webhook *(***/     
case $request === '/paystack/initialize' && $method === 'POST':
        $controller = new PaystackController($pdo);
    $controller->initialize();
        exit;

   
case $request === '/paystack/callback' && $method === 'GET':
$controller = new PaystackController($pdo);
    $controller->callback();
        exit;


  case $request === '/paystack/webhook' :
        //&& $method === 'POST':
        $controller = new PaystackController($pdo);
    $controller->webhook();
        exit;




    // -----------------------------
    // DEFAULT — not found
    // -----------------------------
    default:
        http_response_code(404);
        echo "<h1>404 - Page Not Found</h1>";
        echo "<p>Route '{$request}' not handled by router (front-router).</p>";
        exit;
}
    
    
}






    
    
    
    


?>








