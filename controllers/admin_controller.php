<?php
//require_once __DIR__ . '/../config/db.php';
include('../includes/dbconnection.php');
class admin_controller {
    private $pdo;

    public function __construct() {
        try {
            $this->pdo = get_pdo();
            
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }
    

    public function getUserStats() {
    {
        $select_query = '
        SELECT "UserID", "Role"
            FROM public."User"
            WHERE
                "Username" = :username
            LIMIT 1;';

        $pdo = get_pdo();
        $stmt = $pdo->prepare($select_query);
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->execute();

        $result = $stmt->fetch();

        return $result;
    }

    }
}