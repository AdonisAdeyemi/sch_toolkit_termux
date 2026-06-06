<?php
// core/Config/Config.php
namespace Core\Config;

class Config {

    public static function make(): array {
        return [
            'app' => [
                'name' => Env::get('APP_NAME', 'MyApp'),
                'env'  => Env::get('APP_ENV', 'development'),
             'url' => Env::get('APP_URL' , 'http://localhost:8000'),   
                
                'debug' => filter_var(Env::get('APP_DEBUG', false), FILTER_VALIDATE_BOOLEAN),
            ],

            'db' => [
                'driver'   => Env::get('DB_DRIVER', 'mysql'),
                'host'     => Env::get('DB_HOST', '127.0.0.1'),
                'port'     => Env::get('DB_PORT', 3306),
                'database' => Env::get('DB_NAME', 'testdb'),
                'username' => Env::get('DB_USER', 'root'),
                'password' => Env::get('DB_PASS', ''),
                'charset'  => Env::get('DB_CHARSET', 'utf8mb4'),
            ],

            'quota' => [
                'free_tokens' => (int) Env::get('FREE_TOKENS', 10),
                'max_tokens'  => (int) Env::get('MAX_TOKENS', 1000),
            ],
        ];
    }
}


?>











