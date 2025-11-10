<?php
class BalanceController
{
  public function listAccounts(?string $customerId): array
  {

    $sql = <<<SQL
      SELECT
        c."DisplayName"        AS customer_name,
        a."AccountID"          AS account_id,
        a."AccountType"        AS account_type,
        (a."Balance"::numeric) AS balance
      FROM public."Account" a
      JOIN public."Customer" c ON c."CustomerID" = a."CustomerID"
    SQL;

    $params = [];
    if ($customerId) {
      $sql .= ' WHERE a."CustomerID" = :cid';
      $params[] = [':cid', $customerId, PDO::PARAM_STR];
    }

    $sql .= ' ORDER BY c."DisplayName", a."AccountType", a."AccountID"';

    $stmt = DBController::exec_statement($sql, $params);
    return $stmt->fetchAll();
  }

}
