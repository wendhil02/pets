<?php
session_start();
include 'design/top.php';
include 'design/mid.php';
include '../internet/connect_ka.php';

// Siguraduhin na may naka-login
if (!isset($_SESSION['email'])) {
    echo "<p class='text-red-500'>Unauthorized access.</p>";
    exit();
}

$user_email = $_SESSION['email'];

// Kunin ang pets na hindi pa vaccinated
$sql = "SELECT id, petname, type, breed, image FROM pet WHERE email = ? AND (vaccine_card IS NULL OR vaccine_card = '')";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_email);
$stmt->execute();
$result = $stmt->get_result();

$pets = $result->fetch_all(MYSQLI_ASSOC);

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Not Vaccinated Pets</title>
    <script>

    </script>
</head>

<body class="flex bg-gray-100">
    <div id="mainContent" class="main-content flex-1 transition-all">
        <nav class="bg-[#0077b6] shadow-md mt-3 mr-2 ml-2 p-2 flex items-center justify-between rounded-lg max-w-auto mx-auto">
            <button id="toggleSidebar" class="text-white text-lg px-2 py-1 hover:bg-blue-100 rounded-md border border-transparent">
                ☰
            </button>

            <span class="font-bold text-white text-sm md:text-base lg:text-lg">
                Welcome, <?= htmlspecialchars($user_email) ?>
            </span>

            <!-- Notification Modal -->
            <div id="notificationModal" class="fixed top-5 right-5 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg hidden transition">
                <span id="notificationText"></span>
            </div>
        </nav>

        <div class="p-6 bg-white mt-2 mx-auto mr-2 ml-2">
        <div class="flex justify-between items-center mb-4">
    <h2 class="text-lg font-semibold">Not Vaccinated Pets</h2>
    <div class="flex space-x-2">
        <button onclick="openGuide()" class="bg-blue-500 text-white px-3 py-2 rounded-lg hover:bg-red-600 transition">
            Guide 🛈
        </button>
        <a href="update_vaccine_card.php" class="px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
            Update Vaccine Card
        </a>
    </div>
