<?php
require_once('../controllers/security/csrf.php');
require_once('../controllers/auth.php');
require_once('../controllers/transfer_controller.php');;

$nonce = generate_random();
add_csp_header($nonce);

try {
    $auth_controller = new AuthController();
    $auth_controller->check_user_role([Roles::USER, Roles::ADMIN], "/dashboard");

    $transfer_controller = new TransferController();
    $own_accounts = $transfer_controller->get_user_accounts();

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["transfer"]))
        $transfer_controller->process_funds_transfer($own_accounts);
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["Deposit"]))
        $transfer_controller->process_deposit_request($own_accounts);
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["Withdraw"]))
        $transfer_controller->process_withdraw_request($own_accounts);
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

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="./assets/images/favicon.png">
    <title>Nexabank | Transfer</title>
    <!-- Custom CSS -->
    <link href="./dist/css/style.min.css" rel="stylesheet" nonce="<?= htmlspecialchars($nonce, ENT_QUOTES) ?>" crossorigin="anonymous">
    <!-- Custom Js -->
    <script src="./assets/js/transfer_page.js" nonce="<?= htmlspecialchars($nonce, ENT_QUOTES) ?>" crossorigin="anonymous"></script>
</head>

<body>
    <div class="preloader">
        <div class="lds-ripple">
            <div class="lds-pos"></div>
            <div class="lds-pos"></div>
        </div>
    </div>
    <div id="main-wrapper">
        <header class="topbar">
            <nav class="navbar top-navbar navbar-expand-md navbar-dark">
                <div class="navbar-header">
                    <!-- This is for the sidebar toggle which is visible on mobile only -->
                    <a class="nav-toggler waves-effect waves-light d-block d-md-none" href="#">
                        <i class="ti-menu ti-close"></i>
                    </a>
                    <!-- Logo -->
                    <div class="navbar-brand">
                        <a href="index.html" class="logo">
                            <!-- Logo icon -->
                            <b class="logo-icon">
                                <!-- Dark Logo icon -->
                                <img src="./assets/images/logo-icon.png" alt="homepage" class="dark-logo" />
                            </b>
                            <!--End Logo icon -->
                            <!-- Logo text -->
                            <span class="logo-text">
                                <!-- dark Logo text -->
                                <img src="./assets/images/logo-text.png" alt="homepage" class="dark-logo" />
                                <!-- Light Logo text -->
                                <img src="./assets/images/logo-light-text.png" class="light-logo" alt="homepage" />
                            </span>
                        </a>
                        <a class="sidebartoggler d-none d-md-block" href="#" data-sidebartype="mini-sidebar">
                            <i class="mdi mdi-toggle-switch mdi-toggle-switch-off font-20"></i>
                        </a>
                    </div>
                    <!-- End Logo -->
                    <!-- Toggle which is visible on mobile only -->
                    <a class="topbartoggler d-block d-md-none waves-effect waves-light" href="#" data-toggle="collapse" data-target="#navbarSupportedContent"
                        aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                        <i class="ti-more"></i>
                    </a>
                </div>
                <!-- End Logo -->
                <div class="navbar-collapse collapse" id="navbarSupportedContent">
                    <!-- ============================================================== -->
                    <!-- toggle and nav items -->
                    <!-- ============================================================== -->
                    <ul class="navbar-nav float-left mr-auto">
                        <!-- <li class="nav-item d-none d-md-block">
                            <a class="nav-link sidebartoggler waves-effect waves-light" href="#" data-sidebartype="mini-sidebar">
                                <i class="mdi mdi-menu font-24"></i>
                            </a>
                        </li> -->
                        <!-- ============================================================== -->
                        <!-- Search -->
                        <!-- ============================================================== -->
                        <li class="nav-item search-box">
                            <a class="nav-link waves-effect waves-dark" href="#">
                                <div class="d-flex align-items-center">
                                    <i class="mdi mdi-magnify font-20 mr-1"></i>
                                    <div class="ml-1 d-none d-sm-block">
                                        <span>Search</span>
                                    </div>
                                </div>
                            </a>
                            <form class="app-search position-absolute">
                                <input type="text" class="form-control" placeholder="Search &amp; enter">
                                <a class="srh-btn">
                                    <i class="ti-close"></i>
                                </a>
                            </form>
                        </li>
                    </ul>
                    <!-- ============================================================== -->
                    <!-- Right side toggle and nav items -->
                    <!-- ============================================================== -->
                    <ul class="navbar-nav float-right">
                        <!-- ============================================================== -->
                        <!-- Comment -->
                        <!-- ============================================================== -->
                        <li class="nav-item dropdown border-right">
                            <a class="nav-link dropdown-toggle waves-effect waves-dark" href="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="mdi mdi-bell-outline font-22"></i>
                                <span class="badge badge-pill badge-info noti">3</span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right mailbox animated bounceInDown">
                                <span class="with-arrow">
                                    <span class="bg-primary"></span>
                                </span>
                                <ul class="list-style-none">
                                    <li>
                                        <div class="drop-title bg-primary text-white">
                                            <h4 class="m-b-0 m-t-5">4 New</h4>
                                            <span class="font-light">Notifications</span>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="message-center notifications">
                                            <!-- Message -->
                                            <a href="#" class="message-item">
                                                <span class="btn btn-danger btn-circle">
                                                    <i class="fa fa-link"></i>
                                                </span>
                                                <div class="mail-contnet">
                                                    <h5 class="message-title">Luanch Admin</h5>
                                                    <span class="mail-desc">Just see the my new admin!</span>
                                                    <span class="time">9:30 AM</span>
                                                </div>
                                            </a>
                                            <!-- Message -->
                                            <a href="#" class="message-item">
                                                <span class="btn btn-success btn-circle">
                                                    <i class="ti-calendar"></i>
                                                </span>
                                                <div class="mail-contnet">
                                                    <h5 class="message-title">Event today</h5>
                                                    <span class="mail-desc">Just a reminder that you have event</span>
                                                    <span class="time">9:10 AM</span>
                                                </div>
                                            </a>
                                            <!-- Message -->
                                            <a href="#" class="message-item">
                                                <span class="btn btn-info btn-circle">
                                                    <i class="ti-settings"></i>
                                                </span>
                                                <div class="mail-contnet">
                                                    <h5 class="message-title">Settings</h5>
                                                    <span class="mail-desc">You can customize this template as you want</span>
                                                    <span class="time">9:08 AM</span>
                                                </div>
                                            </a>
                                            <!-- Message -->
                                            <a href="#" class="message-item">
                                                <span class="btn btn-primary btn-circle">
                                                    <i class="ti-user"></i>
                                                </span>
                                                <div class="mail-contnet">
                                                    <h5 class="message-title">Pavan kumar</h5>
                                                    <span class="mail-desc">Just see the my admin!</span>
                                                    <span class="time">9:02 AM</span>
                                                </div>
                                            </a>
                                        </div>
                                    </li>
                                    <li>
                                        <a class="nav-link text-center m-b-5 text-dark" href="#;">
                                            <strong>Check all notifications</strong>
                                            <i class="fa fa-angle-right"></i>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                        <!-- ============================================================== -->
                        <!-- End Comment -->
                        <!-- ============================================================== -->
                        <!-- ============================================================== -->
                        <!-- User profile and search -->
                        <!-- ============================================================== -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle waves-effect waves-dark pro-pic" href="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <img src="./assets/images/users/2.jpg" alt="user" class="rounded-circle" width="40">
                                <span class="m-l-5 font-medium d-none d-sm-inline-block">Jonathan Doe <i class="mdi mdi-chevron-down"></i></span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right user-dd animated flipInY">
                                <span class="with-arrow">
                                    <span class="bg-primary"></span>
                                </span>
                                <div class="d-flex no-block align-items-center p-15 bg-primary text-white m-b-10">
                                    <div class="">
                                        <img src="./assets/images/users/2.jpg" alt="user" class="rounded-circle" width="60">
                                    </div>
                                    <div class="m-l-10">
                                        <h4 class="m-b-0">Jonathan Doe</h4>
                                        <p class=" m-b-0">jon@gmail.com</p>
                                    </div>
                                </div>
                                <div class="profile-dis scrollable">
                                    <a class="dropdown-item" href="#">
                                        <i class="ti-user m-r-5 m-l-5"></i> My Profile</a>
                                    <a class="dropdown-item" href="#">
                                        <i class="ti-wallet m-r-5 m-l-5"></i> My Balance</a>
                                    <a class="dropdown-item" href="#">
                                        <i class="ti-email m-r-5 m-l-5"></i> Inbox</a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="#">
                                        <i class="ti-settings m-r-5 m-l-5"></i> Account Setting</a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="#">
                                        <i class="fa fa-power-off m-r-5 m-l-5"></i> Logout</a>
                                    <div class="dropdown-divider"></div>
                                </div>
                                <div class="p-l-30 p-10">
                                    <a href="#" class="btn btn-sm btn-success btn-rounded">View Profile</a>
                                </div>
                            </div>
                        </li>
                        <!-- ============================================================== -->
                        <!-- User profile and search -->
                        <!-- ============================================================== -->
                    </ul>
                </div>
            </nav>
        </header>
        <aside class="left-sidebar">
            <!-- Sidebar scroll-->
            <div class="scroll-sidebar">
                <!-- Sidebar navigation-->
                <nav class="sidebar-nav">
                    <ul id="sidebarnav">
                        <li class="nav-small-cap">
                            <i class="mdi mdi-dots-horizontal"></i>
                            <span class="hide-menu">Personal</span>
                        </li>
                        <li class="sidebar-item">
                            <a class="sidebar-link has-arrow waves-effect waves-dark" href="#" aria-expanded="false">
                                <i class="mdi mdi-av-timer"></i>
                                <span class="hide-menu">Dashboard </span>
                                <span class="badge badge-pill badge-info ml-auto m-r-15">3</span>
                            </a>
                            <ul aria-expanded="false" class="collapse  first-level">
                                <li class="sidebar-item">
                                    <a href="index.html" class="sidebar-link">
                                        <i class="mdi mdi-adjust"></i>
                                        <span class="hide-menu"> Classic </span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="index2.html" class="sidebar-link">
                                        <i class="mdi mdi-adjust"></i>
                                        <span class="hide-menu"> Analytical </span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="index3.html" class="sidebar-link">
                                        <i class="mdi mdi-adjust"></i>
                                        <span class="hide-menu"> Modern </span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="sidebar-item">
                            <a class="sidebar-link has-arrow waves-effect waves-dark" href="#" aria-expanded="false">
                                <i class="mdi mdi-tune"></i>
                                <span class="hide-menu">Sidebar Type </span>
                            </a>
                            <ul aria-expanded="false" class="collapse  first-level">
                                <li class="sidebar-item">
                                    <a href="sidebar-type-minisidebar.html" class="sidebar-link">
                                        <i class="mdi mdi-view-quilt"></i>
                                        <span class="hide-menu"> Minisidebar </span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="sidebar-type-iconsidebar.html" class="sidebar-link">
                                        <i class="mdi mdi-view-parallel"></i>
                                        <span class="hide-menu"> Icon Sidebar </span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="sidebar-type-overlaysidebar.html" class="sidebar-link">
                                        <i class="mdi mdi-view-day"></i>
                                        <span class="hide-menu"> Overlay Sidebar </span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="sidebar-type-fullsidebar.html" class="sidebar-link">
                                        <i class="mdi mdi-view-array"></i>
                                        <span class="hide-menu"> Full Sidebar </span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="sidebar-type-horizontalsidebar.html" class="sidebar-link">
                                        <i class="mdi mdi-view-module"></i>
                                        <span class="hide-menu"> Horizontal Sidebar </span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="sidebar-item">
                            <a class="sidebar-link has-arrow waves-effect waves-dark" href="#" aria-expanded="false">
                                <i class="mdi mdi-crop-square"></i>
                                <span class="hide-menu">Page Layouts </span>
                            </a>
                            <ul aria-expanded="false" class="collapse  first-level">
                                <li class="sidebar-item">
                                    <a href="layout-inner-fixed-left-sidebar.html" class="sidebar-link">
                                        <i class="mdi mdi-format-align-left"></i>
                                        <span class="hide-menu"> Inner Fixed Left Sidebar </span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="layout-inner-fixed-right-sidebar.html" class="sidebar-link">
                                        <i class="mdi mdi-format-align-right"></i>
                                        <span class="hide-menu"> Inner Fixed Right Sidebar </span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="layout-inner-left-sidebar.html" class="sidebar-link">
                                        <i class="mdi mdi-format-float-left"></i>
                                        <span class="hide-menu"> Inner Left Sidebar </span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="layout-inner-right-sidebar.html" class="sidebar-link">
                                        <i class="mdi mdi-format-float-right"></i>
                                        <span class="hide-menu"> Inner Right Sidebar </span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="page-layout-fixed-header.html" class="sidebar-link">
                                        <i class="mdi mdi-view-quilt"></i>
                                        <span class="hide-menu"> Fixed Header </span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="page-layout-fixed-sidebar.html" class="sidebar-link">
                                        <i class="mdi mdi-view-parallel"></i>
                                        <span class="hide-menu"> Fixed Sidebar </span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="page-layout-fixed-header-sidebar.html" class="sidebar-link">
                                        <i class="mdi mdi-view-column"></i>
                                        <span class="hide-menu"> Fixed Header &amp; Sidebar </span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a href="page-layout-boxed-layout.html" class="sidebar-link">
                                        <i class="mdi mdi-view-carousel"></i>
                                        <span class="hide-menu"> Box Layout </span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </nav>
                <!-- End Sidebar navigation -->
            </div>
            <!-- End Sidebar scroll-->
        </aside>
        <!-- End of Nav -->
        <div class="page-wrapper">
            <!-- Bread crumb and right sidebar toggle -->
            <div class="page-breadcrumb">
                <div class="row">
                    <div class="col-5 align-self-center">
                        <h4 class="page-title">Fund Management</h4>
                    </div>
                    <div class="col-7 align-self-center">
                        <div class="d-flex align-items-center justify-content-end">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item">
                                        <a href="#">Home</a>
                                    </li>
                                    <li class="breadcrumb-item active" aria-current="page">Fund Management</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
            <div class="container-fluid">
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="row">
                        <div class="col-12">
                            <div class="alert alert-warning"> <?php echo ($_SESSION[SessionVariables::GENERIC_ERROR->value]);
                                                                unset($_SESSION[SessionVariables::GENERIC_ERROR->value]); ?>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">x</span> </button>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-header bg-info">
                                <h4 class="m-b-0 text-white">Transfer funds</h4>
                            </div>
                            <form name="Transfer_own_accounts" action="/transfer" method="POST">
                                <div class="card-body">
                                    <?php if (isset($_SESSION[SessionVariables::TRANSFER_ERROR->value])): ?>
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="alert alert-warning"> <?php echo ($_SESSION[SessionVariables::TRANSFER_ERROR->value]);
                                                                                    unset($_SESSION[SessionVariables::TRANSFER_ERROR->value]); ?>
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
                                    <h4 class="card-title">Transfer</h4>
                                    <div class="form-body">
                                        <div class="col-sm-12 col-xs-12 m-s-5">
                                            <form class="input-form">
                                                <div class="row">
                                                    <div class="col-lg-6">
                                                        <div class="input-group mb-3">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">FROM: </span>
                                                            </div>
                                                            <input name="FROM_account" type="text" class="form-control select-textbox" aria-label="Text input with dropdown button" maxlength="30">
                                                            <div class="input-group-append">
                                                                <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Select</button>
                                                                <div class="dropdown-menu">
                                                                    <a class='dropdown-item select-btn'>Select</a>
                                                                    <?php
                                                                    foreach ($own_accounts as $account) {
                                                                        echo "<a class='dropdown-item select-btn'>$account</a>";
                                                                    }
                                                                    ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-lg-6">
                                                        <div class="input-group mb-3">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">TO: </span>
                                                            </div>
                                                            <input name="TO_account" type="text" class="form-control select-textbox" aria-label="Text input with dropdown button" maxlength="30">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-lg-6 input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">$</span>
                                                        </div>
                                                        <input type="text" name="Amount" class="form-control" placeholder="0.00" maxlength="14">
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                        <div class="form-actions">
                                            <div class="card-body">
                                                <button type="submit" class="btn btn-success" name="transfer" value="transfer"> <i class="ti-exchange-vertical mr-2"></i>Transfer</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-header bg-info">
                                <h4 class="m-b-0 text-white">Deposit funds</h4>
                            </div>
                            <form name="Transfer_own_accounts" action="/transfer" method="POST">
                                <div class="card-body">
                                    <?php if (isset($_SESSION['transfer_self_error'])): ?>
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="alert alert-warning"> <?php echo ($_SESSION[SessionVariables::DEPOSIT_ERROR->value]);
                                                                                    unset($_SESSION[SessionVariables::DEPOSIT_ERROR->value]); ?>
                                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">x</span> </button>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    <?php if (isset($_SESSION[SessionVariables::DEPOSIT_SUCCESS->value])): ?>
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="alert alert-success"> <?php echo ($_SESSION[SessionVariables::DEPOSIT_SUCCESS->value]);
                                                                                    unset($_SESSION[SessionVariables::DEPOSIT_SUCCESS->value]); ?>
                                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">x</span> </button>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    <h4 class="card-title">Deposit to account</h4>
                                    <div class="form-body">
                                        <div class="col-sm-12 col-xs-12 m-s-5">
                                            <form class="input-form">
                                                <div class="row">
                                                    <div class="col-lg-6">
                                                        <div class="input-group mb-3">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">TO: </span>
                                                            </div>
                                                            <input name="TO_account" type="text" class="form-control select-textbox" aria-label="Text input with dropdown button">
                                                            <div class="input-group-append">
                                                                <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Select</button>
                                                                <div class="dropdown-menu">
                                                                    <a class='dropdown-item select-btn'>Select</a>
                                                                    <?php
                                                                    foreach ($own_accounts as $account) {
                                                                        echo "<a class='dropdown-item select-btn'>$account</a>";
                                                                    }
                                                                    ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-lg-6 input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">$</span>
                                                        </div>
                                                        <input type="text" name="Amount" class="form-control" placeholder="0.00">
                                                    </div>
                                                </div>
                                                <hr/>
                                                <h5 class="card-title">Card details</h5>
                                                <div class="row">
                                                    <div class="col-lg-4">
                                                        <div class="input-group mb-3">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">Card number: </span>
                                                            </div>
                                                            <input name="Card_Number" type="text" class="form-control select-textbox" aria-label="Text input with dropdown button">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-lg-2 col-md-3 col-sm-6">
                                                        <div class="input-group mb-3">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">CVV: </span>
                                                            </div>
                                                            <input name="Card_CVV" type="text" class="form-control select-textbox" aria-label="Text input with dropdown button">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-2 col-md-3 col-sm-6">
                                                        <div class="input-group mb-3">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">Expiry: </span>
                                                            </div>
                                                            <input name="Card_Expiry_Month" type="text" class="form-control select-textbox" aria-label="Text input with dropdown button">
                                                            <div class="form-control m-0">/</div>
                                                            <input name="Card_Expiry_Year" type="text" class="form-control select-textbox" aria-label="Text input with dropdown button">
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                        <div class="form-actions">
                                            <div class="card-body">
                                                <button type="submit" class="btn btn-success" name="Deposit" value="Deposit"> <i class="mdi mdi-arrow-down-box mr-2"></i>Deposit</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-header bg-info">
                                <h4 class="m-b-0 text-white">Withdraw funds</h4>
                            </div>
                            <form name="Transfer_own_accounts" action="/transfer" method="POST">
                                <div class="card-body">
                                    <?php if (isset($_SESSION[SessionVariables::WITHDRAW_ERROR->value])): ?>
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="alert alert-warning"> <?php echo ($_SESSION[SessionVariables::WITHDRAW_ERROR->value]);
                                                                                    unset($_SESSION[SessionVariables::WITHDRAW_ERROR->value]); ?>
                                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">x</span> </button>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    <?php if (isset($_SESSION[SessionVariables::WITHDRAW_SUCCESS->value])): ?>
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="alert alert-success"> <?php echo ($_SESSION[SessionVariables::WITHDRAW_SUCCESS->value]);
                                                                                    unset($_SESSION[SessionVariables::WITHDRAW_SUCCESS->value]); ?>
                                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">x</span> </button>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    <h4 class="card-title">Withdraw from account</h4>
                                    <div class="form-body">
                                        <div class="col-sm-12 col-xs-12 m-s-5">
                                            <form class="input-form">
                                                <div class="row">
                                                    <div class="col-lg-6">
                                                        <div class="input-group mb-3">
                                                            <div class="input-group-prepend">
                                                                <span class="input-group-text">FROM: </span>
                                                            </div>
                                                            <input name="FROM_account" type="text" class="form-control select-textbox" aria-label="Text input with dropdown button">
                                                            <div class="input-group-append">
                                                                <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Select</button>
                                                                <div class="dropdown-menu">
                                                                    <a class='dropdown-item select-btn'>Select</a>
                                                                    <?php
                                                                    foreach ($own_accounts as $account) {
                                                                        echo "<a class='dropdown-item select-btn'>$account</a>";
                                                                    }
                                                                    ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-lg-6 input-group mb-3">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">$</span>
                                                        </div>
                                                        <input type="text" name="Amount" class="form-control" placeholder="0.00">
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                        <div class="form-actions">
                                            <div class="card-body">
                                                <button type="submit" class="btn btn-success" name="Withdraw" value="Withdraw"> <i class="mdi mdi-arrow-up-box mr-2"></i>Withdraw</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- All Required js -->
    <script src="./assets/libs/jquery/dist/jquery.min.js" nonce="<?= htmlspecialchars($nonce, ENT_QUOTES) ?>" crossorigin="anonymous"></script>
    <!-- Bootstrap tether Core JavaScript -->
    <script src="./assets/libs/popper.js/dist/umd/popper.min.js" nonce="<?= htmlspecialchars($nonce, ENT_QUOTES) ?>" crossorigin="anonymous"></script>
    <script src="./assets/libs/bootstrap/dist/js/bootstrap.min.js" nonce="<?= htmlspecialchars($nonce, ENT_QUOTES) ?>" crossorigin="anonymous"></script>
    <!-- apps -->
    <script src="./dist/js/app.min.js" nonce="<?= htmlspecialchars($nonce, ENT_QUOTES) ?>" crossorigin="anonymous"></script>
    <script src="./dist/js/app.init.js" nonce="<?= htmlspecialchars($nonce, ENT_QUOTES) ?>" crossorigin="anonymous"></script>
    <script src="./dist/js/app-style-switcher.js" nonce="<?= htmlspecialchars($nonce, ENT_QUOTES) ?>" crossorigin="anonymous"></script>
    <!-- slimscrollbar scrollbar JavaScript -->
    <script src="./assets/libs/perfect-scrollbar/dist/perfect-scrollbar.jquery.min.js" nonce="<?= htmlspecialchars($nonce, ENT_QUOTES) ?>" crossorigin="anonymous"></script>
    <script src="./assets/extra-libs/sparkline/sparkline.js" nonce="<?= htmlspecialchars($nonce, ENT_QUOTES) ?>" crossorigin="anonymous"></script>
    <!--Wave Effects -->
    <script src="./dist/js/waves.js" nonce="<?= htmlspecialchars($nonce, ENT_QUOTES) ?>" crossorigin="anonymous"></script>
    <!--Menu sidebar -->
    <script src="./dist/js/sidebarmenu.js" nonce="<?= htmlspecialchars($nonce, ENT_QUOTES) ?>" crossorigin="anonymous"></script>
    <!--Custom JavaScript -->
    <script src="./dist/js/custom.min.js" nonce="<?= htmlspecialchars($nonce, ENT_QUOTES) ?>" crossorigin="anonymous"></script>

</html>