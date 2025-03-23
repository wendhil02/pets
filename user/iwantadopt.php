<?php
session_start();
include 'design/top.php';
include 'design/mid.php';
include '../internet/connect_ka.php';

if (!isset($_SESSION['email'])) {
    header("Location: auth.php"); // Redirect to login page
    exit();
}

$email = $_SESSION['email'];

if (isset($_SESSION['notif'])) {
    $notif = $_SESSION['notif'];
    echo '<div id="notification" class="fixed top-4 right-4 p-3 rounded-md text-white ';
    echo $notif['type'] == 'success' ? 'bg-green-500' : 'bg-red-500';
    echo '">';
    echo $notif['message'];
    echo '</div>';
    unset($_SESSION['notif']); // Remove after displaying
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
                    Welcome, <?= htmlspecialchars($email) ?>
                </span>
            </div>
        </nav>
    
        <div class="flex justify-center items-center bg-gray-100 px-4">
    <div class="p-8 bg-white shadow-xl rounded-xl w-full max-w-md mt-6">
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-500 to-blue-700 text-white text-center p-4 rounded-lg shadow">
            <h2 class="text-xl font-bold">🐾 Adopt a Pet</h2>
            <p class="text-sm opacity-90">Select an approved pet and fill out the form to proceed.</p>
        </div>

        <!-- Form -->
        <form action="submit_adoption.php" method="POST" class="mt-6">
            <!-- Pet Selection -->
            <label for="pet_id" class="block text-sm font-medium text-gray-700 mb-1">Select a Pet:</label>
            <select id="pet_id" name="pet_id" class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-400 outline-none" required onchange="updatePetDetails(this)">
                <option value="">-- Select a Pet --</option>
                <?php
              
                $email = $_SESSION['email'];

                // Fetch pets that do NOT belong to the logged-in user
                $sql = "SELECT id, petname, image, breed, age, vaccine_status, vaccine_type 
                        FROM pet 
                        WHERE status = 'approved' AND email != ?";

                $stmt = $conn->prepare($sql);
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $result = $stmt->get_result();

                while ($row = $result->fetch_assoc()):
                ?>
                    <option
                        value="<?= htmlspecialchars($row['id']); ?>"
                        data-image="<?= !empty($row['image']) ? "../uploads/" . htmlspecialchars($row['image']) : 'default_pet.png'; ?>"
                        data-breed="<?= htmlspecialchars($row['breed']); ?>"
                        data-age="<?= htmlspecialchars($row['age']); ?>"
                        data-vaccine-status="<?= htmlspecialchars($row['vaccine_status']); ?>"
                        data-vaccine-type="<?= htmlspecialchars($row['vaccine_type']); ?>">
                        <?= htmlspecialchars($row['petname']); ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <!-- Pet Details -->
            <div id="petDetailsContainer" class="mt-6 hidden text-center border rounded-lg p-4 bg-gray-50">
                <img id="petImage" src="default_pet.png" alt="Selected Pet" class="w-32 h-32 object-cover rounded-lg border mx-auto shadow-lg">
                <div class="mt-3">
                    <p class="text-sm text-gray-700"><strong>Breed:</strong> <span id="petBreed"></span></p>
                    <p class="text-sm text-gray-700"><strong>Age:</strong> <span id="petAge"></span></p>
                    <p class="text-sm text-gray-700"><strong>Vaccine Status:</strong> <span id="petVaccineStatus"></span></p>
                    <p class="text-sm text-gray-700"><strong>Vaccine Type:</strong> <span id="petVaccineType"></span></p>
                </div>
            </div>

            <!-- Email Input -->
            <label for="email" class="block mt-6 text-sm font-medium text-gray-700">Your Email:</label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($email) ?>" class="w-full p-3 border rounded-lg bg-gray-100 text-gray-600 cursor-not-allowed" readonly>

            <!-- Contact Input -->
            <label for="contact" class="block mt-4 text-sm font-medium text-gray-700">Additional Contact:</label>
            <input type="text" id="contact" name="contact" class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-400 outline-none" placeholder="Enter your contact number" required>

            <!-- Submit Button -->
            <button type="submit" class="mt-6 w-full bg-blue-500 text-white p-3 rounded-lg hover:bg-blue-600 transition duration-300 shadow-md">Submit Request</button>
        </form>
    </div>
</div>

        <script>
 function updatePetDetails(select) {
                let detailsContainer = document.getElementById('petDetailsContainer');
                let imageElement = document.getElementById('petImage');
                let breedElement = document.getElementById('petBreed');
                let ageElement = document.getElementById('petAge');
                let vaccineStatusElement = document.getElementById('petVaccineStatus');
                let vaccineTypeElement = document.getElementById('petVaccineType');

                let selectedOption = select.options[select.selectedIndex];

                if (selectedOption.value === "") {
                    detailsContainer.classList.add('hidden');
                    imageElement.src = "default_pet.png"; // Reset to default
                    breedElement.textContent = "";
                    ageElement.textContent = "";
                    vaccineStatusElement.textContent = "";
                    vaccineTypeElement.textContent = "";
                    return;
                }

                let imageUrl = selectedOption.getAttribute('data-image') || "default_pet.png";
                let breed = selectedOption.getAttribute('data-breed') || "N/A";
                let age = selectedOption.getAttribute('data-age') || "N/A";
                let vaccineStatus = selectedOption.getAttribute('data-vaccine-status') || "N/A";
                let vaccineType = selectedOption.getAttribute('data-vaccine-type') || "N/A";

                detailsContainer.classList.remove('hidden');
                imageElement.src = imageUrl;
                breedElement.textContent = breed;
                ageElement.textContent = age;
                vaccineStatusElement.textContent = vaccineStatus;
                vaccineTypeElement.textContent = vaccineType;
            }

            // Auto-hide notifications after 3 seconds
            setTimeout(() => {
                let notif = document.getElementById("notification");
                if (notif) {
                    notif.style.opacity = "0";
                    setTimeout(() => notif.remove(), 500);
                }
            }, 3000);
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
</html>