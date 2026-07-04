<h2>Classes</h2>

<form method="POST" action="/admin/classes/store">
    <input type="text" name="class_name" placeholder="e.g SS1, SS2" required>
    <button type="submit">Create Class</button>
</form>

<hr>

<h3>Existing Classes</h3>

<ul>
    <?php foreach ($classes as $class): ?>
        <li>
            <?= htmlspecialchars($class['class_name']) ?>

            <form method="POST" action="/admin/classes/delete" style="display:inline;">
                <input type="hidden" name="id" value="<?= $class['id'] ?>">
                <button type="submit">Delete</button>
            </form>
        </li>
    <?php endforeach; ?>
</ul>
