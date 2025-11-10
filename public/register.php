<?php
require_once('../controllers/security/csrf.php');

$nonce = generate_random();
add_csp_header($nonce);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
  try {
    register_user();
  } catch (Exception $exception) {
    error_log($exception->getMessage() . "\n" . $exception->getTraceAsString());
    redirect_500();
  } catch (Throwable $throwable) {
    error_log($throwable->getMessage() . "\n" .  $throwable->getTraceAsString());
    redirect_500();
  }
}

function register_user()
{
  $username        = trim($_POST['username']);
  $password        = $_POST['password'];
  $repeat_pass     = $_POST['repeat_password'];
  $firstName       = trim($_POST['firstname']);
  $lastName        = trim($_POST['lastname']);
  $dob             = $_POST['dob'];
  $contactNo       = trim($_POST['contactno']);
  $email           = trim($_POST['email']);

  // Check password match
  if ($password !== $repeat_pass) {
    $_SESSION[SessionVariables::GENERIC_ERROR->value] = "Passwords dont match.";
    return;
  }

  if (is_valid_username($username) === false) {
    $_SESSION[SessionVariables::GENERIC_ERROR->value] = "Invalid username, username can only contain alphanumeric characters.";
    return;
  }

  if (is_valid_name($firstName) === false) {
    $_SESSION[SessionVariables::GENERIC_ERROR->value] = "Invalid firstname.";
    return;
  }

  if (is_valid_name($lastName) === false) {
    $_SESSION[SessionVariables::GENERIC_ERROR->value] = "Invalid firstname.";
    return;
  }

  // Try create DT object using the dob string
  try {
    $_dob = new DateTime($dob);
  } catch (DateMalformedStringException $e) {
    $_SESSION[SessionVariables::GENERIC_ERROR->value] = "Invalid DOB.";
    return;
  }

  // Validate contact number length
  if (strlen($contactNo) > 8) {
    $_SESSION[SessionVariables::GENERIC_ERROR->value] = "Contact number must not exceed 8 digits.";
    return;
  }

  // validate integer
  $options = ['options' => ['min_range' => 10_000_000]];
  if (filter_var($contactNo, FILTER_VALIDATE_INT, $options) === false) {
    $_SESSION[SessionVariables::GENERIC_ERROR->value] = "Contact number must only contain numbers.";
    return;
  }

  // Validate email
  if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
    $_SESSION[SessionVariables::GENERIC_ERROR->value] = "Invalid email.";
    return;
  }

  // Create a display name from first & last
  $displayName = $firstName . " " . substr($lastName, 0, 1);

  // Hash password securely
  $hashedPassword = argon_hash($password);
  $role = Roles::USER->value;

  // Check if username exists
  $query = 'SELECT 1 FROM "User" WHERE "Username" = :username';
  $stmt = DBController::exec_statement($query, [
    [':username', $username, PDO::PARAM_STR]
  ]);

  if ($stmt->rowCount() > 0) {
    $_SESSION[SessionVariables::GENERIC_ERROR->value] = "Username has been taken.";
    return;
  }

  // Generate UUID once
  $userId = DBController::$pdo->query("SELECT gen_random_uuid()")->fetchColumn();

  // Insert into User
  $insertUser = <<<SQL
            INSERT INTO "User" ("UserID", "Username", "Password", "Role", "LastLogin")
            VALUES (:id, :username, :password, :role, NULL)
          SQL;

  DBController::exec_statement($insertUser, [
    [':id', $userId, PDO::PARAM_STR],
    [':username', $username, PDO::PARAM_STR],
    [':password', $hashedPassword, PDO::PARAM_STR],
    [':role', $role, PDO::PARAM_STR]
  ]);


  // Insert into Customer
  $customerId = DBController::$pdo->query("SELECT gen_random_uuid()")->fetchColumn();
  $insertCustomer = 'INSERT INTO "Customer" ("CustomerID", "UserID", "DisplayName", "FirstName", "LastName", "DOB", "ContactNo", "Email")
                           VALUES (:cid, :uid, :display, :fname, :lname, :dob, :contact, :email)';
  $success = DBController::exec_statement($insertCustomer, [
    [':cid', $customerId, PDO::PARAM_STR],
    [':uid', $userId, PDO::PARAM_STR],
    [':display', $displayName, PDO::PARAM_STR],
    [':fname', $firstName, PDO::PARAM_STR],
    [':lname', $lastName, PDO::PARAM_STR],
    [':dob', $dob, PDO::PARAM_STR],
    [':contact', $contactNo, PDO::PARAM_INT],
    [':email', $email, PDO::PARAM_STR]
  ]);

  if ($success) {
    $_SESSION[SessionVariables::SUCCESS->value] = "You have successfully registered.";
    header("Location: /login");
  } else {
    $_SESSION[SessionVariables::GENERIC_ERROR->value] = "Something went wrong, please try again.";
  }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Nexabank | Register</title>
  <!-- Match login.php CSS -->
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
        <div id="registerform-div">
          <div class="logo">
            <span class="db"><img src="/assets/images/logo-icon.png" alt="logo" /></span>
            <h5 class="font-medium m-b-20">Sign Up</h5>
          </div>
          <div class="row">
            <div class="col-12">
              <?php if (isset($_SESSION[SessionVariables::GENERIC_ERROR->value])): ?>
                <div class="row">
                  <div class="col-12">
                    <div class="alert alert-warning"> <?php echo ($_SESSION[SessionVariables::GENERIC_ERROR->value]);
                                                      unset($_SESSION[SessionVariables::GENERIC_ERROR->value]); ?>
                      <button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">x</span> </button>
                    </div>
                  </div>
                </div>
              <?php endif; ?>
              <?php if (isset($_SESSION[SessionVariables::SUCCESS->value])): ?>
                <div class="row">
                  <div class="col-12">
                    <div class="alert alert-success"> <?php echo ($_SESSION[SessionVariables::SUCCESS->value]);
                                                      unset($_SESSION[SessionVariables::SUCCESS->value]); ?>
                      <button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">x</span> </button>
                    </div>
                  </div>
                </div>
              <?php endif; ?>
              <form class="form-horizontal m-t-20" id="registerform" method="POST">
                <?= csrf_input(); ?>
                <!-- Username -->
                <div class="input-group mb-3">
                  <div class="input-group-prepend"><span class="input-group-text"><i class="ti-user"></i></span></div>
                  <input type="text" name="username" maxlength="25" class="form-control form-control-lg" placeholder="Username" required>
                </div>

                <!-- Password -->
                <div class="input-group mb-3">
                  <div class="input-group-prepend"><span class="input-group-text"><i class="ti-lock"></i></span></div>
                  <input type="password" name="password" maxlength="254" class="form-control form-control-lg" placeholder="Password" required>
                </div>

                <!-- Repeat Password -->
                <div class="input-group mb-3">
                  <div class="input-group-prepend"><span class="input-group-text"><i class="ti-lock"></i></span></div>
                  <input type="password" name="repeat_password" maxlength="254" class="form-control form-control-lg" placeholder="Repeat Password" required>
                </div>

                <!-- First Name -->
                <div class="input-group mb-3">
                  <div class="input-group-prepend"><span class="input-group-text"><i class="ti-id-badge"></i></span></div>
                  <input type="text" name="firstname" class="form-control form-control-lg" placeholder="First Name" required>
                </div>

                <!-- Last Name -->
                <div class="input-group mb-3">
                  <div class="input-group-prepend"><span class="input-group-text"><i class="ti-id-badge"></i></span></div>
                  <input type="text" name="lastname" class="form-control form-control-lg" placeholder="Last Name" required>
                </div>

                <!-- Date of Birth -->
                <div class="input-group mb-3">
                  <div class="input-group-prepend"><span class="input-group-text"><i class="ti-calendar"></i></span></div>
                  <input type="date" name="dob" class="form-control form-control-lg" required>
                </div>


                <!-- Contact Number -->
                <div class="input-group mb-3">
                  <div class="input-group-prepend"><span class="input-group-text"><i class="ti-mobile"></i></span></div>
                  <input type="text" name="contactno"
                    class="form-control form-control-lg"
                    placeholder="Contact Number"
                    maxlength="8"
                    pattern="\d{1,8}"
                    required>
                </div>


                <!-- Email -->
                <div class="input-group mb-3">
                  <div class="input-group-prepend"><span class="input-group-text"><i class="ti-email"></i></span></div>
                  <input type="email" name="email" class="form-control form-control-lg" placeholder="Email Address" required>
                </div>

                <!-- Submit -->
                <div class="form-group text-center">
                  <div class="col-xs-12 p-b-20">
                    <button class="btn btn-block btn-lg btn-info" type="submit" name="submit" value="Register">Register Now</button>
                  </div>
                </div>

                <!-- Link to login -->
                <div class="form-group m-b-0 m-t-10">
                  <div class="col-sm-12 text-center">
                    Already have an account? <a href="/login" class="text-info m-l-5"><b>Sign In</b></a>
                  </div>
                </div>

              </form>
            </div>
          </div>
        </div> <!-- /registerform-div -->
      </div>
    </div>
  </div>
</body>

</html>