<?php
$host = "localhost";
$user = "root";  // Change if needed
$pass = "";      // Change if needed
$db = "image_upload_db";

// Database Connection
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle Image Upload
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["image"])) {
    $image = $_FILES["image"]["tmp_name"];
    $imageData = base64_encode(file_get_contents($image));

    $stmt = $conn->prepare("INSERT INTO images (image_data) VALUES (?)");
    $stmt->bind_param("s", $imageData);
    if ($stmt->execute()) {
        echo '<div class="alert alert-success">Image uploaded successfully!</div>';
    } else {
        echo '<div class="alert alert-danger">Error uploading image.</div>';
    }
    $stmt->close();
}

// Fetch Images
$result = $conn->query("SELECT * FROM images ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Image Upload (Base64)</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <h2 class="text-center mb-4">Upload and View Images</h2>

    <form method="POST" enctype="multipart/form-data" class="mb-4">
        <div class="mb-3">
            <label for="image" class="form-label">Select Image:</label>
            <input type="file" name="image" id="image" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Upload</button>
    </form>

    <h3 class="text-center mt-4">Uploaded Images</h3>
    <div class="row">
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="col-md-3">
                <div class="card mb-3">
                    <img src="data:image/*;base64,<?= $row['image_data'] ?>" class="card-img-top">
                    <div class="card-body text-center">
                        <p class="card-text">Image ID: <?= $row['id'] ?></p>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php $conn->close(); ?>
