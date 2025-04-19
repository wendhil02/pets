<?php
session_start();
include '../internet/connect_ka.php';

// Ensure the user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: ../index.php");
    exit();
}

// Get the logged-in user's email
$email = $_SESSION['email'];

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $pet_name = trim($_POST['pet_name']);
    $breed = trim($_POST['breed']);
    $type = trim($_POST['pet_type']);
    $additional_info = trim($_POST['additional_info']);

    if (isset($_FILES['pet_image']) && $_FILES['pet_image']['error'] == 0) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $max_size = 15 * 1024 * 1024;

        $file_type = $_FILES['pet_image']['type'];
        $file_size = $_FILES['pet_image']['size'];
        $file_name = $_FILES['pet_image']['name'];
        $file_tmp_name = $_FILES['pet_image']['tmp_name'];

        if (!in_array($file_type, $allowed_types)) {
            $error_message = "Invalid image type. Only JPG, PNG, and GIF are allowed.";
        } elseif ($file_size > $max_size) {
            $error_message = "File size is too large. Maximum allowed size is 15MB.";
        } else {
            $unique_file_name = time() . "_" . basename($file_name);
            $upload_dir = '../uploads/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            $upload_path = $upload_dir . $unique_file_name;
            if (move_uploaded_file($file_tmp_name, $upload_path)) {
                $image_path = $upload_path;

                // Insert pet info with image and email
                $sql = "INSERT INTO pet (petname, breed, type, info, image, email, status) VALUES (?, ?, ?, ?, ?, ?, 'approved')";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssssss", $pet_name, $breed, $type, $additional_info, $image_path, $email);

                if ($stmt->execute()) {
                    $_SESSION['success_message'] = "Pet information has been successfully submitted!";
                    header("Location: shelters.php");
                    exit();
                } else {
                    $error_message = "Failed to submit the pet information. Please try again.";
                }

                $stmt->close();
            } else {
                $error_message = "Failed to upload the image. Please try again.";
            }
        }
    } else {
        // No image uploaded, insert without image
        $sql = "INSERT INTO pet (petname, breed, type, info, email, status) VALUES (?, ?, ?, ?, ?, 'approved')";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssss", $pet_name, $breed, $type, $additional_info, $email);

        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Pet information has been successfully submitted!";
            header("Location: shelters.php");
            exit();
        } else {
            $error_message = "Failed to submit the pet information. Please try again.";
        }

        $stmt->close();
    }
}
?>

<!-- If an error message exists, show it -->
<?php if (isset($error_message)): ?>
    <p class="text-red-600 text-center mt-4"><?php echo $error_message; ?></p>
<?php endif; ?>
