<?php
require_once(__DIR__ . '/../includes/dbconnection.php');
require_once('../controllers/security/csrf.php');
require_once('helpers.php');

class AccountController
{
    // Getting parameters to insert into Account table
    public static function create_account()
    {
        // Getting and santizing submitted parameters
        $userid  = remove_non_alphanum(trim($_POST['userid'] ?? ''));
        $account_type = remove_non_alphanum(trim($_POST['account'] ?? ''));

        // Validating submitted account type
        if (!check_for_account_type($account_type)) {
            echo "Something Went Wrong. Please try again";
            // redirect_with_error("Something Went Wrong. Please try again", "Invalid Character in Account Type", "/dashboard");
        } else {

            $pdo = get_pdo();

            // Get highest account ID
            $stmt = $pdo->prepare('SELECT MAX("AccountID") AS max_id FROM public."Account"');
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $max_account_ID = $row['max_id'];
            $max_account_ID = $max_account_ID + 1;

            // Getting Customer ID using User ID
            $stmt = $pdo->prepare('SELECT "CustomerID" FROM public."Customer" WHERE "UserID" = :userid');
            $stmt->bindParam(':userid', $userid, PDO::PARAM_STR);
            $stmt->execute();
            $customer_id = $stmt->fetch();

            // Check if user exist
            if ($stmt->rowCount() > 0) {
                $customer_id = $customer_id['CustomerID'];
                AccountController::insert_customer_account($max_account_ID, $customer_id, $account_type);
            } else {
                echo "Something Went Wrong. Please try again";
            }
        }
    }

    // Inserting account into account table
    private static function insert_customer_account($max_account_ID, $customer_id, $account_type)
    {
        $pdo = get_pdo();
        $stmt = $pdo->prepare('INSERT INTO public."Account" ("AccountID", "CustomerID", "AccountType", "Balance")
                               VALUES (:AccountID, :CustomerID, :AccountType, :Balance)');
        $success = $stmt->execute([
            'AccountID'    => $max_account_ID,
            'CustomerID'   => $customer_id,
            'AccountType'    => $account_type,
            'Balance' => "$0.00"
        ]);
        // Success or fail message
        if ($stmt->rowCount() > 0) {
            echo $account_type . " Account (". $max_account_ID . ") successfully created";
        } else {
            echo "Something Went Wrong. Please try again";
        }
    }

    // Listing customer's accounts as a table
    public static function list_account($userid)
    {
        // Getting and santizing submitted parameters
        $userid  = remove_non_alphanum($userid);

        $pdo = get_pdo();
        // Getting Customer ID using session User ID
        $stmt = $pdo->prepare('SELECT "CustomerID" FROM public."Customer" WHERE "UserID" = :userid');
        $stmt->bindParam(':userid', $userid, PDO::PARAM_STR);
        $stmt->execute();
        $customer_id = $stmt->fetch();
        $customer_id = $customer_id['CustomerID'];

        // Getting customers account
        $stmt = $pdo->prepare('SELECT * FROM public."Account" WHERE "CustomerID" = :customerid');
        $stmt->bindParam(':customerid', $customer_id, PDO::PARAM_STR);
        $stmt->execute();
        // Printing rows of customer's accounts
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr><td>" . htmlspecialchars($row['AccountID']) . "</td><td>" .
                htmlspecialchars($row['AccountType']) . "</td><td> " .
                htmlspecialchars($row['Balance']) . "</td></tr>";
        }
    }

    // Listing customer's accounts as a table with delete option
    public static function list_account_to_delete($userid)
    {
        // Getting and santizing submitted parameters
        $userid  = remove_non_alphanum($userid);

        $pdo = get_pdo();
        // Getting Customer ID using session User ID
        $stmt = $pdo->prepare('SELECT "CustomerID" FROM public."Customer" WHERE "UserID" = :userid');
        $stmt->bindParam(':userid', $userid, PDO::PARAM_STR);
        $stmt->execute();
        $customer_id = $stmt->fetch();
        $customer_id = $customer_id['CustomerID'];

        // Getting customers account
        $stmt = $pdo->prepare('SELECT * FROM public."Account" WHERE "CustomerID" = :customerid');
        $stmt->bindParam(':customerid', $customer_id, PDO::PARAM_STR);
        $stmt->execute();
        // Printing rows of customer's accounts
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr><td>" . htmlspecialchars($row['AccountID']) . "</td><td>" .
                htmlspecialchars($row['AccountType']) . "</td><td> " .
                htmlspecialchars($row['Balance']) . "</td><td>" .
                '<form method="POST" action="">' . csrf_input() .
                '<input type="hidden" name="accountid" value=' . ($row['AccountID']) . '>' .
                '<input type="hidden" name="userid" value=' . ($userid) . '>' .
                '<input name="delete" type="submit" value="Delete"></form></td></tr>';
        }
    }

    // Delete customer's account
    public static function delete_account()
    {
        // Getting submitted accountid
        $account_id = remove_non_alphanum(trim($_POST['accountid'] ?? ''));
        $userid  = remove_non_alphanum(trim($_POST['userid'] ?? ''));

        $pdo = get_pdo();

        // Getting Customer ID using session User ID
        $stmt = $pdo->prepare('SELECT "CustomerID" FROM public."Customer" WHERE "UserID" = :userid');
        $stmt->bindParam(':userid', $userid, PDO::PARAM_STR);
        $stmt->execute();
        $customer_id = $stmt->fetch();
        $customer_id = $customer_id['CustomerID'];

        // Deleting account from Account table
        $stmt = $pdo->prepare('DELETE FROM public."Account" WHERE "AccountID" = :AccountID AND "CustomerID" = :CustomerID');
        $stmt->execute([
            'AccountID'    => $account_id,
            'CustomerID' => $customer_id
        ]);

        // Message if deletion was successful or failed
        if ($stmt->rowCount() > 0) {
            echo "Account ".$account_id. " successfully deleted";
        } else {
            echo "Something Went Wrong. Please try again";
        }
    }

}

