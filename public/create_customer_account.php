<?php
require_once('../controllers/security/csrf.php');
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

// Create account when customer submit account type to be created
if (isset($_POST['submit']) && isset($_POST['account']) && csrf_verify()) {
  $Account_Controller = new AccountController();
  $Account_Controller->create_account();
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
    <h2>Customer Account Creation
    </h2>
    <table border="1">
      <tr>
        <th>Account ID</th>
        <th>Account Type</th>
        <th>Balance</th>
      </tr>
      <?php
      // List customer accounts in a table
      $Account_Controller = new AccountController();
      $Account_Controller->list_account($userid);

      // list_account($userid); 
      ?>

    </table>

    <div class="input-box button">

      <!-- Form submission for new account creation -->
      <form method="POST" action="">
        <?= csrf_input() ?>
        <label>Choose account type:</label>
        <select id="account" name="account">
          <option name="account">aaaa</option>
          <option name="account">Checking</option>
          <option name="account">Savings</option>
          <option name="account">Investment</option>
          <input type="hidden" name="userid" value="<?php echo htmlspecialchars($userid); ?>">
        </select>
        <br>
        <input name="submit" type="submit" value="Submit">
      </form>

      <a href="/dashboard">Back to Dashboard</a>
      <a href="/logout">Logout</a>
    </div>

  </div>
</body>

</html>