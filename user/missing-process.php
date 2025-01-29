<?php
include('./dbconn/config.php');

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize inputs
    $name = htmlspecialchars(trim($_POST['name']));
    $phone = htmlspecialchars(trim($_POST['phone']));
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $address = htmlspecialchars(trim($_POST['address']));
    $petName = htmlspecialchars(trim($_POST['petName']));
    $petType = htmlspecialchars(trim( $_POST['petType']));
    $petBreed = htmlspecialchars(trim($_POST['petBreed']));
    $info = htmlspecialchars(trim($_POST['info']));

    $errors = [];
    if (empty($name) || !preg_match("/^[a-zA-Z ]+$/", $name)) $errors[] = "Invalid name.";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email.";

    // If there are validation errors, return the errors in JSON format
    if ($errors) {
        echo json_encode(['status' => 'error', 'message' => implode(' ', $errors)]);
        exit;
    }

    // File handling
    $petImageDir = 'uploads/pet_missing/';
    if (!is_dir($petImageDir)) mkdir($petImageDir, 0777, true);

    $petImage = $_FILES['petImage'];  // Only handle petImage as per the form provided
    $petImageName = uniqid('petMissing_') . '.' . pathinfo($petImage['name'], PATHINFO_EXTENSION);
    $uploadPath1 = $petImageDir . $petImageName;

    // Check if the file is valid image
    if (!move_uploaded_file($petImage['tmp_name'], $uploadPath1)) {
        echo json_encode(['status' => 'error', 'message' => 'File upload failed.']);
        exit;
    }

    // Insert into the database
    $stmt = $conn->prepare("INSERT INTO missing (reportParty, phone_number, email, address, pet_name, petType, pet_breed, additional_info, pet_image) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssss", $name, $phone, $email, $address, $petName, $petType, $petBreed, $info, $uploadPath1);

    if ($stmt->execute()) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Registration successfully!'
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to save the upload record in the database. ' . $stmt->error]);
    }
    $stmt->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}

$conn->close();
?>
