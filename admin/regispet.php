<?php
// Admin approval logic
include '../internet/connect_ka.php'; // Database connection

if (isset($_GET['approve_id'])) {
    $id = $_GET['approve_id'];
    
    // Update the user status to 'approved'
    $sql = "UPDATE registerlanding SET status = 'approved' WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "User approved successfully!";
    } else {
        echo "Failed to approve user.";
    }
    $stmt->close();
}

// Display users for admin approval
$sql = "SELECT id, first_name, last_name, email, status FROM registerlanding WHERE status = 'pending'";
$result = $conn->query($sql);

echo "<h2>Pending User Approvals</h2>";
while ($row = $result->fetch_assoc()) {
    echo "<p>{$row['first_name']} {$row['last_name']} ({$row['email']}) 
          <a href='?approve_id={$row['id']}'>Approve</a></p>";
}

$conn->close();
?>