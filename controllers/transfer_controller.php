<?php
enum TransferErrorMsg: string
{
    case INVALID_REQUEST_MSG = "Invalid request";
    case NO_ACCOUNT_FOUND = "No accounts found";
    case INVALID_INPUT = "Please enter valid account numbers/amount.";
    case ERROR_WITH_REQUEST = "Error processing request";
    case INSUFFICENT_BALANCE = "Insufficient balance in account";
    case SAME_TO_FROM_ACC = "Destination account cannot be the same as source account.";
    case INVALID_AMOUNT = "Invalid amount";
    case INVALID_ACCOUNT_NUM = "Invalid account number";
    case INVALID_CARD_INFO = "Invalid credit card info";
}

enum TransactionType: string
{
    case TRANSFER = "TRANSFER";
    case DEPOSIT = "DEPOSIT";
    case WITHDRAW = "WITHDRAW";
}

class TransferController
{
    private const TRANSFER_SUCCESS_MSG = "Funds have been transferred!";
    private const DEPOSIT_SUCCESS_MSG = "Funds have been added!";
    private const WITHDRAW_SUCCESS_MSG = "Funds have been withdrawn!";
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

        $from_acc = trim($_POST["FROM_account"]);
        $to_acc = trim($_POST["TO_account"]);
        $amount = trim($_POST["Amount"]);

        TransferController::validate_transfer_details($own_accounts, $from_acc, $to_acc, $amount);

