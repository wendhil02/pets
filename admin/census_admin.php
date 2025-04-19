<?php
session_start();
include 'design/top.php';
include 'design/mid.php';
include '../internet/connect_ka.php';

if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}
$email = $_SESSION['email'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Census Data</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

</head>

<body class="flex bg-gray-50">

    <div id="mainContent" class="main-content flex-1 transition-all">
        <!-- Navbar -->
  <nav class="bg-[#0077b6] shadow-md mt-3 mr-2 ml-2 p-2 flex items-center justify-between rounded-lg max-w-auto mx-auto">
    <!--  Button -->
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

        <!-- Dashboard Content -->
        <div class="p-6 bg-white">
    <h2 class="text-2xl font-semibold text-black text-center mb-6">
        <span class="text-2xl font-semibold text-red-800 uppercase text-center">
            <i class="fa-solid fa-shield-dog text-yellow-500"></i> Census Information
        </span>
    </h2>

    <!-- Search Bar -->
    <form method="GET" action="" class="mb-6">
        <div class="flex justify-center items-center">
            <input type="text" name="search" placeholder="Search by Name, Address..."
                class="px-4 py-2 border rounded-lg w-1/3 text-sm" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-lg ml-2">Search</button>
        </div>
    </form>

    <!-- Census Data Table -->
    <?php
    // Connect to the database
    include '../internet/connect_ka.php';

    // Capture the search query if any
    $search = isset($_GET['search']) ? $_GET['search'] : '';

    // SQL query to fetch data from census_data table with a filter based on the search query
    $sql = "SELECT id, firstname, middlename, lastname, age, gender, occupation, housenumber, streetname, barangay, city, marked 
    FROM census_data 
    WHERE firstname LIKE '%$search%' OR middlename LIKE '%$search%' OR lastname LIKE '%$search%' 
    OR housenumber LIKE '%$search%' OR streetname LIKE '%$search%' OR barangay LIKE '%$search%' 
    OR city LIKE '%$search%'";

    $result = $conn->query($sql);

    // Check if there are records
    if ($result->num_rows > 0) {
        echo "<div class='overflow-x-auto'> <!-- Scrollable Container -->
        <table class='min-w-full table-auto shadow-lg rounded-lg'>
            <thead>
                <tr class='bg-[#0077b6] text-white'>
                    <th class='px-6 py-4 text-sm font-semibold'>First Name</th>
                    <th class='px-6 py-4 text-sm font-semibold'>Middle Name</th>
                    <th class='px-6 py-4 text-sm font-semibold'>Last Name</th>
                    <th class='px-6 py-4 text-sm font-semibold'>House Number</th>
                    <th class='px-6 py-4 text-sm font-semibold'>Street Name</th>
                    <th class='px-6 py-4 text-sm font-semibold'>Barangay</th>
                    <th class='px-6 py-4 text-sm font-semibold'>City</th>
                    <th class='px-6 py-4 text-sm font-semibold'>Action</th> <!-- Action Column -->
                </tr>
            </thead>
            <tbody class='bg-white'>";

        // Display records in table rows
        while ($row = $result->fetch_assoc()) {
            $id = $row['id']; // Store the record's ID for later use (e.g., for marking)
            $marked = $row['marked']; // Check if the record is marked

            // Set button color based on the marked status
            $button_class = ($marked == 1) ? 'bg-green-500' : 'bg-yellow-500'; // Green if marked, Yellow if not

            // Form to mark an entry (this form will trigger a "mark" action)
            echo "<tr class='border-t'>
            <td class='px-6 py-4 text-sm text-gray-700'>{$row['firstname']}</td>
            <td class='px-6 py-4 text-sm text-gray-700'>{$row['middlename']}</td>
            <td class='px-6 py-4 text-sm text-gray-700'>{$row['lastname']}</td>
            <td class='px-6 py-4 text-sm text-gray-700'>{$row['housenumber']}</td>
            <td class='px-6 py-4 text-sm text-gray-700'>{$row['streetname']}</td>
            <td class='px-6 py-4 text-sm text-gray-700'>{$row['barangay']}</td>
            <td class='px-6 py-4 text-sm text-gray-700'>{$row['city']}</td>
            <td class='px-6 py-4 text-sm'>
                <form method='POST' action='mark_action.php'>
                    <input type='hidden' name='id' value='$id'>
                    <button type='submit' class='$button_class text-white px-4 py-2 rounded-lg'>
                        " . (($marked == 1) ? 'Marked' : 'Mark') . "
                    </button>
                </form>
            </td>
        </tr>";
        }

        echo "</tbody>
    </table>
</div>";
    } else {
        echo "<p class='text-gray-700 text-center'>No records found.</p>";
    }

    // Close the connection
    $conn->close();
    ?>
</div>


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
        function updateTime() {
            let now = new Date();
            let timeString = now.toLocaleTimeString(); // Format: HH:MM:SS AM/PM
            document.getElementById("currentTime").textContent = timeString;
        }

        // Update time every second
        setInterval(updateTime, 1000);
        updateTime(); // Call once to display immediately
        
        // JavaScript to update current time and date
function updateTimeAndDate() {
    // Get current date and time
    const currentTime = new Date();
    
    // Format current time (e.g., 12:34 PM)
    const formattedTime = currentTime.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    
    // Format current date (e.g., April 4, 2025)
    const formattedDate = currentTime.toLocaleDateString([], { year: 'numeric', month: 'long', day: 'numeric' });

    // Update the current time and date in the DOM
    document.getElementById('currentTime').textContent = formattedTime;
    document.getElementById('currentDate').textContent = formattedDate;
}

// Update time and date every minute
setInterval(updateTimeAndDate, 60000);

// Initial call to update the time and date immediately
updateTimeAndDate();
    </script>


</body>

</html>