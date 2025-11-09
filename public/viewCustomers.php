<?php
require_once('../controllers/security/session_bootstrap.php');
$userid = $_SESSION['UserID'] ?? null;
$role   = $_SESSION['Role'] ?? null;
if ($userid === '' || $role !== 'STAFF') {
    header('Location: /login');
    exit;
}
//To Check if Staff's UserID is found in the stafff database, prevent spoofing
$getStaffName = <<<SQL
  SELECT "StaffID"
  FROM "Staff"
  WHERE "UserID" = :uid
  LIMIT 1;
SQL;
$stmt = DBController::exec_statement($getStaffName,[[':uid', $userid, PDO::PARAM_STR]]);
$row = $stmt->fetch();
if (!$row) {
  header('Location: /login');
  exit;
}
// Fetch all customers
$query = <<<SQL
SELECT cust."CustomerID",
       cust."FirstName",
       cust."LastName",
       cust."DOB",
       cust."Email",
       cust."ContactNo",
       cust."DisplayName",
       u."UserID"
FROM "Customer" cust
JOIN "User" u ON cust."UserID" = u."UserID"
ORDER BY cust."CustomerID";
SQL;
$customers = DBController::exec_statement($query)->fetchAll();

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Nexabank | Customers</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
  <h2 class="mb-4">All Customers</h2>

  <div class="accordion" id="customerAccordion">
    <?php foreach ($customers as $index => $cust): ?>
      <div class="accordion-item">
        <h2 class="accordion-header" id="heading<?= $index ?>">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                  data-bs-target="#collapse<?= $index ?>" aria-expanded="false"
                  aria-controls="collapse<?= $index ?>">
            <?= htmlspecialchars($cust['DisplayName']) ?>
          </button>
        </h2>
        <div id="collapse<?= $index ?>" class="accordion-collapse collapse"
             aria-labelledby="heading<?= $index ?>" data-bs-parent="#customerAccordion">
          <div class="accordion-body">
            <p><strong>First Name:</strong> <?= htmlspecialchars($cust['FirstName']) ?></p>
            <p><strong>Last Name:</strong> <?= htmlspecialchars($cust['LastName']) ?></p>
            <p><strong>Date of Birth:</strong> <?= htmlspecialchars($cust['DOB']) ?></p>
            <p><strong>Contact No:</strong> <?= htmlspecialchars($cust['ContactNo']) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($cust['Email']) ?></p>
            <p><strong>Customer ID:</strong> <?= htmlspecialchars($cust['CustomerID']) ?></p>
            <form method="POST" action="/resetPasswordRequest">
              <input type="hidden" name="customer_id" value="<?= htmlspecialchars($cust['UserID']) ?>">
              <button type="submit" class="btn btn-warning btn-sm">Reset Password</button>
            </form>

          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
