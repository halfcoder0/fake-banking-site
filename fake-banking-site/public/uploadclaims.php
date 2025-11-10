<?php
require_once('../controllers/security/session_bootstrap.php');
$error = '';
$success = '';
$userid = $_SESSION['UserID'] ?? null;
$role = $_SESSION['Role'] ?? null;

//Check if an user is being logged in.
if ($userid === '' || $role !== 'USER'){
  $error = 'Invalid user';
  header('Location: /');
  exit;
}else{
  // Get CustomerID from UserID for the database
  $getCustomerId = <<<SQL
    SELECT "CustomerID"
    FROM "Customer"
    WHERE "UserID" = :uid
    LIMIT 1;
  SQL;
  $stmt = DBController::exec_statement($getCustomerId,[[':uid', $_SESSION['UserID'], PDO::PARAM_STR]]);
  $row = $stmt->fetch();
  if (!$row) {
    // Do not reveal details to client; log server-side
    error_log("No Customer found for UserID={$userid}");
    header('Location: /login');
    exit;
  }
  $customerId = $row['CustomerID'];
}
// Upload configuration
const MAX_IMAGE_BYTES = 2 * 1024 * 1024; // 2 MB
$allowedExt  = ['jpg','jpeg','png','gif'];
$allowedMime = ['image/jpeg','image/png','image/gif'];

// Store outside webroot for safety; ensure this directory exists and is readable by PHP
$storageRoot = __DIR__ . '/assets/images/uploads/claims/';
if (!is_dir($storageRoot)) {
    // Attempt to create with restrictive permissions
    @mkdir($storageRoot, 0750, true);
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $desc = trim($_POST['description'] ?? '');
    $file = $_FILES['claim_image'] ?? null;

    // Basic preconditions
    if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
        $error = 'Please select a valid image.';
    } elseif (strlen($desc) === 0) {
        $error = 'Description is required.';
    } else {
        // Size limit
        if ($file['size'] > MAX_IMAGE_BYTES) {
            $error = 'File too large. Max 2 MB.';
        }

        // Extension whitelist (derived from original name)
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!$error && !in_array($ext, $allowedExt, true)) {
            $error = 'Only JPG, PNG, or GIF files are allowed.';
        }

        // MIME validation via finfo
        if (!$error) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime  = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);
            if (!in_array($mime, $allowedMime, true)) {
                $error = 'Invalid file type.';
            }
        }

        // Image probe (ensures it’s really an image)
        if (!$error && !getimagesize($file['tmp_name'])) {
            $error = 'Uploaded file is not a valid image.';
        }

        if (!$error) {
            // Generate a strong random filename; decouple from user-supplied name
            $safeName = bin2hex(random_bytes(16)) . '.' . $ext;
            $targetPath = $storageRoot. $safeName;

            // Move the uploaded file
            if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
                $error = 'Failed to move uploaded file.';
            } else {
                // Optional: tighten permissions on the stored file
                @chmod($targetPath, 0640);

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
                    $error = 'Database error while saving claim.';
                }
            }
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Nexabank | Upload Claim</title>
  <link rel="icon" type="image/png" sizes="16x16" href="./assets/images/favicon.png">
  <link href="./dist/css/style.min.css" rel="stylesheet">
  <style>
    .auth-wrapper {
      background: url(./assets/images/big/auth-bg.jpg) no-repeat center center;
      background-color: #f0f5f9;
    }
  </style>
</head>
<body>
  <div class="main-wrapper">
    <div class="preloader">
      <div class="lds-ripple"><div class="lds-pos"></div><div class="lds-pos"></div></div>
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
                    <a href="/listclaims" class="text-info m-l-5"><b>My Claims</b></a>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div><!-- /claimform-div -->
      </div>
    </div>
  </div>
  <script src="./assets/libs/jquery/dist/jquery.min.js"></script>
  <script src="./assets/libs/popper.js/dist/umd/popper.min.js"></script>
  <script src="./assets/libs/bootstrap/dist/js/bootstrap.min.js"></script>
  <script>
    $(".preloader").fadeOut();
  </script>
</body>
</html>
