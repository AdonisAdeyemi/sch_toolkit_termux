<?php
ini_set("display_errors", 1);

$pageTitle = $pageTitle ?? 'My App' ;
$appName = $_SESSION['appName'] ?? "";

/**  <link rel="stylesheet" href="/myapp/public/auth/auth.css"> **/

echo <<< _END
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">

  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title> $pageTitle </title>
  <link rel="stylesheet" href="/public/shared/assets/css/auth.css"> 
  
  

<script src='https://cdn.jsdelivr.net/npm/eruda'></script>
<script>
  eruda.init();
</script>

</head>
_END;

?>































