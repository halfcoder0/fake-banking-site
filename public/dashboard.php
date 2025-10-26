<?php 
require("../controllers/security/session_bootstrap.php");
$userid = $_SESSION['UserID'] ?? '';
$role = $_SESSION['Role'] ?? '';

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
    <link rel="stylesheet" href="./css/style.css">
  </head>
<body>
  <div class="wrapper">
    <h2>Welcome !! <?php echo htmlspecialchars($role . '-' . $userid); ?>
      <div class="input-box button">
    <a href="/logout">Logout</a>
  </div>
</h2>
  </div>
</body>
</html>