</div>


            <!-- Hidden Guide Modal -->
            <div id="guideModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex justify-center items-center">
                <div class="bg-white p-6 rounded-lg shadow-lg w-96">
                    <h2 class="text-xl font-semibold mb-2">Guide for Uploading Vaccine Cards</h2>
                    <p class="text-sm text-gray-700 mb-4">
                        1️⃣ Select the appropriate vaccine type from the dropdown.<br>
                        2️⃣ If the vaccine type is not listed, choose "Other" and enter the vaccine name.<br>
                        3️⃣ Click "Upload Vaccine Card" and select a valid file (JPG, PNG).<br>
                        4️⃣ The pet's status will update to "Fully Vaccinated" after upload.
                    </p>
                    <button onclick="closeGuide()" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition w-full">
                        Close
                    </button>
                </div>
            </div>

            <?php foreach ($pets as $pet):
                $petImage = !empty($pet['image']) ? '../uploads/' . htmlspecialchars($pet['image']) : 'images/default-pet.png';
                $vaccineCard = !empty($pet['vaccine_card']) ? '../uploads/vaccine_cards/' . htmlspecialchars($pet['vaccine_card']) : null;
                $vaccineType = !empty($pet['vaccine_type']) ? htmlspecialchars($pet['vaccine_type']) : 'No vaccine type recorded';
            ?>
                <div class="p-4 border rounded-lg shadow bg-gray-100 flex justify-between items-center mb-3">
                    <div class="flex items-center space-x-4">
                        <img src="<?php echo $petImage; ?>" alt="Pet Image" class="w-16 h-16 object-cover rounded-lg border">
                        <div>
                            <p class="font-semibold text-gray-800">
                                <?php echo htmlspecialchars($pet['petname']); ?> (<?php echo htmlspecialchars($pet['type']); ?> - <?php echo htmlspecialchars($pet['breed']); ?>)
                            </p>
                            <p class="text-sm text-gray-600">🩺 Vaccine Type: <span class="font-medium" id="vaccine-type-<?php echo $pet['id']; ?>"><?php echo $vaccineType; ?></span></p>

                            <?php if ($vaccineCard): ?>
                                <p class="text-green-600 text-sm">✔ Vaccine Card Uploaded</p>
                                <a href="<?php echo $vaccineCard; ?>" target="_blank" class="text-blue-500 text-sm underline">View</a>
                            <?php else: ?>
                                <p class="text-red-500 text-sm">⚠ No vaccine card uploaded</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Vaccine Upload & Type Selection -->
                    <div class="flex flex-col space-y-2">
                        <select id="vaccine-select-<?php echo $pet['id']; ?>" class="border p-2 rounded" onchange="toggleOtherInput(<?php echo $pet['id']; ?>)">
                            <option value="Rabies">Rabies</option>
                            <option value="Distemper">Distemper</option>
                            <option value="Parvovirus">Parvovirus</option>
                            <option value="Leptospirosis">Leptospirosis</option>
                            <option value="Other">Other</option>
                        </select>

                        <!-- Hidden input field for "Other" -->
                        <input type="text" id="other-vaccine-<?php echo $pet['id']; ?>" class="border p-2 rounded hidden" placeholder="Enter vaccine name">

                        <label class="cursor-pointer bg-blue-500 text-white py-2 px-4 rounded-lg hover:bg-blue-600 transition">
                            Upload Vaccine Card
                            <input type="file" class="hidden" onchange="uploadVaccine(<?php echo $pet['id']; ?>, this)">
                        </label>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <script>
            function toggleOtherInput(petId) {
                let vaccineSelect = document.getElementById(`vaccine-select-${petId}`);
                let otherInput = document.getElementById(`other-vaccine-${petId}`);

                if (vaccineSelect.value === "Other") {
                    otherInput.classList.remove("hidden"); // Show input
                    otherInput.focus(); // Focus on the input field
                } else {
                    otherInput.classList.add("hidden"); // Hide input
                    otherInput.value = ""; // Clear input
                }
            }

            function uploadVaccine(petId, input) {
                let file = input.files[0];
                let vaccineSelect = document.getElementById(`vaccine-select-${petId}`);
                let otherInput = document.getElementById(`other-vaccine-${petId}`);
                let vaccineType = vaccineSelect.value === "Other" ? otherInput.value.trim() : vaccineSelect.value;

                if (!file) {
                    showNotification("Please select a file.", 'error');
                    return;
                }

                if (vaccineSelect.value === "Other" && vaccineType === "") {
                    showNotification("Please specify the vaccine name.", 'error');
                    return;
                }

                let formData = new FormData();
                formData.append("pet_id", petId);
                formData.append("vaccine_card", file);
                formData.append("vaccine_type", vaccineType);

                fetch("notvaccinated_pets.php", {
                        method: "POST",
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === "success") {
                            showNotification("Vaccine card uploaded successfully!", 'success');
                            location.reload();
                        } else {
                            showNotification("Error: " + data.message, 'error');
                        }
                    })
                    .catch(error => {
                        console.error("Upload error:", error);
                        showNotification("An unexpected error occurred.", 'error');
                    });
            }



            function showNotification(message, type = 'success') {
                const notification = document.createElement('div');
                notification.className = `fixed top-5 right-5 px-4 py-2 rounded-lg shadow-lg text-white ${
        type === 'success' ? 'bg-green-500' : 'bg-red-500'
    }`;
                notification.textContent = message;

                document.body.appendChild(notification);

                setTimeout(() => {
                    notification.classList.add('opacity-0', 'transition-opacity', 'duration-500');
                    setTimeout(() => notification.remove(), 500);
                }, 3000);
            }

            function openGuide() {
                document.getElementById("guideModal").classList.remove("hidden");
            }

            function closeGuide() {
                document.getElementById("guideModal").classList.add("hidden");
            }
        </script>


        <script>
            const sidebar = document.getElementById("sidebar");
            const mainContent = document.getElementById("mainContent");
            const toggleSidebar = document.getElementById("toggleSidebar");
            const closeSidebarMobile = document.getElementById("closeSidebarMobile");

            toggleSidebar.addEventListener("click", function() {
                if (window.innerWidth <= 768) {
                    sidebar.classList.toggle("open");
                } else {
                    sidebar.classList.toggle("closed");
                    mainContent.classList.toggle("shrink");
                }
            });

            closeSidebarMobile.addEventListener("click", function() {
                sidebar.classList.remove("open");
            });
        </script>
</body>

</html>