<?php /** @var array $classes */ ?>
<?php /** @var array $subjects (refactored :: now¸ class click dynamically loads $subjects with ajax)*/ ?>
<?php /** edit @var array $ac%ivePeriod */ ?>

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
            <select id="class_subject_id" class="form-control">
                <option value="">Select Subject</option>
<!-- class-subjects To be loaded dynamically with ajax, when class is clicked-->
            </select>
        </div>

        <div class="col-md-3">
            <label>Period</label>
            <select id="period_id" class="form-control">
                <option value="">Select Period</option>
       
                    <option value="<?= $activePeriod['period_id'] ?>">
                        <?= htmlspecialchars($activePeriod['period_name']) ?>
                    </option>

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

        <input type="hidden" name="class_subject_id" id="form_class_subject_id">
        <input type="hidden" name="period_id" id="form_period_id">

        <div id="gridContainer"></div>
        
        <div id= "grid_shown_div" style="display:none;">
        <div>Tip : Press "ENTER" key to move to next field. </div>

        <button type="submit" id="save-btn" class="btn btn-success mt-3">
            Save Results
        </button>
        </div>

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

//flag for disabled selectors - prevent change mid-edit
let isFiltersLocked = false;

    // =========================
    // LOAD GRID
    // =========================
    
    /* isFiltersLocked = true */
    
    $('#loadGrid').on('click', function () {

    
        if (!isFiltersLocked) {

        let class_id   = $('#class_id').val();
        let class_subject_id = $('#class_subject_id').val();
        let period_id  = $('#period_id').val();
        
    console.log("period_id : ", period_id) ;

        if (!class_id || !class_subject_id || !period_id) {
      let message = "Please select all fields";
        
            $('#msg').html(`<span style="color:red">${message}</span>`);
            
     
//Flash msg
         showFlash
         (
         [
         {'type':"danger",'text': message }
         ]
         )       
            
            return;
        }

        $('#msg').html('Loading...');

        $.ajax({
            url: '/<?= $appName ?>/results/load-subject-grid',
            method: 'POST',
            data: {
                class_id,
                class_subject_id,
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
                
$('#grid_shown_div').show();
   // lock dropdown  after success
    lockFilters();
    
     $('#msg').show();
    
            },

error: function (xhr, status, error) {

    console.log('AJAX ERROR OBJECT:', xhr);
    console.log('STATUS:', status);
    console.log('ERROR:', error);
    console.log('RESPONSE TEXT:', xhr.responseText);

let message = "Server error";

                $('#msg').html(`<span style="color:red">${message}</span>`);
                
     
//Flash msg
         showFlash
         (
         [
         {'type':"danger",'text': message }
         ]
         )       
            }
        });
        
        }
    });
    
 /**************************/
  /* isFiltersLocked = false */
//Now override button behavior when locked:

