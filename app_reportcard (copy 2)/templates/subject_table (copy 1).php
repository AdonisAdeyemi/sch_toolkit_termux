<table width="100%" border="1" cellspacing="0" cellpadding="2"
       style="border-collapse:collapse; margin-bottom:10px; font-size:11px;">



<tr>

    <th style="width:35%; height:70px; vertical-align:bottom;">
        Subject
    </th>

    <th style="width:1%; height:70px; vertical-align:bottom;">
        <div style="transform:rotate(270deg); font-size:10px; padding-left:15px;">
            CA1
        </div>
    </th>

    <th style="width:1%; height:70px; vertical-align:bottom;">
        <div style="transform:rotate(270deg); font-size:10px; padding-left:15px;">
            CA2
        </div>
    </th>

    <th style="width:1%; height:70px; vertical-align:bottom;">
        <div style="transform:rotate(270deg); font-size:10px; padding-left:15px;">
            EXAM
        </div>
    </th>

    <th style="width:6%; height:70px; vertical-align:bottom;">
        <div style="transform:rotate(270deg); font-size:10px; padding-left:15px;">
            TOTAL
        </div>
    </th>

    <th style="width:6%; height:70px; vertical-align:bottom;">
        <div style="transform:rotate(270deg); font-size:10px; padding-left:15px;">
            GRADE
        </div>
    </th>

    <th style="width:12%; height:70px; vertical-align:bottom;">
        <div style="transform:rotate(270deg); font-size:10px; padding-left:15px;">
            REMARK
        </div>
    </th>

    <th style="width:1%; height:70px; vertical-align:bottom;">
        <div style="transform:rotate(270deg); font-size:10px; padding-left:15px;">
            POS
        </div>
    </th>

</tr>


    <?php foreach ($student['subjects'] as $subject): ?>

        <tr>

            <td><?= $subject['name'] ?></td>

            <td align="center"><?= $subject['ca1'] ?></td>
            <td align="center"><?= $subject['ca2'] ?></td>
            <td align="center"><?= $subject['exam'] ?></td>

            <td align="center"><?= $subject['one_subject_total'] ?></td>
            <td align="center"><?= $subject['grade'] ?></td>
            <td align="center"><?= $subject['grade_remark'] ?></td>
            <td align="center"><?= $subject['position'] ?></td>

        </tr>

    <?php endforeach; ?>

</table>
