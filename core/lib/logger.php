<?php
/* ==========================================================
   Simple Smart Logger
   - Creates a new log file every hour/minute
   - Stores logs under /logs folder
   ========================================================== */

if (!function_exists('log_debug')) {
    function log_debug($data, $prefix = 'debug') {
        /* ensure logs directory exists */
        $logDir = __DIR__ . '/../logs';
        if (!is_dir($logDir)) {
            mkdir($logDir, 0775, true);
        }

        /* create log filename like: debug_2025-10-13_14-32.log */
        $timestamp = date('Y-m-d_H-i');
        $filename = "{$logDir}/{$prefix}_{$timestamp}.log";

        /* convert any variable to readable format */
        $output = print_r($data, true);

        /* build log entry with timestamp inside file */
        $logEntry = "[" . date('Y-m-d H:i:s') . "] " . $output . "\n";

        /* append to the right file */
        file_put_contents($filename, $logEntry, FILE_APPEND);
    }
}
?>
