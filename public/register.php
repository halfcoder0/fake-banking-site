<?php
// include('../includes/dbconnection.php');
require_once(__DIR__ . '/../includes/dbconnection.php');
if (isset($_POST['submit'])) {
    $fname    = $_POST['fname'];
    $contno   = $_POST['contactno'];
    $email    = $_POST['email'];
    $password = md5($_POST['password']);
    $pdo = get_pdo();
    // Check if email or contact already exists
    $stmt = $pdo->prepare("SELECT email FROM tblecustomer WHERE email = :email OR mobilenumber = :contno");
    $stmt->execute(['email' => $email, 'contno' => $contno]);
    $result = $stmt->rowCount();

    if ($result > 0) {
        echo "<script>alert('This email or Contact Number already associated with another account');</script>";
    } else {
        $stmt = $pdo->prepare("INSERT INTO tblecustomer (fullname, mobilenumber, email, password)
                               VALUES (:fname, :contno, :email, :password)");
        $success = $stmt->execute([
            'fname'    => $fname,
            'contno'   => $contno,
            'email'    => $email,
            'password' => $password
        ]);

        if ($success) {
            echo "<script>alert('You have successfully registered');</script>";
            echo "<script>window.location.href ='index.php'</script>";
        } else {
            echo "<script>alert('Something Went Wrong. Please try again');</script>";
            echo "<script>window.location.href ='register.php'</script>";
        }
    }
}
?>


<!DOCTYPE html>
<!-- Coding by CodingLab | www.codinglabweb.com-->
<html lang="en" dir="ltr">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Registration or Sign Up form in HTML CSS | CodingLab </title>
    <link rel="stylesheet" href="./css/style.css">
   </head>
<body>
  <div class="wrapper">
    <h2>Registration</h2>
    <form method="POST">
      <div class="input-box">
        <input type="text" name="fname" placeholder="Enter your name" required>
      </div>
      <div class="input-box">
        <input type="text" name="email" placeholder="Enter your email" required>
      </div>
      <div class="input-box">
        <input type="tel" name="contactno" placeholder="Mobile Number" required>
      </div>
      <div class="input-box">
        <input type="password" name="password" placeholder="Create password" required>
      </div>
      <div class="policy">
        <input type="checkbox">
        <h3>I accept all terms & condition</h3>
      </div>
        <div class="input-box button">
          <input type="Submit" name="submit" value="Register now">
        </div>
    </form>
  </div>

</body>
</html>
