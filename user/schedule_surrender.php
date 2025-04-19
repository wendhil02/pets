<?php
session_start();
include 'design/top.php';
include 'design/mid.php';

include '../internet/connect_ka.php';

if (!isset($_SESSION['email']) || !isset($_SESSION['session_key'])) {
    session_unset();
    session_destroy();
    header("Location: ../index.php");
    exit(); // Make sure you call exit after header() to stop further execution
}

// Now you can access the user data and session key in the protected file
$email = $_SESSION['email'];
$first_name = $_SESSION['first_name'];
$middle_name = $_SESSION['middle_name'];
$last_name = $_SESSION['last_name'];
$session_key = $_SESSION['session_key']; // session_key is available here

// ✅ Kunin ang email mula sa session
$user_email = $_SESSION['email'];

?>

<title>Schedule Set</title>

<body class="flex bg-gray-100">

    <div id="mainContent" class="main-content flex-1 transition-all ">
      
    <nav class="bg-[#0077b6] shadow-md mt-3 mr-2 ml-2 p-2 flex items-center justify-between rounded-lg max-w-auto mx-auto">
            <!-- Button -->
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
        <div class="max-w-2xl mx-auto bg-white p-6 mt-4 rounded-lg shadow-lg">

            <div class="flex items-center justify-between mt-1">
                <h2 class="text-lg font-semibold">Your Pending Pets Schedule Set</h2>

                <button id="openGuideModal" class="px-3 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
                    Guide
                </button>
            </div>

            <!-- ✅ Alert Notification -->
            <div id="notification" class="fixed top-5 right-5 bg-gray-800 text-white px-4 py-2 rounded-md shadow-md hidden transition-opacity duration-300">
                <span id="notifMessage"></span>
            </div>

            <p id="scheduleAlert" class="text-center mt-2"></p>

           
    <div class=" max-w-4xl bg-white p-6 rounded-lg shadow-md">
        <table class="w-full border-collapse text-center">
            <thead>
                <tr class="bg-gray-100 text-gray-700">
                    <th class="border-b p-3 text-sm font-medium">Select</th>
                    <th class="border-b p-3 text-sm font-medium">Pet Name</th>
                    <th class="border-b p-3 text-sm font-medium">Type</th>
                    <th class="border-b p-3 text-sm font-medium">Status Schedule</th>
                    <th class="border-b p-3 text-sm font-medium">Email</th>
                    <th class="border-b p-3 text-sm font-medium">Schedule Date & Time</th>
                   
                </tr>
            </thead>
            <tbody id="pendingPetsList">
                <?php if (isset($pets) && is_array($pets) && count($pets) > 0): ?>
                    <?php foreach ($pets as $pet): ?>
                        <tr class="hover:bg-gray-50 transition-all">
                            <td class="border-b p-3">
                                <input type="checkbox" class="selectPet rounded-md">
                            </td>
                            <td class="border-b p-3"><?php echo htmlspecialchars($pet['petname']); ?></td>
                            <td class="border-b p-3"><?php echo htmlspecialchars($pet['type']); ?></td>
                            <td class="border-b p-3">
                                <?php echo ($pet['schedule_date'] != '') ? 'Scheduled' : 'Not Scheduled'; ?>
                            </td>
                            <td class="border-b p-3"><?php echo htmlspecialchars($pet['email']); ?></td>
                            <td class="border-b p-3">
                                <?php echo ($pet['schedule_date'] != '') ? htmlspecialchars($pet['schedule_date']) : 'Not Set'; ?>
                            </td>
                            <td class="border-b p-3">
                                <?php echo ($pet['schedule_date'] != '') ? 'Scheduled' : 'Pending'; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="border p-4 text-center text-gray-500">No pending pets found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>



            <div class="flex justify-center mt-4">
                <button id="openScheduleModal" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">Set Schedule</button>
            </div>

        </div>

        <!-- ✅ Modal for Scheduling -->
        <div id="scheduleModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden">
            <div class="bg-white p-6 rounded-lg shadow-lg w-80">
                <h3 class="text-lg font-bold mb-2">Set Schedule</h3>
                <label class="block text-sm font-medium text-gray-700">Select Date & Time:</label>
                <input type="datetime-local" id="scheduleDate" class="border p-2 w-full rounded mt-1">
                <div class="flex justify-end gap-2 mt-4">
                    <button id="cancelSchedule" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">Cancel</button>
                    <button id="saveSchedule" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">Save</button>
                </div>
            </div>
        </div>

        <div id="confirmScheduleModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden">
            <div class="bg-white p-6 rounded-lg shadow-lg w-80">
                <h3 class="text-lg font-bold mb-2">Confirm Schedule</h3>
                <p class="text-gray-700">Are you sure you want to set this schedule?</p>
                <div class="flex justify-end gap-2 mt-4">
                    <button id="cancelFinalSchedule" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">No</button>
                    <button id="confirmFinalSchedule" class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600">Yes</button>
                </div>
            </div>
        </div>

        <div id="guideModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden">
            <div class="bg-white p-6 rounded-lg shadow-lg w-96">
                <h3 class="text-lg font-bold mb-4">📌 How to Set a Schedule</h3>
                <ol class="list-decimal pl-6 space-y-2 text-gray-700">
                    <li>Select one or more pets from the list.</li>
                    <li>Click the <b>"Set Schedule"</b> button.</li>
                    <li>Pick a date and time from the date picker.</li>
                    <li>Click the <b>"Save"</b> button to confirm.</li>
                    <li>Your pet's schedule status will update automatically.</li>
                </ol>
                <div class="flex justify-end mt-4">
                    <button id="closeGuideModal" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
                        Close
                    </button>
                </div>
            </div>
        </div>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

        <script>
            document.getElementById("openGuideModal").addEventListener("click", function() {
                document.getElementById("guideModal").classList.remove("hidden");
            });

            document.getElementById("closeGuideModal").addEventListener("click", function() {
                document.getElementById("guideModal").classList.add("hidden");
            });

            document.addEventListener("DOMContentLoaded", function() {
                let selectedPets = new Set();
                let scheduledPets = new Set();
                let scheduleDate = "";

                function showNotification(message, type) {
                    let notif = document.getElementById("notification");
                    let notifMessage = document.getElementById("notifMessage");

                    notifMessage.innerText = message;
                    notif.classList.remove("hidden", "bg-red-500", "bg-green-500");
                    notif.classList.add(type === "success" ? "bg-green-500" : "bg-red-500");

                    notif.style.opacity = "1";
                    setTimeout(() => {
                        notif.style.opacity = "0";
                        setTimeout(() => notif.classList.add("hidden"), 300);
                    }, 3000);
                }

                function loadPendingPets() {
                    fetch("fetch_pending_pets.php")
                        .then(response => response.json())
                        .then(data => {
                            const petsList = document.getElementById("pendingPetsList");
                            petsList.innerHTML = "";

                            if (data.length === 0) {
                                petsList.innerHTML = "<tr><td colspan='6' class='text-center p-4 text-gray-500'>No pending pets.</td></tr>";
                                return;
                            }

                            data.forEach(pet => {
                                let isScheduled = pet.schedule_date && pet.schedule_date !== "Not Scheduled";
                                let disabledAttr = isScheduled ? "disabled" : "";

                                const row = document.createElement("tr");
                                row.innerHTML = `
                        <td class="border p-2">
                            <input type="checkbox" class="select-pet" data-id="${pet.id}" ${disabledAttr}>
                        </td>
                        <td class="border p-2">${pet.petname}</td>
                        <td class="border p-2">${pet.type}</td>
                        <td class="border p-2 ${isScheduled ? 'text-green-500 font-bold' : ''}">
                            ${isScheduled ? "✔ Scheduled" : "Not Set"}
                        </td>
                        <td class="border p-2">${pet.email}</td>
                        <td class="border p-2">${isScheduled ? pet.schedule_date : "—"}</td>
                    `;
                                petsList.appendChild(row);
                            });

                            document.querySelectorAll(".select-pet").forEach(checkbox => {
                                checkbox.addEventListener("change", function() {
                                    if (this.checked) {
                                        selectedPets.add(this.dataset.id);
                                    } else {
                                        selectedPets.delete(this.dataset.id);
                                    }
                                });
                            });
                        })
                        .catch(error => console.error("Error loading pending pets:", error));
                }

                loadPendingPets();

                document.getElementById("openScheduleModal").addEventListener("click", function() {
                    if (selectedPets.size === 0) {
                        showNotification(" Please select at least one pet!", "error");
                        return;
                    }
                    document.getElementById("scheduleModal").classList.remove("hidden");
                });

                document.getElementById("cancelSchedule").addEventListener("click", function() {
                    document.getElementById("scheduleModal").classList.add("hidden");
                });

                document.getElementById("saveSchedule").addEventListener("click", function() {
                    scheduleDate = document.getElementById("scheduleDate").value;

                    if (!scheduleDate) {
                        showNotification("⚠️ Please select a date and time!", "error");
                        return;
                    }

                    document.getElementById("scheduleModal").classList.add("hidden");
                    document.getElementById("confirmScheduleModal").classList.remove("hidden");
                });

                document.getElementById("cancelFinalSchedule").addEventListener("click", function() {
                    document.getElementById("confirmScheduleModal").classList.add("hidden");
                });

                document.getElementById("confirmFinalSchedule").addEventListener("click", function() {
                    fetch("save_schedule.php", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json"
                            },
                            body: JSON.stringify({
                                pet_ids: Array.from(selectedPets),
                                schedule_date: scheduleDate
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                showNotification("✅ Schedule saved successfully!", "success");
                                document.getElementById("confirmScheduleModal").classList.add("hidden");

                                selectedPets.forEach(id => scheduledPets.add(id));
                                selectedPets.clear();

                                loadPendingPets();
                            } else {
                                showNotification("❌ Failed to save schedule.", "error");
                            }
                        })
                        .catch(error => console.error("Error saving schedule:", error));
                });
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
                const timeElement = document.getElementById("timeElement");

                // Check if the element exists
                if (timeElement) {
                    timeElement.textContent = ""; // Modify this as needed
                } else {
                    console.error("Element with id 'timeElement' not found.");
                }
            }

            // Call updateTime to ensure it's executed
            updateTime();
        </script>
</body>