<!-- ===================== HEADER ===================== -->
<div class="d-flex justify-content-between align-items-center mb-3">

    <h3>Classes</h3>

    <button class="btn btn-primary btn-sm"
            data-bs-toggle="modal"
            data-bs-target="#createClassModal">
        + Create Class
    </button>
</div>

<br>

<!-- ===================== TOGGLE ===================== -->
<div class="form-check form-switch mb-3">
    <input class="form-check-input" type="checkbox" id="toggleDeleted">
    <label class="form-check-label" for="toggleDeleted">
        Show Deleted Classes
    </label>
</div>

<!-- ===================== ACTIVE CLASSES ===================== -->
<div id="activeClasses">

    <table class="table table-bordered">

        <thead>
        <tr>
            <th>Class</th>
            <th>Level</th>
            <th>Students</th>
            <th>Actions</th>
        </tr>
        </thead>

        <tbody>
        <?php foreach ($activeClasses as $class): ?>
            <tr>

                <!-- CLASS TEMPLATE NAME -->
                <td>
                    <strong>
                        <?= htmlspecialchars($class['class_name'] ?? 'N/A') ?>
                    </strong>
                </td>

                <!-- LEVEL BADGE -->
                <td>
                    <span class="badge bg-secondary">
                        <?= strtoupper($class['class_level'] ?? '') ?>
                    </span>
                </td>

                <!-- STUDENTS -->
                <td>
                    <span class="badge bg-info">
                        <?= (int)($class['student_count'] ?? 0) ?>
                    </span>
                </td>

                <!-- ACTIONS -->
                <td>
                    <form method="POST"
                          action="/<?= $appName ?>/admin/classes/delete"
                          onsubmit="return confirm('Delete this class?')">

                        <input type="hidden" name="id" value="<?= $class['id'] ?>">

                        <button class="btn btn-danger btn-sm">
                            Delete
                        </button>
                    </form>
                </td>

            </tr>
        <?php endforeach; ?>
        </tbody>

    </table>
</div>

<!-- ===================== DELETED CLASSES ===================== -->
<div id="deletedClasses" style="display:none;">

    <h5 class="mt-3">Deleted Classes</h5>

    <table class="table table-bordered">

        <thead>
        <tr>
            <th>Class</th>
            <th>Level</th>
            <th>Action</th>
        </tr>
        </thead>

        <tbody>
        <?php foreach ($deletedClasses as $class): ?>
            <tr>

                <td>
                    <?= htmlspecialchars($class['class_name'] ?? 'N/A') ?>
                </td>

                <td>
                    <span class="badge bg-secondary">
                        <?= strtoupper($class['class_level'] ?? '') ?>
                    </span>
                </td>

                <td>
                    <form method="POST"
                          action="/<?= $appName ?>/admin/classes/restore">

                        <input type="hidden" name="id" value="<?= $class['id'] ?>">

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

<!-- ===================== CREATE CLASS MODAL ===================== -->
<div class="modal fade" id="createClassModal" tabindex="-1">
    <div class="modal-dialog">

        <form method="POST"
              action="/<?= $appName ?>/admin/classes/store"
              class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Create Class</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <label class="form-label">Class Template</label>

                <select name="class_template_id"
                        class="form-control"
                        required>

                    <?php foreach ($classTemplates as $t): ?>
                        <option value="<?= (int)$t['id'] ?>">
                            <?= htmlspecialchars($t['label']) ?>
                        </option>
                    <?php endforeach; ?>

                </select>

            </div>

            <div class="modal-footer">

                <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">
                    Cancel
                </button>

                <button class="btn btn-primary">
                    Save Class
                </button>

            </div>

        </form>

    </div>
</div>

<!-- ===================== JS TOGGLE ===================== -->
<script>
document.getElementById('toggleDeleted').addEventListener('change', function () {

    document.getElementById('deletedClasses').style.display =
        this.checked ? 'block' : 'none';

});
</script>
