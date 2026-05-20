<?php
// config/config_db.php
declare(strict_types=1);

/**
 * Database Config Loader
 * -----------------------
 * This file:
 *  - Loads the main app config (from .env)
 *  - Includes the DB library (lib/db.php)
 *  - Initializes and returns the PDO connection
 */

require_once __DIR__ . '/config.php';   // Load $config (and .env)

// require_once __DIR__ . '/../../core/lib/lib_db.php'; // Load full DB utility functions

// Initialize PDO connection using loaded configuration
$pdo = db_init($config['db']);

// Optionally attach the PDO connection to the global config
$config['db']['connection'] = $pdo;

// Return for direct inclusion (optional)
return $pdo;

?>















