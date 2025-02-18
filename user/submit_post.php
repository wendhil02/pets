<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Submit Your Post</title>
  <!-- Bootstrap CSS CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
  <div class="container mt-5">
    <h1 class="mb-4">Submit Your Post</h1>
    
    <?php if(isset($_GET['success'])): ?>
      <div class="alert alert-success"> 
        Your post has been submitted successfully and is pending approval.
        You will be redirected shortly.
      </div>
      <script>
        // Once the page loads, clear the form and then redirect after 3 seconds
        document.addEventListener('DOMContentLoaded', function(){
          document.getElementById('submitForm').reset();
          setTimeout(function(){
            window.location.href = 'submit_post.php';
          }, 3000); // Redirect after 3 seconds
        });   <?php
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

      </script>
    <?php endif; ?>
    
    <form id="submitForm" action="process_submission.php" method="POST">
      <div class="mb-3">
        <label for="title" class="form-label">Title</label>
        <input type="text" class="form-control" id="title" name="title" required>
      </div>
      <div class="mb-3">
        <label for="content" class="form-label">Content</label>
        <textarea class="form-control" id="content" name="content" rows="5" required></textarea>
      </div>
      <button type="submit" class="btn btn-primary">Submit Post</button>
      <!-- Clear Form Button -->
      <button type="reset" class="btn btn-secondary">Clear Form</button>
    </form>
  </div>
</body>
</html>
