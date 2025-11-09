<?php
require_once('../controllers/security/csrf.php');
require_once('../controllers/auth.php');

$nonce = generate_random();
add_csp_header($nonce);

// Upload configuration
const MAX_IMAGE_BYTES = 2 * 1024 * 1024; // 2 MB
$error = '';
$success = '';

try {
  $auth_controller = new AuthController();
  $auth_controller->check_user_role([Roles::USER]);

  upload_img($error, $success);
} catch (Exception $exception) {
  $error = "Error with page";
  error_log($exception->getMessage() . $exception->getTraceAsString());
} catch (Throwable $throwable) {
  $error = "Error with page";
  error_log($throwable->getMessage() . $throwable->getTraceAsString());
}

function upload_img(&$error, &$success)
{
  $customerId = $_SESSION['CustomerID'] ?? '';

  $allowedExt  = ['jpg', 'jpeg', 'png', 'gif'];
  $allowedMime = ['image/jpeg', 'image/png', 'image/gif'];

  if ($_SERVER['REQUEST_METHOD'] !== 'POST' || $customerId === '')
    return;
  if (!csrf_verify()) {
    $error = "Invalid request.";
    return;
  }

  // Store outside webroot for safety; ensure this directory exists and is readable by PHP
  $storageRoot = __DIR__ . '/assets/images/uploads/claims/';
  if (!is_dir($storageRoot)) {
    @mkdir($storageRoot, 0666, true);
  }

  $desc = trim($_POST['description'] ?? '');
  $file = $_FILES['claim_image'] ?? null;

  // Basic preconditions
  if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
    $error = 'Please select a valid image.';
    return;
  }

  // Check description
  if (strlen($desc) === 0) {
    $error = 'Description is required.';
    return;
  }

  // Size limit
  if ($file['size'] > MAX_IMAGE_BYTES) {
    $error = 'File too large. Max 2 MB.';
    return;
  }

  // Extension whitelist (derived from original name)
  $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
  if (!in_array($ext, $allowedExt, true)) {
    $error = 'Only JPG, PNG, or GIF files are allowed.';
    return;
  }

  // MIME validation via finfo
  $finfo = finfo_open(FILEINFO_MIME_TYPE);
  $mime  = finfo_file($finfo, $file['tmp_name']);
  finfo_close($finfo);
  if (!in_array($mime, $allowedMime, true)) {
    $error = 'Invalid file type.';
    return;
  }

  // Image probe (ensures it’s really an image)
  if (!getimagesize($file['tmp_name'])) {
    $error = 'Uploaded file is not a valid image.';
    return;
  }

  // Generate a strong random filename; decouple from user-supplied name
  $safeName = bin2hex(random_bytes(16)) . '.' . $ext;
  $targetPath = $storageRoot . $safeName;

  // Move the uploaded file
  if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
    $error =  'Failed to move uploaded file.';
    return;
  }

  // Optional: tighten permissions on the stored file
  @chmod($targetPath, 0666);

  // Store only the safe filename in DB; do not store full server path
  $insert = <<<SQL
                INSERT INTO "Claims"
                  ("ClaimID", "CustomerID", "ManagedBy", "ImagePath", "Description", "CreatedAt", "ApprovedAt")
                VALUES (gen_random_uuid(), :cid, NULL, :img, :desc, NOW(), NULL);
            SQL;

  $ok = DBController::exec_statement($insert, [
    [':cid',  $customerId, PDO::PARAM_STR],
    [':img',  $safeName,   PDO::PARAM_STR], // store filename only
    [':desc', $desc,       PDO::PARAM_STR]
  ]);

  if ($ok) {
    $success = 'Claim uploaded successfully!';
  } else {
    // Roll back file if DB insert failed
    @unlink($targetPath);
    $error = 'Error while saving claim.';
  }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Nexabank | Upload Claim</title>
  <link rel="icon" type="image/png" sizes="16x16" href="./assets/images/favicon.png">
  <link nonce="<?= htmlspecialchars($nonce, ENT_QUOTES) ?>" href="./dist/css/style.min.css" rel="stylesheet">
  <style nonce="<?= htmlspecialchars($nonce, ENT_QUOTES) ?>">
    .auth-wrapper {
      background: url(./assets/images/big/auth-bg.jpg) no-repeat center center;
      background-color: #f0f5f9;
    }
  </style>
</head>

<body>
  <div class="main-wrapper">
    <div class="preloader">
      <div class="lds-ripple">
        <div class="lds-pos"></div>
        <div class="lds-pos"></div>
      </div>
    </div>
    <div class="auth-wrapper d-flex no-block justify-content-center align-items-center">
      <div class="auth-box">
        <div id="claimform-div">
          <div class="logo">
            <span class="db"><img src="./assets/images/logo-icon.png" alt="logo" /></span>
            <h5 class="font-medium m-b-20">Submit a Claim</h5>
            <span>Please provide details and upload supporting image.</span>
          </div>
          <div class="row m-t-20">
            <div class="col-12">
              <?php if ($error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
              <?php endif; ?>
              <?php if ($success): ?>
                <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
              <?php endif; ?>

              <form class="form-horizontal m-t-20" method="POST" enctype="multipart/form-data">
                <?= csrf_input(); ?>
                <div class="input-group mb-3">
                  <div class="input-group-prepend">
                    <span class="input-group-text"><i class="ti-comment-alt"></i></span>
                  </div>
                  <textarea name="description" maxlength="1000" class="form-control form-control-lg" placeholder="Claim Description" required></textarea>
                </div>
                <div class="input-group mb-3">
                  <div class="input-group-prepend">
                    <span class="input-group-text"><i class="ti-image"></i></span>
                  </div>
                  <input type="file" name="claim_image" accept="image/*" class="form-control form-control-lg" required>
                </div>
                <div class="form-group text-center">
                  <div class="col-xs-12 p-b-20">
                    <button class="btn btn-block btn-lg btn-info" type="submit">Upload Claim</button>
                  </div>
                </div>
                <div class="form-group m-b-0 m-t-10">
                  <div class="col-sm-12 text-center">
                    <a href="/dashboard" class="text-info m-l-5"><b>Back to Dashboard</b></a>
                  </div>
                  <div class="col-sm-12 text-center">
                    <a href="/view_claims" class="text-info m-l-5"><b>My Claims</b></a>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div><!-- /claimform-div -->
      </div>
    </div>
  </div>
  <script nonce="<?= htmlspecialchars($nonce, ENT_QUOTES) ?>" src="./assets/libs/jquery/dist/jquery.min.js"></script>
  <script nonce="<?= htmlspecialchars($nonce, ENT_QUOTES) ?>" src="./assets/libs/popper.js/dist/umd/popper.min.js"></script>
  <script nonce="<?= htmlspecialchars($nonce, ENT_QUOTES) ?>" src="./assets/libs/bootstrap/dist/js/bootstrap.min.js"></script>
  <script nonce="<?= htmlspecialchars($nonce, ENT_QUOTES) ?>">
    $(".preloader").fadeOut();
  </script>
</body>

</html>