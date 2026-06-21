<h2>Subjects</h2>

<a href="<?= $appName ?>/subjects/create">
    + Add Subject
</a>

<hr>

<?php if (empty($subjects)): ?>
    <p>No subjects found.</p>
<?php else: ?>
    <table border="1" cellpadding="10" cellspacing="0">
        <thead>
            <tr>
                <th>#</th>
                <th>Subject Name</th>
                <th>Actions</th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($subjects as $index => $subject): ?>
                <tr>
                    <td><?= $index + 1 ?></td>

                    <td>
                        <?= htmlspecialchars($subject['subject_name']) ?>
                    </td>

                    <td>
                        <!-- Edit -->
                        <a href="<?= $appName ?>/subjects/edit?id=<?= $subject['id'] ?>">
                            Edit
                        </a>

                        |

                        <!-- Delete -->
                        <form method="POST"
                              action="<?= $appName ?>/subjects/delete"
                              style="display:inline;">
                            
                            <input type="hidden" name="id" value="<?= $subject['id'] ?>">

                            <button type="submit"
                                    onclick="return confirm('Delete this subject?')">
                                Delete
                            </button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
