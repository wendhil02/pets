<?php
// process_submission.php

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

// Ensure the form is submitted via POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve and sanitize form data
    $title   = trim($_POST['title']);
    $content = trim($_POST['content']);

    // Basic validation
    if (empty($title) || empty($content)) {
        echo "Title and content cannot be empty.";
        exit;
    }

    // Prepare the INSERT statement with approved defaulting to 0 (pending)
    $stmt = $conn->prepare("INSERT INTO posts (title, content, approved) VALUES (?, ?, 0)");
    $stmt->bind_param("ss", $title, $content);

    if ($stmt->execute()) {
        // Redirect back to the submission form with a success flag
        header("Location: submit_post.php?success=1");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
} else {
    echo "Invalid request method.";
}
?>
