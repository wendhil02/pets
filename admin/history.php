<?php
session_start();
include '../internet/connect_ka.php';
include 'design/top.php';
include 'design/mid.php';

if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

$email = $_SESSION['email'];

// Default to 'all' if no filter is selected
$filter = isset($_GET['status']) ? $_GET['status'] : 'all';

// Query to fetch adoption data
$sql = "
    SELECT 
        ar.id AS request_id,
        ar.email AS adopter_email,
        ar.contact,
        ar.status AS request_status,
        ar.schedule_date,
        ar.created_at AS request_created,
        ar.pickup_time,
        p.petname,
        p.type,
        p.breed,
        p.image,
        p.status AS pet_status
    FROM adoption_requests_archive ar
    JOIN pet p ON ar.pet_id = p.id
";

// Adding the filter logic
if ($filter !== 'all') {
    $sql .= " WHERE ar.status = '$filter' ";
} else {
    $sql .= " WHERE ar.status IN ('pending', 'approved', 'own', 'rejected') ";
}

$sql .= " ORDER BY ar.created_at DESC ";

$result = $conn->query($sql);
?>

<body class="flex bg-gray-100">
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

        <div class="p-4 bg-white mt-2 mr-2 ml-2 rounded-lg shadow-lg">
            <h2 class="text-xl font-bold mb-4">Adoption History</h2>

            <!-- Search Bar -->
            <div class="mb-4">
                <label for="search" class="block mb-2 font-semibold">Search:</label>
                <input type="text" id="search" class="border p-2 rounded w-full" placeholder="Search by pet name or adopter email">
            </div>

            <div class="mb-4">
                <label for="statusFilter" class="block mb-2 font-semibold">Filter by Status:</label>
                <select id="statusFilter" class="border p-2 rounded w-40">
                    <option value="all" <?= $filter === 'all' ? 'selected' : '' ?>>All</option>
                    <option value="pending" <?= $filter === 'pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="approved" <?= $filter === 'approved' ? 'selected' : '' ?>>Approved</option>
                    <option value="own" <?= $filter === 'own' ? 'selected' : '' ?>>Own</option>
                    <option value="rejected" <?= $filter === 'rejected' ? 'selected' : '' ?>>Rejected</option>
                </select>
            </div>

            <table class="w-full text-sm text-left text-gray-700 border-collapse">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="p-2">Pet</th>
                        <th class="p-2">Type</th>
                        <th class="p-2">Breed</th>
                        <th class="p-2">Status</th>
                        <th class="p-2">Adopter Email</th>
                        <th class="p-2">Contact</th>
                        <th class="p-2">Schedule</th>
                        <th class="p-2">Pickup Time</th>
                        <th class="p-2">Date Requested</th>
                    </tr>
                </thead>
                <tbody id="resultsTable">
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr class="border-b result-row">
                            <td class="p-2 flex items-center gap-2">
                                <img src="../uploads/<?= $row['image'] ?>" alt="pet" class="w-10 h-10 object-cover rounded">
                                <?= htmlspecialchars($row['petname']) ?>
                            </td>
                            <td class="p-2"><?= htmlspecialchars($row['type']) ?></td>
                            <td class="p-2"><?= htmlspecialchars($row['breed']) ?></td>
                            <td class="p-2 font-semibold capitalize 
                            <?= $row['request_status'] === 'approved' ? 'text-green-600' : ($row['request_status'] === 'rejected' ? 'text-red-600' : 'text-yellow-600') ?>">
                                <?= $row['request_status'] ?>
                            </td>
                            <td class="p-2"><?= htmlspecialchars($row['adopter_email']) ?></td>
                            <td class="p-2"><?= htmlspecialchars($row['contact']) ?></td>
                            <td class="p-2"><?= htmlspecialchars($row['schedule_date']) ?></td>
                            <td class="p-2"><?= htmlspecialchars($row['pickup_time']) ?></td>
                            <td class="p-2"><?= date("M d, Y", strtotime($row['request_created'])) ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        document.getElementById('search').addEventListener('input', function() {
            let searchTerm = this.value.toLowerCase();
            let rows = document.querySelectorAll('.result-row');

            rows.forEach(row => {
                let petName = row.querySelector('td:nth-child(1)').textContent.toLowerCase();
                let adopterEmail = row.querySelector('td:nth-child(5)').textContent.toLowerCase();

                if (petName.includes(searchTerm) || adopterEmail.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });

        document.getElementById('statusFilter').addEventListener('change', function() {
            window.location.href = 'history.php?status=' + this.value;
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