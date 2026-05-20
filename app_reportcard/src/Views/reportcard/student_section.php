<!-- templates/student_section.php -->




    <?php include __DIR__ . '/head_section.php'; ?>




    <!-- STUDENT INFO -->

    <table width="100%"
           border="1"
           cellspacing="0"
           cellpadding="4"
           style="border-collapse:collapse; margin-bottom:10px;">

        <tr>

            <td>
                <strong>Name:</strong>

                <?= $student['name'] ?>
            </td>

            <td align="center">

                <strong>Class:</strong>

                <?=  'xxx' ?? $student['class_name']  ?>

            </td>

        </tr>

    </table>




    <?php include __DIR__ . '/attendance.php'; ?>


<table width="100%" cellspacing="0" cellpadding="0" style="border-collapse:collapse; margin-bottom:10px;">

    <tr>

        <!-- LEFT SIDE: SUBJECT TABLE -->
        <td width="70%" valign="top" style="padding-right:8px;">

            <?php include __DIR__ . '/subject_table.php'; ?>
            <?php include __DIR__ . '/summary.php'; ?>

        </td>


        <!-- RIGHT SIDE: DOMAINS + LEGEND -->
        <td width="30%" valign="top">

            <?php include __DIR__ . '/domains.php'; ?>

            <div style="margin-top:10px;"></div>

            <?php include __DIR__ . '/grade_legend.php'; ?>

        </td>

    </tr>

</table>





    <!-- FOOTER -->

    <table width="100%"
           border="1"
           cellspacing="0"
           cellpadding="4"
           style="border-collapse:collapse;">

        <tr>

            <td align="right">

                Generated on <?= date('Y-m-d') ?>

            </td>

        </tr>

    </table>

</div>

<div class="page-break"></div>










