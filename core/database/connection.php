<?php
// core/Database/Connection.php

class Connection {

    public static function make(array $db): PDO {
        $dsn = sprintf(
            "%s:host=%s;port=%s;dbname=%s;charset=%s",
            $db['driver'],
            $db['host'],
            $db['port'],
            $db['database'],
            $db['charset']
        );

        return new PDO(
            $dsn,
            $db['username'],
            $db['password'],
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]
        );
    }
}


?>








