<?php
require_once('../controllers/security/session_bootstrap.php');
$staffid = $_SESSION['StaffID'] ?? null;
$role = $_SESSION['Role'] ?? null;
if ($staffid === '') {
  header('Location: /login');
  exit;
}

// Handle delete request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customerID = $_POST['customer_id'] ?? null;
    $customerUserID = $_POST['user_id'] ?? null;
    try {
          // Update role to "Deleted"
          $sql = 'UPDATE "User" SET "Role" = :role WHERE "UserID" = :uid';
          DBController::exec_statement($sql, [
              [':role', 'Deleted', PDO::PARAM_STR],
              [':uid',  $customerUserID, PDO::PARAM_STR]
          ]);
          //anonymize Customer record
          $sql = <<<SQL
          UPDATE "Customer"
          SET "DisplayName" = 'Deleted User',
              "FirstName"   = '[deleted]',
              "LastName"    = '[deleted]',
              "Email"       = '[deleted]',
              "DOB"         = '0001-01-01',
              "ContactNo"   = 0
          WHERE "UserID" = :uid;
          SQL;
          DBController::exec_statement($sql, [[":uid", $customerUserID, PDO::PARAM_STR]]);
          echo "<script>alert('successfully delete the customer);
          window.location.href = '/viewCustomers';
          </script>";
        exit;
    } catch (Exception $e) {
        $pdo->rollBack();
        // log error if needed
        echo "<script>alert('Failed to delete user');
        window.location.href = '/viewCustomers';
        </script>";
        exit;
    }

}





?>
