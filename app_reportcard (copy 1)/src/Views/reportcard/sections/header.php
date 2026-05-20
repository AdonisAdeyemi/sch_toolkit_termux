

<div class="watermarkxxx">

    <?php for($i=0; $i<400; $i++): ?>

        <div class="wm-tile">
            <img src="<?=  $settings['logo'] ?? '/app_reportcard/public/uploads/logo/logo_diamond.jpeg' ?>"  />
            <span><?= "MUCH MORE LIFE COLLEGE"??$settings['printed_name'] ?></span>
        </div>

    <?php endfor; ?>

</div>





    <!-- SCHOOL LOGO -->
    <div class="logo-box">

        <?php if (true ?? !empty($settings['logo'])): ?>
        
     
            <!-- img src="<?= '/app_reportcard/public/uploads/logo/logo_01.jpg' ?>"
                 class="school-logo"  -->

            <img src="<?= '/app_reportcard/public/uploads/logo/logo_01.jpg'?? $settings['logo'] ?>"
                 class="school-logo">

        <?php endif; ?>

    </div>
    
    
    
    
    
    

    <!-- SCHOOL INFO -->
    <div class="school-info">

        <div class="school-name">
            <?= $settings['printed_name'] ?>
        </div>

        <div class="sub-info">
        <?= $settings['address'] ?><br>
        <?= $class_name ?> | <?= $session ?> | Term <?= $term ?></div>

    </div>










    
    <!-- STUDENT PASSPORT -->
    <div class="passport-box">
                 
        <?php if (!empty($student['passport'])): ?>

            <img src="<?= '/app_reportcard/public/uploads/passport/pass_sch01_std01.png' ??  $student['passport'] ?>"
                 class="student-passport">

        <?php else: ?>

            <img src="/app_reportcard/public/uploads/passport/passport_avatar.png"
                 class="student-passport">

        <?php endif; ?>

    </div>

    
    
    
    
    










