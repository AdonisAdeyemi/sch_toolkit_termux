<!-- templates/domains.php -->

<table width="100%" cellspacing="0" cellpadding="0"
       style="margin-bottom:10px; border-collapse:collapse;">

    <tr>
        <td width="100%" valign="top">

            <!-- AFFECTIVE DOMAIN -->
            <table width="100%" border="1"
                   cellspacing="0"
                   cellpadding="4"
                   style="border-collapse:collapse; margin-bottom:10px; font-size:12px;">

                <tr>
                    <th colspan="6">AFFECTIVE DOMAIN</th>
                </tr>

                <tr>
                    <th>Trait</th>
                    <th>5</th>
                    <th>4</th>
                    <th>3</th>
                    <th>2</th>
                    <th>1</th>
                </tr>

                <?php foreach ($student['affective'] as $row): ?>

                    <tr>
                        <td><?= $row['domain_name'] ?></td>

                        <?php for ($i = 5; $i >= 1; $i--): ?>
   <td align="center" style="font-size:14px; vertical-align:middle;">
                                <?= ((int)$row['rating'] === $i) ? '&#10004;' : '' ?>
                            </td>
                        <?php endfor; ?>
                    </tr>

                <?php endforeach; ?>

            </table>


            <!-- PSYCHOMOTOR DOMAIN -->
            <table width="100%" border="1"
                   cellspacing="0"
                   cellpadding="4"
                   style="border-collapse:collapse; font-size:12px;">

                <tr>
                    <th colspan="6">PSYCHOMOTOR DOMAIN</th>
                </tr>

                <tr>
                    <th>Skill</th>
                    <th>5</th>
                    <th>4</th>
                    <th>3</th>
                    <th>2</th>
                    <th>1</th>
                </tr>

                <?php foreach ($student['psychomotor'] as $row): ?>

                    <tr>
                        <td><?= $row['domain_name'] ?></td>

                        <?php for ($i = 5; $i >= 1; $i--): ?>
                            <td align="center" style="font-size:14px; vertical-align:middle;">
                                <?= ((int)$row['rating'] === $i) ? '&#10004;' : '' ?>
                            </td>
                        <?php endfor; ?>
                    </tr>

                <?php endforeach; ?>

            </table>

        </td>
    </tr>

</table>
