<?php
$classId = $classId ?? 0;
$periodId = $periodId ?? 0;
$currentStudent = $currentStudent ?? null;
$results = $results ?? [];
$attendance = $attendance['days_present'] ?? '';
$comments = $comments ?? [];
$domains = $domains ?? [];
$domainScores = $domainScores ?? [];
?>

<div class="container mt-3">

    <h4>Report Card Remarks Entry</h4>

    <!-- FILTER SECTION -->
    <form method="GET" class="row mb-3">

        <div class="col-md-4">
            <label>Class</label>
            <select name="class_id" class="form-control" required>
                <option value="">Select Class</option>
                <?php foreach ($classes as $class): ?>
                    <option value="<?= $class['id'] ?>"
                        <?= $classId == $class['id'] ? 'selected' : '' ?>>
                        <?= $class['label'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-md-4">
            <label>Period</label>
            <select name="period_id" class="form-control" required>
                <option value="">Select Period</option>
                <?php foreach ($periods as $p): ?>
                    <option value="<?= $p['id'] ?>"
                        <?= $periodId == $p['id'] ? 'selected' : '' ?>>
                        <?= $p['period_name'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-md-4 mt-4">
            <button class="btn btn-primary mt-2">Load</button>
        </div>

    </form>

    <?php if ($currentStudent): ?>

        <!-- NAVIGATION -->
        <div class="d-flex justify-content-between align-items-center mb-3">

            <a class="btn btn-secondary"
               href="?class_id=<?= $classId ?>&period_id=<?= $periodId ?>&index=<?= $prevIndex ?>">
                ← Previous
            </a>

            <div>
                <strong>
                    <?= $currentStudent['student_name'] ?>
                </strong>
                <br>
                <small>
                    <?= $studentIndex + 1 ?> / <?= $totalStudents ?>
                </small>
            </div>

            <a class="btn btn-secondary"
               href="?class_id=<?= $classId ?>&period_id=<?= $periodId ?>&index=<?= $nextIndex ?>">
                Next →
            </a>

        </div>

        <!-- FORM WRAPPER -->
        <form id="studentForm">

            <input type="hidden" name="student_id" value="<?= $currentStudent['id'] ?>">
            <input type="hidden" name="period_id" value="<?= $periodId ?>">

            <!-- RESULTS TABLE -->
            <div class="card mb-3">
                <div class="card-header">Academic Results</div>
                <div class="card-body">

                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th>Subject</th>
                            <th>CA1</th>
                            <th>CA2</th>
                            <th>Exam</th>
                            <th>Total</th>
                            <th>Grade</th>
                        </tr>
                        </thead>

                        <tbody>
                        <?php foreach ($results as $r): ?>
                            <tr>
                                <td><?= $r['subject_name'] ?></td>
                                <td><?= $r['ca1_score'] ?></td>
                                <td><?= $r['ca2_score'] ?></td>
                                <td><?= $r['exam_score'] ?></td>
                                <td><?= $r['total_score'] ?></td>
                                <td><?= $r['grade'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>

                </div>
            </div>

            <!-- ATTENDANCE -->
            <div class="card mb-3">
                <div class="card-header">Attendance</div>
                <div class="card-body">
                    <input type="number"
                           name="attendance"
                           class="form-control"
                           value="<?= $attendance ?>"
                           placeholder="Days Present">
                </div>
            </div>

            <!-- COMMENTS -->
            <div class="card mb-3">
                <div class="card-header">Comments</div>
                <div class="card-body">

                    <label>Class Teacher Comment</label>
                    <textarea name="comments[class_teacher]"
                              class="form-control mb-3"
                              rows="3"><?= $comments['class_teacher'] ?? '' ?></textarea>

                    <label>Principal Comment</label>
                    <textarea name="comments[principal]"
                              class="form-control"
                              rows="3"><?= $comments['principal'] ?? '' ?></textarea>

                </div>
            </div>

            <!-- DOMAIN RATINGS -->
            <div class="card mb-3">
                <div class="card-header">Affective / Psychomotor</div>
                <div class="card-body">

                    <div class="row">

                        <?php foreach ($domains as $d): ?>

                            <div class="col-md-6 mb-2">

                                <label>
                                    <?= $d['domain_name'] ?>
                                </label>

                                <select class="form-control"
                                        name="domains[<?= $d['id'] ?>]">

                                    <?php for ($i = 1; $i <= 5; $i++): ?>

                                        <option value="<?= $i ?>"
                                            <?= (isset($domainScores[$d['id']]) &&
                                                 $domainScores[$d['id']] == $i)
                                                ? 'selected' : '' ?>>

                                            <?= $i ?>

                                        </option>

                                    <?php endfor; ?>

                                </select>

                            </div>

                        <?php endforeach; ?>

                    </div>

                </div>
            </div>

            <!-- SAVE BUTTON -->
            <button type="button"
                    id="saveBtn"
                    class="btn btn-success btn-lg">
                Save Student
            </button>

        </form>

    <?php else: ?>

        <div class="alert alert-info">
            Select class and period to begin.
        </div>

    <?php endif; ?>

</div>

<!-- AJAX -->
<script>
document.getElementById('saveBtn')?.addEventListener('click', function () {

    const form = document.getElementById('studentForm');
    const formData = new FormData(form);

    fetch('<?= app_url("report-remarks/save") ?>', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(res => {

        if (res.status === 'success') {
            alert('Saved successfully');
        } else {
            alert(res.message || 'Error saving');
        }

    })
    .catch(err => {
        alert('Network error');
    });

});
</script>
