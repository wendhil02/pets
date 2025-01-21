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

    // File handling
    $petImageDir = 'uploads/pet_images/';
    $vaccineImageDir = 'uploads/vaccine_images/';
    if (!is_dir($petImageDir)) mkdir($petImageDir, 0777, true);
    if (!is_dir($vaccineImageDir)) mkdir($vaccineImageDir, 0777, true);
    

    $petImage = $_FILES['petImage'];
    $vaccineImage = $_FILES['vaccineImage'];
    $petImageName = uniqid('pet_') . '.' . pathinfo($petImage['name'], PATHINFO_EXTENSION);

    $uploadPath1 = $petImageDir . $petImageName;

    $vaccineImageName = uniqid('vaccine_') . '.' . pathinfo($vaccineImage['name'], PATHINFO_EXTENSION);
    $uploadPath2 = $vaccineImageDir . $vaccineImageName;

    if (move_uploaded_file($petImage['tmp_name'], $uploadPath1) &&
        move_uploaded_file($vaccineImage['tmp_name'], $uploadPath2)) {
        
        $registrationID = bin2hex(random_bytes(16));

        $stmt = $conn->prepare("
            INSERT INTO register (
                registrationID, owner, phone, email, address, pet, age, breed, info, pet_image, pet_vaccine
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param(
            "sssssssssss",
            $registrationID, $name, $phone, $email, $address, $petName, $petAge, $petBreed, $info, $uploadPath1, $uploadPath2
        );

        if ($stmt->execute()) {
            // Generate the QR code URL
            $viewUrl = "http://localhost/user/pet_view.php?id=" . $registrationID;

            echo json_encode([
                'status' => 'success',
                'message' => 'Upload successful!',
                'qrUrl' => $viewUrl,
            ]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to save the upload record in the database.']);
        }
        $stmt->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'File upload failed.']);
    }
   
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}

$conn->close();
?>
