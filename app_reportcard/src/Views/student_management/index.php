<div class="container py-4">

    <h3 class="mb-4">
        Class-Students Management
    </h3>

    <form method="GET">

        <div class="card mb-4">

            <div class="card-body">

                <div class="row">

                    <div class="col-md-4 mb-3">

                        <label class="form-label">
                            Academic Session
                        </label>

                        <select
                            name="filter_session_id"
                            class="form-select"
                            required>

                            <option value="">
                                -- Select Session --
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

<!-- xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx -->
<!-- xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx -->

                    <div class="col-md-4 mb-3">

                        <label class="form-label">
                            Class
                        </label>

                        <select
                            name="filter_class_id"
                            class="form-select">

                            <option value="0">
                                -- Select Class --
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

<!-- xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx -->
<!-- xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx -->




<!-- Department -->

    <div class="col-md-2 mb-2">


    <label class="form-label">

        Department

    </label>

    <select
        class="form-select department_select"
        id="filterDepartmentId"
        name="filter_department_id"
        required>

        <option value="0">

            Select Department

        </option>


    </select>

</div>




<!-- xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx -->
<!-- xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx -->
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


<!-- xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx -->
<!-- xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx -->

                <div 
                class="mt-2">

                    <button id="loadButton" class="btn btn-primary">

                        Load Students

                    </button>

<!-- xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx -->
<!-- xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx -->

                    <button
                        type="button"
                        class="btn btn-success ms-2"
                        id="newStudentBtn">

                        + Enroll New

                    </button>

                    <button
                        type="button"
                        class="btn btn-outline-primary ms-2"
                        id="existingStudentBtn">

                        + Enroll Existing

                    </button>

                </div>

            </div>

        </div>

    </form>


<div id="studentTableContainer"> 

</div>


</div>

<!-- xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
xxxxxxxxxxxxxxxx -->

<?php require "partials/modal_student.php";?>


<!-- xxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx -->


<!-- contains shared function : 
populateDepartments (
class_id_for_dept_derivation,
 dept_elem_selector ) -->

<script src="/public/reportcard/assets/js/department.js"></script>

<script>

//get referenceData from controller
const referenceData =
<?= json_encode($referenceData) ?>;

console.log("referenceData", referenceData)

    const loadButton = document.getElementById('loadButton');

    loadButton.addEventListener('click', async function(e)
    {
 e.preventDefault();
 
 //validate session & class

 const sessionSelect =
    document.querySelector('[name="filter_session_id"]');
 const classSelect =
    document.querySelector('[name="filter_class_id"]');

if (
(!sessionSelect || sessionSelect.value === '')
||
(classSelect.value === "0")
) {
    alert('Please select both session AND class.');
    return;
}

 
//reload
 await reloadStudentTable();
 
//flash
        showFlash([
                {
                    type: 'success',
                    text: "Load Successful!"
                }
            ]);     

    
});



/*******
newStudentBtn listener
********/

const studentModal =
    new bootstrap.Modal(
        document.getElementById('studentModal')
    );
    
const passportInput =
    document.querySelector('#studentForm input[name="passport"]');

const passportPreview =
    document.getElementById('passportPreview');


document
.getElementById('newStudentBtn')
.addEventListener('click', () => {

  const form = document.getElementById('studentForm') ;

//validate session & class

 const sessionSelect =
    document.querySelector('[name="filter_session_id"]');
 const classSelect =
    document.querySelector('[name="filter_class_id"]');

if (
(!sessionSelect || sessionSelect.value === '')
||
(classSelect.value === "0")
) {
    alert('Please select both session AND class.');
    return;
}

/******* reset modal fields ***********/
    document.getElementById('studentModalTitle').textContent =
        'New Student';

    document.getElementById('studentForm').reset();

    document.getElementById('studentId').value = '';

    passportPreview.src = '';
    passportPreview.style.display = 'none';
    passportInput.value = '';
    
    
    //must be after form.reset
 
//get filter values
   const sessionFilterValue = document.querySelector('[name="filter_session_id"]').value;
 const classFilterValue = document.querySelector('[name="filter_class_id"]').value;

//populate modal's form elements : with filter's schoolId and classId
 form.querySelector('[name="session_id"]').value = sessionFilterValue;
form.querySelector('[name="class_id"]').value = classFilterValue;
    
console.log("class_id", form.querySelector('[name="class_id"]'));
const select = form.querySelector('[name="class_id"]');

console.log("classFilterValue",classFilterValue);
console.log("select.options",[...select.options].map(o => o.value));
    
    
    
    

    studentModal.show();

});

/*******************/

