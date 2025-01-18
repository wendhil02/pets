<?php
include('dbconn/config.php');  // Ensure your DB connection file is correct

// Get user ID from the URL
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $userId = $_GET['id']; // Sanitize input to ensure it's a valid number

    // Fetch user details
    $sql = "SELECT * FROM register WHERE id = ?";
    $stmt = $conn->prepare($sql);
    
    if ($stmt === false) {
        // Handle SQL error if preparation fails
        echo "<div class='text-center'><h1 class='text-red-600 font-bold text-lg'>SQL Error: Unable to prepare the query.</h1></div>";
        exit;
    }
    
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // User found, fetch the user data
        $user = $result->fetch_assoc();
        
        // Check if user has a picture, and set a default if not
        $userImage = !empty($user['pet_image']) ? $user['pet_image'] : 'default-pet-image.jpg'; // Default image fallback
        
        // Check if owner email exists
        $Email = !empty($user['email']) ? $user['email'] : 'No email provided'; // Fallback if no email is available
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>User Details</title>
            <script src="https://cdn.tailwindcss.com"></script>
        </head>
        <body class="bg-gray-100">
            <div class="min-h-screen flex items-center justify-center">
                <div class="bg-white shadow-md rounded-lg max-w-md w-full p-6">
                    <div class="flex flex-col items-center">
                        <!-- User Picture -->
                        <img src="<?php echo htmlspecialchars($userImage); ?>" 
                             alt="User Picture" 
                             class="w-32 h-32 rounded-full mb-4 border-2 border-gray-300">
                        
                        <!-- Pet Name -->
                        <h1 class="text-2xl font-bold mb-2">Hello, I'm <?php echo htmlspecialchars($user['pet']); ?></h1>
                        
                        <!-- Emergency Notification Message -->
                        <p class="text-gray-600 mb-4">In case of emergency, please notify my owner via 
    <?php if ($Email !== 'No email provided'): ?>
        <a href="mailto:<?php echo urlencode($Email); ?>" class="text-blue-600 hover:underline">email</a>.
    <?php else: ?>
        <span class="text-red-600">Email not provided</span>.<br>
    <?php endif; ?>
</p>

                    </div>
            </div>
        </body>
        </html>
        <?php
    } else {
        // If no user found
        echo "<div class='text-center'><h1 class='text-red-600 font-bold text-lg'>User not found!</h1></div>";
    }

    // Clean up and close the statement
    $stmt->close();
} else {
    // Invalid or missing ID in the URL
    echo "<div class='text-center'><h1 class='text-red-600 font-bold text-lg'>Invalid request!</h1></div>";
}

$conn->close();  // Close the database connection
?>
