<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Report Card</title>

<style>
body {
    font-family: Arial, sans-serif;
    background: #f5f5f5;
}

.report-card {
    width: 900px;
    margin: 20px auto;
    background: #fff;
    padding: 20px;
    border: 10px solid black;
}

.header {
    text-align: center;
}

.header img {
    width: 60px;
    float: left;
}

.school-name {
    font-size: 28px;
    font-weight: bold;
    color: darkred;
}

.sub-info {
    font-size: 14px;
}

.title {
    background: red;
    color: white;
    display: inline-block;
    padding: 5px 15px;
    margin-top: 10px;
    font-weight: bold;
}

.section-title {
    color: red;
    font-weight: bold;
    margin-top: 20px;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 5px;
}

th, td {
    border: 1px solid #999;
    padding: 5px;
    font-size: 12px;
}

th {
    background: #eee;
}

.small-table {
    width: 60%;
}

.grade-box, .affective-box {
    width: 45%;
    display: inline-block;
    vertical-align: top;
}

.lines {
    border-bottom: 1px dotted #000;
    margin-bottom: 10px;
    height: 20px;
}
</style>

</head>

<body>

<div class="report-card">

    <!-- HEADER -->
    <div class="header">
        <img src="logo.png" alt="logo">
        <div class="school-name">MUCH MORE LIFE COLLEGE</div>
        <div class="sub-info">
            jeje laye 1<br>
            greatness<br>
            999<br>
            www.hhh
        </div>

        <div class="title">REPORT CARD</div>
    </div>

    <!-- ATTENDANCE -->
    <div class="section-title">1. ATTENDANCE</div>
    <table class="small-table">
        <tr>
            <th></th>
            <th>FREQUENCIES</th>
        </tr>
        <tr>
            <td>NUMBER OF TIMES SCHOOL OPENED</td>
            <td>900</td>
        </tr>
        <tr>
            <td>NUMBER OF TIMES PRESENT</td>
            <td></td>
        </tr>
        <tr>
            <td>NUMBER OF TIMES ABSENT</td>
            <td>900</td>
        </tr>
    </table>

    <!-- COGNITIVE -->
    <div class="section-title">2. COGNITIVE ABILITY</div>
    <table>
        <tr>
            <th>Subject</th>
            <th>Weekly</th>
            <th>Test</th>
            <th>Exam</th>
            <th>Average</th>
            <th>Grade</th>
            <th>Position</th>
            <th>Remarks</th>
            <th>Signature</th>
        </tr>

        <tr>
            <td>MATHEMATICS</td>
            <td>5</td>
            <td>7</td>
            <td>56</td>
            <td>68</td>
            <td>C4</td>
            <td>1st</td>
            <td>CREDIT</td>
            <td></td>
        </tr>

        <!-- Add more rows dynamically -->
    </table>

    <!-- LOWER SECTION -->
    <div>

        <!-- GRADE -->
        <div class="grade-box">
            <div class="section-title">3. GRADE</div>
            <table>
                <tr><td>A</td><td>Excellent</td></tr>
                <tr><td>B</td><td>Good</td></tr>
                <tr><td>C</td><td>Credit</td></tr>
                <tr><td>D</td><td>Pass</td></tr>
                <tr><td>E</td><td>Poor</td></tr>
                <tr><td>F</td><td>Fail</td></tr>
                <tr><td>AB</td><td>Absent</td></tr>
            </table>

            <div class="section-title">5. CUMULATIVE SCORES</div>
            1st Term: ______ %<br>
            2nd Term: ______ %<br>
            3rd Term: ______ %
        </div>

        <!-- AFFECTIVE -->
        <div class="affective-box">
            <div class="section-title">4. AFFECTIVE AREAS</div>
            <table>
                <tr>
                    <th>Traits</th>
                    <th>A</th><th>B</th><th>C</th><th>D</th><th>E</th>
                </tr>
                <tr><td>PUNCTUALITY</td><td>✔</td><td></td><td></td><td></td><td></td></tr>
                <tr><td>NEATNESS</td><td></td><td>✔</td><td></td><td></td><td></td></tr>
                <tr><td>POLITENESS</td><td></td><td></td><td>✔</td><td></td><td></td></tr>
            </table>
        </div>

    </div>

    <!-- STUDENT INFO -->
    <br><br>
    <div>6. Name: <span class="lines"></span></div>
    <div>7. Class: <span class="lines"></span> Term: FIRST TERM</div>
    <div>8. Date of Vacation: <span class="lines"></span></div>
    <div>9. Next Term Begins: <span class="lines"></span></div>
    <div>10. Class Teacher's Comment: <span class="lines"></span></div>
    <div>11. Principal's Comment: <span class="lines"></span></div>

</div>

</body>
</html>
