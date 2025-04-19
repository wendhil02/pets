<?php
include '../internet/connect_ka.php';
include 'design/top.php';
include 'design/mid.php';

$sql = "
    SELECT 
        ar.id AS request_id,
        ar.email AS adopter_email,
        ar.contact,
        ar.status AS request_status,
        ar.schedule_date,
        ar.created_at AS request_created,
        ar.pickup_time,
        p.petname,
        p.type,
        p.breed,
        p.image,
        p.status AS pet_status
    FROM adoption_requests ar
    JOIN pet p ON ar.pet_id = p.id
    WHERE ar.status IN ('pending', 'own', 'rejected')
    ORDER BY ar.created_at DESC
";
$result = $conn->query($sql);
?>



<body class="flex bg-gray-100">
    <div id="mainContent" class="main-content flex-1 transition-all">
        <!-- Navbar -->
        <nav class="bg-[#0077b6] shadow-md mt-3 mr-2 ml-2 p-2 flex items-center justify-between rounded-lg max-w-auto mx-auto">
            <button id="toggleSidebar" class="text-white text-lg px-2 py-1 hover:bg-blue-100 rounded-md border border-transparent">
                ☰
            </button>
            <span class="font-bold text-white text-sm md:text-base lg:text-lg">Welcome, Admin</span>
        </nav>

        <div class="p-4 bg-white mt-2 mr-2 ml-2 rounded-lg shadow-lg">
            <h2 class="text-xl font-bold mb-4">Adoption History</h2>
            <table class="w-full text-sm text-left text-gray-700 border-collapse">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="p-2">Pet</th>
                        <th class="p-2">Type</th>
                        <th class="p-2">Breed</th>
                        <th class="p-2">Status</th>
                        <th class="p-2">Adopter Email</th>
                        <th class="p-2">Contact</th>
                        <th class="p-2">Schedule</th>
                        <th class="p-2">Pickup Time</th>
                        <th class="p-2">Date Requested</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr class="border-b">
                            <td class="p-2 flex items-center gap-2">
                                <img src="../uploads/<?= $row['image'] ?>" alt="pet" class="w-10 h-10 object-cover rounded">
                                <?= htmlspecialchars($row['petname']) ?>
                            </td>
                            <td class="p-2"><?= htmlspecialchars($row['type']) ?></td>
                            <td class="p-2"><?= htmlspecialchars($row['breed']) ?></td>
                            <td class="p-2 font-semibold capitalize 
                    <?= $row['request_status'] === 'approved' ? 'text-green-600' : ($row['request_status'] === 'rejected' ? 'text-red-600' : 'text-yellow-600') ?>">
                                <?= $row['request_status'] ?>
                            </td>
                            <td class="p-2"><?= htmlspecialchars($row['adopter_email']) ?></td>
                            <td class="p-2"><?= htmlspecialchars($row['contact']) ?></td>
                            <td class="p-2"><?= htmlspecialchars($row['schedule_date']) ?></td>
                            <td class="p-2"><?= htmlspecialchars($row['pickup_time']) ?></td>
                            <td class="p-2"><?= date("M d, Y", strtotime($row['request_created'])) ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
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

        closeSidebarMobile?.addEventListener("click", function() {
            sidebar.classList.remove("open");
        });
    </script>

</body>

</html>