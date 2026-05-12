<div class="domain-box">

    <div class="section-title">
        PSYCHOMOTOR DOMAIN
    </div>

    <table class="domain-table">

        <tr>
            <th>Skill</th>
            <th>A</th>
            <th>B</th>
            <th>C</th>
            <th>D</th>
            <th>E</th>
        </tr>

        <?php foreach ($student['psychomotor'] as $row): ?>

        <tr>

            <td style="text-align:left">
                <?= $row['domain_name'] ?>
            </td>

            <?php for($i = 5; $i >= 1; $i--): ?>

                <td>
                    <?= ($row['rating'] == $i) ? '✓' : '' ?>
                </td>

            <?php endfor; ?>

        </tr>

        <?php endforeach; ?>

    </table>

</div>
