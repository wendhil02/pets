<?php
session_start();
include 'internet/connect_ka.php'; // Database connection

// Logout functionality
if (isset($_GET['logout'])) {
    session_unset();  // Unset all session variables
    session_destroy(); // Destroy session
    header("Location: auth.php");
    exit();
}

// Handle Registration
if (isset($_POST['register'])) {
    $email = trim($_POST['email']);
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);

    $sql = "INSERT INTO registerlanding (email, first_name, last_name) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $email, $first_name, $last_name);

    if ($stmt->execute()) {
        $message = "✅ Registration successful! You can now log in.";
    } else {
        $message = "❌ Email already registered.";
    }
    $stmt->close();
}

// Handle Login (Email-Only)
if (isset($_POST['login'])) {
    $email = trim($_POST['email']);

    $sql = "SELECT email, first_name, last_name FROM registerlanding WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($email, $first_name, $last_name);
        $stmt->fetch();

        $_SESSION['email'] = $email;
        $_SESSION['first_name'] = $first_name;
        $_SESSION['last_name'] = $last_name;

        header("Location: user/parehistro.php");
        exit();
    } else {
        $message = "❌ Email not registered.";
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
            <button type="submit" name="login" class="w-full bg-blue-500 text-white p-2 rounded">Login</button>
        </form>

        <!-- Register Form -->
        <form method="post">
            <input type="text" name="first_name" placeholder="First Name" required class="w-full p-2 border rounded mb-2">
            <input type="text" name="last_name" placeholder="Last Name" required class="w-full p-2 border rounded mb-2">
            <input type="email" name="email" placeholder="Email" required class="w-full p-2 border rounded mb-2">
            <button type="submit" name="register" class="w-full bg-green-500 text-white p-2 rounded">Register</button>
        </form>
    </div>
</body>
</html>
