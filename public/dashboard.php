<?php 
require("../controllers/security/session_bootstrap.php");
$userid = $_SESSION['UserID'] ?? '';
$role = $_SESSION['Role'] ?? '';
// error_log(json_encode($_SESSION));

if ($userid === '' || $role === ''){
  error_log('no user.');
  header('Location: /');
  exit;
}

?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | NexaBank</title>
    <!-- Custom CSS -->
    <link href="./dist/css/style.min.css" rel="stylesheet" nonce="<?= htmlspecialchars($nonce, ENT_QUOTES) ?>" crossorigin="anonymous">

    <style nonce="<?= htmlspecialchars($nonce, ENT_QUOTES) ?>">
        .auth-wrapper {
            background: url(./assets/images/big/auth-bg.jpg) no-repeat center center;
            background-color: #f0f5f9;
        }
    </style>
  </head>
<body>
  <div class="wrapper">
    <h2>Welcome !! <?php echo htmlspecialchars($role . '-' . $userid); ?>
      <div class="input-box button">
        <a href="/create_customer_account">Create New Account</a> <br>
        <a href="/delete_customer_account">Delete Bank Account</a> <br>
    <a href="/logout">Logout</a>
  </div>
</h2>
  </div>
</body>
</html>
