<div class="container py-4">

    <h3 class="mb-4">School Period Settings</h3>

<!-- xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx -->
<!-- xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx -->
<!-- xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx -->

<div class="card mt-4 mb-8">

    <div class="card-header bg-primary text-white">

        <strong>Set Active Academic Period</strong>

    </div>

    <div class="card-body p-0">

        <table class="table table-striped table-hover mb-0">

            <thead>

                <tr>

                    <th>Session - Term</th>

                    <th>Status</th>

                    <th width="160">Action</th>

                </tr>

            </thead>

            <tbody>

            <?php foreach ($periods as $period): ?>

                <tr>

                    <td>

                        <?= htmlspecialchars($period['period_name']) ?>

                    </td>

                    <td>

                        <?php if ($period['id'] == $activePeriodId): ?>

                            <span class="badge bg-success">

                                Active

                            </span>

                        <?php else: ?>

                            <span class="badge bg-secondary">

                                Inactive

                            </span>

                        <?php endif; ?>

                    </td>

                    <td>

                        <?php if ($period['id'] == $activePeriodId): ?>

                            <button
                                class="btn btn-success btn-sm"
                                disabled>

                                Current

                            </button>

                        <?php else: ?>

                          <form
    method="POST"
    action="/<?= $appName ?>/school-settings/set-active-period"
    class="d-inline"
    onsubmit="return confirm(
        'Make <?= htmlspecialchars($period['period_name']) ?> the active academic period?\n\nThis will become the default period used throughout the system.'
    );">

    <input
        type="hidden"
        name="period_id"
        value="<?= $period['id'] ?>">

    <button
        type="submit"
        class="btn btn-primary btn-sm">

        Make Active

    </button>

</form>
                        <?php endif; ?>

                    </td>

                </tr>

            <?php endforeach; ?>

            </tbody>

        </table>

    </div>

</div>
<!-- xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx -->
<!-- xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx -->
<!-- xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx -->



    <?php if ($activePeriodId): ?>
    
    
    <div class="mb-3">

<!-- periodId used in ajax -->

    <input
        type="hidden"
        name="save_lock_period_id"
        value="<?= $activePeriodId ?>">

</div>
    
   <!-- xxxxxxxxxxxxxxxxxxxxxx  -->
    
    
    <div class="card mb-3">

    <div class="card-header bg-primary text-white">
     <strong>  Lock Editing of Results for Active Period (<?= $activePeriodName ?? "-" ?>) 
     </strong>
    </div>

    <div class="card-body">

        <?php
        $lockStatus = (int)($settings['lock_status'] ?? 0);

        $alertClass =
            $lockStatus === 0 ? 'alert-success'
            : ($lockStatus === 1 ? 'alert-warning' : 'alert-danger');

        $statusText =
            $lockStatus === 0
                ? '✓ OPEN - Teachers and Admin can edit results.'
                : ($lockStatus === 1
                    ? '🔐 TEACHER LOCK - Teachers cannot edit. Admin can still edit.'
                    : '🔒 PERMANENT LOCK - No further edits allowed.');
        ?>




        <div
            id="lockStatusText"
            class="alert <?= $alertClass ?> mb-3">

            <?= $statusText ?>

        </div>

        <label class="form-label">
            Change Status
        </label>

        <select
            id="lockStatus"
            class="form-select">

            <option value="0" <?= $lockStatus === 0 ? 'selected' : '' ?>>
                Open
            </option>

            <option value="1" <?= $lockStatus === 1 ? 'selected' : '' ?>>
                Teacher Lock
            </option>

            <option value="2" <?= $lockStatus === 2 ? 'selected' : '' ?>>
                Permanent Lock
            </option>

        </select>

        <button
            type="button"
            id="saveLockBtn"
            class="btn btn-primary mt-3">

            Update Status

        </button>

    </div>

</div>
    
<!-- xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx -->
    

        <!-- STEP 2: FULL SETTINGS FORM -->
        <form method="POST" action="/<?= $appName ?>/school-settings/save">

            <input type="hidden" name="period_id" value="<?= $activePeriodId ?>">
            
         

