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
}

?>













