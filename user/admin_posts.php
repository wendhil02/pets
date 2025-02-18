<?php
// admin_posts.php

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

// Retrieve posts that are not yet approved (pending approval)
$sql    = "SELECT id, title, content, created_at FROM posts WHERE approved = 0";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin - Approve or Reject Posts</title>
  <!-- Bootstrap CSS CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
  <h1 class="mb-4">User Posts Awaiting Approval</h1>
<!--message success/rejected-->
  <?php if(isset($_GET['success'])): ?>
    <div class="alert alert-success">Post approved successfully!</div>
  <?php endif; ?>
  
  <?php if(isset($_GET['rejected'])): ?>
    <div class="alert alert-warning">Post rejected successfully!</div>
  <?php endif; ?>

  <table class="table table-striped">
    <thead>
      <tr>
        <th>Title</th>
        <th>Content</th>
        <th>Submitted At</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php if ($result && $result->num_rows > 0): ?>
          <?php while($row = $result->fetch_assoc()): ?>
              <tr>
                  <td><?= htmlspecialchars($row['title']) ?></td>
                  <td><?= htmlspecialchars($row['content']) ?></td>
                  <td><?= htmlspecialchars($row['created_at']) ?></td>  
                  <td>
                      <!-- Approve Button -->
                      <form action="process_approval.php" method="POST" class="d-inline">
                          <input type="hidden" name="post_id" value="<?= $row['id'] ?>">
                          <button type="submit" name="action" value="approve" class="btn btn-success btn-sm">Approve</button>
                      </form>
                      <!-- Reject Button -->
                      <form action="process_approval.php" method="POST" class="d-inline ms-2">
                          <input type="hidden" name="post_id" value="<?= $row['id'] ?>">
                          <button type="submit" name="action" value="reject" class="btn btn-danger btn-sm">Reject</button>
                      </form>
                  </td>
              </tr>
          <?php endwhile; ?>
      <?php else: ?>
          <tr>
              <td colspan="4">No posts pending approval.</td>
          </tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>
</body>
</html>
