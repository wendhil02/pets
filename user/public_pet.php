<?php
include '../internet/connect_ka.php';

// ✅ Get pet info using qr_id from URL
$pet = null;
if (isset($_GET['id'])) {  // Changed from qr_id to id for simplicity
    $qr_id_value = $_GET['id'];

    $sql = "SELECT petname, breed, type, age, vaccine_status, vaccine_type, info, image, qr_code, email 
            FROM pet WHERE qr_id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $qr_id_value);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $pet = $result->fetch_assoc();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pet ? htmlspecialchars($pet['petname']) . "'s Profile" : "Pet Not Found" ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>

<body class="bg-gray-100 flex items-center justify-center min-h-screen p-4">

    <?php if ($pet): ?>
        <div class="w-full max-w-md bg-white p-6 rounded-xl shadow-lg border border-gray-300">
            <!-- Header Section -->
            <div class="flex flex-col items-center space-y-2 text-center">
                <img src="logo/logo.png" alt="LGU Logo" class="w-14 h-14 rounded-full border-2 border-yellow-500 shadow-sm">
                <span class="text-sm font-semibold text-gray-700 uppercase">
                    <i class="fa-solid fa-shield-dog text-yellow-500"></i> LGU - Pet Animal Welfare Protection System
                </span>
            </div>

            <!-- Pet Profile Section -->
            <div class="flex items-center mt-4 space-x-4">
                <div class="w-24 h-24 border-4 border-gray-500 rounded-full overflow-hidden">
                    <img src="../uploads/<?= htmlspecialchars($pet['image']) ?>" alt="Pet Image" class="w-full h-full object-cover">
                </div>
                <div>
                    <h1 class="text-xl font-bold text-gray-800">
                        <i class="fa-solid fa-paw text-blue-500"></i> <?= htmlspecialchars($pet['petname']) ?>
                    </h1>
                    <p class="text-sm text-gray-500">
                        <i class="fa-solid fa-envelope text-gray-400"></i> <?= htmlspecialchars($pet['email']) ?>
                    </p>
                </div>
            </div>

            <!-- Pet Information -->
            <div class="mt-4 space-y-3">
                <div class="flex justify-between border-b pb-2">
                    <p class="font-medium text-gray-600"><i class="fa-solid fa-dog text-blue-500"></i> Type:</p>
                    <p class="text-gray-800"><?= htmlspecialchars($pet['type']) ?></p>
                </div>
                <div class="flex justify-between border-b pb-2">
                    <p class="font-medium text-gray-600"><i class="fa-solid fa-bone text-green-500"></i> Breed:</p>
                    <p class="text-gray-800"><?= htmlspecialchars($pet['breed']) ?></p>
                </div>
                <div class="flex justify-between border-b pb-2">
                    <p class="font-medium text-gray-600"><i class="fa-solid fa-calendar text-red-500"></i> Age:</p>
                    <p class="text-gray-800"><?= htmlspecialchars($pet['age']) ?> years</p>
                </div>
                <div class="flex justify-between border-b pb-2">
                    <p class="font-medium text-gray-600"><i class="fa-solid fa-syringe text-purple-500"></i> Vaccine Status:</p>
                    <p class="text-gray-800"><?= htmlspecialchars($pet['vaccine_status']) ?></p>
                </div>
                <div class="flex justify-between border-b pb-2">
                    <p class="font-medium text-gray-600"><i class="fa-solid fa-notes-medical text-pink-500"></i> Vaccine Type:</p>
                    <p class="text-gray-800"><?= htmlspecialchars($pet['vaccine_type']) ?></p>
                </div>
                <div class="border-t pt-2">
                    <p class="font-medium text-gray-600"><i class="fa-solid fa-info-circle text-yellow-500"></i> Pet Info:</p>
                    <p class="text-gray-800"><?= nl2br(htmlspecialchars($pet['info'])) ?></p>
                </div>
            </div>

            <!-- Back to Home Button -->

            <!-- Back to Home Button -->
            <div class="mt-4 text-center">
    <a href="https://smartbarangayconnect.com" target="_blank" class="text-blue-600 font-medium hover:underline">
        smartbarangayconnect.com
    </a>
</div>

        </div>

    <?php else: ?>
        <div class="bg-white p-6 rounded-lg shadow-md text-center max-w-md w-full">
            <h1 class="text-2xl font-bold text-red-500"><i class="fa-solid fa-exclamation-triangle"></i> Pet Not Found</h1>
            <p class="text-gray-700 mt-2">The pet profile you are looking for does not exist.</p>


        </div>
    <?php endif; ?>

</body>

</html>