<?php
session_start();
include('../includes/dbconnection.php'); // sets up $pdo

if (isset($_POST['login'])) {
    $emailcon = $_POST['username'];
    $password = md5($_POST['password']); // hash input with MD5 to match DB

    $stmt = $pdo->prepare("SELECT id FROM tblecustomer WHERE (email = :email OR fullname= :email) AND password = :password");
    $stmt->execute([
        'email' => $emailcon,
        'password' => $password
    ]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $_SESSION['uid'] = $user['id'];
        header("Location: dashboard.php");
        exit;
    } else {
        echo "<script>alert('Invalid Details');</script>";
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
    <form method="POST">
      <div class="input-box">
        <input type="text" name="username" placeholder="Enter your username" required>
      </div>
      <div class="input-box">
        <input type="password" name="password" placeholder="Enter your password" required>
      </div>
      <div class="input-box button">
        <input type="Submit" name="login" value="Login">
      </div>
      <div class="text">
        <h3>Don’t have an account? <a href="register.php">Register now</a></h3>
      </div>
    </form>
  </div>
</body>
</html>
