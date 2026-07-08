<h2 class="mb-4">
    Assign Subjects to Classes
</h2>

<p class="text-muted">
    Choose a class below to assign or manage its subjects.
</p>

<div class="table-responsive">

<table class="table table-bordered table-hover">

<thead>

<tr>
    <th>Class</th>
    <th>Students</th>
    <th>Action</th>
</tr>

</thead>

<tbody>

<?php foreach ($classes as $class): ?>

<tr>

    <td>
        <?= htmlspecialchars($class['class_name']) ?>
    </td>

    <td>
        <?= (int)$class['student_count'] ?>
    </td>

    <td>

        <a
            class="btn btn-primary btn-sm"
            href="/<?= $appName ?>/classes/<?= $class['id'] ?>/subjects">

            Assign Subjects

        </a>

    </td>

</tr>

<?php endforeach; ?>

</tbody>

</table>

</div>
