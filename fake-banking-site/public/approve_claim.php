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

// Get StaffID for this user
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


header('Location: /staffassignedclaims');
exit;
?>
