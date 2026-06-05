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
         question_text, options, answer, has_image, image_filename,
          q_number, paper_number, imported_at)
        VALUES
        (:arm, :exam_body, :year, :subject, :topic, :type,
         :question_text, :options, :answer, :has_image, :image_filename,
      :q_number, :paper_number, NOW())
    ");
echo "333333 <br>";
    while (($data = fgetcsv($handle)) !== false) {
        $row = array_combine($headers, $data);

        $optionsJson = $row['options'] ?? null;
        if ($optionsJson && !json_decode($optionsJson, true)) {
            echo "<p style='color:red'>⚠ Invalid JSON in options at row {$rowCount}</p>";
            continue;
        }

        $imageFilename = trim($row['image_filename'] ?? '');
        $hasImage = $imageFilename !== '' ? 1 : 0; /* seems useless ... in design, it seems all rows have ImageName & this field is just testing $hasImageName UNLESS i ensure only ones with image have imgNane*/

$default_paperNumber = 1;
        $stmt->execute([
            ':arm' => $row['arm'] ?? 'sss',
            ':exam_body' => $row['exam_body'] ?? null,
            ':year' => $row['year'] ?? null,
            ':subject' => $row['subject'] ?? '',
            ':topic' => $row['topic'] ?? null,
            ':type' => $row['type'] ?? 'mcq',
            ':question_text' => $row['question_text'] ?? '',
            ':options' => $optionsJson,
            ':answer' => $row['answer'] ?? '',
            ':has_image' => $hasImage,
            ':image_filename' => $imageFilename,
            ':q_number' => $row['q_number'] ?? null,
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
<html>
<head><title>Import Questions</title></head>
<body>
<h2>🧾 Import Questions (CSV + ZIP optional)</h2>
<form method="POST" enctype="multipart/form-data">
    <label>CSV File:</label><br>
    <input type="file" name="csv" required><br><br>
    <label>Images ZIP (optional):</label><br>
    <input type="file" name="zip"><br><br>
    <button type="submit">Import Now</button>
</form>
</body>
</html>





