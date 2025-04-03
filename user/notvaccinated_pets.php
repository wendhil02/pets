<?php
session_start();
include '../internet/connect_ka.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['email'])) {
        echo json_encode(["status" => "error", "message" => "Unauthorized access."]);
        exit();
    }

    $user_email = $_SESSION['email'];

    // Check if file is uploaded
    if (!isset($_FILES['vaccine_card']) || $_FILES['vaccine_card']['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(["status" => "error", "message" => "Error uploading the file."]);
        exit();
    }

    $pet_id = $_POST['pet_id'];
    $vaccine_type = trim($_POST['vaccine_type']);
    $file = $_FILES['vaccine_card'];

    // Get file details
    $fileName = basename($file['name']);
    $fileTmpPath = $file['tmp_name'];
    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    // Allowed file types
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'pdf'];
    if (!in_array($fileExt, $allowedExtensions)) {
        echo json_encode(["status" => "error", "message" => "Invalid file type. Only JPG, PNG, and PDF files are allowed."]);
        exit();
    }

    // Generate unique filename
    $newFileName = "vaccine_" . time() . "_" . $pet_id . "." . $fileExt;
    $uploadDir = '../uploads/vaccine_cards/';

    // Ensure the directory exists
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $destination = $uploadDir . $newFileName;

    // Move uploaded file to the destination
    if (move_uploaded_file($fileTmpPath, $destination)) {
        // Update the vaccine_status and set status to 'own'
        $updateSql = "UPDATE pet SET vaccine_card = ?, vaccine_type = ?, vaccine_status = 'Fully Vaccinated', status = 'own' WHERE id = ? AND email = ?";
        $stmt = $conn->prepare($updateSql);
        $stmt->bind_param("ssis", $newFileName, $vaccine_type, $pet_id, $user_email);

        if ($stmt->execute()) {
            echo json_encode([
                "status" => "success", 
                "message" => "Vaccine card uploaded successfully.", 
                "vaccine_status" => "Fully Vaccinated", 
                "status_updated" => "own"
            ]);
        } else {
            echo json_encode(["status" => "error", "message" => "Database update failed."]);
        }

        $stmt->close();
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to move uploaded file."]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request."]);
}

$conn->close();
?>


