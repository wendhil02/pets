<?php
session_start();
include '../internet/connect_ka.php';

// ✅ Enable error reporting to debug issues
error_reporting(E_ALL);
ini_set('display_errors', 1);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer-master/src/Exception.php';
require '../PHPMailer-master/src/PHPMailer.php';
require '../PHPMailer-master/src/SMTP.php';

// ✅ Set JSON response header
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // ✅ Get request data safely
    $pet_id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $action = isset($_POST['action']) ? trim($_POST['action']) : '';
    $owner_email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $pet_name = isset($_POST['petname']) ? trim($_POST['petname']) : '';
    $reason = isset($_POST['reason']) ? trim($_POST['reason']) : null;

    // ✅ Validate required fields
    if (!$pet_id || !$action || !$owner_email || !$pet_name) {
        echo json_encode(["success" => false, "message" => "Invalid request parameters"]);
        exit;
    }

    if (!in_array($action, ['approve', 'reject'])) {
        echo json_encode(["success" => false, "message" => "Invalid action"]);
        exit;
    }

    // ✅ Set status based on action
    if ($action === 'approve') {
        $new_status = 'approved';  // Set status to 'available' for approval
    } else {
        $new_status = 'own';  // Set status to 'own' for rejection
    }

    // ✅ Ensure rejection reason is provided if rejecting
    if ($action === 'reject' && empty($reason)) {
        echo json_encode(["success" => false, "message" => "Rejection reason is required"]);
        exit;
    }

    //  Update pet status based on action
    if ($action === 'approve') {
        $sql_update = "UPDATE pet SET status = ?, reason = NULL, schedule_date = NULL WHERE id = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("si", $new_status, $pet_id);
    } else {
        $sql_update = "UPDATE pet SET status = ?, reason = ?, schedule_date = NULL WHERE id = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("ssi", $new_status, $reason, $pet_id);
    }

    // ✅ Execute update query
    if ($stmt_update->execute()) {
        // ✅ Setup PHPMailer
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'wendhil10@gmail.com'; // Replace with your Gmail
            $mail->Password = 'ffml onzu stox lcwb'; // Use an App Password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // ✅ Set sender and recipient
            $mail->setFrom('your-email@gmail.com', 'Pet Adoption Team');
            $mail->addAddress($owner_email);

            // ✅ Email content
            $mail->isHTML(true);
            if ($action === 'approve') {
                $mail->Subject = "Pet Surrender Request Approved!";
                $mail->Body = "<p>Hello,</p>
                    <p>Good news! Your pet surrender request for '<b>$pet_name</b>' has been <b>approved</b>.</p>
                    <p>Your pet is now <b>available</b> for adoption.</p>
                    <p>Thank you for your patience.</p>
                    <p>Best regards,<br>Pet Adoption Team</p>";
            } else {
                $mail->Subject = "Pet Surrender Request Rejected";
                $mail->Body = "<p>Hello,</p>
                    <p>We regret to inform you that your surrender request for '<b>$pet_name</b>' has been <b>rejected</b>.</p>
                    <p><b>Reason:</b> $reason</p>
                    <p>The pet is now <b>owned</b> again and will not be available for adoption.</p>
                    <p>If you have any concerns, please contact us.</p>
                    <p>Best regards,<br>Pet Adoption Team</p>";
            }

            // ✅ Send email
            if (!$mail->send()) {
                throw new Exception("Failed to send email: " . $mail->ErrorInfo);
            }

            echo json_encode(["success" => true, "message" => "Status updated and email sent successfully"]);
        } catch (Exception $e) {
            echo json_encode(["success" => false, "message" => "Email error: " . $e->getMessage()]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Failed to update status"]);
    }

    //  Close database connections
    $stmt_update->close();
    $conn->close();
} else {
    echo json_encode(["success" => false, "message" => "Invalid request method"]);
}
?>