<!-- xxxxxxxxxxxxxxxxxxxxxxxxxxxxxx -->
            

            <div class="card mt-8 ">

                <div class="card-header bg-primary text-white">
  <strong>  Edit Common Reportcard Info for Active Period (<?= $activePeriodName ?? "-" ?>)
  </strong>
                </div>

                <div class="card-body">

                    <div class="mb-3">
                        <label class="form-label">Days Open</label>
                        <input type="number"
                               class="form-control"
                               name="days_open"
                               value="<?= $settings['days_open'] ?? "" ?>">
                    </div>

                    <div class="row">

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Term Start Date</label>
                            <input type="date"
                                   class="form-control"
                                   name="term_start_date"
                                   value="<?= $settings['term_start_date'] ?? '' ?>">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Vacation Date</label>
                            <input type="date"
                                   class="form-control"
                                   name="date_of_vacation"
                                   value="<?= $settings['date_of_vacation'] ?? '' ?>">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Resumption Date</label>
                            <input type="date"
                                   class="form-control"
                                   name="date_of_resumption"
                                   value="<?= $settings['date_of_resumption'] ?? '' ?>">
                        </div>

                    </div>
<button type="button" id="saveBtn" class="btn btn-primary btn-lg">
    Save Settings
</button>

<span id="saveStatus" class="ms-3"></span>

                </div>



            </div>

                

        </form>

    <?php endif; ?>

</div>

<script>

document.getElementById('saveBtn')?.addEventListener('click', async function () {

    const form = this.closest('form');
    const status = document.getElementById('saveStatus');

    status.textContent = 'Saving...';
    status.className = 'ms-3 text-muted';

    try {

        const response = await fetch(form.action, {
            method: 'POST',
            body: new FormData(form)
        });

        const result = await response.json();

        if (result.status === 'success') {
        
  let message = 'Saved successfully' ;

            status.textContent = message ;
            status.className = 'ms-3 text-success';

        //Flash msg
         showFlash
         (
         [
         {'type':"success",'text': message }
         ]
         )
    
        } else {
 let message = result.message || 'Save failed';;

            status.textContent = message;
            status.className = 'ms-3 text-danger';

        //Flash msg
         showFlash
         (
         [
         {'type':"danger",'text': message }
         ]
         )
    

        }

    } catch (err) {

let message = 'Network error';

        status.textContent = message ;
        status.className = 'ms-3 text-danger';
        
        //Flash msg
         showFlash
         (
         [
         {'type':"danger",'text': message }
         ]
         )
    
        
    }
    
});

/****************/


document.getElementById('saveLockBtn')?.addEventListener('click', async function () {

    const periodId = document.querySelector('[name="save_lock_period_id"]').value;
    const lockStatusText = document.getElementById('lockStatusText');

let lockStatus = Number($('#lockStatus').val());

    if (!periodId) {
        alert('Select a period first');
        return;
    }

    lockStatusText.textContent = 'Updating...';

    try {
    console.log("checkpoint 0")
    
        const res = await fetch('/<?= $appName ?>/school-settings/update-lock', {
            method: 'POST',
            body: new URLSearchParams({
                period_id: periodId,
                lock_status : lockStatus
            })
        });
console.log("checkpoint 1a")

        const data = await res.json();

console.log("checkpoint 2")

        if (data.status === 'success') {
        
        console.log("success",data)

  /****************************/

lockStatusText.classList.remove(
    'alert-success',
    'alert-warning',
    'alert-danger'
);



if (data.lock_status == 0) {

    lockStatusText.classList.add('alert-success');

    lockStatusText.textContent =
        '✓ OPEN - Teachers and Admin can edit results.';
}
else if (data.lock_status == 1) {

    lockStatusText.classList.add('alert-warning');

    lockStatusText.textContent =
        '🔐 TEACHER LOCK - Teachers cannot edit. Admin can still edit.';
}
else {

    lockStatusText.classList.add('alert-danger');

    lockStatusText.textContent =
        '🔒 PERMANENT LOCK - No further edits allowed.';
}
            
        } else {
        
             console.log("error1")
             
            lockStatusText.textContent = data.message;
            lockStatusText.className = 'ms-3 text-danger';
        }

    } catch (e) {
        console.log("catch error : ",e.message)
        lockStatusText.textContent = 'Network error - catch';
        lockStatusText.className = 'ms-3 text-danger';
    }

});

/*******************/


</script>













