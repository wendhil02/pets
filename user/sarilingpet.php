<?php
ob_start();
session_start();
include 'design/top.php';
include 'design/mid.php';
include '../internet/connect_ka.php';

if (!isset($_SESSION['email']) || !isset($_SESSION['session_key'])) {
    session_unset();
    session_destroy();
    header("Location: ../index.php");
    exit(); // Make sure you call exit after header() to stop further execution
}

// Now you can access the user data and session key in the protected file
$email = $_SESSION['email'];
$first_name = $_SESSION['first_name'];
$middle_name = $_SESSION['middle_name'];
$last_name = $_SESSION['last_name'];
$session_key = $_SESSION['session_key']; // session_key is available here

// ✅ Verify if the user with the email exists and is approved
$sql_check_user = "SELECT email FROM registerlanding WHERE email = ? AND status = 'approved'";
$stmt_check_user = $conn->prepare($sql_check_user);
if (!$stmt_check_user) {
    die("Error: Query preparation failed - " . $conn->error);
}

$stmt_check_user->bind_param("s", $email);
$stmt_check_user->execute();
$stmt_check_user->store_result();

// If the user does not exist or is not approved, log them out
if ($stmt_check_user->num_rows === 0) {
    // No matching user found, redirect to login page
    session_unset();
    session_destroy();
    header("Location: ../index.php");
    exit();
}

// ✅ Fetch only Fully Vaccinated pets EXCLUDING "approved"
$sql = "SELECT id, petname, breed, type, age, vaccine_status, status, email, image 
        FROM pet 
        WHERE email = ? 
        AND vaccine_status = 'Fully Vaccinated' 
        AND status != 'approved'
        ORDER BY FIELD(status, 'pending', 'rejected', 'available', 'own'), id DESC";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Error: Query preparation failed - " . $conn->error);
}

// ✅ Bind the email parameter correctly
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
ob_end_flush();

// Your logic to display pets
?>


<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>



