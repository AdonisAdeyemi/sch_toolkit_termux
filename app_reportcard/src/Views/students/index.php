<div class="container py-4">

    <h3 class="mb-4">
        Class-Students Management
    </h3>

    <form
        method="GET"
        action="/<?= $appName ?>/students">

        <div class="card mb-4">

            <div class="card-body">

                <div class="row">

                    <div class="col-md-4 mb-3">

                        <label class="form-label">
                            Academic Session
                        </label>

                        <select
                            name="session_id"
                            class="form-select"
                            required>

                            <option value="">
                                Select Session
                            </option>

                            <?php foreach ($sessions as $session): ?>

                                <option
                                    value="<?= $session['id'] ?>"
                                    <?= $sessionId == $session['id'] ? 'selected' : '' ?>>

                                    <?= htmlspecialchars($session['session_name']) ?>

                                </option>

                            <?php endforeach; ?>

                        </select>

                    </div>

                    <div class="col-md-4 mb-3">

                        <label class="form-label">
                            Class
                        </label>

                        <select
                            name="class_id"
                            class="form-select">

                            <option value="0">
                                All Classes
                            </option>

                            <?php foreach ($classes as $class): ?>

                                <option
                                    value="<?= $class['id'] ?>"
                                    <?= $classId == $class['id'] ? 'selected' : '' ?>>

                                    <?= htmlspecialchars($class['class_name']) ?>

                                </option>

                            <?php endforeach; ?>

                        </select>

                    </div>

                    <div class="col-md-4 mb-3">

                        <label class="form-label">
                            Search
                        </label>

                        <input
                            type="text"
                            name="search"
                            class="form-control"
                            placeholder="Student name or Enrollment No."
                            value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">

                    </div>

                </div>

                <div class="mt-2">

                    <button class="btn btn-primary">

                        Load Students

                    </button>

                    <button
                        type="button"
                        class="btn btn-success ms-2"
                        id="newStudentBtn">

                        + New Student

                    </button>

                    <button
                        type="button"
                        class="btn btn-outline-primary ms-2"
                        id="existingStudentBtn">

                        + Existing Student

                    </button>

                </div>

            </div>

        </div>

    </form>

    <div class="card">

        <div class="card-header">

            Students

            <span class="badge bg-secondary float-end">

                <?= count($students) ?>

            </span>

        </div>

        <div class="table-responsive">

            <table class="table table-hover align-middle mb-0">

                <thead>

                    <tr>

                        <th style="width:70px;">
                            Passport
                        </th>

                        <th>
                            Student
                        </th>

                        <th>
                            Admission No.
                        </th>

                        <th>
                            Sex
                        </th>

                        <th>
                            Religion
                        </th>

                        <th>
                            Class
                        </th>

                        <th style="width:180px;">
                            Actions
                        </th>

                    </tr>

                </thead>

                <tbody>

                    <?php if (empty($students)): ?>

                        <tr>

                            <td colspan="7" class="text-center text-muted py-4">

                                No students found.

                            </td>

                        </tr>

                    <?php endif; ?>

                    <?php foreach ($students as $student): ?>

                        <tr>

                            <td>

                                <?php if (!empty($student['passport_url'])): ?>

                                    <img
                                        src="<?= htmlspecialchars($student['passport_url']) ?>"
                                        style="width:45px;height:45px;border-radius:50%;object-fit:cover;">

                                <?php else: ?>

                                    👤

                                <?php endif; ?>

                            </td>

                            <td>

                                <?= htmlspecialchars($student['student_name']) ?>

                            </td>

                            <td>

                                <?= htmlspecialchars($student['admission_no'] ?? "Not Assigned") ?>

                            </td>

                            <td>

                                <?= htmlspecialchars($student['sex'] ?? '-') ?>

                            </td>

                            <td>

                                <?= htmlspecialchars($student['religion']) ?>

                            </td>

                            <td>

                                <?= htmlspecialchars($student['class_name']) ?>

                            </td>

                            <td>

                                <button
                                    class="btn btn-sm btn-outline-primary">

                                    Edit

                                </button>

                                <button
                                    class="btn btn-sm btn-outline-danger">

                                    Remove

                                </button>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                </tbody>

            </table>

        </div>

    </div>

</div>

<!-- xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
xxxxxxxxxxxxxxxx -->

