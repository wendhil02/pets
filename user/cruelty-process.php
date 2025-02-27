<?php
include('./dbconn/config.php');

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize inputs
    $name     = htmlspecialchars(trim($_POST['name']));
    $phone    = htmlspecialchars(trim($_POST['phone']));
    $email    = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $address  = htmlspecialchars(trim($_POST['address']));
    $petType  = htmlspecialchars(trim($_POST['petType']));
    $petBreed = htmlspecialchars(trim($_POST['petBreed']));
    $info     = htmlspecialchars(trim($_POST['info']));

    $errors = [];
    if (empty($name) || !preg_match("/^[a-zA-Z ]+$/", $name)) {
        $errors[] = "Invalid name.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email.";
    }
    if (!empty($errors)) {
        echo json_encode(['status' => 'error', 'message' => implode(" ", $errors)]);
        exit;
    }

    // File handling: Convert image to base64
    $petImage = $_FILES['petImage'];
    if ($petImage['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(['status' => 'error', 'message' => 'File upload error.']);
        exit;
    }

    // Read the image file and encode it in base64
    $imageContent = file_get_contents($petImage['tmp_name']);
    if ($imageContent === false) {
        echo json_encode(['status' => 'error', 'message' => 'Failed to read the file.']);
        exit;
    }
    $petImageBase64 = base64_encode($imageContent);

    // Validate the image file is indeed an image
$imgInfo = getimagesize($petImage['tmp_name']);
if ($imgInfo === false) {
    echo json_encode(['status' => 'error', 'message' => 'Uploaded file is not a valid image.']);
    exit;
}

    // Insert into the database
    $stmt = $conn->prepare("
        INSERT INTO report (
            reportParty, phone_number, email, address, pet_type, pet_breed, additional_info, pet_image
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("ssssssss", $name, $phone, $email, $address, $petType, $petBreed, $info, $petImageBase64);

    if ($stmt->execute()) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Registration successful!'
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to save the record in the database. ' . $stmt->error
        ]);
    }
    $stmt->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}

$conn->close();
?>
