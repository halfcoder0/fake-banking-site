<?php
header('Content-Type: application/json');
ini_set('display_errors', value: 1); //to remove
error_reporting(E_ALL); //to remove


require_once __DIR__ . '/../../includes/dbconnection.php';
// require_once __DIR__ . '/../../auth.php';  // enable when login is ready
// require_login();

try {

    // When sessions are wired:
    // $me = $pdo->prepare('SELECT "CustomerID" FROM public."Customer" WHERE "UserID" = :uidF LIMIT 1');
    // $me->execute([':uid' => $_SESSION['UserID']]);
    // $row = $me->fetch();
    // if (!$row) { echo json_encode([]); exit; }
    // $cid = $row['CustomerID'];

    $sql = <<<SQL
    SELECT
      c."DisplayName"         AS customer_name,
      a."AccountID"           AS account_id,
      a."AccountType"         AS account_type,
      (a."Balance"::numeric)  AS balance
    FROM public."Account" a
    JOIN public."Customer" c ON c."CustomerID" = a."CustomerID"
    -- WHERE a."CustomerID" = :cid   -- uncomment when session is ready
    ORDER BY c."DisplayName", a."AccountType", a."AccountID";
  SQL;
    $pdo = get_pdo();

    // $stmt = $pdo->prepare($sql);
    // $stmt->execute([':cid' => $cid]);
    // $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $rows = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($rows, JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error']);
}
