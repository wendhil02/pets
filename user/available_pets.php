<?php
session_start();
include 'design/mid.php';
include 'design/top.php';
include '../internet/connect_ka.php';

if (!isset($_SESSION['email'])) {
    die("<p class='text-red-500'> You must be logged in to view pet adoption listings.</p>");
}

$user_email = $_SESSION['email']; // Get user email from session

// Fetch pets that are approved and NOT owned by the logged-in user
$sql = "SELECT id, petname, age, type, breed, image, email FROM pet WHERE status = 'approved' AND email != ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_email);
$stmt->execute();
$result = $stmt->get_result();
?>

<body class="flex bg-gray-100">
    <!-- Main Content -->
    <div id="mainContent" class="main-content flex-1 transition-all">
        <nav class="bg-[#0077b6] shadow-md mt-3 mr-2 ml-2 p-2 flex items-center justify-between rounded-lg max-w-auto mx-auto">
            <button id="toggleSidebar" class="text-white text-lg px-2 py-1 hover:bg-blue-100 rounded-md border border-transparent">
                ☰
            </button>

            <div class="flex items-center gap-4">
                <span id="currentTime" class="text-white font-semibold text-sm md:text-base lg:text-lg"></span>
                <span class="font-bold text-white text-sm md:text-base lg:text-lg">
                    Welcome, <?= htmlspecialchars($user_email) ?>
                </span>
            </div>
        </nav>

        <!-- Dashboard Content -->
        <div class="p-4 bg-white mt-3 mr-2 ml-2 rounded-lg shadow-lg">
            <h2 class="text-lg font-semibold mb-4">Available Pets for Adoption</h2>
            <?php if ($result->num_rows > 0): ?>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 ">
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <div class="border rounded-lg p-4 shadow bg-blue-50">
                            <img src="../uploads/<?php echo htmlspecialchars($row['image']); ?>" alt="Pet Image" class="w-full h-32 object-cover rounded">
                            <h3 class="text-lg font-bold mt-2"><?php echo htmlspecialchars($row['petname']); ?></h3>
                            <p class="text-sm text-gray-600"><?php echo htmlspecialchars($row['breed']); ?> - <?php echo htmlspecialchars($row['type']); ?></p>
                            <p class="text-sm">Age: <?php echo htmlspecialchars($row['age']); ?> years</p>
                            <p class="text-sm text-gray-700">📧 Owner Email: <?php echo htmlspecialchars($row['email']); ?></p>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <p class="text-center text-gray-500">No pets available for adoption at the moment.</p>
            <?php endif; ?>
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
    </script>

</body>