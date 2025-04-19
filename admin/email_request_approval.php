<?php

session_start();
include 'design/top.php';
include 'design/mid.php';
require '../internet/connect_ka.php';
if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

$email = $_SESSION['email'];
// Fetch all pending adoption requests
// Fetch all adoption requests where email_confirmed has a value (not null or 0)
$sql = "SELECT ar.id, p.id AS pet_id, p.petname, ar.email AS adopter_email, ar.contact, ar.status, ar.schedule_date, ar.pickup_time, ar.email_confirmed
        FROM adoption_requests ar
        JOIN pet p ON ar.pet_id = p.id
        WHERE ar.status = 'pending' AND ar.email_confirmed IS NOT NULL AND ar.email_confirmed != 0";


$result = $conn->query($sql);
$pendingRequests = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $pendingRequests[] = $row;
    }
}

// Check if the 'id' is passed via URL and handle adoption approval
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Fetch the adoption request details for the given 'id'
    $sql = "SELECT ar.id, p.id AS pet_id, p.petname, ar.email AS adopter_email, ar.contact, ar.status, ar.schedule_date, ar.pickup_time, ar.email_confirmed
            FROM adoption_requests ar
            JOIN pet p ON ar.pet_id = p.id
            WHERE ar.id = ? AND ar.status = 'pending'";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $request = $result->fetch_assoc();

        // Update the status of the adoption request to 'approved' and set email_confirmed to 1
        $updateSql = "UPDATE adoption_requests SET status = 'approved', email_confirmed = 1 WHERE id = ?";
        $updateStmt = $conn->prepare($updateSql);
        $updateStmt->bind_param("i", $id);
        $updateStmt->execute();

        // Send email notifications


        echo "Request approved and notifications sent!";
    } else {
        echo "Request not found or already approved.";
    }
}

$conn->close();


?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pending Adoption Requests</title> <!-- Updated the title to match the content -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.0.3/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="flex bg-gray-100">


    <div id="mainContent" class="main-content flex-1 transition-all ">
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
        <div class="container mx-auto p-6">
            <h2 class="text-3xl font-semibold text-center text-[#0077b6] mb-6">Pending Adoption Requests</h2> <!-- Updated heading -->

            <table class="min-w-full table-auto bg-white shadow-lg rounded-lg">
                <thead>
                    <tr class="bg-[#0077b6] text-white text-center">
                        <th class="border border-gray-300 p-3">Pet Name</th>
                        <th class="border border-gray-300 p-3">Adopter Email</th>
                        <th class="border border-gray-300 p-3">Contact</th>

                        <th class="border border-gray-300 p-3">Action</th> <!-- Added column for action (approve/reject) -->
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($pendingRequests) > 0): ?>
                        <?php foreach ($pendingRequests as $request): ?>
                            <tr class="text-center">
                                <td class="border border-gray-300 p-3"><?= htmlspecialchars($request['petname']) ?></td>
                                <td class="border border-gray-300 p-3"><?= htmlspecialchars($request['adopter_email']) ?></td>
                                <td class="border border-gray-300 p-3"><?= htmlspecialchars($request['contact']) ?></td>

                                <td class="border border-gray-300 p-3">
                                    <a href="get_approved_requests.php?id=<?= $request['id'] ?>" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 focus:outline-none">
                                        Approve
                                    </a>
                                </td> <!-- Action button to approve -->
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="border border-gray-300 p-3 text-center">No pending adoption requests.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const sidebar = document.getElementById("sidebar");
            const mainContent = document.getElementById("mainContent");
            const toggleSidebar = document.getElementById("toggleSidebar");
            const closeSidebarMobile = document.getElementById("closeSidebarMobile");

            if (sidebar && mainContent && toggleSidebar && closeSidebarMobile) {
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
            }

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
        });
    </script>

</body>

</html>