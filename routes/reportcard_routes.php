<?php
require_once PROJECT_ROOT . '/core/lib/Router.php';
$router = new Router($pdo);

echo "<hr>reportcard router :: uri request  - $request <br>";

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
use ReportCard\Controllers\ClassController;
use ReportCard\Controllers\DashboardController;
use ReportCard\Controllers\SubjectController;


echo "request : $request <br>" ;


if (true)
{ // future AB, cleaning of uri names is adviced

/*************~~~~~~!~~~****/
/*** --- Questions API --- **&**/
/*
    $router->get('/api/questions/query_and_search', [QuestionController::class , 'queryAndSearchQuestions']) ;
    
 $router->post('/api/compilation/duplicate', [CompilationController::class , 'duplicate']);
*/







//DASHboard : empty after "reportcard" prefix is stripped off
$router->get('/dashboard', [
    DashboardController::class,
    'show'
]);

$router->get('/generate/class', [
    ReportController::class,
    'generateClass'
]);

$router->get('/generate/student', [
    ReportController::class,
    'generateStudent'
]);



/**** school classes ******/


$router->get('/admin/classes', [
    ClassController::class,
    'index'
]);


$router->post('/admin/classes/store', [
    ClassController::class,
    'store'
]);


$router->post('/admin/classes/delete', [
    ClassController::class,
    'delete'
]);


$router->post('/admin/classes/list', [
    ClassController::class,
    'list'
]);

$router->get('/admin/classes/deleted', [
    ClassController::class,
    'deleted'
]);

$router->post('/admin/classes/restore', [
    ClassController::class,
    'restore'
]);

/*********** Subjects **************/

$router->get('/admin/subjects', [
    SubjectController::class,
    'index'
]);

$router->get('/admin/subjects/create', [
    SubjectController::class,
    'create'
]);

$router->post('/admin/subjects/store', [
    SubjectController::class,
    'store'
]);

$router->get('/admin/subjects/edit', [
    SubjectController::class,
    'edit'
]);

$router->post('/admin/subjects/update', [
    SubjectController::class,
    'update'
]);

$router->post('/admin/subjects/delete', [
    SubjectController::class,
    'delete'
]);


$router->post('/admin/subjects/restore', [
    SubjectController::class,
    'restore'
]);
  
// ------------------------------
// Run the router
// ------------------------------
$router->dispatch($request,$method);

        
 } //end of api block
 
 
 
 
 
 
 
 
 
 
 
 
