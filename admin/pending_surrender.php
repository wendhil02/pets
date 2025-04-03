<?php

include 'design/top.php';
include 'design/mid.php';
include '../internet/connect_ka.php';

// Fetch pending surrender requests including schedule_date
$sql = "SELECT id, petname, status, email, image, schedule_date FROM pet WHERE status = 'pending'";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();
?>

<body class="flex bg-gray-100">
    <div id="mainContent" class="main-content flex-1 transition-all">
        <nav class="bg-[#0077b6] shadow-md mt-3 mr-2 ml-2 p-2 flex items-center justify-between rounded-lg max-w-auto mx-auto">
            <button id="toggleSidebar" class="text-white text-lg px-2 py-1 hover:bg-blue-100 rounded-md border border-transparent">☰</button>
            <span class="font-bold text-white text-sm md:text-base lg:text-lg">Welcome, Admin</span>
        </nav>

        <div class="p-6 bg-white rounded-lg shadow mt-5 mx-2">
            <h2 class="text-lg font-semibold mb-4">Pending Surrender Requests</h2>

            <!-- ✅ Custom Confirmation Modal -->
            <div id="confirmModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center">
                <div class="bg-white p-5 rounded-md shadow-md max-w-sm text-center">
                    <p id="confirmText" class="text-lg font-semibold mb-4"></p>
                    <div class="flex justify-center gap-4">
                        <button id="confirmYes" class="bg-green-500 text-white px-4 py-2 rounded-md">Yes</button>
                        <button id="confirmNo" class="bg-gray-500 text-white px-4 py-2 rounded-md">No</button>
                    </div>
                </div>
            </div>

            <!-- ✅ Notification Popup -->
            <div id="notification" class="fixed top-5 right-5 bg-gray-800 text-white px-4 py-2 rounded-md shadow-md hidden transition-opacity duration-300">
                <span id="notifMessage"></span>
            </div>

            <?php if ($result->num_rows > 0): ?>
                <table class="w-full border-collapse border border-blue-900">
                    <thead>
                        <tr class="bg-gray-200">
                            <th class="border p-1">Picture</th>
                            <th class="border p-2">Pet Name</th>
                            <th class="border p-2">Schedule</th>
                            <th class="border p-2">Owner Email</th>
                            <th class="border p-2">Status</th>
                            <th class="border p-2">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr id="row-<?php echo $row['id']; ?>">
                                <td class="border border-transparent p-2 text-center">
                                    <div class="flex justify-center">
                                        <img src="../uploads/<?php echo htmlspecialchars($row['image']); ?>"
                                            alt="Pet Image" class="w-16 h-16 object-cover rounded-md">
                                    </div>
                                </td>

                                <td class="border border-transparent p-2 text-center">
                                    <?php echo htmlspecialchars($row['petname']); ?>
                                </td>

                                <td class="border border-transparent p-2 text-center text-gray-500">
                                    <?php echo htmlspecialchars($row['schedule_date']); ?>
                                </td>

                                <td class="border border-transparent p-2 text-center text-gray-500">
                                    <?php echo htmlspecialchars($row['email']); ?>
                                </td>

                                <td class="border border-transparent p-2 text-center">
                                    <?php echo htmlspecialchars($row['status']); ?>
                                </td>

                                <td class="border border-transparent p-2 text-center">
                                    <div class="flex justify-center space-x-2">
                                        <button
                                            onclick="updateStatus(
        <?php echo $row['id']; ?>, 
        'approve', 
        '<?php echo addslashes(htmlspecialchars($row['email'])); ?>', 
        '<?php echo addslashes(htmlspecialchars($row['petname'])); ?>'
    )"
                                            class="bg-green-500 text-white px-3 py-1 rounded hover:bg-green-600 disabled:bg-gray-400 disabled:cursor-not-allowed"
                                            <?php echo empty($row['schedule_date']) ? 'disabled' : ''; ?>>
                                            O
                                        </button>

                                        <button onclick="updateStatus(
                                            <?php echo $row['id']; ?>, 
                                            'reject', 
                                            '<?php echo addslashes(htmlspecialchars($row['email'])); ?>', 
                                            '<?php echo addslashes(htmlspecialchars($row['petname'])); ?>'
                                        )" class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600">
                                            O
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="text-center text-gray-500">No pending surrender requests.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Rejection Modal -->
    <div id="rejectionModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden">
        <div class="bg-white p-5 rounded-lg shadow-lg w-96">
            <h2 class="text-lg font-semibold mb-3">Reject Pet Surrender</h2>
            <p class="text-sm text-gray-600 mb-2">Please provide a reason for rejecting <span id="rejectPetName" class="font-semibold"></span>.</p>
            <textarea id="rejectReason" class="w-full p-2 border rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Enter reason..."></textarea>
            <div class="flex justify-end mt-3">
                <button onclick="closeModal()" class="px-4 py-2 bg-gray-400 text-white rounded-lg mr-2">Cancel</button>
                <button id="confirmReject" class="px-4 py-2 bg-red-600 text-white rounded-lg">Reject</button>
            </div>
        </div>
    </div>


    <script>
        
        document.addEventListener("DOMContentLoaded", function () {
    let currentPetId = null;
    let currentEmail = null;
    let currentPetName = null;

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

    function showConfirmation(message, onConfirm) {
        let modal = document.getElementById("confirmModal");
        let confirmText = document.getElementById("confirmText");
        let confirmYes = document.getElementById("confirmYes");
        let confirmNo = document.getElementById("confirmNo");

        confirmText.innerText = message;
        modal.classList.remove("hidden");

        confirmYes.onclick = function () {
            modal.classList.add("hidden");
            onConfirm();
        };

        confirmNo.onclick = function () {
            modal.classList.add("hidden");
        };
    }

    function updateStatus(id, action, email, petname) {
        if (action === 'reject') {
            currentPetId = id;
            currentEmail = email;
            currentPetName = petname;
            document.getElementById("rejectPetName").textContent = petname;
            document.getElementById("rejectionModal").classList.remove("hidden");
        } else {
            sendStatusUpdate(id, action, email, petname, null);
        }
    }

    function closeModal() {
        document.getElementById("rejectionModal").classList.add("hidden");
        document.getElementById("rejectReason").value = ""; // Clear input
    }

    document.getElementById("confirmReject").addEventListener("click", function () {
        let reason = document.getElementById("rejectReason").value.trim();
        if (!reason) {
            showNotification("Please enter a rejection reason!", "error");
            return;
        }
        sendStatusUpdate(currentPetId, "reject", currentEmail, currentPetName, reason);
        closeModal();
    });

    function sendStatusUpdate(id, action, email, petname, reason) {
        let formData = new URLSearchParams();
        formData.append("id", id);
        formData.append("action", action);
        formData.append("email", email);
        formData.append("petname", petname);
        if (reason) formData.append("reason", reason);

        fetch('approve_surrender.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: formData.toString()
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification(`Pet ${action}ed successfully!`, "success");
                document.getElementById("row-" + id)?.remove();
            } else {
                showNotification("Error: " + data.message, "error");
            }
        })
        .catch(error => console.error('Error:', error));
    }

    window.updateStatus = updateStatus;
    window.closeModal = closeModal;
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
    </script>

</body>