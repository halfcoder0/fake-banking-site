<?php
require_once __DIR__ . '/db_controller.php';

class TransactionController
{
    public function listTransactions(?string $customerId): array
    {
        DBController::init_db();

        $sql = <<<SQL
          SELECT
            t."TransactionID"                                   AS transaction_id,
            to_char(t."TransactionDate", 'YYYY-MM-DD HH24:MI')  AS transaction_date,
            t."ReferenceNumber"                                 AS reference_number,
            (t."Amount"::numeric)                               AS amount,
            fc."DisplayName"                                    AS from_customer,
            fa."AccountType"                                    AS from_account_type,
            tc."DisplayName"                                    AS to_customer,
            ta."AccountType"                                    AS to_account_type
          FROM public."Transaction" t
          JOIN public."Account"  fa ON fa."AccountID"  = t."FromAccount"
          JOIN public."Customer" fc ON fc."CustomerID" = fa."CustomerID"
          JOIN public."Account"  ta ON ta."AccountID"  = t."ToAccount"
          JOIN public."Customer" tc ON tc."CustomerID" = ta."CustomerID"
          WHERE (:cid IS NULL)
             OR fc."CustomerID" = :cid
             OR tc."CustomerID" = :cid
          ORDER BY t."TransactionDate" DESC, t."ReferenceNumber" DESC
          LIMIT 1000
        SQL;

        $params = $customerId
          ? [[":cid", $customerId, PDO::PARAM_STR]]
          : [[":cid", null, PDO::PARAM_NULL]];

        $stmt = DBController::exec_statement($sql, $params);
        return $stmt->fetchAll();
    }
}
