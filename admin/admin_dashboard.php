<?php
ob_start();
session_start();
include 'design/top.php';
include 'design/mid.php';
include '../internet/connect_ka.php';

if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['announcement'])) {
    $announcement = trim($_POST['announcement']);
    $imageName = null;

    if (!empty($announcement)) {
        // Handle Image Upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = '../uploads/announcements/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $imageName = uniqid() . '_' . basename($_FILES['image']['name']);
            $uploadPath = $uploadDir . $imageName;

            move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath);
        }

        // Insert into database
        $stmt = $conn->prepare("INSERT INTO announcements (message, image) VALUES (?, ?)");
        $stmt->bind_param("ss", $announcement, $imageName);
        $stmt->execute();
        $stmt->close();

        // Clear POST data to avoid resubmission
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}

// Handle Featured Image Upload
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['upload_featured'])) {
    if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] == UPLOAD_ERR_OK) {
        $uploadDir = '../uploads/featured/'; // Create a folder named 'featured'
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $featuredImageName = uniqid() . '_' . basename($_FILES['featured_image']['name']);
        $uploadPath = $uploadDir . $featuredImageName;

        if (move_uploaded_file($_FILES['featured_image']['tmp_name'], $uploadPath)) {
            // Save to database
            $stmt = $conn->prepare("INSERT INTO featured_images (image_path) VALUES (?)");
            $stmt->bind_param("s", $featuredImageName);
            $stmt->execute();
            $stmt->close();

            // Redirect to avoid resubmit
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        }
    }
}


// Fetch latest announcements
$announcements = $conn->query("SELECT * FROM announcements ORDER BY created_at DESC LIMIT 5");
ob_end_flush();
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

        <!-- Announcement Section -->
        <div class="p-6 bg-white">
            <h2 class="text-xl font-bold mb-4 text-gray-700">📢 Announcements</h2>

            <!-- Form to post announcement -->
            <form method="POST" enctype="multipart/form-data" class="mb-6">
                <textarea name="announcement" rows="3" class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" placeholder="Write an announcement..."></textarea>

                <input type="file" name="image" accept="image/*" class="mt-2 block w-full text-sm text-gray-600">

                <button type="submit" class="mt-2 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Post Announcement</button>

                <label class="block mb-2 text-sm font-medium text-gray-700">Upload Featured Image</label>
                <input type="file" name="featured_image" accept="image/*" required class="block w-full text-sm text-gray-600 mb-4">

                <button type="submit" name="upload_featured" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                    Upload Featured Image
                </button>
            </form>


            <!-- Display latest announcements -->
            <div class="space-y-4">
                <?php while ($row = $announcements->fetch_assoc()): ?>
                    <div class="p-4 bg-gray-100 rounded shadow-sm border-l-4 border-blue-500">
                        <p class="text-gray-800"><?= nl2br(htmlspecialchars($row['message'])) ?></p>
                        <small class="text-gray-500 block mt-1"><?= date('F j, Y g:i A', strtotime($row['created_at'])) ?></small>
                    </div>
                <?php endwhile; ?>
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