<div class="modal fade" id="studentModal" tabindex="-1">

    <div class="modal-dialog modal-lg">

        <div class="modal-content">

            <form
                id="studentForm"
                enctype="multipart/form-data">

                <input
                    type="hidden"
                    id="studentId"
                    name="student_id">

                <input
                    type="hidden"
                    name="session_id"
                    value="<?= $sessionId ?>">

                <div class="modal-header">

                    <h5
                        class="modal-title"
                        id="studentModalTitle">

                        New Student

                    </h5>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal">
                    </button>

                </div>

                <div class="modal-body">

                    <div class="row">

                        <div class="col-md-8 mb-3">

                            <label class="form-label">

                                Full Name

                            </label>

                            <input
                                class="form-control"
                                name="student_name"
                                required>

                        </div>

                        <div class="col-md-4 mb-3">

                            <label class="form-label">

                                Admission No.

                            </label>

                            <input
                                class="form-control"
                                name="admission_no">

                        </div>

                    </div>

                    <div class="row">

                        <div class="col-md-6 mb-3">

                            <label class="form-label">

                                Sex

                            </label>

                            <select
                                class="form-select"
                                name="sex"
                                required>

                                <option value="">
                                    Select
                                </option>

                                <option value="M">
                                    Male
                                </option>

                                <option value="F">
                                    Female
                                </option>

                            </select>

                        </div>

                        <div class="col-md-6 mb-3">

                            <label class="form-label">

                                Religion

                            </label>

                            <select
                                class="form-select"
                                name="religion"
                                required>

                                <option value="">
                                    Select
                                </option>

                                <option value="CRS">
                                    CRS
                                </option>

                                <option value="IRS">
                                    IRS
                                </option>

                            </select>

                        </div>

                    </div>

                    <div class="mb-3">

                        <label class="form-label">

                            Passport

                        </label>

                        <input
                            type="file"
                            class="form-control"
                            name="passport"
                            accept="image/*">

                    </div>

                    <div
                        class="mb-3 text-center">

                        <img
                            id="passportPreview"
                            src=""
                            class="img-thumbnail"
                            style="max-height:120px;display:none;">

                    </div>

                    <div class="mb-3">

                        <label class="form-label">

                            Class

                        </label>

                        <select
                            class="form-select"
                            name="class_id"
                            required>

                            <?php foreach ($classes as $class): ?>

                                <option
                                    value="<?= $class['id'] ?>"
                                    <?= $classId == $class['id'] ? 'selected' : '' ?>>

                                    <?= $class['class_name'] ?>

                                </option>

                            <?php endforeach; ?>

                        </select>

                    </div>

                </div>

                <div class="modal-footer">

                    <button
                        class="btn btn-secondary"
                        data-bs-dismiss="modal"
                        type="button">

                        Cancel

                    </button>

                    <button
                        class="btn btn-primary"
                        id="saveStudentBtn"
                        type="submit">

                        Save Student

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>


<!-- xxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx -->


<script>

const studentModal =
    new bootstrap.Modal(
        document.getElementById('studentModal')
    );
    
const passportInput =
    document.querySelector('#studentForm input[name="passport"]');

const passportPreview =
    document.getElementById('passportPreview');


document
.getElementById('newStudentBtn')
.addEventListener('click', () => {

    document.getElementById('studentModalTitle').textContent =
        'New Student';

    document.getElementById('studentForm').reset();

    document.getElementById('studentId').value = '';

    passportPreview.src = '';
    passportPreview.style.display = 'none';
    passportInput.value = '';

    studentModal.show();

});

/*******************/

passportInput.addEventListener('change', function () {

    const file = this.files[0];

    if (!file) {

        passportPreview.src = '';
        passportPreview.style.display = 'none';
        return;

    }

    passportPreview.src =
        URL.createObjectURL(file);

    passportPreview.style.display = 'inline-block';

});

/***************************/




/*******************/

document
.getElementById('studentForm')
.addEventListener('submit', async function (e) {

    e.preventDefault();

    const form = this;

    const saveBtn =
        document.getElementById('saveStudentBtn');

    saveBtn.disabled = true;
    saveBtn.textContent = 'Saving...';

    try {

        const response = await fetch(
            '/<?= $appName ?>/students/save',
            {
                method: 'POST',
                body: new FormData(form)
            }
        );

        const result = await response.json();

        if (result.status === 'success') {

            studentModal.hide();

            showFlash([
                {
                    type: 'success',
                    text: result.message
                }
            ]);

            location.reload();

        } else {

            showFlash([
                {
                    type: 'danger',
                    text: result.message
                }
            ]);

        }

    } catch {

        showFlash([
            {
                type: 'danger',
                text: 'Network error'
            }
        ]);

    }

    saveBtn.disabled = false;
    saveBtn.textContent = 'Save Student';

});

/************/


/********************/


/****************/



</script>













