<?php
require_once('helpers.php');
require_once('enum.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class AuthController
{
    const MAX_USERNAME_LEN = 50;
    const MAX_PASSWORD_LEN = 100;

    /**
     * Validate credentials before sending OTP
     */
    public function attempt_auth(array $creds)
    {
        if (!csrf_verify()) AuthController::return_error('CSRF ERROR');

        $username = trim($creds['username'] ?? '');
        $password = trim($creds['password'] ?? '');

        if (is_valid_username($username) === false)
            AuthController::return_error('Invalid username/password.');

        $user_hash = AuthController::retrieve_hash($username);
        if ($user_hash === null || !password_verify($password, $user_hash)) {
            AuthController::return_error('Invalid username/password.');
        }

        $user_data = AuthController::get_user_info($username);
        if ($user_data === false) AuthController::return_error('Error retrieving user.');

        AuthController::generate_otp($user_data);
    }

    /**
     * Generate OTP & redirect to OTP page
     */
    private function generate_otp($user_data)
    {
        //Generate OTP
        $otp = random_int(100000, 999999);
        $expiry = date('Y-m-d H:i:s', strtotime('+5 minutes'));

        $insertOtp = <<<SQL
          INSERT INTO "UserOTP" ("UserID", "Code", "ExpiresAt")
          VALUES (:uid, :code, :exp)
          ON CONFLICT ("UserID") DO UPDATE
          SET "Code" = EXCLUDED."Code", "ExpiresAt" = EXCLUDED."ExpiresAt";
        SQL;

        DBController::exec_statement($insertOtp, [
            [':uid', $user_data['UserID'], PDO::PARAM_STR],
            [':code', $otp, PDO::PARAM_STR],
            [':exp', $expiry, PDO::PARAM_STR]
        ]);

        switch (Roles::tryFrom($user_data['Role'])){
            case Roles::USER:
                $query = <<<SQL
                    SELECT "Email" FROM "Customer" WHERE "UserID" = :uid;
                SQL;
                break;
            case Roles::STAFF:
                $query = <<<SQL
                    SELECT "Email" FROM "Staff" WHERE "UserID" = :uid;
                SQL;
                break;
            case Roles::ADMIN:
                $query = <<<SQL
                    SELECT "Email" FROM "Staff" WHERE "UserID" = :uid;
                SQL;
                break;
            case Roles:: DELETED:
                AuthController::return_error('Invalid username/password.');
                break;
            default:
                AuthController::return_error('Error with login.');
                break;
        }

        $params = [[':uid', $user_data['UserID'], PDO::PARAM_STR]];
        $stmt = DBController::exec_statement($query, $params);
        
        $row = $stmt->fetch();
        $email = $row['Email'];

        // $mail = new PHPMailer(true);
        // try {
        //     $mail->isSMTP();
        //     $mail->Host       = 'smtp.gmail.com';
        //     $mail->SMTPAuth   = true;
        //     $mail->Username   = $_ENV['SMTP_USER'];         // your Gmail
        //     $mail->Password   = $_ENV['SMTP_PASS'];        // 16-char App Password
        //     $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        //     $mail->Port       = 587;

        //     $mail->setFrom('nexabanksit@gmail.com', 'Nexabank');
        //     $mail->addAddress($email);         // send to user’s email

        //     $mail->isHTML(true);
        //     $mail->Subject = 'Your Nexabank OTP Code';
        //     $mail->Body    = "Your OTP is <b>{$otp}</b>. It will expire in 5 minutes.";
        //     $mail->AltBody = "Your OTP is {$otp}. It will expire in 5 minutes.";

        //     $mail->send();
        // } catch (Exception $e) {
        //     error_log("Mailer Error: {$mail->ErrorInfo}");
        //     AuthController::return_error('Unable to send OTP email.');
        // }

        $_SESSION['pending_user'] = $user_data['UserID'];
        $_SESSION['pending_role'] = $user_data['Role'];

        header("Location: /otp");
        exit;
    }

    /**
     * Redirect user to appropriate pages
     */
    public function redirect_user(string $role)
    {
        switch (Roles::tryFrom($role)) {
            case Roles::USER:
                error_log("user page");
                header('Location: /dashboard');
                exit;
            case Roles::STAFF:
                error_log("staff page");
                header('Location: /viewCustomers');
                exit;
            case Roles::ADMIN:
                error_log("admin page");
                header('Location: /admin-dashboard');
                exit;
        }

        header('Location: /logout');
        exit;
    }

    /**
     * Update user login time
     */
    private function update_login_time()
    {
        $userID = $_SESSION["UserID"] ?? '';
        if ($userID === '')
            AuthController::return_error('Error with login');

        $update_query = '
            UPDATE public."User"
            SET "LastLogin" = :lastLogin
            WHERE "UserID" = :userID;';
        $now = new DateTime();
        $now = $now->format('Y-m-d H:i:s');

        $params = array(
            [':userID', $userID, PDO::PARAM_STR],
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
        $query = <<<SQL
        SELECT "Password"
            FROM public."User"
            WHERE
                "Username" = :username 
                AND NOT "Role" = :deleted
            LIMIT 1;
        SQL;

        $params = [
            [':username', $username, PDO::PARAM_STR],
            [':deleted', Roles::DELETED->value, PDO::PARAM_STR]
        ];
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
    public function handle_different_roles(array $user_data)
    {
        $user_id = $user_data["UserID"];
        $role = $user_data["Role"];

        hard_recreate_session();

        $_SESSION["Role"] = $role;
        $_SESSION["UserID"] = $user_id;

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

        AuthController::update_login_time();
        AuthController::redirect_user($role);
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
    public function check_user_role(array $allowed_roles = [Roles::USER])
    {
        if (!isset($_SESSION['Role'])) {
            error_log('No role found in session, likely not logged in.');
            redirect_404();
        }

        $user_role = Roles::tryFrom($_SESSION['Role']);

        if (!in_array($user_role, $allowed_roles))
            redirect_404();
    }
}
