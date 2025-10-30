<?php
require("../controllers/security/session_bootstrap.php");
require_once('../controllers/helpers.php');
require_once('../controllers/security/csrf.php');
require('../controllers/auth.php');


$nonce = generate_random();
add_csp_header($nonce);

if (isset($_POST['username']) && isset($_POST['password']) && isset($_POST['login']) && isset($_POST['csrf_token'])) {
    try {
        $auth_controller = new AuthController();
        $auth_controller->attempt_auth($_POST);
    } catch (Exception $exception) {
        $_SESSION["error"] = "Error logging in.";
        error_log($exception->getMessage());
        header("Location: /login");
    } catch (Throwable $thowable) {
        $_SESSION["error"] = "Error logging in.";
        error_log($thowable->getMessage());
        header("Location: /login");
    }

    exit;
}

?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="./assets/images/favicon.png">
    <title>Nexabank | Login</title>
    <!-- Custom CSS -->
    <link href="./dist/css/style.min.css" rel="stylesheet" nonce="<?= htmlspecialchars($nonce, ENT_QUOTES) ?>" crossorigin="anonymous">

    <style nonce="<?= htmlspecialchars($nonce, ENT_QUOTES) ?>">
        .auth-wrapper {
            background: url(./assets/images/big/auth-bg.jpg) no-repeat center center;
            background-color: #f0f5f9;
        }
    </style>
</head>

<body>
    <div class="main-wrapper">
        <div class="preloader">
            <div class="lds-ripple">
                <div class="lds-pos"></div>
                <div class="lds-pos"></div>
            </div>
        </div>
        <div class="auth-wrapper d-flex no-block justify-content-center align-items-center">
            <div class="auth-box">
                <div id="loginform-div">
                    <div class="logo">
                        <span class="db"><img src="./assets/images/logo-icon.png" alt="logo" /></span>
                        <h5 class="font-medium m-b-20">Sign In</h5>
                    </div>
                    <!-- Form -->
                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="row">
                            <div class="col-12">
                                <div class="alert alert-warning"> <?php echo ($_SESSION['error']);
                                                                    unset($_SESSION['error']); ?>
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">x</span> </button>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                    <div class="row">
                        <div class="col-12">
                            <form class="form-horizontal m-t-20 needs-validation" id="loginform" method="POST" action="/login">
                                <?= csrf_input() ?>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="basic-addon1"><i class="ti-user"></i></span>
                                    </div>
                                    <input name="username" type="text" maxlength="50" class="form-control form-control-lg" placeholder="Username" aria-label="Username" required>
                                </div>
                                <div class="input-group has-validation mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="basic-addon2"><i class="ti-pencil"></i></span>
                                    </div>
                                    <input name="password" type="password" maxlength="254" class="form-control form-control-lg" placeholder="Password" aria-label="Password" required>
                                </div>


                                <!-- TO BE IMPLEMENTED <div class="form-group row">
                                    <div class="col-md-12">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="customCheck1">
                                            <label class="custom-control-label" for="customCheck1">Remember me</label>
                                            <a href="" id="to-recover" class="text-dark float-right"><i class="fa fa-lock m-r-5"></i> Forgot pwd?</a>
                                        </div>
                                    </div>
                                </div> -->
                                <div class="form-group text-center">
                                    <div class="col-xs-12 p-b-20">
                                        <button class="btn btn-block btn-lg btn-info" type="submit" name="login" value="login">Log In</button>
                                    </div>
                                </div>
                                <div class="form-group m-b-0 m-t-10">
                                    <div class="col-sm-12 text-center">
                                        Don't have an account? <a href="/register" class="text-info m-l-5"><b>Sign Up</b></a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div id="recoverform">
                    <div class="logo">
                        <span class="db"><img src="./assets/images/logo-icon.png" alt="logo" /></span>
                        <h5 class="font-medium m-b-20">Recover Password</h5>
                        <span>Enter your Email and instructions will be sent to you!</span>
                    </div>
                    <div class="row m-t-20">
                        <!-- Form -->
                        <form class="col-12" action="index.html">
                            <!-- email -->
                            <div class="form-group row">
                                <div class="col-12">
                                    <input class="form-control form-control-lg" type="email" required="" placeholder="Username">
                                </div>
                            </div>
                            <!-- pwd -->
                            <div class="row m-t-20">
                                <div class="col-12">
                                    <button class="btn btn-block btn-lg btn-danger" type="submit" name="action">Reset</button>
                                </div>
                            </div>
                        </form>
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
    <!-- This page plugin js -->
    <script nonce="<?= htmlspecialchars($nonce, ENT_QUOTES) ?>">
        $('[data-toggle="tooltip"]').tooltip();
        $(".preloader").fadeOut();
        $('#to-recover').on("click", function() {
            $("#loginform").slideUp();
            $("#recoverform").fadeIn();
        });
    </script>
</body>

</html>
