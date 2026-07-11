

function populateDepartments (classId, selector = '.department_select') {

console.log("> in populateDepartments")

    const cls = referenceData.classes[classId];

    if (!cls) return;

    const classLevel = cls.class_level.toUpperCase();
    const departments = referenceData.departments[classLevel] || [];

console.log("classLevel",classLevel)

console.log("departments",departments)
    document.querySelectorAll(selector).forEach(select => {
        select.innerHTML = '<option value="0">Select Department </option>';

        departments.forEach(department => {
            select.insertAdjacentHTML(
                'beforeend',
                `<option value="${department.id}">${department.name}</option>`
            );
        });
    });
}

/**************************/


function toggleSubdivision( department, subdivision, artsDepartmentId ) {
    const isArts =
        parseInt(department.value, 10) === artsDepartmentId;

    subdivision.disabled = !isArts;

    if (!isArts) {
        subdivision.value = '';
    }
}

/****** for reportcard NOT exactly super relevant elsewhere to be in /shared: refactor from /public/assets/shared/ to /public/assets/reportcard/ && others too  *****/
function populateSubdivisions(
    departmentId,
    subdivisionSelect
) {
    subdivisionSelect.innerHTML =
        '<option value="">Select Subdivision</option>';

    if (parseInt(departmentId, 10) !== ARTS_DEPARTMENT_ID) {

        subdivisionSelect.disabled = true;
        return;
    }

    subdivisionSelect.disabled = false;

    const subdivisions =
        referenceData.subdivisions[departmentId] || [];

    subdivisions.forEach(subdivision => {

        subdivisionSelect.insertAdjacentHTML(
            'beforeend',
            `<option value="${subdivision.id}">
                ${subdivision.name}
            </option>`
        );

    });
}














