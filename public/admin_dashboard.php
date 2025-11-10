<?php
require_once('../controllers/security/csrf.php');
require_once('../controllers/auth.php');
require_once('../controllers/admin_controller.php');

$nonce = generate_random();
add_csp_header($nonce);
$_SESSION[SessionVariables::NONCE->value] = $nonce;

try {
    $auth_controller = new AuthController();
    $auth_controller->check_user_role([Roles::ADMIN]);

    $controller = new admin_controller();
    $stats = $controller->getUserStats();
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

<?php include('../includes/admin_header.php'); ?>

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
            <?php include('../includes/admin_dashboard_header.php'); ?>
            <!-- ============================================================== -->
            <div class="container-fluid">
                <!-- ============================================================== -->
                <div class="card-group">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="d-flex no-block align-items-center">
                                        <div>
                                            <i class="mdi mdi-emoticon font-20 text-muted"></i>
                                            <p class="font-16 m-b-5">New Clients</p>
                                        </div>
                                        <div class="ml-auto">
                                            <h1 class="font-light text-right">23</h1>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="progress">
                                        <div class="progress-bar bg-info" role="progressbar" style="width: 75%; height: 6px;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Column -->
                    <!-- Column -->
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="d-flex no-block align-items-center">
                                        <div>
                                            <i class="mdi mdi-image font-20  text-muted"></i>
                                            <p class="font-16 m-b-5">New Projects</p>
                                        </div>
                                        <div class="ml-auto">
                                            <h1 class="font-light text-right">169</h1>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="progress">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: 60%; height: 6px;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Column -->
                    <!-- Column -->
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="d-flex no-block align-items-center">
                                        <div>
                                            <i class="mdi mdi-currency-eur font-20 text-muted"></i>
                                            <p class="font-16 m-b-5">New Invoices</p>
                                        </div>
                                        <div class="ml-auto">
                                            <h1 class="font-light text-right">157</h1>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="progress">
                                        <div class="progress-bar bg-purple" role="progressbar" style="width: 65%; height: 6px;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Column -->
                    <!-- Column -->
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="d-flex no-block align-items-center">
                                        <div>
                                            <i class="mdi mdi-poll font-20 text-muted"></i>
                                            <p class="font-16 m-b-5">New Sales</p>
                                        </div>
                                        <div class="ml-auto">
                                            <h1 class="font-light text-right">236</h1>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="progress">
                                        <div class="progress-bar bg-danger" role="progressbar" style="width: 70%; height: 6px;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- ============================================================== -->
                <div class="row">
                    <div class="col-lg-3 col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Campaign</h4>
                                <div id="campaign" style="height: 168px; width: 100%;" class="m-t-10"></div>
                                <!-- row -->
                                <div class="row text-center text-lg-left">
                                    <!-- column -->
                                    <div class="col-4">
                                        <h4 class="m-b-0 font-medium">60%</h4>
                                        <span class="text-muted">Open</span>
                                    </div>
                                    <!-- column -->
                                    <div class="col-4">
                                        <h4 class="m-b-0 font-medium">26%</h4>
                                        <span class="text-muted">Click</span>
                                    </div>
                                    <!-- column -->
                                    <div class="col-4">
                                        <h4 class="m-b-0 font-medium">18%</h4>
                                        <span class="text-muted">Bounce</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title m-b-5">Referral Earnings</h5>
                                <h3 class="font-light">$769.08</h3>
                                <div class="m-t-20 text-center">
                                    <div id="earnings"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-12 order-lg-2 order-md-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div>
                                        <h4 class="card-title">Sales Ratio</h4>
                                    </div>
                                    <div class="ml-auto">
                                        <div class="dl m-b-10">
                                            <select class="custom-select border-0 text-muted">
                                                <option value="0" selected="">August 2018</option>
                                                <option value="1">May 2018</option>
                                                <option value="2">March 2018</option>
                                                <option value="3">June 2018</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center no-block">
                                    <div>
                                        <span class="text-muted">This Week</span>
                                        <h3 class="mb-0 text-info font-light">$23.5K <span class="text-muted font-12"><i class="mdi mdi-arrow-up text-success"></i>18.6</span></h3>
                                    </div>
                                    <div class="ml-4">
                                        <span class="text-muted">Last Week</span>
                                        <h3 class="mb-0 text-muted font-light">$945 <span class="text-muted font-12"><i class="mdi mdi-arrow-down text-danger"></i>46.3</span></h3>
                                    </div>
                                </div>
                                <div class="sales ct-charts mt-5"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 order-lg-3 order-md-2">
                        <div class="card">
                            <div class="card-body m-b-0">
                                <h4 class="card-title">Thursday <span class="font-14 font-normal text-muted">12th April, 2018</span></h4>
                                <div class="d-flex align-items-center flex-row m-t-30">
                                    <h1 class="font-light"><i class="wi wi-day-sleet"></i> <span>35<sup>°</sup></span></h1>
                                </div>
                            </div>
                            <div class="weather-report" style="height:120px; width:100%;"></div>
                        </div>
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title m-b-0">Users</h4>
                                <h2 class="font-light"><?php echo number_format($stats['count']); ?></h2>
                                <div class="m-t-30">
                                    <div class="row text-center">
                                        <div class="col-6 border-right">
                                            <h4 class="m-b-0">58%</h4>
                                            <span class="font-14 text-muted">Staff</span>
                                        </div>
                                        <div class="col-6">
                                            <h4 class="m-b-0">42%</h4>
                                            <span class="font-14 text-muted">Customers</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- ============================================================== -->
            </div>
            <!-- ============================================================== -->
            <?php
            include('../includes/admin_footer.php');
            ?>
            <!-- ============================================================== -->
        </div>
        <!-- ============================================================== -->
    </div>
    <!-- ============================================================== -->
    <?php
    include('../includes/admin_customizer_button.php');
    ?>
    <div class="chat-windows"></div>
    <!-- ============================================================== -->
    <!-- All Jquery -->
    <!-- ============================================================== -->
    <script src="../../assets/libs/jquery/dist/jquery.min.js"  nonce="<?= htmlspecialchars($nonce, ENT_QUOTES) ?>"></script>
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
    <!--Wave Effects -->
    <script src="../../dist/js/waves.js" nonce="<?= htmlspecialchars($nonce, ENT_QUOTES) ?>"></script>
    <!--Menu sidebar -->
    <script src="../../dist/js/sidebarmenu.js" nonce="<?= htmlspecialchars($nonce, ENT_QUOTES) ?>"></script>
    <!--Custom JavaScript -->
    <script src="../../dist/js/custom.min.js" nonce="<?= htmlspecialchars($nonce, ENT_QUOTES) ?>"></script>
    <!--This page JavaScript -->
    <!--chartis chart-->
    <script src="../../assets/libs/chartist/dist/chartist.min.js" nonce="<?= htmlspecialchars($nonce, ENT_QUOTES) ?>"></script>
    <script src="../../assets/libs/chartist-plugin-tooltips/dist/chartist-plugin-tooltip.min.js" nonce="<?= htmlspecialchars($nonce, ENT_QUOTES) ?>"></script>
    <!--c3 charts -->
    <script src="../../assets/extra-libs/c3/d3.min.js" nonce="<?= htmlspecialchars($nonce, ENT_QUOTES) ?>"></script>
    <script src="../../assets/extra-libs/c3/c3.min.js" nonce="<?= htmlspecialchars($nonce, ENT_QUOTES) ?>"></script>
    <script src="../../assets/extra-libs/jvector/jquery-jvectormap-2.0.2.min.js" nonce="<?= htmlspecialchars($nonce, ENT_QUOTES) ?>"></script>
    <script src="../../assets/extra-libs/jvector/jquery-jvectormap-world-mill-en.js" nonce="<?= htmlspecialchars($nonce, ENT_QUOTES) ?>"></script>
    <script src="../../dist/js/pages/dashboards/dashboard1.js" nonce="<?= htmlspecialchars($nonce, ENT_QUOTES) ?>"></script>
</body>

</html>