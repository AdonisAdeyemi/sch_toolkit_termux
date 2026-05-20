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

        ob_start();
        include $path;

        return ob_get_clean();
    }
}










