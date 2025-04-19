<?php
session_start();
include 'design/top.php';
include 'design/mid.php';
include '../internet/connect_ka.php';
if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

$email = $_SESSION['email'];
// Fetch reports
$sql = "SELECT * FROM cruelty_reports_archive ORDER BY archived_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Archived Reports</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex bg-gray-100 font-poppins">
        <div id="mainContent" class="main-content flex-1 transition-all">
        <!-- Navbar -->
      <nav class="bg-[#0077b6] shadow-md mt-3 mr-2 ml-2 p-2 flex items-center justify-between rounded-lg max-w-auto mx-auto">
    <!--  Button -->
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


    <div class="max-w-6xl mx-auto mt-8 p-6 bg-white rounded-lg shadow-md ml-2 mr-2">
        <h2 class="text-2xl font-bold text-gray-800 mb-4">📦 Archived Reports</h2>

        <?php if ($result->num_rows > 0): ?>
            <table class="w-full border border-gray-300 rounded-lg">
                <thead class="bg-gray-600 text-white text-sm">
                    <tr>
                        <th class="p-2 border">Reporter Email</th>
                        <th class="p-2 border">Location</th>
                        <th class="p-2 border">Date & Time</th>
                        <th class="p-2 border">Description</th>
                        <th class="p-2 border">Evidence</th> <!-- Added Evidence Column -->
                        <th class="p-2 border">Archived At</th>
                        <th class="p-2 border">Action</th> <!-- Added Action Column -->
                    </tr>
                </thead>
                <tbody class="text-sm">
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr class="bg-gray-50 hover:bg-gray-100">
                            <td class="p-2 border"><?= htmlspecialchars($row["reporter_email"]); ?></td>
                            <td class="p-2 border"><?= htmlspecialchars($row["incident_location"]); ?></td>
                            <td class="p-2 border"><?= htmlspecialchars($row["incident_datetime"]); ?></td>
                            <td class="p-2 border"><?= nl2br(htmlspecialchars($row["incident_description"])); ?></td>
                            <td class="p-2 border text-center">
                                <?php if (!empty($row["evidence_path"])): ?>
                                    <a href="<?= htmlspecialchars($row["evidence_path"]); ?>" class="text-blue-500 hover:underline" target="_blank">View Evidence</a>
                                <?php else: ?>
                                    <span class="text-gray-500">No Evidence</span>
                                <?php endif; ?>
                            </td>
                            <td class="p-2 border"><?= htmlspecialchars($row["archived_at"]); ?></td>
                            <td class="p-2 border text-center">
                                <!-- Action Button (Download PDF) -->
                                <a href="download_report.php?id=<?= $row['id']; ?>" class="text-blue-500 hover:underline">Download PDF</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="text-gray-500 text-sm text-center">No archived reports.</p>
        <?php endif; ?>
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
    const formattedTime = currentTime.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    
    // Format current date (e.g., April 4, 2025)
    const formattedDate = currentTime.toLocaleDateString([], { year: 'numeric', month: 'long', day: 'numeric' });

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

<?php
$conn->close();
?>
