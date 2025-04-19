<?php
session_start();
include '../internet/connect_ka.php'; // Include your database connection
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'user') {
    header("Location: index.php");
    exit();
}

$email = $_SESSION['email'];

// Fetch user's first name, middle name, and last name from the session
$user_first_name = $_SESSION['first_name'];
$user_middle_name = $_SESSION['middle_name'];
$user_last_name = $_SESSION['last_name'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $reporter_email = $_SESSION['email'] ?? 'Anonymous';
    $incident_location = $_POST['incident_location'];
    $incident_datetime = $_POST['incident_datetime'];
    $incident_description = $_POST['incident_description'];
    $willing_to_testify = $_POST['willing_to_testify'];
    $social_media = $_POST['social_media'] ?? 'N/A';
    $agree_policy = isset($_POST['agree_policy']) ? 1 : 0; 

    // File Upload Handling
    $target_dir = "../uploads/videos/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $file_name = $_FILES["evidence"]["name"];
    $file_tmp = $_FILES["evidence"]["tmp_name"];
    $file_size = $_FILES["evidence"]["size"];
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

    $allowed_extensions = ["mp4", "avi", "mov", "mkv"];
    $max_size = 50 * 1024 * 1024;

    if (!in_array($file_ext, $allowed_extensions)) {
        die("Invalid file format. Only MP4, AVI, MOV, and MKV allowed.");
    }

    if ($file_size > $max_size) {
        die("File size exceeds 50MB limit.");
    }

    $new_file_name = time() . "_" . uniqid() . "." . $file_ext;
    $target_file = $target_dir . $new_file_name;

    if (!move_uploaded_file($file_tmp, $target_file)) {
        die("Error uploading file.");
    }

    $sql = "INSERT INTO cruelty_reports 
            (reporter_email, first_name, middle_name, last_name, incident_location, incident_datetime, incident_description, evidence_path, willing_to_testify, social_media, agree_policy)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssssssi", $reporter_email, $user_first_name, $user_middle_name, $user_last_name, $incident_location, $incident_datetime, $incident_description, $target_file, $willing_to_testify, $social_media, $agree_policy);

    if ($stmt->execute()) {
        $_SESSION['report_success'] = "Animal cruelty report submitted successfully!";
        header("Location: report_cruelty.php");
        exit();
    } else {
        header("Location: report_cruelty.php?error=1");
        exit();
    }

    $stmt->close();
    $conn->close();
}
?>

