<?php
require_once('../controllers/security/csrf.php');
require_once('../controllers/auth.php');

$nonce = generate_random();
add_csp_header($nonce);

try {
    $auth_controller = new AuthController();
    $auth_controller->check_user_role([Roles::USER]);
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
    <link nonce="<?= htmlspecialchars($nonce, ENT_QUOTES) ?>" href="assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link nonce="<?= htmlspecialchars($nonce, ENT_QUOTES) ?>" href="dist/css/style.min.css" rel="stylesheet">
</head>

<body>
    <!-- ============================================================== -->
    <!-- Preloader - style you can find in spinners.css -->
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
                        <a href="/" class="logo">
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
                        <h4 class="page-title">Dashboard</h4>
                    </div>
                </div>
            </div>
            <!-- ============================================================== -->
            <div class="container-fluid">
                <!-- Start Page Content -->
                <!-- Column rendering for balances -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Account Balances</h4>
                                <h6 class="card-subtitle"></h6>
                                <div class="table-responsive">
                                    <table id="balances_table" class="table table-striped table-bordered display"
                                        style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>Customer</th>
                                                <th>Account ID</th>
                                                <th>Account Type</th>
                                                <th>Balance (SGD)</th>
                                            </tr>
                                        </thead>
                                    </table>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Column rendering for transactions -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Transaction History</h4>
                                <div class="table-responsive">
                                    <table id="col_render" class="table table-striped table-bordered display"
                                        style="width:100%">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Reference Number</th>
                                                <th>From (Customer)</th>
                                                <th>From Account Type</th>
                                                <th>To (Customer)</th>
                                                <th>To Account Type</th>
                                                <th>Amount ($)</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- ============================================================== -->
            </div>
        </div>
    </div>
    <!-- ============================================================== -->
    <script nonce="<?= htmlspecialchars($nonce, ENT_QUOTES) ?>" src="../../assets/libs/jquery/dist/jquery.min.js"></script>
    <!-- Bootstrap tether Core JavaScript -->
    <script nonce="<?= htmlspecialchars($nonce, ENT_QUOTES) ?>" src="../../assets/libs/popper.js/dist/umd/popper.min.js"></script>
    <script nonce="<?= htmlspecialchars($nonce, ENT_QUOTES) ?>" src="../../assets/libs/bootstrap/dist/js/bootstrap.min.js"></script>
    <!-- apps -->
    <script nonce="<?= htmlspecialchars($nonce, ENT_QUOTES) ?>" src="../../dist/js/app.min.js"></script>
    <script nonce="<?= htmlspecialchars($nonce, ENT_QUOTES) ?>" src="../../dist/js/app.init.horizontal.js"></script>
    <script nonce="<?= htmlspecialchars($nonce, ENT_QUOTES) ?>" src="../../dist/js/app-style-switcher.horizontal.js"></script>
    <!-- slimscrollbar scrollbar JavaScript -->
    <script nonce="<?= htmlspecialchars($nonce, ENT_QUOTES) ?>" src="../../assets/libs/perfect-scrollbar/dist/perfect-scrollbar.jquery.min.js"></script>
    <script nonce="<?= htmlspecialchars($nonce, ENT_QUOTES) ?>" src="../../assets/extra-libs/sparkline/sparkline.js"></script>
    <!--Wave Effects -->
    <script nonce="<?= htmlspecialchars($nonce, ENT_QUOTES) ?>" src="../../dist/js/waves.js"></script>
    <!--Custom JavaScript -->
    <script nonce="<?= htmlspecialchars($nonce, ENT_QUOTES) ?>" src="../../dist/js/custom.min.js"></script>
    <!--This page plugins -->
    <script nonce="<?= htmlspecialchars($nonce, ENT_QUOTES) ?>" src="../../assets/extra-libs/DataTables/datatables.min.js"></script>
   
    <script nonce="<?= htmlspecialchars($nonce, ENT_QUOTES) ?>" src="../../dist/js/pages/datatable/datatable-advanced.init.js"></script>

</body>
