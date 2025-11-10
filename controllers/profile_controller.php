<?php
class ProfileController
{
    /* ============================================================
     * PUBLIC METHODS
     * ============================================================ */

    /** 
     * Load profile data for current user (from session)
     */
    public function getProfile(string $user_id): array
    {
        $sql = <<<SQL
            SELECT 
                u."Username",
                c."FirstName",
                c."LastName",
                c."DOB",
                c."ContactNo",
                c."Email",
                c."DisplayName"
            FROM public."User" u
            JOIN public."Customer" c ON c."UserID" = u."UserID"
            WHERE u."UserID" = :uid
            LIMIT 1;
        SQL;

        $params = [
            [':uid', $user_id, PDO::PARAM_STR]
        ];

        $stmt = DBController::exec_statement($sql, $params);
        $row = $stmt->fetch();

        return $row ?: [];
    }


    /**
     * Update profile for current user
     */
    public function updateProfile(array $input, string $user_id, string $customer_id): array
    {
        $clean = $this->sanitize($input);

        $error = $this->validate($clean, $user_id);
        if ($error) {
            return ['success' => false, 'error' => $error];
        }

        // Update User table
        $this->updateUser($clean, $user_id);

        // Update Customer table
        $this->updateCustomer($clean, $customer_id, $user_id);

        // Update password only if provided
        if (!empty($clean['password'])) {
            $this->updatePassword($clean['password'], $user_id);
        }

        return ['success' => true];
    }



    /* ============================================================
     * PRIVATE HELPERS 
     * ============================================================ */

    /** Basic sanitization + XSS protection */
    private function clean(string $str): string
    {
        return htmlspecialchars(trim($str), ENT_QUOTES, 'UTF-8');
    }

    /** Sanitize all fields */
    private function sanitize(array $d): array
    {
        return [
            'username'     => $this->clean($d['username'] ?? ''),
            'password'     => trim($d['password'] ?? ''),
            'repeat_pass'  => trim($d['repeat_pass'] ?? ''),
            'firstName'    => $this->clean($d['firstName'] ?? ''),
            'lastName'     => $this->clean($d['lastName'] ?? ''),
            'dob'          => trim($d['dob'] ?? ''),
            'contactNo'    => trim($d['contactNo'] ?? ''),
            'email'        => trim($d['email'] ?? ''),
            'displayName'  => $this->clean($d['displayName'] ?? ''),
        ];
    }


    /** Validation */
    private function validate(array $d, string $user_id): ?string
    {
        if ($d['password'] !== '' && strlen($d['password']) < 8)
            return "Password must be at least 8 characters.";

        if ($d['password'] !== $d['repeat_pass'])
            return "Passwords do not match.";

        if (!filter_var($d['email'], FILTER_VALIDATE_EMAIL))
            return "Invalid email format.";

        if ($d['username'] === '' || strlen($d['username']) < 3)
            return "Username must be at least 3 characters.";

        if (!preg_match('/^[a-zA-Z0-9]+$/', $d['username']))
            return "Username must be alphanumeric.";

        // Check for required values
        if ($d['firstName'] === '') return "First name is required.";
        if ($d['lastName'] === '') return "Last name is required.";
        if ($d['displayName'] === '') return "Display name is required.";
        if ($d['dob'] === '') return "Date of birth is required.";
        if ($d['contactNo'] !== '' && !ctype_digit($d['contactNo']))
            return "Contact number must be numeric.";

        // Check username uniqueness
        if ($this->usernameExists($d['username'], $user_id))
            return "Username is not valid. Please choose a different one.";

        return null;
    }


    /** Check if username belongs to another user */
    private function usernameExists(string $username, string $user_id): bool
    {
        $sql = <<<SQL
            SELECT 1
            FROM public."User"
            WHERE "Username" = :username
            AND "UserID" <> :uid
            LIMIT 1;
        SQL;

        $params = [
            [':username', $username, PDO::PARAM_STR],
            [':uid', $user_id, PDO::PARAM_STR]
        ];

        return DBController::exec_statement($sql, $params)->fetch() !== false;
    }


    /** Update User table */
    private function updateUser(array $d, string $user_id): void
    {
        $sql = <<<SQL
            UPDATE public."User"
            SET "Username" = :username
            WHERE "UserID" = :uid;
        SQL;

        $params = [
            [':username', $d['username'], PDO::PARAM_STR],
            [':uid', $user_id, PDO::PARAM_STR]
        ];

        DBController::exec_statement($sql, $params);
    }


    /** Update Customer table (enforce ownership) */
    private function updateCustomer(array $d, string $customer_id, string $user_id): void
    {
        $sql = <<<SQL
            UPDATE public."Customer"
            SET 
                "FirstName"   = :fname,
                "LastName"    = :lname,
                "DOB"         = :dob,
                "ContactNo"   = :contact,
                "Email"       = :email,
                "DisplayName" = :displayName
            WHERE "CustomerID" = :cid
            AND "UserID" = :uid;   
        SQL;

        $params = [
            [':fname', $d['firstName'], PDO::PARAM_STR],
            [':lname', $d['lastName'], PDO::PARAM_STR],
            [':dob', $d['dob'], PDO::PARAM_STR],
            [':contact', $d['contactNo'], PDO::PARAM_INT],
            [':email', $d['email'], PDO::PARAM_STR],
            [':displayName', $d['displayName'], PDO::PARAM_STR],
            [':cid', $customer_id, PDO::PARAM_STR],
            [':uid', $user_id, PDO::PARAM_STR]
        ];

        DBController::exec_statement($sql, $params);
    }


    /** Update user password */
    private function updatePassword(string $newPass, string $user_id): void
    {
        $hash = password_hash($newPass, PASSWORD_DEFAULT);

        $sql = <<<SQL
            UPDATE public."User"
            SET "Password" = :pw
            WHERE "UserID" = :uid;
        SQL;

        $params = [
            [':pw', $hash, PDO::PARAM_STR],
            [':uid', $user_id, PDO::PARAM_STR]
        ];

        DBController::exec_statement($sql, $params);
    }
}
