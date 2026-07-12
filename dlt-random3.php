Enrollment CSV Import Checklist

Database

□ No schema changes required

□ Confirm report_student_enrollments supports upsert


UI

□ Add Import Enrollments button

□ Add Download CSV Template button

□ Create upload modal

□ Add drag-and-drop/file picker

□ Show import summary


CSV Template

□ Generate template


Columns:

□ Admission No

□ Class

□ Department

□ Department Subdivision


Controller

□ Upload CSV

□ Validate file type

□ Parse CSV

□ Call service


Service

□ Read rows

□ Skip empty rows

□ Validate required fields

□ Lookup student by admission number

□ Lookup class by name

□ Lookup department by name

□ Lookup subdivision by name (optional)

□ Validate subdivision belongs to department

□ Upsert enrollment

□ Count imported/skipped/errors

□ Return import summary


Model

□ getStudentByAdmissionNo()

□ getClassByName()

□ getDepartmentByName()

□ getSubdivisionByName()

□ Upsert enrollment


Validation

□ Student exists

□ Class exists

□ Department exists

□ Subdivision exists (if supplied)

□ Subdivision belongs to department

□ Admission number not blank


Result

□ Success message

□ Error summary

□ Row-specific error messages

□ Refresh enrollment table


Nice-to-have

□ Ignore duplicate rows within the CSV

□ Trim whitespace

□ Case-insensitive name matching

□ Accept blank subdivision for non-Arts departments
