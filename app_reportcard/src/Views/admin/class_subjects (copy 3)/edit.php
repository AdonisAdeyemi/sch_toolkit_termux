<?php
/** @var array $class */
/** @var array $subjects */
/** @var array $assigned */

var_dump ($subjects);
?>

<h2>Class Subjects</h2>

<h3><?= htmlspecialchars($class['class_name'] ?? '') ?></h3>

<div id="msg" style="margin:10px 0;color:green;"></div>

<form id="subjectForm">

    <input type="hidden" name="class_id" value="<?= (int)$class['id'] ?>">

    <div style="margin-top: 20px;">

        <?php foreach ($subjects as $subject): ?>

            <?php
                $id = (int)$subject['report_subject_id'];
$checked = $subject['is_assigned']
    ? 'checked'
    : '';
$currentDepartmentId =
    $subject['department_id'];
            ?>

            <label style="display:block; margin-bottom:8px;">
                
 <input
    type="checkbox"
    class="subject-checkbox"
    name="subjects[]"
    value="<?= (int)$subject['report_subject_id'] ?>"
    <?= $checked ?>
>
                
                <?= htmlspecialchars($subject['subject_name']) ?>
                
            </label>
            
            
            
            <div style="margin-left:25px; margin-top:5px;">

<?php foreach ($departments as $department): ?>

<label>

<input
    type="radio"
    class="department-radio"
    data-department-name="<?= strtolower($department['name']) ?>"
    name="department[<?= (int)$subject['report_subject_id'] ?>]"
    value="<?= (int)$department['id'] ?>"
    <?= $currentDepartmentId == $department['id']
        ? 'checked'
        : '' ?>
>

    <?= htmlspecialchars($department['name']) ?>

</label>

<?php endforeach; ?>

        <hr>
</div>

        <?php endforeach; ?>

    </div>

    <button type="submit" id="btnSave" style="margin-top: 15px;">
        Save Subjects
    </button>

</form>

<!-- script src="/shared/public/assets/js/jquery-3.7.1.min.js"></script -->

<script>
$(document).ready(function () {

    $('#subjectForm').on('submit', function (e) {
        e.preventDefault();

        $('#btnSave').prop('disabled', true).text('Saving...');

        $.ajax({
            url: '/classes/' + <?= (int)$class['id'] ?> + '/subjects',
            method: 'POST',
            data: $(this).serialize(),
            dataType: 'json',

            success: function (res) {

    if (res.status === 'success') {

        let msg = 'Subjects updated successfully';

        // Show added/removed details if available
        if (res.added && res.added.length) {
            msg += ` | Added: ${res.added.length}`;
        }

        if (res.removed && res.removed.length) {
            msg += ` | Removed: ${res.removed.length}`;
        }

        // Warn if some were blocked (IMPORTANT for your report_results safety)
        if (res.blocked && res.blocked.length) {
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
}
            
            ,

            error: function () {
                $('#msg')
                    .css('color', 'red')
                    .text('Server error');
            },

            complete: function () {
                $('#btnSave').prop('disabled', false).text('Save Subjects');
            }
        });
    });

/***********************/
$(document).on(
    'change',
    '.subject-checkbox',
    function () {

        const $checkbox = $(this);

        const $container =
            $checkbox
                .closest('label')
                .next('div');

        const radioName =
            $container
                .find('.department-radio')
                .first()
                .attr('name');

        if ($checkbox.is(':checked')) {

            const alreadySelected =
                $container
                    .find('.department-radio:checked')
                    .length > 0;

            if (!alreadySelected) {

                $container
                    .find(
                        '.department-radio[data-department-name="general"]'
                    )
                    .prop('checked', true);
            }

        } else {

            $container
                .find(
                    '.department-radio'
                )
                .prop('checked', false);
        }
    }
);

/*********************************/
$(document).on(
    'change',
    '.department-radio',
    function () {

        $(this)
            .closest('div')
            .prev('label')
            .find('.subject-checkbox')
            .prop('checked', true);
    }
);




});
</script>







