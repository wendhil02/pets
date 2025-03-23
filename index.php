<?php 
session_start();
include 'design/top.php';
include 'design/mid.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'internet/connect_ka.php';

// ✅ Check if email & session_token are provided
if (!isset($_GET['email']) || !isset($_GET['session_token'])) {
    header("Location: https://smartbarangayconnect.com");
    exit();
}

$email = $_GET['email'];
$session_token = $_GET['session_token'];

// ✅ Fetch registerlanding data from Main Domain API
$api_url = "https://smartbarangayconnect.com/api_get_registerlanding.php";
$response = file_get_contents($api_url);
$data = json_decode($response, true);

if (!$data || !is_array($data)) {
    die("❌ Failed to fetch data from Main Domain.");
}

// ✅ Remove only the existing record of the logged-in user instead of truncating all data
$stmt = $conn->prepare("DELETE FROM registerlanding WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->close();

// ✅ Insert only the current user's data
foreach ($data as $row) {
    if ($row['email'] === $email) { // ✅ Filter only the email of the current user
        $stmt = $conn->prepare("INSERT INTO registerlanding 
            (id, email, first_name, last_name, session_token, birth_date, sex, mobile, working, occupation, house, street, barangay, city) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        if (!$stmt) {
            die("❌ Query Preparation Failed: " . $conn->error);
        }

        $stmt->bind_param("isssssssssssss", 
            $row['id'], $row['email'], $row['first_name'], $row['last_name'], $row['session_token'],
            $row['birth_date'], $row['sex'], $row['mobile'], $row['working'], $row['occupation'],
            $row['house'], $row['street'], $row['barangay'], $row['city']
        );

        $stmt->execute();
        $stmt->close();
        break; // ✅ Stop looping after inserting the current user's data
    }
}

// ✅ Verify session token in subdomain database
$sql = "SELECT id, email, first_name, last_name, birth_date, sex, mobile, working, occupation, house, street, barangay, city 
        FROM registerlanding WHERE email = ? AND session_token = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("❌ Query Preparation Failed: " . $conn->error);
}

$stmt->bind_param("ss", $email, $session_token);
if (!$stmt->execute()) {
    die("❌ Query Execution Failed: " . $stmt->error);
}

$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("❌ Invalid session token or email!");
}

$row = $result->fetch_assoc();

// ✅ Store all data in session
$_SESSION['id'] = $row['id'];
$_SESSION['email'] = $email;
$_SESSION['first_name'] = $row['first_name'];
$_SESSION['last_name'] = $row['last_name'];
$_SESSION['session_token'] = $session_token;

//  Additional session data
$_SESSION['birth_date'] = $row['birth_date'];
$_SESSION['sex'] = $row['sex'];
$_SESSION['mobile'] = $row['mobile'];
$_SESSION['working'] = $row['working'];
$_SESSION['occupation'] = $row['occupation'];
$_SESSION['house'] = $row['house'];
$_SESSION['street'] = $row['street'];
$_SESSION['barangay'] = $row['barangay'];
$_SESSION['city'] = $row['city'];

// ✅ Redirect to dashboard
header("Location: user/parehistro.php");
exit();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
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
        <div class="p-6 bg-white">
        <h2 class="text-lg font-semibold mb-4">Registered Pets</h2>

<?php if ($result->num_rows > 0): ?> 
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="border rounded-lg p-4 shadow">
                <img src="../uploads/<?php echo htmlspecialchars($row['image']); ?>" alt="Pet Image" class="w-full h-32 object-cover rounded">
                <h3 class="text-lg font-bold mt-2"><?php echo htmlspecialchars($row['petname']); ?></h3>
                <p class="text-sm text-gray-600"><?php echo htmlspecialchars($row['breed']); ?> - <?php echo htmlspecialchars($row['type']); ?></p>
                <p class="text-sm">Age: <?php echo htmlspecialchars($row['age']); ?> years</p>
                <p class="text-sm">Vaccine: <?php echo htmlspecialchars($row['vaccine_status']); ?></p>
            </div>
        <?php endwhile; ?>
    </div>
<?php else: ?>
    <p class="text-center text-gray-500">No registered pets yet.</p>
<?php endif; ?>

        </div>



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
