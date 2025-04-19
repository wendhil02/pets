<?php
session_start();
include 'design/top.php';
include 'design/mid.php';
// Include database connection
include '../internet/connect_ka.php';

// ✅ Ensure user is logged in
if (!isset($_SESSION['email'])) {
    // If not logged in, redirect to login page
    header("Location: ../index.php"); // or the file where login form is
    exit();
}

// You can also check if the session_key exists to make sure the session is valid
if (!isset($_SESSION['session_key'])) {
    // If session_key is missing, log the user out
    header("Location: ../logout.php"); // or the logout handler
    exit();
}

$first_name = $_SESSION['first_name'];
$middle_name = $_SESSION['middle_name'];
$last_name = $_SESSION['last_name'];
$session_key = $_SESSION['session_key'];
// Assuming the email of the logged-in user is stored in the session
$user_email = $_SESSION['email'] ?? null;

// Check if the user is logged in
if ($user_email) {
    // Fetch pet data for the logged-in user (pet name, email, image, vaccine card)
    $pet_result = mysqli_query($conn, "SELECT pet.id, pet.petname, pet.image, pet.vaccine_card, registerlanding.email 
                                       FROM pet 
                                       JOIN registerlanding ON registerlanding.email = '$user_email' 
                                       WHERE pet.email = registerlanding.email");

    // Check if the query returned any results
    if (!$pet_result) {
        die("Error fetching pet data: " . mysqli_error($conn));
    }
} else {
    // If no user is logged in, redirect to login page
    header("Location: index.php");
    exit;
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update VaccineCard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>

<body class="flex bg-gray-100">
<div id="mainContent" class="main-content flex-1 transition-all">
        <!-- Navbar -->
        <nav class="bg-[#0077b6] shadow-md mt-3 mr-2 ml-2 p-2 flex items-center justify-between rounded-lg max-w-auto mx-auto">
            <!-- ☰ Button (For PC and Mobile) -->
            <button id="toggleSidebar" class="text-white text-lg px-2 py-1 hover:bg-blue-100 rounded-md border border-transparent">
                ☰
            </button>
            <span class="font-bold text-white text-sm md:text-base lg:text-lg">Welcome, Wendhil Himarangan</span>
        </nav>
    <div class="max-w-lg mx-auto mt-10 p-6 bg-white rounded-lg shadow-md">
        <div class="flex flex-col items-center space-y-2 text-center">
            <img src="logo/logo.png" alt="LGU Logo" class="w-14 h-14 rounded-full border-2 border-yellow-500 shadow-sm">
            <span class="text-sm font-semibold text-gray-700 uppercase">
                <i class="fa-solid fa-shield-dog text-yellow-500"></i> Pet Vaccine Update
            </span>
        </div>

        <!-- Notification Message -->
        <div id="notification" class="hidden p-4 mt-4 rounded-lg text-center">
            <p id="notificationMessage" class="text-red-900"></p>
        </div>

        <!-- Pet Selection -->
        <label class="block">
    <span class="text-gray-700 font-medium">Select Pet:</span>
    <select name="pet_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring focus:border-blue-500" onchange="showPetInfo(this.value)">
        <option value="" disabled selected hidden>Choose Pet</option>
        <?php while ($row = mysqli_fetch_assoc($pet_result)) { ?>
            <option value="<?php echo $row['id']; ?>" data-name="<?php echo $row['petname']; ?>" data-email="<?php echo $row['email']; ?>" data-image="<?php echo $row['image']; ?>" data-vaccine-card="<?php echo $row['vaccine_card']; ?>">
                <?php echo htmlspecialchars($row['petname']); ?>
            </option>
        <?php } ?>
    </select>
</label>


        <!-- Pet Image, Name, and Email -->
        <div id="petInfo" class="hidden bg-gray-100 p-3 rounded-lg text-center mt-4" style="display: none;">
            <img id="petImage" src="" class="w-32 h-32 mx-auto rounded-lg shadow-md" alt="Pet Image">
            <p id="petName" class="text-sm font-semibold text-gray-700 mt-2"></p>
            <p id="petEmail" class="text-sm font-semibold text-gray-700 mt-2"></p>
        </div>

        <!-- Display Vaccine Card -->
        <div id="vaccineCardDisplay" class="hidden mt-6 text-center" style="display: none;">
            <h3 class="text-lg font-semibold text-gray-700">Vaccine Card</h3>
            <img id="vaccineCardImage" src="" alt="Vaccine Card" class="w-64 h-64 mx-auto rounded-lg shadow-md">
        </div>

        <!-- Vaccine Card Upload -->
        <div id="vaccineCardUpload" class="mt-6" style="display: none;">
            <h3 class="text-lg font-semibold text-gray-700">Upload Vaccine Card</h3>
            <form id="vaccineForm" action="" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="pet_id" id="selectedPetId">
                <label class="block text-gray-700">Select Vaccine Card Image:</label>
                <input type="file" name="vaccine_card" accept="image/*" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring focus:border-blue-500" required>
                <div id="fileErrorMessage" class="text-red-600 text-sm mt-2 hidden">Please select a vaccine card image before updating.</div>
                <button type="button" id="updateButton" class="mt-4 bg-blue-500 text-white px-4 py-2 rounded-lg">Update</button>
            </form>
        </div>

    </div>

    <!-- Modal for Confirmation -->
    <div id="confirmationModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex justify-center items-center hidden">
        <div class="bg-white p-6 rounded-lg w-96">
            <h3 class="text-lg font-semibold text-gray-700 text-center">Are you sure you want to update the vaccine card?</h3>
            <div class="flex justify-between mt-4">
                <button id="confirmButton" class="bg-blue-500 text-white px-4 py-2 rounded-lg">Yes</button>
                <button id="cancelButton" class="bg-gray-500 text-white px-4 py-2 rounded-lg">No</button>
            </div>
        </div>
    </div>
</div>

    <script>
        // Show pet details (name, email, and image)
        function showPetInfo(petId) {
            let select = document.querySelector("select[name='pet_id']");
            let selectedOption = select.options[select.selectedIndex];

            let name = selectedOption.getAttribute("data-name");
            let email = selectedOption.getAttribute("data-email");
            let image = selectedOption.getAttribute("data-image");
            let vaccineCard = selectedOption.getAttribute("data-vaccine-card");

            if (petId) {
                // Show the pet info
                document.getElementById("petInfo").style.display = "block";
                document.getElementById("petImage").src = "../uploads/" + image;
                document.getElementById("petName").innerHTML = `<strong>Pet Name:</strong> ${name}`;
                document.getElementById("petEmail").innerHTML = `<strong>Email:</strong> ${email}`;
                document.getElementById("selectedPetId").value = petId; // Set the selected pet ID in the hidden input

                console.log("Vaccine Card Image Path: " + "../uploads/vaccine_cards/" + vaccineCard);

                // Display vaccine card if available
                if (vaccineCard) {
                    document.getElementById("vaccineCardDisplay").style.display = "block"; // Show the vaccine card
                    document.getElementById("vaccineCardImage").src = "../uploads/vaccine_cards/" + vaccineCard + "?t=" + new Date().getTime(); // Adding timestamp to avoid cache
                } else {
                    document.getElementById("vaccineCardDisplay").style.display = "none"; // Hide the vaccine card if not available
                }

                document.getElementById("vaccineCardUpload").style.display = "block"; // Show the vaccine card upload form
            } else {
                document.getElementById("petInfo").style.display = "none"; // Hide pet info if no pet is selected
                document.getElementById("vaccineCardDisplay").style.display = "none"; // Hide vaccine card
                document.getElementById("vaccineCardUpload").style.display = "none"; // Hide vaccine card upload
            }
        }

        // Show confirmation modal when update button is clicked
        document.getElementById('updateButton').addEventListener('click', function() {
            // Get the file input element
            let vaccineCardInput = document.querySelector("input[name='vaccine_card']");
            let errorMessage = document.getElementById("fileErrorMessage");

            // Check if a file is selected
            if (vaccineCardInput.files.length === 0) {
                // Display error message next to the file input
                errorMessage.style.display = "block"; // Show error message
                return; // Prevent showing the modal if no file is selected
            } else {
                // Hide error message if a file is selected
                errorMessage.style.display = "none";
            }

            // Show the confirmation modal if a file is selected
            document.getElementById("confirmationModal").classList.remove("hidden");
        });

        // Confirm update action (Submit the form)
        document.getElementById('confirmButton').addEventListener('click', function() {
            // Submit the form using AJAX
            let formData = new FormData(document.getElementById("vaccineForm"));

            // Perform AJAX request
            fetch("updatevac.php", {
                    method: "POST",
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    // Handle the response
                    showNotification(data.message, data.status);

                    // If the update was successful, reset the form
                    if (data.status === 'success') {
                        resetForm(); // Reset the pet selection and form
                    }

                    // Close the modal after confirmation
                    document.getElementById("confirmationModal").classList.add("hidden");
                })
                .catch(error => {
                    showNotification('An error occurred while uploading the vaccine card.', 'error');
                });
        });

        // Function to reset the form to its default state
        function resetForm() {
            // Reset the pet selection dropdown
            let select = document.querySelector("select[name='pet_id']");
            select.value = ""; // Set to the "Choose Pet" option

            // Hide all the sections
            document.getElementById("petInfo").style.display = "none";
            document.getElementById("vaccineCardDisplay").style.display = "none";
            document.getElementById("vaccineCardUpload").style.display = "none";

            // Reset the vaccine card input
            document.querySelector("input[name='vaccine_card']").value = "";

            // Clear any error messages
            let errorMessage = document.getElementById("fileErrorMessage");
            errorMessage.style.display = "none";
        }

        // Cancel update action (Close the modal without submitting)
        document.getElementById('cancelButton').addEventListener('click', function() {
            // Close the modal if "No" is clicked
            document.getElementById("confirmationModal").classList.add("hidden");
        });

        // Function to display notification
        function showNotification(message, type) {
            let notification = document.getElementById('notification');
            let notificationMessage = document.getElementById('notificationMessage');

            // Set notification message and style based on type
            notificationMessage.textContent = message;
            if (type === 'success') {
                notification.classList.remove('bg-red-500');
                notification.classList.add('bg-green-300');
            } else {
                notification.classList.remove('bg-green-300');
                notification.classList.add('bg-red-500');
            }

            // Show the notification
            notification.classList.remove('hidden');

            // Hide notification after 3 seconds
            setTimeout(function() {
                notification.classList.add('hidden');
            }, 3000);
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