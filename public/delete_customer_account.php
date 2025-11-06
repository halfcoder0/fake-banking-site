<?php
require("../controllers/security/session_bootstrap.php");
require('../controllers/customer_account.php');
require_once(__DIR__ . '/../includes/dbconnection.php');
$userid = $_SESSION['UserID'] ?? '';
$role = $_SESSION['Role'] ?? '';

// Check if user is logged in, if not go back to login
if ($userid === '' || $role === '') {
  error_log('no user.');
  header('Location: /');
  exit;
}

// Create account when customer submit account type to be created
if (isset($_POST['submit']) && isset($_POST['account'])) {
  create_account($_POST);
}

// Delete account when customer choose to delete one
if (isset($_POST['delete'])) {
  delete_account($_POST);
}

?>

<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>NexaBank</title>
  <link rel="stylesheet" href="./css/style.css">
</head>

<body>
  <div class="wrapper">
    <h2>Customer Account Deletion 
                    
</h2>
      <table border="1">
        <tr>
          <th>Account ID</th>
          <th>Account Type</th>
          <th>Balance</th>
          <th></th>
        </tr>
        <?php
        list_account_to_delete($userid); // List customer accounts in a table
        ?>
      </table>

        <a href="/dashboard">Back to Dashboard</a>
        <a href="/logout">Logout</a>
    
  </div>
</body>

</html>