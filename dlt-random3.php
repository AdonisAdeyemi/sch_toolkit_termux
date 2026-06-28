Good. Since New Student CRUD works, you're about 80% done. Editing is mostly reusing what you already have.

I'd implement it in this order:


---

1. Make Edit buttons identifiable

<button
    class="btn btn-sm btn-outline-primary editStudentBtn"
    data-student-id="<?= $student['id'] ?>">

    Edit

</button>

(You already have this.)


---

2. Controller

You already planned:

GET /student_registry/get

Return JSON:

{
    "status":"success",
    "student":{
        "id":5,
        "student_name":"John Doe",
        "admission_no":"MMLC-05",
        "religion":"CRS",
        "sex":"M",
        "passport_url":"student_5.jpg"
    }
}


---

3. JS

Listen for Edit clicks.

Instead of attaching listeners to every button, use event delegation:

document.addEventListener('click', async (e) => {

    const btn = e.target.closest('.editStudentBtn');

    if (!btn) return;

    await loadStudent(btn.dataset.studentId);

});

No need to reattach listeners after the table reloads.


---

4. loadStudent()

This is the only new major function.

It should:

fetch /student_registry/get?id=...

fill the modal

change modal title

change form action

show current passport

open modal



---

5. Populate

studentId.value = s.id;

studentName.value = s.student_name;

admissionNo.value = s.admission_no ?? '';

religion.value = s.religion;

sex.value = s.sex;


---

6. Passport preview

If student has one:

passportPreview.src = s.passport_url;

passportPreview.style.display = 'block';

otherwise hide it.


---

7. Change modal

Instead of

New Student

display

Edit Student


---

8. Change form action

Instead of

/student_registry/save

use

/student_registry/update


---

9. Submit

Don't create another JS function.

Keep:

saveStudent(e)

Exactly as it is.

The only difference is:

studentForm.action

is now /update.

The same AJAX code works.


---

10. Controller

Implement

update()

which will:

validate

update student

upload passport if provided

update passport_url if uploaded

return JSON


Exactly like save(), except it calls

updateStudent(...)

instead of

createStudent(...)


---

Result

One modal.

One submit function.

One preview function.

One table.

Only:

loadStudent()

controller update()


are really new.

That is exactly how I would structure it.
