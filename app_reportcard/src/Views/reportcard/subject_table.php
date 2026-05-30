<table width="100%" border="1" cellspacing="0" cellpadding="2"
       style="border-collapse:collapse; margin-bottom:10px; font-size:11px; table-layout:fixed;">

<tr>
<th style = "<?= $color_preference_style ?>"  align="center"  colspan="8">
AFFECTIVE DOMAIN
</th>
<tr>

<tr>

    <th style="width:35%; height:70px; vertical-align:bottom;">
        Subject
    </th>

    <th style="width:5%; height:70px; vertical-align:bottom; text-align:center; font-size:10px;">
        <?php echo verticalText("CA1") . "<br>(10)"; ?>
    </th>

    <th style="width:5%; height:70px; vertical-align:bottom; text-align:center; font-size:10px;">
        <?php echo verticalText("CA2"). "<br>(20)"; ?>
    </th>

    <th style="width:5%; height:70px; vertical-align:bottom; text-align:center; font-size:10px;">
        <?php echo verticalText("EXAM"). "<br>(70)"; ?>
    </th>

    <th style="width:8%; height:70px; vertical-align:bottom; text-align:center; font-size:10px;">
        <?php echo verticalText("TOTAL"). "<br>(100)"; ?>
    </th>

    <th style="width:8%; height:70px; vertical-align:bottom; text-align:center; font-size:10px;">
        <?php echo verticalText("GRADE"); ?>
    </th>

    <th style="width:15%; height:70px; vertical-align:bottom; text-align:center; font-size:10px;">
        <?php echo verticalText("REMARK"); ?>
    </th>

    <th style="width:4%; height:70px; vertical-align:bottom; text-align:center; font-size:10px;">
        <?php echo verticalText("POS"); ?>
    </th>

</tr>


    <?php foreach ($student['subjects'] as $subject): ?>

        <tr>

            <td><?= $subject['subject_name'] ?></td>

            <td align="center"><?= $subject['ca1'] ?></td>
            <td align="center"><?= $subject['ca2'] ?></td>
            <td align="center"><?= $subject['exam'] ?></td>

            <td align="center"><?= $subject['subject_total'] ?></td>
            <td align="center"><?= $subject['subject_grade'] ?></td>
            <td align="center"><?= $subject['subject_grade_remark'] ?></td>
            <td align="center"><?= $subject['position_in_subject_text'] ?? $subject['position_in_subject'] ?></td>
            


        </tr>

    <?php endforeach; ?>

</table>
