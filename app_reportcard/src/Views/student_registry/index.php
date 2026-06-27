
<div class="container py-4">

    <h3 class="mb-4">
        Student Registryyy
    </h3>

    <form
        method="GET"
        action="/<?= $appName ?>/student_registry">

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

 // studentForm.action =
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


</script>























