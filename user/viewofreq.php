<?php
session_start();
include '../internet/connect_ka.php';
include 'design/top.php';
include 'design/mid.php';

if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'user') {
    header("Location: index.php");
    exit();
}

$email = $_SESSION['email'];
$sql = "SELECT ar.id, p.id AS pet_id, p.petname, ar.email AS adopter_email, ar.contact, ar.status, ar.schedule_date, ar.pickup_time, ar.email_confirmed, ar.owner_mark
        FROM adoption_requests ar
        JOIN pet p ON ar.pet_id = p.id
        WHERE p.email = ?";


$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $_SESSION['email']);
$stmt->execute();
$result = $stmt->get_result();

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
    <h2 class="text-2xl font-bold mb-6 text-gray-800">Adoption Requests to Your Pets</h2>

    <?php
    // Handle marking
    if (isset($_GET['mark_id'])) {
        $mark_id = $_GET['mark_id'];

        $mark = $conn->prepare("UPDATE adoption_requests SET owner_mark = 1 WHERE id = ?");
        $mark->bind_param("i", $mark_id);
        $mark->execute();

        echo '<div class="bg-blue-100 text-blue-800 p-2 mb-4 rounded">Successfully marked!</div>';
        
        echo "<script>window.location.href='" . $_SERVER['PHP_SELF'] . "';</script>";
        exit();
    }
    ?>

    <div class="overflow-x-auto">
        <table class="min-w-full bg-white border border-gray-200 rounded-lg">
            <thead class="bg-[#0077b6] text-white">
                <tr>
                    <th class="py-3 px-6 text-left">Pet Name</th>
                    <th class="py-3 px-6 text-left">Adopter Email</th>
                    <th class="py-3 px-6 text-left">Contact</th>
                    <th class="py-3 px-6 text-left">Status</th>
                    <th class="py-3 px-6 text-left">Marked</th> <!-- new -->
                    <th class="py-3 px-6 text-left">Action</th> <!-- new -->
                </tr>
            </thead>
            <tbody class="text-gray-700">
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr class="border-b hover:bg-gray-100">
                        <td class="py-3 px-6"><?= htmlspecialchars($row['petname']) ?></td>
                        <td class="py-3 px-6"><?= htmlspecialchars($row['adopter_email']) ?></td>
                        <td class="py-3 px-6"><?= htmlspecialchars($row['contact']) ?></td>
                        <td class="py-3 px-6">
                            <?php if($row['status'] == 'pending'): ?>
                                <span class="px-3 py-1 text-sm bg-yellow-100 text-yellow-800 rounded-full">Pending</span>
                            <?php elseif($row['status'] == 'approved'): ?>
                                <span class="px-3 py-1 text-sm bg-green-100 text-green-800 rounded-full">Approved</span>
                            <?php elseif($row['status'] == 'rejected'): ?>
                                <span class="px-3 py-1 text-sm bg-red-100 text-red-800 rounded-full">Rejected</span>
                            <?php else: ?>
                                <?= htmlspecialchars($row['status']) ?>
                            <?php endif; ?>
                        </td>
                        <td class="py-3 px-6">
                            <?php if($row['owner_mark'] == 1): ?>
                                <span class="px-3 py-1 text-sm bg-green-200 text-green-800 rounded-full">Marked</span>
                            <?php else: ?>
                                <span class="px-3 py-1 text-sm bg-gray-200 text-gray-700 rounded-full">Not Marked</span>
                            <?php endif; ?>
                        </td>
                        <td class="py-3 px-6">
                            <?php if($row['owner_mark'] == 0): ?>
                                <a href="?mark_id=<?= $row['id'] ?>" 
                                   class="bg-blue-500 hover:bg-blue-700 text-white text-sm font-bold py-2 px-4 rounded">
                                   Mark
                                </a>
                            <?php else: ?>
                                <span class="text-gray-400 text-sm">Already Marked</span>
                            <?php endif; ?>
                        </td>
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