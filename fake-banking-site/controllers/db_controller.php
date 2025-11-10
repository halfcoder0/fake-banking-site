<?php

use GrahamCampbell\ResultType\Error;

/**
 * **Database Controller** \
 * Initialize & store PDO
 *
 * Helper functions:
 * - exec_statement($query,$params)
 */
class DBController
{

    public static ?PDO $pdo = NULL;

    /**
     * Initialize database connection
     */
    public static function init_db()
    {
        if (DBController::$pdo !== NULL) return;

        try {
            $dsn = sprintf(
                "pgsql:host=%s;port=%s;dbname=%s;",
                $_ENV['POSTGRES_HOST'],
                $_ENV['POSTGRES_DB_PORT'],
                $_ENV['POSTGRES_DB']
            );

            DBController::$pdo = new PDO($dsn, $_ENV['POSTGRES_USER'], $_ENV['POSTGRES_PASSWORD'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,   // Throw exceptions
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Return associative arrays
                PDO::ATTR_EMULATE_PREPARES => false             // Use real prepared statements
            ]);
        } catch (PDOException $e) {
            error_log("DB Connection failed: " . $e->getMessage());
        }
    }

    /**
     * Execute & returns the result of a SQL Query \
     * Can include params to bind \
     * Usage: exec_statement($query, array([ $param_name, value, PDO type ]) ) \
     * Example: exec_statement($query,array([':username', $username, PDO::PARAM_STR]))
     */
     public static function exec_statement(string $query, array $params = []): bool|PDOStatement
     {
         if (DBController::$pdo === NULL) {
             error_log("[DB ERROR] PDO is null, DB not initialized!");
             throw new Exception("Error obtaining data.");
         }

         try {
             error_log("[DB] SQL: " . $query);
             $stmt = DBController::$pdo->prepare($query);

             foreach ($params as $p) {
                 error_log("[DB] Binding {$p[0]} = " . var_export($p[1], true));
                 $stmt->bindValue($p[0], $p[1], $p[2]);
             }

             $stmt->execute();
             error_log("[DB] Statement executed successfully.");
             return $stmt;

         } catch (PDOException $e) {
             error_log("[DB ERROR] SQL: " . $query);
             error_log("[DB ERROR] Params: " . print_r($params, true));
             error_log("[DB ERROR] Message: " . $e->getMessage());
             throw new Exception("Error obtaining data.");
         }
     }

}
