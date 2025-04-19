<?php
ob_start();
session_start();
include 'design/top.php';
include 'design/mid.php';
include '../internet/connect_ka.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

$email = $_SESSION['email'];
$sql = "SELECT * FROM pet";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body class="flex bg-gray-100">
    <!-- Main Content -->
    <div id="mainContent" class="main-content flex-1 transition-all">

        <nav class="bg-[#0077b6] shadow-md mt-3 mr-2 ml-2 p-2 flex items-center justify-between rounded-lg max-w-auto mx-auto">
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
        <div class="container mx-auto px-4 py-6 max-w-7xl">
            <h2 class="text-2xl font-semibold text-gray-800 mb-6">🐾 Registered Pets</h2>

            <div class="mb-4 flex justify-between items-center gap-4">
    <input
        type="text"
        id="searchInput"
        placeholder="Search pets..."
        class="w-full md:w-1/3 px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" />
</div>


            <div class="overflow-auto rounded-lg shadow border border-gray-200">
                <table class="min-w-full bg-white text-sm text-left text-gray-700">
                    <thead class="bg-[#0077b6] text-white uppercase">
                        <tr class="text-center">
                            <th class="px-4 py-3">Pet Name</th>
                            <th class="px-4 py-3">Age</th>
                            <th class="px-4 py-3">Type</th>
                            <th class="px-4 py-3">Breed</th>
                            <th class="px-4 py-3">Info</th>
                            <th class="px-4 py-3">Vaccine Status</th>
                            <th class="px-4 py-3">Vaccine Type</th>
                            <th class="px-4 py-3">Image</th>
                            <th class="px-4 py-3">Created At</th>
                            <th class="px-4 py-3">Owner Email</th>
                            <th class="px-4 py-3">Status</th>
                        </tr>

                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php if ($result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-2"><?= htmlspecialchars($row['petname'] ?? '') ?></td>
                                    <td class="px-4 py-2"><?= htmlspecialchars($row['age'] ?? '') ?></td>
                                    <td class="px-4 py-2"><?= htmlspecialchars($row['type'] ?? '') ?></td>
                                    <td class="px-4 py-2"><?= htmlspecialchars($row['breed'] ?? '') ?></td>
                                    <td class="px-4 py-2"><?= htmlspecialchars($row['info'] ?? '') ?></td>
                                    <td class="px-4 py-2">
                                        <span class="inline-block px-2 py-1 text-xs font-medium rounded-full
                                    <?= $row['vaccine_status'] === 'Vaccinated' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                            <?= htmlspecialchars($row['vaccine_status'] ?? '') ?>
                                        </span>
                                    </td>
                                    <td class="px-4 py-2"><?= htmlspecialchars($row['vaccine_type'] ?? '') ?></td>
                                    <td class="px-4 py-2">
                                        <?php if (!empty($row['image'])): ?>
                                            <img src="../uploads/<?= $row['image']; ?>" alt="Pet Image" class="w-12 h-12 object-cover rounded-full">
                                        <?php else: ?>
                                            <span class="text-gray-400 italic">N/A</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-4 py-2"><?= htmlspecialchars($row['created_at'] ?? '') ?></td>
                                    <td class="px-4 py-2"><?= htmlspecialchars($row['email'] ?? '') ?></td>
                                    <td class="px-4 py-2">
                                        <span class="inline-block px-2 py-1 text-xs font-semibold rounded 
                                    <?= $row['status'] === 'approved' ? 'bg-green-200 text-green-800' : 'bg-yellow-100 text-yellow-800'; ?>">
                                            <?= htmlspecialchars($row['status'] ?? '') ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="13" class="px-4 py-4 text-center text-gray-500 italic">No pets found in the database.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>


        <script>
            const searchInput = document.getElementById("searchInput");

            searchInput.addEventListener("keyup", function() {
                const filter = searchInput.value.toLowerCase();
                const rows = document.querySelectorAll("tbody tr");

                rows.forEach(row => {
                    const cells = row.querySelectorAll("td");
                    const match = Array.from(cells).some(td =>
                        td.textContent.toLowerCase().includes(filter)
                    );
                    row.style.display = match ? "" : "none";
                });
            });
        </script>

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

            // Close Sidebar on Mobile when "" is clicked
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