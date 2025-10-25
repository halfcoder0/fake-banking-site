<?php
try {
    $dsn = sprintf(
        "pgsql:host=%s;port=%s;dbname=%s;",
        $_ENV['DATABASE_HOST'],
        $_ENV['DATABASE_PORT'],
        $_ENV['DATABASE_NAME']
    );

    $pdo = new PDO($dsn, $_ENV['DATABASE_USERNAME'], $_ENV['DATABASE_PASSWORD'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,   // Throw exceptions
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Return associative arrays
        PDO::ATTR_EMULATE_PREPARES => false             // Use real prepared statements
    ]);
} catch (PDOException $e) {
    die("DB Connection failed: " . $e->getMessage());
}
