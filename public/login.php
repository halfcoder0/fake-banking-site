<?php
include('../controllers/helpers.php');
session_start();

if (isset($_POST['username']) && isset($_POST['password'])) {
  attempt_login();
  header('Location: /login');
  exit;
}

function attempt_login()
{
  $username = $_POST['username'] ?? '';
  $password = $_POST['password'] ?? '';

  $auth_url = "http://nginx_webserver_container/auth/login";
  $post_data = [
    'username' => $username,
    'password' => $password
  ];

  $response = http_post($auth_url, $post_data);

  if ($response === false) {
    $_SESSION["Error"] = "Curl Error";
    header('Location: /login');
    return;
  }

  $data = json_decode($response['body'], true);
  if (json_last_error() !== JSON_ERROR_NONE) {
    $_SESSION['Error'] = 'Bad JSON from auth service';
    echo $response['body'];
    echo $response['error'];
    //header('Location: /login');
    exit;
  }

  if (!empty($data['Error']) || !empty($data['error'])) {
    $_SESSION['Error'] = $data['Error'] ?? $data['error'];
    header('Location: /login');
    return;
  }

  if (isset($data['token'])) {
    header("Location: /dashboard");
    exit();
  }
}

?>

<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login | NexaBank</title>
  <link rel="stylesheet" href="./css/style.css">
</head>

<body>
  <div class="wrapper">
    <h2>Login</h2>
    <form method="POST" action="/login">
      <div class="input-box">
        <input type="text" name="username" placeholder="Enter your username" required>
      </div>
      <div class="input-box">
        <input type="password" name="password" placeholder="Enter your password" required>
      </div>
      <?php
      if (isset($_SESSION['Error'])) {
        echo $_SESSION["Error"];
        $_SESSION["Error"] = '';
      }
      ?>
      <div class="input-box button">
        <input type="Submit" name="login" value="Login">
      </div>
      <div class="text">
        <h3>Don't have an account? <a href="register.php">Register now</a></h3>
      </div>
    </form>
  </div>
</body>

</html>