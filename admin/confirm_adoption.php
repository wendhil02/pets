<?php
require '../internet/connect_ka.php';
session_start();

if (isset($_GET['token'])) {
    $confirmation_token = $_GET['token'];

    // Verify the token and update the status of the adoption request
    $sql = "SELECT id, pet_id FROM adoption_requests WHERE confirmation_token = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $confirmation_token);
    $stmt->execute();
    $stmt->bind_result($request_id, $pet_id);
    $stmt->fetch();
    $stmt->close();

    if ($request_id) {
        // Update adoption request status to 'confirmed'
        $sql = "UPDATE adoption_requests SET email_confirmed = 1 WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $request_id);
        $stmt->execute();
        $stmt->close();

        $_SESSION['notification'] = ['type' => 'success', 'message' => 'Your adoption request has been confirmed. Await admin approval.'];
    } else {
        $_SESSION['notification'] = ['type' => 'error', 'message' => 'Invalid confirmation link.'];
    }

    header("Location: manage_adoptions.php");
    exit();
}

$conn->close();
?>