        TransferController::attempt_transfer_funds($from_acc, $to_acc, $amount);
    }

    /**
     * Validate & Convert input \
     * Via Pass By Reference
     */
    private function validate_transfer_details($own_accounts, &$from_acc, &$to_acc, &$amount)
    {
        if (!is_valid_money($amount))
            redirect_with_error(TransferErrorMsg::INVALID_AMOUNT->value, '', Routes::TRANSFER_PAGE->value);

        $options = ['options' => ['min_range' => 1]];
        $from_acc = filter_var($from_acc, FILTER_VALIDATE_INT, $options);
        $to_acc = filter_var($to_acc, FILTER_VALIDATE_INT, $options);
        $amount = filter_var($amount, FILTER_VALIDATE_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

        if ($amount === false)
            redirect_with_error(TransferErrorMsg::INVALID_AMOUNT->value, 'Transfer: Amount conversion failed', Routes::TRANSFER_PAGE->value);
        if ($amount <= 0 || $amount >= 999_999_999)
            redirect_with_error(TransferErrorMsg::INVALID_AMOUNT->value, 'Transfer: Negative amt or amt > 999 million', Routes::TRANSFER_PAGE->value);
        if ($from_acc === false || $to_acc === false)
            redirect_with_error(TransferErrorMsg::INVALID_INPUT->value, 'Transfer: Invalid acc num input / negative value', Routes::TRANSFER_PAGE->value);
        if (!in_array($from_acc, $own_accounts))
            redirect_with_error(TransferErrorMsg::INVALID_INPUT->value, 'Transfer: account not found', Routes::TRANSFER_PAGE->value);
        if ($from_acc === $to_acc)
            redirect_with_error(TransferErrorMsg::SAME_TO_FROM_ACC->value, '', Routes::TRANSFER_PAGE->value);

        $amount = round($amount, 2);
    }

    /**
     * Transfer funds
     */
    private function attempt_transfer_funds($from_acc, $to_acc, $amount)
    {
        $from_balance = TransferController::get_balance($from_acc);

        if ($from_balance === false)
            redirect_with_error(TransferErrorMsg::ERROR_WITH_REQUEST->value, "Transfer: Balance conversion FROM_account failed.", Routes::TRANSFER_PAGE->value);
        if ($amount > $from_balance)
            redirect_with_error(TransferErrorMsg::INSUFFICENT_BALANCE->value, "Transfer: Transfer amt > from_balance", Routes::TRANSFER_PAGE->value);
        if ($from_acc === $to_acc)
            redirect_with_error(TransferErrorMsg::SAME_TO_FROM_ACC->value, '', Routes::TRANSFER_PAGE->value);

        $success = TransferController::transfer_fund($from_acc, $to_acc, $amount);
        if ($success === false)
            redirect_with_error(TransferErrorMsg::ERROR_WITH_REQUEST->value, "Transfer: Error updating acc balances", Routes::TRANSFER_PAGE->value);

        $_SESSION[SessionVariables::SUCCESS->value] = TransferController::TRANSFER_SUCCESS_MSG;
    }

    private function get_balance($from_acc)
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

        return $from_balance;
    }

    /**
     * Update Balance for both from & to accounts \
     * Rollback changes & RET error if failure in any steps of transaction
     */
    private function transfer_fund($from_acc, $to_acc, $amount): bool
    {
        DBController::$pdo->beginTransaction();

        TransferController::lock_account_balance([$from_acc, $to_acc]);
        TransferController::decrease_acc_balance($from_acc, $amount);
        TransferController::increase_acc_balance($to_acc, $amount);
        TransferController::insert_transaction_history($from_acc, $to_acc, $amount);

        return DBController::$pdo->commit();
    }

    /**
     * Perform row lock on Source & Destination accounts \
     * to prevent concurrent update operations
     */
    private function lock_account_balance($accounts = [])
    {
        $select_query = <<<SQL
            SELECT *
            FROM public."Account"
            WHERE "AccountID" in (:from_account,:to_account)
            FOR UPDATE;
        SQL;
        $params = [
            [":from_account", $accounts[0], PDO::PARAM_INT],
            [":to_account", $accounts[1], PDO::PARAM_INT]
        ];

        $result = DBController::exec_statement($select_query, $params);

        if ($result === false)
            redirect_with_error(TransferErrorMsg::ERROR_WITH_REQUEST->value, "Transfer: TO/FROM Account lock failed", Routes::TRANSFER_PAGE->value);
    }

    /**
     * Update Source Account
     */
    private function decrease_acc_balance($from_acc, $amount)
    {
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

        if ($stmt->rowCount() !== 1) {
            error_log("Update from account failed");
            DBController::$pdo->rollBack();
            return false;
        }
    }

    /**
     * Update Destination Account
     */
    private function increase_acc_balance($account_id, $amount)
    {
        $update_query = <<<SQL
            UPDATE public."Account"
            SET "Balance" = "Balance" + (CAST(:amount AS numeric)::money)
            WHERE "AccountID" = :account_id
              AND ("Balance" + (CAST(:amount AS numeric)::money)) >= money(0.00)
        SQL;
        $params = [
            [":account_id", $account_id, PDO::PARAM_INT],
            [":amount", $amount, PDO::PARAM_STR]
        ];
        $stmt = DBController::exec_statement($update_query, $params);

        if ($stmt->rowCount() !== 1) {
            error_log("Update to account failed");
            DBController::$pdo->rollBack();
            return false;
        }
    }

    /**
     * Insert Transaction History
     */
    private function insert_transaction_history($from_acc, $to_acc, $amount, string $type = TransactionType::TRANSFER->value)
    {
        $ref_num = random_int(0, PHP_INT_MAX);      // Generate secure positive 64-bit INT  
        $now = new DateTime();
        $curr_date = $now->format('Y-m-d H:i:s');

        DBController::exec_statement('CREATE EXTENSION IF NOT EXISTS "uuid-ossp";');

        $insert_query = <<<SQL
            INSERT INTO public."Transaction"(
                "TransactionID", "ToAccount", "FromAccount", "ReferenceNumber", "TransactionDate", "Amount", "Type")
                VALUES (uuid_generate_v4(), :to_account, :from_account, :ref_num, :curr_date, :amount, :transaction_type);
        SQL;
        $params = [
            [":to_account", $to_acc, PDO::PARAM_INT],
            [":from_account", $from_acc, PDO::PARAM_INT],
            [":amount", $amount, PDO::PARAM_STR],
            [":ref_num", $ref_num, PDO::PARAM_INT],
            [":curr_date", $curr_date, PDO::PARAM_STR],
            [":transaction_type", $type, PDO::PARAM_STR]
        ];
        $stmt = DBController::exec_statement($insert_query, $params);

        if ($stmt->rowCount() !== 1) {
            error_log("Update to account failed");
            DBController::$pdo->rollBack();
            return false;
        }
    }

    /**
     * Process deposit request
     */
    public function process_deposit_request($own_accounts)
    {
        $account_id = trim($_POST["TO_account"]);
        $amount = trim($_POST["Amount"]);
        $card_number = trim($_POST["Card_Number"]);
        $card_cvv = trim($_POST["Card_CVV"]);
        $expiry_month = trim($_POST["Card_Expiry_Month"]);
        $expiry_yr = trim($_POST["Card_Expiry_Year"]);

        $validation_error = TransferController::validate_deposit_details($own_accounts, $account_id, $amount, $card_number, $card_cvv, $expiry_month, $expiry_yr);
        if ($validation_error[0] == false)
            redirect_with_error($validation_error[1], $validation_error[2], Routes::TRANSFER_PAGE->value, SessionVariables::DEPOSIT_ERROR->value);

        $success = TransferController::deposit_money($account_id, $amount);
        if ($success === false)
            redirect_with_error(TransferErrorMsg::ERROR_WITH_REQUEST->value, $validation_error[2], Routes::TRANSFER_PAGE->value, SessionVariables::DEPOSIT_ERROR->value);

        $_SESSION[SessionVariables::DEPOSIT_SUCCESS->value] = TransferController::DEPOSIT_SUCCESS_MSG;
    }

    /**
     * Validate deposit details \
     * Pass by Ref: account_id, amount
     */
    private function validate_deposit_details($own_accounts, &$account_id, &$amount, $card_number, $card_cvv, $expiry_month, $expiry_yr)
    {
        if (!is_valid_money($amount))
            return [false, TransferErrorMsg::INVALID_INPUT->value, 'Deposit: not valid money'];

        $acc_options = ['options' => ['min_range' => 1]];
        $cvv_options = ['options' => ['min_range' => 100, 'max_range' => 999]];
        $month_options = ['options' => ['min_range' => 1, 'max_range' => 12]];
        $yr_options = ['options' => ['min_range' => 25, 'max_range' => 35]];

        $account_id = filter_var($account_id, FILTER_VALIDATE_INT, $acc_options);
        $amount = filter_var($amount, FILTER_VALIDATE_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $card_number = TransferController::is_valid_card_num($card_number);
        $card_cvv = filter_var($card_cvv, FILTER_VALIDATE_INT, $cvv_options);
        $expiry_month = filter_var($expiry_month, FILTER_VALIDATE_INT, $month_options);
        $expiry_yr = filter_var($expiry_yr, FILTER_VALIDATE_INT, $yr_options);

        if ($card_number === false || $card_cvv === false || $expiry_month === false || $expiry_yr === false)
            return [false, TransferErrorMsg::INVALID_CARD_INFO->value, 'Deposit: Invalid credit card info'];
        if ($account_id === false)
            return [false, TransferErrorMsg::INVALID_ACCOUNT_NUM->value, 'Deposit: Invalid Account num'];
        if ($amount === false)
            return [false, TransferErrorMsg::INVALID_AMOUNT->value, 'Deposit: Invalid amt'];
        if ($amount < 0)
            return [false, TransferErrorMsg::INVALID_AMOUNT->value, 'Deposit: Invalid amt'];
        if (!in_array($account_id, $own_accounts))
            return [false, TransferErrorMsg::INVALID_ACCOUNT_NUM->value, 'Deposit: Invalid input - not an account linked to user'];

        return [true, '', ''];
    }

    /**
     * Credit card validation
     */
    private function is_valid_card_num($card_number)
    {
        // Ensure card number length is 13–19 digits (ISO/IEC 7812)
        if (!preg_match('/^\d{13,19}$/', $card_number)) return false;

        return TransferController::luhn_algorithm($card_number);
    }

    /**
     * luhn algorithm
     */
    private function luhn_algorithm($card_number): bool
    {
        $sum = 0;
        $shouldDouble = false;

        // Process digits from right to left
        for ($i = strlen($card_number) - 1; $i >= 0; $i--) {
            $digit = (int)$card_number[$i];

            if ($shouldDouble) {
                $digit *= 2;
                if ($digit > 9) {
                    $digit -= 9;
                }
            }

            $sum += $digit;
            $shouldDouble = !$shouldDouble;
        }

        return $sum % 10 === 0;
    }

    /**
     * Deposit money into account
     */
    private function deposit_money($account_id, $amount)
    {
        DBController::$pdo->beginTransaction();

        TransferController::lock_account_balance([$account_id, 0]);
        TransferController::increase_acc_balance($account_id, $amount);
        TransferController::insert_transaction_history(0, $account_id, $amount, TransactionType::DEPOSIT->value);

        return DBController::$pdo->commit();
    }

    /**
     * Process withdraw
     */
    public function process_withdraw_request($own_accounts)
    {
        $account_id = $_POST["FROM_account"];
        $amount = trim($_POST["Amount"]);

        $validation_error = TransferController::validate_withdraw_details($own_accounts, $account_id, $amount);
        if ($validation_error[0] == false)
            redirect_with_error($validation_error[1], $validation_error[2], Routes::TRANSFER_PAGE->value, SessionVariables::WITHDRAW_ERROR->value);

        // Check sufficent balance
        $from_balance = TransferController::get_balance($account_id);
        if ($from_balance === false)
            redirect_with_error(TransferErrorMsg::ERROR_WITH_REQUEST->value, "Transfer: Balance conversion FROM_account failed.", Routes::TRANSFER_PAGE->value, SessionVariables::WITHDRAW_ERROR->value);
        if ($amount > $from_balance)
            redirect_with_error(TransferErrorMsg::INSUFFICENT_BALANCE->value, "Transfer: Transfer amt > from_balance", Routes::TRANSFER_PAGE->value, SessionVariables::WITHDRAW_ERROR->value);

        $success = TransferController::withdraw_money($account_id, $amount);
        if ($success === false)
            redirect_with_error(TransferErrorMsg::ERROR_WITH_REQUEST->value, $validation_error[2], Routes::TRANSFER_PAGE->value, SessionVariables::WITHDRAW_ERROR->value);

        $_SESSION[SessionVariables::WITHDRAW_SUCCESS->value] = TransferController::WITHDRAW_SUCCESS_MSG;
    }       

    /**
     * Validate Withdraw details
     */
    private function validate_withdraw_details($own_accounts, &$account_id, &$amount)
    {
        if (!is_valid_money($amount))
            return [false, TransferErrorMsg::INVALID_INPUT->value, 'Deposit: not valid money'];

        $acc_options = ['options' => ['min_range' => 1]];
        $account_id = filter_var($account_id, FILTER_VALIDATE_INT, $acc_options);

        if ($account_id === false)
            return [false, TransferErrorMsg::INVALID_ACCOUNT_NUM->value, 'Withdraw: Invalid Account num'];
        if ($amount === false)
            return [false, TransferErrorMsg::INVALID_AMOUNT->value, 'Withdraw: Invalid amt'];
        if ($amount < 0)
            return [false, TransferErrorMsg::INVALID_AMOUNT->value, 'Withdraw: Invalid amt'];
        if (!in_array($account_id, $own_accounts))
            return [false, TransferErrorMsg::INVALID_ACCOUNT_NUM->value, 'Deposit: Invalid input - not an account linked to user'];

        return [true, '', ''];
    }

    /**
     * Withdraw money from account
     */
    private function withdraw_money($account_id, $amount)
    {
        DBController::$pdo->beginTransaction();

        TransferController::lock_account_balance([$account_id, 0]);
        TransferController::decrease_acc_balance($account_id, $amount);
        TransferController::insert_transaction_history($account_id, 0, $amount, TransactionType::WITHDRAW->value);

        return DBController::$pdo->commit();
    }
}