$(document).on('click', '#loadGrid', function (e) {

    if (isFiltersLocked) {
    
    let ok = confirm("Change class/subject?\nCurrent grid will be cleared.");

    if (ok) {
resetGridState();
    }
    
    }
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
                    <span class="student-name">
                    ${row.student_name}
                    </span>
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

        $('#gridContainer').html(html);
        
        $('#gridContainer').html(`
    <div class="table-scroll">
        ${html}
    </div>
`);

       recalcAllRows() ;
    }
    
 // recalculate AllRows
    
    function recalcAllRows() {

    $('#gridContainer tr').each(function () {

        let row = $(this);

        // skip header rows safely
        if (row.find('.ca1, .ca2, .exam').length === 0) {
            return;
        }

        updateTotalAndGradeUI(row);
    });
}

    // =========================
    // AUTO CALC (FRONTEND PREVIEW)
    // =========================

    
    function updateTotalAndGradeUI(row)
    {

            let ca1  = parseInt(row.find('.ca1').val()) || 0;
            let ca2  = parseInt(row.find('.ca2').val()) || 0;
            let exam = parseInt(row.find('.exam').val()) || 0;

            let total = ca1 + ca2 + exam;

            let grade = getGrade(total);

            row.find('.total').text(total);
            row.find('.grade').text(grade);


    };

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
        let class_subject_id = $('#class_subject_id').val();
        let period_id  = $('#period_id').val();

        // inject hidden fields
      $('#form_class_subject_id').val(class_subject_id);
      $('#form_period_id').val(period_id);

        $.ajax({
            url: '/<?= $appName ?>/results/save-subject-results',
            method: 'POST',
            data: $(this).serialize(),
            dataType: 'json',

            success: function (res) {

                if (res.status === 'success') {
                    $('#msg').html('<span style="color:green">' + res.message + '</span>');
         

//Flash msg
         showFlash
         (
         [
         {'type':"success",'text': res.message }
         ]
         )     
                } else {
                    $('#msg').html('<span style="color:red">' + res.message + '</span>');
    
//Flash msg
         showFlash
         (
         [
         {'type':"danger",'text': res.message }
         ]
         )     
                
            }
            }
            ,

error: function (xhr, status, error) {

    console.log('AJAX ERROR OBJECT:', xhr);
    console.log('STATUS:', status);
    console.log('ERROR:', error);
    console.log('RESPONSE TEXT:', xhr.responseText);

let message = "Save failed";

                $('#msg').html(`<span style="color:red">${message}</span>`);
                
     
//Flash msg
         showFlash
         (
         [
         {'type':"danger",'text': message}
         ]
         )
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

let message = "Error loading subjects";

                if (res.status !== 'success') {
                    $('#class_subject_id').html(`<option>${message}</option>`);
                    
       
//Flash msg
         showFlash
         (
         [
         {'type':"danger",'text': message}
         ]
         )
                    return;
                }

                let options = '<option value="">Select Subject</option>';
                
    if(res.data.length === 0)
 {
 //empty subjects assigned to class
options  += '<option value="">No Subjects Assigned to this Class yet. See Admin</option>';
 }

else
{
                res.data.forEach(function (subject) {
                    options += `
                        <option value="${subject.id}">
                            ${subject.subject_name}
                        </option>
                    `;
                });
 }

                $('#class_subject_id').html(options);
            },

            error: function (xhr) {

                console.log(xhr.responseText); // DEBUG RAW ERROR

let message = "Error loading subjects";
                $('#class_subject_id').html(`<option>${message}</option>`);
                
      
//Flash msg
         showFlash
         (
         [
         {'type':"danger",'text': message}
         ]
         )

            }
        });
    });
    
/************************/


//bind Auto Calc
//updateTotalAndGrade for every number entered

$(document).on('input', '.ca1, .ca2, .exam', function () {

    let row = $(this).closest('tr');

    updateTotalAndGradeUI(row);

    
});
     
     
 function moveNextInput($input) {

    let inputs = $('#gridContainer')
        .find('input.ca1, input.ca2, input.exam')
        .filter(':visible');

    let index = inputs.index($input);

    if (index === -1) return;

    let next = inputs.eq(index + 1);

    if (next.length) {
        next.focus();
    }
}

$(document).on('keydown', '.ca1, .ca2, .exam', function (e) {

    if (e.key === 'Enter') {
        e.preventDefault();
        moveNextInput($(this));
    }
});


$(document).on('focus', '.ca1, .ca2, .exam', function () {
    $(this).select();
});

/*******************/
//APPLY LOCK ON LOAD GRID
function lockFilters() {

    isFiltersLocked = true;

    $('#class_id, #class_subject_id, #period_id')
        .prop('disabled', true)
        .addClass('bg-light');

    $('#loadGrid')
        .removeClass('btn-primary')
        .addClass('btn-warning')
        .text('Change Class/Subject (Reset Page)');
}

function unlockFilters() {
    isFiltersLocked = false;

    $('#class_id, #class_subject_id, #period_id')
        .prop('disabled', false)
        .removeClass('bg-light');

    $('#loadGrid')
        .removeClass('btn-warning')
        .addClass('btn-primary')
        .text('Load Students');
}



/************/
function resetGridState() {

    // 1. clear subject dropdown
    /*
    $('#class_subject_id').html(`
        <option value="">Select Subject</option>
    `);
    */

    // 2. clear grid
    $('#gridContainer').empty();

    // 3. unlock filters
unlockFilters() 

    // 4. reset button
    /*
    $('#loadGrid')
        .removeClass('btn-warning')
        .addClass('btn-primary')
        .text('Load Students');
        */
        
    //remove info
    $('#grid_shown_div').hide();
    $('#msg').hide();
}



});
</script>











