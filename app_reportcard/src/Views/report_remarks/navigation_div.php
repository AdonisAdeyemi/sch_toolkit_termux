
        <!-- STUDENT NAVIGATION -->
        <div class="card mb-3">

            <div class="card-body">

                <div class="d-flex justify-content-between align-items-center">

<!--  xxxxxxxxxxxxxxxxxxxxxxxxxxxx
xxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
xxxxxxxxxxxxxxxxxxxxxxxxxxxx
Conditional previous button >> for 1st & last-->
<?php if ($isFirstStudent): ?>
    <button
        class="btn btn-outline-secondary"
        disabled>
        ← Prev
    </button>
<?php else: ?>

    <a
        class="btn btn-outline-secondary nav-link-confirm"
        href="/<?= $appName ?>/report-remarks?class_id=<?= $classId ?>&period_id=<?= $periodId ?>&index=<?= $prevIndex ?>">
        ← Prev
    </a>
<?php endif; ?>


                    <div class="text-center">

                        <h5 class="mb-1">
                            <?= e($currentStudent['student_name']) ?>
                        </h5>

                        <div class="text-muted">
                            Admission No:
                            <?= e($currentStudent['admission_no'] ?? '-') ?>
                        </div>

                        <small>
                            Student <?= $studentIndex + 1 ?>
                            of <?= $totalStudents ?>
                        </small>

                    </div>

<!--  xxxxxxxxxxxxxxxxxxxxxxxxxxxx
xxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
xxxxxxxxxxxxxxxxxxxxxxxxxxxx
Conditional next button >> for 1st & last-->

<?php if ($isLastStudent): ?>
    <button
        class="btn btn-outline-secondary"
        disabled>
        Next →
    </button>
<?php else: ?>
    <a
        class="btn btn-outline-secondary nav-link-confirm"
        href="/<?= $appName ?>/report-remarks?class_id=<?= $classId ?>&period_id=<?= $periodId ?>&index=<?= $nextIndex ?>">
        Next →
    </a>
<?php endif; ?>



                </div>

            </div>

        </div>









