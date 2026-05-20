<table class="subject-table">
    <thead>
        <tr>
            <th>Subject</th>
            <th>CA1</th>
            <th>CA2</th>
            <th>Exam</th>
            <th>Total</th>
            <th>Grade</th>
            <th>Remark</th>
            <th>Pos</th>
        </tr>
    </thead>

    <tbody>
        <?php foreach ($student['subjects'] as $sub): ?>
            <tr>
                <td style="text-align:left"><?= $sub['name'] ?></td>
                <td><?= $sub['ca1'] ?? 0 ?></td>
                <td><?= $sub['ca2'] ?? 0 ?></td>
                <td><?= $sub['exam'] ?? 0 ?></td>
                <td><?= $sub['one_subject_total'] ?? 0 ?></td>
                <td><?= $sub['grade'] ?></td>
                <td><?= $sub['grade_remark'] ?></td>
                <td><?= $sub['position'] ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>







