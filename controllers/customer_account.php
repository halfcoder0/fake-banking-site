<?php
require_once(__DIR__ . '/../includes/dbconnection.php');
require_once('helpers.php');

// Create new customer account
function create_account($account_details)
{
    // if (!csrf_verify()) handle_error('CSRF ERROR');

    // Getting submitted parameters
    $account_type = trim($account_details['account'] ?? '');
    $userid  = trim($account_details['userid'] ?? '');

    // if (check_for_non_alphanum($username)) handle_error('Only alphabets & numbers allowed for username.');

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
            echo "<script>window.location.href ='create_customer_account'</script>";
        } else {
            echo "<script>alert('Something Went Wrong. Please try again');</script>";
            echo "<script>window.location.href ='create_customer_account'</script>";
        }

    // redirect_user($role);
}

// Listing customer's accounts as a table
function list_account($userid)
{
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
        echo "<tr><td>" . htmlspecialchars($row['AccountID']) . "</td><td>". 
        htmlspecialchars($row['AccountType']) . "</td><td> " .
        htmlspecialchars($row['Balance']) . "</td><td>" . 
        '<form method="POST" action="">' . 
        '<input type="hidden" name="accountid" value='. ($row['AccountID']) .'>' .
        '<input name="delete" type="submit" value="Delete"></form></td></tr>';
  
    }
}

// Delete customer's account
function delete_account($account_details)
{
    // Getting submitted accountid
    $account_id = trim($account_details['accountid'] ?? '');

    // Deleting account from Account table
    $pdo = get_pdo();
    $stmt = $pdo->prepare('DELETE FROM public."Account" WHERE "AccountID" = :AccountID');
    $success = $stmt->execute([
        'AccountID'    => $account_id,
    ]);
    // Success or fail message
    if ($success) {
        echo "<script>alert('Account successfully deleted');</script>";
        echo "<script>window.location.href ='create_customer_account'</script>";
    } else {
        echo "<script>alert('Something Went Wrong. Please try again');</script>";
        echo "<script>window.location.href ='create_customer_account'</script>";
    }
}
