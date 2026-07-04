<?php /** @var array $classes */ ?>
<?php /** @var array $subjects */ ?>
<?php /** @var array $periods */ ?>

<h3>Results Entry</h3>

<ul class="nav nav-tabs">
    <li class="nav-item">
        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#bySubject">
            By Subject
        </button>
    </li>

    <li class="nav-item">
        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#byStudent">
            By Student
        </button>
    </li>
</ul>

<div class="tab-content mt-3">

<!-- ===================== SUBJECT ENTRY ===================== -->
<div class="tab-pane fade show active" id="bySubject">

    <div class="row mb-3">

        <div class="col-md-3">
            <label>Class</label>
            <select id="class_id" class="form-control">
                <option value="">Select Class</option>
                <?php foreach ($classes as $c): ?>
                    <option value="<?= $c['id'] ?>">
                        <?= htmlspecialchars($c['class_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-md-3">
            <label>Subject</label>
            <select id="subject_id" class="form-control">
                <option value="">Select Subject</option>
                <?php foreach ($subjects as $s): ?>
                    <option value="<?= $s['id'] ?>">
                        <?= htmlspecialchars($s['subject_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-md-3">
            <label>Period</label>
            <select id="period_id" class="form-control">
                <option value="">Select Period</option>
                <?php foreach ($periods as $p): ?>
                    <option value="<?= $p['id'] ?>">
                        <?= htmlspecialchars($p['session']) ."|Term_". htmlspecialchars($p['term']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-md-3 mt-2 d-flex align-items-end">
        
            <button id="loadGrid" class="btn btn-primary w-100">
                Load Students
            </button>
        </div>

    </div>

    <!-- GRID -->
    <form id="resultForm">

        <input type="hidden" name="class_subject_id" id="class_subject_id">
        <input type="hidden" name="period_id" id="form_period_id">

        <div id="gridContainer"></div>

        <button type="submit" class="btn btn-success mt-3">
            Save Results
        </button>

    </form>

    <div id="msg" class="mt-3"></div>

</div>

<!-- ===================== STUDENT ENTRY (placeholder for now) ===================== -->
<div class="tab-pane fade" id="byStudent">
    <p>Student Entry UI (Phase 2)</p>
</div>

</div>


<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script>
$(document).ready(function () {

    // =========================
    // LOAD GRID
    // =========================
    $('#loadGrid').on('click', function () {

        let class_id   = $('#class_id').val();
        let subject_id = $('#subject_id').val();
        let period_id  = $('#period_id').val();

        if (!class_id || !subject_id || !period_id) {
            $('#msg').html('<span style="color:red">Please select all fields</span>');
            return;
        }

        $('#msg').html('Loading...');

        $.ajax({
            url: '/<?= $appName ?>/results/load-subject-grid',
            method: 'POST',
            data: {
                class_id,
                subject_id,
                period_id
            },
            dataType: 'json',

            success: function (res) {
            
            console.log ("res.data >>> ",res.data);

                if (res.status !== 'success') {
                    $('#msg').html('<span style="color:red">' + res.message + '</span>');
                    return;
                }

                renderGrid(res.data);

                $('#msg').html('<span style="color:green">Loaded</span>');
            },

error: function (xhr, status, error) {

    console.log('AJAX ERROR OBJECT:', xhr);
    console.log('STATUS:', status);
    console.log('ERROR:', error);
    console.log('RESPONSE TEXT:', xhr.responseText);


                $('#msg').html('<span style="color:red">Server error</span>');
            }
        });
    });

    // =========================
    // RENDER GRID
    // =========================
    function renderGrid(data) {

        let html = `
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Student</th>
                    <th>CA1 (10)</th>
                    <th>CA2 (20)</th>
                    <th>Exam (70)</th>
                    <th>Total</th>
                    <th>Grade</th>
                </tr>
            </thead>
            <tbody>
        `;

        data.forEach((row, i) => {

            html += `
            <tr>

                <td>
                    ${row.student_name}
                    <input type="hidden" name="student_id[]" value="${row.student_id}">
                </td>

                <td>
                    <input type="number" class="ca1 form-control"
                        name="ca1[]" value="${row.ca1_score ?? ''}" min="0" max="10">
                </td>

                <td>
                    <input type="number" class="ca2 form-control"
                        name="ca2[]" value="${row.ca2_score ?? ''}" min="0" max="20">
                </td>

                <td>
                    <input type="number" class="exam form-control"
                        name="exam[]" value="${row.exam_score ?? ''}" min="0" max="70">
                </td>

                <td class="total">0</td>
                <td class="grade">-</td>

            </tr>
            `;
        });

        html += `</tbody></table>`;

        $('#gridContainer').html(html);

        bindAutoCalc();
    }

    // =========================
    // AUTO CALC (FRONTEND PREVIEW)
    // =========================
    function bindAutoCalc() {

        $(document).on('input', '.ca1, .ca2, .exam', function () {

            let row = $(this).closest('tr');

            let ca1  = parseInt(row.find('.ca1').val()) || 0;
            let ca2  = parseInt(row.find('.ca2').val()) || 0;
            let exam = parseInt(row.find('.exam').val()) || 0;

            let total = ca1 + ca2 + exam;

            let grade = getGrade(total);

            row.find('.total').text(total);
            row.find('.grade').text(grade);
        });
    }

    function getGrade(total) {
        if (total >= 70) return 'A';
        if (total >= 60) return 'B';
        if (total >= 50) return 'C';
        if (total >= 45) return 'D';
        if (total >= 40) return 'E';
        return 'F';
    }

    // =========================
    // SAVE RESULTS
    // =========================
    $('#resultForm').on('submit', function (e) {

        e.preventDefault();

        $('#msg').html('Saving...');

        let class_id   = $('#class_id').val();
        let subject_id = $('#subject_id').val();
        let period_id  = $('#period_id').val();

        // inject hidden fields
      //  $('#class_sxxxubject_id').val(subject_id);
      //  $('#form_period_id').val(period_id);

        $.ajax({
            url: '/<?= $appName ?>/results/save-subject-results',
            method: 'POST',
            data: $(this).serialize(),
            dataType: 'json',

            success: function (res) {

                if (res.status === 'success') {
                    $('#msg').html('<span style="color:green">' + res.message + '</span>');
                } else {
                    $('#msg').html('<span style="color:red">' + res.message + '</span>');
                }
            },

error: function (xhr, status, error) {

    console.log('AJAX ERROR OBJECT:', xhr);
    console.log('STATUS:', status);
    console.log('ERROR:', error);
    console.log('RESPONSE TEXT:', xhr.responseText);


                $('#msg').html('<span style="color:red">Save failed</span>');
            }
        });
    });
    
 /*xxxxxxxxxxxx
 LOAD SUBJECTS - WHEN CLASS IS CLICKED
 xxxxxxxxxxxxxxx*/
    
    $('#class_id').on('change', function () {

        let classId = $(this).val();

        $('#subject_id').html('<option>Loading...</option>');

        if (!classId) {
            $('#subject_id').html('<option value="">Select Subject</option>');
            return;
        }

        $.ajax({
            url: '/<?= $appName ?>/classes/' + classId + '/subject_list',
            method: 'GET',
            dataType: 'json',

            success: function (res) {
            
            console.log("result_success : ", res)

                if (res.status !== 'success') {
                    $('#subject_id').html('<option>Error loading subjects</option>');
                    return;
                }

                let options = '<option value="">Select Subject</option>';

                res.data.forEach(function (subject) {
                    options += `
                        <option value="${subject.id}">
                            ${subject.subject_name}
                        </option>
                    `;
                });

                $('#subject_id').html(options);
            },

            error: function (xhr) {

                console.log(xhr.responseText); // DEBUG RAW ERROR

                $('#subject_id').html('<option>Error loading subjects</option>');
            }
        });
    });

     

});
</script>










