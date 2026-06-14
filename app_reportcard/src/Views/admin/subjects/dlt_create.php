<h2>Create Subject</h2>

<?php if (!empty($error)): ?>
    <p style="color:red;">
        <?= $error ?>
    </p>
<?php endif; ?>



<form method="POST" action="<?= appname_url('/admin/subjects/store') ?>">
    <input type="text" name="subject_name" placeholder="Enter subject name">

    <button type="submit">
        Save Subject
    </button>
</form>
