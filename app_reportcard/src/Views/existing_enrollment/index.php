<div class="container py-4">
<a
    href="/<?= $appName ?>/student_manage?session_id=<?= $sessionId ?>&class_id=<?= $classId ?>"
    class="btn btn-outline-secondary mb-3">

    ← Back to Class Students

</a>



    <h3 class="mb-4">
        Enroll Existing Students
    </h3>

    <div class="card mb-4">

        <div class="card-body">

            <div class="row">

                <div class="col-md-6">

                    <strong>Academic Session:</strong><br>

                    <?= htmlspecialchars($session['session_name']) ?>

                </div>

                <div class="col-md-6">

                    <strong>Class:</strong><br>

                    <?= htmlspecialchars($class['class_name']) ?>

                </div>

            </div>

        </div>

    </div>

    <div class="card mb-4">

        <div class="card-body">
        
<input
    type="hidden"
    name="session_id"
    value="<?= $sessionId ?>">

<input
    type="hidden"
    name="class_id"
    value="<?= $classId ?>">
        
   <!-- xxxxxxxxxxxxxxxxxxxxxxxxxxx -->

            <div class="row">

                <div class="col-md-4 mb-3">

                    <label class="form-label">

                        Search

                    </label>

                    <input
                        type="text"
                        class="form-control"
                        name="search"
                        placeholder="Student name or Admission No.">

                </div>

                <div class="col-md-2 mb-3">

                    <label class="form-label">

                        Religion

                    </label>

                    <select
                        class="form-select"
                        name="religion">

                        <option value="">
                            All
                        </option>

                        <option value="CRS">
                            CRS
                        </option>

                        <option value="IRS">
                            IRS
                        </option>

                    </select>

                </div>

                <div class="col-md-2 mb-3">

                    <label class="form-label">

                        Sex

                    </label>

                    <select
                        class="form-select"
                        name="sex">

                        <option value="">
                            All
                        </option>

                        <option value="M">
                            Male
                        </option>

                        <option value="F">
                            Female
                        </option>

                    </select>

                </div>

                <div class="col-md-2 mb-3">

                    <label class="form-label">

                        &nbsp;

                    </label>

                    <div class="form-check">

                        <input
                            class="form-check-input"
                            type="checkbox"
                            id="hasAdmissionNo"
                            name="has_admission_no">

                        <label
                            class="form-check-label"
                            for="hasAdmissionNo">

                            Has Admission No.

                        </label>

                    </div>

                    <div class="form-check">

                        <input
                            class="form-check-input"
                            type="checkbox"
                            id="hasPassport"
                            name="has_passport">

                        <label
                            class="form-check-label"
                            for="hasPassport">

                            Has Passport

                        </label>

                    </div>

                    <div class="form-check">

                        <input
                            class="form-check-input"
                            type="checkbox"
                            id="hasDob"
                            name="has_dob">

                        <label
                            class="form-check-label"
                            for="hasDob">

                            Has Date of Birth

                        </label>

                    </div>

                </div>

                <div class="col-md-2 d-flex align-items-end">

                    <button
                        class="btn btn-primary w-100"
                        id="searchStudentsBtn">

                        Search

                    </button>

                </div>

            </div>

        </div>

    </div>

    <div id="studentTableContainer">

    </div>

</div>

<script>

document.addEventListener('DOMContentLoaded', () => {

    reloadStudentTable();

    document
        .getElementById('searchStudentsBtn')
        .addEventListener('click', reloadStudentTable);
        
    
/*****************************************
| Enroll Existing Student
*****************************************/

document.addEventListener('click', async function (e) {

    const btn = e.target.closest('.enrollStudentBtn');

    if (!btn) return;

    if (!confirm('Enroll this student into the selected class?')) {
        return;
    }

    btn.disabled = true;
    btn.textContent = 'Enrolling...';

    try {
        console.log("1");
        const response = await fetch(

            '/<?= $appName ?>/existing_enrollment/enroll',

            {
                method: 'POST',

                headers: {
                    'Content-Type':
                        'application/x-www-form-urlencoded'
                },

                body: new URLSearchParams({

                    session_id:
                        document.querySelector('[name="session_id"]').value,

                    class_id:
                        document.querySelector('[name="class_id"]').value,

                    student_id:
                        btn.dataset.studentId

                })

            }

        );
                console.log("2");




const text = await response.text();
console.log(text);
                        console.log("3");
const result = JSON.parse(text);

                        console.log("4");

        if (result.status === 'success') {

            showFlash([
                {
                    type: 'success',
                    text: result.message
                }
            ]);

            await reloadStudentTable();

            return;
        }

        showFlash([
            {
                type: 'danger',
                text: result.message
            }
        ]);

    }
    catch (e) {
    
    console.log("catch error : ", e.message)

        showFlash([
            {
                type: 'danger',
                text: 'Network error.'
            }
        ]);

    }
    finally {

        btn.disabled = false;
        btn.textContent = 'Enroll';

    }

});








});

/************* END OF DOMContentLoaded ***************/

async function reloadStudentTable()
{
    const response = await fetch(

        `/<?= $appName ?>/existing_enrollment/table?` +

        new URLSearchParams({

            session_id:
                <?= $sessionId ?>,

            class_id:
                <?= $classId ?>,

            search:
                document.querySelector('[name="search"]').value,

            religion:
                document.querySelector('[name="religion"]').value,

            sex:
                document.querySelector('[name="sex"]').value,

            has_admission_no:
                document.querySelector('[name="has_admission_no"]').checked ? 1 : 0,

            has_passport:
                document.querySelector('[name="has_passport"]').checked ? 1 : 0,

            has_dob:
                document.querySelector('[name="has_dob"]').checked ? 1 : 0

        })

    );

    document
        .getElementById('studentTableContainer')
        .innerHTML = await response.text();
}

</script>








