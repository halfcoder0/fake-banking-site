<?php
require_once __DIR__ . '/db_controller.php';

class AccountController
{
    public function listAccounts(?string $customerId): array
    {
        DBController::init_db();

        $sql = <<<SQL
          SELECT
            c."DisplayName"        AS customer_name,
            a."AccountID"          AS account_id,
            a."AccountType"        AS account_type,
            (a."Balance"::numeric) AS balance
          FROM public."Account" a
          JOIN public."Customer" c ON c."CustomerID" = a."CustomerID"
          WHERE (:cid IS NULL) OR a."CustomerID" = :cid
          ORDER BY c."DisplayName", a."AccountType", a."AccountID"
        SQL;

        $params = $customerId
          ? [[":cid", $customerId, PDO::PARAM_STR]]
          : [[":cid", null, PDO::PARAM_NULL]];

        $stmt = DBController::exec_statement($sql, $params);
        return $stmt->fetchAll();
    }
}
