<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);


/* db pdo */
require_once __DIR__ . '/../config/config_db.php'; /* your PDO setup file */

   $stmt = $pdo->prepare("
            select * from users  where school_id = ? and username = ?");
         $stmt->execute([
           41, "s"
    ]);
    
     $resArr = $stmt->fetchAll(PDO::FETCH_ASSOC); 


// var_dump($resArr);

$db_pw = $resArr[0] ['password_hash'] ;
$in_pw = 111111 ;

echo "hiiiii<br>";
echo password_verify($in_pw, $db_pw ) ? "verified ok" : "verify failed" ;
echo "<br>okkkkk";

?>


