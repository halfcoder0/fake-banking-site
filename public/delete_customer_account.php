<?php
require_once('../controllers/security/csrf.php');
// require('../controllers/customer_account.php');
require('../controllers/AccountController.php');
require_once(__DIR__ . '/../includes/dbconnection.php');

$nonce = generate_random();
add_csp_header($nonce);

$userid = $_SESSION['UserID'] ?? '';
$role = $_SESSION['Role'] ?? '';

// Check if user is logged in, if not go back to login
if ($userid === '' || $role === '') {
  error_log('no user.');
  header('Location: /');
  exit;
}


// Delete account when customer choose to delete one
if (isset($_POST['delete']) && csrf_verify()) {
  $Account_Controller = new AccountController();
  $Account_Controller->delete_account();
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
      // List customer accounts to delete in a table
      $Account_Controller = new AccountController();
      $Account_Controller->list_account_to_delete($userid);
      // list_account_to_delete($userid); // List customer accounts in a table
      ?>
    </table>

    <a href="/dashboard">Back to Dashboard</a>
    <a href="/logout">Logout</a>

  </div>
</body>

</html>