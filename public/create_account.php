<?php 
require("../controllers/security/session_bootstrap.php");
require('../controllers/accounts.php');
require_once(__DIR__ . '/../includes/dbconnection.php');
$userid = $_SESSION['UserID'] ?? '';
$role = $_SESSION['Role'] ?? '';

// if (isset($_POST[$userid]) && isset($_POST['account']) ) {
//     create_account($_POST);
//     exit;
// }

// Check if user is logged in, if not go back to login
if ($userid === '' || $role === ''){
  error_log('no user.');
  header('Location: /');
  exit;
}

if (isset($_POST['submit'])) {
    
    $account_type = $_POST['account'];
      $pdo = get_pdo();
    // Check if email or contact already exists
    $stmt = $pdo->prepare('SELECT MAX("AccountID") AS max_id FROM public."Account"');
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $max_account_ID = $row['max_id'];  
    $max_account_ID = $max_account_ID + 1;

    // if ($max_account_ID > 0) {
    //   echo "<script>alert('This email or Contact Number already associated with another account');</script>";
    // } else {
    //   echo "<script>alert('This email or Contact Number already associated with another account');</script>";
    // }
    error_log("Max ID: " . print_r($row['max_id'], true));
    error_log("Account Type: " . $account_type);

    // Getting Customer ID using User ID
    $stmt = $pdo->prepare('SELECT "CustomerID" FROM public."Customer" WHERE "UserID" = :userid');
    $stmt->bindParam(':userid', $userid, PDO::PARAM_STR);
    $stmt->execute();
    $customer_id = $stmt->fetch();
    $customer_id = $customer_id['CustomerID'];
    
    // Inserting new account into Account table
    $stmt = $pdo->prepare('INSERT INTO public."Account" ("AccountID", "CustomerID", "AccountType", "Balance")
                               VALUES (:AccountID, :CustomerID, :AccountType, :Balance)');
        $success = $stmt->execute([
            'AccountID'    => $max_account_ID,
            'CustomerID'   => $customer_id,
            'AccountType'    => $account_type,
            'Balance' => "$500.00"
        ]);
        // Success or fail message
        if ($success) {
            echo "<script>alert('Account successfully created');</script>";
            echo "<script>window.location.href ='create_account.php'</script>";
        } else {
            echo "<script>alert('Something Went Wrong. Please try again');</script>";
            echo "<script>window.location.href ='create_account.php'</script>";
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
    <h2>Welcome !! <?php echo htmlspecialchars($role . '-' . $userid); 
    ?>
    
 <table border="1">
    <tr>
      <th>Account ID</th>
      <th>Account Type</th>
      <th>Balance</th>
    </tr>
    <?php
     
    // require("../controllers/security/session_bootstrap.php");
// require('../controllers/accounts.php');
// require_once(__DIR__ . '/../includes/dbconnection.php');


$userid = $_SESSION['UserID'] ?? '';
$role = $_SESSION['Role'] ?? '';

    // $pdo = get_pdo();
    //  error_log("userid is ". $userid);
    // error_log("pdo is ". $pdo);
    // // Getting Customer ID using User ID
    // $stmt = $pdo->prepare('SELECT "CustomerID" FROM public."Customer" WHERE "UserID" = :userid');
    // $stmt->bindParam(':userid', $userid, PDO::PARAM_STR);
    // $stmt->execute();
    // $customer_id = $stmt->fetch();
    // $customer_id = $customer_id['CustomerID'];

    // // Check if email or contact already exists
    // $stmt = $pdo->prepare('SELECT * FROM public."Account" WHERE "CustomerID" = :customerid');
    // $stmt->bindParam(':customerid', $customer_id, PDO::PARAM_STR);
    // $stmt->execute();
    // // $row = $stmt->fetch(PDO::FETCH_ASSOC);
    // while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    //     echo "<tr><td>" . htmlspecialchars($row[AccountID]) . "</td><td>John Tan</td><td>john.tan@example.com</td></tr>";
    // }
    ?>
    <tr><td>1</td><td>John Tan</td><td>john.tan@example.com</td></tr>
   
  </table>


      <div class="input-box button">
        
    <form method="POST" action="">
    <label>Choose account type:</label>
    <select id="account" name="account">
      <option name="account">Checking</option>
      <option name="account">Savings</option>
      <option name="account">Investment</option>
    </select>
    <br>
    <input name="submit" type="submit" value="Submit">
  </form>
<a href="/dashboard">Back to Dashboard</a>
    <a href="/logout">Logout</a>
  </div>
</h2>
  </div>
</body>
</html>
