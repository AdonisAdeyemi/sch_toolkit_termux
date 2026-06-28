
<div class="container py-4">

    <h3 class="mb-4">
        Student Registry
    </h3>

    <form
        method="GET"
        action="">

        <div class="card mb-4">

            <div class="card-body">

                <div class="row">

                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Search
                        </label>

                        <input
                            type="text"
                            name="search"
                            class="form-control"
                            placeholder="Student name or Admission No."
                            value="<?= htmlspecialchars($search ?? '') ?>">

                    </div>

                </div>

                <div class="mt-2">

                    <button class="btn btn-primary">

                        Search

                    </button>


<button
    id = "newStudentBtn"
    type="button"
    class="btn btn-success ms-2"
    data-bs-toggle="modal"
    data-bs-target="#studentModal">

    + New Student

</button>


                </div>

            </div>

        </div>

    </form>

    <div id="studentTableContainer">

    </div>

</div>




<?php require __DIR__ . '/partials/modal_student.php';
 ?>

<!-- xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx -->
<!-- xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx -->
<!-- xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx -->



<!-- xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx -->
<!-- xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx -->
<!-- xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx -->

<script>

document.addEventListener('DOMContentLoaded', () => {


//load table from /partials/table.php
reloadStudentTable()

/*****************/

    const studentForm = document.getElementById('studentForm');

    const newStudentBtn = document.getElementById('newStudentBtn');

    const studentId = document.getElementById('studentId');

    const passportPreview =
        document.getElementById('passportPreview');

    const passport =
        document.getElementById('passport');

    newStudentBtn.addEventListener(
        'click',
        () => {
        
        console.log("newStd 1")

            studentForm.reset();
            
        console.log("newStd 2")

            studentId.value = '';

 studentForm.action =
   "/<?= $appName ?>/student_registry/save";

            document.getElementById(
                'studentModalTitle'
            ).textContent = 'New Student';

            passportPreview.src = '';

            passportPreview.style.display = 'none';

        }
    );

    passport.addEventListener(
        'change',
        previewPassport
    );

    studentForm.addEventListener(
        'submit',
        saveStudent
    );

/***********************/

document.addEventListener('click', async (e) => {

    const btn = e.target.closest('.editStudentBtn');

    if (!btn) {
        return;
    }

    await loadStudent(btn.dataset.studentId);

});



});

/******  OUTSIDE DOMContentLoaded ********/

function previewPassport(e)
{
    const file = e.target.files[0];

    const preview =
        document.getElementById('passportPreview');

    if (!file) {

        preview.src = '';

        preview.style.display = 'none';

        return;
    }

    preview.src =
        URL.createObjectURL(file);

    preview.style.display = 'block';
}

/****************/

async function saveStudent(e)
{
    e.preventDefault();
    
    alert("save clicked")

    const studentForm =
        document.getElementById('studentForm');

    const response = await fetch(
        studentForm.action,
        {
            method: 'POST',
            body: new FormData(studentForm)
        }
    );

    const result = await response.json();

    if (result.status === 'success') {

        bootstrap.Modal
            .getInstance(
                document.getElementById('studentModal')
            )
            .hide();

        studentForm.reset();

        document.getElementById(
            'passportPreview'
        ).style.display = 'none';

        await reloadStudentTable();
        
        showFlash([
                {
                    type: 'success',
                    text: "Save Successful"
                }
            ]);

        return;
    }

//not success
            showFlash([
                {
                    type: 'danger',
                    text: result.message
                }
            ]);
}

/*****************************/

/************/

async function reloadStudentTable() {

console.log ("in reloadStudentTable");

    const search =
        document.querySelector('[name="search"]').value;

    const response = await fetch(

        `/<?= $appName ?>/student_registry/table?` +

        new URLSearchParams({
            search: search
        })

    );

    document
        .getElementById('studentTableContainer')
        .innerHTML =
        await response.text();

}

/*******************/

async function loadStudent(studentId)
{
    const response = await fetch(
        `/<?= $appName ?>/student_registry/get?id=${studentId}`
    );

    const result = await response.json();

    if (result.status !== 'success') {

        alert(result.message);

        return;
    }

    const student = result.student;

    document.getElementById('studentId').value =
        student.id;

    document.getElementById('studentName').value =
        student.student_name;

    document.getElementById('admissionNo').value =
        student.admission_no ?? '';

    document.getElementById('religion').value =
        student.religion;

    document.getElementById('sex').value =
        student.sex;

    document.getElementById('studentModalTitle')
        .textContent = 'Edit Student';

    document.getElementById('studentForm').action =
        "/<?= $appName ?>/student_registry/update";

    const preview =
        document.getElementById('passportPreview');

    if (student.passport_url) {
    
let appName = "<?= $appName ?>";
let folder = "passport"
let filename = student.passport_url
let passportUrl = getAssetUrl(appName, folder, filename) 

        preview.src = passportUrl ;

        preview.style.display = 'block';

    } else {

        preview.src = '';

        preview.style.display = 'none';

    }

    bootstrap.Modal
        .getOrCreateInstance(
            document.getElementById('studentModal')
        )
        .show();
}

/*****************/



</script>























