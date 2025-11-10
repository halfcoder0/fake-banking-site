<?php
require_once __DIR__ . '/../controllers/auth.php';

$nonce = generate_random();
add_csp_header($nonce);

$error = '';

try {
  if (!isset($_SESSION['pending_user']) || !isset($_SESSION["pending_role"]))
    redirect_404();

  if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['pending_user']) && isset($_SESSION["pending_role"]))
    verify_otp($error);
} catch (Exception $exception) {
  error_log($exception->getMessage() . "\n" . $exception->getTraceAsString());
  redirect_500();
} catch (Throwable $throwable) {
  error_log($throwable->getMessage() . "\n" . $throwable->getTraceAsString());
  redirect_500();
}

function verify_otp(&$error)
{
  $otp = trim($_POST['otp']) ?? '';
  $userId = $_SESSION['pending_user'] ?? '';
  $role  =  $_SESSION['pending_role'] ?? '';

  if ($userId === '' || $role === '')
    redirect_404();

  $user_data = [
    'UserID' => $userId,
    'Role'   => $role
  ];

  if ($userId && $otp) {
    $stmt = DBController::exec_statement(
      'SELECT "Code","ExpiresAt" FROM "UserOTP" WHERE "UserID" = :uid',
      [[':uid', $userId, PDO::PARAM_STR]]
    );
    $row = $stmt->fetch();
    if ($row && $row['Code'] === $otp && strtotime($row['ExpiresAt']) > time()) {
      //Once 2FA is successfull, we can remove the 2FA at the database
      DBController::exec_statement(
        'DELETE FROM "UserOTP" WHERE "UserID" = :uid',
        [[':uid', $userId, PDO::PARAM_STR]]
      );
      unset($_SESSION['pending_user'], $_SESSION['pending_role']);

      $auth_controller = new AuthController();
      $auth_controller->handle_different_roles($user_data);
      exit;
    } else {
      $error = "Invalid or expired code.";
    }
  } else {
    $error = "Missing OTP or session expired.";
  }
  unset($_SESSION['pending_user'], $_SESSION['pending_role']);
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Nexabank | Verify OTP</title>
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
    <div class="auth-wrapper d-flex no-block justify-content-center align-items-center">
      <div class="auth-box">
        <div id="otpform-div">
          <div class="logo">
            <span class="db"><img src="./assets/images/logo-icon.png" alt="logo" /></span>
            <h5 class="font-medium m-b-20">Two-Factor Verification</h5>
            <span>Please enter the 6-digit code shown to you.</span>
          </div>
          <div class="row m-t-20">
            <div class="col-12">
              <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
              <?php endif; ?>
              <form class="form-horizontal m-t-20" method="POST">
                <div class="input-group mb-3">
                  <div class="input-group-prepend">
                    <span class="input-group-text"><i class="ti-key"></i></span>
                  </div>
                  <input type="text" name="otp" maxlength="6" class="form-control form-control-lg" placeholder="Enter OTP" required>
                </div>
                <div class="form-group text-center">
                  <div class="col-xs-12 p-b-20">
                    <button class="btn btn-block btn-lg btn-info" type="submit">Verify</button>
                  </div>
                </div>
              </form>
              <div class="form-group m-b-0 m-t-10">
                <div class="col-sm-12 text-center">
                  <a href="/login" class="text-info m-l-5"><b>Back to Login</b></a>
                </div>
              </div>
            </div>
          </div>
        </div> <!-- /otpform-div -->
      </div>
    </div>
  </div>
</body>

</html>