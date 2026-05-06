<?php
// config/config.php

// Autoload dependencies (for vlucas/phpdotenv)
//require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// Helper to safely get env variables with fallback
function env($key, $default = null) {
    return $_ENV[$key] ?? $_SERVER[$key] ?? $default;
}

// Build config array
$config = [
    'app' => [
        'name' => env('APP_NAME', 'MyApp'),
        'env'  => env('APP_ENV', 'development'),
        'debug' => filter_var(env('APP_DEBUG', false), FILTER_VALIDATE_BOOLEAN),
    ],

    'db' => [
        'driver'   => env('DB_DRIVER', 'mysql'),
        'host'     => env('DB_HOST', '127.0.0.1'),
        'port'     => env('DB_PORT', 3306),
        'database' => env('DB_NAME', 'testdb'),
        'username' => env('DB_USER', 'root'),
        'password' => env('DB_PASS', ''),
        'charset'  => env('DB_CHARSET', 'utf8mb4'),
    ],

    'quota' => [
        'free_tokens' => (int) env('FREE_TOKENS', 10),
        'max_tokens'  => (int) env('MAX_TOKENS', 1000),
    ],
];

// Build PDO connection
try {
    $dsn = sprintf(
        "%s:host=%s;port=%s;dbname=%s;charset=%s",
        $config['db']['driver'],
        $config['db']['host'],
        $config['db']['port'],
        $config['db']['database'],
        $config['db']['charset']
    );

    $pdo = new PDO(
        $dsn,
        $config['db']['username'],
        $config['db']['password'],
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );

    $config['db']['connection'] = $pdo;
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

?>











