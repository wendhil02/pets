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

    // Check if any field is empty
    if (empty($email) || empty($first_name) || empty($last_name) || empty($password) || empty($house) || empty($street) || 
        empty($barangay) || empty($city) || empty($mobile)) {
        $message = "Please fill up all information.";
    } 
    // Validate that no field (except email) contains "@"
    elseif (strpos($first_name, '@') !== false || strpos($middle_name, '@') !== false || strpos($last_name, '@') !== false ||
            strpos($house, '@') !== false || strpos($street, '@') !== false || strpos($barangay, '@') !== false ||
            strpos($city, '@') !== false || strpos($mobile, '@') !== false) {
        $message = "The '@' character is not allowed in any field except email.";
    } 
    // Check password length
    elseif (strlen($password) < 7) {
        $message = "Password must be at least 7 characters.";
    } else {
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
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $session_key = bin2hex(random_bytes(16));

            $insertSQL = "INSERT INTO registerlanding 
                (email, first_name, middle_name, last_name, status, role, session_key, password, house, street, barangay, city, mobile) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($insertSQL);
            $stmt->bind_param("sssssssssssss", $email, $first_name, $middle_name, $last_name, 'pending', 'user', $session_key, $hashed_password, $house, $street, $barangay, $city, $mobile);

            if ($stmt->execute()) {
                $message = "Registration successful. Wait for admin approval.";
            } else {
                $message = "Registration failed. Please try again.";
            }
            $stmt->close();
        }
    }
}
?>

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

        /* Custom Styles for the Toggle */
        .toggle {
            position: relative;
            display: inline-block;
            width: 34px;
            height: 20px;
        }

        .toggle input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .toggle-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: 0.4s;
            border-radius: 34px;
        }

        .toggle-slider:before {
            position: absolute;
            content: "";
            height: 12px;
            width: 12px;
            border-radius: 50%;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: 0.4s;
        }

        input:checked + .toggle-slider {
            background-color: #4CAF50;
        }

        input:checked + .toggle-slider:before {
            transform: translateX(14px);
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center relative bg-cover bg-center" style="background-image: url('logo/lgupic.jpg');">

    <!-- Overlay background -->
    <div class="absolute inset-0 bg-black opacity-50 z-0"></div>

    <div class="relative z-10 bg-white shadow-xl rounded-xl p-4 sm:p-6 w-full max-w-sm sm:max-w-md text-sm">
        <div class="text-center mb-4">
            <img src="logo/logo.png" alt="Pet Logo" class="mx-auto w-16 h-16 mb-1">
            <h2 class="text-xl font-semibold text-emerald-600">Register</h2>
            <p class="text-gray-600 text-xs">Join us in protecting our furry friends 🐾</p>
        </div>

        <!-- Display the error message in red -->
        <?php if (!empty($message)) echo "<p class='bg-red-100 text-red-700 p-2 mb-3 rounded text-xs'>$message</p>"; ?>

        <form method="post" action="" class="space-y-3">

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                <div>
                    <label class="block text-gray-700">First Name</label>
                    <input type="text" name="first_name"  maxlength="50" class="w-full border px-2 py-1 rounded focus:ring-1 focus:ring-emerald-400" pattern="[A-Za-z\s]+" title="Only letters and spaces allowed">
                </div>
                <div>
                    <label class="block text-gray-700">Middle Name</label>
                    <input type="text" name="middle_name" maxlength="50" class="w-full border px-2 py-1 rounded focus:ring-1 focus:ring-emerald-400" pattern="[A-Za-z\s]+" title="Only letters and spaces allowed">
                </div>
                <div>
                    <label class="block text-gray-700">Last Name</label>
                    <input type="text" name="last_name"  maxlength="50" class="w-full border px-2 py-1 rounded focus:ring-1 focus:ring-emerald-400" pattern="[A-Za-z\s]+" title="Only letters and spaces allowed">
                </div>
                <div>
                    <label class="block text-gray-700">Email</label>
                    <input type="email" name="email"  class="w-full border px-2 py-1 rounded focus:ring-1 focus:ring-emerald-400">
                </div>
            </div>

            <!-- Password with Facebook-style Toggle -->
            <div class="relative">
                <label class="block text-gray-700">Password</label>
                <input type="password" id="password" name="password"  minlength="8" class="w-full border px-2 py-1 rounded focus:ring-1 focus:ring-emerald-400" title="Password should be at least 8 characters long">
                
                <!-- Facebook-style Toggle Button -->
                <label class="toggle mt-2">
                    <input type="checkbox" id="togglePassword">
                    <span class="toggle-slider"></span>
                </label>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                <div>
                    <label class="block text-gray-700">House</label>
                    <input type="text" name="house"  maxlength="100" class="w-full border px-2 py-1 rounded focus:ring-1 focus:ring-emerald-400">
                </div>
                <div>
                    <label class="block text-gray-700">Street</label>
                    <input type="text" name="street"  maxlength="100" class="w-full border px-2 py-1 rounded focus:ring-1 focus:ring-emerald-400">
                </div>
                <div>
                    <label class="block text-gray-700">Barangay</label>
                    <input type="text" name="barangay"  maxlength="100" class="w-full border px-2 py-1 rounded focus:ring-1 focus:ring-emerald-400">
                </div>
                <div>
                    <label class="block text-gray-700">City</label>
                    <input type="text" name="city"  maxlength="100" class="w-full border px-2 py-1 rounded focus:ring-1 focus:ring-emerald-400">
                </div>
            </div>

            <div>
                <label class="block text-gray-700">Mobile</label>
                <input type="text" name="mobile"  maxlength="15" class="w-full border px-2 py-1 rounded focus:ring-1 focus:ring-emerald-400" pattern="^\d{10}$" title="Please enter a valid 10-digit mobile number">
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

    <script>
        document.getElementById('togglePassword').addEventListener('change', function() {
            const passwordField = document.getElementById('password');

            if (this.checked) {
                passwordField.type = 'text';
            } else {
                passwordField.type = 'password';
            }
        });
    </script>

</body>
</html>

</html>
