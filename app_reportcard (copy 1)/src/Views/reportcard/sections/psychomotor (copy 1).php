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

        <?php
        $skills = [
            'Handling Of Tools',
            'Drawing / Painting',
            'Handwriting',
            'Public Speaking',
            'Speech Fluency',
            'Sports & Games'
        ];

        foreach ($skills as $skill):
            $rand = rand(1,5);
        ?>

        <tr>
            <td style="text-align:left"><?= $skill ?></td>

            <?php for($i=1; $i<=5; $i++): ?>
                <td><?= ($i == $rand) ? '✓' : '' ?></td>
            <?php endfor; ?>

        </tr>

        <?php endforeach; ?>

    </table>

</div>
