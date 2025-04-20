<?php
session_start();
include 'design/top.php';
include 'design/mid.php';
include '../internet/connect_ka.php'; // Adjust the path if needed

if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'user') {
    header("Location: ../index.php");
    exit();
}

$email = $_SESSION['email'];

// Fetch latest announcements from the database
$announcement_result = $conn->query("SELECT * FROM announcements ORDER BY created_at DESC LIMIT 5");

$featured_result = $conn->query("SELECT image_path FROM featured_images ORDER BY uploaded_at DESC LIMIT 1");
$featured_image = $featured_result->fetch_assoc();

$directory = '../uploads/featured/';
$allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

$featured_images = [];

// Scan folder
if (is_dir($directory)) {
    $files = scandir($directory);
    foreach ($files as $file) {
        $file_path = $directory . $file;
        $file_extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));

        if (in_array($file_extension, $allowed_extensions)) {
            $featured_images[] = ['image_path' => $file];
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        @keyframes slide {
            0% {
                transform: translateX(0%);
            }

            20% {
                transform: translateX(0%);
            }

            25% {
                transform: translateX(-100%);
            }

            45% {
                transform: translateX(-100%);
            }

            50% {
                transform: translateX(-200%);
            }

            70% {
                transform: translateX(-200%);
            }

            75% {
                transform: translateX(0%);
            }

            100% {
                transform: translateX(0%);
            }
        }

        .animate-slider {
            animation: slide 15s infinite ease-in-out;
        }
    </style>


</head>

<body class="flex bg-gray-100">
    <!-- Main Content -->
    <div id="mainContent" class="main-content flex-1 transition-all">
        <!-- Navbar -->
        <nav class="bg-[#0077b6] shadow-md mt-3 mr-2 ml-2 p-2 flex items-center justify-between rounded-lg max-w-auto mx-auto sticky top-0 z-50">
            <!-- Button -->
            <button id="toggleSidebar" class="text-white text-lg px-2 py-1 hover:bg-blue-100 rounded-md border border-transparent">
                ☰
            </button>

            <div class="flex items-center gap-4 flex-grow">
                <!-- Current Time and Date -->
                <span id="currentTime" class="text-white font-semibold text-sm md:text-base lg:text-lg"></span>
                <div id="currentDate" class="text-white font-semibold text-sm md:text-base lg:text-lg"></div>
            </div>

            <div class="flex items-center gap-4">
                <!-- Welcome Message -->
                <span class="font-bold text-white text-sm md:text-base lg:text-lg">
                    Welcome, <?= htmlspecialchars($email) ?>
                </span>
            </div>
        </nav>


<!-- Dashboard Content -->
<div class="p-6 bg-gradient-to-b from-yellow-50 via-white to-yellow-50 mt-4 rounded-xl shadow-md">

    <!-- Announcements -->
    <div class="mb-10">
        <?php if (!empty($featured_images)): ?>
            <div class="p-5 bg-white rounded-xl shadow-lg">
                <div class="flex flex-col items-center space-y-3">
                    <img src="logo/logo.png" alt="LGU Logo" class="w-[100px] h-[100px] rounded-full border-4 border-yellow-400 shadow-md">
                    <span class="text-2xl font-bold text-yellow-900 uppercase text-center">
                        <i class="fa-solid fa-shield-dog text-yellow-500 mr-2"></i> LGU - Pet Animal Welfare Protection System
                    </span>
                </div>
                <div class="relative overflow-hidden rounded-lg mx-auto mt-6 max-w-lg h-[350px] shadow-md">
                    <div class="flex animate-slider">
                        <?php foreach ($featured_images as $image): ?>
                            <div class="flex-shrink-0 w-full">
                                <img src="../uploads/featured/<?= htmlspecialchars($image['image_path']) ?>" alt="Featured Image" class="w-full h-full object-cover rounded-lg hover:scale-105 transition-transform duration-500">
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <h2 class="text-2xl font-bold mb-6 text-gray-800 mt-10 text-center">
            📢 Official Announcement: Pet Animal Welfare Protection System
        </h2>

        <?php if ($announcement_result && $announcement_result->num_rows > 0): ?>
            <?php while ($row = $announcement_result->fetch_assoc()): ?>
                <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-800 p-5 mb-4 rounded-xl shadow">
                    <div class="text-base mb-2"><?= nl2br(htmlspecialchars($row['message'])) ?></div>
                    <?php if (!empty($row['image'])): ?>
                        <div class="mt-3">
                            <img src="../uploads/announcements/<?= htmlspecialchars($row['image']) ?>" alt="Announcement Image" class="rounded-md shadow-md w-full max-h-[250px] object-cover">
                        </div>
                    <?php endif; ?>
                    <div class="text-xs text-gray-500 mt-3">
                        Posted on <?= date("F j, Y, g:i A", strtotime($row['created_at'])) ?>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="text-center text-sm text-gray-500">No announcements available.</p>
        <?php endif; ?>
    </div>

    <!-- About Section -->
    <div class="bg-white p-6 rounded-xl shadow-lg mb-10">
        <h3 class="text-2xl font-bold text-yellow-600 mb-4">🐾 About Pet Animal Welfare Protection System</h3>
        <p class="text-gray-700 leading-relaxed mb-4">
            The <strong>Pet Animal Welfare Protection System</strong> is an innovative initiative designed to safeguard and promote the well-being of pets within our community. It ensures that all pets are properly registered, cared for, and protected under animal welfare standards.
        </p>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div>
                <h4 class="text-xl font-semibold text-gray-800 mt-4 mb-2">🎯 Objectives:</h4>
                <ul class="list-disc list-inside text-gray-700 space-y-2">
                    <li>Provide a systematic way to register and monitor pet animals.</li>
                    <li>Encourage the adoption of rescued or abandoned animals.</li>
                    <li>Educate the community about responsible pet ownership.</li>
                    <li>Assist in the enforcement of animal welfare laws and regulations.</li>
                    <li>Support vaccination drives and veterinary health care for pets.</li>
                    <li>Respond to reports of animal abuse, neglect, or abandonment.</li>
                </ul>
            </div>
            <div>
                <h4 class="text-xl font-semibold text-gray-800 mt-4 mb-2">🌟 Mission & Vision:</h4>
                <p class="text-gray-700 mb-4">
                    <strong>Mission:</strong> To create a safe, nurturing environment where all pet animals are treated with compassion, respect, and dignity.
                </p>
                <p class="text-gray-700">
                    <strong>Vision:</strong> A community where every pet is valued, protected, and given the opportunity to live a healthy and happy life, free from harm and neglect.
                </p>
            </div>
        </div>

        <h4 class="text-xl font-semibold text-gray-800 mt-6 mb-2">❤️ Core Values:</h4>
        <ul class="list-disc list-inside text-gray-700 space-y-2">
            <li><strong>Compassion</strong> – Treating all animals with kindness and empathy.</li>
            <li><strong>Responsibility</strong> – Promoting responsible pet ownership and care.</li>
            <li><strong>Protection</strong> – Ensuring the safety and welfare of all pets.</li>
            <li><strong>Community Engagement</strong> – Encouraging public involvement in animal welfare efforts.</li>
            <li><strong>Advocacy</strong> – Upholding the rights and dignity of every pet animal.</li>
        </ul>
    </div>

    <!-- Pet Welfare Info -->
    <div class="bg-green-100 border-l-4 border-green-500 text-green-800 p-6 rounded-xl shadow-lg">
        <h3 class="text-xl font-bold mb-4">🐶 About Pet Welfare Tips</h3>
        <ul class="list-disc list-inside text-gray-700 space-y-3 text-sm">
            <li>Ensure pets have proper shelter, food, and clean water daily.</li>
            <li>Regular veterinary care is essential for maintaining health and keeping vaccinations updated.</li>
            <li>Abandoning, neglecting, or abusing animals is a criminal offense under RA 8485 (Animal Welfare Act).</li>
            <li>Adopt, don’t shop — give rescued animals a second chance at a loving home.</li>
            <li>Spay or neuter your pets to help control overpopulation and reduce stray animals.</li>
            <li>Microchipping and proper identification help return lost pets to their owners faster.</li>
            <li>Report animal cruelty, neglect, or abuse immediately.</li>
            <li>Join community programs supporting animal rescue, rehabilitation, and education.</li>
        </ul>

        <div class="mt-6 p-4 bg-yellow-100 border-l-4 border-yellow-400 text-yellow-700 rounded-md">
            <p class="text-sm font-semibold">Reminder:</p>
            <p class="text-xs mt-1">Always adopt from certified shelters. Ensure your pets receive yearly vaccinations and regular health checks. Responsible pet ownership is a lifetime commitment.</p>
        </div>
    </div>

</div>


        <script>
            const sidebar = document.getElementById("sidebar");
            const mainContent = document.getElementById("mainContent");
            const toggleSidebar = document.getElementById("toggleSidebar");
            const closeSidebarMobile = document.getElementById("closeSidebarMobile");

            // Toggle Sidebar for PC & Mobile
            toggleSidebar.addEventListener("click", function() {
                if (window.innerWidth <= 768) {
                    sidebar.classList.toggle("open"); // Mobile Mode
                } else {
                    sidebar.classList.toggle("closed"); // PC Mode
                    mainContent.classList.toggle("shrink");
                }
            });

            // Close Sidebar on Mobile when "✖" is clicked
            closeSidebarMobile.addEventListener("click", function() {
                sidebar.classList.remove("open");
            });

            function updateTime() {
                let now = new Date();
                let timeString = now.toLocaleTimeString(); // Format: HH:MM:SS AM/PM
                document.getElementById("currentTime").textContent = timeString;
            }

            // Update time every second
            setInterval(updateTime, 1000);
            updateTime(); // Call once to display immediately

            // JavaScript to update current time and date
            function updateTimeAndDate() {
                // Get current date and time
                const currentTime = new Date();

                // Format current time (e.g., 12:34 PM)
                const formattedTime = currentTime.toLocaleTimeString([], {
                    hour: '2-digit',
                    minute: '2-digit'
                });

                // Format current date (e.g., April 4, 2025)
                const formattedDate = currentTime.toLocaleDateString([], {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });

                // Update the current time and date in the DOM
                document.getElementById('currentTime').textContent = formattedTime;
                document.getElementById('currentDate').textContent = formattedDate;
            }

            // Update time and date every minute
            setInterval(updateTimeAndDate, 60000);

            // Initial call to update the time and date immediately
            updateTimeAndDate();
        </script>

</body>

</html>