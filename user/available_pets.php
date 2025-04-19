<?php
session_start();
include 'design/top.php';
include 'design/mid.php';
include '../internet/connect_ka.php';

if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'user') {
    header("Location: ../index.php");
    exit();
}
$email = $_SESSION['email'];

$first_name = $_SESSION['first_name'];
$middle_name = $_SESSION['middle_name'];
$last_name = $_SESSION['last_name'];
$session_key = $_SESSION['session_key'];
$user_email = $_SESSION['email']; // Get user email from session

// Fetch pets that are approved and NOT owned by the logged-in user
$sql = "SELECT id, petname, age, type, breed, image, email FROM pet WHERE status = 'approved' AND email != ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_email);
$stmt->execute();
$result = $stmt->get_result();
?>
<head>
     <title>Adopted List</title>
</head>
<body class="flex bg-gray-100">
    <!-- Main Content -->
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