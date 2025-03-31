<?php
session_start();
include 'design/top.php';
include 'design/mid.php';
include '../internet/connect_ka.php';

?>

<body class="flex bg-gray-100">
    <!-- Main Content -->
    <div id="mainContent" class="main-content flex-1 transition-all">
        <!-- Navbar -->
        <nav class="bg-[#0077b6] shadow-md mt-3 mr-2 ml-2 p-2 flex items-center justify-between rounded-lg max-w-auto mx-auto">
            <!-- ☰ Button (For PC and Mobile) -->
            <button id="toggleSidebar" class="text-white text-lg px-2 py-1 hover:bg-blue-100 rounded-md border border-transparent">
                ☰
            </button>
            
        </nav>

        <!-- Dashboard Content -->
        <div class="p-6 bg-white">
    <h2 class="text-lg font-semibold mb-4">Report a Missing Pet</h2>
    <p class="text-gray-700 mb-4">
        Please provide detailed information about your missing pet to assist in the search.
    </p>
    <form action="upload_missing_pet.php" method="post" enctype="multipart/form-data" class="space-y-4">
        <div>
            <label for="pet_name" class="block text-sm font-medium text-gray-700">Pet's Name</label>
            <input type="text" id="pet_name" name="pet_name" required
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-opacity-50">
        </div>
        <div>
            <label for="pet_description" class="block text-sm font-medium text-gray-700">Description</label>
            <textarea id="pet_description" name="pet_description" rows="3" required
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-opacity-50"></textarea>
        </div>
        <div>
            <label for="last_seen" class="block text-sm font-medium text-gray-700">Last Seen Location</label>
            <input type="text" id="last_seen" name="last_seen" required
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-opacity-50">
        </div>
    
        <div>
            <label for="pet_video" class="block text-sm font-medium text-gray-700">Upload a Video of Your Pet</label>
            <input type="file" id="pet_video" name="pet_video" accept="video/*" required
                class="mt-1 block w-full text-sm text-gray-900 border-gray-300 rounded-md cursor-pointer focus:outline-none">
            <p class="mt-1 text-sm text-gray-500">Accepted formats: MP4, AVI, MOV. Max size: 50MB.</p>
        </div>
        <div>
            <button type="submit"
                class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring focus:ring-blue-300">
                Submit Report
            </button>
        </div>
    </form>
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

