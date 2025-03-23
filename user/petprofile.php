<?php
session_start();
include 'design/mid.php';
include 'design/top.php';
include '../internet/connect_ka.php';

// ✅ Check if user is logged in
if (!isset($_SESSION['email'])) {
    die("<p class='text-red-500'>❌ You must be logged in to view your pet profile.</p>");
}

// ✅ Kunin ang email mula sa session
$user_email = $_SESSION['email'];

// ✅ Kunin ang pet details gamit ang email
$sql = "SELECT id, petname, breed, type, age, vaccine_status, vaccine_type, info, image, qr_code 
        FROM pet WHERE email = ? LIMIT 1";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("<p class='text-red-500'>❌ SQL Error: " . $conn->error . "</p>");
}

$stmt->bind_param("s", $user_email);
$stmt->execute();
$result = $stmt->get_result();

// ✅ Check kung may pet o wala
if ($result === false || $result->num_rows == 0) {
    $pet = null;
} else {
    $pet = $result->fetch_assoc();
    $img_path = "../uploads/" . $pet['image'];
    $qr_path = "../qrcodes/" . $pet['qr_code'];
}

?>

<body class="flex bg-gray-100">
    <div id="mainContent" class="main-content flex-1 transition-all">
        <nav class="bg-[#0077b6] shadow-md mt-3 mr-2 ml-2 p-2 flex items-center justify-between rounded-lg max-w-auto mx-auto">
            <!-- ☰ Button (For PC and Mobile) -->
            <button id="toggleSidebar" class="text-white text-lg px-2 py-1 hover:bg-blue-100 rounded-md border border-transparent">
                ☰
            </button>

            <div class="flex items-center gap-4">
                <!-- 🟢 Real-time Time Display -->
                <span id="currentTime" class="text-white font-semibold text-sm md:text-base lg:text-lg"></span>

                <!-- Welcome Message -->
                <span class="font-bold text-white text-sm md:text-base lg:text-lg">
                    Welcome, <?= htmlspecialchars($user_email) ?>
                </span>
            </div>
        </nav>

        <div class="max-w-2xl mx-auto bg-white p-8 mt-6 rounded-2xl shadow-xl">
    <?php if ($pet): ?>
        <!-- ✅ Pet Profile Header -->
        <h1 class="text-3xl font-bold text-gray-900 text-center"><?= htmlspecialchars($pet['petname']) ?>'s Profile</h1>

        <div class="flex flex-col items-center mt-6">
            <!-- ✅ Pet Image -->
            <?php if (!empty($pet['image']) && file_exists($img_path)): ?>
                <img src="<?= $img_path ?>" alt="Pet Image" class="w-48 h-48 object-cover rounded-full shadow-lg border-4 border-blue-400">
            <?php else: ?>
                <p class="text-red-500 mt-2">❌ Image file not found.</p>
            <?php endif; ?>

            <!-- ✅ Pet Details -->
            <div class="mt-4 text-gray-700 text-center space-y-2">
                <p><strong class="text-gray-900">Type:</strong> <?= htmlspecialchars($pet['type']) ?></p>
                <p><strong class="text-gray-900">Breed:</strong> <?= htmlspecialchars($pet['breed']) ?></p>
                <p><strong class="text-gray-900">Age:</strong> <?= htmlspecialchars($pet['age']) ?> years</p>
                <p><strong class="text-gray-900">Vaccine Status:</strong> <?= htmlspecialchars($pet['vaccine_status']) ?></p>
                <p><strong class="text-gray-900">Vaccine Type:</strong> <?= htmlspecialchars($pet['vaccine_type']) ?></p>
                <p><strong class="text-gray-900">Additional Info:</strong> <?= nl2br(htmlspecialchars($pet['info'])) ?></p>
            </div>

            <!-- ✅ QR Code Section -->
            <div class="mt-6 p-4 bg-gray-100 rounded-lg shadow-md w-full text-center">
                <h2 class="text-lg font-semibold text-gray-900">QR Code</h2>
                <?php if (!empty($pet['qr_code']) && file_exists($qr_path)): ?>
                    <img src="<?= $qr_path ?>" alt="Pet QR Code" class="w-32 h-32 mt-3 mx-auto border-2 border-gray-300 shadow">
                    
                    <!-- 🟢 Download QR Code Button -->
                    <a href="<?= $qr_path ?>" download="<?= htmlspecialchars($pet['petname']) ?>_qrcode.png"
                       class="mt-4 inline-block px-5 py-2 bg-blue-500 text-white rounded-lg shadow-md hover:bg-blue-600 transition">
                        📥 Download QR Code
                    </a>
                <?php else: ?>
                    <p class="text-red-500 mt-2">❌ QR Code file not found.</p>
                <?php endif; ?>
            </div>
        </div>
    <?php else: ?>
        <!-- ✅ Message for No Pets -->
        <div class="text-center">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">No pets registered yet.</h1>
            <p class="text-gray-600">You haven't registered any pets. Please register your pet to view their profile.</p>
            
            <div class="flex justify-center mt-5">
                <a href="parehistro.php" class="px-5 py-3 bg-green-500 text-white rounded-lg shadow-md hover:bg-green-600 transition">
                    ➕ Register a Pet
                </a>
            </div>
        </div>
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