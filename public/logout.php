<?php
require_once('../controllers/security/session_bootstrap.php');

session_secure_logout();
header("Location: /");
?>
