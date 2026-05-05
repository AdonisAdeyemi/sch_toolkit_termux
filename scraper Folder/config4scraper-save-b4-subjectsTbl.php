<?php
// config4scraper.php

$host = 'localhost';
$db   = 'scrape_myschoolng_db';
$user = 'root';
$pass = '1234';
$charset = 'utf8mb4'; // Best for handling special symbols in questions

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

$options = [
    // This makes PDO throw an error you can actually read if a query fails
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    // This makes data come back as nice associative arrays by default
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    // Disabling emulation ensures we use real prepared statements for security
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
     // Create the connection object used by your scraper
     $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
     // If the connection fails, stop the script and show the error
     die("Connection failed: " . $e->getMessage());
}

