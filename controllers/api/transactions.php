<?php
header('Content-Type: application/json');
ini_set('display_errors', value: 1); error_reporting(E_ALL);

require_once __DIR__ . '/../../includes/dbconnection.php';

// try {
//   $sql = <<<SQL
//     SELECT
//       t."TransactionID" AS transaction_id,
//       to_char(t."TransactionDate", 'YYYY-MM-DD') AS transaction_date,
//       t."ReferenceNumber" AS reference_number,

//       /* IMPORTANT: return a numeric, not formatted text */
//       (t."Amount"::numeric) AS amount,

//       fc."DisplayName" AS from_customer,
//       fa."AccountType" AS from_account_type,
//       tc."DisplayName" AS to_customer,
//       ta."AccountType" AS to_account_type
//     FROM public."Transaction" t
//     JOIN public."Account"  fa ON fa."AccountID"  = t."FromAccount"
//     JOIN public."Customer" fc ON fc."CustomerID" = fa."CustomerID"
//     JOIN public."Account"  ta ON ta."AccountID"  = t."ToAccount"
//     JOIN public."Customer" tc ON tc."CustomerID" = ta."CustomerID"
//     ORDER BY t."TransactionDate" DESC, t."ReferenceNumber" DESC;
//   SQL;

//   $rows = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
//   echo json_encode($rows, JSON_UNESCAPED_UNICODE);
// } catch (Throwable $e) {
//   http_response_code(500);
//   echo json_encode(['error' => $e->getMessage()]);
// }
echo json_encode(['error'=> 'yes']);

?>