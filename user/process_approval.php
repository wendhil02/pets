<?php
// process_approval.php

// Database connection settings
$servername = "localhost:3306";
$username   = "root";
$password   = "";
$database   = "your_database";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check for POST request with a valid post_id and action
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['post_id'], $_POST['action'])) {
    $postId = intval($_POST['post_id']);
    $action = $_POST['action'];

    // Determine status based on action:
    // 'approve' sets approved = 1, 'reject' sets approved = 2
    if ($action === 'approve') {
        $status = 1;
        // Example: get admin name from session or set a default value
        $adminName = "admin_user"; // Replace with actual admin username from session if available.
        $updateQuery = "UPDATE posts SET approved = ?, approved_by = ?, approved_at = NOW() WHERE id = ?";
    } elseif ($action === 'reject') {
        $status = 2;
        $adminName = "admin_user"; // You can also store who rejected it if desired.
        $updateQuery = "UPDATE posts SET approved = ?, approved_by = ?, approved_at = NOW() WHERE id = ?";
    } else {
        die("Invalid action.");
    }

    // Prepare and execute the update statement with additional approval information
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("isi", $status, $adminName, $postId);
    
    if ($stmt->execute()) {
        // Redirect back to admin_posts.php with a corresponding flag
        if($action === 'approve'){
            header("Location: admin_posts.php?success=1");
        } else {
            header("Location: admin_posts.php?rejected=1");
        }
        exit();
    } else {
        echo "Error updating record: " . $conn->error;
    }
} else {
    echo "Invalid request.";
}
?>
