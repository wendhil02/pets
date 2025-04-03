<?php
session_start();
include '../internet/connect_ka.php'; // Include your database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $reporter_email = $_SESSION['email'] ?? 'Anonymous'; // If user is not logged in, mark as Anonymous
    $incident_location = $_POST['incident_location'];
    $incident_datetime = $_POST['incident_datetime'];
    $incident_description = $_POST['incident_description'];
    $willing_to_testify = $_POST['willing_to_testify'];
    $social_media = $_POST['social_media'] ?? 'N/A'; // Changed from contact_number
    $agree_policy = isset($_POST['agree_policy']) ? 1 : 0; 

    // File Upload Handling
    $target_dir = "../uploads/videos/"; // Folder where videos will be stored
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true); // Create directory if it doesn't exist
    }

    $file_name = $_FILES["evidence"]["name"];
    $file_tmp = $_FILES["evidence"]["tmp_name"];
    $file_size = $_FILES["evidence"]["size"];
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

    $allowed_extensions = ["mp4", "avi", "mov", "mkv"];
    $max_size = 100 * 1024 * 1024; // 100MB
    // Updated max size to 50MB

    if (!in_array($file_ext, $allowed_extensions)) {
        die("Invalid file format. Only MP4, AVI, MOV, and MKV allowed.");
    }

    if ($file_size > $max_size) {
        die("File size exceeds 50MB limit.");
    }

    // Rename file to prevent conflicts
    $new_file_name = time() . "_" . uniqid() . "." . $file_ext;
    $target_file = $target_dir . $new_file_name;

    // Move uploaded file to the target directory
    if (!move_uploaded_file($file_tmp, $target_file)) {
        die("Error uploading file.");
    }

    // Insert report details into database
    $sql = "INSERT INTO cruelty_reports (reporter_email, incident_location, incident_datetime, incident_description, evidence_path, willing_to_testify, social_media, agree_policy)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssi", $reporter_email, $incident_location, $incident_datetime, $incident_description, $target_file, $willing_to_testify, $social_media, $agree_policy);

    if ($stmt->execute()) {
        echo "<script>alert('Report submitted successfully!'); window.location.href='report_cruelty.php';</script>";
    } else {
        echo "<script>alert('Error submitting report.'); history.back();</script>";
    }

    $stmt->close();
    $conn->close();
}
?>

