<?php
include('./dbconn/config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['petId'])) {
    // Sanitize and validate inputs
    $petId    = (int) $_POST['petId'];
    $petName  = trim($_POST['petName']);
    $petAge   = (int) $_POST['petAge'];
    $petBreed = trim($_POST['petBreed']);
    $petInfo  = trim($_POST['petInfo']);
    $mail     = filter_var($_POST['mail'], FILTER_SANITIZE_EMAIL);
    $petImage = trim($_POST['petImage']);

    // Check for duplicates: consider a record duplicate if the same pet_id and email already exist
    $checkQuery = "SELECT id FROM adoption WHERE pet_id = ? AND mail = ?";
    if ($checkStmt = $conn->prepare($checkQuery)) {
        $checkStmt->bind_param("is", $petId, $mail);
        $checkStmt->execute();
        $checkStmt->store_result();
        
        if ($checkStmt->num_rows > 0) {
            // Duplicate found, so do not insert again.
            echo "This adoption record already exists.";
            $checkStmt->close();
            exit;
        }
        $checkStmt->close();
    } else {
        echo "Error preparing duplicate check: " . $conn->error;
        exit;
    }

    // Prepare the SQL statement for insertion
    $query = "INSERT INTO adoption (pet_id, pet_name, pet_age, pet_breed, pet_info, mail, pet_image) VALUES (?, ?, ?, ?, ?, ?, ?)";
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("isissss", $petId, $petName, $petAge, $petBreed, $petInfo, $mail, $petImage);
        
        if ($stmt->execute()) {
            header("Location: mypet.php"); // Redirect upon success
            exit;
        } else {
            echo "Error executing statement: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Error preparing statement: " . $conn->error;
    }
}
?>
