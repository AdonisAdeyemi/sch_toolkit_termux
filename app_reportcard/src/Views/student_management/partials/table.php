<div class="card">


<div class="card-header d-flex justify-content-between align-items-center">

    <div>

        Student Registry

        <span class="badge bg-secondary">

            <?= count($students) ?>

        </span>

    </div>

    <div>

        <a
            href="?show_deleted=<?= $showDeleted ? 0 : 1 ?>"
            class="btn btn-sm btn-outline-secondary">

            <?= $showDeleted
                ? 'Show Active'
                : 'Show Deleted' ?>

        </a>

    </div>

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
                            Sex
                        </th>

                        <th>
                            Class
                        </th>
                        
                        <th>
                            Department
                        </th>
                        
                        <th>
                            Subdiv
                        </th>

                        <th style="width:180px;">
                            Actions
                        </th>

                    </tr>

                </thead>

                <tbody>

                    <?php if (empty($students)): ?>

                        <tr>

                            <td colspan="7" class="text-center text-muted py-4">

                                No students found.

                            </td>

                        </tr>

                    <?php endif; ?>

                    <?php foreach ($students as $student): ?>

                        <tr>

                            <td>

                                <?php if (!empty($student['passport_url'])): ?>

<?php
$folderName = 'passport';
$fileName =     $student['passport_url'] ?? null ;
  
$student['passport_url'] = getAssetUrl( $folderName , $fileName );     
?>

                                    <img
                                        src="<?= htmlspecialchars($student['passport_url']) ?>"
                                        style="width:45px;height:45px;border-radius:50%;object-fit:cover;">

                                <?php else: ?>

                                    👤

                                <?php endif; ?>

                            </td>

                            <td>

                                <?= htmlspecialchars($student['student_name']) ?>

                            </td>

                            <td>

                                <?= htmlspecialchars($student['admission_no'] ?? "Not Assigned") ?>

                            </td>

                            <td>

                                <?= htmlspecialchars($student['sex'] ?? '-') ?>

                            </td>


                            <td>

                                <?= htmlspecialchars($student['class_name']) ?>

                            </td>
                            

                            <td>

                                <?= htmlspecialchars($student['department_name'] ?? "-") ?>

                            </td>
                            
 <!-- xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx -->       
                            
                            <td>

                                <?= htmlspecialchars($student['subdivision_name'] ?? "-") ?>

                            </td>

<!-- xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx -->


                            <td>

<button
    class="btn btn-sm btn-outline-danger removeStudentBtn"
    data-student-id="<?= $student['student_id'] ?>">

    Remove from Class

</button>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                </tbody>

            </table>

        </div>

    </div>







