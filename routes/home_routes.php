<?php
require_once CORE_PATH . '/lib/Router.php';
$router = new Router($pdo);

echo "top - home router :: uri request  - $request <br>";


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














