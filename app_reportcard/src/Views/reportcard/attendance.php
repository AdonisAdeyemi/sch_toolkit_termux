<table width="100%" border="0" cellspacing="0" cellpadding="0">

    <tr>

        <!-- LEFT COLUMN: ATTENDANCE -->
        <td width="50%" valign="top" style="padding-right:5px;">

            <table width="100%" border="1" cellspacing="0" cellpadding="4"
                   style="border-collapse:collapse; font-size:12px;">

                <tr>
                    <th colspan="2" align="center">
                        ATTENDANCE RECORD
                    </th>
                </tr>

                <tr>
                    <td>Days School Opened</td>
                    <td align="center"><?= $student['student_info']['days_open'] ?></td>
                </tr>

                <tr>
                    <td>Days Present</td>
                    <td align="center"><?= $student['student_info']['days_present'] ?></td>
                </tr>

                <tr>
                    <td>Days Absent</td>
                    <td align="center"><?= $student['student_info']['days_absent'] ?></td>
                </tr>

            </table>

        </td>


        <!-- RIGHT COLUMN: VACATION -->
        <td width="50%" valign="top" style="padding-left:5px; font-size:12px;">

            <table width="100%" border="0" cellspacing="0" cellpadding="4">

                <tr>
                    <td width="40%"><strong>Date of Vacation:</strong></td>
                    <td>
                        <span style="display:inline-block; border-bottom:1px solid #000; min-width:140px; text-align:center;">
                            <?= $control_panel['school_vacates'] ?? '________________' ?>
                        </span>
                    </td>
                </tr>

                <tr>
                    <td><strong>Next Term Begins:</strong></td>
                    <td>
                        <span style="display:inline-block; border-bottom:1px solid #000; min-width:140px; text-align:center;">
                            <?= $control_panel['school_resumes'] ?? '________________' ?>
                        </span>
                    </td>
                </tr>

            </table>

        </td>

    </tr>

</table>
