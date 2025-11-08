<?php
class admin_controller
{
    public static function getUserStats()
    {
        $select_query = <<<SQL
                SELECT count(*) FROM public."User"
            SQL;

        $result = DBController::exec_statement($select_query)->fetch();
        filter_var_array($result, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        return $result;
    }

    private static function is_valid_uuid(string $uuid): bool
    {
        return (bool) preg_match(
            '/^[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[1-5][0-9a-fA-F]{3}-[89abAB][0-9a-fA-F]{3}-[0-9a-fA-F]{12}$/',
            $uuid
        );
    }

    public static function deleteStaff()
    {
        $userid = trim($_POST['userid']) ?? '';
        if (!admin_controller::is_valid_uuid($userid)) {
            $_SESSION["delete_staff_status"] = "Invalid userid. Please try again.";
            header("Location: /update_staff");
        }

        try {
            DBController::$pdo->beginTransaction();

            // Delete from Staff first
            $delete_staff_query = <<<SQL
                DELETE FROM public."Staff"
                WHERE "UserID" = :user_id;
                SQL;
            $params_staff = [
                [":user_id", $userid, PDO::PARAM_STR]
            ];

            $stmt = DBController::exec_statement($delete_staff_query, $params_staff);
            if ($stmt->rowCount() !== 1) {
                DBController::$pdo->rollBack();
                throw new Exception("Delete from Staff - " . $userid . " failed.");
            }

            // Delete from User
            $delete_user_query = <<<SQL
                DELETE FROM public."User"
                WHERE "UserID" = :user_id;
                SQL;
            $params_user = [
                [":user_id", $userid, PDO::PARAM_STR]
            ];

            $stmt = DBController::exec_statement($delete_user_query, $params_user);
            if ($stmt->rowCount() !== 1) {
                DBController::$pdo->rollBack();
                throw new Exception("Delete from User - " . $userid . " failed.");
            }

            DBController::$pdo->commit();

            $_SESSION["delete_staff_status"] = "Staff deleted successfully.";
            //  Call search function to update result
            isset($_SESSION["search_name"]) ? admin_controller::searchStaff($_SESSION["search_name"]) : header("Location: /update_staff");
        } catch (Exception $e) {
            error_log("Error: " . $e->getMessage());
            // On error
            $_SESSION["delete_staff_status"] = "Failed to delete staff. Please try again.";
            header("Location: /update_staff");
        }
        exit;
    }

    private static function throw_validation_error($msg, $session_var)
    {
        $_SESSION[$session_var] = $msg;
        throw new Exception($msg);
    }

    private static function validate_staff_fields($userid, $name, $email, $password, $confirm_password, $role, $displayName, $dob, $contact, $err_session_var)
    {
        $username_regex = ["options" => ["regexp" => "/^[A-Za-z0-9]+$/"]];
        $alphabet_regex = ["options" => ["regexp" => "/^[A-Za-z ]+$/"]];

        if (($userid !== false && empty($userid)) || empty($name) || empty($email) || empty($role) || empty($dob) || empty($displayName) || empty($contact))
            admin_controller::throw_validation_error("Missing required fields.", $err_session_var);

        if ($userid !== false && !admin_controller::is_valid_uuid($userid))
            admin_controller::throw_validation_error("Invalid userid.", $err_session_var);

        if (filter_var($name, FILTER_VALIDATE_REGEXP, $username_regex) === false)
            admin_controller::throw_validation_error("Username can only contain alphanumerics.", $err_session_var);

        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false)
            admin_controller::throw_validation_error("Invalid email.", $err_session_var);

        if (!Roles::tryFrom($role))
            admin_controller::throw_validation_error("Invalid role", $err_session_var);

        if (filter_var($displayName, FILTER_VALIDATE_REGEXP, $alphabet_regex) === false)
            admin_controller::throw_validation_error("Display name can only contain alpahbets", $err_session_var);

        if ($dob !== null) {
            try {
                $_dob = new DateTime($dob);
            } catch (DateMalformedStringException $e) {
                admin_controller::throw_validation_error("Invalid DOB", $err_session_var);
            }
        }

        if ($contact !== null && filter_var($contact, FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]]) === false)
            admin_controller::throw_validation_error("Invalid contact number", $err_session_var);

        if ($password !== $confirm_password)
            admin_controller::throw_validation_error("Passwords do not match.", $err_session_var);
    }

    public static function updateStaff()
    {
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
            admin_controller::validate_staff_fields($userid, $name, $email, $password, $confirm_password, $role, $displayName, $dob, $contact, SessionVariables::UPDATE_STAFF_STATUS);

            DBController::$pdo->beginTransaction();
            DBController::exec_statement('CREATE EXTENSION IF NOT EXISTS "uuid-ossp";');
            if (empty($password)) {
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
            } else {
                // Hash password
                $hashed_password = argon_hash($password);

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

            $stmt = DBController::exec_statement($update_user_query, $params_user);
            if ($stmt->rowCount() !== 1) {
                DBController::$pdo->rollBack();
                throw new Exception("Update user - " . $userid . " failed.");
            }

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

            $stmt = DBController::exec_statement($update_staff_query, $params_staff);
            if ($stmt->rowCount() !== 1) {
                DBController::$pdo->rollBack();
                throw new Exception("Update Staff on user - " . $userid . " failed.");
            }

            DBController::$pdo->commit();

            // On success
            if (!isset($_SESSION[SessionVariables::UPDATE_STAFF_STATUS->value]))
                $_SESSION[SessionVariables::UPDATE_STAFF_STATUS->value] = "Staff updated successfully!";

            //  Call search function to update result
            isset($_SESSION["search_name"]) ? admin_controller::searchStaff($_SESSION["search_name"]) : header("Location: /update_staff");
        } catch (Exception $e) {
            // On error
            error_log("Error: " . $e->getMessage() . "\n" . $e->getTraceAsString());
            if (!isset($_SESSION[SessionVariables::UPDATE_STAFF_STATUS->value]))
                $_SESSION["update_staff_status"] = "Failed to update staff. Please try again.";

            header("Location: /update_staff");
        }
        exit;
    }

    public static function searchStaff($search_name = '')
    {
        $name = $_POST['name'] ?? '';
        if ($search_name !== '')
            $name = $search_name;

        error_log("Admin Search:" . $name);
        $username_regex = ["options" => ["regexp" => "/^[A-Za-z0-9]+$/"]];

        try {
            if ($name !== '' && filter_var($name, FILTER_VALIDATE_REGEXP, $username_regex) === false)
                admin_controller::throw_validation_error("Username can only contain alphanumerics.", SessionVariables::UPDATE_STAFF_STATUS->value);

            DBController::exec_statement('CREATE EXTENSION IF NOT EXISTS "uuid-ossp";');
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

            $_SESSION["search_name"] = $name;
            $_SESSION["search_results"] = $results;
            // Redirect back to create_staff
            header("Location: /update_staff");
            exit;
        } catch (Exception $e) {
            error_log("Error: " . $e->getMessage());
            // On error
            header("Location: /update_staff");
            exit;
        }
    }

    public static function createStaff()
    {
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
            admin_controller::validate_staff_fields(false, $name, $email, $password, $confirm_password, $role, $displayName, $dob, $contact, SessionVariables::CREATE_STAFF_STATUS->value);

            // Hash password
            $hashed_password = argon_hash($password);

            DBController::$pdo->beginTransaction();
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

            $stmt = DBController::exec_statement($insert_user_query, $params_user);
            if ($stmt->rowCount() !== 1) {
                DBController::$pdo->rollBack();
                throw new Exception("Insert into user failed.");
            }

            $get_userid_query = <<<SQL
                SELECT "UserID" FROM public."User" WHERE "Username" = :name;
                SQL;
            $params_get_userid = [
                [":name", $name, PDO::PARAM_STR]
            ];

            $stmt = DBController::exec_statement($get_userid_query, $params_get_userid);
            $new_user_id = $stmt->fetchColumn();
            if (!$new_user_id) {
                DBController::$pdo->rollBack();
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
            $stmt = DBController::exec_statement($insert_staff_query, $params_staff);
            if ($stmt->rowCount() !== 1) {
                DBController::$pdo->rollBack();
                throw new Exception("Insert into STAFF failed.");
            }

            DBController::$pdo->commit();

            // On success
            $_SESSION[SessionVariables::CREATE_STAFF_STATUS->value] = "Staff created successfully!";
        } catch (Exception $e) {
            error_log("Error: " . $e->getMessage() . "\n" . $e->getTraceAsString());

            if (!isset($_SESSION[SessionVariables::CREATE_STAFF_STATUS->value]))
                $_SESSION[SessionVariables::CREATE_STAFF_STATUS->value] = "Failed to create staff. Please try again.";
        }
        header("Location: /create_staff");
        exit;
    }
}

// --- Handle POST requests from the form ---
if ($_SERVER['REQUEST_METHOD'] !== 'POST')
    redirect_404();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    switch ($action) {
        case 'create_staff':
            admin_controller::createStaff();
            break;
        case 'search_staff':
            admin_controller::searchStaff();
            break;
        case 'update_staff':
            admin_controller::updateStaff();
            break;
        case 'delete_staff':
            admin_controller::deleteStaff();
            break;
        default:
            redirect_404();
    }
}
