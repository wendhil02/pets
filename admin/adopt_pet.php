<?php
session_start();
include 'design/top.php';
include 'design/mid.php';
include '../internet/connect_ka.php';

if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

$result = $conn->query("SELECT COUNT(*) AS total FROM adoption_history");
$row = $result->fetch_assoc();
if ($row['total'] == 0) {
    $conn->query("ALTER TABLE adoption_history AUTO_INCREMENT = 1");
}

$email = $_SESSION['email'];

// ===== STEP 1: Automatic transfer of all adoption_requests (including pending) into adoption_history =====

$sql_fetch = "SELECT id, pet_id, email, contact, status, schedule_date, created_at, pickup_time FROM adoption_requests"; // All requests, including pending
$result_fetch = $conn->query($sql_fetch);

if ($result_fetch->num_rows > 0) {
    while ($row = $result_fetch->fetch_assoc()) {
        $id = $row['id'];
        $pet_id = $row['pet_id'];
        $request_email = $row['email'];
        $contact = $row['contact'];
        $status = $row['status'];
        $schedule_date = $row['schedule_date'];
        $created_at = $row['created_at']; // This is the correct field for request date
        $pickup_time = $row['pickup_time'];

        // Check if already exists in adoption_history to avoid duplicates
        $check = $conn->prepare("SELECT id FROM adoption_history WHERE id = ?");
        $check->bind_param("i", $id);
        $check->execute();
        $check_result = $check->get_result();

        if ($check_result->num_rows == 0) {
            // Insert into adoption_history (corrected: added created_at)
            $insertSql = "INSERT INTO adoption_history (id, pet_id, email, contact, status, schedule_date, created_at, pickup_time) 
                          VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($insertSql);
            $stmt->bind_param("iissssss", $id, $pet_id, $request_email, $contact, $status, $schedule_date, $created_at, $pickup_time);
            $stmt->execute();
        }
    }
}

// ===== STEP 2: Fetch and display data from adoption_history =====

$sql_history = "SELECT ah.id, ah.email, ah.pet_id, p.petname, ah.contact, ah.status, ah.schedule_date, ah.created_at, ah.pickup_time
                FROM adoption_history ah
                JOIN pet p ON ah.pet_id = p.id
                ORDER BY ah.created_at DESC";
$stmt = $conn->prepare($sql_history);
$stmt->execute();
$result_history = $stmt->get_result();
?>

<head>
    <title>Adopt Pet</title>
</head>

<body class="flex bg-gray-100">
    <div id="mainContent" class="main-content flex-1 transition-all">
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

        <div class="flex justify-center items-center bg-gray-100 px-4 w-full">
            <div class="p-2 bg-white shadow-2xl rounded-2xl w-full max-w-7xl mt-5">

                <!-- Title -->
                <div class="mb-6 text-center">
                    <h2 class="text-3xl font-bold text-gray-800">Adoption History</h2>
                    <p class="text-gray-500 mt-2">All Rejected Requests</p>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto rounded-lg">
                    <table class="min-w-full bg-white border border-gray-300 rounded-lg overflow-hidden">
                        <thead class="bg-gradient-to-r from-blue-500 to-blue-700 text-white">
                            <tr>
                                <th class="px-6 py-3 text-left text-sm font-semibold">Email</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold">Pet ID</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold">Pet Name</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold">Contact</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold">Status</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold">Schedule Date</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold">Request Date</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold">Pickup Time</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-700">
                            <?php
                            if ($result_history->num_rows > 0) {
                                $rowIndex = 0;
                                while ($row = $result_history->fetch_assoc()) {
                                    $bgColor = $rowIndex % 2 == 0 ? 'bg-gray-50' : 'bg-white';
                                    echo "<tr class='$bgColor hover:bg-blue-50 transition'>";
                                    echo "<td class='px-6 py-4 border-b'>" . htmlspecialchars($row['email']) . "</td>";
                                    echo "<td class='px-6 py-4 border-b'>" . htmlspecialchars($row['pet_id']) . "</td>";
                                    echo "<td class='px-6 py-4 border-b'>" . htmlspecialchars($row['petname']) . "</td>";
                                    echo "<td class='px-6 py-4 border-b'>" . htmlspecialchars($row['contact']) . "</td>";

                                    // Status with badge style
                                    $status = htmlspecialchars($row['status']);
                                    $badgeColor = ($status == 'approved') ? 'bg-green-100 text-green-700' : (($status == 'rejected') ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700');
                                    echo "<td class='px-6 py-4 border-b'><span class='px-2 py-1 rounded-full text-xs font-semibold $badgeColor'>$status</span></td>";

                                    echo "<td class='px-6 py-4 border-b'>" . htmlspecialchars($row['schedule_date']) . "</td>";
                                    echo "<td class='px-6 py-4 border-b'>" . htmlspecialchars($row['created_at']) . "</td>";
                                    echo "<td class='px-6 py-4 border-b'>" . htmlspecialchars($row['pickup_time']) . "</td>";
                                    echo "</tr>";

                                    $rowIndex++;
                                }
                            } else {
                                echo "<tr><td colspan='8' class='px-6 py-4 text-center text-gray-500'>No adoption history found.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
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