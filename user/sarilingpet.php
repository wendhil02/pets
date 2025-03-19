<?php
session_start();
include 'design/top.php';
include 'design/mid.php';
include '../internet/connect_ka.php';

if (!isset($_SESSION['email'])) {
    header("Location: login.php"); // Redirect kung walang session
    exit();
}

$user_email = $_SESSION['email']; // Get logged-in user's email

// Fetch pets only registered by the logged-in user's email
$sql = "SELECT petname, age, type, breed, info, vaccine_status, vaccine_type, image 
        FROM registerlanding 
        WHERE email = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_email);
$stmt->execute();
$result = $stmt->get_result();
?>

<body class="flex bg-gray-100">
    <div id="mainContent" class="main-content flex-1 transition-all">
        <nav class="bg-[#0077b6] shadow-md mt-3 mr-2 ml-2 p-2 flex items-center justify-between rounded-lg max-w-auto mx-auto">
            <button id="toggleSidebar" class="text-white text-lg px-2 py-1 hover:bg-blue-100 rounded-md border border-transparent">
                ☰
            </button>
            <span class="font-bold text-white text-sm md:text-base lg:text-lg">Welcome, <?php echo $_SESSION['username']; ?></span>
        </nav>

        <div class="p-6 bg-white">
            <h2 class="text-lg font-semibold mb-4">Your Registered Pets</h2>

            <?php if ($result->num_rows > 0): ?>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <div class="border rounded-lg p-4 shadow">
                            <img src="../uploads/<?php echo htmlspecialchars($row['image']); ?>" alt="Pet Image" class="w-full h-32 object-cover rounded">
                            <h3 class="text-lg font-bold mt-2"><?php echo htmlspecialchars($row['petname']); ?></h3>
                            <p class="text-sm text-gray-600"><?php echo htmlspecialchars($row['breed']); ?> - <?php echo htmlspecialchars($row['type']); ?></p>
                            <p class="text-sm">Age: <?php echo htmlspecialchars($row['age']); ?> years</p>
                            <p class="text-sm">Vaccine: <?php echo htmlspecialchars($row['vaccine_status']); ?></p>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <p class="text-center text-gray-500">No registered pets yet.</p>
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
        </script>

</body>

</html>