<?php
/*
$classId        = $classId ?? 0;
$periodId       = $periodId ?? 0;
$currentStudent = $currentStudent ?? null;
$results        = $results ?? [];
$attendanceDays = $attendance['days_present'] ?? '';
$comments       = $comments ?? [];
$domains        = $domains ?? [];
$domainScores   = $domainScores ?? [];
*/

function e($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

?>

<div class="container py-3">

    <h3 class="mb-4">Report Card Remarks Entry</h3>

    <!-- FILTERS -->
    <form method="GET" action="/<?= $appName?>/report-remarks" class="row g-3 mb-4">

        <div class="col-md-5">
            <label class="form-label">Class</label>

            <select name="class_id" class="form-select" required>
                <option value="">Select Class</option>

                <?php foreach ($classes as $class): ?>
                    <option
                        value="<?= (int) $class['id'] ?>"
                        <?= ((int) $classId === (int) $class['id']) ? 'selected' : '' ?>>
                        <?= e($class['class_name']) ?>
                    </option>
                <?php endforeach; ?>

            </select>
        </div>

        <div class="col-md-5">
            <label class="form-label">Period</label>

            <select name="period_id" class="form-select" required>
                <option value="">Select Period</option>

                <?php foreach ($periods as $period): ?>
                    <option
                        value="<?= (int) $period['id'] ?>"
                        <?= ((int) $periodId === (int) $period['id']) ? 'selected' : '' ?>>
                        <?= e($period['period_name']) ?>
                    </option>
                <?php endforeach; ?>

            </select>
        </div>

        <div class="col-md-2 d-flex align-items-end">
            <button class="btn btn-primary w-100">
                Load
            </button>
        </div>

    </form>

    <?php if ($currentStudent): ?>

<?php require 'navigation_div.php' ?>

        <form id="studentForm">

            <input
                type="hidden"
                name="student_id"
                value="<?= (int) $currentStudent['id'] ?>">

            <input
                type="hidden"
                name="period_id"
                value="<?= (int) $periodId ?>">

            <!-- RESULTS -->
            <div class="card mb-3">

                <div class="card-header">
                    Academic Results
                </div>

                <div class="card-body table-responsive">

                    <table class="table table-bordered table-striped">

                        <thead>
                        <tr class="lh-1">
                            <th>Subject</th>
                            <th><?= verticalText('CA1') ?></th>
                            <th><?= verticalText('CA2') ?></th>
                            <th><?= verticalText('EXAM') ?></th>
                            <th><?= verticalText('TOTAL') ?></th>
                            <th><?= verticalText('GRADE') ?></th>
                        </tr>
                        </thead>

                        <tbody>

                        <?php foreach ($results as $result): ?>

                            <tr>
                                <td><?= e($result['subject_name']) ?></td>
                                <td><?= e($result['ca1_score']) ?></td>
                                <td><?= e($result['ca2_score']) ?></td>
                                <td><?= e($result['exam_score']) ?></td>
                                <td><?= e($result['total_score']) ?></td>
                                <td><?= e($result['grade']) ?></td>
                            </tr>

                        <?php endforeach; ?>

                        </tbody>

                    </table>

                </div>

            </div>

            <!-- ATTENDANCE -->
            <div class="card mb-3">

                <div class="card-header">
                    Attendance (Max for the term set by Admin to : <?= $max_attendance?>)
                </div>

                <div class="card-body">

                    <label class="form-label">
                        Days Present
                    </label>

                    <input
                        type="number"
                        min="0"
                        max="<?= $max_attendance ?>"
                        class="form-control"
                        id="attendance"
                        name="attendance"
                        value="<?= e($attendance['days_present'] ) ?>">

                </div>

            </div>

            <!-- COMMENTS -->
            <div class="card mb-3">

                <div class="card-header">
                    Comments
                </div>

                <div class="card-body">

                    <div class="mb-3">

                        <label class="form-label">
                            Class Teacher Comment
                        </label>

                        <textarea
                            name="comments[class_teacher]"
                            rows="4"
                            class="form-control"><?= e($comments['class_teacher'] ?? '') ?></textarea>

                    </div>

                    <div>

                        <label class="form-label">
                            Principal Comment
                        </label>

                        <textarea
                            name="comments[principal]"
                            rows="4"
                            class="form-control"><?= e($comments['principal'] ?? '') ?></textarea>

                    </div>

                </div>

            </div>

            <!-- DOMAIN SCORES -->
            <div class="card mb-3">

                <div class="card-header">
        Affective & Psychomotor Ratings (5=High, 1=Low)
                </div>

                <div class="card-body">

                    <div class="row">

                        <?php foreach ($domains as $domain): ?>

                            <div class="col-md-6 mb-3">

                                <label class="form-label">
                                    <?= e($domain['domain_name']) ?>
                                </label>

 <select
 class="form-select"
                                    name="domains[<?= (int) $domain['id'] ?>]">

 <?php
 for ($rating = 5; $rating >= 1; $rating--): ?>

      <option   value="<?= $rating ?>"
   <?= (($domainScores[$domain['id']] ?? '') == $rating)
  ? 'selected'
   : '' ?>>

  <?= $rating ?>

     </option>

<?php endfor; ?>

                                </select>

                            </div>

                        <?php endforeach; ?>

                    </div>

                </div>

            </div>

            <div class="mb-3">

                <button
                    type="button"
                    id="saveBtn"
                    class="btn btn-success">
                    Save Student
                </button>

                <span
                    id="saveStatus"
                    class="ms-3 text-muted">
                </span>
                <br>
          <span id="dirtyIndicator" class="text-warning d-none">
    Unsaved changes
</span>

            </div>

        </form>
        
    
<?php require 'navigation_div.php' ?>
        

    <?php else: ?>

        <div class="alert alert-info">
            Select a class and period to begin.
        </div>

    <?php endif; ?>

</div>

<script>

document.getElementById('saveBtn')?.addEventListener('click', async function () {

    const status = document.getElementById('saveStatus');

    status.textContent = 'Saving...';

    try {

        const form = document.getElementById('studentForm');

        const response = await fetch(
            '/<?= $appName ?>/report-remarks/save',
            {
                method: 'POST',
                body: new FormData(form)
            }
        );

        const result = await response.json();

        if (result.status === 'success') {
        
        hasUnsavedChanges = false;
        
    document
    .getElementById('dirtyIndicator')
    ?.classList.add('d-none');
        
let message =  'Saved successfully';

            status.className = 'ms-3 text-success';
            status.textContent = message;
            
//Flash msg
         showFlash
         (
         [
         {'type':"success",'text': message }
         ]
         )
         
         

        } else {
        
 let message = result.message || 'Save failed';

            status.className = 'ms-3 text-danger';
            status.textContent = message ;
            
    //Flash msg
         showFlash
         (
         [
         {'type':"danger",'text': message }
         ]
         )

        }

    } catch (error) {
    
 let message = 'Network error';

        status.className = 'ms-3 text-danger';
        status.textContent = message ;
        
        //Flash msg
         showFlash
         (
         [
         {'type':"danger",'text': message }
         ]
         )

    }

});

/*xxxxxxxxxxxxxxxxxxxxxxxxxxxxx
xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx*/

document.getElementById('attendance')?.addEventListener("keyup", function () {

    const max = Number(this.max);

    if (this.value > max) {
        this.setCustomValidity(`Max allowed is ${max}`);
    } else {
        this.setCustomValidity("");
    }

    this.reportValidity();

});

/*****************
*************************/
const form = document.getElementById('studentForm');

form?.addEventListener('input', () => {
    hasUnsavedChanges = true;
    
 document
    .getElementById('dirtyIndicator')
    ?.classList.remove('d-none');
    
});

/********
**************/
window.addEventListener('beforeunload', function (e) {

    if (!hasUnsavedChanges) {
        return;
    }

    e.preventDefault();
    e.returnValue = '';

});
/**********
******************/
document.querySelectorAll('.nav-link-confirm').forEach(link => {

    link.addEventListener('click', function (e) {

        if (!hasUnsavedChanges) {
            return;
        }

        const proceed = confirm(
            'You have unsaved changes. Continue without saving?'
        );

        if (!proceed) {
            e.preventDefault();
        }

    });

});



</script>








