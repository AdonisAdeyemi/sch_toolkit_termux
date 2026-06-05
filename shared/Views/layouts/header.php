<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$versionNumber = filemtime(__DIR__."/../../../public/shared/assets/js/js_helper.js");

// "appname inputed to this rendered view ::: {$appName}";

echo <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{$title}</title>
  
  
  
  <link rel="stylesheet" href="/public/shared/assets/css/bootstrap.min.css">
  
  
    
  <style>
    body {
      background-color: #f8f9fa;
    }
  </style>
  


  <script src="/shared/public/assets/js/js_helper.js?v=$versionNumber"></script>
  


  <!-- eruda  -->
<script src='https://cdn.jsdelivr.net/npm/eruda'></script>
<script>
  eruda.init();
  </script>

  
</head>

HTML;

//Page-specific CSS
if (!empty($styles)){
   foreach ($styles as $css){
   $cleanedCss = htmlspecialchars($css);
       
    echo  "<link rel='stylesheet' href='$cleanedCss'>" ;
    }
 }
       
echo <<< HTML
<body>
  <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
      <a class="navbar-brand" href="/{$appName}/dashboard">MyApp</a>
    
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
        
          <li class="nav-item"><a class="nav-link" href="/{$appName}/dashboard/">Dashboard</a></li>
          
          <li class="nav-item"><a class="nav-link" href="/{$appName}/user/view/change_password">Change Password</a></li>
          
          <li class="nav-item"><a class="nav-link" href="/{$appName}/api/logout">Logout</a></li>
          
          
        </ul>
      </div>
    </div>
  </nav>

  <main class="container py-4">
  
  <div id="toast-container"
     class="position-fixed top-0 end-0 p-3"
     style="z-index: 1080; max-width: 100vw;">
</div>
  
HTML;

/*
//Flash messages
foreach (getFlash() as $msg)
 {
$type = htmlspecialchars($msg['type']); 
$text = htmlspecialchars($msg['text']);
 
echo <<< HTML
    <div class="alert alert-$type alert-dismissible fade show"> $text
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
HTML;
}
*/

?>


<?php
require 'send_flash_msg_to_js.php';
?>










