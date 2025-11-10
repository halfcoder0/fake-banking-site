<?php

class TransactionController
{

  public function listTransactions(?string $customerId): array
  {
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
      LEFT JOIN public."Account"  fa ON fa."AccountID"  = t."FromAccount"
      LEFT JOIN public."Customer" fc ON fc."CustomerID" = fa."CustomerID"
      LEFT JOIN public."Account"  ta ON ta."AccountID"  = t."ToAccount"
      LEFT JOIN public."Customer" tc ON tc."CustomerID" = ta."CustomerID"
    SQL;

    $params = [];
    if ($customerId) {
      $sql .= ' WHERE fc."CustomerID" = :cid OR tc."CustomerID" = :cid';
      $params[] = [':cid', $customerId, PDO::PARAM_STR];
    }

    $sql .= ' ORDER BY t."TransactionDate" DESC, t."ReferenceNumber" DESC LIMIT 1000';

    $stmt = DBController::exec_statement($sql, $params);
    return $stmt->fetchAll();
  }

}
