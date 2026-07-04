<!DOCTYPE html>
<html>
<head>
    <title>Classes</title>

    <link rel="stylesheet" href="/assets/css/bootstrap.min.css">
</head>

<body>

<?= $appName = $_SESSION["appName"] ;  ?>
<?php echo "<br>heeeey
<br> $appName
"; ?>

<div class="container mt-4">

<div class="d-flex justify-content-between align-items-center mb-3">

    <h3>Classes</h3>

    <div>
        <a href="/<?= $appName ?>/admin/classes" class="btn btn-outline-primary btn-sm">
            Active Classes
        </a> &nbsp;

        <a href="/<?= $appName ?>/admin/classes/deleted" class="btn btn-outline-secondary btn-sm">
            Deleted Classes
        </a>

<br><br>

        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createClassModal">
            + Create Class
        </button>
    </div>
    

</div>
<br>


    <!-- FLASH MESSAGE -->
    <?php if (!empty($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <?= $_SESSION['success']; unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($_SESSION['error'])): ?>
        <div class="alert alert-danger">
            <?= $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <!-- TABLE -->
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Class Name</th>
                <th>Students</th>
                <th width="150">Actions</th>
            </tr>
        </thead>

        <tbody>
        <?php foreach ($classes as $class): ?>
            <tr>
                <td><?= htmlspecialchars($class['class_name']) ?></td>

                <td>
                    <span class="badge bg-info">
                        <?= $class['student_count'] ?? 0 ?>
                    </span>
                </td>

                <td>
                    <!-- DELETE -->
                    <form method="POST" action="/<?= $appName ?>/admin/classes/delete" style="display:inline;">
                        <input type="hidden" name="id" value="<?= $class['id'] ?>">

                        <button class="btn btn-danger btn-sm"
                                onclick="return confirm('Delete this class?')">
                            Delete
                        </button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

</div>



<!-- CREATE CLASS MODAL -->
<div class="modal fade" id="createClassModal" tabindex="-1">
    <div class="modal-dialog">

        <form method="POST" action="/<?= $appName ?>/admin/classes/store" class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Create Class</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <label>Class Name</label>
                <input type="text"
                       name="class_name"
                       class="form-control"
                       placeholder="e.g SS1 A"
                       required>

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

















