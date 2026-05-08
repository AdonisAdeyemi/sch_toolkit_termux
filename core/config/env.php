<?php
// core/Config/Env.php

use Dotenv\Dotenv;

class Env {
    public static function load(string $path): void {
        $dotenv = Dotenv::createImmutable($path);
        $dotenv->load();
    }

    public static function get($key, $default = null) {
        return $_ENV[$key] ?? $_SERVER[$key] ?? $default;
    }
}

?>










