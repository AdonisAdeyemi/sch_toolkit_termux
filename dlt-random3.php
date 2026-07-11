Good. Here's the implementation order I'd follow.


---

1. Controller

When loading the enrollment page, fetch subdivisions for Arts.

$artsId = $this->departmentModel
    ->getDepartmentIdByName('Arts');

$subdivisions = $this->departmentSubdivisionModel
    ->getSubdivisionsByDepartment($artsId);

Pass:

compact(
    ...
    'subdivisions'
)


---

2. View

Add a new select after Department.

<div class="col-md-6 mb-3">

    <label class="form-label">
        Department Subdivision
    </label>

    <select
        class="form-select"
        id="departmentSubdivision"
        name="department_subdivision_id"
        disabled>

        <option value="">
            -- None --
        </option>

        <?php foreach ($subdivisions as $subdivision): ?>

            <option
                value="<?= $subdivision['id'] ?>">

                <?= htmlspecialchars($subdivision['name']) ?>

            </option>

        <?php endforeach; ?>

    </select>

</div>


---

3. JavaScript

Enable only when Arts is selected.

const department = document.getElementById('department');
const subdivision = document.getElementById('departmentSubdivision');

department.addEventListener('change', function () {

    const isArts =
 this.options[this.selectedIndex].text.trim() === 'Arts';

    subdivision.disabled = !isArts;

    if (!isArts) {
        subdivision.value = '';
    }

});

(Later you can compare department IDs instead of the text.)


---

4. Controller

Read:

$departmentSubdivisionId =
    !empty($_POST['department_subdivision_id'])
        ? (int) $_POST['department_subdivision_id']
        : null;

Pass it to the service/model.


---

5. Model

Update the UPSERT.

department_subdivision_id

Insert:

department_subdivision_id

Update:

department_subdivision_id =
VALUES(department_subdivision_id)


---

6. Edit Enrollment

When editing:

selected="<?= ... ?>"

Enable the dropdown if the student's department is Arts.


---

7. Validation

Before saving:

$subdivision = $this->departmentSubdivisionModel
    ->getSubdivision($departmentSubdivisionId);

if (
    $departmentSubdivisionId &&
    (
        !$subdivision ||
        $subdivision['department_id'] != $departmentId
    )
) {
    throw new ValidationException(
        'Invalid department subdivision.'
    );
}


---

One improvement

Instead of comparing the department name ('Arts') in JavaScript, output the Arts department ID from PHP and compare IDs. For example:

const ARTS_DEPARTMENT_ID = <?= (int) $artsId ?>;

Then:

subdivision.disabled =
    parseInt(department.value) !== ARTS_DEPARTMENT_ID;

This is more robust because it won't break if the department is renamed to something like "Arts & Humanities".
