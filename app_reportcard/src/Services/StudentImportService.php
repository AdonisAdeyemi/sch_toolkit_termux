<?php

namespace Reportcard\Services;

use Reportcard\Models\StudentModel;
use PDO;

class StudentImportService
{
    private StudentModel $studentModel;

    public function __construct(PDO $pdo)
    {
        $this->studentModel = new StudentModel($pdo);
    }

    /*
    |--------------------------------------------------------------------------
    | Import Students
    |--------------------------------------------------------------------------
    */
    public function import(
    int $schoolId,
    string $csvFilePath
): array
{
    $rows = $this->parseCsv($csvFilePath);

    $result = [
        'imported' => 0,
        'updated'  => 0,
        'skipped'  => 0,
        'errors'   => 0,
        'messages' => []
    ];

    $seenAdmissions = [];

    foreach ($rows as $rowNumber => $row) {

        $rowNumber += 2; // Header is row 1

        $admissionNo = trim($row['Admission No']);

        /*
        |--------------------------------------------------------------------------
        | Duplicate Admission Number in CSV
        |--------------------------------------------------------------------------
        */

        if (isset($seenAdmissions[$admissionNo])) {

            $result['errors']++;

            $result['messages'][] =
                "Row {$rowNumber}: Duplicate admission number '{$admissionNo}'.";

            continue;
        }

        $seenAdmissions[$admissionNo] = true;

        /*
        |--------------------------------------------------------------------------
        | Validate Row
        |--------------------------------------------------------------------------
        */

        $errors = $this->validateRow($row);

        if (!empty($errors)) {

            $result['errors']++;

            $result['messages'][] =
                "Row {$rowNumber}: " .
                implode(' ', $errors);

            continue;
        }

        /*
        |--------------------------------------------------------------------------
        | Import Student
        |--------------------------------------------------------------------------
        */

        $status = $this->importRow(
            $schoolId,
            $row
        );

        switch ($status) {

            case 'imported':
                $result['imported']++;
                break;

            case 'updated':
                $result['updated']++;
                break;

            default:
                $result['skipped']++;
                break;
        }
    }

    return $result;
}
 /*********************/
 
 private function parseCsv(
    string $csvFilePath
): array
{
    $handle = fopen($csvFilePath, 'r');

    if ($handle === false) {
        throw new \RuntimeException(
            'Unable to open CSV file.'
        );
    }

    $headers = fgetcsv($handle);

    if ($headers === false) {
        fclose($handle);

        throw new \RuntimeException(
            'CSV file is empty.'
        );
    }
    
//remove invisible BOM charactoer
$headers = array_map(function ($header) {
    return trim(preg_replace('/^\xEF\xBB\xBF/', '', $header));
}, $headers);
    
  
    //validate header names
    $required = [
    'Admission No',
    'Student Name',
    'Sex',
    'Date of Birth'
];

foreach ($required as $column) {

    if (!in_array($column, $headers, true)) {
        
        throw new \Exception(
    "Missing required column: {$column}"
);

    }

}
    
    
    /***************/
    
    

    $headers = array_map('trim', $headers);

    $rows = [];

    while (($data = fgetcsv($handle)) !== false) {

        if (count(array_filter($data)) === 0) {
            continue;
        }

        $rows[] = array_combine(
            $headers,
            array_map('trim', $data)
        );
    }

    fclose($handle);

    return $rows;
}

 /********************/
 
 private function validateRow(
    array $row
): array
{
    $errors = [];

    $admissionNo =
        trim($row['Admission No'] ?? '');

    $studentName =
        trim($row['Student Name'] ?? '');

    $sex =
        strtoupper(trim($row['Sex'] ?? ''));

    $dob =
        trim($row['Date of Birth'] ?? '');

    /*
    |--------------------------------------------------------------------------
    | Required fields
    |--------------------------------------------------------------------------
    */

    if ($admissionNo === '') {
        $errors[] = 'Admission No is required.';
    }

    if ($studentName === '') {
        $errors[] = 'Student Name is required.';
    }

    /*
    |--------------------------------------------------------------------------
    | Sex
    |--------------------------------------------------------------------------
    */

    if (
        $sex !== ''
        && !in_array($sex, ['M', 'F'], true)
    ) {
        $errors[] = 'Sex must be M or F.';
    }

    /*
    |--------------------------------------------------------------------------
    | Date of Birth
    |--------------------------------------------------------------------------
    */

 
 if (
    !empty($dob) &&
    $this->normalizeDate($dob) === null
) {
    $errors[] = 'Invalid Date of Birth.';
}

    return $errors;
}
 /****************/
   
private function importRow(
    int $schoolId,
    array $row
): string
{
    $admissionNo = trim($row['Admission No']);

    $studentName = trim($row['Student Name']);

    $sex = strtoupper(
        trim($row['Sex'] ?? '')
    );
        
    $dateOfBirth = $this->normalizeDate(
    $row['Date of Birth'] ?? null
);

    $student = $this->studentModel
        ->findByAdmissionNo(
            $schoolId,
            $admissionNo
        );

    if ($student) {

        $this->studentModel->updateStudent(
            $schoolId,
            $student['id'],
            $studentName,
            $admissionNo,
            $sex,
            $dateOfBirth
        );

        return 'updated';
    }

    $this->studentModel->createStudent(
        $schoolId,
        $studentName,
        $admissionNo,
        $sex,
        null,           // passport_url
        $dateOfBirth
    );

    return 'imported';
}    
 /******************/
 
 private function normalizeDate(
    ?string $date
): ?string
{
    $date = trim((string)$date);

    if ($date === '') {
        return null;
    }

    $formats = [
        'd-m-Y',
        'd/m/Y'
    ];

    foreach ($formats as $format) {

        $dt = \DateTime::createFromFormat(
            $format,
            $date
        );

        if (
            $dt &&
            $dt->format($format) === $date
        ) {
            return $dt->format('Y-m-d');
        }
    }

    return null;
}
 
 /**************************/
 
 /*****************/
    
    
    
    
    
    
}






















