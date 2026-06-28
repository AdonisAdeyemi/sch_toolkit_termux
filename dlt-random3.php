Perfect. Since this is an enrollment action, it belongs in your StudentManagementController, not the Student Registry.

Route

$router->post(
    '/student_manage/remove-from-class',
    [StudentManagementController::class, 'removeFromClass']
);


---

Controller

public function removeFromClass(): void

Responsibilities:

1. Read:

session_id

student_id



2. Verify the enrollment exists.


3. Check whether results exist.


4. If results exist → return an error.


5. Otherwise delete the enrollment.


6. Return JSON.




---

EnrollmentModel

1. Check if results exist

public function enrollmentHasResults(
    int $sessionId,
    int $studentId
): bool

SQL:

SELECT 1
FROM report_results
WHERE
    session_id = ?
    AND student_id = ?
LIMIT 1;

Return:

return (bool) $stmt->fetchColumn();


---

2. Remove from class

public function removeFromClass(
    int $sessionId,
    int $studentId
): bool

SQL

DELETE
FROM report_enrollment
WHERE
    session_id = ?
    AND student_id = ?;


---

Controller flow

Remove button
      │
      ▼
removeFromClass()

      │
      ▼
Has results?

   Yes ───────────────►
{
    status:'error',
    message:'Student already has results and cannot be removed.'
}

   No
      │
      ▼
DELETE enrollment

      │
      ▼
{
    status:'success',
    message:'Student removed from class.'
}


---

JS

Your button:

<button
    class="btn btn-sm btn-outline-danger removeStudentBtn"
    data-student-id="<?= $student['id'] ?>">
    Remove from Class
</button>

Then use event delegation:

document.addEventListener('click', async (e) => {

    const btn = e.target.closest('.removeStudentBtn');

    if (!btn) return;

    if (!confirm(
        'Remove this student from the class?'
    )) {
        return;
    }

    // fetch(...)
});


---

One improvement

Instead of passing only student_id, I'd also pass the enrollment ID:

data-enrollment-id="<?= $student['enrollment_id'] ?>"

Then your model can simply delete:

DELETE
FROM report_enrollment
WHERE id = ?

Using the enrollment's primary key is safer and more future-proof than identifying a row by (session_id, student_id), especially if your schema evolves later.
