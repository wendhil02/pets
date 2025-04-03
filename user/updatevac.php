<?php
// Start session to get the logged-in user email
session_start();

// Include database connection
include '../internet/connect_ka.php';

// Assuming the email of the logged-in user is stored in the session
$user_email = $_SESSION['email'] ?? null;

// Check if the user is logged in
if ($user_email) {
    // Fetch pet data for the logged-in user (pet name, email, image, vaccine card)
    $pet_result = mysqli_query($conn, "SELECT pet.id, pet.petname, pet.image, pet.vaccine_card, registerlanding.email 
                                       FROM pet 
                                       JOIN registerlanding ON registerlanding.email = '$user_email' 
                                       WHERE pet.email = registerlanding.email");
    
    // Check if the query returned any results
    if (!$pet_result) {
        die("Error fetching pet data: " . mysqli_error($conn));
    }
} else {
    // If no user is logged in, redirect to login page
    header("Location: auth.php");
    exit;
}


// Handle vaccine card update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['vaccine_card'])) {
    $pet_id = $_POST['pet_id'];
    $vaccine_card = $_FILES['vaccine_card'];

    // Check if the file was uploaded without errors
    if ($vaccine_card['error'] === UPLOAD_ERR_OK) {
        // Define the target directory where files should be uploaded
        $target_dir = "../uploads/vaccine_cards/";  // Relative path to the uploads folder
        $target_file = $target_dir . basename($vaccine_card['name']);  // Full path to the uploaded file
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));  // Get file extension

        // Check if the file is an image (you can add more checks here)
        if (in_array($imageFileType, ['jpg', 'jpeg', 'png'])) {
            // Try to move the uploaded file to the target directory
            if (move_uploaded_file($vaccine_card['tmp_name'], $target_file)) {
                // Update the pet's vaccine card image in the database
                $update_query = "UPDATE pet SET vaccine_card = '" . basename($vaccine_card['name']) . "' WHERE id = '$pet_id'";

                if (mysqli_query($conn, $update_query)) {
                    echo json_encode(["status" => "success", "message" => "Vaccine card updated successfully!"]);
                } else {
                    echo json_encode(["status" => "error", "message" => "Error updating vaccine card: " . mysqli_error($conn)]);
                }
            } else {
                echo json_encode(["status" => "error", "message" => "Sorry, there was an error uploading your file."]);
            }
        } else {
            echo json_encode(["status" => "error", "message" => "Only JPG, JPEG, and PNG files are allowed."]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "No file was uploaded or there was an error during upload."]);
    }
}
?>
