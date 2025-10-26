<?php
require_once(__DIR__ . '/../includes/dbconnection.php');
require_once('helpers.php');

function attempt_auth($creds)
{
    if (!csrf_verify()) handle_error('CSRF ERROR');

    $username = trim($creds['username'] ?? '');
    $password  = trim($creds['password'] ?? '');
    $username = remove_non_alphanum($username);

    if ($username === '' || $password === '') handle_error('Invalid username/password.');

    $user_hash = retrieve_hash($username);

    if ($user_hash === '') handle_error('Invalid username/password.');

    if (password_verify($password, $user_hash)) {
        header('Location: /dashboard');
        exit;
    }

    handle_error('Invalid username/password.');
}

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

function handle_error($msg)
{
    $_SESSION['error'] = $msg;
    header('Location: /login');
    exit;
}
