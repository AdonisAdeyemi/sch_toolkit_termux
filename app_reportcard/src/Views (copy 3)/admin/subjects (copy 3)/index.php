<h2>Subjects Names</h2>

<!-- ADD BUTTON -->
<button type="button" data-bs-toggle="modal" data-bs-target="#subjectModal">
    + Add Subject
</button>

<hr>

<!-- SUBJECT TABLE -->
<?php if (empty($subjects)): ?>
    <p>No subjects found.</p>
<?php else: ?>
    <table border="1" cellpadding="10" cellspacing="0">
        <thead>
            <tr>
                <th>#</th>
                <th>Subject Name</th>
                <th>Type</th>
                <th width="180">Actions</th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($subjects as $index => $subject): ?>
                <tr>
                    <td><?= $index + 1 ?></td>

                    <td>
                        <?= htmlspecialchars($subject['subject_name']) ?>
                    </td>

                    <td>
                        <?php if ($subject['is_custom'] == 0): ?>
                            <span style="color: green; font-weight: bold;">
                                Default
                            </span>
                        <?php else: ?>
                            <span style="color: blue; font-weight: bold;">
                                Custom
                            </span>
                        <?php endif; ?>
                    </td>

                    <td>

                        <?php if ($subject['is_custom'] == 0): ?>

                            <span style="color: #888;">
                                Locked
                            </span>

                        <?php else: ?>

                            <!-- EDIT -->
                            <button
                                type="button"
                                data-bs-toggle="modal"
                                data-bs-target="#editSubjectModal<?= $subject['id'] ?>">
                                Edit
                            </button>

                            |

                            <!-- DELETE -->
                            <form method="POST"
                                  action="/<?= $appName ?>/admin/subjects/delete"
                                  style="display:inline;">

                                <input type="hidden"
                                       name="id"
                                       value="<?= $subject['id'] ?>">

                                <button type="submit"
                                        onclick="return confirm('Delete this subject?')">
                                    Delete
                                </button>

                            </form>

                        <?php endif; ?>

                    </td>
                </tr>

                <!-- ========================= -->
                <!-- EDIT SUBJECT MODAL -->
                <!-- ========================= -->
                <?php if ($subject['is_custom'] == 1): ?>

                    <div class="modal fade"
                         id="editSubjectModal<?= $subject['id'] ?>"
                         tabindex="-1"
                         aria-hidden="true">

                        <div class="modal-dialog">
                            <div class="modal-content">

                                <form method="POST"
                                      action="/<?= $appName ?>/admin/subjects/update">

                                    <input type="hidden"
                                           name="id"
                                           value="<?= $subject['id'] ?>">

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

                                        <label>Subject Name</label>

                                        <input
                                            type="text"
                                            name="subject_name"
                                            class="form-control"
                                            value="<?= htmlspecialchars($subject['subject_name']) ?>"
                                            required>

                                    </div>

                                    <div class="modal-footer">

                                        <button type="button"
                                                class="btn btn-secondary"
                                                data-bs-dismiss="modal">
                                            Cancel
                                        </button>

                                        <button type="submit"
                                                class="btn btn-primary">
                                            Update Subject
                                        </button>

                                    </div>

                                </form>

                            </div>
                        </div>

                    </div>

                <?php endif; ?>

            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>


<!-- ========================= -->
<!-- CREATE SUBJECT MODAL -->
<!-- ========================= -->
<div class="modal fade" id="subjectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <form method="POST"
                  action="/<?= $appName ?>/admin/subjects/store">

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

                    <button type="submit"
                            class="btn btn-primary">
                        Save Subject
                    </button>

                </div>

            </form>

        </div>
    </div>
</div>
