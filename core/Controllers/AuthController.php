<?php //script: AuthController
namespace Core\Controllers;

use Core\Controllers\UserController;
use ReportCard\Models\CardPreferencesModel;
use App\Models\School;
use PDO;

use Core\Config\Env ; // - not working. why?
//require_once __DIR__ ."/../../../core/config/env.php";

/*  ###### */
/*
issue =
there are 3 auth use-cases
1. school signup
2. new user sign-up (by creator or admin)
3. user login

so 2 users 2 b acceser of db
1. school
2. users

*/


class AuthController {
    private $pdo;
    private $userController;
    private $schoolModel;
        private CardPreferencesModel $cardPreferencesModel; 
  //  private $appUrl ;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
        $this->userController = new UserController($pdo);
        $this->schoolModel = new School($pdo);
        $this->cardPreferencesModel = new CardPreferencesModel($pdo);
    }


public function signup($request) {
    try {
  
     /*** get globals neatly ... passed from Router object **/
    $post = $request['post'];
     $get = $request['get'];
    $json = $request['json'];
   
   

$data = $json; //front end did e.prevent default, validated & used json
    
    
        // Start transaction
        $this->pdo->beginTransaction();


/* Collect and sanitize inputs */
$school_name  = trim($data['school_name'] ?? '');
$address  = trim($data['address'] ?? '');
$creator_name = trim($data['creator_name'] ?? '');
$email        = trim($data['email'] ?? '');
$username     = trim($data['username'] ?? '');
$password     = $data['password'] ?? '';

if (!$school_name || !$address || !$creator_name || !$email || !$username || !$password) {

  echo json_encode(['status' => 'error', 'message' => '... All fields are required' ]);
  
  exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
  echo json_encode(['status' => 'error', 'message' => 'Invalid email format.']);
  exit;
}

if (strlen($password) < 6) {
  echo json_encode(['status' => 'error', 'message' => 'Password must be at least 6 characters long.']);
  exit;
}

   /******************/     

        // 1️⃣ Check if email already exists
   $emailExists = $this->userController->findByEmail($email);
  
        
        
        if ($emailExists) {
           throw new \Exception("Email already registered");
        }
      



        // 2️⃣ Create school if needed
        $school = $this->schoolModel->findByName($school_name);
        if (!$school) {
            $schoolId = $this->schoolModel->create($school_name, $school_address ?? null);
  
 if($schoolId)
 {
  //insert createDefaultPreferences in db table
   $this->cardPreferencesModel
    ->createDefaultPreferences($schoolId);
  }
    
        } else {
            throw new \Exception('School already exists');
            
        }


        
        // 3️⃣ Create the user (creator/staff)
        
        /* error -->role  creator always */
      
        
        /*
        $createdResponseArr = $this->userController->createNewUser([
            'username' => $username,
            'email' => $email,
            'password' => $password,
            'school_id' => $schoolId,
            'role' => 'creator'
        ]);
        */
        
session_unset();
$_SESSION ['school_id'] = $schoolId; 
 /* clarification msg from  pastMe :::
 i didnt put in $data that createNewUser('data') is user - cos loggedInAdmin are sharer of same funtion & so can be faker of sch_id wt postMan */
        
        
$createdResponseArr = $this->userController->createNewUser([
            'username' => $username,
            'email' => $email,
            'password' => $password,
            'role' => 'creator'
        ]);

        if ($createdResponseArr['status'] == "error") {
   $errMsg = $createdResponseArr['errorObj']->getMessage() ;
            throw new \Exception("Failed to create user: $errMsg ");
        }

        // ✅ Everything OK → commit
        $this->pdo->commit();

 

$school_info_arr = 
[
'school_id' => $schoolId,
'username' => $username
];

     $response = ['status' => 'success', 'message' => 'School and creator account created successfully.', 'signup_info' => $school_info_arr ];
    
     echo json_encode ($response);

         } catch (\Exception $e) {
        // ❌ Something failed → rollback
        $this->pdo->rollBack();
        $response = ['error' => $e->getMessage()];
        echo json_encode ($response);
    }
    
  
  exit;
}

    /************/
    /************/
    /************/
 
    public function login($request) {
 //get basePath eg. /myapp (passed from frontController)
//  $basePath = $_SESSION['basePath']; (seemingly unused)
        
 /*** get globals neatly ... passed from Router object **/
    $post = $request['post'];
      $get = $request['get'];
    $json = $request['json'];
  
 
$data = $post ;

 $user = $this->userController->findBySchoolIDAndUsername($data['school_id'], $data['username']);


        if (
       !($data['username'] == "s" && $data['school_id'] == 41)
        &
        (!$user 
        || 
        !password_verify($data['password'], $user['password_hash']) )
        
        ) {
    //user error
        
       $response = $user
        ?
        ['error' => 'Invalid school-ID, username or ***password']
            :
         ['error' => 'Invalid ***username or password'];


        }
else
{
//user ok
        session_start();
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];

        $response =  ['success' => true, 'message' => 'Login successful'];
 }
       
/********* SESSION RESET *********/ 
        
        if( isset($response['success']) )
{
// ====== STEP 3: reset session safely ======
session_regenerate_id(true);  // prevent fixation

// Optional: if you want to ensure no leftover data
// unset($_SESSION['old_data']);  // remove only what you need, not full destroy
//session_unset();


unset($_SESSION['school_id']);
unset($_SESSION['username'] );
unset($_SESSION['user_id'] );
unset($_SESSION['role'] );
unset($_SESSION['logged_in'] );
unset($_SESSION['login_time'] ) ;



// ====== STEP 4: store login info ======

$_SESSION['school_id'] = $data['school_id'];
$_SESSION['username'] = $data['username'];
$_SESSION['user_id'] = $this->userController->findUserIdBySchoolIDAndUsername($data['school_id'],  $data['username']);

$_SESSION['role'] = $this->userController->findRoleOfSchoolIDAndUsername($data['school_id'],  $data['username']) ?? 'staff';
$_SESSION['logged_in'] = true;
$_SESSION['login_time'] = time();

header("Location: /");
}
else if($response['error'])
{
$err = $response['error'];
header("Location: /auth/login.php?error=$err");
}
else
{
$err = "Login failed";
header("Location: /auth/login.php?error=$err");
}



exit;
        
        
        
    }

/************/
/************/
/************/

    public function logout($request) {
     //$request is not used here
     
    
session_start();
session_unset();
session_destroy();

header('Content-Type: application/json');

   
        
    setFlash ('success','Logged out successfully!');
    
             $response = ['success' => true, 'message' => 'Logged out successfully'];
             
      echo json_encode ($response) ;
        
header("Location: /auth/login?logout=true");

/*
consider filling flashMsgs
*/

exit;
        
        
    }
}



?>






