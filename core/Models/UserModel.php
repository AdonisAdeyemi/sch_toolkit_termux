<?php
namespace Core\Models; 

use PDO;
/*
be blocker of
4schol
1. duplicate school name

4user
1. duplicate email
2; duplicate school+username

also
use transaction
*/

class UserModel {
    private $db;

    public function __construct(PDO $pdo) {
        $this->db = $pdo;
    }

    public function findByEmail($email) {
    
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data) {
   
 $school_id = $_SESSION['school_id']; //sch_id no longerin form... logged_in user cant fake school_id with postman
   
        /* Hash password */
  $hashed_password = password_hash($data['password'], PASSWORD_DEFAULT);
        
      
        $stmt = $this->db->prepare("
            INSERT INTO users (username, email, password_hash, school_id, role)
            VALUES (?, ?, ?, ?, ?)
        ");
       return $stmt->execute([
            $data['username'],
            $data['email'],
            $hashed_password,
            $school_id,
            $data['role'] ?? 'staff'
        ]);
        
    }
    
/********(*(*********/



    public function change_password($data) {
   
 $user_id = $_SESSION['user_id']; //user_id no longerin form... logged_in user cant fake school_id with postman
 
 
 /*
 if suplied_old pass != old_db_pass
       flash err + exit
       
put new_pass into db
 */
 
   
     
  
  /* get old pass from db */
 $stmt = $this->db->prepare("
            SELECT password_hash FROM users
            WHERE id = ? 
        ");
 $stmt->execute([ $user_id ]);
 $res = $stmt->fetch(PDO::FETCH_ASSOC);
 $db_hashed_pass_old = $res['password_hash'] ;
 
 $inputed_pass_old = $data['password_old'] ;
 
 /* confirm match */
 
 if (!password_verify( $inputed_pass_old, $db_hashed_pass_old )) {
 
  return (['status' => 'error', 'message' => 'Old Password is incorrect']);

  exit;
 
 }
 
 /*
 if ( $db_hashed_pass_old != $hashed_password_old)
  {
  var_dump("db pass", $db_hashed_pass_old ) ;
  var_dump("old pass", $hashed_password_old );

  return (['status' => 'error', 'message' => 'Old Password is incorrect']);

  exit;
  }
  */
     /* Hash new password */
  $hashed_password_new = password_hash($data['password_new'], PASSWORD_DEFAULT);      
      
 $stmt = $this->db->prepare("UPDATE users SET password_hash = ? WHERE id = ? ");
   
 $res2 =  $stmt->execute([
            $hashed_password_new,
            $user_id
        ]);
   if ($res2) 
   {
   return ['status' => 'success', 'message' => 'Password Changed Successfully.'];
  exit;
   }
    }






/***************************/

    /**
     * Find a user Row by userID
     */
    public function findByUserID($user_id) {
        $stmt = $this->db->prepare("
            SELECT * FROM users
            WHERE id = ? 
        ");
        $stmt->execute([$user_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
  /*&***&*&******/

public function getAllUsersBySchoolID($school_id,$showDeleted=false)
    {
    
    if ($showDeleted) {
    $stmt = $this->db->prepare("SELECT * FROM users WHERE school_id = :school_id ORDER BY id ASC");
} else {
    $stmt = $this->db->prepare("SELECT * FROM users WHERE school_id = :school_id AND is_deleted = 0 ORDER BY id ASC");
}
        $stmt->execute(['school_id' => $school_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

/********  **********(***/

public function getStaffCount($school_id)
{
$showDeleted = false;

$staffRows = $this->getAllUsersBySchoolID($school_id,$showDeleted) ;
return count($staffRows);

}


/********(*(*********/

    /**
     * Find a user by school ID and username.
     */
    public function findBySchoolIDAndUsername($school_id, $username) {
        $stmt = $this->db->prepare("
            SELECT * FROM users
            WHERE school_id = ? AND username = ?
        ");
        $stmt->execute([$school_id, $username]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
  /*&***&*&******/
  
  // Users.php model
public function getUserIdBySchoolAndUsername($school_id, $username) {
    $stmt = $this->pdo->prepare(
        "SELECT id FROM users WHERE school_id = :school_id AND username = :username LIMIT 1"
    );
    $stmt->execute([
        ':school_id' => $school_id,
        ':username' => $username
    ]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    return $user ? $user['id'] : null;
}
    
 /***************/
 
 public function update($id, $data) {
        // update code ...
    }

/*********************/

    public function softDeleteById($id,$admin_id) {
        $stmt = $this->db->prepare("UPDATE users SET is_deleted = 1, deleted_by = ?, deleted_at = NOW()  WHERE id = ?");
  $stmt->execute([$admin_id,$id]);
return $stmt->rowCount() > 0 ; 
    }

/**************/

public function changeRole($id, $role)
{

 $stmt = $this->db->prepare("UPDATE users SET role = ? WHERE id = ?");
return  $stmt->execute([$role,$id]);

}

/*************************/

public function restoreUser($id)
{
    $stmt = $this->db->prepare("
        UPDATE users 
        SET is_deleted = 0 
        WHERE id = :id
    ");
    return $stmt->execute(['id' => (int)$id]);
}


    
/**************/
public function getCreatorIdBySchool($schoolId)
{
    $sql = "SELECT id FROM users WHERE school_id = :school_id AND role = 'creator' LIMIT 1";
    $stmt = $this->db->prepare($sql);
    $stmt->execute(['school_id' => $schoolId]);
    return $stmt->fetchColumn() ?: null;
}

/**************/
public function isAdminOrCreator(int $userId): bool
{
    $stmt = $this->db->prepare(
        "SELECT role
         FROM users
         WHERE user_id = :user_id"
    );

    $stmt->execute([
        'user_id' => $userId
    ]);

    $role = $stmt->fetchColumn();

    return (
        $role === 'admin'
        || $role === 'creator'
    );
}



/**********/

    
    
    
}

?>





















