<?php
namespace ReportCard\Controllers;

use Core\Controllers\BaseController;

use PDO;
use Exception;


class DashboardController extends BaseController {

    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;

    }



    public function show() {
        try
{

$title = $this->appName() ." Dashboard";
$appName = $this->appName() ;
    
        $this->render('dashboard/dashboard', compact('title','appName'));
        
        
    }
         catch (\Exception $e) {
          $errMsg =  $e->getMessage() ;
            // Optional: log errors and continue
            error_log("Dashboard error  " . $errMsg);
            setFlash( "danger","Dashboard Error : ".$errMsg) ;   
          log_debug($errMsg,"dashErr");
        }
    }
    }
    




?>








