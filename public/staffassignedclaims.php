<?php
require_once('../controllers/security/csrf.php');
require_once('../controllers/auth.php');

$nonce = generate_random();
add_csp_header($nonce);

$baseUrl = '/assets/images/uploads/claims/';

try {
  $auth_controller = new AuthController();
  $auth_controller->check_user_role([Roles::STAFF]);

  $claims = get_assigned_claims();
} catch (Exception $exception) {
  $error = "Error with page";
  error_log($exception->getMessage() . $exception->getTraceAsString());
} catch (Throwable $throwable) {
  $error = "Error with page";
  error_log($throwable->getMessage() . $throwable->getTraceAsString());
}

function get_assigned_claims()
{
  $staffID = $_SESSION['StaffID'];

  // Fetch claims assigned to this staff
  $query = <<<SQL
    SELECT c."ClaimID", c."ImagePath", c."Description", c."CreatedAt",
           c."ApprovedAt", u."Username"
    FROM "Claims" c
    JOIN "Customer" cust ON c."CustomerID" = cust."CustomerID"
    JOIN "User" u ON cust."UserID" = u."UserID"
    WHERE c."ManagedBy" = :uid
    ORDER BY c."CreatedAt" DESC;
SQL;

  $claims = DBController::exec_statement($query, [
    [':uid', $staffID, PDO::PARAM_STR]
  ])->fetchAll();

  return $claims;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Nexabank | My Pages</title>
  <link nonce="<?= htmlspecialchars($nonce, ENT_QUOTES) ?>" href="./dist/css/style.min.css" rel="stylesheet">
  <link nonce="<?= htmlspecialchars($nonce, ENT_QUOTES) ?>" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
  <div class="container mt-5">
    <h2 class="mb-4">My Assigned Claims</h2>

    <div class="accordion" id="claimsAccordion">
      <?php foreach ($claims as $index => $claim): ?>
        <div class="accordion-item">
          <h2 class="accordion-header" id="heading<?= $index ?>">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
              data-bs-target="#collapse<?= $index ?>" aria-expanded="false"
              aria-controls="collapse<?= $index ?>">
              Claim <?= htmlspecialchars($claim['ClaimID']) ?> —
              <?= htmlspecialchars(substr($claim['Description'], 0, 30)) ?>...
            </button>
          </h2>
          <div id="collapse<?= $index ?>" class="accordion-collapse collapse"
            aria-labelledby="heading<?= $index ?>" data-bs-parent="#claimsAccordion">
            <div class="accordion-body">
              <p><strong>User:</strong> <?= htmlspecialchars($claim['Username']) ?></p>
              <p><strong>Description:</strong> <?= nl2br(htmlspecialchars($claim['Description'])) ?></p>
              <p><strong>Created At:</strong> <?= htmlspecialchars($claim['CreatedAt']) ?></p>
              <p><strong>Status:</strong> <?= $claim['ApprovedAt'] ?? 'Pending' ?></p>
              <?php if (!empty($claim['ImagePath'])): ?>
                <div class="mt-3">
                  <img src="<?= htmlspecialchars($baseUrl . basename($claim['ImagePath'])); ?>"
                    alt="Claim Image" class="img-fluid rounded" style="max-width:200px;">
                </div>
              <?php endif; ?>

              <div class="mt-3">
                <!-- Approve -->
                <form action="/approve_claim" method="post" class="d-inline">
                  <?= csrf_input(); ?>
                  <input type="hidden" name="claim_id" value="<?= htmlspecialchars($claim['ClaimID']) ?>">
                  <button type="submit" class="btn btn-success btn-sm">Approve</button>
                </form>
                <!-- Reject -->
                <form action="/reject_claim" method="post" class="d-inline">
                  <?= csrf_input(); ?>
                  <input type="hidden" name="claim_id" value="<?= htmlspecialchars($claim['ClaimID']) ?>">
                  <button type="submit" class="btn btn-danger btn-sm">Reject</button>
                </form>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <script nonce="<?= htmlspecialchars($nonce, ENT_QUOTES) ?>" src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>