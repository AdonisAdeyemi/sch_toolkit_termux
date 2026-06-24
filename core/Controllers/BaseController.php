<?php
namespace Core\Controllers;

class BaseController
{
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
 
 private function canEditPeriod(
    int $schoolId,
    int $periodId,
    bool $isAdmin
): ?string {

    $lockStatus = $this->schoolPeriodSettingsModel
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


    
    
}

?>

















