<?php
require_once('../controllers/security/session_bootstrap.php');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$userid = $_SESSION['UserID'] ?? null;
$role   = $_SESSION['Role'] ?? null;

if ($userid === '' || $role !== 'STAFF') {
    header('Location: /');
    exit;
}

// Get StaffID
$getStaff = <<<SQL
    SELECT "StaffID" FROM "Staff" WHERE "UserID" = :uid LIMIT 1;
SQL;
$stmt = DBController::exec_statement($getStaff, [[':uid', $userid, PDO::PARAM_STR]]);
$row = $stmt->fetch();
if (!$row) {
    throw new Exception("No Staff found for this UserID");
}
$staffID = $row['StaffID'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['claim_id'])) {
    $claimId = $_POST['claim_id'];

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
        $stmt = DBController::exec_statement($Query,[
            [':cid', $row['CustomerID'], PDO::PARAM_STR],
        ]);
        $emailQuery = $stmt ? $stmt->fetch() : null;

        if($emailQuery){
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
            $mail->Username   = $_ENV['SMTP_USER'];  ;      // your Gmail
            $mail->Password   = $_ENV['SMTP_PASS'];  ;        // 16-char App Password
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
            AuthController::return_error('Unable to send OTP email.');
        }
      }else{
        $error="Error";
        exit;
      }

    }


}

header('Location: /staffassignedclaims');
exit;
