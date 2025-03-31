<?php
session_start();
include 'design/top.php';
include 'design/mid.php';
include '../internet/connect_ka.php';
include '../phpqrcode-master/qrlib.php'; // ✅ Include QR Code Library

// ✅ Ensure user is logged in
if (!isset($_SESSION['email'])) {
    $_SESSION['notification'] = "❌ Please log in first.";
    header("Location: auth.php");
    exit();
}

$user_email = $_SESSION['email']; // Get user email from session

// ✅ Reset AUTO_INCREMENT If No Pets Exist
$result = $conn->query("SELECT COUNT(*) AS total FROM pet");
$row = $result->fetch_assoc();
if ($row['total'] == 0) {
    $conn->query("ALTER TABLE pet AUTO_INCREMENT = 1");
}

// ✅ Function to Clean User Input
function cleanInput($input)
{
    return isset($input) && trim($input) !== '' ? htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8') : null;
}

// ✅ Handle Form Submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // ✅ Collect and Sanitize Input Data
    $petname = cleanInput($_POST['petname']);
    $age = isset($_POST['age']) && is_numeric($_POST['age']) ? (int) $_POST['age'] : null;
    $type = cleanInput($_POST['type']);
    $breed = cleanInput($_POST['breed']);
    $info = cleanInput($_POST['info']);
    $vaccine_status = cleanInput($_POST['vaccine']);

    // ✅ Handle Vaccine Type (Support for both array and string)
    $vaccine_type = isset($_POST['vaccineType']) ? (is_array($_POST['vaccineType']) ? implode(", ", $_POST['vaccineType']) : cleanInput($_POST['vaccineType'])) : null;

    // ✅ Function to Upload Files
    function uploadFile($file, $targetDir, $prefix)
    {
        if (!empty($file['name'])) {
            if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);

            $fileType = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $uniqueFilename = uniqid($prefix, true) . "." . $fileType;
            $targetFile = $targetDir . $uniqueFilename;

            // ✅ Validate File (Only JPG, JPEG, PNG & Max 2MB)
            if (!in_array($fileType, ['jpg', 'jpeg', 'png']) || $file["size"] > 2000000) {
                return ['error' => "❌ Invalid file. Upload JPG, JPEG, or PNG (max 2MB)."];
            }

            // ✅ Move Uploaded File
            if (!move_uploaded_file($file["tmp_name"], $targetFile)) {
                return ['error' => "❌ File upload failed."];
            }

            return ['filename' => $uniqueFilename];
        }
        return null;
    }

    // ✅ Upload Pet Image
    $petImageUpload = uploadFile($_FILES['image'], "../uploads/", "pet_");
    if (isset($petImageUpload['error'])) {
        $_SESSION['notification'] = $petImageUpload['error'];
        header("Location: parehistro.php");
        exit();
    }
    $petImage = $petImageUpload['filename'] ?? null;

    // ✅ Upload Vaccine Card Image
    $vaccineCardUpload = uploadFile($_FILES['vaccine_card'], "../uploads/vaccine_cards/", "vaccine_");
    if (isset($vaccineCardUpload['error'])) {
        $_SESSION['notification'] = $vaccineCardUpload['error'];
        header("Location: parehistro.php");
        exit();
    }
    $vaccineCard = $vaccineCardUpload['filename'] ?? null;

    // ✅ Generate Unique Pet ID (Used as QR Code ID)
    $qr_id = uniqid("qr_");

    // ✅ Generate QR Code (Using qr_id)
    $qrData = "http://localhost/userside/user/petprofile.php?id=" . $qr_id;
    $qr_dir = "../qrcodes/";
    if (!is_dir($qr_dir)) mkdir($qr_dir, 0777, true);

    $qr_filename = "qr_" . $qr_id . ".png";
    QRcode::png($qrData, $qr_dir . $qr_filename, QR_ECLEVEL_L, 6);

    // ✅ Insert Pet Data Into Database (Including qr_id)
    $sql = "INSERT INTO pet (email, petname, age, type, breed, info, vaccine_status, vaccine_type, image, qr_code, qr_id, vaccine_card) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssisssssssss", $user_email, $petname, $age, $type, $breed, $info, $vaccine_status, $vaccine_type, $petImage, $qr_filename, $qr_id, $vaccineCard);

    if ($stmt->execute()) {
        $_SESSION['notification'] = "✅ Pet successfully registered with a QR code!";
    } else {
        $_SESSION['notification'] = "❌ Error: " . $stmt->error;
    }
    $stmt->close();

    // ✅ Redirect to Registration Page After Submission
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

            <div class="flex items-center gap-4">
                <!-- 🟢 Real-time Time Display -->
                <span id="currentTime" class="text-white font-semibold text-sm md:text-base lg:text-lg"></span>

                <!-- Welcome Message -->
                <span class="font-bold text-white text-sm md:text-base lg:text-lg">
                    Welcome, <?= htmlspecialchars($user_email) ?>
                </span>
            </div>
        </nav>

        <!-- Dashboard Content -->
        <div class="p-4 bg-white mt-1 mr-2 ml-2 rounded-lg shadow-lg">
            <h1 class="text-2xl font-bold text-black mb-4">🐾 Pet Registration</h1>

            <div class="mt-4">
                <button id="openGuide" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                    🛈 Guide
                </button>
            </div>

            <!-- Guide Modal -->
            <div id="guideModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden">
                <div class="bg-white p-6 rounded-lg shadow-lg max-w-md w-full relative">
                    <!-- Close Button -->
                    <button id="closeGuideModal" class="absolute top-2 right-2 text-gray-500 hover:text-gray-800 text-xl">
                        &times;
                    </button>

                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Pet Registration Guide</h2>
                    <p class="text-sm text-gray-700">
                        🐾 **Step 1:** Fill in the pet’s name, age, type, and breed. <br>
                        📷 **Step 2:** Upload a clear photo of your pet. <br>
                        💉 **Step 3:** If your pet's vaccine type is not listed, kindly upload the vaccine card instead. This will serve as the basis for vaccination status verification. <br>
                        📝 **Step 4:** Add important pet information. <br>
                        ✅ **Step 5:** Click "Register Pet" to complete the process.
                    </p>
                </div>
            </div>

            <form action="#" method="POST" enctype="multipart/form-data" class="bg-gray-100  p-6 rounded-lg shadow-md space-y-6 max-w-lg mx-auto">
                <!-- Error Message Container -->
                <div id="errorContainer" class="hidden p-3 mb-4 text-red-700 bg-red-100 border border-red-400 rounded"></div>
                <div id="vaccineNotification" class="hidden fixed top-5 right-5 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg">
                    Vaccine selection updated successfully!
                </div>

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
                        <label for="vaccine_card" class="block text-sm font-semibold text-black mb-1">Upload Vaccine Card</label>
                        <input type="file" id="vaccine_card" name="vaccine_card" disabled
                            class="w-full px-3 py-2 border rounded-lg shadow-sm bg-gray-300 text-gray-500 file:mr-2 file:py-2 file:px-4 file:border file:rounded-lg file:bg-blue-100 file:text-blue-700 file:hover:bg-blue-200">
                        <p id="vaccineWarning" class="text-red-500 text-sm hidden">You can only upload a vaccine card if the pet is fully vaccinated.</p>
                    </div>

                    <div>
                        <label for="info" class="block text-sm font-semibold text-black mb-1">Pet Information</label>
                        <textarea id="info" name="info" rows="3" placeholder="Enter details about the pet"
                            class="w-full px-4 py-2 border rounded-lg shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-300"></textarea>
                    </div>
                </div>


                <!-- Vaccine Status -->
                <div>
                    <label for="vaccine" class="block text-sm font-semibold text-black mb-1">Vaccine Status</label>
                    <select id="vaccine" name="vaccine" onchange="checkVaccineStatus()"
                        class="w-full px-4 py-2 border rounded-lg shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-300">
                        <option value="Not Vaccinated" >Not Vaccinated</option>
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
                    <div class="bg-white p-6 rounded-lg shadow-lg max-w-sm w-full relative">
                        <!-- Close Button -->
                        <button id="closeVaccineModal" class="absolute top-2 right-2 text-gray-500 hover:text-gray-800 text-xl">
                            &times;
                        </button>

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
        document.getElementById("openGuide").addEventListener("click", function() {
            document.getElementById("guideModal").classList.remove("hidden");
        });

        document.getElementById("closeGuideModal").addEventListener("click", function() {
            document.getElementById("guideModal").classList.add("hidden");
        });



        function checkVaccineStatus() {
            var vaccineStatus = document.getElementById("vaccine").value;
            var vaccineCardInput = document.getElementById("vaccine_card");
            var vaccineWarning = document.getElementById("vaccineWarning");

            if (vaccineStatus === "Fully Vaccinated") {
                vaccineCardInput.disabled = false;
                vaccineCardInput.classList.remove("bg-gray-300", "text-gray-500");
                vaccineWarning.classList.add("hidden"); // Hide warning
            } else {
                vaccineCardInput.disabled = true;
                vaccineCardInput.classList.add("bg-gray-300", "text-gray-500");
                vaccineWarning.classList.remove("hidden"); // Show warning
            }
        }

        // Disable by default on page load
        window.onload = function() {
            checkVaccineStatus();
        };


        document.addEventListener("DOMContentLoaded", function() {
            const inputs = document.querySelectorAll("input[type='text'], textarea");

            inputs.forEach(input => {
                input.addEventListener("input", function() {
                    this.value = this.value.replace(/@/g, ""); // Remove '@' if entered
                });
            });
        });


        document.querySelector("form").addEventListener("submit", function(e) {
            let petname = document.getElementById("petname").value.trim();
            let age = document.getElementById("age").value.trim();
            let type = document.getElementById("type").value;
            let breed = document.getElementById("breed").value.trim();
            let info = document.getElementById("info").value.trim();
            let vaccine = document.getElementById("vaccine").value;
            let image = document.getElementById("image").files.length;
            let vaccineCard = document.getElementById("vaccine_card").files.length;
            let errorContainer = document.getElementById("errorContainer");

            let errors = [];

            if (!petname) errors.push("⚠️ Pet name is required.");
            if (!age || isNaN(age) || age <= 0) errors.push("⚠️ Age must be a positive number.");
            if (!type) errors.push("⚠️ Pet type is required.");
            if (!breed) errors.push("⚠️ Breed is required.");
            if (!info) errors.push("⚠️ Pet info is required.");
            if (image === 0) errors.push("⚠️ Pet image is required.");

            if (vaccine === "Fully Vaccinated") {
                let vaccineType = document.getElementById("vaccineType").value;
                if (!vaccineType) errors.push("⚠️ Please select a vaccine type.");
                if (vaccineCard === 0) errors.push("⚠️ Vaccine card upload is required for fully vaccinated pets.");
            }

            if (errors.length > 0) {
                e.preventDefault(); // ❗❗ Stop form submission ❗❗

                // Show errors in the error container
                errorContainer.innerHTML = errors.join("<br>");
                errorContainer.classList.remove("hidden");

                // Auto-hide errors after 3 seconds
                setTimeout(() => {
                    errorContainer.classList.add("hidden");
                }, 3000);
            }
        });


        document.getElementById("closeVaccineModal").addEventListener("click", function() {
            document.getElementById("vaccineModal").classList.add("hidden");
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

            // Default "None" option
            let noneOption = document.createElement("option");
            noneOption.value = "None";
            noneOption.textContent = "None";
            vaccineType.appendChild(noneOption);

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

        // Function to confirm vaccine selection
        function confirmVaccineSelection(event) {
            event.preventDefault(); // Prevent any form submission

            let chosenVaccine = document.getElementById("vaccineType").value;
            let notification = document.getElementById("vaccineNotification");

            if (chosenVaccine === "None") {
                selectedVaccine = ""; // Clear selection if "None" is chosen
            } else if (chosenVaccine && !selectedVaccine) {
                selectedVaccine = chosenVaccine;
            } else {
                // Show notification for duplicate selection
                showNotification("You can only select one vaccine.", "bg-red-500");
                return;
            }

            updateSelectedVaccineDisplay();
            document.getElementById("vaccineModal").classList.add("hidden"); // Close modal
            showNotification("Vaccine selection updated successfully!", "bg-green-500");
        }

        // Function to show notification
        function showNotification(message, bgColor) {
            let notification = document.getElementById("vaccineNotification");
            notification.textContent = message;
            notification.classList.remove("hidden");
            notification.classList.remove("bg-green-500", "bg-red-500"); // Remove existing colors
            notification.classList.add(bgColor); // Add new color

            setTimeout(() => {
                notification.classList.add("hidden"); // Hide after 3 seconds
            }, 3000);
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