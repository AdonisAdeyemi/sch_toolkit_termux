
<div class="modal fade" id="studentModal" tabindex="-1">

    <div class="modal-dialog modal-lg">

        <div class="modal-content">

            <form
                id="studentForm"
                enctype="multipart/form-data">

                <input
                    type="hidden"
                    id="studentId"
                    name="student_id">

                <input
                    type="hidden"
                    name="session_id"
                    value="<?= $sessionId ?>">
           <!-- sessionId is not supplied by controller::index() - set by js -->

                <div class="modal-header">

                    <h5 class="modal-title"   id="studentModalTitle">

                        New Student

                    </h5>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal">
                    </button>

                </div>

                <div class="modal-body">

                    <div class="row">

                        <div class="col-md-8 mb-3">

                            <label class="form-label">

                                Full Name

                            </label>

                            <input
                                class="form-control"
                                name="student_name"
                                required>

                        </div>

                        <div class="col-md-4 mb-3">

                            <label class="form-label">

                                Admission No. (optional)

                            </label>

                            <input
                                class="form-control"
                                name="admission_no">

                        </div>

                    </div>

                    <div class="row">

                        <div class="col-md-6 mb-3">

                            <label class="form-label">

                                Sex

                            </label>

                            <select
                                class="form-select"
                                name="sex"
                                required>

                                <option value="">
                                    Select
                                </option>

                                <option value="M">
                                    Male
                                </option>

                                <option value="F">
                                    Female
                                </option>

                            </select>

                        </div>

                        <div class="col-md-6 mb-3">

                            <label class="form-label">

                                Religion

                            </label>

                            <select
                                class="form-select"
                                name="religion"
                                required>

                                <option value="">
                                    Select
                                </option>

                                <option value="CRS">
                                    CRS
                                </option>

                                <option value="IRS">
                                    IRS
                                </option>

                            </select>

                        </div>

                    </div>

                    <div class="mb-3">

                        <label class="form-label">

                            Passport (optional)

                        </label>

                        <input
                            type="file"
                            class="form-control"
                            name="passport"
                            accept="image/*">

                    </div>

                    <div
                        class="mb-3 text-center">

                        <img
                            id="passportPreview"
                            src=""
                            class="img-thumbnail"
                            style="max-height:120px;display:none;">

                    </div>

                    <div class="mb-3">

                        <label class="form-label">

                            Class

                        </label>

                        <select
                            class="form-select"
                            name="class_id"
                            required>

                            <?php foreach ($classes as $class): ?>

<option
value="<?= $class['id'] ?>"
<?= $classId == $class['id'] ? 'selected' : '' ?>
>

<?= $class['class_name'] ?>

</option>

                            <?php endforeach; ?>

                        </select>

                    </div>

                </div>

                <div class="modal-footer">

                    <button
                        class="btn btn-secondary"
                        data-bs-dismiss="modal"
                        type="button">

                        Cancel

                    </button>

                    <button
                        class="btn btn-primary"
                        id="saveStudentBtn"
                        type="submit">

                        Save Student

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>
