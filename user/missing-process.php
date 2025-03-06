<?php
include('./dbconn/config.php');

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize inputs
    $name    = htmlspecialchars(trim($_POST['name']));
    $phone   = htmlspecialchars(trim($_POST['phone']));
    $email   = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $address = htmlspecialchars(trim($_POST['address']));
    $petName = htmlspecialchars(trim($_POST['petName']));
    $petType = htmlspecialchars(trim($_POST['petType']));
    $petBreed = htmlspecialchars(trim($_POST['petBreed']));
    $info    = htmlspecialchars(trim($_POST['info']));
    $status = isset($_POST['status']) ? $_POST['status'] : '';

    $errors = [];
    if (empty($name) || !preg_match("/^[a-zA-Z ]+$/", $name)) {
        $errors[] = "Invalid name.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email.";
    }

    // If there are validation errors, return the errors in JSON format
    if ($errors) {
        echo json_encode(['status' => 'error', 'message' => implode(' ', $errors)]);
        exit;
    }

    // Handle pet image upload using Base64 encoding
    if (isset($_FILES['petImage']) && $_FILES['petImage']['error'] === UPLOAD_ERR_OK) {
        // Get the file's MIME type
        $mime = mime_content_type($_FILES['petImage']['tmp_name']);
        // Read and encode the image file to Base64 with data URI prefix
        $base64Image = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($_FILES['petImage']['tmp_name']));
    } else {
        echo json_encode(['status' => 'error', 'message' => 'File upload failed.']);
        exit;
    }

    // Insert into the database with the Base64 image
    $stmt = $conn->prepare("INSERT INTO missing (reportParty, phone_number, email, address, pet_name, petType, pet_breed, additional_info, status, pet_image) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssssss", $name, $phone, $email, $address, $petName, $petType, $petBreed, $info, $status, $base64Image);
    

    if ($stmt->execute()) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Registration successful!'
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
