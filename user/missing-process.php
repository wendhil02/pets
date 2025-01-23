<?php
// Include database configuration file
include('dbconn/config.php');

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Initialize response array
    $response = [
        'status' => 'error',
        'message' => 'An unexpected error occurred.',
    ];

    try {
        // Retrieve and sanitize input data
        $name = htmlspecialchars(trim($_POST['name']));
        $phone = htmlspecialchars(trim($_POST['phone']));
        $email = htmlspecialchars(trim($_POST['email']));
        $address = htmlspecialchars(trim($_POST['address']));
        $petName = htmlspecialchars(trim($_POST['petName']));
        $petAge = (int) $_POST['petAge'];
        $petBreed = htmlspecialchars(trim($_POST['petBreed']));
        $info = htmlspecialchars(trim($_POST['info']));

        // Check if an image was uploaded
        if (isset($_FILES['petImage']) && $_FILES['petImage']['error'] === UPLOAD_ERR_OK) {
            $image = $_FILES['petImage'];
            $imageName = uniqid('pet_', true) . '.' . pathinfo($image['name'], PATHINFO_EXTENSION);
            $uploadDir = 'uploads/pet_images/';
            $uploadPath = $uploadDir . $imageName;

            // Ensure the upload directory exists
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            // Validate file type (ensure it's an image)
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            if (!in_array($image['type'], $allowedTypes)) {
                $response['message'] = 'Invalid image type. Only JPEG, PNG, and GIF are allowed.';
                echo json_encode($response);
                exit;
            }

            // Move uploaded file to the target directory
            if (!move_uploaded_file($image['tmp_name'], $uploadPath)) {
                $response['message'] = 'Failed to upload image.';
                echo json_encode($response);
                exit;
            }
        } else {
            $response['message'] = 'Please upload a valid image file.';
            echo json_encode($response);
            exit;
        }

        // Insert data into the database
        $stmt = $conn->prepare("
            INSERT INTO pet_registration (
                owner_name, phone_number, email, address, pet_name, pet_age, pet_breed, additional_info, pet_image
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param(
            'sssssis',
            $name,
            $phone,
            $email,
            $address,
            $petName,
            $petAge,
            $petBreed,
            $info,
            $imageName
        );

        if ($stmt->execute()) {
            $response['status'] = 'success';
            $response['message'] = 'Pet registration submitted successfully!';
        } else {
            $response['message'] = 'Failed to save data. Please try again.';
        }

        $stmt->close();
    } catch (Exception $e) {
        $response['message'] = 'Error: ' . $e->getMessage();
    }
}

// Return the response as JSON
header('Content-Type: application/json');
echo json_encode($response);
?>
