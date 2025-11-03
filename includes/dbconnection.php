<?php
function get_pdo(): PDO
{
    static $pdo = null;
    if ($pdo === null) {
        try {
            $dsn = sprintf(
                "pgsql:host=%s;port=%s;dbname=%s;",
                $_ENV['POSTGRES_HOST'],
                $_ENV['POSTGRES_DB_PORT'],
                $_ENV['POSTGRES_DB']
            );

            $pdo = new PDO($dsn, $_ENV['POSTGRES_USER'], $_ENV['POSTGRES_PASSWORD'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,   // Throw exceptions
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Return associative arrays
                PDO::ATTR_EMULATE_PREPARES => false             // Use real prepared statements
            ]);




        } catch (PDOException $e) {
            die("DB Connection failed: " . $e->getMessage());
        }
    }
    return $pdo;
}
