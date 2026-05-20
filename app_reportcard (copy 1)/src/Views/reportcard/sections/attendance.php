
<!-- ATTENDANCE SECTION -->


    <div class="attendance-title">
        ATTENDANCE RECORD
    </div>

    <table class="attendance-table">
        <tr>
            <td><strong>Days School Opened</strong></td>
            <td><?= $student['days_open'] ?? 0 ?></td>
        </tr>

        <tr>
            <td><strong>Days Present</strong></td>
            <td><?= $student['days_present'] ?? 0 ?></td>
        </tr>

        <tr>
            <td><strong>Days Absent</strong></td>
            <td><?= $student['days_absent'] ?? 0 ?></td>
        </tr>
    </table>





















