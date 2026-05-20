<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../src/ReportCardService.php';
require_once __DIR__ . '/../../core/lib/helper_functions.php';

use Dompdf\Dompdf;
use Dompdf\Options;

$service = new ReportCardService();

$students = $service->getStudents();

/*
|--------------------------------------------------------------------------
| SELECT MODE
|--------------------------------------------------------------------------
*/

$studentId = $_GET['id'] ?? "all" ;


if ($studentId === 'all') {

    $selectedStudents = $students;

} else {

    $studentId = (int) $studentId;

    if (!isset($students[$studentId])) {
        die('Student not found');
    }

    $selectedStudents = [
        $students[$studentId]
    ];
}



/*
|--------------------------------------------------------------------------
| HTML BUFFER
|--------------------------------------------------------------------------
*/
//logo src
$logoPath = __DIR__ . '/assets/logo/logo_01.jpg';

$logoExtension = pathinfo($logoPath, PATHINFO_EXTENSION);

$logoData = base64_encode(file_get_contents($logoPath));

$logoSrc = 'data:image/' . $logoExtension . ';base64,' . $logoData;



//passportSrc
$passportPath = __DIR__ . '/assets/passport/passport_avatar.png';

$passportExtension = pathinfo($passportPath, PATHINFO_EXTENSION);

$passportData = base64_encode(file_get_contents($passportPath));

$passportSrc = 'data:image/' . $passportExtension . ';base64,' . $passportData;






ob_start();

include __DIR__ . '/../templates/header.php';

/*
echo <<<HTML
     <img src="$logoSrc" width="70" height="70" >
HTML;
*/


foreach ($selectedStudents as $student) {
    include __DIR__ . '/../templates/student_section.php';
}

include __DIR__ . '/../templates/footer.php';

$html =  ob_get_clean();


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


/*
echo $html;
*/














