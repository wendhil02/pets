<?php
include 'internet/connect_ka.php';
ini_set('display_errors', 1);
error_reporting(E_ALL);

$message = '';

if (isset($_POST['register'])) {
    $email = trim($_POST['email']);
    $first_name = trim($_POST['first_name']);
    $middle_name = trim($_POST['middle_name']);
    $last_name = trim($_POST['last_name']);
    $password = trim($_POST['password']);
    $house = trim($_POST['house']);
    $street = trim($_POST['street']);
    $barangay = trim($_POST['barangay']);
    $city = trim($_POST['city']);
    $mobile = trim($_POST['mobile']);

    $status = 'pending';
    $role = 'user';
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $session_key = bin2hex(random_bytes(16));

    // Check if email already exists
    $checkEmailSQL = "SELECT COUNT(*) FROM registerlanding WHERE email = ?";
    $stmt = $conn->prepare($checkEmailSQL);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($emailCount);
    $stmt->fetch();
    $stmt->close();

    if ($emailCount > 0) {
        $message = "Email already exists. Please use another email.";
    } else {
        // Proceed to insert
        $insertSQL = "INSERT INTO registerlanding 
            (email, first_name, middle_name, last_name, status, role, session_key, password, house, street, barangay, city, mobile) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insertSQL);
        $stmt->bind_param("sssssssssssss", $email, $first_name, $middle_name, $last_name, $status, $role, $session_key, $hashed_password, $house, $street, $barangay, $city, $mobile);

        if ($stmt->execute()) {
            $message = "✅ Registration successful. Wait for admin approval.";
        } else {
            $message = "❌ Registration failed. Please try again.";
        }
        $stmt->close();
    }
}
?>


<!-- ✅ HTML Part -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register | Pet Welfare</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-900 to-blue-900 min-h-screen flex items-center justify-center px-4">

    <div class="bg-white shadow-xl rounded-xl p-4 sm:p-6 w-full max-w-sm sm:max-w-md text-sm">
        <div class="text-center mb-4">
            <img src="logo/logo.png" alt="Pet Logo" class="mx-auto w-16 h-16 mb-1">
            <h2 class="text-xl font-semibold text-emerald-600">Register</h2>
            <p class="text-gray-600 text-xs">Join us in protecting our furry friends 🐾</p>
        </div>

        <?php if (!empty($message)) echo "<p class='bg-green-100 text-green-700 p-2 mb-3 rounded text-xs'>$message</p>"; ?>

        <form method="post" action="" class="space-y-3">

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                <div>
                    <label class="block text-gray-700">First Name</label>
                    <input type="text" name="first_name" required maxlength="50" class="w-full border px-2 py-1 rounded focus:ring-1 focus:ring-emerald-400" pattern="[A-Za-z\s]+" title="Only letters and spaces allowed">
                </div>
                <div>
                    <label class="block text-gray-700">Middle Name</label>
                    <input type="text" name="middle_name" maxlength="50" class="w-full border px-2 py-1 rounded focus:ring-1 focus:ring-emerald-400" pattern="[A-Za-z\s]+" title="Only letters and spaces allowed">
                </div>
                <div>
                    <label class="block text-gray-700">Last Name</label>
                    <input type="text" name="last_name" required maxlength="50" class="w-full border px-2 py-1 rounded focus:ring-1 focus:ring-emerald-400" pattern="[A-Za-z\s]+" title="Only letters and spaces allowed">
                </div>
                <div>
                    <label class="block text-gray-700">Email</label>
                    <input type="email" name="email" required class="w-full border px-2 py-1 rounded focus:ring-1 focus:ring-emerald-400">
                </div>
            </div>

            <div>
                <label class="block text-gray-700">Password</label>
                <input type="password" name="password" required minlength="8" class="w-full border px-2 py-1 rounded focus:ring-1 focus:ring-emerald-400" title="Password should be at least 8 characters long">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                <div>
                    <label class="block text-gray-700">House</label>
                    <input type="text" name="house" required maxlength="100" class="w-full border px-2 py-1 rounded focus:ring-1 focus:ring-emerald-400">
                </div>
                <div>
                    <label class="block text-gray-700">Street</label>
                    <input type="text" name="street" required maxlength="100" class="w-full border px-2 py-1 rounded focus:ring-1 focus:ring-emerald-400">
                </div>
                <div>
                    <label class="block text-gray-700">Barangay</label>
                    <input type="text" name="barangay" required maxlength="100" class="w-full border px-2 py-1 rounded focus:ring-1 focus:ring-emerald-400">
                </div>
                <div>
                    <label class="block text-gray-700">City</label>
                    <input type="text" name="city" required maxlength="100" class="w-full border px-2 py-1 rounded focus:ring-1 focus:ring-emerald-400">
                </div>
            </div>

            <div>
                <label class="block text-gray-700">Mobile</label>
                <input type="text" name="mobile" required maxlength="15" class="w-full border px-2 py-1 rounded focus:ring-1 focus:ring-emerald-400" pattern="^\d{10}$" title="Please enter a valid 10-digit mobile number">
            </div>

            <button type="submit" name="register" class="w-full bg-emerald-500 hover:bg-emerald-600 text-white py-1.5 text-sm rounded transition duration-200">
                Register
            </button>
        </form>

        <p class="mt-3 text-xs text-center text-gray-600">
            Already have an account?
            <a href="index.php" class="text-emerald-600 hover:underline">Login here</a>
        </p>
    </div>

</body>

</html>
