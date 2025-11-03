<?php
require_once(__DIR__ . '/../includes/dbconnection.php');
require_once('helpers.php');

/**
 * Attempt to authenticate user
 */
function attempt_auth($creds)
{
    if (!csrf_verify()) handle_error('CSRF ERROR');

    $username = trim($creds['username'] ?? '');
    $password  = trim($creds['password'] ?? '');

    if (check_for_non_alphanum($username)) handle_error('Only alphabets & numbers allowed for username.');
    if ($username === '' || $password === '') handle_error('Invalid username/password.');

    $user_hash = retrieve_hash($username);

    if ($user_hash === '') handle_error('Invalid username/password.');
    if (!password_verify($password, $user_hash)) handle_error('Invalid username/password.');

    $user_data = get_user_info($username);

    if (!$user_data) handle_error('Error retrieving user.');

    hard_recreate_session();
    $role = $user_data["Role"];
    $_SESSION["Role"] = $role;
    $_SESSION["UserID"] = $user_data["UserID"];
    update_login_time($username);

    redirect_user($role);
}

/**
 * redirect user to appropriate pages
 */
function redirect_user($role)
{
    switch ($role) {
        case 'User':
            header('Location: /dashboard');
            exit;
    }

    header('Location: /dashboard');
    exit;
}

/**
 * Update user login time
 */
function update_login_time($username)
{
    $update_query = '
        UPDATE public."User"
        SET "LastLogin" = :lastLogin
        WHERE "Username" = :username;';
    $now = new DateTime();
    $now = $now->format('Y-m-d H:i:s');

    $pdo = get_pdo();
    $stmt = $pdo->prepare($update_query);
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->bindParam(':lastLogin', $now, PDO::PARAM_STR);
    $success = $stmt->execute();

    if (!$success) handle_error('Error updating user.');
}

/**
 * Get user role & id from DB
 */
function get_user_info($username)
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

/**
 * Get user's password hash from DB
 */
function retrieve_hash($username)
{
    $hash = '';
    $query = '
    SELECT "Password"
        FROM public."User"
        WHERE
            "Username" = :username
        LIMIT 1;
    ';

    $pdo = get_pdo();
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->execute();
    $result = $stmt->fetch();

    if ($result) $hash = $result['Password'];

    return $hash;
}

/**
 *  Redirect & set error in session
 */
function handle_error($msg)
{
    $_SESSION['error'] = $msg;
    header('Location: /login');
    exit;
}
