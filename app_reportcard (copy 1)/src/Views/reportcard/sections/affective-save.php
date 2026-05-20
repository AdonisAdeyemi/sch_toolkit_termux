<div class="xxxdomain-box">



    <div class="xxxsection-title">
        AFFECTIVE DOMAIN
    </div>

    <table class="xxxdomain-table" style="    border:1px solid #000;
    padding:0;
    text-align:center;">

        <tr>
            <th style="padding:0;">Trait</th>
            <th style="padding:0;">A</th>
            <th style="padding:0;">B</th>
            <th style="padding:0;">C</th>
            <th style="padding:0;">D</th>
            <th style="padding:0;">E</th>
        </tr>

        <?php foreach ($student['affective'] as $row): ?>

        <tr>

                            <!-- ?= 'x'??$row['domain_name'] ?-->
                            
            <td style="text-align:left;
             border:1px solid #000;
    padding:0;
    text-align:center;"
            ">'x'</td>

            <?php for($i = 5; $i >= 1; $i--): ?>

      <!-- ?= ($row['rating'] == $i) ? 'X' : '' ? -->
                <td style="text-align:left;  border:1px solid #000;
    padding:0;
    text-align:center;">'X'</td>

            <?php endfor; ?>

        </tr>

        <?php endforeach; ?>

    </table>

</div>













