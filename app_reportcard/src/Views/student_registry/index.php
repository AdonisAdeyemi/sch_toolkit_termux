
<div class="container py-4">

    <h3 class="mb-4">
        Student Registry
    </h3>

    <form
        method="GET"
        action="/<?= $appName ?>/student_register">

        <div class="card mb-4 p-1">

<div class="row">

    <!-- Search -->

    <div class="col-md-4 mb-2">

        <label class="form-label">

            Search

        </label>

        <input
            type="text"
            class="form-control"
            name="search"
            value="<?= htmlspecialchars($search ?? '') ?>"
            placeholder="Student name or Admission No.">

    </div>




    <!-- Sex -->

    <div class="col-md-2 mb-2">

        <label class="form-label">

            Sex

        </label>

        <select
            class="form-select"
            name="sex">

            <option value="">All</option>

            <option
                value="M"
                <?= ($sex ?? '') === 'M' ? 'selected' : '' ?>>

                Male

            </option>

            <option
                value="F"
                <?= ($sex ?? '') === 'F' ? 'selected' : '' ?>>

                Female

            </option>

        </select>

    </div>

    <!-- Passport -->

    <div class="col-md-2 mb-2">

        <label class="form-label">

            Passport

        </label>

        <select
            class="form-select"
            name="passport">

            <option value="">All</option>

            <option
                value="1"
                <?= ($passport ?? '') === '1' ? 'selected' : '' ?>>

                Has Passport

            </option>

            <option
                value="0"
                <?= ($passport ?? '') === '0' ? 'selected' : '' ?>>

                No Passport

            </option>

        </select>

    </div>

    <!-- DOB -->

    <div class="col-md-2 mb-2">

        <label class="form-label">

            DOB

        </label>

        <select
            class="form-select"
            name="dob">

            <option value="">All</option>

            <option
                value="1"
                <?= ($dob ?? '') === '1' ? 'selected' : '' ?>>

                Has DOB

            </option>

            <option
                value="0"
                <?= ($dob ?? '') === '0' ? 'selected' : '' ?>>

                No DOB

            </option>

        </select>

    </div>

</div>
                
<!-- xxxxxxxxxxxxxxxxxxxxxxxxx -->                

                <div class="mt-2">

                    <button id="search_btn" class="btn btn-primary">

                        Search

                    </button>


<button
    id = "newStudentBtn"
    type="button"
    class="btn btn-success ms-2 "
    data-bs-toggle="modal"
    data-bs-target="#studentModal">

    + New Student

</button>


                </div>
                
<!-- xxxxxxxxxxxxxxxxxxxxxxxxx -->                 
                
   <div class="form-check mt-3">

    <input
        class="form-check-input"
        type="checkbox"
        id="showDeleted"
        name="show_deleted">

    <label
        class="form-check-label"
        for="showDeleted">

        Show Deleted Students

    </label>

</div>             

<!-- xxxxxxxxxxxxxxxxxxxxxxxxx -->                 

            </div>

        </div>

    </form>
    
<!-- xxxxxxxxxxxxxxxxxxxxxxxxxxxx -->
    
</div>

<!-- xxxxxxxxxxxxxxxxxxxxxxxxxxxx -->



<div id="studentTableContainer"></div>
    
    

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

let appName = "<?= $appName ?>";

document.addEventListener('DOMContentLoaded', () => {


//load table from  /partials/table.php 
reloadStudentTable();

/*************/
    const searchBtn =
        document.getElementById('search_btn');
        
searchBtn.addEventListener('click', async (e) => {
  
    e.preventDefault();


  await reloadStudentTable();
        
        showFlash([
                {
                    type: 'success',
                    text: "Load Successful"
                }
            ]);

        return;
})


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

            studentForm.reset();
            

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

//this is for editStudentBtn >> it collects all clicks & filters editStudentBtn

document.addEventListener('click', async (e) => {

    const btn = e.target.closest('.editStudentBtn');

    if (!btn) {
        return;
    }

    await loadStudent(btn.dataset.studentId);
    
    /*
        showFlash([
                {
                    type: 'success',
                    text: "Load Successful"
                }
            ]);    
    */

});

/**************************/

document.addEventListener('click', function (e) {

    const btn = e.target.closest('.deleteStudentBtn');

    if (!btn) {
        return;
    }

    if (!confirm('Delete this student?')) {
        return;
    }

    const form = document.createElement('form');

    form.method = 'POST';
    form.action = `/${appName}/student_registry/delete`;

    form.innerHTML = `
        <input
            type="hidden"
            name="student_id"
            value="${btn.dataset.studentId}">
    `;

    document.body.appendChild(form);

    form.submit();

});

/***************/

document.addEventListener('click', function (e) {

    const btn = e.target.closest('.restoreStudentBtn');

    if (!btn) {
        return;
    }

    if (!confirm('Restore this student?')) {
        return;
    }

    const form = document.createElement('form');

    form.method = 'POST';
    form.action = `/${appName}/student_registry/restore`;

    form.innerHTML = `
        <input
            type="hidden"
            name="student_id"
            value="${btn.dataset.studentId}">
    `;

    document.body.appendChild(form);

    form.submit();

});


/************************/

/**************************/

document
    .getElementById('showDeleted')
    .addEventListener('change', reloadStudentTable);
    
    
/*****************/






});

/******  OUTSIDE DOMContentLoaded ********/

function previewPassport(e)
{
    const file = e.target.files[0];

    const preview =
        document.getElementById('passportPreview');

    if (!file) {

        preview.src = '';mt

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
/*
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
*/
/****************************/

async function reloadStudentTable()
{
    const search =
        document.querySelector('[name="search"]').value;

    const sex =
        document.querySelector('[name="sex"]').value;

    const passport =
        document.querySelector('[name="passport"]').value;

    const dob =
        document.querySelector('[name="dob"]').value;

const showDeleted = document.getElementById('showDeleted').checked ? 1 : 0;

    const response = await fetch(

        `/<?= $appName ?>/student_registry/table?` +

        new URLSearchParams({

            search: search,
            sex: sex,
            passport: passport,
            dob: dob,
            show_deleted: showDeleted
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


    document.getElementById('sex').value =
        student.sex;
        
    document.getElementById('dateOfBirth').value =
    student.date_of_birth ?? '';

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
/***************/
/************/

</script>























