<?php
session_start();
include '../internet/connect_ka.php';


if (!isset($_POST['pet_id'], $_POST['contact'])) {
    $_SESSION['notif'] = [
        'type' => 'error',
        'message' => 'All fields are required!'
    ];
    header("Location: iwantadopt.php");
    exit();
}

$pet_id = $_POST['pet_id'];
$email = $_SESSION['email']; // Kukunin ang email mula sa session
$contact = $_POST['contact'];

// Check kung nag-request na ng adoption para sa parehong pet
$checkSql = "SELECT * FROM adoption_requests WHERE email = ? AND pet_id = ?";
$checkStmt = $conn->prepare($checkSql);
$checkStmt->bind_param("si", $email, $pet_id);
$checkStmt->execute();
$checkResult = $checkStmt->get_result();

if ($checkResult->num_rows > 0) {
    $_SESSION['notif'] = [
        'type' => 'error',
        'message' => 'You have already requested adoption for this pet!'
    ];
    header("Location: iwantadopt.php");
    exit();
}

// Insert adoption request
$sql = "INSERT INTO adoption_requests (pet_id, email, contact) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iss", $pet_id, $email, $contact);

if ($stmt->execute()) {
    $_SESSION['notif'] = [
        'type' => 'success',
        'message' => 'Adoption request submitted successfully!'
    ];
} else {
    $_SESSION['notif'] = [
        'type' => 'error',
        'message' => 'Failed to submit adoption request.'
    ];
}

$stmt->close();
$conn->close();
header("Location: iwantadopt.php");
exit();
?>
