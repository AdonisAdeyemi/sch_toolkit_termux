<div class="card">

    <div class="card-header">

        Student Registry

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


                    <th style="width:180px;">
                        Actions
                    </th>

                </tr>

            </thead>

            <tbody>

                <?php if (empty($students)): ?>

                    <tr>

                        <td
                            colspan="6"
                            class="text-center text-muted py-4">

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
                                    src="<?= $student['passport_url'] ?>"
                                    style="width:45px;height:45px;border-radius:50%;object-fit:cover;">

                            <?php else: ?>

                                👤

                            <?php endif; ?>

                        </td>

                        <td>

                            <?= htmlspecialchars($student['student_name']) ?>

                        </td>

                        <td>

                            <?= htmlspecialchars($student['admission_no'] ?: 'Not yet set') ?>

                        </td>
                        
                        
<td>

<?= calculateAge($student['date_of_birth']) ?>

</td>                        
                        

                        <td>

                            <?= htmlspecialchars($student['sex']) ?>

                        </td>

     <td>
                            <button
                                class="btn btn-sm btn-outline-primary editStudentBtn"
                                data-student-id="<?= $student['id'] ?>">

                                Edit

                            </button>

                        </td>

                    </tr>

                <?php endforeach; ?>

            </tbody>

        </table>

    </div>

</div>
