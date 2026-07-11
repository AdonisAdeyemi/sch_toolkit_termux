<div class="card">

    <div class="card-header">

        Available Students

        <span class="badge bg-secondary float-end">

            <?= count($students) ?>

        </span>

    </div>

    <div class="table-responsive">

        <table class="table table-hover align-middle mb-0">

            <thead>

                <tr>

                    <th style="width:70px;">
                        Passport
                    </th>

                    <th>
                        Student
                    </th>

                    <th>
                        Admission No.
                    </th>

                    <th>
                        Age
                    </th>

                    <th>
                        Sex
                    </th>


                    <th>
                        Previous Class
                    </th>
                    
                    
                    <th>
                        Department
                    </th>
                    
              
                    <th>
                        Subdivision
                    </th>

                    <th style="width:120px;">
                        Action
                    </th>

                </tr>

            </thead>

            <tbody>

                <?php if (empty($students)): ?>

                    <tr>

                        <td
                            colspan="8"
                            class="text-center text-muted py-4">

                            No students available for enrollment.

                        </td>

                    </tr>

                <?php endif; ?>

                <?php foreach ($students as $student): ?>

                    <tr>

                        <td>

                            <?php if (!empty($student['passport_url'])): ?>

                                <img
                                    src="<?= getAssetUrl('passport',$student['passport_url']) ?>"
                                    style="
                                        width:45px;
                                        height:45px;
                                        border-radius:50%;
                                        object-fit:cover;
                                    ">

                            <?php else: ?>

                                👤

                            <?php endif; ?>

                        </td>

                        <td>

                            <?= htmlspecialchars($student['student_name']) ?>

                        </td>

                        <td>

                            <?= htmlspecialchars(
                                $student['admission_no'] ?: 'Not Assigned'
                            ) ?>

                        </td>

                        <td>

                            <?= !empty($student['dob'])
                                ? calculate_age($student['dob'])
                                : '-' ?>

                        </td>

                        <td>

                            <?= htmlspecialchars($student['sex']) ?>

                        </td>



                        <td>

                            <?php if (!empty($student['last_class_name'])): ?>

                                <?= htmlspecialchars($student['last_class_name']) ?>

                                <br>

                                <small class="text-muted">

                                    <?= htmlspecialchars($student['last_session_name']) ?>

                                </small>

                            <?php else: ?>

                                <span class="text-muted">

                                    Never Enrolled

                                </span>

                            <?php endif; ?>

                        </td>
                        
                        
                        
                       
<td>

    <select
        class="form-select form-select-sm department_select"
        name="department_id"
        required>

        <option value="">
            Select Department
        </option>

    </select>

</td>

<!-- xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx -->
                        
<td>

    <select
        class="form-select form-select-sm department_subdivision_select"
        name="department_subdivision_id"
        disabled>

        <option value="">
            None
        </option>

        <?php foreach ($subdivisions as $subdivision): ?>

            <option
                value="<?= $subdivision['id'] ?>">

                <?= htmlspecialchars($subdivision['name']) ?>

            </option>

        <?php endforeach; ?>

    </select>

</td>

<!-- xxxxxxxxxxxxxxxxxxxxxxxx -->

                        <td>

                            <button
                                class="btn btn-sm btn-success enrollStudentBtn"
                                data-student-id="<?= $student['id'] ?>">

                                Enroll

                            </button>

                        </td>

                    </tr>

                <?php endforeach; ?>

            </tbody>

        </table>

    </div>

</div>
