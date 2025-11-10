<?php
require_once('../controllers/security/csrf.php');
require_once('../controllers/auth.php');

$nonce = generate_random();
add_csp_header($nonce);

try {
  $auth_controller = new AuthController();
  $auth_controller->check_user_role([Roles::USER]);

  $claims = get_claims();
} catch (Exception $exception) {
  $_SESSION[SessionVariables::GENERIC_ERROR->value] = "Error with page";
  error_log($exception->getMessage() . $exception->getTraceAsString());
} catch (Throwable $throwable) {
  $_SESSION[SessionVariables::GENERIC_ERROR->value] = "Error with page";
  error_log($throwable->getMessage() . $throwable->getTraceAsString());
}

function get_claims()
{
  $customerId = $_SESSION['CustomerID'] ?? '';
  if ($customerId === '')
    throw new Exception('CustomerID not found in session.');

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

  return DBController::exec_statement($query, [
    [':cid', $customerId, PDO::PARAM_STR]
  ])->fetchAll();
}

//BaseUrl for displaying on pics
$baseUrl = '/assets/images/uploads/claims/';
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Nexabank | My Claims</title>
  <link nonce="<?= htmlspecialchars($nonce, ENT_QUOTES) ?>" href="./dist/css/style.min.css" rel="stylesheet">
  <link nonce="<?= htmlspecialchars($nonce, ENT_QUOTES) ?>" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
  <div class="container mt-5">
    <h2 class="mb-4">My Claims</h2>
    <div class="col-sm-12 text-center">
      <a href="/upload_claims" class="text-info m-l-5"><b>Submit new Claims</b></a>
    </div>
    <div class="accordion" id="claimsAccordion">
      <?php foreach ($claims as $index => $claim): ?>
        <div class="accordion-item">
          <h2 class="accordion-header" id="heading<?= $index ?>">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
              data-bs-target="#collapse<?= $index ?>" aria-expanded="false"
              aria-controls="collapse<?= $index ?>">
              Unique Claim ID: <?= htmlspecialchars($claim['ClaimID']) ?>...
            </button>
          </h2>
          <div id="collapse<?= $index ?>" class="accordion-collapse collapse"
            aria-labelledby="heading<?= $index ?>" data-bs-parent="#claimsAccordion">
            <div class="accordion-body">
              <p><strong>Unique Claim ID:</strong> <?= htmlspecialchars($claim['ClaimID']) ?></p>
              <p><strong>Description:</strong> <?= nl2br(htmlspecialchars($claim['Description'])) ?></p>
              <p><strong>Created At:</strong> <?= htmlspecialchars($claim['CreatedAt']) ?></p>
              <p><strong>Approved At:</strong> <?= $claim['ApprovedAt'] ?? 'Pending' ?></p>
              <p><strong>Managed By:</strong> <?= $claim['StaffName'] ?? 'Unassigned' ?></p>
              <?php if (!empty($claim['ImagePath'])): ?>
                <div class="mt-3">
                  <img src='<?= htmlspecialchars($baseUrl . basename($claim['ImagePath'])); ?>' class="img-fluid rounded">
                </div>
              <?php endif; ?>
              <!--button to delete claims -->
              <form action="/delete_claims" method="post" class="mt-3">
                <?= csrf_input() ?>
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
  <script nonce="<?= htmlspecialchars($nonce, ENT_QUOTES) ?>" src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
<?php
if (isset($_SESSION[SessionVariables::GENERIC_ERROR->value])): ?>
    <script nonce="<?= htmlspecialchars($nonce, ENT_QUOTES) ?>">
        alert("<?= addslashes($_SESSION[SessionVariables::GENERIC_ERROR->value]) ?>");
    </script>
    <?php unset($_SESSION[SessionVariables::GENERIC_ERROR->value]); // clear after use 
    ?>
<?php endif; ?>
</html>