<?php
   
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
     

$dB_PORT= "3306";
$dB_HOST= "sql308.infinityfree.com";
$dB_NAME="if0_40622177_exam_app";
$dB_USER="if0_40622177";
$dB_PASS="QZAQo3eQl3Ud"   ;
    
$pdo = new PDO("mysql:host=$dB_HOST;dbname=$dB_NAME", "$dB_USER", "$dB_PASS");

// Step 1: get duplicates
$duplicates = $pdo->query("
    SELECT question_text, GROUP_CONCAT(id) as ids, COUNT(*) as total
    FROM questions
    GROUP BY question_text
    HAVING total > 1
")->fetchAll(PDO::FETCH_ASSOC);

foreach ($duplicates as $dup) {
    
    $ids = explode(',', $dup['ids']);
    
    
    // Skip first occurrence (keep as is)
    array_shift($ids);

    foreach ($ids as $id) {
        echo $id . "<br>";
        // append a short unique suffix
        $suffix = substr(md5(time() . rand()), 0, 6);
        $new_text = $dup['question_text'] . " [" . $suffix . "]";
        
        $stmt = $pdo->prepare("UPDATE questions SET question_text = ? WHERE id = ?");
        $stmt->execute([$new_text, $id]);
    }
}


?>



