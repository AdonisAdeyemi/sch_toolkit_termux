<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../src/ReportCardService.php';

use Dompdf\Dompdf;
use Dompdf\Options;

$service = new ReportCardService();

$students = $service->getStudents();

/*
|--------------------------------------------------------------------------
| PICK STUDENT
|--------------------------------------------------------------------------
|
| Example:
| generate.php?id=1
|
*/

$studentId = isset($_GET['id']) ? (int) $_GET['id'] : 1;

if (!isset($students[$studentId])) {
    die('Student not found');
}

$student = $students[$studentId];

/*
|--------------------------------------------------------------------------
| HTML BUFFER
|--------------------------------------------------------------------------
*/

ob_start();

include __DIR__ . '/../templates/report_card.php';

$html = ob_get_clean();

/*
|--------------------------------------------------------------------------
| DOMPDF OPTIONS
|--------------------------------------------------------------------------
*/

$options = new Options();

$options->set('isRemoteEnabled', true);
$options->set('isHtml5ParserEnabled', true);
$options->set('defaultFont', 'Times-Roman');

/*
|--------------------------------------------------------------------------
| DOMPDF INSTANCE
|--------------------------------------------------------------------------
*/

$dompdf = new Dompdf($options);

$dompdf->loadHtml($html);

/*
|--------------------------------------------------------------------------
| PAPER SIZE
|--------------------------------------------------------------------------
|
| A4 Portrait
|
*/

$dompdf->setPaper('A4', 'portrait');

/*
|--------------------------------------------------------------------------
| RENDER PDF
|--------------------------------------------------------------------------
*/

$dompdf->render();

/*
|--------------------------------------------------------------------------
| STREAM TO BROWSER
|--------------------------------------------------------------------------
|
| Attachment false = open in browser
| Attachment true  = force download
|
*/

$dompdf->stream(
    'report_card_' . $student['student_id'] . '.pdf',
    [
        'Attachment' => false
    ]
);


















