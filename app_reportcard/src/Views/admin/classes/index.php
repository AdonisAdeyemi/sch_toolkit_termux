<!DOCTYPE html>
<html>
<head>
    <title>Classes</title>

    <link rel="stylesheet" href="/assets/css/bootstrap.min.css">
</head>

<body>

<div class="container mt-4">

<?php
var_dump ($_SESSION);
?>


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
  
  
  
  
  
  <div class="d-flex justify-content-between align-items-center mb-3">

    <h3>Classes</h3>


    <div>

        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createClassModal">
            + Create Class
        </button>
    </div>





<br>

</div>
  
  
    <div class="form-check form-switch">
        <input class="form-check-input" type="checkbox" id="toggleDeleted">
        <label class="form-check-label" for="toggleDeleted">
            Show Deleted Classes
        </label>
    </div>
  
      <br> 
  
  
  
  
  <div id="activeClasses">

    <table class="table table-bordered">
        <thead>
        <tr>
            <th>Class Name</th>
            <th>Students</th>
            <th>Actions</th>
        </tr>
        </thead>

        <tbody>
        <?php foreach ($activeClasses as $class): ?>
            <tr>
                <td><?= htmlspecialchars($class['class_name']) ?></td>

                <td>
                    <span class="badge bg-info">
                        <?= $class['student_count'] ?? 0 ?>
                    </span>
                </td>

                <td>
                    <form method="POST" action="/<?= $appName ?>/admin/classes/delete">
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
  
  
  
  
  
  
  
  
  
<div id="deletedClasses" style="display:none;">

    <h5 class="mt-3">Deleted Classes</h5>

    <table class="table table-bordered">
        <thead>
        <tr>
            <th>Class Name</th>
            <th>Action</th>
        </tr>
        </thead>

        <tbody>
        <?php foreach ($deletedClasses as $class): ?>
            <tr>
                <td><?= htmlspecialchars($class['class_name']) ?></td>

                <td>
                    <form method="POST" action="/<?= $appName ?>/admin/classes/restore">
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










<script>
document.getElementById('toggleDeleted').addEventListener('change', function () {

    const deletedDiv = document.getElementById('deletedClasses');

    if (this.checked) {
        deletedDiv.style.display = 'block';
    } else {
        deletedDiv.style.display = 'none';
    }

});
</script>















