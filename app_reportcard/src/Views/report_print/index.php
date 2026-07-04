<?php
$periodText = "Not Configured";
$lockText = "Unknown";
$lockBadge = "secondary";

if (!empty($activePeriod)) {

    $periodText =
        $activePeriod['session_name']
        . " - Term "
        . $activePeriod['term'];

    switch ((int)$activePeriod['lock_status']) {

        case 0:
            $lockText = "Open";
            $lockBadge = "success";
            break;

        case 1:
            $lockText = "Teacher Lock";
            $lockBadge = "warning";
            break;

        case 2:
            $lockText = "Permanent Lock";
            $lockBadge = "danger";
            break;
    }
}

$periodId = $activePeriod['period_id'] ?? 0;
?>

<div class="container py-4">

    <h2 class="mb-4">

        🖨 Generate Report Cards

    </h2>

    <div class="card mb-4">

        <div class="card-body">

            <div class="row">

                <div class="col-md-8">

                    <strong>

                        Current Period

                    </strong>

                    <br>

                    <?= htmlspecialchars($periodText) ?>

                </div>

                <div class="col-md-4 text-md-end">

                    <strong>

                        Result Status

                    </strong>

                    <br>

                    <span class="badge bg-<?= $lockBadge ?>">

                        <?= htmlspecialchars($lockText) ?>

                    </span>

                </div>

            </div>

        </div>

    </div>

    <div class="card">

        <div class="card-body">

            <div class="row align-items-end">

                <div class="col-md-6 mb-3">

                    <label class="form-label">

                        Class

                    </label>

                    <select
                        id="classId"
                        class="form-select">

                        <option value="">

                            Select Class

                        </option>

                        <?php foreach ($classes as $class): ?>

                            <option
                                value="<?= $class['id'] ?>">

                                <?= htmlspecialchars($class['class_name']) ?>

                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>

                <div class="col-md-3 mb-3">

                    <button
                        id="printClassBtn"
                        class="btn btn-primary w-100">

                        Print Entire Class

                    </button>

                </div>

                <div class="col-md-3 mb-3">

                    <button
                        id="showStudentsBtn"
                        class="btn btn-success w-100">

                        Show Students

                    </button>

                </div>

            </div>

        </div>

    </div>

    <div
        id="studentSection"
        class="card mt-4"
        style="display:none;">

        <div class="card-header">

            Students

        </div>

        <div class="card-body">

            <div
                id="emptyMessage"
                class="alert alert-info"
                style="display:none;">

                No students enrolled in this class.

            </div>

            <div class="table-responsive">

                <table
                    class="table table-bordered table-striped"
                    id="studentTable">

                    <thead>

                        <tr>

                            <th>

                                Admission No

                            </th>

                            <th>

                                Student

                            </th>

                            <th>

                                Department

                            </th>

                            <th width="120">

                                Action

                            </th>

                        </tr>

                    </thead>

                    <tbody>

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>

<script>

const periodId =
<?= (int)$periodId ?>;

const appName =
"<?= $appName ?>";

document
.getElementById(
    'printClassBtn'
)
.addEventListener(
'click',
function(){

const classId =
document
.getElementById(
'classId'
)
.value;

if(!classId){

alert(
'Please select a class.'
);

return;

}

window.open(

`/${appName}/generate/class?class_id=${classId}&period_id=${periodId}`,

'_blank'

);

}
);

document
.getElementById(
'showStudentsBtn'
)
.addEventListener(
'click',
loadStudents
);

function loadStudents(){

const classId =
document
.getElementById(
'classId'
)
.value;

if(!classId){

alert(
'Please select a class.'
);

return;

}

fetch(

`/${appName}/reports/students?class_id=${classId}`

)

.then(r=>r.json())

.then(data=>{

document
.getElementById(
'studentSection'
)
.style.display='block';

const tbody =
document
.querySelector(
'#studentTable tbody'
);

tbody.innerHTML='';

if(data.length===0){

document
.getElementById(
'emptyMessage'
)
.style.display='block';

return;

}

document
.getElementById(
'emptyMessage'
)
.style.display='none';

data.forEach(student=>{

tbody.insertAdjacentHTML(

'beforeend',

`
<tr>

<td>

${student.admission_no ?? ''}

</td>

<td>

${student.student_name}

</td>

<td>

${student.department_name ?? ''}

</td>

<td>

<a

class="btn btn-sm btn-primary"

target="_blank"

href="/${appName}/generate/student?student_id=${student.student_id}&period_id=${periodId}"

>

Print

</a>

</td>

</tr>

`

);

});

});

}

</script>
