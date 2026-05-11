<div class="domain-box">

    <div class="section-title">
        AFFECTIVE DOMAIN
    </div>

    <table class="domain-table">

        <tr>
            <th>Trait</th>
            <th>A</th>
            <th>B</th>
            <th>C</th>
            <th>D</th>
            <th>E</th>
        </tr>

        <?php
        $traits = [
            'Attentiveness',
            'Honesty',
            'Neatness',
            'Punctuality / Assembly',
            'Self-Control / Calmness',
            'Obedience',
            'Reliability',
            'Sense Of Responsibility',
            'Relationship With Others'
        ];

        foreach ($traits as $trait):
            $rand = rand(1,5);
        ?>

        <tr>
            <td style="text-align:left"><?= $trait ?></td>

            <?php for($i=1; $i<=5; $i++): ?>
                <td><?= ($i == $rand) ? '✓' : '' ?></td>
            <?php endfor; ?>

        </tr>

        <?php endforeach; ?>

    </table>

</div>
