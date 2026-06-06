<?php
namespace Core\Controllers;

use Core\config\Env;
use Core\Controllers\BaseController;

class HomeController extends BaseController
{
    public function index()
    {
        // later: auth check here
        
   //refactor BaseController::render(( later to take full view file & remove the manual includes in here
   //plus it violates DRY


  $appUrl = Env::get("APP_URL");
  $title = "Home";


//normal $this->render() is for stc views folder NOT shared views folder

               require PROJECT_ROOT . '/shared/Views/layouts/header.php';
require_once PROJECT_ROOT . "/shared/Views/dashboard/home.php";
        require_once PROJECT_ROOT . '/shared/Views/layouts/footer.php';
       
       
       

    }
}








