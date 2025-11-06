<?php
//require_once __DIR__ . '/../config/db.php';
//include('../controllers/db_controller.php');
require("security/session_bootstrap.php");
include('../controllers/helpers.php');
class admin_controller {



        public static function getUserStats() {
        
            $select_query = 
            '
            SELECT count(*) FROM public."User"';

            // $params = array([':username', $username, PDO::PARAM_STR]);
            $result = DBController::exec_statement($select_query)->fetch();
            //error_log(json_encode($result));
            return $result;
        

        }

        public static function deleteStaff() {
            //DBController::exec_statement('CREATE EXTENSION IF NOT EXISTS "uuid-ossp";');
            $userid = $_POST['userid'] ?? '';
            try{
                // Delete from Staff first
                $delete_staff_query = <<<SQL
                DELETE FROM public."Staff"
                WHERE "UserID" = :user_id;
                SQL;

                $params_staff = [
                    [":user_id", $userid, PDO::PARAM_STR]
                ];

                DBController::exec_statement($delete_staff_query, $params_staff);

                // Delete from User
                $delete_user_query = <<<SQL
                DELETE FROM public."User"
                WHERE "UserID" = :user_id;
                SQL;

                $params_user = [
                    [":user_id", $userid, PDO::PARAM_STR]
                ];

                DBController::exec_statement($delete_user_query, $params_user);

                $_SESSION["delete_staff_status"] = "Staff deleted successfully.";
                header("Location: /update_staff");

            }catch (Exception $e) {
                error_log("Error: " . $e->getMessage());
                // On error
                $_SESSION["delete_staff_status"] = "Failed to delete staff. Please try again.";
                header("Location: /update_staff");
                exit;
            }
            }

        public static function updateStaff() {
            DBController::exec_statement('CREATE EXTENSION IF NOT EXISTS "uuid-ossp";');
            // Retrieve and sanitize inputs
            $userid = $_POST['userid'] ?? '';
            $name = $_POST['username'] ?? '';
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';
            $role = strtoupper($_POST['role'] ?? 'STAFF'); 
            $displayName = $_POST['display_name'] ?? $name; 
            $dob = $_POST['dob'] ?? null;
            $contact = $_POST['contact'] ?? null;
            try {

                // Validation
                if ( empty($userid) || empty($name) || empty($email) || empty($role) || empty($dob) || empty($displayName) || empty($contact) ){
                    throw new Exception("Missing required fields.");
                }

                if ($password !== $confirm_password) {
                    throw new Exception("Passwords do not match.");
                }

                if (empty($password)){
                    // Update User
                    $update_user_query = <<<SQL
                    UPDATE public."User"
                    SET
                        "Username" = :username,
                        "Role" = :role
                    WHERE "UserID" = :user_id;
                    SQL;

                    $params_user = [
                        [":username", $name, PDO::PARAM_STR],
                        [":role", $role, PDO::PARAM_STR],
                        [":user_id", $userid, PDO::PARAM_STR]   // retrieved from Staff or search
                    ];

                }
                else{
                    // Hash password
                    $hashed_password = password_hash($password, PASSWORD_ARGON2ID);
                
                    // Update User
                    $update_user_query = <<<SQL
                    UPDATE public."User"
                    SET
                        "Username" = :username,
                        "Role" = :role,
                        "Password" = :hashed_password
                    WHERE "UserID" = :user_id;
                    SQL;

                    $params_user = [
                        [":username", $name, PDO::PARAM_STR],
                        [":role", $role, PDO::PARAM_STR],
                        [":hashed_password", $hashed_password, PDO::PARAM_STR],
                        [":user_id", $userid, PDO::PARAM_STR]   // retrieved from Staff or search
                    ];
                }

                DBController::exec_statement($update_user_query, $params_user);

                $update_staff_query = <<<SQL
                UPDATE public."Staff"
                SET
                    "DisplayName" = :display_name,
                    "DOB" = :dob,
                    "Contact" = :contact,
                    "Email" = :email
                WHERE "UserID" = :user_id;
                SQL;

                $params_staff = [
                    [":display_name", $displayName, PDO::PARAM_STR],
                    [":dob", $dob ?? null, PDO::PARAM_STR],         // will insert NULL if $dob is null
                    [":contact", $contact ?? null, PDO::PARAM_INT], // will insert NULL if $contact is null
                    [":email", $email, PDO::PARAM_STR],
                    [":user_id", $userid, PDO::PARAM_STR]       // provided from search
                ];

                DBController::exec_statement($update_staff_query, $params_staff);
                // On success
                $_SESSION["update_staff_status"] = "Staff updated successfully!";
                // Redirect back to create_staff
                header("Location: /update_staff");
                exit;

            } catch (Exception $e) {
                error_log("Error: " . $e->getMessage());
                // On error
                $_SESSION["update_staff_status"] = "Failed to update staff. Please try again.";
                header("Location: /update_staff");
                exit;
            }
        }

