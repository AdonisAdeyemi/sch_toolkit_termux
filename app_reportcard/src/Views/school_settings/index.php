<div class="container py-4">

    <h3 class="mb-4">School Period Settings</h3>

    <!-- STEP 1: PERIOD SELECT ONLY -->
    <?php if (!$periodId): ?>

        <div class="card p-3">

            <form method="GET" action="/<?= $appName ?>/school-settings">

                <div class="mb-3">
                    <label class="form-label">Select Academic Period</label>

                    <select name="period_id" class="form-select" required>
                        <option value="">-- Choose Period --</option>

                        <?php foreach ($periods as $p): ?>
                            <option value="<?= $p['id'] ?>">
                                <?= $p['period_name'] ?>
                            </option>
                        <?php endforeach; ?>

                    </select>
                </div>
                
           
                <button class="btn btn-primary">
                    Load Settings
                </button>


            </form>

        </div>

    <?php else: ?>
    
    
    <div class="mb-3">
    <label class="form-label">
        Academic Period
    </label>

    <select class="form-select" disabled>

<!-- periodId used in ajax -->

    <input
        type="hidden"
        name="period_id"
        value="<?= $periodId ?>">

</div>
    
   <!-- xxxxxxxxxxxxxxxxxxxxxx  -->
    
    
    <div class="card mb-3">

    <div class="card-header">
        Result Lock Status
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

            <input type="hidden" name="period_id" value="<?= $periodId ?>">
            
         

<!-- xxxxxxxxxxxxxxxxxxxxxxxxxxxxxx -->
            
            
            

            <div class="card mb-3">

                <div class="card-header bg-primary text-white">
                    Active Period Settings
                </div>

                <div class="card-body">

                    <div class="mb-3">
                        <label class="form-label">Days Open</label>
                        <input type="number"
                               class="form-control"
                               name="days_open"
                               value="<?= $settings['days_open'] ?? 124 ?>">
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

                </div>

            </div>

                
<button type="button" id="saveBtn" class="btn btn-success btn-lg">
    Save Settings
</button>

<span id="saveStatus" class="ms-3"></span>

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

    const periodId = document.querySelector('[name="period_id"]').value;
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













