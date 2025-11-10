<?php
require_once('../controllers/security/session_bootstrap.php');

$userid = $_SESSION['UserID'] ?? null;
$role   = $_SESSION['Role'] ?? null;

if ($userid === '' || $role !== 'STAFF') {
    header('Location: /');
    exit;
}else{
  // Get CustomerID from UserID for the database
  $getStaffName = <<<SQL
    SELECT "StaffID"
    FROM "Staff"
    WHERE "UserID" = :uid
    LIMIT 1;
  SQL;
  $stmt = DBController::exec_statement($getStaffName,[[':uid', $_SESSION['UserID'], PDO::PARAM_STR]]);
  $row = $stmt->fetch();
  if (!$row) {
    throw new Exception("No Staff found for this UserID");
  }
  $staffID = $row['StaffID'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['claim_id'])) {
    $claimId = $_POST['claim_id'];

    $updateQuery = <<<SQL
        UPDATE "Claims"
        SET "ManagedBy" = :uid
        WHERE "ClaimID" = :cid
        AND "ManagedBy" IS NULL; -- only assign if not already taken
    SQL;

    DBController::exec_statement($updateQuery, [
        [':uid', $staffID, PDO::PARAM_STR],
        [':cid', $claimId, PDO::PARAM_STR]
    ]);
}
header('Location: /staffclaimsoverview');
exit;
