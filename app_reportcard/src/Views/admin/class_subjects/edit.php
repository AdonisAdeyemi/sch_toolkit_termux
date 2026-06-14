<?php
/** @var array $class */
/** @var array $subjects */
/** @var array $departments */

$groupedSubjects = [];

foreach ($subjects as $subject) {
    $groupedSubjects[$subject['subject_group']][] = $subject;
}

/**
 * Group title mapper
 */
function groupTitle($key)
{
    return match ($key) {
        'general' => 'GENERAL',
        'primary_1_3' => 'PRIMARY 1–3',
        'primary_4_6' => 'PRIMARY 4–6',
        'jss' => 'JSS 1–3',
        'sss' => 'SSS 1–3',
        'trade' => 'TRADE SUBJECTS',
        default => strtoupper($key)
    };
}
?>

<h2>Class Subjects</h2>
<h3><?= htmlspecialchars($class['class_name'] ?? '') ?></h3>

<div id="msg" style="margin:10px 0;color:green;"></div>

<div style="margin-bottom:10px;">
    <span id="subjectCount" class="badge bg-primary">
        Selected: 0
    </span>
</div>


<form id="subjectForm">

    <input type="hidden" name="class_id" value="<?= (int)$class['id'] ?>">

    <div class="accordion" id="subjectAccordion">

        <?php foreach ($groupedSubjects as $group => $items): ?>

            <?php
                $collapseId = 'collapse_' . $group;
                $headingId = 'heading_' . $group;
            ?>

            <div class="accordion-item">

                <h2 class="accordion-header" id="<?= $headingId ?>">

                    <button class="accordion-button collapsed"
                            type="button"
                            data-bs-toggle="collapse"
                            data-bs-target="#<?= $collapseId ?>">

                        <?= groupTitle($group) ?>

                        <span class="badge bg-secondary ms-2">
                            <?= count($items) ?>
                        </span>

                    </button>

                </h2>

                <div id="<?= $collapseId ?>"
                     class="accordion-collapse collapse"
                     data-bs-parent="#subjectAccordion">

                    <div class="accordion-body">

                        <?php foreach ($items as $subject): ?>

                            <?php
                                $id = (int)$subject['report_subject_id'];
                                $checked = $subject['is_assigned'] ? 'checked' : '';
                                $currentDepartmentId = $subject['department_id'];
                            ?>

                            <!-- SUBJECT ROW -->
                            <label class="d-flex justify-content-between align-items-center mb-2">

                                <div>
                                    <input type="checkbox"
                                           class="subject-checkbox"
                                           name="subjects[]"
                                           value="<?= $id ?>"
                                           <?= $checked ?>>

                                    <span style="margin-left:6px;">
                                        <?= htmlspecialchars($subject['subject_name']) ?>
                                    </span>
                                </div>

 <?= groupChip($subject['subject_group']) ?>

                            </label>

                            <!-- DEPARTMENTS -->
                            <div class="ms-4 mb-3">

                                <?php foreach ($departments as $department): ?>

                                    <label class="me-3">

                                        <input type="radio"
                                               class="department-radio"
                                               data-department-name="<?= strtolower($department['name']) ?>"
                                               name="department[<?= $id ?>]"
                                               value="<?= (int)$department['id'] ?>"
                                               <?= $currentDepartmentId == $department['id'] ? 'checked' : '' ?>>

                                        <?= htmlspecialchars($department['name']) ?>

                                    </label>

                                <?php endforeach; ?>

                            </div>

                            <hr>

                        <?php endforeach; ?>

                    </div>

                </div>

            </div>

        <?php endforeach; ?>

    </div>

    <button type="submit" id="btnSave" class="btn btn-primary mt-3">
        Save Subjects
    </button>

</form>

<script>
$(document).ready(function () {

    /******************************
     * SAVE SUBJECTS (AJAX)
     ******************************/
    $('#subjectForm').on('submit', function (e) {
        e.preventDefault();

        $('#btnSave').prop('disabled', true).text('Saving...');

        $.ajax({
            url: '/<?= $appName ?>/classes/' + <?= (int)$class['id'] ?> + '/subjects',
            method: 'POST',
            data: $(this).serialize(),
            dataType: 'json',

            success: function (res) {

                if (res.status === 'success') {

   console.log ("success - res:",res);

                    let msg = 'Subjects updated successfully';

                    if (res.added?.length) {
                        msg += ` | Added: ${res.added.length}`;
                    }

                    if (res.removed?.length) {
                        msg += ` | Removed: ${res.removed.length}`;
                    }
                    
                    if (res.updated?.length) {
                        msg += ` | Updated: ${res.updated.length}`;
                    }

                    if (res.blocked?.length) {
                        msg += ` | Blocked: ${res.blocked.length} (has results)`;
                        $('#msg').css('color', 'orange');
                    } else {
                        $('#msg').css('color', 'green');
                    }

                    $('#msg').text(msg);

                } else {
                    $('#msg')
                        .css('color', 'red')
                        .text(res.message || 'Update failed');
                }
            },

//xxxxxxxxxxxxxxxx

error: function (xhr, status, error) {

    console.log('AJAX ERROR OBJECT:', xhr);
    console.log('STATUS:', status);
    console.log('ERROR:', error);
    console.log('RESPONSE TEXT:', xhr.responseText);

    let msg = 'Server error';

    // Try to extract JSON error if backend returned JSON
    try {
        const res = JSON.parse(xhr.responseText);

        if (res.message) {
            msg = res.message;
        } else if (res.error) {
            msg = res.error;
        }

    } catch (e) {
        // response is not JSON (probably PHP error / HTML)
        msg = xhr.responseText || msg;
    }

    $('#msg')
        .css('color', 'red')
        .text(msg);
},

//xxxxx


            complete: function () {
                $('#btnSave').prop('disabled', false).text('Save Subjects');
            }
        });
    });

    /******************************
     * CHECKBOX → AUTO DEPARTMENT
     ******************************/
    $(document).on('change', '.subject-checkbox', function () {

        const $checkbox = $(this);

        const $container =
            $checkbox.closest('label').next('div');

        if ($checkbox.is(':checked')) {

            const alreadySelected =
                $container.find('.department-radio:checked').length > 0;

            if (!alreadySelected) {

                $container
                    .find('.department-radio[data-department-name="general"]')
                    .prop('checked', true);
            }

        } else {

            $container.find('.department-radio').prop('checked', false);
        }
    });

    /******************************
     * DEPARTMENT → AUTO CHECK SUBJECT
     ******************************/
    $(document).on('change', '.department-radio', function () {

    
    $(this).closest('.accordion-body')
    .find('.subject-checkbox')
    .first()
    .prop('checked', true);

updateSubjectCount();
});


/********* Update Subject checked  **********/
function updateSubjectCount() {
    const count = $('.subject-checkbox:checked').length;
    $('#subjectCount').text('Selected: ' + count);
}

$(document).on('change', '.subject-checkbox', function () {
    updateSubjectCount();
});

$(document).ready(function () {
    updateSubjectCount();
});


})





</script>