        public static function searchStaff() {
            DBController::exec_statement('CREATE EXTENSION IF NOT EXISTS "uuid-ossp";');
            $name = $_POST['name'] ?? '';
            error_log($name);
            try {
                //SELECT "Username", "Role", "LastLogin" FROM public."User" WHERE "Username" like :name;
                $get_userid_query = <<<SQL
                SELECT 
                    u."UserID",
                    u."Username",
                    u."Role",
                    u."LastLogin",
                    s."DisplayName",
                    s."DOB",
                    s."Contact",
                    s."Email"
                FROM public."User" u
                JOIN public."Staff" s ON u."UserID" = s."UserID"
                WHERE u."Username" ILIKE :name;
                SQL;

                $params_get_userid = [
                    [":name", "%{$name}%", PDO::PARAM_STR]
                ];

                $stmt = DBController::exec_statement($get_userid_query, $params_get_userid);
                $results = $stmt->fetchAll();
                error_log(json_encode($results));

                $_SESSION["search_results"] = $results;
                // Redirect back to create_staff
                header("Location: /update_staff");
                exit;

            } catch (Exception $e) {
                error_log("Error: " . $e->getMessage());
                // On error
                header("Location: /create_staff?status=error");
                exit;
            }
        }

       public static function createStaff() {
            DBController::exec_statement('CREATE EXTENSION IF NOT EXISTS "uuid-ossp";');
            // Retrieve and sanitize inputs
            $name = $_POST['name'] ?? '';
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';
            $role = strtoupper($_POST['role'] ?? 'STAFF'); 
            $displayName = $_POST['display_name'] ?? $name; 
            $dob = $_POST['dob'] ?? null;
            $contact = $_POST['contact'] ?? null;
            $now = new DateTime();
            $curr_date = $now->format('Y-m-d H:i:s');

            try {

                // Validation
                if (empty($name) || empty($email) || empty($password) || empty($role)) {
                    throw new Exception("Missing required fields.");
                }

                if ($password !== $confirm_password) {
                    throw new Exception("Passwords do not match.");
                }

                // Hash password
                $hashed_password = password_hash($password, PASSWORD_ARGON2ID);
                // Insert User
                $insert_user_query = <<<SQL
                INSERT INTO public."User"(
                    "UserID", "Username", "Password", "Role", "LastLogin")
                VALUES (
                    gen_random_uuid(), :name, :hashed_password, :role, NOW()
                );
                SQL;

                $params_user = [
                    [":name", $name, PDO::PARAM_STR],
                    [":hashed_password", $hashed_password, PDO::PARAM_STR],
                    [":role", $role, PDO::PARAM_STR]
                ];


                DBController::exec_statement($insert_user_query, $params_user);

                $get_userid_query = <<<SQL
                SELECT "UserID" FROM public."User" WHERE "Username" = :name;
                SQL;

                $params_get_userid = [
                    [":name", $name, PDO::PARAM_STR]
                ];

                $stmt = DBController::exec_statement($get_userid_query, $params_get_userid);
                $new_user_id = $stmt->fetchColumn();

                if (!$new_user_id) {
                    throw new Exception("Failed to retrieve UserID for the new user.");
                }

                $insert_staff_query = <<<SQL
                INSERT INTO public."Staff"(
                    "StaffID", "UserID", "DisplayName", "DOB", "Contact", "Email")
                VALUES (
                    gen_random_uuid(), :user_id, :display_name, :dob, :contact, :email
                );
                SQL;

                // Bind parameters
                $params_staff = [
                    [":user_id", $new_user_id, PDO::PARAM_STR],
                    [":display_name", $displayName, PDO::PARAM_STR],
                    [":dob", $dob ?? null, PDO::PARAM_STR],       // will insert NULL if $dob is null
                    [":contact", $contact ?? null, PDO::PARAM_INT], // will insert NULL if $contact is null
                    [":email", $email, PDO::PARAM_STR]
                ];
                // Execute
                DBController::exec_statement($insert_staff_query, $params_staff);

                // On success
                $_SESSION["create_staff_status"] = "Staff created successfully!";
                // Redirect back to create_staff
                header("Location: /create_staff");
                exit;

            } catch (Exception $e) {
                error_log("Error: " . $e->getMessage());
                // On error
                $_SESSION["create_staff_status"] = "Failed to create staff. Please try again.";
                header("Location: /create_staff");
                exit;
            }
        }

            
}

        // --- Handle POST requests from the form ---
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';

            if ($action === 'create_staff') {
                admin_controller::createStaff();
            }
            if ($action === 'search_staff') {
                admin_controller::searchStaff();
            }
            if ($action === 'update_staff') {
                admin_controller::updateStaff();
            }
            if ($action === 'delete_staff') {
                admin_controller::deleteStaff();
            }
        }