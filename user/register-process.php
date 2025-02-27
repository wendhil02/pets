<?php
// Database connection
include('./dbconn/config.php');

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = htmlspecialchars(trim($_POST['name']));
    $phone = htmlspecialchars(trim($_POST['phone']));
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $address = htmlspecialchars(trim($_POST['address']));
    $petName = htmlspecialchars(trim($_POST['petName']));
    $petAge = intval($_POST['petAge']);
    $petBreed = htmlspecialchars(trim($_POST['petBreed']));
    $info = htmlspecialchars(trim($_POST['info']));

    $errors = [];
    if (empty($name) || !preg_match("/^[a-zA-Z ]+$/", $name)) $errors[] = "Invalid name.";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email.";
    if ($petAge <= 0) $errors[] = "Pet age must be positive.";

    if ($errors) {
        echo json_encode(['status' => 'error', 'message' => implode(' ', $errors)]);
        exit;
    }

    // Process Images (Convert to Base64)
    function convertToBase64($imageFile) {
        $imageData = file_get_contents($imageFile['tmp_name']);
        return base64_encode($imageData);
    }

    $petImage = $_FILES['petImage'];
    $vaccineImage = $_FILES['vaccineImage'];

    if ($petImage['error'] !== 0 || $vaccineImage['error'] !== 0) {
        echo json_encode(['status' => 'error', 'message' => 'File upload error.']);
        exit;
    }

    $petImageBase64 = convertToBase64($petImage);
    $vaccineImageBase64 = convertToBase64($vaccineImage);

    // Generate Registration ID
    $registrationID = bin2hex(random_bytes(16));

    // Insert Data into Database
    $stmt = $conn->prepare("
        INSERT INTO register (
            registrationID, owner, phone, email, address, pet, age, breed, info, pet_image, pet_vaccine
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param(
        "sssssssssss",
        $registrationID, $name, $phone, $email, $address, $petName, $petAge, $petBreed, $info, $petImageBase64, $vaccineImageBase64
    );

    if ($stmt->execute()) {
        // Generate the QR code URL
        $viewUrl = "http://localhost/pets/user/pet_profiling.php?id=" . $registrationID;

        echo json_encode([
            'status' => 'success',
            'message' => 'Registration successful!',
            'qrUrl' => $viewUrl,
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to save the upload record in the database.']);
    }
    $stmt->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}

$conn->close();
?>
