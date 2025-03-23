<?php
session_start();
include '../internet/connect_ka.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer-master/src/Exception.php';
require '../PHPMailer-master/src/PHPMailer.php';
require '../PHPMailer-master/src/SMTP.php';

require '../vendor/autoload.php';  // If using Composer

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $pet_id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    $owner_email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $pet_name = isset($_POST['petname']) ? trim($_POST['petname']) : '';
    $reason = isset($_POST['reason']) ? trim($_POST['reason']) : null;

    if (!$pet_id || !$action || !$owner_email || !$pet_name) {
        echo json_encode(["success" => false, "message" => "Invalid request parameters"]);
        exit;
    }

    if (!in_array($action, ['approve', 'reject'])) {
        echo json_encode(["success" => false, "message" => "Invalid action"]);
        exit;
    }

    // ✅ Change status based on action
    $new_status = ($action === 'approve') ? 'approved' : 'available';

    // ✅ Check if rejection reason is required
    if ($action === 'reject' && empty($reason)) {
        echo json_encode(["success" => false, "message" => "Rejection reason is required"]);
        exit();
    }

    // ✅ Update pet status, reason, and schedule_date (set to NULL for both approve and reject)
    if ($action === 'approve') {
        $sql_update = "UPDATE pet SET status = ?, reason = NULL, schedule_date = NULL WHERE id = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("si", $new_status, $pet_id);
    } else {
        $sql_update = "UPDATE pet SET status = ?, reason = ?, schedule_date = NULL WHERE id = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("ssi", $new_status, $reason, $pet_id);
    }

    if ($stmt_update->execute()) {
        // ✅ Send email notification
        $mail = new PHPMailer(true);

        try {
            // SMTP Configuration
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'wendhil10@gmail.com'; // Replace with your Gmail
            $mail->Password = 'ffml onzu stox lcwb'; // Use an App Password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Sender & Recipient
            $mail->setFrom('your-email@gmail.com', 'Pet Adoption Team');
            $mail->addAddress($owner_email);

            // Email Content
            $mail->isHTML(true);

            if ($action === 'approve') {
                $mail->Subject = "Pet Surrender Request Approved!";
                $mail->Body = "<p>Hello,</p>
                    <p>Good news! Your pet surrender request for '<b>$pet_name</b>' has been <b>approved</b>.</p>
                    <p>Thank you for your patience.</p>
                    <p>Best regards,<br>Pet Adoption Team</p>";
            } else {
                $mail->Subject = "Pet Surrender Request Rejected";
                $mail->Body = "<p>Hello,</p>
                    <p>We regret to inform you that your surrender request for '<b>$pet_name</b>' has been <b>rejected</b>.</p>
                    <p><b>Reason:</b> $reason</p>
                    <p>The pet is now <b>available</b> again for adoption.</p>
                    <p>If you have any concerns, please contact us.</p>
                    <p>Best regards,<br>Pet Adoption Team</p>";
            }

            // Send Email
            if ($mail->send()) {
                echo json_encode(["success" => true, "message" => "Status updated and email sent successfully"]);
            } else {
                echo json_encode(["success" => false, "message" => "Failed to send email"]);
            }
        } catch (Exception $e) {
            echo json_encode(["success" => false, "message" => "Email error: " . $mail->ErrorInfo]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Failed to update status"]);
    }

    $stmt_update->close();
    $conn->close();
} else {
    echo json_encode(["success" => false, "message" => "Invalid request method"]);
}
?>