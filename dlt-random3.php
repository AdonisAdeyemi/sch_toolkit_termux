Perfect. Since you've already built this once in Student Management, this should be mostly copy-and-paste with the enrollment logic removed.

Step 1 — Button

<button
    type="button"
    class="btn btn-success ms-2"
    data-bs-toggle="modal"
    data-bs-target="#studentModal">

    + New Student

</button>


---

Step 2 — Modal

Rename it to something reusable:

modal_student.php

instead of

modal_new.php

because the same modal will later be used for Edit.


---

Step 3 — Form

<form
    id="studentForm"
    enctype="multipart/form-data">

    <input
        type="hidden"
        id="studentId"
        name="student_id">

    Student Name

    Admission No.

    Religion

    Sex

    Passport

    Image Preview

    Save

</form>

Notice:

No Session

No Class



---

Step 4 — JS

newStudentBtn.onclick = () => {

    studentForm.reset();

    studentId.value = '';

    previewLogo.style.display = 'none';

};


---

Step 5 — Submit

studentForm.addEventListener(
    'submit',
    saveStudent
);


---

Step 6 — AJAX

async function saveStudent(e) {

    e.preventDefault();

    const response = await fetch(

        appUrl + '/student-registry/save',

        {
            method: 'POST',
            body: new FormData(studentForm)
        }

    );

    const result = await response.json();

    if (result.status === 'success') {

        bootstrap.Modal
            .getInstance(studentModal)
            .hide();

        reloadTable();

    } else {

        alert(result.message);

    }

}


---

Step 7 — Reload

Exactly the same pattern you've already used:

async function reloadTable() {

    const response = await fetch(

        appUrl +
        '/student-registry/table?' +
        new URLSearchParams({

            search:
                search.value

        })

    );

    studentTableContainer.innerHTML =
        await response.text();

}


---

After this step

The complete flow will be:

Student Registry

↓

+ New Student

↓

Modal

↓

Save

↓

AJAX

↓

Table Reload

✓

At that point, creating students in the registry will be fully functional, and you'll be ready to make the same modal handle Edit Student with only a few additional changes.
