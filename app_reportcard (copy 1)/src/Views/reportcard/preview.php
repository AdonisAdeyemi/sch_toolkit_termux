<?php 

$GLOBALS['student'] = $student;
$GLOBALS['settings'] = $settings;
$GLOBALS['class_name'] = $class_name;
$GLOBALS['period'] = $period;


foreach ($students as $student): ?>

    <?php include VIEW_PATH . "/reportcard/themes/classic.php"; ?>
    
    
    <?php endforeach; ?>