<body class="flex bg-gray-100">
    <div id="mainContent" class="main-content flex-1 transition-all">
        <!-- Navbar -->
        <!-- Navbar -->
        <nav class="bg-[#0077b6] shadow-md mt-3 mr-2 ml-2 p-2 flex items-center justify-between rounded-lg max-w-auto mx-auto">
            <!-- ☰ Button -->
            <button id="toggleSidebar" class="text-white text-lg px-2 py-1 hover:bg-blue-100 rounded-md border border-transparent">
                ☰
            </button>
            

            <div class="flex items-center gap-4">  
                <!--  Notification Bell --><span id="currentTime" class="text-white font-semibold text-sm md:text-base lg:text-lg"></span>
                <div class="relative">

                    <button id="notifButton" class="text-white text-lg relative">
                    <i class="fa-solid fa-bell fa-shake"></i> <!-- Font Awesome Icon -->
                        <span id="notifBadge" class="absolute -top-1 -right-2 bg-red-500 text-white text-xs font-bold px-1.5 py-0.5 rounded-full hidden">0</span>
                    </button>


                    <!-- 📜 Notification Dropdown -->
                    <div id="notifDropdown" class="hidden absolute right-0 mt-2 w-64 bg-white border border-gray-300 shadow-lg rounded-lg p-2">
                        <p class="text-gray-700 font-semibold mb-2">Approved Pets</p>
                        <ul id="notifList" class="text-gray-600 text-sm"></ul>
                    </div>

                </div>

                <!-- Welcome Message -->
                <span class="font-bold text-white text-sm md:text-base lg:text-lg">
                    Welcome, <?= htmlspecialchars($email) ?>
                </span>
            </div>
        </nav>


        <div class="p-4 bg-gray-200 mt-3 mr-2 ml-2 rounded-lg shadow-lg">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold">Your Pets & Pets for Adoption</h2>

                <div class="flex gap-3">
                    <!-- 🟢 Guide Button -->
                    <button onclick="openGuideModal()" class="px-4 py-2 bg-blue-500 text-white rounded-lg shadow-md hover:bg-blue-600">
                        📖 Guide
                    </button>
                    <!-- 🟢 Schedule Surrender Button -->
                    <a href="unvaccinepet.php" class="px-4 py-2 bg-green-500 text-white rounded-lg shadow-md hover:bg-red-600">
                        Not vaccinated
                    </a>

                    <!-- 🟢 Schedule Surrender Button -->
                    <a href="schedule_surrender.php" class="px-4 py-2 bg-green-500 text-white rounded-lg shadow-md hover:bg-red-600">
                        Schedule
                    </a>
                </div>
            </div>
            <div id="guideModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden z-50">

                <div class="bg-white p-6 rounded-lg shadow-lg max-w-md w-full">
                    <h3 class="text-xl font-bold mb-3">📖 Pet Surrender & Adoption Guide</h3>

                    <p class="text-gray-700 mb-2"><strong>🟢 Surrendering a Pet:</strong></p>
                    <ul class="list-disc list-inside text-gray-600 mb-4">
                        <li>Carefully consider your decision before surrendering your pet.</li>
                        <li>Ensure your pet is in good health and has up-to-date vaccinations.</li>
                        <li>Submit a surrender request for initial review.</li>
                        <li>You will be required to schedule an appointment at the Pet Animal Welfare Protection center.</li>
                        <li>During your visit, the information provided will be verified before receiving final approval from the administrator.</li>
                    </ul>

                    <p class="text-gray-700 mb-2"><strong>🟢 Adopting a Pet:</strong></p>
                    <ul class="list-disc list-inside text-gray-600 mb-4">
                        <li>Select a pet from the list of approved animals available for adoption.</li>
                        <li>Await confirmation of your adoption request from the administrator.</li>
                        <li>Once approved, schedule a pickup date for your newly adopted pet.</li>
                    </ul>


                    <!-- ✅ Close Button -->
                    <button onclick="closeGuideModal()" class="mt-2 w-full bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-600">
                        Close
                    </button>
                </div>
            </div>

            <?php if ($result->num_rows > 0): ?>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <div class="border rounded-lg p-4 shadow bg-gray-50 relative">
                            <img src="../uploads/<?php echo htmlspecialchars($row['image']); ?>"
                                alt="Pet Image"
                                class="w-full h-auto max-h-40 object-cover rounded">

                            <h3 class="text-lg font-bold mt-2">
                                <?php echo htmlspecialchars($row['petname']); ?>
                            </h3>
                            <p class="text-sm text-gray-600">
                                <?php echo htmlspecialchars($row['breed']); ?> - <?php echo htmlspecialchars($row['type']); ?>
                            </p>
                            <p class="text-sm">Age: <?php echo htmlspecialchars($row['age']); ?> years old</p>
                            <p class="text-sm">Vaccine: <?php echo htmlspecialchars($row['vaccine_status']); ?></p>

                            <?php
                            $status = htmlspecialchars(trim($row['status']));
                            $statusClass = ($status == 'pending') ? 'text-yellow-600' : (($status == 'rejected') ? 'text-red-600' : 'text-blue-600');
                            ?>

                            <p class="text-sm font-semibold">
                                Status: <span id="status-<?php echo $row['id']; ?>" class="<?php echo $statusClass; ?>">
                                    <?php echo ucfirst($status); ?>
                                </span>
                            </p>

                            <?php if ($row['status'] === 'own'): ?>
                                <button onclick="openModal(<?php echo $row['id']; ?>)"
                                    class="text-xs bg-red-500 text-white px-2 py-1 rounded hover:bg-red-600 absolute top-2 right-2">
                                    Surrender
                                </button>
                            <?php endif; ?>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <p class="text-center text-gray-500">
                    No pets to display for this email: <strong><?php echo htmlspecialchars($email); ?></strong>
                </p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal -->
    <div id="surrenderModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex justify-center items-center">
        <div class="bg-white p-6 rounded-lg shadow-lg text-center">
            <h2 class="text-lg font-bold mb-4">Confirm Surrender</h2>
            <p>Are you sure you want to surrender this pet for adoption?</p>
            <p>set your schedule to come</p>

            <form id="surrenderForm">
                <input type="hidden" name="pet_id" id="modalPetId">
                <div class="mt-4">
                    <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">
                        Yes, Surrender
                    </button>
                    <button type="button" onclick="closeModal()" class="bg-gray-300 px-4 py-2 rounded ml-2">
                        No, Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
    
    document.addEventListener("DOMContentLoaded", function () {
    let notifDropdownVisible = false;

    function fetchNotifications() {
        fetch("fetch_approved_pets.php")
            .then(response => response.json())
            .then(data => {
                const notifBadge = document.getElementById("notifBadge");
                const notifList = document.getElementById("notifList");

                notifList.innerHTML = ""; // Clear list bago mag-update

                let unseenCount = 0; // Bilangin ang hindi pa nakikitang notif

                if (data.length > 0) {
                    data.forEach(notif => {
                        if (notif.seen === 0) unseenCount++; // Bilangin ang hindi pa nababasa

                        const li = document.createElement("li");
                        li.className = `border-b border-gray-200 p-2 cursor-pointer hover:bg-gray-100 ${
                            notif.seen === 0 ? "font-bold" : "text-gray-500"
                        }`;

                        li.innerHTML = `✅ <strong>${notif.petname}</strong> is Approved!<br>
                            <span class="text-xs text-gray-500">${notif.approved_at}</span>`;

                        // ✅ Update seen status kapag na-click
                        li.addEventListener("click", function () {
                            markNotificationAsSeen(notif.id, li);
                        });

                        notifList.appendChild(li);
                    });

                    // ✅ Update red badge count
                    if (unseenCount > 0) {
                        notifBadge.textContent = unseenCount;
                        notifBadge.classList.remove("hidden");
                    } else {
                        notifBadge.classList.add("hidden");
                    }

                } else {
                    notifBadge.classList.add("hidden"); // Walang bagong notif
                    notifList.innerHTML = "<li class='p-2 text-gray-400'>No new approvals</li>";
                }
            })
            .catch(error => console.error("Error fetching notifications:", error));
    }

    function markNotificationAsSeen(notificationId, listItem) {
        fetch("mark_seen.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ notification_id: notificationId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                listItem.classList.remove("font-bold");
                listItem.classList.add("text-gray-500");

                // Re-fetch notifications to update red badge
                fetchNotifications();
            }
        })
        .catch(error => console.error("Error marking notifications as seen:", error));
    }

    // ✅ Toggle Dropdown at Fetch Notifications
    document.getElementById("notifButton").addEventListener("click", function() {
        const notifDropdown = document.getElementById("notifDropdown");
        notifDropdownVisible = !notifDropdownVisible;

        notifDropdown.classList.toggle("hidden", !notifDropdownVisible);

        if (notifDropdownVisible) {
            fetchNotifications();
        }
    });

    // ✅ Automatic fetch on page load
    fetchNotifications();

    // ✅ Auto-refresh every 30 seconds (pero hindi gagalaw ang dropdown)
    setInterval(() => {
        if (!notifDropdownVisible) {
            fetchNotifications();
        }
    }, 30000);
});


        function openModal(petId) {
            document.getElementById('modalPetId').value = petId;
            document.getElementById('surrenderModal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('surrenderModal').classList.add('hidden');
        }

        document.getElementById('surrenderForm').addEventListener('submit', function(event) {
            event.preventDefault();

            let petId = document.getElementById('modalPetId').value;
            let formData = new FormData();
            formData.append("pet_id", petId);

            fetch("surrender_pet.php", {
                    method: "POST",
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        let petStatus = document.getElementById("status-" + petId);
                        if (petStatus) {
                            petStatus.innerText = "Pending";
                            petStatus.classList.remove("text-blue-600", "text-red-600");
                            petStatus.classList.add("text-yellow-600");
                        }

                        let surrenderButton = document.querySelector(`button[onclick="openModal(${petId})"]`);
                        if (surrenderButton) {
                            surrenderButton.remove();
                        }

                        closeModal();
                    } else {
                        alert("Error: " + data.message);
                    }
                })
                .catch(error => console.error("Error:", error));
        });

        function openGuideModal() {
            document.getElementById('guideModal').classList.remove('hidden');
        }

        function closeGuideModal() {
            document.getElementById('guideModal').classList.add('hidden');
        }
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
    </script>

</body>