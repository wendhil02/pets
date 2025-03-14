<?php
include('./dbconn/config.php');
include('./dbconn/authentication.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize form inputs
    $owner_name     = trim($_POST['name']);
    $pet_name       = trim($_POST['pet_name']);
    $pet_type       = trim($_POST['pet_type']);
    $pet_age        = (int) $_POST['pet_age'];
    $pet_breed      = trim($_POST['pet_breed']);
    $pet_info       = trim($_POST['pet_info']);
    $phone          = trim($_POST['phone']);
    $email          = trim($_POST['email']);
    $address        = trim($_POST['address']);
    $vaccine_status = trim($_POST['vaccine_status']);

    // Handle Pet Image Upload (Convert to Base64)
    if (isset($_FILES['pet_image']) && $_FILES['pet_image']['error'] == 0) {
        $imageData = base64_encode(file_get_contents($_FILES['pet_image']['tmp_name']));
    } else {
        $imageData = null;
    }

    // Check if the pet is already registered (by owner name and pet name)
    $check_sql = "SELECT * FROM pets WHERE name = ? AND pet_name = ?";
    $stmt = $conn->prepare($check_sql);
    if (!$stmt) {
        header('Location: register.php?error=Prepare error: ' . $conn->error);
        exit();
    }
    $stmt->bind_param("ss", $owner_name, $pet_name);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        header('Location: register.php?error=Pet already registered!');
        exit();
    }
    $stmt->close();

    // Insert into pets table
    $insert_sql = "INSERT INTO pets (name, phone, email, address, pet_name, pet_age, pet_type, pet_breed, pet_info, pet_image, vaccine_status) 
                   VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    // Format: 5 strings, 1 integer, then 5 strings → "sssssisssss"
    $stmt = $conn->prepare($insert_sql);
    if (!$stmt) {
        header('Location: register.php?error=Prepare error: ' . $conn->error);
        exit();
    }
    $stmt->bind_param("sssssisssss", $owner_name, $phone, $email, $address, $pet_name, $pet_age, $pet_type, $pet_breed, $pet_info, $imageData, $vaccine_status);
    
    if ($stmt->execute()) {
        $pet_id = $conn->insert_id; // Get the last inserted pet ID
        $stmt->close();

        // Insert vaccine records if pet is vaccinated and vaccine details are provided
        if ($vaccine_status == "Vaccinated" && isset($_POST['vaccine_type'])) {
            foreach ($_POST['vaccine_type'] as $index => $type) {
                $vaccine_name    = trim($_POST['vaccine_name'][$index]);
                $vaccine_date    = trim($_POST['vaccine_date'][$index]);
                $administered_by = trim($_POST['administered_by'][$index]);

                // Use the correct column name "vaccine_type"
                $vaccine_sql = "INSERT INTO vaccines (pet_id, vaccine_type, vaccine_name, vaccine_date, administered_by) 
                                VALUES (?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($vaccine_sql);
                if (!$stmt) {
                    header('Location: register.php?error=Vaccine prepare error: ' . $conn->error);
                    exit();
                }
                // Bind types: pet_id = integer, rest = string → "issss"
                $stmt->bind_param("issss", $pet_id, $type, $vaccine_name, $vaccine_date, $administered_by);
                $stmt->execute();
                $stmt->close();
            }
        }

        // Generate QR Code (Base64) without saving to folder
        include 'phpqrcode/qrlib.php'; // Ensure you have the phpqrcode library installed
        $qr_data = "http://localhost/pets/user/view_pet.php?id=" . $pet_id;
        ob_start();
        QRcode::png($qr_data, null, QR_ECLEVEL_L, 5);
        $qr_image = base64_encode(ob_get_clean());

        // Save QR code to the pets table
        $qr_update_sql = "UPDATE pets SET qr_code = ? WHERE id = ?";
        $stmt = $conn->prepare($qr_update_sql);
        if (!$stmt) {
            header('Location: register.php?error=QR code prepare error: ' . $conn->error);
            exit();
        }
        $stmt->bind_param("si", $qr_image, $pet_id);
        $stmt->execute();
        $stmt->close();

        header('Location: register.php?success=Pet registered successfully!');
        exit();
    } else {
        header('Location: register.php?error=Failed to register pet!');
        exit();
    }
}

$conn->close();
?>
