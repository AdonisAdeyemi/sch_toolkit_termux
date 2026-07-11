==================================================
TODO: Department Subdivisions (Arts → CRS / IRS)
==================================================

DATABASE
□ Create report_department_subdivisions
□ Add department_id FK
□ Seed:
    □ CRS
    □ IRS

□ Add nullable department_subdivision_id to:
    □ report_class_subjects
    □ report_student_enrollments

MODELS
□ DepartmentSubdivisionModel
□ getSubdivisionsByDepartment()
□ getSubdivisionIdByName()

CLASS SUBJECTS
□ Update create()
□ Update edit()
□ Auto-assign subdivision for:
    □ CRS
    □ IRS

ENROLLMENT
□ Add subdivision dropdown
□ Save department_subdivision_id
□ Update edit enrollment
□ Validate subdivision belongs to selected department

RESULTS
□ Filter subjects using subdivision
□ Include:
    □ General department subjects
    □ Matching subdivision subjects

REPORT CARD
□ Verify CRS students don't see IRS
□ Verify IRS students don't see CRS
□ Verify Science unaffected
□ Verify Commercial unaffected

TESTING
□ Arts → CRS
□ Arts → IRS
□ Science
□ Commercial
□ JSS departments
□ Existing schools migrate correctly

FUTURE REFACTOR
□ Remove hardcoded CRS/IRS logic
□ Make subdivision assignment data-driven
□ CRUD for department subdivisions
□ Support unlimited subdivisions
