A clean approach is to add a Delete button per row and a Show Deleted toggle above the table.

1. Card header

Replace the header with:

<div class="card-header d-flex justify-content-between align-items-center">

    <div>

        Student Registry

        <span class="badge bg-secondary">

            <?= count($students) ?>

        </span>

    </div>

    <div>

        <a
            href="?show_deleted=<?= $showDeleted ? 0 : 1 ?>"
            class="btn btn-sm btn-outline-secondary">

            <?= $showDeleted
                ? 'Show Active'
                : 'Show Deleted' ?>

        </a>

    </div>

</div>

Your controller should read:

$showDeleted = !empty($_GET['show_deleted']);

and pass it to the view.


---

2. Actions column

Replace:

<td>

    <button
        class="btn btn-sm btn-outline-primary editStudentBtn"
        data-student-id="<?= $student['id'] ?>">

        Edit

    </button>

</td>

with:

<td>

    <button
        class="btn btn-sm btn-outline-primary editStudentBtn"
        data-student-id="<?= $student['id'] ?>">

        Edit

    </button>

    <?php if (empty($student['is_deleted'])): ?>

        <button
            class="btn btn-sm btn-outline-danger deleteStudentBtn"
            data-student-id="<?= $student['id'] ?>">

            Delete

        </button>

    <?php else: ?>

        <button
            class="btn btn-sm btn-outline-success restoreStudentBtn"
            data-student-id="<?= $student['id'] ?>">

            Restore

        </button>

    <?php endif; ?>

</td>


---

3. Soft delete route

POST /student_registry/delete

Controller:

public function delete()

Model:

UPDATE report_students
SET is_deleted = 1
WHERE school_id = ?
AND id = ?


---

4. Restore route

POST /student_registry/restore

Controller:

public function restore()

Model:

UPDATE report_students
SET is_deleted = 0
WHERE school_id = ?
AND id = ?


---

5. Controller index()

$showDeleted = !empty($_GET['show_deleted']);

$students = $this->studentModel->getStudents(
    $schoolId,
    $showDeleted
);


---

6. Model

public function getStudents(
    int $schoolId,
    bool $showDeleted = false
): array

$sql = "
SELECT ...
FROM report_students
WHERE school_id = ?
";

$params = [$schoolId];

$sql .= $showDeleted
    ? " AND is_deleted = 1"
    : " AND is_deleted = 0";

This gives you the same pattern you've already used for Classes and Subjects, keeping the UI consistent across your application.










