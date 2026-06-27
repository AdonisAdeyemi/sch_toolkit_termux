<?php
namespace Core\Controllers;

use ReportCard\Models\SchoolPeriodSettingsModel;
use PDO;

class BaseController
{

protected SchoolPeriodSettingsModel $baseSchoolPeriodSettingsModel;

    protected function render(string $view, array $data = []): void
    {
        // Extract array keys into local variables ($title, $users, etc.)
        extract($data);
        

        // Build full path to the view
        $viewFile = VIEW_PATH . '/' . $view . '.php';

        if (!file_exists($viewFile)) {
            throw new \RuntimeException("View not found: {$viewFile}");
        }

        // Include layout and content
        require PROJECT_ROOT . '/shared/Views/layouts/header.php';
        require $viewFile;
        require PROJECT_ROOT . '/shared/Views/layouts/footer.php';
    }
    
    
 // School id
 protected function schoolId(): int
{
    return (int) $_SESSION['school_id'];
}
    
 // appName helper
 protected function appName()
{
    return $_SESSION['appName'] ?? "";

}


//xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx

 /*********************/
 
 protected function canEditPeriod(
    int $schoolId,
    int $periodId,
    bool $isAdmin,
    PDO $pdo
): ?string {

$this->baseSchoolPeriodSettingsModel =
     new SchoolPeriodSettingsModel($pdo);

    $lockStatus = $this->baseSchoolPeriodSettingsModel
        ->getLockStatus($schoolId, $periodId);

    // 2 = Permanent Lock
    if ($lockStatus == 2) {
        return 'This period has been permanently locked.';
    }

    // 1 = Teacher Lock
    if ($lockStatus == 1 && !$isAdmin) {
        return 'This period has been locked for teachers.';
    }

    return null;
}

/***********/

protected function uploadImage(
    array $file,
    string $folder,
    string $filenamePrefix
): string
{
    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new \Exception('No file uploaded.');
    }

    $allowed = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp'
    ];

    $mime = mime_content_type($file['tmp_name']);

    if (!isset($allowed[$mime])) {
        throw new \Exception(
            'Only JPG, PNG and WEBP images are allowed.'
        );
    }

    $extension = $allowed[$mime];

    $filename =
        $filenamePrefix .
        '_' .
        uniqid() .
        '.' .
        $extension;

    $uploadDir =
        PROJECT_ROOT .
        '/public/reportcard/assets/' .
        trim($folder, '/') .
        '/';

    if (!is_dir($uploadDir)) {

        mkdir(
            $uploadDir,
            0755,
            true
        );

    }

    $destination =
        $uploadDir .
        $filename;

    if (
        !move_uploaded_file(
            $file['tmp_name'],
            $destination
        )
    ) {
        throw new \Exception(
            'Failed to upload image.'
        );
    }

    return $filename;
}

/**************/

protected function getAssetUrl(
    string $folder,
    ?string $filename
): string
{
    if (empty($filename)) {
        return '';
    }

    return "/public/{$this->appName()}/assets/" .
        trim($folder, '/') .
        "/" .
        $filename;
}


    
    
}

?>























