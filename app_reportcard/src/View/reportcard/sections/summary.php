<div class="summary">
    <table>
        <tr>
            <td><strong>Total:</strong> <?= $student['all_subjects_total'] ?></td>
            <td><strong>Average:</strong> <?= $student['average'] ?></td>
        </tr>

        <tr>
            <td><strong>Teacher Comment:</strong> <?= $student['teacher_exam_comment'] ?></td>
        </tr>

        <tr>
            <td><strong>Principal Comment:</strong> <?= $student['principal_exam_comment'] ?></td>
        </tr>
    </table>
</div>

<div class="remarks">
    <strong>Remark:</strong> <?= $student['remark'] ?>
</div>
