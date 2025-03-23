<?php
session_start();
include 'design/mid.php';
include 'design/top.php';
include '../internet/connect_ka.php';

if (!isset($_SESSION['email'])) {
    $_SESSION['notification'] = "❌ Please log in first.";
    header("Location: auth.php"); // Redirect to login page
    exit();
}
// ✅ Kunin ang email mula sa session
$user_email = $_SESSION['email'];

?>


<body class="flex bg-gray-100">

    <div id="mainContent" class="main-content flex-1 transition-all ">
        <nav class="bg-[#0077b6] shadow-md mt-3 mr-2 ml-2 p-2 flex items-center justify-between rounded-lg max-w-auto mx-auto">
            <!-- ☰ Button (For PC and Mobile) -->
            <button id="toggleSidebar" class="text-white text-lg px-2 py-1 hover:bg-blue-100 rounded-md border border-transparent">
                ☰
            </button>

            <div class="flex items-center gap-4">
                <!-- 🟢 Real-time Time Display -->


                <!-- Welcome Message -->
                <span class="font-bold text-white text-sm md:text-base lg:text-lg">
                    Welcome, <?= htmlspecialchars($user_email) ?>
                </span>
            </div>
        </nav>
        <div class="max-w-2xl mx-auto bg-white p-6 mt-4 rounded-lg shadow-lg">
            <h2 class="text-lg font-semibold mb-4">Your Pending Pets Schedule Set</h2>
            <!-- ✅ Alert message -->
             <!-- ✅ Notification Popup -->
<div id="notification" class="fixed top-5 right-5 bg-gray-800 text-white px-4 py-2 rounded-md shadow-md hidden transition-opacity duration-300">
    <span id="notifMessage"></span>
</div>

<p id="scheduleAlert" class="text-center mt-2"></p>

            <div class="flex justify-center mt-6">
                <div class="w-full max-w-2xl">
                    <table class="w-full border-collapse border border-gray-300 text-center">
                        <thead>
                            <tr class="bg-gray-100">
                            <th class="border p-2">Set Schedule</th>
                                <th class="border p-2">Pet Name</th>
                                <th class="border p-2">Type</th>
                                <th class="border p-2">Schedule</th>
                                <th class="border p-2">email</th>
                            </tr>
                        </thead>
                        <tbody id="pendingPetsList" class="text-center">
                            <!-- Dito maglo-load ang pets gamit ang JS -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- ✅ Modal for Scheduling -->
    <div id="scheduleModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden">
        <div class="bg-white p-6 rounded-lg shadow-lg w-80">
            <h3 class="text-lg font-bold mb-2">Schedule for <span id="modalPetName"></span></h3>
            <input type="hidden" id="modalPetId">
            <label class="block text-sm font-medium text-gray-700">Select Date & Time:</label>
            <input type="datetime-local" id="scheduleDate" class="border p-2 w-full rounded mt-1">
            <div class="flex justify-end gap-2 mt-4">
                <button id="cancelSchedule" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">Cancel</button>
                <button id="saveSchedule" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">Save</button>
            </div>
        </div>
    </div>

    <script>
document.addEventListener("DOMContentLoaded", function() {
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
                    petsList.innerHTML = "<tr><td colspan='5' class='text-center p-4 text-gray-500'>No pending pets.</td></tr>";
                    return;
                }

                data.forEach(pet => {
                    const isScheduled = pet.has_schedule;

                    const row = document.createElement("tr");
                    row.innerHTML = `
                        <td class="border p-2">
                            <button class="schedule-btn ${isScheduled ? 'bg-green-500 opacity-50 cursor-not-allowed' : 'bg-blue-500 hover:bg-blue-600'} text-white px-3 py-1 rounded"
                                data-id="${pet.id}" data-name="${pet.petname}" 
                                ${isScheduled ? "disabled" : ""}>
                                ${isScheduled ? "Scheduled" : "Set Schedule"}
                            </button>
                        </td>
                        <td class="border p-2">${pet.petname}</td>
                        <td class="border p-2">${pet.type}</td>
                        <td class="border p-2">${pet.schedule_date}</td>
                        <td class="border p-2">${pet.email}</td>
                    `;
                    petsList.appendChild(row);
                });

                document.querySelectorAll(".schedule-btn:not([disabled])").forEach(button => {
                    button.addEventListener("click", function() {
                        document.getElementById("modalPetId").value = this.dataset.id;
                        document.getElementById("modalPetName").textContent = this.dataset.name;
                        document.getElementById("scheduleModal").classList.remove("hidden");

                        document.getElementById("scheduleAlert").innerHTML = "<p class='text-red-500 font-semibold'>⚠️ Set your schedule.</p>";
                    });
                });
            })
            .catch(error => console.error("Error loading pending pets:", error));
    }

    loadPendingPets();

    document.getElementById("cancelSchedule").addEventListener("click", function() {
        document.getElementById("scheduleModal").classList.add("hidden");
        document.getElementById("scheduleAlert").innerHTML = "";
    });

    document.getElementById("saveSchedule").addEventListener("click", function() {
        const petId = document.getElementById("modalPetId").value;
        const scheduleDate = document.getElementById("scheduleDate").value;

        if (!scheduleDate) {
            showNotification("⚠️ Please select a date and time!", "error");
            return;
        }

        fetch("save_schedule.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({
                    pet_id: petId,
                    schedule_date: scheduleDate
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification("✅ Schedule saved successfully!", "success");
                    document.getElementById("scheduleModal").classList.add("hidden");
                    document.getElementById("scheduleAlert").innerHTML = "";
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
            let now = new Date();
            let timeString = now.toLocaleTimeString(); // Format: HH:MM:SS AM/PM
            document.getElementById("currentTime").textContent = timeString;
        }

        // Update time every second
        setInterval(updateTime, 1000);
        updateTime(); // Call once to display immediately
    </script>
</body>