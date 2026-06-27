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
                            name="session_id"
                            class="form-select"
                            required>

                            <option value="">
                                Select Session
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

                    <div class="col-md-4 mb-3">

                        <label class="form-label">
                            Class
                        </label>

                        <select
                            name="class_id"
                            class="form-select">

                            <option value="0">
                                All Classes
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

                <div id="loadButton"
                class="mt-2">

                    <button class="btn btn-primary">

                        Load Students

                    </button>

                    <button
                        type="button"
                        class="btn btn-success ms-2"
                        id="newStudentBtn">

                        + New Student

                    </button>

                    <button
                        type="button"
                        class="btn btn-outline-primary ms-2"
                        id="existingStudentBtn">

                        + Existing Student

                    </button>

                </div>

            </div>

        </div>

    </form>


<div id="studentTableContainer"> 

<div>


</div>

<!-- xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
xxxxxxxxxxxxxxxx -->

<div class="modal fade" id="studentModal" tabindex="-1">

    <div class="modal-dialog modal-lg">

        <div class="modal-content">

            <form
                id="studentForm"
                enctype="multipart/form-data">

                <input
                    type="hidden"
                    id="studentId"
                    name="student_id">

                <input
                    type="hidden"
                    name="session_id"
                    value="<?= $sessionId ?>">

                <div class="modal-header">

                    <h5
                        class="modal-title"
                        id="studentModalTitle">

                        New Student

                    </h5>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal">
                    </button>

                </div>

                <div class="modal-body">

                    <div class="row">

                        <div class="col-md-8 mb-3">

                            <label class="form-label">

                                Full Name

                            </label>

                            <input
                                class="form-control"
                                name="student_name"
                                required>

                        </div>

                        <div class="col-md-4 mb-3">

                            <label class="form-label">

                                Admission No. (optional)

                            </label>

                            <input
                                class="form-control"
                                name="admission_no">

                        </div>

                    </div>

                    <div class="row">

                        <div class="col-md-6 mb-3">

                            <label class="form-label">

                                Sex

                            </label>

                            <select
                                class="form-select"
                                name="sex"
                                required>

                                <option value="">
                                    Select
                                </option>

                                <option value="M">
                                    Male
                                </option>

                                <option value="F">
                                    Female
                                </option>

                            </select>

                        </div>

                        <div class="col-md-6 mb-3">

                            <label class="form-label">

                                Religion

                            </label>

                            <select
                                class="form-select"
                                name="religion"
                                required>

                                <option value="">
                                    Select
                                </option>

                                <option value="CRS">
                                    CRS
                                </option>

                                <option value="IRS">
                                    IRS
                                </option>

                            </select>

                        </div>

                    </div>

                    <div class="mb-3">

                        <label class="form-label">

                            Passport (optional)

                        </label>

                        <input
                            type="file"
                            class="form-control"
                            name="passport"
                            accept="image/*">

                    </div>

                    <div
                        class="mb-3 text-center">

                        <img
                            id="passportPreview"
                            src=""
                            class="img-thumbnail"
                            style="max-height:120px;display:none;">

                    </div>

                    <div class="mb-3">

                        <label class="form-label">

                            Class

                        </label>

                        <select
                            class="form-select"
                            name="class_id"
                            required>

                            <?php foreach ($classes as $class): ?>

<option
value="<?= $class['id'] ?>"
<?= $classId == $class['id'] ? 'selected' : '' ?>
>

<?= $class['class_name'] ?>

</option>

                            <?php endforeach; ?>

                        </select>

                    </div>

                </div>

                <div class="modal-footer">

                    <button
                        class="btn btn-secondary"
                        data-bs-dismiss="modal"
                        type="button">

                        Cancel

                    </button>

                    <button
                        class="btn btn-primary"
                        id="saveStudentBtn"
                        type="submit">

                        Save Student

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>


<!-- xxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx -->


<script>

    const loadButton = document.getElementById('loadButton');

    loadButton.addEventListener('click', async function(e)
    {
 e.preventDefault();
 
 await reloadStudentTable();
    
    
});



/***************/

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

    document.getElementById('studentModalTitle').textContent =
        'New Student';

    document.getElementById('studentForm').reset();

    document.getElementById('studentId').value = '';

    passportPreview.src = '';
    passportPreview.style.display = 'none';
    passportInput.value = '';

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

            showFlash([
                {
                    type: 'success',
                    text: result.message
                }
            ]);

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

/************/

async function reloadStudentTable() {

console.log ("in reloadStudentTable");

    const sessionId =
        document.querySelector('[name="session_id"]').value;

    const classId =
        document.querySelector('[name="class_id"]').value;

    const search =
        document.querySelector('[name="search"]').value;

    const response = await fetch(

        `/<?= $appName ?>/student_manage/table?` +

        new URLSearchParams({

            session_id: sessionId,
            class_id: classId,
            search: search

        })

    );

    document
        .getElementById('studentTableContainer')
        .innerHTML =
        await response.text();

}

/********************/



/****************/



</script>













