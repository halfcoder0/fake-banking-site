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
        <aside class="left-sidebar">
            <!-- Sidebar scroll-->
            <div class="scroll-sidebar">
                <!-- Sidebar navigation-->
                <nav class="sidebar-nav">
                    <ul id="sidebarnav">
                        <!-- User Profile-->
                        <li class="nav-small-cap"><i class="mdi mdi-dots-horizontal"></i> <span
                                class="hide-menu">Personal</span></li>
                        <li class="sidebar-item"> <a class="sidebar-link has-arrow waves-effect waves-dark"
                                href="javascript:void(0)" aria-expanded="false"><i class="mdi mdi-av-timer"></i><span
                                    class="hide-menu">Dashboard </span></a>
                            <ul aria-expanded="false" class="collapse  first-level">
                                <li class="sidebar-item"><a href="index.html" class="sidebar-link"><i
                                            class="mdi mdi-adjust"></i><span class="hide-menu"> Classic </span></a></li>
                                <li class="sidebar-item"><a href="index2.html" class="sidebar-link"><i
                                            class="mdi mdi-adjust"></i><span class="hide-menu"> Analytical </span></a>
                                </li>
                                <li class="sidebar-item"><a href="index3.html" class="sidebar-link"><i
                                            class="mdi mdi-adjust"></i><span class="hide-menu"> Cryptocurrency
                                        </span></a></li>
                            </ul>
                        </li>
                        <li class="nav-small-cap"><i class="mdi mdi-dots-horizontal"></i> <span
                                class="hide-menu">Apps</span></li>
                        <li class="sidebar-item"> <a class="sidebar-link two-column has-arrow waves-effect waves-dark"
                                href="javascript:void(0)" aria-expanded="false"><i
                                    class="mdi mdi-inbox-arrow-down"></i><span class="hide-menu">Apps </span></a>
                            <ul aria-expanded="false" class="collapse first-level">
                                <li class="sidebar-item"><a href="inbox-email.html" class="sidebar-link"><i
                                            class="mdi mdi-email"></i><span class="hide-menu"> Email </span></a></li>
                                <li class="sidebar-item"><a href="inbox-email-detail.html" class="sidebar-link"><i
                                            class="mdi mdi-email-alert"></i><span class="hide-menu"> Email Detail
                                        </span></a></li>
                                <li class="sidebar-item"><a href="inbox-email-compose.html" class="sidebar-link"><i
                                            class="mdi mdi-email-secure"></i><span class="hide-menu"> Email Compose
                                        </span></a></li>
                                <li class="sidebar-item"><a href="ticket-list.html" class="sidebar-link"><i
                                            class="mdi mdi-book-multiple"></i><span class="hide-menu"> Ticket List
                                        </span></a></li>
                                <li class="sidebar-item"><a href="ticket-detail.html" class="sidebar-link"><i
                                            class="mdi mdi-book-plus"></i><span class="hide-menu"> Ticket Detail
                                        </span></a></li>
                                <li class="sidebar-item"><a href="app-chats.html" class="sidebar-link"><i
                                            class="mdi mdi-comment-processing-outline"></i><span class="hide-menu">
                                            Chats Apps </span></a></li>
                                <li class="sidebar-item"><a href="app-calendar.html" class="sidebar-link"><i
                                            class="mdi mdi-calendar-range"></i><span class="hide-menu"> Calender
                                        </span></a></li>
                                <li class="sidebar-item"><a href="app-taskboard.html" class="sidebar-link"><i
                                            class="ti-menu-alt"></i><span class="hide-menu"> Taskboard </span></a></li>
                            </ul>
                        </li>
                        <li class="nav-small-cap"><i class="mdi mdi-dots-horizontal"></i> <span
                                class="hide-menu">UI</span></li>
                        <li class="sidebar-item mega-dropdown"> <a
                                class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)"
                                aria-expanded="false"><i class="mdi mdi-widgets"></i><span class="hide-menu">Ui
                                </span></a>
                            <ul aria-expanded="false" class="collapse first-level">
                                <li class="sidebar-item"><a href="ui-buttons.html" class="sidebar-link"><i
                                            class="mdi mdi-toggle-switch"></i><span class="hide-menu">
                                            Buttons</span></a></li>
                                <li class="sidebar-item"><a href="ui-modals.html" class="sidebar-link"><i
                                            class="mdi mdi-tablet"></i><span class="hide-menu"> Modals</span></a></li>
                                <li class="sidebar-item"><a href="ui-tab.html" class="sidebar-link"><i
                                            class="mdi mdi-sort-variant"></i><span class="hide-menu"> Tab</span></a>
                                </li>
                                <li class="sidebar-item"><a href="ui-tooltip-popover.html" class="sidebar-link"><i
                                            class="mdi mdi-image-filter-vintage"></i><span class="hide-menu"> Tooltip
                                            &amp; Popover</span></a></li>
                                <li class="sidebar-item"><a href="ui-notification.html" class="sidebar-link"><i
                                            class="mdi mdi-message-bulleted"></i><span class="hide-menu">
                                            Notification</span></a></li>
                                <li class="sidebar-item"><a href="ui-progressbar.html" class="sidebar-link"><i
                                            class="mdi mdi-poll"></i><span class="hide-menu"> Progressbar</span></a>
                                </li>
                                <li class="sidebar-item"><a href="ui-typography.html" class="sidebar-link"><i
                                            class="mdi mdi-format-line-spacing"></i><span class="hide-menu">
                                            Typography</span></a></li>
                                <li class="sidebar-item"><a href="ui-bootstrap.html" class="sidebar-link"><i
                                            class="mdi mdi-bootstrap"></i><span class="hide-menu"> Bootstrap
                                            Ui</span></a></li>
                                <li class="sidebar-item"><a href="ui-breadcrumb.html" class="sidebar-link"><i
                                            class="mdi mdi-equal"></i><span class="hide-menu"> Breadcrumb</span></a>
                                </li>
                                <li class="sidebar-item"><a href="ui-list-media.html" class="sidebar-link"><i
                                            class="mdi mdi-file-video"></i><span class="hide-menu"> List
                                            Media</span></a></li>
                                <li class="sidebar-item"><a href="ui-grid.html" class="sidebar-link"><i
                                            class="mdi mdi-view-module"></i><span class="hide-menu"> Grid</span></a>
                                </li>
                                <li class="sidebar-item"><a href="ui-carousel.html" class="sidebar-link"><i
                                            class="mdi mdi-view-carousel"></i><span class="hide-menu">
                                            Carousel</span></a></li>
                                <li class="sidebar-item"><a href="ui-cards.html" class="sidebar-link"><i
                                            class="mdi mdi-layers"></i><span class="hide-menu"> Basic Cards</span></a>
                                </li>
                                <li class="sidebar-item"><a href="ui-card-customs.html" class="sidebar-link"><i
                                            class="mdi mdi-credit-card-scan"></i><span class="hide-menu">Custom
                                            Cards</span></a></li>
                                <li class="sidebar-item"><a href="ui-card-weather.html" class="sidebar-link"><i
                                            class="mdi mdi-weather-fog"></i><span class="hide-menu">Weather
                                            Cards</span></a></li>
                                <li class="sidebar-item"><a href="ui-card-draggable.html" class="sidebar-link"><i
                                            class="mdi mdi-bandcamp"></i><span class="hide-menu">Draggable
                                            Cards</span></a></li>
                                <li class="sidebar-item"><a href="component-sweetalert.html" class="sidebar-link"><i
                                            class="mdi mdi-layers"></i><span class="hide-menu"> Sweet Alert</span></a>
                                </li>
                                <li class="sidebar-item"><a href="component-nestable.html" class="sidebar-link"><i
                                            class="mdi mdi-credit-card-scan"></i><span
                                            class="hide-menu">Nestable</span></a></li>
                                <li class="sidebar-item"><a href="component-noui-slider.html" class="sidebar-link"><i
                                            class="mdi mdi-weather-fog"></i><span class="hide-menu">Noui
                                            slider</span></a></li>
                                <li class="sidebar-item"><a href="component-rating.html" class="sidebar-link"><i
                                            class="mdi mdi-bandcamp"></i><span class="hide-menu">Rating</span></a></li>
                                <li class="sidebar-item"><a href="component-toastr.html" class="sidebar-link"><i
                                            class="mdi mdi-poll"></i><span class="hide-menu">Toastr</span></a></li>
                                <li class="sidebar-item"><a href="widgets-apps.html" class="sidebar-link"><i
                                            class="mdi mdi-comment-processing-outline"></i><span class="hide-menu"> Apps
                                            Widgets </span></a></li>
                                <li class="sidebar-item"><a href="widgets-data.html" class="sidebar-link"><i
                                            class="mdi mdi-calendar"></i><span class="hide-menu"> Data Widgets
                                        </span></a></li>
                                <li class="sidebar-item"><a href="widgets-charts.html" class="sidebar-link"><i
                                            class="mdi mdi-bulletin-board"></i><span class="hide-menu"> Charts
                                            Widgets</span></a></li>
                            </ul>
                        </li>
                        <li class="nav-small-cap"><i class="mdi mdi-dots-horizontal"></i> <span
                                class="hide-menu">Forms</span></li>
                        <li class="sidebar-item"> <a class="sidebar-link has-arrow waves-effect waves-dark"
                                href="javascript:void(0)" aria-expanded="false"><i class="mdi mdi-collage"></i><span
                                    class="hide-menu">Forms</span></a>
                            <ul aria-expanded="false" class="collapse first-level">
                                <li class="sidebar-item"> <a class="sidebar-link has-arrow waves-effect waves-dark"
                                        href="javascript:void(0)" aria-expanded="false"><i
                                            class="mdi mdi-collage"></i><span class="hide-menu">Form Elements</span></a>
                                    <ul aria-expanded="false" class="collapse second-level">
                                        <li class="sidebar-item"><a href="form-inputs.html" class="sidebar-link"><i
                                                    class="mdi mdi-priority-low"></i><span class="hide-menu"> Forms
                                                    Input</span></a></li>
                                        <li class="sidebar-item"><a href="form-input-groups.html"
                                                class="sidebar-link"><i class="mdi mdi-rounded-corner"></i><span
                                                    class="hide-menu"> Input Groups</span></a></li>
                                        <li class="sidebar-item"><a href="form-input-grid.html" class="sidebar-link"><i
                                                    class="mdi mdi-select-all"></i><span class="hide-menu"> Input
                                                    Grid</span></a></li>
                                        <li class="sidebar-item"><a href="form-checkbox-radio.html"
                                                class="sidebar-link"><i class="mdi mdi-shape-plus"></i><span
                                                    class="hide-menu"> Checkboxes &amp; Radios</span></a></li>
                                        <li class="sidebar-item"><a href="form-bootstrap-touchspin.html"
                                                class="sidebar-link"><i class="mdi mdi-switch"></i><span
                                                    class="hide-menu"> Bootstrap Touchspin</span></a></li>
                                        <li class="sidebar-item"><a href="form-bootstrap-switch.html"
                                                class="sidebar-link"><i class="mdi mdi-toggle-switch-off"></i><span
                                                    class="hide-menu"> Bootstrap Switch</span></a></li>
                                        <li class="sidebar-item"><a href="form-select2.html" class="sidebar-link"><i
                                                    class="mdi mdi-relative-scale"></i><span class="hide-menu">
                                                    Select2</span></a></li>
                                        <li class="sidebar-item"><a href="form-dual-listbox.html"
                                                class="sidebar-link"><i class="mdi mdi-tab-unselected"></i><span
                                                    class="hide-menu"> Dual Listbox</span></a></li>
                                    </ul>
                                </li>
                                <li class="sidebar-item"> <a class="sidebar-link has-arrow waves-effect waves-dark"
                                        href="javascript:void(0)" aria-expanded="false"><i
                                            class="mdi mdi-receipt"></i><span class="hide-menu">Form Layouts</span></a>
                                    <ul aria-expanded="false" class="collapse second-level">
                                        <li class="sidebar-item"><a href="form-basic.html" class="sidebar-link"><i
                                                    class="mdi mdi-vector-difference-ba"></i><span class="hide-menu">
                                                    Basic Forms</span></a></li>
                                        <li class="sidebar-item"><a href="form-horizontal.html" class="sidebar-link"><i
                                                    class="mdi mdi-file-document-box"></i><span class="hide-menu"> Form
                                                    Horizontal</span></a></li>
                                        <li class="sidebar-item"><a href="form-actions.html" class="sidebar-link"><i
                                                    class="mdi mdi-code-greater-than"></i><span class="hide-menu"> Form
                                                    Actions</span></a></li>
                                        <li class="sidebar-item"><a href="form-row-separator.html"
                                                class="sidebar-link"><i class="mdi mdi-code-equal"></i><span
                                                    class="hide-menu"> Row Separator</span></a></li>
                                        <li class="sidebar-item"><a href="form-bordered.html" class="sidebar-link"><i
                                                    class="mdi mdi-flip-to-front"></i><span class="hide-menu"> Form
                                                    Bordered</span></a></li>
                                        <li class="sidebar-item"><a href="form-striped-row.html" class="sidebar-link"><i
                                                    class="mdi mdi-content-duplicate"></i><span class="hide-menu">
                                                    Striped Rows</span></a></li>
                                        <li class="sidebar-item"><a href="form-detail.html" class="sidebar-link"><i
                                                    class="mdi mdi-cards-outline"></i><span class="hide-menu"> Form
                                                    Detail</span></a></li>
                                    </ul>
                                </li>
                                <li class="sidebar-item"> <a class="sidebar-link has-arrow waves-effect waves-dark"
                                        href="javascript:void(0)" aria-expanded="false"><i
                                            class="mdi mdi-code-equal"></i><span class="hide-menu">Form
                                            Addons</span></a>
                                    <ul aria-expanded="false" class="collapse second-level">
                                        <li class="sidebar-item"><a href="form-paginator.html" class="sidebar-link"><i
                                                    class="mdi mdi-export"></i><span class="hide-menu">
                                                    Paginator</span></a></li>
                                        <li class="sidebar-item"><a href="form-img-cropper.html" class="sidebar-link"><i
                                                    class="mdi mdi-crop"></i><span class="hide-menu"> Image
                                                    Cropper</span></a></li>
                                        <li class="sidebar-item"><a href="form-dropzone.html" class="sidebar-link"><i
                                                    class="mdi mdi-crosshairs-gps"></i><span class="hide-menu">
                                                    Dropzone</span></a></li>
                                        <li class="sidebar-item"><a href="form-mask.html" class="sidebar-link"><i
                                                    class="mdi mdi-box-shadow"></i><span class="hide-menu"> Form
                                                    Mask</span></a></li>
                                        <li class="sidebar-item"><a href="form-typeahead.html" class="sidebar-link"><i
                                                    class="mdi mdi-cards-variant"></i><span class="hide-menu"> Form
                                                    Typehead</span></a></li>
                                    </ul>
                                </li>
                                <li class="sidebar-item"> <a class="sidebar-link has-arrow waves-effect waves-dark"
                                        href="javascript:void(0)" aria-expanded="false"><i
                                            class="mdi mdi-alert-box"></i><span class="hide-menu">Form
                                            Validation</span></a>
                                    <ul aria-expanded="false" class="collapse second-level">
                                        <li class="sidebar-item"><a href="form-bootstrap-validation.html"
                                                class="sidebar-link"><i class="mdi mdi-credit-card-scan"></i><span
                                                    class="hide-menu"> Bootstrap Validation</span></a></li>
                                        <li class="sidebar-item"><a href="form-custom-validation.html"
                                                class="sidebar-link"><i class="mdi mdi-credit-card-plus"></i><span
                                                    class="hide-menu"> Custom Validation</span></a></li>
                                    </ul>
                                </li>
                                <li class="sidebar-item"> <a class="sidebar-link has-arrow waves-effect waves-dark"
                                        href="javascript:void(0)" aria-expanded="false"><i
                                            class="mdi mdi-pencil-box-outline"></i><span class="hide-menu">Form
                                            Pickers</span></a>
                                    <ul aria-expanded="false" class="collapse second-level">
                                        <li class="sidebar-item"><a href="form-picker-colorpicker.html"
                                                class="sidebar-link"><i class="mdi mdi-calendar-plus"></i><span
                                                    class="hide-menu"> Colorpicker</span></a></li>
                                        <li class="sidebar-item"><a href="form-picker-datetimepicker.html"
                                                class="sidebar-link"><i class="mdi mdi-calendar-clock"></i><span
                                                    class="hide-menu"> Datetimepicker</span></a></li>
                                        <li class="sidebar-item"><a href="form-picker-bootstrap-rangepicker.html"
                                                class="sidebar-link"><i class="mdi mdi-calendar-range"></i><span
                                                    class="hide-menu"> BT Rangepicker</span></a></li>
                                        <li class="sidebar-item"><a href="form-picker-bootstrap-datepicker.html"
                                                class="sidebar-link"><i class="mdi mdi-calendar-check"></i><span
                                                    class="hide-menu"> BT Datepicker</span></a></li>
                                        <li class="sidebar-item"><a href="form-picker-material-datepicker.html"
                                                class="sidebar-link"><i class="mdi mdi-calendar-text"></i><span
                                                    class="hide-menu"> Material Datepicker</span></a></li>
                                    </ul>
                                </li>
                                <li class="sidebar-item"> <a class="sidebar-link has-arrow waves-effect waves-dark"
                                        href="javascript:void(0)" aria-expanded="false"><i class="mdi mdi-dns"></i><span
                                            class="hide-menu">Form Editor</span></a>
                                    <ul aria-expanded="false" class="collapse second-level">
                                        <li class="sidebar-item"><a href="form-editor-ckeditor.html"
                                                class="sidebar-link"><i class="mdi mdi-drawing"></i><span
                                                    class="hide-menu">Ck Editor</span></a></li>
                                        <li class="sidebar-item"><a href="form-editor-quill.html"
                                                class="sidebar-link"><i class="mdi mdi-drupal"></i><span
                                                    class="hide-menu">Quill Editor</span></a></li>
                                        <li class="sidebar-item"><a href="form-editor-summernote.html"
                                                class="sidebar-link"><i class="mdi mdi-brightness-6"></i><span
                                                    class="hide-menu">Summernote Editor</span></a></li>
                                        <li class="sidebar-item"><a href="form-editor-tinymce.html"
                                                class="sidebar-link"><i class="mdi mdi-bowling"></i><span
                                                    class="hide-menu">Tinymce Edtor</span></a></li>
                                    </ul>
                                </li>
                                <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link"
                                        href="form-wizard.html" aria-expanded="false"><i
                                            class="mdi mdi-cube-send"></i><span class="hide-menu">Form Wizard</span></a>
                                </li>
                                <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link"
                                        href="form-repeater.html" aria-expanded="false"><i
                                            class="mdi mdi-creation"></i><span class="hide-menu">Form
                                            Repeater</span></a></li>
                            </ul>
                        </li>
                        <li class="nav-small-cap"><i class="mdi mdi-dots-horizontal"></i> <span
                                class="hide-menu">Tables</span></li>
                        <li class="sidebar-item"> <a class="sidebar-link has-arrow waves-effect waves-dark"
                                href="javascript:void(0)" aria-expanded="false"><i class="mdi mdi-border-none"></i><span
                                    class="hide-menu">Tables</span></a>
                            <ul aria-expanded="false" class="collapse first-level">
                                <li class="sidebar-item"> <a class="sidebar-link has-arrow waves-effect waves-dark"
                                        href="javascript:void(0)" aria-expanded="false"><i
                                            class="mdi mdi-border-none"></i><span class="hide-menu">Bootstrap
                                            Tables</span></a>
                                    <ul aria-expanded="false" class="collapse second-level">
                                        <li class="sidebar-item"><a href="table-basic.html" class="sidebar-link"><i
                                                    class="mdi mdi-border-all"></i><span class="hide-menu">Basic Table
                                                </span></a></li>
                                        <li class="sidebar-item"><a href="table-dark-basic.html" class="sidebar-link"><i
                                                    class="mdi mdi-border-left"></i><span class="hide-menu">Dark Basic
                                                    Table </span></a></li>
                                        <li class="sidebar-item"><a href="table-sizing.html" class="sidebar-link"><i
                                                    class="mdi mdi-border-outside"></i><span class="hide-menu">Sizing
                                                    Table </span></a></li>
                                        <li class="sidebar-item"><a href="table-layout-coloured.html"
                                                class="sidebar-link"><i class="mdi mdi-border-bottom"></i><span
                                                    class="hide-menu">Coloured Table Layout</span></a></li>
                                    </ul>
                                </li>
                                <li class="sidebar-item"> <a class="sidebar-link has-arrow waves-effect waves-dark"
                                        href="javascript:void(0)" aria-expanded="false"><i
                                            class="mdi mdi-border-inside"></i><span
                                            class="hide-menu">Datatables</span></a>
                                    <ul aria-expanded="false" class="collapse second-level">
                                        <li class="sidebar-item"><a href="table-datatable-basic.html"
                                                class="sidebar-link"><i class="mdi mdi-border-vertical"></i><span
                                                    class="hide-menu"> Basic Initialisation</span></a></li>
                                        <li class="sidebar-item"><a href="table-datatable-api.html"
                                                class="sidebar-link"><i class="mdi mdi-blur-linear"></i><span
                                                    class="hide-menu"> API</span></a></li>
                                        <li class="sidebar-item"><a href="table-datatable-advanced.html"
                                                class="sidebar-link"><i class="mdi mdi-border-style"></i><span
                                                    class="hide-menu"> Advanced Initialisation</span></a></li>
                                    </ul>
                                </li>
                                <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link"
                                        href="table-jsgrid.html" aria-expanded="false"><i
                                            class="mdi mdi-border-top"></i><span class="hide-menu">Table
                                            Jsgrid</span></a></li>
                                <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link"
                                        href="table-responsive.html" aria-expanded="false"><i
                                            class="mdi mdi-border-style"></i><span class="hide-menu">Table
                                            Responsive</span></a></li>
                                <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link"
                                        href="table-footable.html" aria-expanded="false"><i
                                            class="mdi mdi-tab-unselected"></i><span class="hide-menu">Table
                                            Footable</span></a></li>
                            </ul>
                        </li>
                        <li class="nav-small-cap"><i class="mdi mdi-dots-horizontal"></i> <span
                                class="hide-menu">Charts</span></li>
                        <li class="sidebar-item"> <a class="sidebar-link has-arrow waves-effect waves-dark sidebar-link"
                                href="javascript:void(0)" aria-expanded="false"><i
                                    class="mdi mdi-image-filter-tilt-shift"></i><span class="hide-menu">
                                    Charts</span></a>
                            <ul aria-expanded="false" class="collapse first-level">
                                <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link"
                                        href="chart-morris.html" aria-expanded="false"><i
                                            class="mdi mdi-image-filter-tilt-shift"></i><span class="hide-menu"> Morris
                                            Chart</span></a></li>
                                <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link"
                                        href="chart-chart-js.html" aria-expanded="false"><i
                                            class="mdi mdi-svg"></i><span class="hide-menu">Chartjs</span></a></li>
                                <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link"
                                        href="chart-sparkline.html" aria-expanded="false"><i
                                            class="mdi mdi-chart-histogram"></i><span class="hide-menu">Sparkline
                                            Chart</span></a></li>
                                <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark sidebar-link"
                                        href="chart-chartist.html" aria-expanded="false"><i
                                            class="mdi mdi-blur"></i><span class="hide-menu">Chartis Chart</span></a>
                                </li>
                                <li class="sidebar-item"> <a class="sidebar-link has-arrow waves-effect waves-dark"
                                        href="javascript:void(0)" aria-expanded="false"><i
                                            class="mdi mdi-chemical-weapon"></i><span class="hide-menu">C3
                                            Charts</span></a>
                                    <ul aria-expanded="false" class="collapse second-level">
                                        <li class="sidebar-item"><a href="chart-c3-axis.html" class="sidebar-link"><i
                                                    class="mdi mdi-arrange-bring-to-front"></i> <span
                                                    class="hide-menu">Axis Chart</span></a></li>
                                        <li class="sidebar-item"><a href="chart-c3-bar.html" class="sidebar-link"><i
                                                    class="mdi mdi-arrange-send-to-back"></i> <span
                                                    class="hide-menu">Bar Chart</span></a></li>
                                        <li class="sidebar-item"><a href="chart-c3-data.html" class="sidebar-link"><i
                                                    class="mdi mdi-backup-restore"></i> <span class="hide-menu">Data
                                                    Chart</span></a></li>
                                        <li class="sidebar-item"><a href="chart-c3-line.html" class="sidebar-link"><i
                                                    class="mdi mdi-backburger"></i> <span class="hide-menu">Line
                                                    Chart</span></a></li>
                                    </ul>
                                </li>
                                <li class="sidebar-item"> <a class="sidebar-link has-arrow waves-effect waves-dark"
                                        href="javascript:void(0)" aria-expanded="false"><i
                                            class="mdi mdi-chart-areaspline"></i><span
                                            class="hide-menu">Echarts</span></a>
                                    <ul aria-expanded="false" class="collapse second-level">
                                        <li class="sidebar-item"><a href="chart-echart-basic.html"
                                                class="sidebar-link"><i class="mdi mdi-chart-line"></i> <span
                                                    class="hide-menu">Basic Charts</span></a></li>
                                        <li class="sidebar-item"><a href="chart-echart-bar.html" class="sidebar-link"><i
                                                    class="mdi mdi-chart-scatterplot-hexbin"></i> <span
                                                    class="hide-menu">Bar Chart</span></a></li>
                                        <li class="sidebar-item"><a href="chart-echart-pie-doughnut.html"
                                                class="sidebar-link"><i class="mdi mdi-chart-pie"></i> <span
                                                    class="hide-menu">Pie &amp; Doughnut Chart</span></a></li>
                                    </ul>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-small-cap"><i class="mdi mdi-dots-horizontal"></i> <span class="hide-menu">Sample
                                Pages</span></li>
                        <li class="sidebar-item mega-dropdown"> <a
                                class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)"
                                aria-expanded="false"><i class="mdi mdi-file"></i><span class="hide-menu">Pages
                                </span></a>
                            <ul aria-expanded="false" class="collapse first-level">
                                <li class="sidebar-item"><a href="authentication-login1.html" class="sidebar-link"><i
                                            class="mdi mdi-account-key"></i><span class="hide-menu"> Login </span></a>
                                </li>
                                <li class="sidebar-item"><a href="starter-kit.html" class="sidebar-link"><i
                                            class="mdi mdi-crop-free"></i> <span class="hide-menu">Starter
                                            Kit</span></a></li>
                                <li class="sidebar-item"><a href="pages-animation.html" class="sidebar-link"><i
                                            class="mdi mdi-debug-step-over"></i> <span
                                            class="hide-menu">Animation</span></a></li>
                                <li class="sidebar-item"><a href="pages-search-result.html" class="sidebar-link"><i
                                            class="mdi mdi-search-web"></i> <span class="hide-menu">Search
                                            Result</span></a></li>
                                <li class="sidebar-item"><a href="authentication-login2.html" class="sidebar-link"><i
                                            class="mdi mdi-account-key"></i><span class="hide-menu"> Login 2 </span></a>
                                </li>
                                <li class="sidebar-item"><a href="pages-gallery.html" class="sidebar-link"><i
                                            class="mdi mdi-camera-iris"></i> <span class="hide-menu">Gallery</span></a>
                                </li>
                                <li class="sidebar-item"><a href="pages-treeview.html" class="sidebar-link"><i
                                            class="mdi mdi-file-tree"></i> <span class="hide-menu">Treeview</span></a>
                                </li>
                                <li class="sidebar-item"><a href="pages-block-ui.html" class="sidebar-link"><i
                                            class="mdi mdi-codepen"></i> <span class="hide-menu">Block UI</span></a>
                                </li>
                                <li class="sidebar-item"><a href="authentication-register1.html" class="sidebar-link"><i
                                            class="mdi mdi-account-plus"></i><span class="hide-menu">
                                            Register</span></a></li>
                                <li class="sidebar-item"><a href="pages-session-timeout.html" class="sidebar-link"><i
                                            class="mdi mdi-timer-off"></i> <span class="hide-menu">Session
                                            Timeout</span></a></li>
                                <li class="sidebar-item"><a href="pages-session-idle-timeout.html"
                                        class="sidebar-link"><i class="mdi mdi-timer-sand-empty"></i> <span
                                            class="hide-menu">Session Idle Timeout</span></a></li>
                                <li class="sidebar-item"><a href="pages-utility-classes.html" class="sidebar-link"><i
                                            class="mdi mdi-tune"></i> <span class="hide-menu">Helper Classes</span></a>
                                </li>
                                <li class="sidebar-item"><a href="authentication-register2.html" class="sidebar-link"><i
                                            class="mdi mdi-account-plus"></i><span class="hide-menu"> Register
                                            2</span></a></li>
                                <li class="sidebar-item"><a href="pages-maintenance.html" class="sidebar-link"><i
                                            class="mdi mdi-camera-iris"></i> <span class="hide-menu">Maintenance
                                            Page</span></a></li>
                                <li class="sidebar-item"><a href="ui-user-card.html" class="sidebar-link"><i
                                            class="mdi mdi-account-box"></i> <span class="hide-menu"> User Card
                                        </span></a></li>
                                <li class="sidebar-item"><a href="pages-profile.html" class="sidebar-link"><i
                                            class="mdi mdi-account-network"></i><span class="hide-menu"> User
                                            Profile</span></a></li>
                                <li class="sidebar-item"><a href="authentication-lockscreen.html"
                                        class="sidebar-link"><i class="mdi mdi-account-off"></i><span class="hide-menu">
                                            Lockscreen</span></a></li>
                                <li class="sidebar-item"><a href="ui-user-contacts.html" class="sidebar-link"><i
                                            class="mdi mdi-account-star-variant"></i><span class="hide-menu"> User
                                            Contact</span></a></li>
                                <li class="sidebar-item"><a href="pages-invoice.html" class="sidebar-link"><i
                                            class="mdi mdi-vector-triangle"></i><span class="hide-menu"> Invoice Layout
                                        </span></a></li>
                                <li class="sidebar-item"><a href="pages-invoice-list.html" class="sidebar-link"><i
                                            class="mdi mdi-vector-rectangle"></i><span class="hide-menu"> Invoice
                                            List</span></a></li>
                                <li class="sidebar-item"><a href="authentication-recover-password.html"
                                        class="sidebar-link"><i class="mdi mdi-account-convert"></i><span
                                            class="hide-menu"> Recover password</span></a></li>
                                <li class="sidebar-item"><a href="map-google.html" class="sidebar-link"><i
                                            class="mdi mdi-google-maps"></i><span class="hide-menu"> Google Map
                                        </span></a></li>
                                <li class="sidebar-item"><a href="map-vector.html" class="sidebar-link"><i
                                            class="mdi mdi-map-marker-radius"></i><span class="hide-menu"> Vector
                                            Map</span></a></li>
                                <li class="sidebar-item"><a href="icon-material.html" class="sidebar-link"><i
                                            class="mdi mdi-emoticon"></i> <span class="hide-menu"> Material Icons
                                        </span></a></li>
                                <li class="sidebar-item"><a href="eco-products.html" class="sidebar-link"><i
                                            class="mdi mdi-cards-variant"></i> <span class="hide-menu">Eco -
                                            Products</span></a></li>
                                <li class="sidebar-item"><a href="icon-fontawesome.html" class="sidebar-link"><i
                                            class="mdi mdi-emoticon-cool"></i><span class="hide-menu"> Fontawesome
                                            Icons</span></a></li>
                                <li class="sidebar-item"><a href="icon-themify.html" class="sidebar-link"><i
                                            class="mdi mdi-chart-bubble"></i><span class="hide-menu"> Themify
                                            Icons</span></a></li>
                                <li class="sidebar-item"><a href="icon-weather.html" class="sidebar-link"><i
                                            class="mdi mdi-weather-cloudy"></i><span class="hide-menu"> Weather
                                            Icons</span></a></li>
                                <li class="sidebar-item"><a href="eco-products-cart.html" class="sidebar-link"><i
                                            class="mdi mdi-cart"></i> <span class="hide-menu">Eco- Products
                                            Cart</span></a></li>
                                <li class="sidebar-item"><a href="icon-simple-lineicon.html" class="sidebar-link"><i
                                            class="mdi mdi mdi-image-broken-variant"></i> <span class="hide-menu">
                                            Simple Line icons</span></a></li>
                                <li class="sidebar-item"><a href="icon-flag.html" class="sidebar-link"><i
                                            class="mdi mdi-flag-triangle"></i><span class="hide-menu"> Flag
                                            Icons</span></a></li>
                                <li class="sidebar-item"><a href="timeline-center.html" class="sidebar-link"><i
                                            class="mdi mdi-clock-fast"></i> <span class="hide-menu"> Center Timeline
                                        </span></a></li>
                                <li class="sidebar-item"><a href="eco-products-edit.html" class="sidebar-link"><i
                                            class="mdi mdi-cart-plus"></i> <span class="hide-menu">Eco- Products
                                            Edit</span></a></li>
                                <li class="sidebar-item"><a href="timeline-horizontal.html" class="sidebar-link"><i
                                            class="mdi mdi-clock-end"></i><span class="hide-menu"> Horizontal
                                            Timeline</span></a></li>
                                <li class="sidebar-item"><a href="timeline-left.html" class="sidebar-link"><i
                                            class="mdi mdi-clock-in"></i><span class="hide-menu"> Left
                                            Timeline</span></a></li>
                                <li class="sidebar-item"><a href="timeline-right.html" class="sidebar-link"><i
                                            class="mdi mdi-clock-start"></i><span class="hide-menu"> Right
                                            Timeline</span></a></li>
                                <li class="sidebar-item"><a href="eco-products-detail.html" class="sidebar-link"><i
                                            class="mdi mdi-camera-burst"></i> <span class="hide-menu">Eco- Product
                                            Details</span></a></li>
                                <li class="sidebar-item"><a href="error-400.html" class="sidebar-link"><i
                                            class="mdi mdi-alert-outline"></i> <span class="hide-menu"> Error 400
                                        </span></a></li>
                                <li class="sidebar-item"><a href="error-403.html" class="sidebar-link"><i
                                            class="mdi mdi-alert-outline"></i><span class="hide-menu"> Error
                                            403</span></a></li>
                                <li class="sidebar-item"><a href="error-404.html" class="sidebar-link"><i
                                            class="mdi mdi-alert-outline"></i><span class="hide-menu"> Error
                                            404</span></a></li>
                                <li class="sidebar-item"><a href="eco-products-orders.html" class="sidebar-link"><i
                                            class="mdi mdi-chart-pie"></i> <span class="hide-menu">Eco- Product
                                            Orders</span></a></li>
                                <li class="sidebar-item"><a href="error-500.html" class="sidebar-link"><i
                                            class="mdi mdi-alert-outline"></i><span class="hide-menu"> Error
                                            500</span></a></li>
                                <li class="sidebar-item"><a href="error-503.html" class="sidebar-link"><i
                                            class="mdi mdi-alert-outline"></i><span class="hide-menu"> Error
                                            503</span></a></li>
                                <li class="sidebar-item"><a href="eco-products-checkout.html" class="sidebar-link"><i
                                            class="mdi mdi-clipboard-check"></i> <span class="hide-menu">Eco- Products
                                            Checkout</span></a></li>
                            </ul>
                        </li>
                        <li class="sidebar-item"> <a class="sidebar-link has-arrow waves-effect waves-dark"
                                href="javascript:void(0)" aria-expanded="false"><i
                                    class="mdi mdi-notification-clear-all"></i><span class="hide-menu">DD</span></a>
                            <ul aria-expanded="false" class="collapse first-level">
                                <li class="sidebar-item"><a href="javascript:void(0)" class="sidebar-link"><i
                                            class="mdi mdi-octagram"></i><span class="hide-menu"> item 1.1</span></a>
                                </li>
                                <li class="sidebar-item"><a href="javascript:void(0)" class="sidebar-link"><i
                                            class="mdi mdi-octagram"></i><span class="hide-menu"> item 1.2</span></a>
                                </li>
                                <li class="sidebar-item"> <a class="has-arrow sidebar-link" href="javascript:void(0)"
                                        aria-expanded="false"><i class="mdi mdi-playlist-plus"></i> <span
                                            class="hide-menu">Menu 1.3</span></a>
                                    <ul aria-expanded="false" class="collapse second-level">
                                        <li class="sidebar-item"><a href="javascript:void(0)" class="sidebar-link"><i
                                                    class="mdi mdi-octagram"></i><span class="hide-menu"> item
                                                    1.3.1</span></a></li>
                                        <li class="sidebar-item"><a href="javascript:void(0)" class="sidebar-link"><i
                                                    class="mdi mdi-octagram"></i><span class="hide-menu"> item
                                                    1.3.2</span></a></li>
                                        <li class="sidebar-item"><a href="javascript:void(0)" class="sidebar-link"><i
                                                    class="mdi mdi-octagram"></i><span class="hide-menu"> item
                                                    1.3.3</span></a></li>
                                        <li class="sidebar-item"><a href="javascript:void(0)" class="sidebar-link"><i
                                                    class="mdi mdi-octagram"></i><span class="hide-menu"> item
                                                    1.3.4</span></a></li>
                                    </ul>
                                </li>
                                <li class="sidebar-item"><a href="javascript:void(0)" class="sidebar-link"><i
                                            class="mdi mdi-playlist-check"></i><span class="hide-menu"> item
                                            1.4</span></a></li>
                            </ul>
                        </li>
                    </ul>
                </nav>
                <!-- End Sidebar navigation -->
            </div>
            <!-- End Sidebar scroll-->
        </aside>
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