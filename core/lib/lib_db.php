<?php
// lib/db.php
declare(strict_types=1);

/**
 * Database Utility Library
 * ------------------------
 * Provides a simple, consistent PDO interface for the app.
 * Uses lazy connection initialization and supports transactions.
 */

// Ensure config is loaded before calling db_connect()
if (!function_exists('db_connect')) {

    /**
     * @var PDO|null
     */
    static $db_connection = null;

    /**
     * Establish and/or return a persistent PDO connection
     *
     * @param array|null $settings  Optional DB config array (if not passed, must have been loaded globally)
     * @return PDO
     */
    function db_connect(?array $settings = null): PDO
    {
        global $config; // fallback if $settings not explicitly passed

        if (!isset($settings)) {
            if (!isset($config['db'])) {
                throw new RuntimeException('Database configuration not loaded.');
            }
            $settings = $config['db'];
        }

        static $db_connection = null;

        if ($db_connection instanceof PDO) {
            return $db_connection; // reuse connection
        }

        $dsn = sprintf(
            "%s:host=%s;port=%s;dbname=%s;charset=%s",
            $settings['driver'] ?? 'mysql',
            $settings['host'] ?? '127.0.0.1',
            $settings['port'] ?? 3306,
            $settings['database'] ?? '',
            $settings['charset'] ?? 'utf8mb4'
        );

        try {
            $db_connection = new PDO(
                $dsn,
                $settings['username'] ?? 'root',
                $settings['password'] ?? '',
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
        } catch (PDOException $e) {
            throw new RuntimeException('DB Connection failed: ' . $e->getMessage());
        }

        return $db_connection;
    }

    /**
     * Run a SELECT query and return results.
     */
    function db_query(string $sql, array $params = []): array
    {
        $pdo = db_connect();
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Run an INSERT/UPDATE/DELETE and return affected rows.
     */
    function db_exec(string $sql, array $params = []): int
    {
        $pdo = db_connect();
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount();
    }

    /**
     * Run a query and return the first row (or null)
     */
    function db_first(string $sql, array $params = []): ?array
    {
        $pdo = db_connect();
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * Transaction helpers
     */
    function db_begin(): void { db_connect()->beginTransaction(); }
    function db_commit(): void { db_connect()->commit(); }
    function db_rollback(): void { db_connect()->rollBack(); }

    /**
     * Shortcut: Insert and return last insert ID
     */
    function db_insert(string $sql, array $params = []): string
    {
        $pdo = db_connect();
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $pdo->lastInsertId();
    }

    /**
     * Initialize a connection directly from config array.
     * Used by config/db.php to preload connection.
     */
    function db_init(array $db_config): PDO
    {
        return db_connect($db_config);
    }
}

?>












