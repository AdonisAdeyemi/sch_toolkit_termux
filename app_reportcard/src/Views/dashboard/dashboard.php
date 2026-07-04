<?php

extract($stats);

$activePeriod = $activePeriod ?? [];

$sessionName = $activePeriod['session_name'] ?? '-';
$term = $activePeriod['term'] ?? '-';
$lockStatus = (int)($activePeriod['lock_status'] ?? 0);

?>

<div class="container py-4">

    <div class="mb-4">
        <h1 class="display-6">Dashboard</h1>
        <p class="text-muted">
            Welcome to the Report Card Management System.
        </p>
    </div>

    <!-- ===================================================== -->
    <!-- DASHBOARD SUMMARY -->
    <!-- ===================================================== -->

    <div class="row g-3 mb-5">

        <div class="col-md-3">
            <div class="card border-primary h-100">
                <div class="card-body">
                    <small class="text-muted">Students</small>
                    <h2><?= $totalStudents ?></h2>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-success h-100">
                <div class="card-body">
                    <small class="text-muted">Classes</small>
                    <h2><?= $totalClasses ?></h2>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-info h-100">
                <div class="card-body">
                    <small class="text-muted">Subjects</small>
                    <h2><?= $totalSubjects ?></h2>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-dark h-100">
                <div class="card-body">
                    <small class="text-muted">Current Period</small>

                    <div>
                        <strong><?= htmlspecialchars($sessionName) ?></strong>
                    </div>

                    <div>
                        Term <?= htmlspecialchars($term) ?>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="row g-3 mb-5">

        <div class="col-md-4">

            <div class="alert alert-warning mb-0">

                <strong>
                    <?= $studentsAwaitingEnrollment ?>
                </strong>

                student(s) awaiting enrollment.

            </div>

        </div>

        <div class="col-md-4">

            <div class="alert alert-danger mb-0">

                <strong>
                    <?= $studentsWithIncompleteResults ?>
                </strong>

                student(s) have incomplete results.

            </div>

        </div>

        <div class="col-md-4">

            <div class="alert alert-info mb-0">

                Result Status:

                <strong>

                    <?=
                    match ($lockStatus) {

                        0 => 'Open',

                        1 => 'Teacher Lock',

                        2 => 'Permanent Lock',

                        default => 'Unknown'

                    }
                    ?>

                </strong>

            </div>

        </div>

    </div>

    <?php if ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'creator' ): ?>

    <!-- ===================================================== -->
    <!-- ADMIN -->
    <!-- ===================================================== -->
<hr class="my-5">

<h2 class="text-secondary fw-bold">
    🛠️ ADMINISTRATOR PANEL
</h2>

<p class="text-muted">
    Configure academic setup, student administration, report card settings and reports.
</p>

