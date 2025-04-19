<?php
// fetch_qr_code.php
include("../internet/connect_ka.php"); // Make sure to include your database connection file

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Query to get the QR code file name based on the pet ID
    $query = "SELECT qr_code FROM pet WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $qr_code_filename = $row['qr_code'];

        // Ensure filename does not already have the .png extension
        if (strpos($qr_code_filename, '.png') === false) {
            $qr_code_filename .= '.png';
        }

        // Respond with the URL to the QR code image
        echo json_encode([
            'success' => true,
            'qr_code_url' => "../qrcodes/{$qr_code_filename}"
        ]);
    } else {
        // If no result is found, return an error message
        echo json_encode(['success' => false]);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false]);
}
?>

