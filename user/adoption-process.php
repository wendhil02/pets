<?php
include('./dbconn/config.php');

// Validate and sanitize input
function validate_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

// Retrieve form data
$name = validate_input($_POST['name'] ?? '');
$phone = validate_input($_POST['phone'] ?? '');
$email = validate_input($_POST['email'] ?? '');
$address = validate_input($_POST['address'] ?? '');
$petName = validate_input($_POST['petName'] ?? '');
$petType = validate_input($_POST['petType'] ?? '');
$petBreed = validate_input($_POST['petBreed'] ?? '');
$info = validate_input($_POST['info'] ?? '');
$reason = validate_input($_POST['reason'] ?? '');
$experience = validate_input($_POST['experience'] ?? '');

// Server-side validation
$errors = [];

if (empty($name) || preg_match('/\d/', $name)) {
    $errors[] = "Invalid name. Name cannot be empty or contain numbers.";
}

if (empty($phone) || !preg_match('/^\d{11}$/', $phone)) {
    $errors[] = "Invalid phone number. It must be 11 digits.";
}

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Invalid email address.";
}

if (empty($address)) {
    $errors[] = "Address is required.";
}

if (empty($petName)) {
    $errors[] = "Pet name is required.";
}

if (empty($petType)) {
    $errors[] = "Pet type is required.";
}

if (empty($petBreed)) {
    $errors[] = "Pet breed is required.";
}

// Check for errors
if (!empty($errors)) {
    echo json_encode([
        "status" => "error",
        "message" => "Validation errors occurred.",
        "errors" => $errors
    ]);
    exit();
}

// Insert data into the database
$sql = "INSERT INTO adoption (name, phone, email, address, pet_name, pet_type, pet_breed, info, reason, experience)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssssssss", $name, $phone, $email, $address, $petName, $petType, $petBreed, $info, $reason, $experience);

if ($stmt->execute()) {
    echo json_encode([
        "status" => "success",
        "message" => "Form submitted successfully."
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Failed to submit the form: " . $stmt->error
    ]);
}

// Close connections
$stmt->close();
?>
