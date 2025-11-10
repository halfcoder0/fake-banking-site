<?php
require_once('../controllers/security/csrf.php');
require_once('../controllers/auth.php');
require_once('../controllers/profile_controller.php');

$nonce = generate_random();
add_csp_header($nonce);

try {
    // Require login and correct role (User only)
    $auth = new AuthController();
    $auth->check_user_role([Roles::USER], "/login");

    // Load controller
    $profile_controller = new ProfileController();

    // Get user session values
    $user_id = $_SESSION['UserID'];
    $customer_id = $_SESSION['CustomerID'];

    /* ============================================================
     * POST REQUEST — handle form submission
     * ============================================================ */
    if ($_SERVER["REQUEST_METHOD"] === "POST") {

        if (!csrf_verify()) {
            $_SESSION['flash'] = [
                'type' => 'error',
                'msg' => 'Invalid CSRF token.'
            ];
            header("Location: /profile");
            exit;
        }

        $result = $profile_controller->updateProfile($_POST, $user_id, $customer_id);

        if (!empty($result['success'])) {
            $_SESSION['flash'] = [
                'type' => 'success',
                'msg' => 'Profile updated successfully!'
            ];
        } else {
            $_SESSION['flash'] = [
                'type' => 'error',
                'msg' => $result['error'] ?? "Update failed."
            ];
        }

        // Redirect (PRG pattern)
        header("Location: /profile");
        exit;
    }

    /* ============================================================
     * GET REQUEST — load profile + flash (if any)
     * ============================================================ */
    $profile = $profile_controller->getProfile($user_id);

    // Load & clear flash message
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
} catch (Exception $exception) {
    error_log($exception->getMessage() . "\n" . $exception->getTraceAsString());
    redirect_500();
} catch (Throwable $throwable) {
    error_log($throwable->getMessage() . "\n" .  $throwable->getTraceAsString());
    redirect_500();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="../assets/images/favicon.png">
    <title>Nexabank | User Dashboard</title>
    <!-- This page plugin CSS -->
    <link nonce="<?= htmlspecialchars($nonce, ENT_QUOTES) ?>"
        href="assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link nonce="<?= htmlspecialchars($nonce, ENT_QUOTES) ?>" href="dist/css/style.min.css" rel="stylesheet">
</head>

<body>
    <!-- ============================================================== -->
    <div class="preloader">
        <div class="lds-ripple">
            <div class="lds-pos"></div>
            <div class="lds-pos"></div>
        </div>
    </div>
    <!-- ============================================================== -->
    <div id="main-wrapper">
        <!-- ============================================================== -->
        <header class="topbar">
            <nav class="navbar top-navbar navbar-expand-md navbar-dark">
                <div class="navbar-header">
                    <!-- This is for the sidebar toggle which is visible on mobile only -->
                    <a class="nav-toggler waves-effect waves-light d-block d-md-none" href="javascript:void(0)">
                        <i class="ti-menu ti-close"></i>
                    </a>
                    <!-- ============================================================== -->
                    <div class="navbar-brand">
                        <a href="#" class="logo">
                            <!-- Logo icon -->
                            <b class="logo-icon">
                                <!--You can put here icon as well // <i class="wi wi-sunset"></i> //-->
                                <!-- Dark Logo icon -->
                                <img src="assets/images/logo-icon.png" alt="homepage" class="dark-logo" />
                                <!-- Light Logo icon -->
                                <img src="assets/images/logo-light-icon.png" alt="homepage" class="light-logo" />
                            </b>
                            <!--End Logo icon -->
                        </a>
                    </div>
                    <!-- ============================================================== -->
                    <a class="topbartoggler d-block d-md-none waves-effect waves-light" href="javascript:void(0)"
                        data-toggle="collapse" data-target="#navbarSupportedContent"
                        aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                        <i class="ti-more"></i>
                    </a>
                </div>
                <!-- ============================================================== -->
            </nav>
        </header>
        <!-- ============================================================== -->
        <?php include __DIR__ . '/../includes/user_navbar.php' ?>
        <!-- ============================================================== -->
        <div class="page-wrapper">
            <!-- ============================================================== -->
            <div class="page-breadcrumb">
                <div class="row">
                    <div class="col-5 align-self-center">
                        <h4 class="page-title">Profile Page</h4>
                    </div>
                </div>
            </div>
            <!-- ============================================================== -->
            <div class="container-fluid">
                <!-- Start Page Content -->
                <!-- User Profile -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">

                                <h4 class="card-title">Welcome, <?= htmlspecialchars($_SESSION["DisplayName"]) ?></h4>
                                <!-- Alerts -->
                                <?php if (!empty($flash)): ?>
                                    <div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'danger' ?> alert-dismissible fade show"
                                        role="alert">
                                        <?= htmlspecialchars($flash['msg'], ENT_QUOTES) ?>
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                <?php endif; ?>

                                <!-- ================== -->
                                <!-- form -->
                                <form method="POST" action="/profile">
                                    <?= csrf_input() ?>

                                    <!-- Username -->
                                    <div class="form-group mb-3">
                                        <label class="form-label">Username</label>
                                        <input type="text" class="form-control" id="username" name="username"
                                            value="<?= htmlspecialchars($profile['Username']) ?>" required>
                                    </div>

                                    <!-- Display Name -->
                                    <div class="form-group mb-3">
                                        <label class="form-label">Display Name</label>
                                        <input type="text" class="form-control" id="displayName" name="displayName"
                                            value="<?= htmlspecialchars($profile['DisplayName']) ?>" required>
                                    </div>

                                    <!-- First Name -->
                                    <div class="form-group mb-3">
                                        <label class="form-label">First Name</label>
                                        <input type="text" class="form-control" id="firstName" name="firstName"
                                            value="<?= htmlspecialchars($profile['FirstName']) ?>" required>
                                    </div>

                                    <!-- Last Name -->
                                    <div class="form-group mb-3">
                                        <label class="form-label">Last Name</label>
                                        <input type="text" class="form-control" id="lastName" name="lastName"
                                            value="<?= htmlspecialchars($profile['LastName']) ?>" required>
                                    </div>

                                    <!-- DOB -->
                                    <div class="form-group mb-3">
                                        <label class="form-label">Date of Birth</label>
                                        <input type="date" class="form-control" id="dob" name="dob"
                                            value="<?= htmlspecialchars($profile['DOB']) ?>" required>
                                    </div>

                                    <!-- Contact -->
                                    <div class="form-group mb-3">
                                        <label class="form-label">Contact Number</label>
                                        <input type="text" class="form-control" id="contactNo" name="contactNo"
                                            value="<?= htmlspecialchars($profile['ContactNo']) ?>" required>
                                    </div>

                                    <!-- Email -->
                                    <div class="form-group mb-3">
                                        <label class="form-label">Email</label>
                                        <input type="email" class="form-control" id="email" name="email"
                                            value="<?= htmlspecialchars($profile['Email']) ?>" required>
                                    </div>

                                    <hr>

                                    <!-- Password -->
                                    <div class="form-group mb-3">
                                        <label class="form-label">New Password</label>
                                        <input type="password" class="form-control" id="password" name="password">
                                        <small class="form-text text-muted">Leave blank if unchanged.</small>
                                    </div>

                                    <!-- Repeat Password -->
                                    <div class="form-group mb-3">
                                        <label class="form-label">Repeat Password</label>
                                        <input type="password" class="form-control" id="repeat_pass" name="repeat_pass">
                                    </div>

                                    <button type="submit" class="btn btn-success">Save Changes</button>
                                </form>

                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <!-- ============================================================== -->
        <script nonce="<?= htmlspecialchars($nonce, ENT_QUOTES) ?>"
            src="../../assets/libs/jquery/dist/jquery.min.js"></script>
        <!-- Bootstrap tether Core JavaScript -->
        <script nonce="<?= htmlspecialchars($nonce, ENT_QUOTES) ?>"
            src="../../assets/libs/popper.js/dist/umd/popper.min.js"></script>
        <script nonce="<?= htmlspecialchars($nonce, ENT_QUOTES) ?>"
            src="../../assets/libs/bootstrap/dist/js/bootstrap.min.js"></script>
        <!-- apps -->
        <script nonce="<?= htmlspecialchars($nonce, ENT_QUOTES) ?>" src="../../dist/js/app.min.js"></script>
        <script nonce="<?= htmlspecialchars($nonce, ENT_QUOTES) ?>" src="../../dist/js/app.init.horizontal.js"></script>
        <script nonce="<?= htmlspecialchars($nonce, ENT_QUOTES) ?>"
            src="../../dist/js/app-style-switcher.horizontal.js"></script>
        <!-- slimscrollbar scrollbar JavaScript -->
        <script nonce="<?= htmlspecialchars($nonce, ENT_QUOTES) ?>"
            src="../../assets/libs/perfect-scrollbar/dist/perfect-scrollbar.jquery.min.js"></script>
        <script nonce="<?= htmlspecialchars($nonce, ENT_QUOTES) ?>"
            src="../../assets/extra-libs/sparkline/sparkline.js"></script>
        <!--Wave Effects -->
        <script nonce="<?= htmlspecialchars($nonce, ENT_QUOTES) ?>" src="../../dist/js/waves.js"></script>
        <!--Custom JavaScript -->
        <script nonce="<?= htmlspecialchars($nonce, ENT_QUOTES) ?>" src="../../dist/js/custom.min.js"></script>
        <!--This page plugins -->
        <script nonce="<?= htmlspecialchars($nonce, ENT_QUOTES) ?>"
            src="../../assets/extra-libs/DataTables/datatables.min.js"></script>

</body>