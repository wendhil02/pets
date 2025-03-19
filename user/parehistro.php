<?php
session_start();
include 'design/top.php';
include 'design/mid.php';
include '../internet/connect_ka.php';

$notification = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    function cleanInput($input)
    {
        return !empty($input) ? htmlspecialchars(trim($input)) : null;
    }

    $petname = cleanInput($_POST['petname']);
    $age = is_numeric($_POST['age']) ? (int) $_POST['age'] : null;
    $type = cleanInput($_POST['type']);
    $breed = cleanInput($_POST['breed']);
    $info = cleanInput($_POST['info']);
    $vaccine_status = cleanInput($_POST['vaccine']);

    // ✅ Handle vaccine_type array properly
    $vaccine_type = isset($_POST['vaccineType'])
        ? (is_array($_POST['vaccineType']) ? implode(", ", $_POST['vaccineType']) : $_POST['vaccineType'])
        : null;

    // Image Upload Processing
    $unique_filename = null;
    if (!empty($_FILES['image']['name'])) {
        $image = $_FILES['image']['name'];
        $target_dir = "../uploads/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);

        $imageFileType = strtolower(pathinfo($image, PATHINFO_EXTENSION));
        $unique_filename = uniqid("pet_", true) . "." . $imageFileType;
        $target_file = $target_dir . $unique_filename;

        if (!in_array($imageFileType, ['jpg', 'jpeg', 'png']) || $_FILES["image"]["size"] > 2000000) {
            $_SESSION['notification'] = "❌ Invalid image file.";
            header("Location: parehistro.php");
            exit();
        }

        if (!move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $unique_filename = null;
        }
    }

    // Database Insert
    if ($conn) {
        $sql = "INSERT INTO registerlanding (petname, age, type, breed, info, vaccine_status, vaccine_type, image) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sissssss", $petname, $age, $type, $breed, $info, $vaccine_status, $vaccine_type, $unique_filename);

        if ($stmt->execute()) {
            $_SESSION['notification'] = "✅ Pet successfully registered!";
        } else {
            $_SESSION['notification'] = "❌ Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $_SESSION['notification'] = "❌ Database connection error.";
    }

    header("Location: parehistro.php");
    exit();
}
?>




