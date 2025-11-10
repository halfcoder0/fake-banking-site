<?php
http_response_code(500);
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
    <link rel="icon" type="image/png" sizes="16x16" href="../../assets/images/favicon.png">
    <title>Nice admin Template - The Ultimate Multipurpose admin template</title>
    <!-- Custom CSS -->
    <link nonce="<?= htmlspecialchars($nonce, ENT_QUOTES) ?>" href="../../dist/css/style.min.css" rel="stylesheet">
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
                <h1 class="error-title text-info">500</h1>
                <h3 class="text-uppercase error-subtitle">INTERNAL SERVER ERROR !</h3>
                <p class="text-muted m-t-30 m-b-30">PLEASE TRY AFTER SOME TIME</p>
                <a href="/" class="btn btn-danger btn-rounded waves-effect waves-light m-b-40">Back to home</a> </div>
        </div>
    </div>
    <!-- ============================================================== -->
    <script nonce="<?= htmlspecialchars($nonce, ENT_QUOTES) ?>" src="../../assets/libs/jquery/dist/jquery.min.js"></script>
    <!-- Bootstrap tether Core JavaScript -->
    <script nonce="<?= htmlspecialchars($nonce, ENT_QUOTES) ?>" src="../../assets/libs/popper.js/dist/umd/popper.min.js"></script>
    <script nonce="<?= htmlspecialchars($nonce, ENT_QUOTES) ?>" src="../../assets/libs/bootstrap/dist/js/bootstrap.min.js"></script>
    <!-- ============================================================== -->
    <script nonce="<?= htmlspecialchars($nonce, ENT_QUOTES) ?>">
    $('[data-toggle="tooltip"]').tooltip();
    $(".preloader").fadeOut();
    </script>
</body>

</html>