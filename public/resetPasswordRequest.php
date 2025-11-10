<?php
require_once('../controllers/security/session_bootstrap.php');
require_once('../controllers/auth.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

try {
    $auth_controller = new AuthController();
    $auth_controller->check_user_role([Roles::STAFF]);
} catch (Exception $exception) {
    error_log($exception->getMessage() . "\n" . $exception->getTraceAsString());
    redirect_500();
} catch (Throwable $throwable) {
    error_log($throwable->getMessage() . "\n" .  $throwable->getTraceAsString());
    redirect_500();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['customer_id'])) {
    $userId = $_POST['customer_id'];
    // 1. Generate secure random password
    function generatePassword($length = 12)
    {
        return bin2hex(random_bytes($length / 2)); // hex string, length 12
    }
    $newPassword = generatePassword(12);

    // 2. Hash password
    $hashed = argon_hash($newPassword);

    // 3. Update DB
    $updateQuery = <<<SQL
        UPDATE "User"
        SET "Password" = :pwd
        WHERE "UserID" = :uid;
    SQL;
    DBController::exec_statement($updateQuery, [
        [':pwd', $hashed, PDO::PARAM_STR],
        [':uid', $userId, PDO::PARAM_STR]
    ]);
    // 4. Fetch email from Customer table
    $emailQuery = <<<SQL
        SELECT cust."Email"
        FROM "Customer" cust
        WHERE cust."UserID" = :uid
        LIMIT 1;
    SQL;
    $stmt = DBController::exec_statement($emailQuery, [
        [':uid', $userId, PDO::PARAM_STR]
    ]);
    $row = $stmt ? $stmt->fetch() : null;

    if ($row && !empty($row['Email'])) {
        $customerEmail = $row['Email'];
        // 5. Send email
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = $_ENV['SMTP_USER'];
            $mail->Password   = $_ENV['SMTP_PASS'];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            $mail->setFrom('noreply@nexabank.com', 'Nexabank');
            $mail->addAddress($customerEmail);

            $mail->isHTML(true);
            $mail->Subject = 'Password Reset';
            $mail->Body    = "Dear User,<br>Your password has been reset.<br><b>New Password:</b> {$newPassword}";

            $mail->send();
        } catch (Exception $e) {
            error_log("Mailer Error: {$mail->ErrorInfo}");
        }
    }
    echo "<script>alert('Email with the newly generated password is sent to the user');</script>";
    echo "<script>window.location.href ='/viewCustomers'</script>";
} else {
    echo "<script>alert('Failed');</script>";
    echo "<script>window.location.href ='/viewCustomers'</script>";
}
exit;
