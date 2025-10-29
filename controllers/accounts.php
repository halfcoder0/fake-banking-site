<?php
require_once(__DIR__ . '/../includes/dbconnection.php');
require_once('helpers.php');

function create_account($account_details)
{
    if (!csrf_verify()) handle_error('CSRF ERROR');

    $username = trim($account_details['username'] ?? '');
    $password  = trim($account_details['password'] ?? '');

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