<body class="flex bg-gray-100">
    <!-- Main Content -->
    <div id="mainContent" class="main-content flex-1 transition-all">
        <!-- Navbar -->
        <nav class="bg-[#0077b6] shadow-md mt-3 mr-2 ml-2 p-2 flex items-center justify-between rounded-lg max-w-auto mx-auto">
            <!-- ☰ Button (For PC and Mobile) -->
            <button id="toggleSidebar" class="text-white text-lg px-2 py-1 hover:bg-blue-100 rounded-md border border-transparent">
                ☰
            </button>
            <span class="font-bold text-white text-sm md:text-base lg:text-lg">Welcome, Wendhil Himarangan</span>
        </nav>

        <!-- Dashboard Content -->
        <div class="p-6 bg-white shadow-lg rounded-lg">
            <h1 class="text-2xl font-bold text-black mb-4">🐾 Pet Registration</h1>
            <form action="#" method="POST" enctype="multipart/form-data" class="bg-gray-100  p-6 rounded-lg shadow-md space-y-6 max-w-lg mx-auto">
                <!-- Error Message Container -->
                <div id="errorContainer" class="hidden p-3 mb-4 text-red-700 bg-red-100 border border-red-400 rounded"></div>

                <?php if (isset($_SESSION['notification'])): ?>
                    <div id="notification" class="fixed top-5 right-5 bg-green-600 text-white px-6 py-3 rounded-lg shadow-lg transition transform translate-x-20 opacity-0"
                        style="animation: slide-in 0.5s forwards, fade-out 0.5s 2.5s forwards;">
                        <?= $_SESSION['notification']; ?>
                    </div>
                    <style>
                        @keyframes slide-in {
                            to {
                                transform: translateX(0);
                                opacity: 1;
                            }
                        }

                        @keyframes fade-out {
                            to {
                                opacity: 0;
                            }
                        }
                    </style>
                    <?php unset($_SESSION['notification']); ?>
                <?php endif; ?>

                <h2 class="text-xl font-semibold text-black text-center">🐾 Pet Registration</h2>

                <!-- Pet Name -->
                <div>
                    <label for="name" class="block text-sm font-semibold text-black mb-1">Pet Name</label>
                    <input type="text" id="petname" name="petname" placeholder="Enter pet name"
                        class="w-full px-4 py-2 border rounded-lg shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-300">

                </div>

                <!-- Age, Type, Breed -->
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label for="age" class="block text-sm font-semibold text-black mb-1">Age</label>
                        <input type="number" id="age" name="age" placeholder="Age in years"
                            class="w-full px-4 py-2 border rounded-lg shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-300">
                    </div>
                    <div>
                        <label for="type" class="block text-sm font-semibold text-black mb-1">Type</label>
                        <select id="type" name="type" onchange="updateVaccineOptions()"
                            class="w-full px-4 py-2 border rounded-lg shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-300">
                            <option value="">-- Select Pet Type --</option>
                            <option value="dog">Dog</option>
                            <option value="cat">Cat</option>
                            <option value="rabbit">Rabbit</option>
                            <option value="bird">Bird</option>
                            <option value="reptile">Reptile</option>
                            <option value="fish">Fish</option>
                        </select>
                    </div>
                    <div>
                        <label for="breed" class="block text-sm font-semibold text-black mb-1">Breed</label>
                        <input type="text" id="breed" name="breed" placeholder="Pet breed"
                            class="w-full px-4 py-2 border rounded-lg shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-300">
                    </div>
                </div>

                <!-- Pet Image & Info -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="image" class="block text-sm font-semibold text-black mb-1">Pet Image</label>
                        <input type="file" id="image" name="image"
                            class="w-full px-3 py-2 border rounded-lg shadow-sm file:mr-2 file:py-2 file:px-4 file:border file:rounded-lg file:bg-blue-100 file:text-blue-700 file:hover:bg-blue-200">
                    </div>
                    <div>
                        <label for="info" class="block text-sm font-semibold text-black mb-1">Pet Info</label>
                        <textarea id="info" name="info" rows="3" placeholder="Enter details about the pet"
                            class="w-full px-4 py-2 border rounded-lg shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-300"></textarea>
                    </div>
                </div>

                <!-- Vaccine Status -->
                <div>
                    <label for="vaccine" class="block text-sm font-semibold text-black mb-1">Vaccine Status</label>
                    <select id="vaccine" name="vaccine" onchange="checkVaccineStatus()"
                        class="w-full px-4 py-2 border rounded-lg shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-300">
                        <option value="Not Vaccinated">Not Vaccinated</option>
                        <option value="Fully Vaccinated">Fully Vaccinated</option>
                    </select>
                </div>

                <!-- Vaccine Display -->
                <div id="vaccineDisplay" class="hidden mt-3">
                    <label class="block text-sm font-semibold text-black mb-1">Selected Vaccine Type:</label>
                    <div id="selectedVaccines" class="bg-gray-200 p-2 rounded-lg text-sm text-gray-800"></div>
                </div>

                <!-- Vaccine Modal -->
                <div id="vaccineModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden">
                    <div class="bg-white p-6 rounded-lg shadow-lg max-w-sm w-full">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4">Select Vaccine Type</h2>

                        <!-- Vaccine Type Selection -->
                        <div id="vaccineTypeContainer">
                            <label for="vaccineType" class="block text-sm font-semibold text-gray-700">Vaccine Type</label>
                            <select id="vaccineType" name="vaccineType"
                                class="w-full px-4 py-2 border rounded-lg shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-300">
                                <!-- Options will be added dynamically -->
                            </select>
                        </div>

                        <!-- Confirm Button -->
                        <div class="mt-4 flex justify-end">
                            <button id="confirmVaccineBtn"
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                Confirm
                            </button>
                        </div>
                    </div>
                </div>
                <!-- Submit Button -->
                <div>
                    <button type="submit"
                        class="w-full bg-blue-600 text-white py-2 rounded-lg text-lg font-semibold hover:bg-blue-700 transition duration-200">
                        Register Pet 🐶
                    </button>
                </div>
            </form>

        </div>

    </div>

    <script>
        document.querySelector("form").addEventListener("submit", function(e) {
            let name = document.getElementById("name").value.trim();
            let age = document.getElementById("age").value.trim();
            let type = document.getElementById("type").value;
            let breed = document.getElementById("breed").value.trim();
            let info = document.getElementById("info").value.trim();
            let vaccine = document.getElementById("vaccine").value;
            let image = document.getElementById("image").files.length;
            let errorContainer = document.getElementById("errorContainer");

            let errors = [];

            if (!name) errors.push("⚠️ Pet name is required.");
            if (!age || isNaN(age) || age <= 0) errors.push("⚠️ Age must be a positive number.");
            if (!type) errors.push("⚠️ Pet type is required.");
            if (!breed) errors.push("⚠️ Breed is required.");
            if (!info) errors.push("⚠️ Pet info is required.");
            if (image === 0) errors.push("⚠️ Pet image is required.");

            if (vaccine === "Fully Vaccinated") {
                let vaccineType = document.getElementById("vaccineType").value;
                if (!vaccineType) errors.push("⚠️ Please select a vaccine type.");
            }

            if (errors.length > 0) {
                e.preventDefault(); // Stop form submission

                // Show errors in the error container
                errorContainer.innerHTML = errors.join("<br>");
                errorContainer.classList.remove("hidden");

                // Auto-hide errors after 3 seconds
                setTimeout(() => {
                    errorContainer.classList.add("hidden");
                }, 3000);
            }
        });

        document.getElementById("confirmVaccineBtn").addEventListener("click", confirmVaccineSelection);
        let selectedVaccine = ""; // Store only one selected vaccine
        let currentPetType = ""; // Track current pet type

        document.getElementById("type").addEventListener("change", function() {
            let vaccineSelect = document.getElementById("vaccine");

            // Reset vaccine status when pet type changes
            vaccineSelect.value = "Not Vaccinated";
            document.getElementById("vaccineDisplay").classList.add("hidden");

            // Clear selected vaccine
            selectedVaccine = "";
            updateSelectedVaccineDisplay();

            // Update vaccine options based on new pet type
            updateVaccineOptions();
        });

        document.getElementById("vaccine").addEventListener("change", function() {
            let vaccineDisplay = document.getElementById("vaccineDisplay");

            if (this.value === "Fully Vaccinated") {
                // Only open modal if no vaccine has been selected yet
                if (!selectedVaccine) {
                    document.getElementById("vaccineModal").classList.remove("hidden");
                } else {
                    alert("You have already selected a vaccine. You cannot add more.");
                }
            } else {
                vaccineDisplay.classList.add("hidden");
                selectedVaccine = "";
                updateSelectedVaccineDisplay();
            }
        });

        // Function to update vaccine options
        function updateVaccineOptions() {
            let petType = document.getElementById("type").value;
            currentPetType = petType; // Save selected pet type

            let vaccineType = document.getElementById("vaccineType");
            vaccineType.innerHTML = ""; // Clear previous options

            let vaccines = {
                dog: ["anti-Rabies", "Distemper", "Parvovirus", "Leptospirosis", "Canine Influenza"],
                cat: ["anti-Rabies", "Feline Leukemia", "FVRCP", "Chlamydia", "Bordetella"],
                rabbit: ["Myxomatosis", "Rabbit Hemorrhagic Disease", "Pasteurella"],
                bird: ["Avian Influenza", "Poxvirus", "Polyomavirus"],
                reptile: ["Salmonella Prevention", "Herpesvirus", "Adenovirus"],
                fish: ["Spring Viremia", "Koi Herpesvirus", "Vibrio Vaccine"]
            };

            if (petType && vaccines[petType]) {
                vaccines[petType].forEach(vaccine => {
                    let option = document.createElement("option");
                    option.value = vaccine;
                    option.textContent = vaccine;
                    vaccineType.appendChild(option);
                });

                document.getElementById("vaccineTypeContainer").classList.remove("hidden"); // Show vaccine dropdown
            } else {
                document.getElementById("vaccineTypeContainer").classList.add("hidden"); // Hide if no vaccines available
            }
        }

        // Function to confirm vaccine selection (Fix: No auto-register)
        function confirmVaccineSelection(event) {
            event.preventDefault(); // Prevent any form submission

            let chosenVaccine = document.getElementById("vaccineType").value;

            if (chosenVaccine && !selectedVaccine) {
                selectedVaccine = chosenVaccine;
                updateSelectedVaccineDisplay();
            } else {
                alert("You can only select one vaccine.");
            }

            // Close modal
            document.getElementById("vaccineModal").classList.add("hidden");
        }

        // Function to update the displayed selected vaccine
        function updateSelectedVaccineDisplay() {
            let selectedVaccineContainer = document.getElementById("selectedVaccines");

            // Show selected vaccine or a message if none is chosen
            selectedVaccineContainer.innerHTML = selectedVaccine ?
                `<span class="bg-gray-300 p-2 rounded">${selectedVaccine}</span>` :
                "No vaccine selected";

            document.getElementById("vaccineDisplay").classList.remove("hidden");
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
    </script>

</body>

</html>