<?php
require_once PROJECT_ROOT . '/core/lib/Router.php';
$router = new Router($pdo);

// 3️⃣b Import (use) the controllers you’ll route to
/*
use ReportCard\Controllers\UserController;
use ReportCard\Controllers\DashboardController;
use ReportCard\Controllers\CompilationController;
use ReportCard\Controllers\QuestionController;
use ReportCard\Controllers\AuthController;
use ReportCard\Controllers\PaystackController;

use ReportCard\Models\AdminCompilationModel;
use ReportCard\Models\User;
use ReportCard\Controllers\AdminCompilationController;

*/


use ReportCard\Controllers\ReportController;


echo "request : $request <br>" ;


if (true)
{ // future AB, cleaning of uri names is adviced

/*************~~~~~~!~~~****/
/*** --- Questions API --- **&**/
/*
    $router->get('/api/questions/query_and_search', [QuestionController::class , 'queryAndSearchQuestions']) ;
    
 $router->post('/api/compilation/duplicate', [CompilationController::class , 'duplicate']);
*/


$router->get('/generate/class', [
    ReportController::class,
    'generateClass'
]);

$router->get('/generate/student', [
    ReportController::class,
    'generateStudent'
]);

$router->get('/generate/student', [
    ReportController::class,
    'generateStudent'
]);

  
// ------------------------------
// Run the router
// ------------------------------
$router->dispatch($request,$method);

        
 } //end of api block
 
 
 
 
 
 
 
 
 
 
 
 
