
    <div class="section-title">
        PERFORMANCE SUMMARY
    </div>

    <table class="summary-table">

        <tr>
            <td>Total Score Obtainable</td>
            <td><?= $student['total_obtainable'] ?? 0 ?></td>
        </tr>

        <tr>
            <td>Total Score Obtained</td>
            <td><?= $student['all_subjects_total'] ?? 0 ?></td>
        </tr>

        <tr>
            <td>Percentage</td>
            <td><?= $student['average'] ?? 0 ?>%</td>
        </tr>

	<!--
        <tr>
            <td>Overall Grade</td>
            <td><?= $student['overall_grade'] ?? 'A' ?></td>
        </tr>
        -->

        <tr>
            <td>Overall Position</td>
            <td><?= $student['position_text'] ?? '-' ?></td>
        </tr>

        <tr>
            <td>Class Size</td>
            <td><?= $student['class_size'] ?? 0 ?></td>
        </tr>

    </table>










