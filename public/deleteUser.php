<?php
require_once('../controllers/auth.php');

try {
    $auth_controller = new AuthController();
    $auth_controller->check_user_role([Roles::STAFF]);
} catch (Exception $exception) {
    error_log($exception->getMessage() . "\n" . $exception->getTraceAsString());
    redirect_500();
} catch (Throwable $throwable) {
    error_log($throwable->getMessage() . "\n" .  $throwable->getTraceAsString());
    redirect_500();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST')
    redirect_404();

// Handle delete request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customerID = $_POST['customer_id'] ?? null;
    $customerUserID = $_POST['user_id'] ?? null;

    if ($customerID === null || $customerUserID === null) {
        error_log("NULL customerID or NULL customerUserID");
        $_SESSION[SessionVariables::USER_DELETE_STATUS->value] = "Error with request";
        header("Location: /viewCustomers");
        exit;
    }

    try {
        delete_user($customerUserID);
    } catch (Exception $e) {
        $pdo->rollBack();
        // log error if needed
        $_SESSION[SessionVariables::USER_DELETE_STATUS->value] = "Failed to delete the customer";
    }
    header("Location: /viewCustomers");
}

function delete_user($customerUserID)
{
    // Update role to "Deleted"
    $sql = <<<SQL
            UPDATE "User" SET "Role" = :role WHERE "UserID" = :uid
        SQL;

    DBController::exec_statement($sql, [
        [':role', Roles::DELETED->value, PDO::PARAM_STR],
        [':uid',  $customerUserID, PDO::PARAM_STR]
    ]);

    //anonymize Customer record
    $sql = <<<SQL
          UPDATE "Customer"
          SET "DisplayName" = 'Deleted User',
              "FirstName"   = 'deleted',
              "LastName"    = 'deleted',
              "Email"       = 'deleted',
              "DOB"         = '0001-01-01',
              "ContactNo"   = 0
          WHERE "UserID" = :uid;
          SQL;
    DBController::exec_statement($sql, [[":uid", $customerUserID, PDO::PARAM_STR]]);
    $_SESSION[SessionVariables::USER_DELETE_STATUS->value] = "successfully deleted the customer";
}
