<?php
require_once('../controllers/security/session_bootstrap.php');

//BaseUrl for displaying on pics
$baseUrl ='/assets/images/uploads/claims/';

// Fetch all claims for the logged-in user
$userid = $_SESSION['UserID'] ?? null;
$role = $_SESSION['Role'] ?? null;

//Check if an user is being logged in.
if ($userid === '' || $role !== 'USER'){
  $error = 'Invalid user';
  header('Location: /');
  exit;
}

// Get CustomerID from UserID
$getCustomerId = <<<SQL
    SELECT "CustomerID"
    FROM "Customer"
    WHERE "UserID" = :uid
    LIMIT 1;
SQL;

$stmt = DBController::exec_statement($getCustomerId, [
    [':uid', $userid, PDO::PARAM_STR]
]);
$row = $stmt->fetch();
if (!$row) {
    die("No customer record found.");
}
$customerId = $row['CustomerID'];

$query = <<<SQL
    SELECT c."ClaimID",
           c."ImagePath",
           c."Description",
           c."CreatedAt",
           c."ApprovedAt",
           c."ManagedBy",
           s."StaffID",
           su."Username" AS "StaffName"
    FROM "Claims" c
    JOIN "Customer" cust ON c."CustomerID" = cust."CustomerID"
    LEFT JOIN "Staff" s ON c."ManagedBy" = s."StaffID"
    LEFT JOIN "User" su ON s."UserID" = su."UserID"
    WHERE c."CustomerID" = :cid
    ORDER BY c."CreatedAt" DESC;
SQL;


$claims = DBController::exec_statement($query, [
    [':cid', $customerId, PDO::PARAM_STR]
])->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Nexabank | My Claims</title>
  <link href="./dist/css/style.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
  <h2 class="mb-4">My Claims</h2>
  <div class="col-sm-12 text-center">
  <a href="/uploadclaims" class="text-info m-l-5"><b>Submit new Claims</b></a>
</div>
  <div class="accordion" id="claimsAccordion">
    <?php foreach ($claims as $index => $claim): ?>
      <div class="accordion-item">
        <h2 class="accordion-header" id="heading<?= $index ?>">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                  data-bs-target="#collapse<?= $index ?>" aria-expanded="false"
                  aria-controls="collapse<?= $index ?>">
            Unique Claim ID: <?= htmlspecialchars($claim['ClaimID'])?>...
          </button>
        </h2>
        <div id="collapse<?= $index ?>" class="accordion-collapse collapse"
             aria-labelledby="heading<?= $index ?>" data-bs-parent="#claimsAccordion">
          <div class="accordion-body">
            <p><strong>Unique Claim ID:</strong> <?= htmlspecialchars($claim['ClaimID'])?></p>
            <p><strong>Description:</strong> <?= nl2br(htmlspecialchars($claim['Description'])) ?></p>
            <p><strong>Created At:</strong> <?= htmlspecialchars($claim['CreatedAt']) ?></p>
            <p><strong>Approved At:</strong> <?= $claim['ApprovedAt'] ?? 'Pending' ?></p>
            <p><strong>Managed By:</strong> <?= $claim['StaffName'] ?? 'Unassigned' ?></p>
            <?php if (!empty($claim['ImagePath'])): ?>
              <div class="mt-3">
                <img src='<?= htmlspecialchars($baseUrl.basename($claim['ImagePath'])); ?>'class="img-fluid rounded">
              </div>
            <?php endif; ?>
            <!--button to delete claims -->
            <form action="/deleteclaims" method="post" class="mt-3">
            <input type="hidden" name="claim_id" value="<?= htmlspecialchars($claim['ClaimID']) ?>">
            <button type="submit" class="btn btn-danger btn-sm"
                    onclick="return confirm('Are you sure you want to delete this claim?');">
              Delete Claim
            </button>
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
