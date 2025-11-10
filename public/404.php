<?php
http_response_code(404);
require_once('../controllers/helpers.php');
$nonce = generate_random();
add_csp_header($nonce);

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
    <title>Nexabank | 404</title>
    <!-- Custom CSS -->
    <link href="./dist/css/style.min.css" rel="stylesheet" nonce="<?= htmlspecialchars($nonce, ENT_QUOTES) ?>" crossorigin="anonymous">
</head>

<body>
    <div class="main-wrapper">
        <div class="preloader">
            <div class="lds-ripple">
                <div class="lds-pos"></div>
                <div class="lds-pos"></div>
            </div>
        </div>
        <div class="error-box">
            <div class="error-body text-center">
                <h1 class="error-title text-danger">404</h1>
                <h3 class="text-uppercase error-subtitle">PAGE NOT FOUND !</h3>
                <p class="text-muted m-t-30 m-b-30">YOU SEEM TO BE TRYING TO FIND HIS WAY HOME</p>
                <a href="/" class="btn btn-danger btn-rounded waves-effect waves-light m-b-40">Back to home</a>
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
    </script>
</body>

</html>