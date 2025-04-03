<?php
session_start();
include 'internet/connect_ka.php';

// ✅ Check if request is POST and email is provided
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['email'])) {
        echo json_encode(["status" => "error", "message" => "❌ Missing email parameter."]);
        exit();
    }

    $email = $_POST['email'];

    // ✅ Update session_token to NULL in the subdomain database
    $stmt = $conn->prepare("UPDATE registerlanding SET session_token=NULL WHERE email=?");
    if ($stmt) {
        $stmt->bind_param("s", $email);
        if (!$stmt->execute()) {
            echo json_encode(["status" => "error", "message" => " Failed to update session_token in subdomain."]);
            exit();
        }
        $stmt->close();
    } else {
        echo json_encode(["status" => "error", "message" => "❌ Statement preparation failed."]);
        exit();
    }

    // ✅ Destroy session
    session_unset();
    session_destroy();

    // ✅ Remove session token from cookies (cross-domain)
    setcookie("session_token", "", time() - 3600, "/", ".smartbarangayconnect.com", true, true);

    echo json_encode(["status" => "success", "message" => " Subdomain logout successful!"]);
    exit();
}

// ✅ If accessed directly, redirect to login page
header("Location: index.php");
exit();
?>