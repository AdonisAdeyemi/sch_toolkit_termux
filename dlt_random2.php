You're at a good milestone. I'd resist adding lots of filters right now and instead complete the core CRUD for the registry.

My suggested order is:

Phase 1 — Complete Student Registry CRUD

✅ Student list

✅ Search

⏳ New Student (modal + AJAX)

⏳ Edit Student (same modal reused)

⏳ Update passport

⏳ AJAX table reload


At this point, the registry is feature-complete.


---

Phase 2 — Connect Registry with Student Management

Replace the current "+ Existing Student" workflow so it becomes:

Student Management

↓

+ Existing Student

↓

Search Student Registry

↓

Select Student

↓

Enroll into selected Session + Class

↓

AJAX reload

Now you have one source of truth for students.


---

Phase 3 — Convenience features

Filter by Sex

Filter by Religion

Sort by Admission No.

Sort by Student Name

Pagination

Export (optional)



---

I would not build these yet

Delete student

Bulk import

Advanced filters

Student history


Those become much easier once CRUD is complete.


---

What I'd build next

Edit Student.

Why?

Because you already have:

getStudentById()

updateStudent()

updatePassportUrl()

modal_new.php


The "New Student" modal can become the "Edit Student" modal with very little extra work:

+ New Student
        │
        ▼
Same modal
        ▲
        │
Edit Student

So the next concrete task I'd tackle is:

1. Rename modal_new.php → modal_student.php


2. Make it support both Create and Edit


3. Implement StudentRegistryController::update()


4. Wire up the Edit button with AJAX.



That keeps you moving forward without duplicating UI.
