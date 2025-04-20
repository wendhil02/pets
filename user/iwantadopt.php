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

        <div class="flex justify-center items-center bg-gray-100 px-4">
            <div class="p-8 bg-white shadow-xl rounded-xl w-full max-w-md mt-6">
                <!-- Header -->
                <div class="bg-gradient-to-r from-blue-500 to-blue-700 text-white text-center p-4 rounded-lg shadow">
                    <h2 class="text-xl font-bold">🐾 Adopt a Pet</h2>
                    <p class="text-sm opacity-90">Select an approved pet and fill out the form to proceed.</p>
                </div>

                <!-- Form -->
                <form action="submit_adoption.php" method="POST" class="mt-6" id="adoptionForm">
                    <!-- Pet Selection -->
                    <label for="pet_id" class="block text-sm font-medium text-gray-700 mb-1">Select a Pet:</label>
                    <select id="pet_id" name="pet_id" class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-400 outline-none" required onchange="updatePetDetails(this)">
                        <option value="">-- Select a Pet --</option>
                        <?php
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
                    <p id="petError" class="text-red-500 text-sm mt-1 hidden"></p>

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
                    <button type="button" onclick="showModal()" class="mt-6 w-full bg-blue-500 text-white p-3 rounded-lg hover:bg-blue-600 transition duration-300 shadow-md">Submit Request</button>
                </form>
            </div>

            <!-- Popup Modal -->
            <div id="confirmationModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden">
                <div class="bg-white p-6 rounded-lg shadow-lg w-96">
                    <h2 class="text-lg font-semibold text-gray-800">Confirm Adoption Request</h2>
                    <p class="text-sm text-gray-600 mt-2">Are you sure you want to submit this request?</p>
                    <div class="mt-4 flex justify-end space-x-2">
                        <button type="button" onclick="hideModal()" class="px-4 py-2 bg-gray-300 rounded-lg hover:bg-gray-400">No</button>
                        <button type="button" onclick="submitForm()" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">Yes</button>
                    </div>
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
                        imageElement.src = "default_pet.png";
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

                function showModal() {
                    document.getElementById("confirmationModal").classList.remove("hidden");
                }

                function hideModal() {
                    document.getElementById("confirmationModal").classList.add("hidden");
                }

                function submitForm() {
                    const petSelect = document.getElementById('pet_id');
                    const errorMessage = document.getElementById('petError');

                    if (!petSelect.value) {
                        errorMessage.textContent = "Please select a pet before submitting.";
                        errorMessage.classList.remove('hidden');
                        hideModal();
                        return;
                    } else {
                        errorMessage.classList.add('hidden');
                        document.getElementById("adoptionForm").submit();
                    }
                }

                // Close modal on outside click
                document.getElementById("confirmationModal").addEventListener("click", function(event) {
                    if (event.target === this) {
                        hideModal();
                    }
                });

                // Close modal on ESC key
                document.addEventListener("keydown", function(event) {
                    if (event.key === "Escape") {
                        hideModal();
                    }
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