passportInput.addEventListener('change', function () {

    const file = this.files[0];

    if (!file) {

        passportPreview.src = '';
        passportPreview.style.display = 'none';
        return;

    }

    passportPreview.src =
        URL.createObjectURL(file);

    passportPreview.style.display = 'inline-block';

});

/***************************/




/*******************/

document
.getElementById('studentForm')
.addEventListener('submit', async function (e) {

    e.preventDefault();
    
  const form = this;


    const saveBtn =
        document.getElementById('saveStudentBtn');

    saveBtn.disabled = true;
    saveBtn.textContent = 'Saving...';

    try {

        const response = await fetch(
            '/<?= $appName ?>/student_manage/save',
            {
                method: 'POST',
                body: new FormData(form)
            }
        );

const text = await response.text();
console.log(text);

const result = JSON.parse(text);

// const result = await response.json();
        
        console.log(result)

        if (result.status === 'success') {

            studentModal.hide();


//reload table 
studentModal.hide();

await reloadStudentTable();

showFlash([
    {
        type: 'success',
        text: result.message
    }
]);

        } else {

            showFlash([
                {
                    type: 'danger',
                    text: "Result but not success : " + result.message
                }
            ]);

        }

    } catch(e) {
    
    console.log("catch block : ",e.message )

        showFlash([
            {
                type: 'danger',
                text: 'Network error'
            }
        ]);

    }

    saveBtn.disabled = false;
    saveBtn.textContent = 'Save Student';

});

/****************/

document.addEventListener('click', async (e) => {

    const btn = e.target.closest('.removeStudentBtn');

    if (!btn) {
        return;
    }
    

    await removeStudentFromClass(btn.dataset.studentId);

});

/***************/
document
.getElementById('existingStudentBtn')
.addEventListener('click', () => {

    const sessionId =
        document.querySelector('[name="filter_session_id"]').value;

    const classId =
        document.querySelector('[name="filter_class_id"]').value;

    if (!sessionId) {

        alert('Please select an academic session.');

        return;
    }

    if (!classId || classId === '0') {

        alert('Please select a class.');

        return;
    }

    window.location.href =
        `/<?= $appName ?>/existing_enrollment?` +

        new URLSearchParams({

            session_id: sessionId,
            class_id: classId

        });

});


/******************/

const classSelect =
    document.querySelector('[name="filter_class_id"]');

classSelect.addEventListener('change', function () {
console.log("in class select listener")

let departmentClassSelector  = ".department_select";

    populateDepartments(this.value, departmentClassSelector);

});


    
/******* LISTENER END *****/


async function reloadStudentTable() {

console.log ("in reloadStudentTable");

    const sessionId =
        document.querySelector('[name="filter_session_id"]').value;

    const classId =
        document.querySelector('[name="filter_class_id"]').value;


    const filterDepartmentId =
        document.querySelector('[name="filter_department_id"]').value;

    const search =
        document.querySelector('[name="search"]').value;

    const response = await fetch(

        `/<?= $appName ?>/student_manage/table?` +

        new URLSearchParams({

            filter_session_id: sessionId,
            filter_class_id: classId,
            filter_department_id : filterDepartmentId,
            search: search

        })

    );

    document
        .getElementById('studentTableContainer')
        .innerHTML =
        await response.text();

}

/********************/

async function removeStudentFromClass(studentId)
{
    if (!confirm(
        'Remove this student from the class?'
    )) {
        return;
    }

    const sessionId =
        document.querySelector('[name="filter_session_id"]').value;

    const response = await fetch(
        '/<?= $appName ?>/student_manage/remove_from_class',
        {
            method: 'POST',

            body: new URLSearchParams({

                session_id: sessionId,
                student_id: studentId

            })
        }
    );

    const result = await response.json();

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

console.log ("error : ",result.message)

    showFlash([
        {
            type: 'danger',
            text: result.message
        }
    ]);
}

/****************/



/*****/
function legacy_populateDepartments(classId)
{
console.log("in populateDepartments")


    const cls =
        referenceData.classes[classId];

    if (!cls)
        return;

const classLevel =
    cls.class_level.toUpperCase();

const departments =
    referenceData.departments[classLevel] || [];

  console.log("in departments : cls.class_level", cls.class_level)
   
 console.log("in departments >  : referenceData.departments : ", referenceData.departments[
            cls.class_level
        ] )

console.log("in departments : ", departments)


const departmentSelectElems = document.querySelectorAll('.department_select');

departmentSelectElems.forEach(select => {

    select.innerHTML =
        '<option value="">Select Department</option>';

    departments.forEach(department => {

        select.insertAdjacentHTML(

            'beforeend',

            `
            <option value="${department.id}">
                ${department.name}
            </option>
            `

        );

    });

});

}

/*******************/





</script>













