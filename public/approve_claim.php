<?php
require_once('../controllers/auth.php');
require_once('../controllers/security/csrf.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$auth_controller = new AuthController();
$auth_controller->check_user_role([Roles::STAFF]);

$staffID = $_SESSION['StaffID'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['claim_id'])) {
    $claimId = trim($_POST['claim_id']) ?? '';

    if (!csrf_verify()){
        error_log("Invalid CSRF");
        return;
    }
    if ($claimId === '' || $staffID === ''){
        error_log("Reject Claims: ClaimID or StaffID is empty");
        return;
    }
    if (!is_valid_uuid($claimId)){
        error_log("Reject Claims: Invalid claimID");
        return;
    }

    // Approve the claim
    $updateQuery = <<<SQL
        UPDATE "Claims"
        SET "ApprovedAt" = NOW()
        WHERE "ClaimID" = :cid
        AND "ManagedBy" = :sid;
    SQL;

    DBController::exec_statement($updateQuery, [
        [':cid', $claimId, PDO::PARAM_STR],
        [':sid', $staffID, PDO::PARAM_STR]
    ]);

    // Fetch customer email
    $emailQuery = <<<SQL
        SELECT cust."Email"
        FROM "Claims" c
        JOIN "Customer" cust ON c."CustomerID" = cust."CustomerID"
        JOIN "User" u ON cust."UserID" = u."UserID"
        WHERE c."ClaimID" = :cid
        LIMIT 1;
    SQL;



    $stmt = DBController::exec_statement($emailQuery, [
        [':cid', $claimId, PDO::PARAM_STR]
    ]);
    $row = $stmt ? $stmt->fetch() : null;

    if ($row && !empty($row['Email'])) {
        $customerEmail = $row['Email'];
        error_log($customerEmail);

        // Send notification
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = $_ENV['SMTP_USER'];   // load from env/config
            $mail->Password   = $_ENV['SMTP_PASS'];   // load from env/config
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            $mail->setFrom('noreply@nexabank.com', 'Nexabank');
            $mail->addAddress($customerEmail);

            $mail->isHTML(true);
            $mail->Subject = 'Your Claim Approved';
            $mail->Body    = "Dear Customer,<br>Your claim <b>{$claimId}</b> has been approved.";

            $mail->send();
        } catch (Exception $e) {
            error_log("Mailer Error: {$mail->ErrorInfo}");
        }
    }
}


header('Location: /staff/assigned_claims');
exit;
?>
