<!-- templates/summary.php -->
<table width="100%" border="1" cellspacing="0" cellpadding="4"
       style="border-collapse:collapse; margin-bottom:10px;">

    <tr
    style = "<?= $color_preference_style ?>"
    >
        <th colspan="4">
            PERFORMANCE SUMMARY
        </th>
    </tr>

    <tr>
        <td><strong>Total Score Obtained</strong></td>
        <td align="center">
            <?= $student['student_info']['all_subjects_total'] ?>
        </td>

        <td><strong>Total Obtainable</strong></td>
        <td align="center">
            <?= $student['student_info']['total_obtainable'] ?>
        </td>
    </tr>

    <tr>
        <td><strong>Average</strong></td>
        <td align="center">
            <?= $student['student_info']['average'] ?>%
        </td>

        <td><strong>Position</strong></td>
        <td align="center">
            <?= $student['student_info']['position_in_class_text'] 
            ??
             $student['student_info']['position_in_class'] ?>
        </td>
    </tr>

    <tr>
        <td><strong>Class Size</strong></td>
        <td align="center">
            <?= $student['student_info']['class_size'] ?>
        </td>

        <td><strong>Remark</strong></td>
        <td align="center">
            <?= $student['student_info']['average_remark'] ?>
        </td>
    </tr>

</table>


<table width="100%" border="1" cellspacing="0" cellpadding="4"
       style="border-collapse:collapse; margin-bottom:10px; font-size:12px;">

    <tr
    style = "<?= $color_preference_style ?>"
    >
        <th colspan="6">CUMULATIVE SCORES</th>
    </tr>

    <tr>
        <td><strong>1st Term</strong></td>
        <td align="center"><?= $student['student_info']['term_1_total'] ?? '-' ?></td>

        <td><strong>2nd Term</strong></td>
        <td align="center"><?= $student['student_info']['term_2_total'] ?? '-' ?></td>

        <td><strong>3rd Term</strong></td>
        <td align="center"><?= $student['student_info']['term_3_total'] ?? '-' ?></td>
    </tr>

</table>



<table width="100%" border="1" cellspacing="0" cellpadding="4"
       style="border-collapse:collapse; margin-bottom:10px;">

    <tr>
        <td>
            <strong>Teacher Comment:</strong>

            <?= $student['student_info']['teacher_exam_comment'] ?? '-' ?>
        </td>
    </tr>

    <tr>
        <td>
            <strong>Principal Comment:</strong>

            <?= $student['student_info']['principal_exam_comment'] ?? '-' ?>
        </td>
    </tr>

</table>
