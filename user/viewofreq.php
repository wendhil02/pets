<?php
ob_start();
session_start();
include 'design/top.php';
include 'design/mid.php';
require '../internet/connect_ka.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);
if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'user') {
    header("Location: index.php");
    exit();
}

$email = $_SESSION['email'];
$sql = "SELECT ar.id, p.id AS pet_id, p.petname, p.type, p.breed, ar.email AS adopter_email, ar.contact, ar.status, ar.schedule_date, ar.pickup_time, ar.email_confirmed, ar.owner_mark
        FROM adoption_requests ar
        JOIN pet p ON ar.pet_id = p.id
        WHERE p.email = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $_SESSION['email']);
$stmt->execute();
$result = $stmt->get_result();

?>

<body class="flex bg-gray-100">
    <div id="mainContent" class="main-content flex-1 transition-all">
        <nav class="bg-[#0077b6] shadow-md mt-3 mr-2 ml-2 p-2 flex items-center justify-between rounded-lg max-w-auto mx-auto">
            <button id="toggleSidebar" class="text-white text-lg px-2 py-1 hover:bg-blue-100 rounded-md border border-transparent">
                ☰
            </button>
            <div class="flex items-center gap-4 flex-grow">
                <span id="currentTime" class="text-white font-semibold text-sm md:text-base lg:text-lg"></span>
                <div id="currentDate" class="text-white font-semibold text-sm md:text-base lg:text-lg"></div>
            </div>
            <div class="flex items-center gap-4">
                <span class="font-bold text-white text-sm md:text-base lg:text-lg">
                    Welcome, <?= htmlspecialchars($email) ?>
                </span>
            </div>
        </nav>

        <div class="p-6 bg-white mt-4 mx-4 rounded-2xl shadow-2xl">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-3xl font-bold text-gray-800">🐾 Adoption Requests for Your Pets</h2>
    </div>

    <?php
    if (isset($_GET['mark_id'])) {
        $mark_id = $_GET['mark_id'];

        $mark = $conn->prepare("UPDATE adoption_requests SET owner_mark = 1 WHERE id = ?");
        $mark->bind_param("i", $mark_id);
        $mark->execute();

        echo '<div class="bg-green-100 text-green-800 font-semibold p-3 rounded-lg mb-4 shadow">Successfully marked!</div>';

        echo "<script>window.location.href='" . $_SERVER['PHP_SELF'] . "';</script>";
        exit();
    }
    ?>

    <div class="overflow-x-auto rounded-lg shadow">
        <table class="min-w-full bg-white border-separate border-spacing-y-2">
            <thead class="bg-[#0077b6] text-white rounded-lg">
                <tr>
                    <th class="py-4 px-6 text-left rounded-l-lg">Pet Name</th>
                    <th class="py-4 px-6 text-left">Type</th>
                    <th class="py-4 px-6 text-left">Breed</th>
                    <th class="py-4 px-6 text-left">Adopter Email</th>
                    <th class="py-4 px-6 text-left">Contact</th>
                    <th class="py-4 px-6 text-left">Status</th>
                    <th class="py-4 px-6 text-left">Mark Status</th>
                    <th class="py-4 px-6 text-left rounded-r-lg">Action</th>
                </tr>
            </thead>
            <tbody class="text-gray-700">
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr class="bg-gray-50 hover:bg-blue-50 transition duration-300">
                        <td class="py-4 px-6 font-semibold"><?= htmlspecialchars($row['petname']) ?></td>
                        <td class="py-4 px-6"><?= htmlspecialchars($row['type']) ?></td>
                        <td class="py-4 px-6"><?= htmlspecialchars($row['breed']) ?></td>
                        <td class="py-4 px-6"><?= htmlspecialchars($row['adopter_email']) ?></td>
                        <td class="py-4 px-6"><?= htmlspecialchars($row['contact']) ?></td>
                        <td class="py-4 px-6">
                            <?php if ($row['status'] == 'pending'): ?>
                                <span class="px-3 py-1 text-xs bg-yellow-100 text-yellow-800 font-semibold rounded-full">Pending</span>
                            <?php elseif ($row['status'] == 'approved'): ?>
                                <span class="px-3 py-1 text-xs bg-green-100 text-green-800 font-semibold rounded-full">Approved</span>
                            <?php elseif ($row['status'] == 'rejected'): ?>
                                <span class="px-3 py-1 text-xs bg-red-100 text-red-800 font-semibold rounded-full">Rejected</span>
                            <?php else: ?>
                                <?= htmlspecialchars($row['status']) ?>
                            <?php endif; ?>
                        </td>
                        <td class="py-4 px-6">
                            <?php if ($row['owner_mark'] == 1): ?>
                                <span class="px-3 py-1 text-xs bg-green-200 text-green-800 font-semibold rounded-full">Marked</span>
                            <?php else: ?>
                                <span class="px-3 py-1 text-xs bg-gray-300 text-gray-700 font-semibold rounded-full">Not Marked</span>
                            <?php endif; ?>
                        </td>
                        <td class="py-4 px-6">
                            <?php if ($row['owner_mark'] == 0): ?>
                                <a href="?mark_id=<?= $row['id'] ?>"
                                    class="inline-block bg-blue-500 hover:bg-blue-600 text-white text-xs font-bold py-2 px-4 rounded-full shadow transition-all duration-300">
                                    Mark
                                </a>
                            <?php else: ?>
                                <span class="text-gray-400 text-xs font-semibold">Already Marked</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>


        <script>
            const sidebar = document.getElementById("sidebar");
            const mainContent = document.getElementById("mainContent");
            const toggleSidebar = document.getElementById("toggleSidebar");
            const closeSidebarMobile = document.getElementById("closeSidebarMobile");

            toggleSidebar.addEventListener("click", function() {
                if (window.innerWidth <= 768) {
                    sidebar.classList.toggle("open");
                } else {
                    sidebar.classList.toggle("closed");
                    mainContent.classList.toggle("shrink");
                }
            });

            closeSidebarMobile?.addEventListener("click", function() {
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