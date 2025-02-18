<?php
include('./dbconn/config.php');
include('./dbconn/authentication.php');

$errorMessage = "";
$successMessage = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['petId'])) {
    // Sanitize and validate inputs
    $petId    = (int) $_POST['petId'];
    $petName  = trim($_POST['petName']);
    $petAge   = (int) $_POST['petAge'];
    $petBreed = trim($_POST['petBreed']);
    $petInfo  = trim($_POST['petInfo']);
    $mail     = filter_var($_POST['mail'], FILTER_SANITIZE_EMAIL);
    $petImage = trim($_POST['petImage']);

    // Check for duplicates: if the same pet_id and email already exist
    $checkQuery = "SELECT id FROM adoption WHERE pet_id = ? AND mail = ?";
    if ($checkStmt = $conn->prepare($checkQuery)) {
        $checkStmt->bind_param("is", $petId, $mail);
        $checkStmt->execute();
        $checkStmt->store_result();
        
        if ($checkStmt->num_rows > 0) {
            // Duplicate found—set an error message.
            $errorMessage = "This adoption record already exists.";
        }
        $checkStmt->close();
    } else {
        $errorMessage = "Error preparing duplicate check: " . $conn->error;
    }

    // If no error, proceed with insertion.
    if (empty($errorMessage)) {
        $query = "INSERT INTO adoption (pet_id, pet_name, pet_age, pet_breed, pet_info,owner, mail, pet_image, approved ) VALUES (?, ?, ?, ?, ?, ?, ?,?, 0)";
        if ($stmt = $conn->prepare($query)) {
            $stmt->bind_param("isisssss", $petId, $petName, $petAge, $petBreed, $petInfo, $owner, $mail, $petImage);
            
            if ($stmt->execute()) {
                $successMessage = "Adoption record successfully created.";
                // Optionally, you could redirect the user here.
                header("Location: mypet.php");
                // exit;
            } else {
                $errorMessage = "Error executing statement: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $errorMessage = "Error preparing statement: " . $conn->error;
        }
    }
}
?>