<?php
// view_approved_posts.php

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

// Retrieve posts that have been approved
$sql    = "SELECT id, title, content, created_at FROM posts WHERE approved = 1 ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Approved Posts</title>
  <!-- Bootstrap CSS CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
  <h1 class="mb-4">Approved Posts</h1>
  <div class="row">
    <?php if ($result && $result->num_rows > 0): ?>
        <?php while($row = $result->fetch_assoc()): ?>
            <div class="col-md-6 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($row['title']) ?></h5>
                        <p class="card-text"><?= nl2br(htmlspecialchars($row['content'])) ?></p>
                    </div>
                    <div class="card-footer text-muted">
                        Posted on <?= htmlspecialchars($row['created_at']) ?>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="col-12">
            <p class="text-muted">No approved posts to display.</p>
        </div>
    <?php endif; ?>
  </div>
</div>
</body>
</html>
