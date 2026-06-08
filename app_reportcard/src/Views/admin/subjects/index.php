<h2>Subject Names</h2>

<div class="d-flex justify-content-between align-items-center mb-3">
    <button class="btn btn-primary btn-sm"
            data-bs-toggle="modal"
            data-bs-target="#subjectModal">
        + Add Subject
    </button>
</div>

<!-- TOGGLE -->
<div class="form-check form-switch">
    <input class="form-check-input" type="checkbox" id="toggleDeletedSubjects">
    <label class="form-check-label" for="toggleDeletedSubjects">
        Show Deleted Subjects
    </label>
</div>

<br>


<!-- ===================== -->
<!-- ACTIVE SUBJECTS -->
<!-- ===================== -->
<div id="activeSubjects">

    <table class="table table-bordered">

        <thead>
        <tr>
            <th>#</th>
            <th>Subject Name</th>
            <th>Type</th>
            <th>Actions</th>
        </tr>
        </thead>

        <tbody>

        <?php foreach ($activeSubjects as $index => $subject): ?>

            <tr>

                <td>
                    <?php echo $index + 1; ?>
                </td>

                <td>
                    <?php echo htmlspecialchars($subject['subject_name']); ?>
                </td>

                <td>
                    <?php if ($subject['is_custom'] == 0): ?>
                        <span class="badge bg-success">Default</span>
                    <?php else: ?>
                        <span class="badge bg-primary">Custom</span>
                    <?php endif; ?>
                </td>

                <td>

                    <?php if ($subject['is_custom'] == 0): ?>

                        <span class="text-muted">Locked</span>

                    <?php else: ?>

                        <!-- EDIT BUTTON -->
                        <button class="btn btn-sm btn-warning"
                                data-bs-toggle="modal"
                                data-bs-target="#editSubjectModal<?php echo $subject['id']; ?>">
                            Edit
                        </button>

                        <!-- DELETE -->
                        <form method="POST"
                              action="/<?php echo $appName; ?>/admin/subjects/delete"
                              style="display:inline;">

                            <input type="hidden"
                                   name="id"
                                   value="<?php echo $subject['id']; ?>">

                            <button class="btn btn-sm btn-danger"
                                    onclick="return confirm('Delete this subject?')">
                                Delete
                            </button>

                        </form>

                    <?php endif; ?>

                </td>

            </tr>

        <?php endforeach; ?>

        </tbody>

    </table>

</div>


<!-- ===================== -->
<!-- EDIT MODALS (OUTSIDE TABLE) -->
<!-- ===================== -->
<?php foreach ($activeSubjects as $subject): ?>

    <?php if ($subject['is_custom'] == 1): ?>

        <div class="modal fade"
             id="editSubjectModal<?php echo $subject['id']; ?>"
             tabindex="-1"
             aria-hidden="true">

            <div class="modal-dialog">

                <form method="POST"
                      action="/<?php echo $appName; ?>/admin/subjects/update"
                      class="modal-content">

                    <div class="modal-header">

                        <h5 class="modal-title">
                            Edit Subject
                        </h5>

                        <button type="button"
                                class="btn-close"
                                data-bs-dismiss="modal">
                        </button>

                    </div>

                    <div class="modal-body">

                        <input type="hidden"
                               name="id"
                               value="<?php echo $subject['id']; ?>">

                        <label>Subject Name</label>

                        <input type="text"
                               name="subject_name"
                               class="form-control"
                               value="<?php echo htmlspecialchars($subject['subject_name']); ?>"
                               required>

                    </div>

                    <div class="modal-footer">

                        <button type="button"
                                class="btn btn-secondary"
                                data-bs-dismiss="modal">
                            Cancel
                        </button>

                        <button type="submit"
                                class="btn btn-warning">
                            Update Subject
                        </button>

                    </div>

                </form>

            </div>

        </div>

    <?php endif; ?>

<?php endforeach; ?>


<!-- ===================== -->
<!-- DELETED SUBJECTS -->
<!-- ===================== -->
<div id="deletedSubjects" style="display:none;">

    <h5 class="mt-3">Deleted Subjects</h5>

    <table class="table table-bordered">

        <thead>
        <tr>
            <th>Subject Name</th>
            <th>Action</th>
        </tr>
        </thead>

        <tbody>

        <?php foreach ($deletedSubjects as $subject): ?>

            <tr>

                <td>
                    <?php echo htmlspecialchars($subject['subject_name']); ?>
                </td>

                <td>

                    <form method="POST"
                          action="/<?php echo $appName; ?>/admin/subjects/restore">

                        <input type="hidden"
                               name="id"
                               value="<?php echo $subject['id']; ?>">

                        <button class="btn btn-success btn-sm">
                            Restore
                        </button>

                    </form>

                </td>

            </tr>

        <?php endforeach; ?>

        </tbody>

    </table>

</div>


<!-- ===================== -->
<!-- CREATE MODAL -->
<!-- ===================== -->
<div class="modal fade" id="subjectModal" tabindex="-1">

    <div class="modal-dialog">

        <form method="POST"
              action="/<?php echo $appName; ?>/admin/subjects/store"
              class="modal-content">

            <div class="modal-header">

                <h5 class="modal-title">
                    Create Subject
                </h5>

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal">
                </button>

            </div>

            <div class="modal-body">

                <label>Subject Name</label>

                <input type="text"
                       name="subject_name"
                       class="form-control"
                       required>

            </div>

            <div class="modal-footer">

                <button type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">
                    Cancel
                </button>

                <button class="btn btn-primary">
                    Save Subject
                </button>

            </div>

        </form>

    </div>

</div>


<!-- ===================== -->
<!-- TOGGLE SCRIPT -->
<!-- ===================== -->
<script>
document.getElementById('toggleDeletedSubjects').addEventListener('change', function () {

    const deletedDiv = document.getElementById('deletedSubjects');

    if (this.checked) {
        deletedDiv.style.display = 'block';
    } else {
        deletedDiv.style.display = 'none';
    }

});
</script>
