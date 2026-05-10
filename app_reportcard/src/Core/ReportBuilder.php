<?php
namespace ReportCard\Core;


class ReportBuilder
{
    public static function render(array $student, array $config = [])
    {
        $theme = $config['theme'] ?? 'classic';

        $themeFile = APP_PATH . '/src/View/reportcard/themes/' . $theme . '.php';

        if (!file_exists($themeFile)) {
            die("Theme not found: {$theme}");
        }

        // Shared variables available inside theme/sections
        $GLOBALS['report_student'] = $student;
        $GLOBALS['report_config']  = $config;

        ob_start();

        include $themeFile;

        return ob_get_clean();
    }

    public static function section(string $section)
    {
        $sectionFile = APP_PATH . '/src/View/reportcard/sections/' . $section . '.php';

        if (file_exists($sectionFile)) {
            include $sectionFile;
        } else {
            echo "<!-- Missing section: {$section} -->";
        }
    }

    public static function config(string $key, $default = null)
    {
        return $GLOBALS['report_config'][$key] ?? $default;
    }

    public static function student(string $key, $default = '')
    {
        return $GLOBALS['report_student'][$key] ?? $default;
    }
}


?>











