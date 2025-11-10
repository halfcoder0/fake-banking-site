<?php
require_once('../controllers/security/session_bootstrap.php');
require_once('../controllers/auth.php');

try {
  $auth_controller = new AuthController();
  $auth_controller->check_user_role([Roles::STAFF]);
} catch (Exception $exception) {
  $error = "Error with page";
  error_log($exception->getMessage() . $exception->getTraceAsString());
} catch (Throwable $throwable) {
  $error = "Error with page";
  error_log($throwable->getMessage() . $throwable->getTraceAsString());
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
    } catch (Exception $e) {
        $pdo->rollBack();
        // log error if needed
        echo "<script>alert('Failed to delete user');
        window.location.href = '/viewCustomers';
        </script>";
    }
}
exit;
?>
