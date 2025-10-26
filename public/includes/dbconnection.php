<?php
try {
    $pdo = new PDO(
        "pgsql:host=postgres_container;port=5432;dbname=nexabank_db",
        "admin",
        "P@ssw0rd"
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("DB Connection failed: " . $e->getMessage());
}
