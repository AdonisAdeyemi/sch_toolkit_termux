<?php
// scripts/import_questions.php
session_start();
$IMPORT_PASSWORD = "devpass"; // change this for security

// simple login
if (!isset($_SESSION['dev_import']) && ($_POST['password'] ?? '') !== $IMPORT_PASSWORD) {
    echo '<form method="POST"><h2>Developer Import</h2>
          <input type="password" name="password" placeholder="Enter password">
          <button>Login</button></form>';
    if ($_POST) echo "<p style='color:red'>Wrong password.</p>";
    exit;
}
$_SESSION['dev_import'] = true;

require_once __DIR__ . '/../../config/config_db.php'; // adjust path if needed

$uploadDir = __DIR__ . '/../uploads/images/';

if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['csv'])) {
    $csvFile = $_FILES['csv']['tmp_name'];
    $zipFile = $_FILES['zip']['tmp_name'] ?? null;

    // optional ZIP extraction
    if ($zipFile && is_uploaded_file($zipFile)) {
        $zip = new ZipArchive();
        if ($zip->open($zipFile) === true) {
            $zip->extractTo($uploadDir);
            $zip->close();
            echo "<p>✅ Ok. Images extracted to uploads/images/</p>";
        } else {
            echo "<p style='color:red'>❌ Failed to open ZIP file.</p>";
        }
    }

echo "111111 <br>";

    // prepare db
   /* already available in config_db.php
*/
    $handle = fopen($csvFile, 'r');
    if (!$handle) {
        die("❌ Cannot open CSV file.");
    }
echo "222222 <br>";
    $headers = fgetcsv($handle);
    $rowCount = 0;

    $stmt = $pdo->prepare("
        INSERT INTO questions
        (arm, exam_body, year, subject, topic, type,
        q_order, q_label, question_text,
        options, answer, 
        has_image, 
          paper_number, imported_at)
        VALUES
        (:arm, :exam_body, :year, :subject, :topic, :type,
     :q_order, :q_label, :question_text, 
     :options, :answer, 
     :has_image,
       :paper_number, NOW())
    ");
echo "333333 <br>";
    while (($data = fgetcsv($handle)) !== false) {
        $row = array_combine($headers, $data);

        $optionsJson = $row['options'] ;
        if (trim($optionsJson) === '' || strtolower($optionsJson) === 'null') {
    $optionsJson = '{}'; // treat theory questions as empty JSON
}

if (json_decode($optionsJson, true) === null) {
    echo "<p style='color:red'>⚠ Invalid JSON in options at row {$rowCount}</p>";
    continue;
}

     //   $imageFilename = trim($row['image_filename'] ?? '');
        $hasImage = empty($hasImage) ? 0 : 1; /* wecanset it to true, later when inputing pics wt sql */

$default_paperNumber = 1;
        $stmt->execute([
            ':arm' => $row['arm'] ?? 'sss',
            ':exam_body' => $row['exam_body'] ?? null,
            ':year' => $row['year'] ?? null,
            ':subject' => $row['subject'] ?? '',
            ':topic' => $row['topic'] ?? null,
            ':type' => $row['type'] ?? 'mcq',
            ':q_order' => $row['q_order'] ?? null,
            ':q_label' => $row['q_label'] ?? null,
            ':question_text' => $row['question_text'] ?? '',
            ':options' => $optionsJson,
            ':answer' => $row['answer'] ?? '',
            ':has_image' => $hasImage,
            ':paper_number' => $row['paper_number'] ?? $default_paperNumber,
        ]);

echo "QUESTION: ".$row['question_text'] ;
        $rowCount++;
        echo "rowCount - $rowCount<br>";
    }
echo "44444 <br>";
    fclose($handle);
    echo "<p>✅ Ok. Imported {$rowCount} questions successfully.</p>";
}
else
{
echo "no....SERVER['REQUEST_METHOD'] === 'POST' OR NO... isset(_FILES['csv'])";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Import Questions</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .import-card { max-width: 500px; margin: 50px auto; }
    </style>
</head>
<body>

<div class="container">
    <div class="card import-card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">🧾 Import Questions</h5>
        </div>
        <div class="card-body">
            <p class="text-muted small">Upload your CSV and an optional ZIP file containing question images.</p>
            
            <form method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="csvFile" class="form-label fw-bold">CSV File</label>
                    <input class="form-control" type="file" id="csvFile" name="csv" accept=".csv" required>
                </div>

                <div class="mb-4">
                    <label for="zipFile" class="form-label fw-bold">Images ZIP <span class="badge bg-secondary text-uppercase" style="font-size: 0.6rem;">Optional</span></label>
                    <input class="form-control" type="file" id="zipFile" name="zip" accept=".zip">
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-upload"></i> Import Now
                    </button>
                </div>
            </form>
        </div>
        <div class="card-footer text-center">
            <a href="sample.csv" class="text-decoration-none small">Download Sample CSV</a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>






















