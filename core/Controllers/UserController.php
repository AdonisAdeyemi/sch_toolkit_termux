<?php
namespace Core\Controllers;


use Core\Models\User;
use Core\Controllers\BaseController;
use InvalidArgumentException;


class UserController extends BaseController
{
    private $userModel;
    private $pdo;

    public function __construct($pdo) {
        $this->userModel = new User($pdo);
        $this->pdo = $pdo;
    }


/******/
public function findByEmail($email) {
return $this->userModel->findByEmail($email) ;
}

/**************/  
  /*** index = get all staff/users **/
    public function index ($school_id) {
       
        // ✅ Fetch all users in same school, check showDeleted request
        $showDeleted = false;
        
        if(isset($_GET['show_deleted']))
        {
        $showDeleted = $_GET['show_deleted'];
        }
            
$appName = $_SESSION['appName'];

        
        $users = $this->userModel->getAllUsersBySchoolID($school_id,$showDeleted);
  /** get creator id eg. 4 making creator undeletable in users/index/ **/
$creatorID_forCurrentSch = $this->getCreatorIdBySchool($_SESSION['school_id']);      
 
  $currentUserId = $_SESSION['user_id'];

echo "usrCntrlr >> currId : $currentUserId and creatorID_forCurrentSch: $creatorID_forCurrentSch <br>";
var_dump ($_SESSION);





$this->render('users/index', [
'users' => $users, 
'title' => 'User List',
'creatorID_forCurrentSch' => $creatorID_forCurrentSch,
'currentUserId' => $currentUserId,
'appName' => $appName
 ]);





        
        
    }  
    
/**(**(**/

public function restore($post_data)
{
    $id = $post_data['id'] ?? null;
    
$appName = $_SESSION['appName'];

    if (!$id) {
        die("Missing user ID.");
    }

    $restored = $this->userModel->restoreUser($id);

    if ($restored) {
        
    setFlash ('success','Staff restored successfully!');
        
        header("Location: /{$appName}/admin/users?restored=1");
        exit;
    } else {
        die("Failed to restore user.");
    }
}

/*****&***/


    public function createForm() {
        
  $appName = $_SESSION['appName'];
        
        $this->render('users/create', [
'title' => 'Create New User',
'appName'=> $appName
 ]);

        
    }

/*****&&&&******/
public function adminCreateNewUser($data) {
$appName = $_SESSION['appName'];

$response=  $this->createNewUser($data); // Handle POST form submit
if($response['status']=='success') 
{
 setFlash ('success', 'Staff created successfully' );
}
else if($response['status']=='error')
{
   $errObj = $response['errorObj'];
setFlash ('danger', $errObj->getMessage() );
}
// Redirect back to user list (for success or failure)
 header("Location: /{$appName}/admin/users");
        
}

/************/

    public function createNewUser($data) {
    
    try
    {
 $this->userModel->create($data);
     // After successfully creating user


 return ['status' => 'success', 'message' => 'User created successfully.'];
    }
    catch (\PDOException $err)
    {
    return ['status'=>'error','errorObj' => $err ] ;
 }
    
    exit;
        
    }


/***************/
/*
public function fillFlashMessages($isSuccess, $err = null)
{

if($isSuccess)
{
$_SESSION['flash_message'] = [
    'type' => 'success',
    'text' => 'Staff created successfully!'
];
}
    
else if (!$isSuccess && $err)
{
  $err_msg = $err->getMessage() ;
    
     $_SESSION['flash_message'] = [
 'type' => 'danger',
    'text' => 'Database Error : '.$err_msg
    ];
    
    }
 }
*/

/*****(((*/

public function getCreatorIdBySchool($schoolId)
{
    if (empty($schoolId)) {
        throw new InvalidArgumentException("School ID is required");
    }
    return $this->userModel->getCreatorIdBySchool($schoolId);
}



/****((**************/	

public function delete($id,$admin_id)
{
    session_start();
    
$appName = $_SESSION['appName'];

    // Only admin/creator can delete
    if (!in_array($_SESSION['role'], ['admin', 'creator'])) {
        http_response_code(403);
        echo "Access denied.";
        exit;
    }

    $this->userModel->softDeleteById($id,$admin_id);
    
    setFlash ('success','Staff deleted successfully!');

    // Redirect back to user list
    header("Location: /{$appName}/admin/users");
    exit;
}

/***************/
//admin/users/change_role
public function changeRole($id,$role)
{

$appName = $_SESSION['appName'];

    // Only admin/creator can changeRole - 
    //is this essential? cos already only admin can access frontEnd _ maybe in case of postman hack _ furure AB confirm !!!
    if (!in_array($_SESSION['role'], ['admin', 'creator'])) {
        http_response_code(403);
        echo "Access denied.";
        exit;
    }

    $this->userModel->changeRole($id, $role);
    
    setFlash ('success','Role changed successfully!');

    // Redirect back to user list
    header("Location: /{$appName}/admin/users");
    exit;

}

/********************/

public function findBySchoolIDAndUsername($school_id, $username) {

$user = $this->userModel->findBySchoolIDAndUsername($school_id, $username) ;

return $user;

}



/***********/

public function findUserIdBySchoolIDAndUsername($school_id, $username)
{
$user = $this->userModel->findBySchoolIDAndUsername($school_id, $username) ;

return $user['id'];

}

/***********/

public function findRoleOfSchoolIDAndUsername($school_id, $username)
{
$user = $this->userModel->findBySchoolIDAndUsername($school_id, $username) ;

return $user['role'];

}

/***(**/

public function show_change_password()
{

$appName = $_SESSION['appName'];

$this->render('users/change_password', 
[
'title' => 'Change Password',
'appName' => $appName
] );

}


/**********************/


public function change_password($data) {

$appName = $_SESSION['appName'];
$passHasError = false;

//future AB :), refactor for better password validation 
if ( trim($data['password_old']) == null
||
 trim($data['password_new']) == null
 )
 {
 setFlash ('danger', 'Password cannot be null');
 $passHasError = true;
 }
 
 
if (strlen($data['password_new']) < 6) {
setFlash ('danger', 'New Password must be at least 6 characters');
$passHasError = true;
  }

/********/

if( !$passHasError )
{
$res_Arr = $this->userModel->change_password($data) ;


if ($res_Arr['status'] == 'success')
{
setFlash ('success', $res_Arr['message'] ) ;
}
else if ($res_Arr['status'] == 'error')
{
setFlash ('danger', $res_Arr['message'] ) ;
}
}

header("Location: /{$appName}/user/view/change_password");

}





/*************************/


}


?>













