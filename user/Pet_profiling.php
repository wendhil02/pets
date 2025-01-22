<?php
// viewContent.php
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Fetch the data from the database using the $id
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "bpa_system";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("SELECT * FROM register WHERE registrationID = ?");
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo "<h1>Content for Pet: " . htmlspecialchars($row['owner']) . "</h1>";
        echo "<p><strong>Name:</strong> " . htmlspecialchars($row['pet']) . "</p>";
        echo "<p><strong>Pet Age:</strong> " . htmlspecialchars($row['age']) . "</p>";
        echo "<p><strong>Breed:</strong> " . htmlspecialchars($row['breed']) . "</p>";
        echo "<p><strong>Additional Info:</strong> " . htmlspecialchars($row['info']) . "</p>";
        echo "<p><strong>Pet Image:</strong><br><img src='" . htmlspecialchars($row['pet_image']) . "' alt='Pet Image' class='img-fluid'></p>";
        echo "<p><strong>Vaccine Record:</strong><br><img src='" . htmlspecialchars($row['pet_vaccine']) . "' alt='Vaccine Record' class='img-fluid'></p>";
    } else {
        echo "<p>No content found.</p>";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "<p>No ID provided.</p>";
}
?>
