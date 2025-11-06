<?php
include('../controllers/admin_controller.php');
//error_log(json_encode($_SESSION));
?>


<!DOCTYPE html>
<html dir="ltr" lang="en">

<?php
        include('../includes/admin_header.php');
?>


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
    <!-- Main wrapper - style you can find in pages.scss -->
    <!-- ============================================================== -->
    <div id="main-wrapper">
        <!-- ============================================================== -->
        <!-- Topbar header - style you can find in pages.scss -->
        <!-- ============================================================== -->
        <?php
        include('../includes/admin_topbar.php');
        include('../includes/admin_left_navbar.php');
        ?>
        <!-- ============================================================== -->
        <!-- End Left Sidebar - style you can find in sidebar.scss  -->
        <!-- ============================================================== -->
        <!-- ============================================================== -->
        <!-- Page wrapper  -->
        <!-- ============================================================== -->
        <div class="page-wrapper">
            <!-- ============================================================== -->
            <!-- Bread crumb and right sidebar toggle -->
            <!-- ============================================================== -->
            <?php
                include('../includes/admin_dashboard_header.php');
            ?>
            <!-- ============================================================== -->
            <!-- End Bread crumb and right sidebar toggle -->
            <!-- ============================================================== -->
            <!-- ============================================================== -->
            <!-- Container fluid  -->
            <!-- ============================================================== -->
            <div class="container-fluid">
               <!-- ============================================================== -->
                <!-- Create Staff Form -->
                <!-- ============================================================== -->
                <div class="row justify-content-center">
                    <div class="col-lg-6 col-md-8 col-sm-12">
                        <div class="card">
                            <div class="card-body">
                                <h3 class="card-title text-center m-b-30">Create Staff Account</h3>

                                <form class="form-horizontal m-t-20" action="/admin_controller" method="POST">
                                    <!-- Hidden action -->
                                    <input type="hidden" name="action" value="create_staff">

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
                <!-- ============================================================== -->
                <!-- End Create Staff Form -->
                <!-- ============================================================== -->
            </div>
            <!-- ============================================================== -->
            <!-- End Container fluid  -->
            <!-- ============================================================== -->
            <!-- ============================================================== -->
            <!-- footer -->
            <!-- ============================================================== -->
            <?php
                include('../includes/admin_footer.php');
            ?>
            <!-- ============================================================== -->
            <!-- End footer -->
            <!-- ============================================================== -->
        </div>
        <!-- ============================================================== -->
        <!-- End Page wrapper  -->
        <!-- ============================================================== -->
    </div>
    <!-- ============================================================== -->
    <!-- End Wrapper -->
    <!-- ============================================================== -->
    <!-- ============================================================== -->
    <!-- customizer Panel -->
    <!-- ============================================================== -->
    <?php
        include('../includes/admin_customizer_button.php');
    ?>
    <div class="chat-windows"></div>
    <!-- ============================================================== -->
    <!-- All Jquery -->
    <!-- ============================================================== -->
    <script src="../../assets/libs/jquery/dist/jquery.min.js"></script>
    <!-- Bootstrap tether Core JavaScript -->
    <script src="../../assets/libs/popper.js/dist/umd/popper.min.js"></script>
    <script src="../../assets/libs/bootstrap/dist/js/bootstrap.min.js"></script>
    <!-- apps -->
    <script src="../../dist/js/app.min.js"></script>
    <script src="../../dist/js/app.init.js"></script>
    <script src="../../dist/js/app-style-switcher.js"></script>
    <!-- slimscrollbar scrollbar JavaScript -->
    <script src="../../assets/libs/perfect-scrollbar/dist/perfect-scrollbar.jquery.min.js"></script>
    <script src="../../assets/extra-libs/sparkline/sparkline.js"></script>
    <!--Wave Effects -->
    <script src="../../dist/js/waves.js"></script>
    <!--Menu sidebar -->
    <script src="../../dist/js/sidebarmenu.js"></script>
    <!--Custom JavaScript -->
    <script src="../../dist/js/custom.min.js"></script>
    <!--This page JavaScript -->
    <!--chartis chart-->
    <script src="../../assets/libs/chartist/dist/chartist.min.js"></script>
    <script src="../../assets/libs/chartist-plugin-tooltips/dist/chartist-plugin-tooltip.min.js"></script>
    <!--c3 charts -->
    <script src="../../assets/extra-libs/c3/d3.min.js"></script>
    <script src="../../assets/extra-libs/c3/c3.min.js"></script>
    <script src="../../assets/extra-libs/jvector/jquery-jvectormap-2.0.2.min.js"></script>
    <script src="../../assets/extra-libs/jvector/jquery-jvectormap-world-mill-en.js"></script>
    <script src="../../dist/js/pages/dashboards/dashboard1.js"></script>
</body>
<?php
if (isset($_SESSION["create_staff_status"])): ?>
    <script>
        alert("<?= addslashes($_SESSION["create_staff_status"]) ?>");
    </script>
    <?php unset($_SESSION["create_staff_status"]); // clear after use ?>
<?php endif; ?>

</html>