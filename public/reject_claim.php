<?php
require_once('../controllers/auth.php');
require_once('../controllers/security/csrf.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$auth_controller = new AuthController();
$auth_controller->check_user_role([Roles::STAFF]);

$staffID = $_SESSION['StaffID'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['claim_id'])) {
    $claimId = trim($_POST['claim_id']) ?? '';

    if (!csrf_verify()) {
        error_log("Invalid CSRF");
        return;
    }
    if ($claimId === '' || $staffID === '') {
        error_log("Reject Claims: ClaimID or StaffID is empty");
        return;
    }
    if (!is_valid_uuid($claimId)) {
        error_log("Reject Claims: Invalid claimID");
        return;
    }
    
    // First, fetch the image path so we can remove the file from storage
    $selectQuery = <<<SQL
        SELECT "ImagePath","CustomerID"
        FROM "Claims"
        WHERE "ClaimID" = :cid
        AND "ManagedBy" = :sid
        LIMIT 1;
    SQL;
    $stmt = DBController::exec_statement($selectQuery, [
        [':cid', $claimId, PDO::PARAM_STR],
        [':sid', $staffID, PDO::PARAM_STR]
    ]);
    $row = $stmt ? $stmt->fetch() : null;

    if ($row) {
        $imagePath = $row['ImagePath'];
        if (!empty($imagePath)) {
            $fullPath = $_SERVER['DOCUMENT_ROOT'] . $imagePath;
            if (is_file($fullPath)) {
                unlink($fullPath); // remove the file from server storage
            }
        }
        //Query the for customerid
        $Query = <<<SQL
            SELECT "Email"
            FROM "Customer"
            WHERE "CustomerID" = :cid
            LIMIT 1;
        SQL;
        $stmt = DBController::exec_statement($Query, [
            [':cid', $row['CustomerID'], PDO::PARAM_STR],
        ]);
        $emailQuery = $stmt ? $stmt->fetch() : null;

        if ($emailQuery) {
            $customerEmail = $emailQuery['Email'];

            // Now delete the claim record
            $deleteQuery = <<<SQL
                DELETE FROM "Claims"
                WHERE "ClaimID" = :cid
                AND "ManagedBy" = :sid;
            SQL;

            DBController::exec_statement($deleteQuery, [
                [':cid', $claimId, PDO::PARAM_STR],
                [':sid', $staffID, PDO::PARAM_STR]
            ]);

            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = $_ENV['SMTP_USER'];;      // your Gmail
                $mail->Password   = $_ENV['SMTP_PASS'];;        // 16-char App Password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;

                $mail->setFrom('nexabanksit@gmail.com', 'Nexabank');
                $mail->addAddress($customerEmail);         // send to user’s email

                $mail->isHTML(true);
                $mail->Subject = 'Your Claim Update';
                $mail->Body    = "Your Claim <b>{$claimId}</b>. Is being rejected.";

                $mail->send();
            } catch (Exception $e) {
                error_log("Mailer Error: {$mail->ErrorInfo}");
                $_SESSION[SessionVariables::GENERIC_ERROR->value]('Unable to send OTP email.');
            }
        } else {
            error_log("no row found.");
            $error = "Error";
            exit;
        }
    }
}

header('Location: /staff/assigned_claims');
exit;
