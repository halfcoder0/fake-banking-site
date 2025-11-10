<?php
require_once('../controllers/security/csrf.php');
require_once('../controllers/auth.php');

$nonce = generate_random();
add_csp_header($nonce);

$baseUrl = '/assets/images/uploads/claims/';
$claims = [];

try {
  $auth_controller = new AuthController();
  $auth_controller->check_user_role([Roles::STAFF]);

  $claims = get_claims();
} catch (Exception $exception) {
  $error = "Error with page";
  error_log($exception->getMessage() . $exception->getTraceAsString());
} catch (Throwable $throwable) {
  $error = "Error with page";
  error_log($throwable->getMessage() . $throwable->getTraceAsString());
}

function get_claims()
{
  $query = <<<SQL
    SELECT c."ClaimID",
           c."ImagePath",
           c."Description",
           c."CreatedAt",
           c."ApprovedAt",
           c."ManagedBy",
           cust."CustomerID",
           cust."DisplayName" AS "CustomerName",
           su."Username"      AS "StaffName"
    FROM "Claims" c
    JOIN "Customer" cust ON c."CustomerID" = cust."CustomerID"
    LEFT JOIN "Staff" s ON c."ManagedBy" = s."StaffID"
    LEFT JOIN "User" su ON s."UserID" = su."UserID"
    ORDER BY c."CreatedAt" DESC;
  SQL;

  return DBController::exec_statement($query)->fetchAll();
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Nexabank | Admin Claims</title>
  <link nonce="<?= htmlspecialchars($nonce, ENT_QUOTES) ?>" href="./dist/css/style.min.css" rel="stylesheet">
  <link nonce="<?= htmlspecialchars($nonce, ENT_QUOTES) ?>" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
  <div class="container mt-5">
    <h2 class="mb-4">All Claims Overview</h2>
    <a href="/staff/assigned_claims" class="text-info m-l-5"><b>My Assigned Cases</b></a>
    <table class="table table-striped table-bordered align-middle">
      <thead class="table-dark">
        <tr>
          <th>Claim ID</th>
          <th>Customer</th>
          <th>Description</th>
          <th>Created At</th>
          <th>Approved At</th>
          <th>Managed By</th>
          <th>Image</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($claims as $claim): ?>
          <tr>
            <td><?= htmlspecialchars($claim['ClaimID']) ?></td>
            <td><?= htmlspecialchars($claim['CustomerName']) ?></td>
            <td><?= htmlspecialchars(substr($claim['Description'], 0, 50)) ?>...</td>
            <td><?= htmlspecialchars($claim['CreatedAt']) ?></td>
            <td><?= $claim['ApprovedAt'] ?? 'Pending' ?></td>
            <td>
              <?= $claim['StaffName'] ? htmlspecialchars($claim['StaffName']) : 'Unassigned' ?>
            </td>

            <td>
              <?php if (!empty($claim['ImagePath'])): ?>
                <img src="<?= htmlspecialchars($baseUrl . basename($claim['ImagePath'])); ?>"
                  alt="Claim Image" style="max-width:80px; max-height:80px;"
                  class="img-thumbnail">
              <?php endif; ?>
            </td>
            <td>
              <?php if (empty($claim['ManagedBy'])): ?>
                <form action="/assign_claims" method="post" class="d-inline">
                  <?= csrf_input(); ?>
                  <input type="hidden" name="claim_id" value="<?= htmlspecialchars($claim['ClaimID']) ?>">
                  <button type="submit" class="btn btn-primary btn-sm">Pick up claims</button>
                </form>
              <?php else: ?>
                <span class="badge bg-secondary">Managed</span>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <script nonce="<?= htmlspecialchars($nonce, ENT_QUOTES) ?>" src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>