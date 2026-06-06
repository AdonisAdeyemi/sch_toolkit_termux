<?php
require_once CORE_PATH . '/lib/Router.php';
$router = new Router($pdo);

echo "top - auth router :: uri request  - $request <br>";

use App\Controllers\AuthController;


/*************~~~~~~!~~~****/
/*** ---  --- **&**/



if (
   str_starts_with($request, '/api') ||
   str_starts_with($request,'/auth/api')
   )
{ // future AB, cleaning of uri names is adviced


   $router->post('/auth/api/login', [AuthController::class , 'login']);
		
 $router->post('/auth/api/signup' , [AuthController::class , 'signup']);
		
		
		 $router->get('/auth/api/logout', [AuthController::class , 'logout']); //does logout need to be an api? its not demander of any data
    
    
  
// ------------------------------
// Run the router
// ------------------------------
$router->dispatch($request,$method);

        
 } //end of api block
 
 
 else
{

switch ($request) {
	case "/auth/login" : 
		require AUTH_PATH . '/login.php';
		
    exit;
    
 /****&*****/
	case "/auth/signup" : 
		 require AUTH_PATH . '/signup.php';
		exit;
		
/****~~~~~~~********/

default :
echo "auth router :: uri request  - $request <br>";
		// --- Catch-all for unknown API endpoints ---

require AUTH_PATH . '/login.php';
		
 exit;
/*
http_response_code(404);
echo json_encode(['error' => 'API endpoint Not found (AUTH)']);
*/
exit;


}
}

?>














