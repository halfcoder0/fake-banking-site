<?php
include('../controllers/admin_controller.php');
require_once('../controllers/security/csrf.php');
require_once('../controllers/auth.php');

$nonce = generate_random();
add_csp_header($nonce);
$_SESSION[SessionVariables::NONCE->value] = $nonce;

try {
    $auth_controller = new AuthController();
    $auth_controller->check_user_role([Roles::ADMIN]);
} catch (Exception $exception) {
    $_SESSION[SessionVariables::GENERIC_ERROR->value] = "Error with page";
    error_log($exception->getMessage() . $exception->getTraceAsString());
} catch (Throwable $throwable) {
    $_SESSION[SessionVariables::GENERIC_ERROR->value] = "Error with page";
    error_log($throwable->getMessage() . $throwable->getTraceAsString());
}

?>

<!DOCTYPE html>
<html lang="en">
<?php
include('../includes/admin_header.php');
?>

<body>
    <div class="preloader">
        <div class="lds-ripple">
            <div class="lds-pos"></div>
            <div class="lds-pos"></div>
        </div>
    </div>
    <div id="main-wrapper">
        <?php
        include('../includes/admin_topbar.php');
        include('../includes/admin_left_navbar.php');
        ?>
        <!-- ============================================================== -->
        <div class="page-wrapper">
            <!-- Bread crumb and right sidebar toggle -->
            <?php
            include('../includes/admin_dashboard_header.php');
            ?>
            <!-- ============================================================== -->
            <div class="container-fluid">
                <!-- Create Staff Form -->
                <div class="row justify-content-center">
                    <div class="col-lg-6 col-md-8 col-sm-12">
                        <div class="card">
                            <div class="card-body">
                                <h3 class="card-title text-center m-b-30">Create Staff Account</h3>
                                <form class="form-horizontal m-t-20" action="/admin_controller" method="POST">
                                    <!-- Hidden action -->
                                    <input type="hidden" name="action" value="create_staff">
                                    <?= csrf_input() ?>
                                    <!-- Username -->
                                    <div class="form-group row">
                                        <div class="col-12">
                                            <label for="name" class="font-weight-bold">Username</label>
                                            <input class="form-control form-control-lg" type="text" id="name" name="name" required value="staff2" placeholder="Enter username">
                                        </div>
                                    </div>

                                    <!-- Display Name -->
                                    <div class="form-group row">
                                        <div class="col-12">
                                            <label for="display_name" class="font-weight-bold">Display Name</label>
                                            <input class="form-control form-control-lg" type="text" id="display_name" name="display_name" required value="Staff Two" placeholder="Enter display name">
                                        </div>
                                    </div>

                                    <!-- Email -->
                                    <div class="form-group row">
                                        <div class="col-12">
                                            <label for="email" class="font-weight-bold">Email</label>
                                            <input class="form-control form-control-lg" type="email" id="email" name="email" required value="staff2@nexabank.com" placeholder="Enter staff email">
                                        </div>
                                    </div>

                                    <!-- Date of Birth -->
                                    <div class="form-group row">
                                        <div class="col-12">
                                            <label for="dob" class="font-weight-bold">Date of Birth</label>
                                            <input class="form-control form-control-lg" type="date" id="dob" name="dob" value="1995-07-15">
                                        </div>
                                    </div>

                                    <!-- Contact -->
                                    <div class="form-group row">
                                        <div class="col-12">
                                            <label for="contact" class="font-weight-bold">Contact Number</label>
                                            <input class="form-control form-control-lg" type="text" id="contact" name="contact" value="91234567" placeholder="Enter contact number">
                                        </div>
                                    </div>

                                    <!-- Password -->
                                    <div class="form-group row">
                                        <div class="col-12">
                                            <label for="password" class="font-weight-bold">Password</label>
                                            <input class="form-control form-control-lg" type="password" id="password" name="password" required value="TestPass123!" placeholder="Enter password">
                                        </div>
                                    </div>

                                    <!-- Confirm Password -->
                                    <div class="form-group row">
                                        <div class="col-12">
                                            <label for="confirm_password" class="font-weight-bold">Confirm Password</label>
                                            <input class="form-control form-control-lg" type="password" id="confirm_password" name="confirm_password" required value="TestPass123!" placeholder="Confirm password">
                                        </div>
                                    </div>

                                    <!-- Role -->
                                    <div class="form-group row">
                                        <div class="col-12">
                                            <label for="role" class="font-weight-bold">Role</label>
                                            <select class="form-control form-control-lg" id="role" name="role" required>
                                                <option value="">-- Select Role --</option>
                                                <option value="staff" selected>Staff</option>
                                                <option value="admin">Admin</option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Submit -->
                                    <div class="form-group text-center">
                                        <div class="col-xs-12 p-b-20">
                                            <button class="btn btn-block btn-lg btn-info" type="submit">Create Staff</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Create Staff Form -->
            </div>
            <!-- End Container fluid  -->
            <?php
            include('../includes/admin_footer.php');
            ?>
            <!-- ============================================================== -->
        </div>
        <!-- End Page wrapper  -->
    </div>
    <!-- ============================================================== -->
    <?php
    include('../includes/admin_customizer_button.php');
    ?>
    <div class="chat-windows"></div>
    <!-- ============================================================== -->
    <!-- All Jquery -->
    <!-- ============================================================== -->
    <script src="../../assets/libs/jquery/dist/jquery.min.js" nonce="<?= htmlspecialchars($nonce, ENT_QUOTES) ?>"></script>
    <!-- Bootstrap tether Core JavaScript -->
    <script src="../../assets/libs/popper.js/dist/umd/popper.min.js" nonce="<?= htmlspecialchars($nonce, ENT_QUOTES) ?>"></script>
    <script src="../../assets/libs/bootstrap/dist/js/bootstrap.min.js" nonce="<?= htmlspecialchars($nonce, ENT_QUOTES) ?>"></script>
    <!-- apps -->
    <script src="../../dist/js/app.min.js" nonce="<?= htmlspecialchars($nonce, ENT_QUOTES) ?>"></script>
    <script src="../../dist/js/app.init.js" nonce="<?= htmlspecialchars($nonce, ENT_QUOTES) ?>"></script>
    <script src="../../dist/js/app-style-switcher.js" nonce="<?= htmlspecialchars($nonce, ENT_QUOTES) ?>"></script>
    <!-- slimscrollbar scrollbar JavaScript -->
    <script src="../../assets/libs/perfect-scrollbar/dist/perfect-scrollbar.jquery.min.js" nonce="<?= htmlspecialchars($nonce, ENT_QUOTES) ?>"></script>
    <script src="../../assets/extra-libs/sparkline/sparkline.js" nonce="<?= htmlspecialchars($nonce, ENT_QUOTES) ?>"></script>
    <!--Menu sidebar -->
    <script src="../../dist/js/sidebarmenu.js" nonce="<?= htmlspecialchars($nonce, ENT_QUOTES) ?>"></script>
    <!--Custom JavaScript -->
    <script src="../../dist/js/custom.min.js" nonce="<?= htmlspecialchars($nonce, ENT_QUOTES) ?>"></script>
</body>
<?php
if (isset($_SESSION["create_staff_status"])): ?>
    <script>
        alert("<?= addslashes($_SESSION["create_staff_status"]) ?>");
    </script>
    <?php unset($_SESSION["create_staff_status"]); // clear after use 
    ?>
<?php endif; ?>

</html>