<?php
include('./dbconn/config.php');

// Check that the form is submitted via POST and that required fields are present.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pet_id'], $_POST['action'])) {
    // Sanitize pet_id and action.
    $pet_id = (int) $_POST['pet_id'];
    $action = $_POST['action'];

    // Determine new approval status based on the action.
    if ($action === 'approve') {
        $approved = 1;
    } elseif ($action === 'reject') {
        $approved = 2;
    } else {
        header("Location: adoption_approval_admin.php?error=" . urlencode("Invalid action."));
        exit;
    }

    // Prepare the UPDATE statement to update the adoption record.
    $query = "UPDATE adoption SET approved = ? WHERE pet_id = ?";
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("ii", $approved, $pet_id);
        if ($stmt->execute()) {
            header("Location: adoption_approval_admin.php?success=" . urlencode("Process completed successfully."));
            exit;
        } else {
            header("Location: adoption_approval_admin.php?error=" . urlencode("Error executing statement: " . $stmt->error));
            exit;
        }
        $stmt->close();
        
    } else {
        header("Location: adoption_approval_admin.php?error=" . urlencode("Error preparing statement: " . $conn->error));
        exit;
    }
} else {
    header("Location: adoption_approval_admin.php?error=" . urlencode("Invalid request."));
    exit;
}
?>
