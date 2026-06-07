<?php
require_once CORE_PATH . '/lib/Router.php';
$router = new Router($pdo);


echo "<hr>top - home router :: uri request  - $request <hr>";




use Core\Controllers\HomeController;


/*************~~~~~~!~~~****/
/*** ---  --- **&**/



if (true )
{ 

   $router->get('/', [HomeController::class , 'index']);
  
// ------------------------------
// Run the router
// ------------------------------
$router->dispatch($request,$method);

        
 } //end of api block
 
 


?>














