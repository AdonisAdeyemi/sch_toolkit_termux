Here's the checklist I'd follow to complete the Existing Student Enrollment feature.

Phase 1 — Navigation

✅ Add + Existing Student button

☐ Route to Enroll Existing Student page

☐ Pass session_id

☐ Pass class_id

☐ Validate both are selected before navigating



---

Phase 2 — MVC

Routes

☐ GET /existing-enrollment

☐ GET /existing-enrollment/table

☐ POST /existing-enrollment/enroll



---

Controller

☐ index()

☐ table()

☐ enroll()



---

Model

☐ getAvailableStudentsForEnrollment()

☐ isAlreadyEnrolled()

☐ enrollStudent()



---

Phase 3 — View

Header

Show:

☐ Session

☐ Class


Example

Enroll Existing Student

Session : 2026/2027
Class   : JSS2A


---

Search Filters

☐ Search

☐ Religion

☐ Sex

☐ Has Admission No

☐ Has Passport

☐ Has DOB



---

Student Table

Columns

☐ Passport

☐ Student

☐ Admission No

☐ Age

☐ Sex

☐ Religion

☐ Action



---

Action

☐ Enroll button



---

Phase 4 — AJAX

☐ Search

☐ Reload table

☐ Enroll

☐ Reload after enroll



---

Phase 5 — Validation

Before enrolling

☐ Session exists

☐ Class exists

☐ Student exists

☐ Student not already enrolled



---

Phase 6 — Messages

Success

Student enrolled successfully.

Duplicate

Student is already enrolled in this class.

Failure

Unable to enroll student.


---

Phase 7 — Nice Touches

☐ Back to Class Students button

☐ Show number of search results

☐ Disable Enroll button while processing

☐ Flash message after success



---

Future Enhancements (Optional)

☐ Multi-select enroll

☐ Pagination

☐ Live search

☐ Bulk enroll by CSV

☐ Show previous class

☐ Show current class

☐ Recently enrolled highlight



---

Overall Progress

Student Registry

✅ Search

✅ Filters

✅ Add Student

✅ Edit Student

✅ DOB

✅ Age display


Class Students

✅ New Student (Register + Enroll)

✅ Remove from Class

☐ Existing Student (current task)


CSV

☐ Student Registry import

☐ Enrollment import (optional)


Once Existing Student Enrollment is complete, the core student management workflow will be functionally complete, leaving CSV import and later enhancements as the remaining major tasks.
