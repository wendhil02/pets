<?php
session_start();
include 'internet/connect_ka.php';
ini_set('display_errors', 1);
error_reporting(E_ALL);

$message = '';

if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    setcookie('remember_me', '', time() - 3600, '/');
    header("Location: index.php");
    exit();
}

if (isset($_COOKIE['remember_me'])) {
    $session_key = $_COOKIE['remember_me'];
    $sql = "SELECT email, first_name, middle_name, last_name, status, role, session_key FROM registerlanding WHERE session_key = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $session_key);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($email, $first_name, $middle_name, $last_name, $status, $role, $session_key_db);
        $stmt->fetch();
        $_SESSION['email'] = $email;
        $_SESSION['first_name'] = $first_name;
        $_SESSION['middle_name'] = $middle_name;
        $_SESSION['last_name'] = $last_name;
        $_SESSION['role'] = $role;
        $_SESSION['session_key'] = $session_key_db;

        header("Location: " . ($role === 'admin' ? "admin/admin_dashboard.php" : "user/dashboard.php"));
        exit();
    }
}

if (isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $sql = "SELECT email, first_name, middle_name, last_name, status, role, session_key, password FROM registerlanding WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($email, $first_name, $middle_name, $last_name, $status, $role, $session_key_db, $stored_password);
        $stmt->fetch();
        if (password_verify($password, $stored_password)) {
            if ($status === 'approved') {
                $_SESSION['email'] = $email;
                $_SESSION['first_name'] = $first_name;
                $_SESSION['middle_name'] = $middle_name;
                $_SESSION['last_name'] = $last_name;
                $_SESSION['role'] = $role;
                $_SESSION['session_key'] = $session_key_db;
                setcookie('remember_me', $session_key_db, time() + (30 * 24 * 60 * 60), '/');
                header("Location: " . ($role === 'admin' ? "admin/admin_dashboard.php" : "user/dashboard.php"));
                exit();
            } else {
                $message = "Your account is not approved yet.";
            }
        } else {
            $message = "Incorrect password.";
        }
    } else {
        $message = "Email not registered.";
    }
}
?>

<!-- ✅ HTML + Tailwind Design -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Pet Welfare | Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-800 to-blue-800 min-h-screen flex items-center justify-center px-4">

    <div class="bg-white shadow-xl rounded-xl p-4 sm:p-6 w-full max-w-sm text-sm">
        <div class="text-center mb-4">
            <img src="logo/logo.png" alt="Pet Logo" class="mx-auto w-16 h-16 mb-2">
            <h2 class="text-xl font-semibold text-emerald-600">Login to Pet Welfare</h2>
            <p class="text-gray-600 text-xs">Protecting our furry friends ❤️</p>
        </div>

        <?php if (!empty($message)) : ?>
            <div class="bg-red-100 text-red-600 p-2 mb-3 text-xs rounded">
                <?= $message ?>
            </div>
        <?php endif; ?>

        <form method="post" action="" class="space-y-3">
            <div>
                <label class="block text-gray-700 text-xs">Email</label>
                <input type="email" name="email" required class="w-full px-3 py-2 rounded border border-gray-300 focus:outline-none focus:ring-1 focus:ring-emerald-400">
            </div>

            <div>
                <label class="block text-gray-700 text-xs">Password</label>
                <input type="password" name="password" required class="w-full px-3 py-2 rounded border border-gray-300 focus:outline-none focus:ring-1 focus:ring-emerald-400">
            </div>

            <button type="submit" name="login" class="w-full bg-emerald-500 hover:bg-emerald-600 text-white py-1.5 text-sm rounded transition duration-200">
                Login
            </button>
        </form>

        <p class="mt-3 text-xs text-center text-gray-600">
            Don’t have an account?
            <a href="registerperson.php" class="text-emerald-600 hover:underline">Register here</a>
        </p>
    </div>

</body>
</html>
