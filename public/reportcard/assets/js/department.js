function populateDepartments(classId, selector = '.department_select') {


    const cls = referenceData.classes[classId];

    if (!cls) return;

    const classLevel = cls.class_level.toUpperCase();
    const departments = referenceData.departments[classLevel] || [];

console.log("classLevel",classLevel)

console.log("departments",departments)
    document.querySelectorAll(selector).forEach(select => {
        select.innerHTML = '<option value="">Select Department</option>';

        departments.forEach(department => {
            select.insertAdjacentHTML(
                'beforeend',
                `<option value="${department.id}">${department.name}</option>`
            );
        });
    });
}
