<?php
include('../includes/dbconnection.php'); // sets up $pdo
include('helpers.php');

function attempt_auth_user($input)
{
    $username = trim($input['username'] ?? '');
    $password  = trim($input['password'] ?? '');
    
    if ($username === '' || $password === '') {
        send_invalid_response();
    }
    echo json_encode(['token' => 'yes']);
}

function send_invalid_response()
{
    http_response_code(400);
    echo json_encode(['Error' => 'Invalid username/password.']);
    exit;
}

header('Content-Type: application/json');
#$input = json_decode(file_get_contents('php://input'), true);
$input = $_POST;
attempt_auth_user($input);


