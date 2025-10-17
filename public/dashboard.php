<?php
session_start();
include('../includes/dbconnection.php'); // this sets up $pdo

// Check if user is logged in
if (!isset($_SESSION['uid']) || empty($_SESSION['uid'])) {
    header('Location: logout.php');
    exit;
}

$uid = $_SESSION['uid'];

// Fetch the user's name using PDO
$stmt = $pdo->prepare("SELECT fullname FROM tblecustomer WHERE id = :id");
$stmt->execute(['id' => $uid]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

$name = $row ? $row['fullname'] : 'Guest';
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
    <h2>Welcome !! <?php echo htmlspecialchars($name); ?>
      <div class="input-box button">
    <a href="logout.php">Logout</a>
  </div>
</h2>
  </div>
</body>
</html>
