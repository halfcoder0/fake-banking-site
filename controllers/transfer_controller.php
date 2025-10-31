<?php
enum TransferErrorMsg: string
{
    case INVALID_REQUEST_MSG = "Invalid transfer request.";
    case NO_ACCOUNT_FOUND = "No accounts found.";
    case INVALID_INPUT = "Invalid input";
    case ERROR_WITH_REQUEST = "Error processing transfer request";
    case INSUFFICENT_BALANCE = "Insufficient balance in account";
    case SAME_TO_FROM_ACC = "Destination account cannot be the same as source account.";
}

class TransferController
{
    private const SUCCESS_MSG = "Funds have been transferred!";
    /**
     * Get accounts owned by user
     */
    public function get_user_accounts()
    {
        $accounts = [];
        $query = <<<SQL
            SELECT "AccountID"
                FROM public."Account"
                WHERE "CustomerID" = :custID;
        SQL;

        $params = array([":custID", $_SESSION[SessionVariables::CUSTOMER_ID->value], PDO::PARAM_STR]);
        $result = DBController::exec_statement($query, $params)->fetchAll();

        if ($result) $accounts = array_column($result, "AccountID");
        if (count($accounts) === 0) $_SESSION[SessionVariables::GENERIC_ERROR->value] = TransferErrorMsg::NO_ACCOUNT_FOUND->value;

        return $accounts;
    }

    /**
     * Transfer between accounts
     */
    public function process_funds_transfer($own_accounts)
    {
        if (count($own_accounts) === 0) {
            $_SESSION['error'] = TransferErrorMsg::NO_ACCOUNT_FOUND->value;
            return;
        }

        $from_acc = $_POST["FROM_account"];
        $to_acc = $_POST["TO_account"];
        $amount = $_POST["Amount"];

        if (TransferController::valid_inputs($own_accounts, $from_acc, $to_acc, $amount) === false)
            redirect_with_error(TransferErrorMsg::INVALID_INPUT->value, '', Routes::TRANSFER_PAGE->value);

        TransferController::santize_input($from_acc, $to_acc, $amount);

        TransferController::attempt_transfer_funds($from_acc, $to_acc, $amount);
    }

    /**
     * Validate user inputs 
     */
    private function valid_inputs($own_accounts, $from_acc, $to_acc, $amount): bool
    {
        if (!valid_positive_int($from_acc)) return false;
        if (!valid_positive_int($to_acc)) return false;
        if (!is_valid_money($amount)) return false;
        if (!in_array($from_acc, $own_accounts)) return false;
        return true;
    }

    /**
     * Sanitize input \
     * Via Pass By Reference
     */
    private function santize_input(&$from_acc, &$to_acc, &$amount)
    {
        $from_acc = filter_var($from_acc, FILTER_VALIDATE_INT);
        $to_acc = filter_var($to_acc, FILTER_VALIDATE_INT);
        $amount = filter_var($amount, FILTER_VALIDATE_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

        if ($from_acc === false || $to_acc === false || $amount === false)
            redirect_with_error(TransferErrorMsg::ERROR_WITH_REQUEST->value, "Transfer: Filter failed after sanitization", Routes::TRANSFER_PAGE->value);

        if ($from_acc < 0 || $to_acc < 0 || $amount < 0)
            redirect_with_error(TransferErrorMsg::ERROR_WITH_REQUEST->value, "Transfer: Filter returned negative value after sanitization", Routes::TRANSFER_PAGE->value);

        $amount = round($amount, 2);
    }

    /**
     * Transfer funds
     */
    private function attempt_transfer_funds($from_acc, $to_acc, $amount)
    {
        $get_balance_query = <<<SQL
            SELECT "Balance"
            FROM public."Account"
            WHERE "AccountID" = :fromAccount
            LIMIT 1
        SQL;

        $params = [[":fromAccount", $from_acc, PDO::PARAM_INT]];
        $result = DBController::exec_statement($get_balance_query, $params)->fetch();

        if ($result === false || count($result) === 0)
            redirect_with_error(TransferErrorMsg::ERROR_WITH_REQUEST->value, "Transfer: Failed to get balance FROM_account", Routes::TRANSFER_PAGE->value);

        $from_balance = filter_var($result["Balance"], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

        if ($from_balance === false)
            redirect_with_error(TransferErrorMsg::ERROR_WITH_REQUEST->value, "Transfer: Balance conversion FROM_account failed.", Routes::TRANSFER_PAGE->value);
        if ($amount > $from_balance)
            redirect_with_error(TransferErrorMsg::INSUFFICENT_BALANCE->value, "Transfer: Transfer amt > from_balance", Routes::TRANSFER_PAGE->value);
        if ($from_acc === $to_acc)
            redirect_with_error(TransferErrorMsg::SAME_TO_FROM_ACC->value, '', Routes::TRANSFER_PAGE->value);

        $success = TransferController::transfer_fund($from_acc, $to_acc, $amount);
        if ($success === false)
            redirect_with_error(TransferErrorMsg::ERROR_WITH_REQUEST->value, "Transfer: Error updating acc balances", Routes::TRANSFER_PAGE->value);

        $_SESSION[SessionVariables::SUCCESS->value] = TransferController::SUCCESS_MSG;
    }

    /**
     * Update Balance for both from & to accounts
     */
    private function transfer_fund($from_acc, $to_acc, $amount): bool
    {
        // Start Execution
        DBController::$pdo->beginTransaction();

        $update_query = <<<SQL
            UPDATE public."Account"
            SET "Balance" = "Balance" - (CAST(:amount AS numeric)::money)
            WHERE "AccountID" = :from_account
              AND ("Balance" - (CAST(:amount AS numeric)::money)) >= money(0.00)
        SQL;
        $params = [
            [":from_account", $from_acc, PDO::PARAM_INT],
            [":amount", $amount, PDO::PARAM_STR]
        ];
        $stmt = DBController::exec_statement($update_query, $params);

        if ($stmt->rowCount() === 0) {
            error_log("Update from account failed");
            DBController::$pdo->rollBack();
            return false;
        }

        $update_query = <<<SQL
            UPDATE public."Account"
            SET "Balance" = "Balance" + (CAST(:amount AS numeric)::money)
            WHERE "AccountID" = :to_account
              AND ("Balance" + (CAST(:amount AS numeric)::money)) >= money(0.00)
        SQL;
        $params = [
            [":to_account", $to_acc, PDO::PARAM_INT],
            [":amount", $amount, PDO::PARAM_STR]
        ];
        $stmt = DBController::exec_statement($update_query, $params);

        if ($stmt->rowCount() === 0) {
            error_log("Update to account failed");
            DBController::$pdo->rollBack();
            return false;
        }

        // Commit to execution
        DBController::$pdo->commit();
        return true;
    }
}
