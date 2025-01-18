<?php
require_once 'libs/phpqrcode.php'; // Include QR code library

// Database connection
$host = 'localhost';
$username = 'root';
$password = ''; // Replace with your MySQL password
$database = 'registration';

$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die(json_encode(['status' => 'error', 'message' => 'Database connection failed: ' . $conn->connect_error]));
}

// Process the form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $image = $_FILES['image'];

    // Validate inputs
    if (empty($name) || empty($email) || empty($image)) {
        echo json_encode(['status' => 'error', 'message' => 'All fields are required!']);
        exit;
    }

    // Handle the image upload
    $targetDir = "test/uploads"; // Directory to store uploaded images
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true); // Create the directory if it doesn't exist
    }

    $fileName = uniqid('image_', true) . '.' . pathinfo($image['name'], PATHINFO_EXTENSION);
    $targetFile = $targetDir . $fileName;

    if (!move_uploaded_file($image['tmp_name'], $targetFile)) {
        echo json_encode(['status' => 'error', 'message' => 'Failed to upload the image.']);
        exit;
    }

    // Insert data into the database
    $stmt = $conn->prepare("INSERT INTO users (name, email, image) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $email, $targetFile);

    if ($stmt->execute()) {
        // Generate QR Code Content
        $qrData = "Name: $name\nEmail: $email\nImage: $targetFile";

        // Generate and save the QR code image
        $qrFolder = 'test/qrcodes';
        if (!is_dir($qrFolder)) {
            mkdir($qrFolder, 0777, true);
        }

        $qrFileName = $qrFolder . uniqid('qrcode_', true) . '.png';
        QRcode::png($qrData, $qrFileName, QR_ECLEVEL_L, 10);

        // Respond with success
        echo json_encode([
            'status' => 'success',
            'message' => 'Registration successful!',
            'data' => [
                'name' => $name,
                'email' => $email,
                'image' => $targetFile,
                'qrcode' => $qrFileName
            ]
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error: ' . $stmt->error]);
    }

    $stmt->close();
}

$conn->close();
?>
