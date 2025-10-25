<?php
require_once('../includes/dbconnection.php');

function attempt_auth($creds)
{
    if (!csrf_verify()) handle_error('CSRF ERROR');

    $username = trim($creds['username'] ?? '');
    $password  = trim($creds['password'] ?? '');

    if ($username === '' || $password === '') handle_error('Invalid username/password.');

    header('Location: /dashboard');
}

function handle_error($msg){
    $_SESSION['error'] = $msg;
    header('Location: /login');
}
