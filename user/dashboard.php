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
        <div class="p-6 bg-white mt-4">
            <!-- Announcements -->
            <div class="mb-6">
                <?php if (!empty($featured_images)): ?>
                    <div class="p-3 bg-gray-100 mt-2 rounded-lg shadow-md">
                        <h2 class="text-xl font-bold mb-4 text-gray-700 text-center">📢Pet Images</h2>

                        <div class="relative overflow-hidden rounded-lg mx-auto max-w-lg h-full">
                            <div class="flex animate-slider">
                                <?php foreach ($featured_images as $image): ?>
                                    <div class="flex-shrink-0 w-full">
                                        <img src="../uploads/featured/<?= htmlspecialchars($image['image_path']) ?>"
                                            alt="Featured Image"
                                            class="w-full h-full object-cover rounded-lg">
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <h2 class="text-xl font-bold mb-4 text-gray-700 mt-3">📢 Announcements</h2>

                <?php if ($announcement_result && $announcement_result->num_rows > 0): ?>
                    <?php while ($row = $announcement_result->fetch_assoc()): ?>
                        <div class="bg-blue-50 border-l-4 border-blue-400 text-blue-700 p-4 mb-3 rounded-md shadow-sm rounded-md">

                            <!-- Announcement Message -->
                            <div class="text-sm mb-2"><?= nl2br(htmlspecialchars($row['message'])) ?></div>

                            <!-- Announcement Image -->
                            <?php if (!empty($row['image'])): ?>
                                <div class="mt-2">
                                    <img src="../uploads/announcements/<?= htmlspecialchars($row['image']) ?>" alt="Announcement Image" class="rounded-md shadow-md max-w-full h-[200px]">
                                </div>
                            <?php endif; ?>

                            <!-- Timestamp -->
                            <div class="text-xs text-gray-500 mt-3">
                                Posted on <?= date("F j, Y, g:i A", strtotime($row['created_at'])) ?>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="text-sm text-gray-500">No announcements available.</p>
                <?php endif; ?>
            </div>


            <!-- Pet Welfare Info -->
            <div class="bg-green-50 border-l-4 border-green-400 text-green-700 p-4 rounded-md shadow-sm">
                <h3 class="text-lg font-semibold mb-2">🐾 About Pet Animal Welfare</h3>
                <ul class="list-disc list-inside text-sm space-y-1">
                    <li>Ensure pets have proper shelter, food, and clean water daily.</li>
                    <li>Regular veterinary care is essential for health and vaccinations.</li>
                    <li>Abandoning or abusing animals is a punishable offense under animal welfare laws.</li>
                    <li>Adopt, don’t shop — give rescued animals a second chance at life.</li>
                    <li>Spay or neuter your pets to help control the animal population.</li>
                </ul>
                <p class="text-xs text-gray-600 mt-3">Be a responsible pet owner. Every animal deserves love, care, and protection.</p>
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