<hr class="mb-4">


    <h3 class="mb-3">
        🏫 Academic Setup
    </h3>

    <div class="row g-4 mb-5">

        <div class="col-md-6">

            <div class="card h-100">

                <div class="card-body">

                    <h5>Classes</h5>

                    <p>
                        Create and manage classes.
                        Assign subjects to each class.
                    </p>

                    <a
                        class="btn btn-primary"
                        href="/<?= $appName ?>/admin/classes">

                        Open

                    </a>

                </div>

            </div>

        </div>

        <div class="col-md-6">

            <div class="card h-100">

                <div class="card-body">

                    <h5>Subjects</h5>

                    <p>

                        Create and manage subject names.

                    </p>

                    <a
                        class="btn btn-primary"
                        href="/<?= $appName ?>/admin/subjects">

                        Open

                    </a>

                </div>

            </div>

        </div>

    </div>

    <h3 class="mb-3">
        📚 Student Administration
    </h3>

    <div class="row g-4 mb-5">

        <div class="col-md-6">

            <div class="card h-100">

                <div class="card-body">

                    <h5>

                        Student Registry

                    </h5>

                    <p>

                        Register and update student information.

                    </p>

                    <a
                        class="btn btn-primary"
                        href="/<?= $appName ?>/student_registry">

                        Open

                    </a>

                </div>

            </div>

        </div>

        <div class="col-md-6">

            <div class="card h-100">

                <div class="card-body">

                    <h5>

                        Assign Students to Class

                    </h5>

                    <p>

                        Assign new or existing students
                        to the current class and session.

                    </p>

                    <a
                        class="btn btn-primary"
                        href="/<?= $appName ?>/student_manage">

                        Open

                    </a>

                </div>

            </div>

        </div>

    </div>

    <h3 class="mb-3">
        ⚙️ Report Card Setup
    </h3>

    <div class="row g-4 mb-5">

        <div class="col-md-6">

            <div class="card h-100">

                <div class="card-body">

                    <h5>

                        Current Term Information

                    </h5>

                    <p>

                        Configure session,
                        term,
                        signatures,
                        dates,
                        locking and other settings.

                    </p>

                    <a
                        class="btn btn-primary"
                        href="/<?= $appName ?>/school-settings">

                        Open

                    </a>

                </div>

            </div>

        </div>

        <div class="col-md-6">

            <div class="card h-100">

                <div class="card-body">

                    <h5>

                        Report Card Design

                    </h5>

                    <p>

                        Customize the appearance
                        of report cards.

                    </p>

                    <a
                        class="btn btn-primary"
                        href="/<?= $appName ?>/card-preferences">

                        Open

                    </a>

                </div>

            </div>

        </div>
        
<div class="col-md-6 col-lg-4">

    <div class="card h-100 shadow-sm">

        <div class="card-body d-flex flex-column">

            <h5 class="card-title">

                🖨 Generate Report Cards

            </h5>

            <p class="card-text flex-grow-1">

                Generate and print report cards for an entire class or individual students.

            </p>

            <a
                href="/<?= $appName ?>/reports"
                class="btn btn-primary">

                Open

            </a>

        </div>

    </div>

</div>        
        

    </div>

    <?php endif; ?>

    <!-- ===================================================== -->
    <!-- STAFF + ADMIN -->
    <!-- ===================================================== -->
    
    
    
 <hr class="my-5">

<h2 class="text-secondary fw-bold">
    👨‍🏫 STAFF PANEL
</h2>

<p class="text-muted">
    Manage results, attendance and report remarks.
</p>

<hr class="mb-4">




    <h3 class="mb-3">
        📖 Academic Records
    </h3>

    <div class="row g-4 mb-5">

        <div class="col-md-6">

            <div class="card h-100">

                <div class="card-body">

                    <h5>

                        Subject Results

                    </h5>

                    <p>

                        Enter,
                        edit
                        and view subject scores.

                    </p>

                    <a
                        class="btn btn-success"
                        href="/<?= $appName ?>/results">

                        Open

                    </a>

                </div>

            </div>

        </div>

        <div class="col-md-6">

            <div class="card h-100">

                <div class="card-body">

                    <h5>

                        Other Results / Remarks

                    </h5>

                    <p>

                        Attendance,
                        affective,
                        psychomotor
                        and teacher remarks.

                    </p>

                    <a
                        class="btn btn-success"
                        href="/<?= $appName ?>/report-remarks">

                        Open

                    </a>

                </div>

            </div>

        </div>

    </div>

    <?php if ($_SESSION['role'] === 'admin'): ?>

    <h3 class="mb-3">
        🖨 Reports
    </h3>

    <div class="row g-4">

        <div class="col-md-12">

            <div class="card">

                <div class="card-body">

                    <h5>

                        Generate Report Cards

                    </h5>

                    <p>

                        Generate class report cards
                        and individual student report cards.

                    </p>

                    <button
                        class="btn btn-secondary"
                        disabled>

                        Coming Soon

                    </button>

                </div>

            </div>

        </div>

    </div>

    <?php endif; ?>

</div>
