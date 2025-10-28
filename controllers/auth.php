<?php
require_once('transfer_controller.php');
require_once('helpers.php');
require_once('enum.php');

class AuthController
{
    /**
     * Attempt to authenticate user
     */
    public function attempt_auth(array $creds)
    {
        if (!csrf_verify()) AuthController::return_error('CSRF ERROR');

        $username = trim($creds['username'] ?? '');
        $password  = trim($creds['password'] ?? '');

        if (check_for_non_alphanum($username)) AuthController::return_error('Only alphabets & numbers allowed for username.');
        if ($username === '' || $password === '') AuthController::return_error('Invalid username/password.');

        $user_hash = AuthController::retrieve_hash($username);

        if ($user_hash === null) AuthController::return_error('Invalid username/password.');
        if (!password_verify($password, $user_hash)) AuthController::return_error('Invalid username/password.');

        $user_data = AuthController::get_user_info($username);

        if ($user_data === false) AuthController::return_error('Error retrieving user.');

        $role = $user_data["Role"];
        hard_recreate_session();

        $_SESSION["UserID"] = $user_data["UserID"];
        $_SESSION["Role"] = $user_data["Role"];
        
        AuthController::handle_different_roles($user_data);

        AuthController::update_login_time($username);
        error_log(json_encode($_SESSION));
        AuthController::redirect_user($role);
    }

    /**
     * Redirect user to appropriate pages
     */
    private function redirect_user(string $role)
    {
        switch (Roles::tryFrom($role)) {
            case Roles::USER:
                header('Location: /dashboard');
                exit;
            case Roles::STAFF:
                header('Location: /staff-dashboard');
                exit;
            case Roles::ADMIN:
                header('Location: /admin-dashboard');
                exit;
        }
        
        header('Location: /logout');
        exit;
    }

    /**
     * Update user login time
     */
    private function update_login_time(string $username)
    {
        $update_query = '
            UPDATE public."User"
            SET "LastLogin" = :lastLogin
            WHERE "Username" = :username;';
        $now = new DateTime();
        $now = $now->format('Y-m-d H:i:s');

        $params = array(
            [':username', $username, PDO::PARAM_STR],
            [':lastLogin', $now, PDO::PARAM_STR]
        );
        $success = DBController::exec_statement($update_query, $params);

        if (!$success) AuthController::return_error('Error updating user.');
    }

    /**
     * Get user role & id from DB
     */
    private function get_user_info(string $username): false|array
    {
        $select_query = <<<SQL
        SELECT "UserID", "Role"
            FROM public."User"
            WHERE
                "Username" = :username
            LIMIT 1;
        SQL;

        $params = array([':username', $username, PDO::PARAM_STR]);
        $result = DBController::exec_statement($select_query, $params)->fetch();

        return $result;
    }

    /**
     * Get user's password hash from DB
     */
    private function retrieve_hash(string $username): ?string
    {
        error_log("Getting hash");
        $query = <<<SQL
        SELECT "Password"
            FROM public."User"
            WHERE
                "Username" = :username
            LIMIT 1;
        SQL;

        $params = array([':username', $username, PDO::PARAM_STR]);
        $result = DBController::exec_statement($query, $params)->fetch();

        if ($result === false) return null;

        return isset($result['Password']) ? (string)$result['Password'] : null;
    }

    /**
     * Redirect user & set error in session
     */
    private function return_error(string $msg, $redirect_url = '/login')
    {
        $_SESSION['error'] = $msg;
        header('Location: ' . $redirect_url);
        exit;
    }

    /**
     * Get & Set role-specific info
     */
    private function handle_different_roles(array $user_data)
    {
        $user_id = $user_data["UserID"];
        $role = $user_data["Role"];

        switch (Roles::tryFrom($role)) {
            case Roles::USER:
                AuthController::handle_user($user_id);
                break;
            case Roles::STAFF:
                AuthController::handle_staff($user_id);
                break;
            case Roles::ADMIN:
                AuthController::handle_staff($user_id);
                break;
            default:
                error_log("Unknown role {$role} for user {$user_id}");
                throw new Exception("Error with user info.");
        }
    }

    /**
     * Get & set customer details to session
     */
    private function handle_user(string $user_id)
    {
        $query = <<<SQL
            SELECT "CustomerID", "DisplayName"
                FROM public."Customer"
                WHERE "UserID" = :userid
            LIMIT 1;
        SQL;

        $params = array([':userid', $user_id, PDO::PARAM_STR]);
        $result = DBController::exec_statement($query, $params)->fetch();

        $_SESSION["CustomerID"] = $result["CustomerID"];
        $_SESSION["DisplayName"] = $result["DisplayName"];
    }

    /**
     * Get & set staff details to session
     */
    private function handle_staff(string $user_id)
    {
        $query = <<<SQL
            SELECT "StaffID", "DisplayName"
                FROM public."Staff"
                WHERE "UserID" = :userid
            LIMIT 1;
        SQL;

        $params = array([':userid', $user_id, PDO::PARAM_STR]);
        $result = DBController::exec_statement($query, $params)->fetch();

        $_SESSION["StaffID"] = $result["StaffID"];
        $_SESSION["DisplayName"] = $result["DisplayName"];
    }

    /**
     * Check if user is authenticated & if user is allowed to view this page \
     * if user is not authenticated, redirect to page DEFAULT:'/login' \
     * if user's role is allowed for the page ($allowed_roles) DEFAULT: USER
     */
    public function check_user_role(array $allowed_roles = [Roles::USER], $redirect_url = '/login')
    {
        $user_role = $_SESSION['Role'] ?? '';

        if ($user_role === '') AuthController::return_error('', $redirect_url);
        if (!in_array($user_role, $allowed_roles)) AuthController::return_error('', $redirect_url);
    }
}
