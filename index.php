<?php
// Include database connection
include 'internet/connect_ka.php'; // Database connection

// Start session
session_start();

// Logout functionality
if (isset($_GET['logout'])) {
    session_unset();  // Unset all session variables
    session_destroy(); // Destroy session
    header("Location: index.php");
    exit();
}

// Handle Registration
if (isset($_POST['register'])) {
    $email = trim($_POST['email']);
    $first_name = trim($_POST['first_name']);
    $middle_name = trim($_POST['middle_name']);
    $last_name = trim($_POST['last_name']);
    $password = 'pasworder';  // Using "pasworder" as the password for testing
    $status = 'pending'; // Default status for new users (waiting for admin approval)
    
    // Hash the password before saving to the database
    $hashed_password = password_hash($password, PASSWORD_DEFAULT); // Use a secure hashing algorithm

    // Generate a unique session key (32 characters long hexadecimal string)
    $session_key = bin2hex(random_bytes(16)); // Generate a unique session key

    // Check if the email already exists in the database
    $checkEmailSQL = "SELECT email FROM registerlanding WHERE email = ?";
    $stmt = $conn->prepare($checkEmailSQL);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // If the email already exists, show an error message
        $message = "Email is already registered. Please use a different email.";
    } else {
        // Insert user into the database with a default role and pending status
        $sql = "INSERT INTO registerlanding (email, first_name, middle_name, last_name, status, session_key, password) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssss", $email, $first_name, $middle_name, $last_name, $status, $session_key, $hashed_password);

        if ($stmt->execute()) {
            $message = "✅ Registration successful! Please wait for admin approval.";
        } else {
            $message = "An error occurred during registration. Please try again.";
        }
    }

    $stmt->close();
}

// Handle Login (Email-Only, with Approval Check)
if (isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);  // Get the entered password

    // Check if the user exists and is approved
    $sql = "SELECT email, first_name, middle_name, last_name, status, session_key, password FROM registerlanding WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($email, $first_name, $middle_name, $last_name, $status, $session_key, $stored_password);
        $stmt->fetch();

        // Verify if the entered password matches the stored password
        if (password_verify($password, $stored_password)) {
            // Check if the account is approved
            if ($status == 'approved') {
                // Set session variables for the logged-in user
                $_SESSION['email'] = $email;
                $_SESSION['first_name'] = $first_name;
                $_SESSION['middle_name'] = $middle_name;
                $_SESSION['last_name'] = $last_name;
                $_SESSION['session_key'] = $session_key; // Store the session key

                // Redirect to user page or admin page depending on the role (role will need to be added if necessary)
                header("Location: user/parehistro.php");
                exit();
            } else {
                $message = "Your account is not approved yet. Please wait for admin approval.";
            }
        } else {
            $message = "Incorrect password. Please try again.";
        }
    } else {
        $message = "Email not registered.";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auth System</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 flex justify-center items-center h-screen">
    <div class="bg-white p-6 rounded-lg shadow-md w-96">
        <h2 class="text-xl font-semibold text-center mb-4">Login / Register</h2>
        <?php if (isset($message)): ?>
            <p class="text-red-500 text-center"><?php echo $message; ?></p>
        <?php endif; ?>

        <!-- Login Form -->
        <form method="post" class="mb-4">
            <input type="email" name="email" placeholder="Email" required class="w-full p-2 border rounded mb-2">
            <input type="password" name="password" placeholder="Password" required class="w-full p-2 border rounded mb-2">
            <button type="submit" name="login" class="w-full bg-blue-500 text-white p-2 rounded">Login</button>
        </form>

        <!-- Register Form -->
        <form method="post">
            <input type="text" name="first_name" placeholder="First Name" required class="w-full p-2 border rounded mb-2">
            <input type="text" name="middle_name" placeholder="Middle Name" required class="w-full p-2 border rounded mb-2">
            <input type="text" name="last_name" placeholder="Last Name" required class="w-full p-2 border rounded mb-2">
            <input type="email" name="email" placeholder="Email" required class="w-full p-2 border rounded mb-2">
            <input type="password" name="password" placeholder="Password" value="pasworder" class="w-full p-2 border rounded mb-2" readonly>
            <button type="submit" name="register" class="w-full bg-green-500 text-white p-2 rounded">Register</button>
        </form>
    </div>
</body>
</html>
