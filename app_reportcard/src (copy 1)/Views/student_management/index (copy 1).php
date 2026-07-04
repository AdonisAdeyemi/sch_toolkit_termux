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
