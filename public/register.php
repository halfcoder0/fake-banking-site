<?php
require_once('../controllers/db_controller.php');

// Initialize DB connection
DBController::init_db();

if (isset($_POST['submit'])) {
    $username        = trim($_POST['username']);
    $password        = $_POST['password'];
    $repeat_pass     = $_POST['repeat_password'];
    $firstName       = trim($_POST['firstname']);
    $lastName        = trim($_POST['lastname']);
    $dob             = $_POST['dob'];
    $contactNo       = trim($_POST['contactno']);
    $email           = trim($_POST['email']);
    $displayName     = $firstName . " " . substr($lastName,0,1); // e.g. "Alice W"

    // Check password match
    if ($password !== $repeat_pass) {
        echo "<script>alert('Passwords do not match.');</script>";
        echo "<script>window.location.href ='register.php'</script>";
        exit;
    }

    // Hash password securely
    $hashedPassword = password_hash($password, PASSWORD_ARGON2ID);
    $role = 'USER';

    // Check if username exists
    $query = 'SELECT 1 FROM "User" WHERE "Username" = :username';
    $stmt = DBController::exec_statement($query, [
        [':username', $username, PDO::PARAM_STR]
    ]);

    if ($stmt->rowCount() > 0) {
        echo "<script>alert('This username is already taken');</script>";
    } else {
        // Generate UUID once
        $userId = DBController::$pdo->query("SELECT gen_random_uuid()")->fetchColumn();

        // Insert into User
        $insertUser = 'INSERT INTO "User" ("UserID", "Username", "Password", "Role", "LastLogin")
                       VALUES (:id, :username, :password, :role, NULL)';
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
            echo "<script>alert('You have successfully registered');</script>";
            echo "<script>window.location.href ='login.php'</script>";
        } else {
            echo "<script>alert('Something went wrong. Please try again');</script>";
            echo "<script>window.location.href ='register.php'</script>";
        }
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
    <div class="auth-wrapper d-flex no-block justify-content-center align-items-center">
      <div class="auth-box">
        <div id="registerform-div">
          <div class="logo">
            <span class="db"><img src="./assets/images/logo-icon.png" alt="logo" /></span>
            <h5 class="font-medium m-b-20">Sign Up</h5>
          </div>
          <div class="row">
            <div class="col-12">
              <form class="form-horizontal m-t-20" id="registerform" method="POST">

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
                  <input type="text" name="contactno" class="form-control form-control-lg" placeholder="Contact Number" required>
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
