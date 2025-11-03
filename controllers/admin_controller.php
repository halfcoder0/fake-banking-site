<?php
//require_once __DIR__ . '/../config/db.php';
//include('../controllers/db_controller.php');
class admin_controller {
    //private $db_control;
    // public function __construct() {
    //     try {
    //         $db_control = new DBController();
    //         $db_control->init_db();
            
    //     } catch (PDOException $e) {
    //         die("Database connection failed: " . $e->getMessage());
    //     }
    //}
    

    public static function getUserStats() {
    
        $select_query = 
        '
        SELECT count(*) FROM public."User"';

        // $params = array([':username', $username, PDO::PARAM_STR]);
        $result = DBController::exec_statement($select_query)->fetch();
        error_log(json_encode($result));
        return $result;
    

    }
}