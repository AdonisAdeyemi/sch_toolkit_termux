<?php

namespace ReportCard\Core;

class View
{
    /**
     * Render a view file with data
     */
    public static function render(string $view, array $data = []): string
    {
        $path = __DIR__ . '/../Views/' . $view . '.php';

        if (!file_exists($path)) {
            throw new \Exception("View not found: " . $view);
        }

        extract($data);
        
                file_put_contents(
    'debug-render-view.log',
    "\n>=== in View::render - data in ===\n".
    print_r($data, true),
    FILE_APPEND
);
        
        $settings = $data ["report_settings"];
$selectedStudents = $data ["students"];

/*
        ob_start();
        echo "<pre>";
        
        var_dump (">extracted data into view", $data );
        echo "</pre>";
        
        include $path;

        return ob_get_clean();
  */
        
        ob_start();

include __DIR__ . '/../Views/reportcard/header.php';

foreach ($selectedStudents as $student) {
    include __DIR__ . '/../Views/reportcard/student_section.php';
}

include __DIR__ . '/../Views/reportcard/footer.php';

return ob_get_clean();

        
    }
}


























