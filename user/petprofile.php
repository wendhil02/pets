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
$user_email = $_SESSION['email'];
$pet = null; // Default pet data

// ✅ Check if `qr_id` is provided from the QR Code scan
if (isset($_GET['id'])) {
    $qr_id_value = $_GET['id']; // This should be the `qr_id` stored in the database

    // ✅ Find pet using `qr_id` instead of `qr_code`
    $sql = "SELECT id, petname, breed, type, age, vaccine_status, vaccine_type, info, image, qr_code, qr_id 
            FROM pet 
            WHERE qr_id = ? AND email = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $qr_id_value, $user_email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $pet = $result->fetch_assoc();
    }
}

// ✅ If no pet is found using `qr_id`, get the first pet of the user
if (!$pet) {
    $sql = "SELECT id, petname, breed, type, age, vaccine_status, vaccine_type, info, image, qr_code, qr_id 
            FROM pet 
            WHERE email = ? 
            LIMIT 1";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $user_email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $pet = $result->fetch_assoc();
    }
}
?>
<head>
     <title>Pet Profile</title>
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

        <div class="max-w-2xl mx-auto bg-white p-8 mt-6 rounded-2xl shadow-xl">
        <?php if ($pet): ?>
    <h1 class="text-3xl font-bold text-gray-900 text-center">
        <span id="petNameDisplay"><?= htmlspecialchars($pet['petname']) ?>'s Profile</span>
    </h1>
    


    <!--  Pet Selector -->
    <div class="flex justify-center mt-4">
        <select id="petSelector" class="p-2 border border-gray-300 rounded-lg">
            <?php
            $sql_pets = "SELECT id, petname, breed, type, age, vaccine_status, vaccine_type, info, image, qr_code 
                         FROM pet 
                         WHERE email = ? 
                         ORDER BY id ASC";
            $stmt_pets = $conn->prepare($sql_pets);
            $stmt_pets->bind_param("s", $user_email);
            $stmt_pets->execute();
            $result_pets = $stmt_pets->get_result();

            while ($p = $result_pets->fetch_assoc()): ?>
                <option value="<?= htmlspecialchars(json_encode($p)) ?>" <?= $p['id'] == $pet['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($p['petname']) ?>
                </option>
            <?php endwhile; ?>
        </select>
    </div>

    <!--  Pet Profile Section -->
    <div class="flex flex-col items-center mt-6">
        <img id="petImage" src="../uploads/<?= htmlspecialchars($pet['image']) ?>" alt="Pet Image" 
             class="w-48 h-48 object-cover rounded-full shadow-lg border-4 border-blue-400">

        <div class="mt-4 text-gray-700 text-center space-y-2">
            <p><strong class="text-gray-900">Type:</strong> <span id="petType"><?= htmlspecialchars($pet['type']) ?></span></p>
            <p><strong class="text-gray-900">Breed:</strong> <span id="petBreed"><?= htmlspecialchars($pet['breed']) ?></span></p>
            <p><strong class="text-gray-900">Age:</strong> <span id="petAge"><?= htmlspecialchars($pet['age']) ?></span> years</p>
            <p><strong class="text-gray-900">Vaccine Status:</strong> <span id="petVaccineStatus"><?= htmlspecialchars($pet['vaccine_status']) ?></span></p>
            <p><strong class="text-gray-900">Vaccine Type:</strong> <span id="petVaccineType"><?= htmlspecialchars($pet['vaccine_type']) ?></span></p>
            <p><strong class="text-gray-900">Pet Information:</strong> <span id="petInfo"><?= nl2br(htmlspecialchars($pet['info'])) ?></span></p>
        </div>

        <!-- ✅ QR Code Section -->
        <div class="mt-6 p-4 bg-gray-100 rounded-lg shadow-md w-full text-center">
            <h2 class="text-lg font-semibold text-gray-900">QR Code</h2>
            <img id="qrCodeImage" src="../qrcodes/<?= htmlspecialchars($pet['qr_code']) ?>" 
                 alt="Pet QR Code" class="w-32 h-32 mt-3 mx-auto border-2 border-gray-300 shadow">

            <a id="downloadQRCode" href="../qrcodes/<?= htmlspecialchars($pet['qr_code']) ?>" download="<?= htmlspecialchars($pet['petname']) ?>_qrcode.png"
               class="mt-4 inline-block px-5 py-2 bg-blue-500 text-white rounded-lg shadow-md hover:bg-blue-600 transition">
                📥 Download QR Code
            </a>

            <!-- 📌 Public Profile Link -->
        </div>
    </div>
<?php else: ?>
    <h1 class="text-3xl font-bold text-gray-900 text-center">No Pets Found</h1>
<?php endif; ?>

<!-- ✅ JavaScript for Switching Pet Profile -->
<script>
document.getElementById("petSelector").addEventListener("change", function() {
    let petData = JSON.parse(this.value);

    // Update pet profile details dynamically
    document.getElementById("petNameDisplay").textContent = petData.petname + "'s Profile";
    document.getElementById("petImage").src = "../uploads/" + petData.image;
    document.getElementById("petType").textContent = petData.type;
    document.getElementById("petBreed").textContent = petData.breed;
    document.getElementById("petAge").textContent = petData.age + " years";
    document.getElementById("petVaccineStatus").textContent = petData.vaccine_status;
    document.getElementById("petVaccineType").textContent = petData.vaccine_type;
    document.getElementById("petInfo").textContent = petData.info;

    // Update QR Code and Download Link
    document.getElementById("qrCodeImage").src = "../qrcodes/" + petData.qr_code;
    document.getElementById("downloadQRCode").href = "../qrcodes/" + petData.qr_code;
    document.getElementById("downloadQRCode").download = petData.petname + "_qrcode.png";
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