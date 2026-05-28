<!-- templates/head_section.php -->

<table width="100%" cellspacing="0" cellpadding="5" border="1" style="border-collapse:collapse; margin-bottom:10px;">

    <tr>

        <td width="15%" align="center" valign="top">

            <!--img
                src="/app_reportcard/public/assets/logo/logo_01.jpg"
                width="70"
                height="70"
            -->
            
            <!--img
                src="http://localhost:8080/app_reportcard/public/assets/logo/logo_01.jpg"
                width="70"
                height="70"
           
           -->
           
     <img
                src="<?= $logoSrc ?>"
                width="70"
                height="70"
           >
           
            

        </td>

        <td width="70%" align="center" valign="top">

            <div style="font-size:22px; font-weight:bold;">
           <?= $settings['printed_name'] ?>
            </div>
<!--
report_settings] => Array
        (
            [id] => 1
            [school_id] => 1
            [theme] => classic
            [address] => Lagos, Nigeria
            [telephone] => 08012345678
            [primary_color_accent] => #3366ff
            [logo_url] => 
            [logo_position] => 
            [printed_name] => Demo Secondary School
            [logo_watermark] => 0
        )


-->


            <div style="font-size:12px;">
                       <?= $settings['address'] ?>
            </div>

            <div style="font-size:12px;">

<?= $settings['address'] . " | " . 
   $student['student_info']['session'] . 
   " | Term " . 
   $student['student_info']['term']; ?>

        </td>

        <td width="15%" align="center" valign="top">

            <img
                src="<?= $passportSrc ?>"
                width="70"
                height="70"
            >

        </td>

    </tr>

</table>







