<div
    class="modal fade"
    id="studentModal"
    tabindex="-1"
    aria-hidden="true">

    <div class="modal-dialog modal-lg">

        <div class="modal-content">


    <form
       id="studentForm"
    action="/<?= $appName ?>/student_registry/save"
    method="POST"
       enctype="multipart/form-data">

                <div class="modal-header">

                    <h5  class="modal-title" id="studentModalTitle">

                        New Student

                    </h5>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal">
                    </button>

                </div>

                <div class="modal-body">

                    <input
                        type="hidden"
                        id="studentId"
                        name="student_id">

                    <div class="row">

                        <div class="col-md-8">

                            <div class="mb-3">

                                <label class="form-label">

                                    Student Name

                                </label>

                                <input
                                    type="text"
                                    class="form-control"
                                    id="studentName"
                                    name="student_name"
                                    required>

                            </div>

                        </div>

                       
<!-- xxxxxxxxxxxxxxxxxxxxxxxxxxx -->
<!-- xxxxxxxxxxxxxxxxxxxxxxxxxxx -->


                        <div class="col-md-4">

                            <div class="mb-3">

                                <label class="form-label">

                  Admission No. (optional)

                                </label>

                                <input
                                    type="text"
                                    class="form-control"
                                    id="admissionNo"
                                    name="admission_no">

                            </div>

                        </div>

                    </div>


                       
<!-- xxxxxxxxxxxxxxxxxxxxxxxxxxx -->
<!-- xxxxxxxxxxxxxxxxxxxxxxxxxxx -->



<div class="row">

<!-- Sex  -->

                        <div class="col-md-6">

                            <div class="mb-3">

                                <label class="form-label">

                                    Sex

                                </label>

                                <select
                                    class="form-select"
                                    id="sex"
                                    name="sex"
                                    required>

                                    <option value="">

                                        Select Sex

                                    </option>

                                    <option value="M">

                                        Male

                                    </option>

                                    <option value="F">

                                        Female

                                    </option>

                                </select>

                            </div>

                        </div>

</div>
                    
                    
                       
<!-- xxxxxxxxxxxxxxxxxxxxxxxxxxx -->
<!-- xxxxxxxxxxxxxxxxxxxxxxxxxxx -->


<div class="col-md-6">

    <div class="mb-3">

        <label class="form-label">

            Date of Birth (optional)

        </label>

        <input
            type="date"
            class="form-control"
            id="dateOfBirth"
            name="date_of_birth">

    </div>

</div>                    
                    

                       
<!-- xxxxxxxxxxxxxxxxxxxxxxxxxxx -->
<!-- xxxxxxxxxxxxxxxxxxxxxxxxxxx -->



                    <div class="mb-3">

                        <label class="form-label">

                            Passport (optional)
                        </label>

                        <input
                            type="file"
                            class="form-control"
                            id="passport"
                            name="passport"
                            accept="image/*">

                    </div>

                       
<!-- xxxxxxxxxxxxxxxxxxxxxxxxxxx -->
<!-- xxxxxxxxxxxxxxxxxxxxxxxxxxx -->


                    <div class="text-center">

                        <img
                            id="passportPreview"
                            src=""
                            style="
                                width:120px;
                                height:120px;
                                object-fit:cover;
                                border-radius:50%;
                                display:none;
                            ">

                    </div>

                </div>

                       
<!-- xxxxxxxxxxxxxxxxxxxxxxxxxxx -->
<!-- xxxxxxxxxxxxxxxxxxxxxxxxxxx -->


                <div class="modal-footer">

                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">

                        Cancel

                    </button>

                    <button
                        type="submit"
                        class="btn btn-primary"
                        id="saveStudentBtn">

                        Save Student

                    </button>

                </div>

                       
<!-- xxxxxxxxxxxxxxxxxxxxxxxxxxx -->
<!-- xxxxxxxxxxxxxxxxxxxxxxxxxxx -->


            </form>

        </div>

    </div>

</div>
