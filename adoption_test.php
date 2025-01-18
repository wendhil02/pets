
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Barangay Animal Welfare</title>
  <link rel="shortcut icon" href="img/barangay.png" type="image/x-icon">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" 
      integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" 
      crossorigin="anonymous" referrerpolicy="no-referrer" />
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
body {
  font-family: 'Poppins', sans-serif;
  background-color: #f4f7fb;
}
.fixed-center {
  position: fixed;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  z-index: 50;
  width: 90%;
  max-width: 400px;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, 0.5);
  z-index: 40;
}

.transition-width {
  transition: width 0.3s;
}
.sidebar-text {
  display: none; /* Hidden by default */
}
.dropdown-content {
  display: none; /* Hidden by default */
}
/* Show text when sidebar is expanded */
.sidebar-expanded .sidebar-text {
  display: block; /* Show text when expanded */
}
/* Show dropdown when expanded */
.dropdown-open {
  display: block; /* Show dropdown content */
}
.rotate-180 {
  transform: rotate(180deg); /* Rotate arrow */
  transition: transform 0.3s; /* Smooth transition */
}
.arrow-hidden {
  display: none; /* Hide arrow */
}
.chart-container {
  position: relative;
  height: 200px; 
  width: 100%;
}

  </style>
</head>

<body class="bg-[#90e0ef] font-poppins">

<!--body layout-->
<div class="flex relative w-full">

<div class="">
    <!--Sidebar start-->

<aside id="sidebar" class=" bg-[#042752] text-white w-64 transition-width duration-300 min-h-screen flex flex-col sidebar-expanded font-poppins text-sm">
    <div class="flex justify-center ">
      <div class="flex justify-center p-4 ">
        <img width="100" height="100" src="img/barangay.png" alt="">
      </div>
      <button id="toggleBtn" class="text-white p-1 focus:outline-none md:hidden">
        <!-- Optional toggle icon can be added here -->
      </button>
   
    </div> 
    <a href="admin_dashboard.php" data-content="dashboard" class="text-center  space-x-4 p-2 ">
        <span class="sidebar-text font-poppins ">Pet AnimalWelfare Protection System</span>
      </a>
    <hr class="mx-2">
    <!-- Sidebar Links -->
    <nav class=" flex flex-col space-y-4 mt-4 p-4">
         
      <a href="admin_dashboard.php" data-content="dashboard" class="flex font-poppins items-center space-x-4 p-2 hover:bg-blue-700 rounded">
        <i class="fa-solid fa-circle-exclamation"></i>  
        <span class="sidebar-text">Dashboard</span>
      </a>
      
      <a href="admin_adoption.php" data-content="registration" class="flex font-poppins items-center space-x-4 p-2 hover:bg-blue-700 rounded">
        <i class="fa-solid fa-user"></i>
        <span class="sidebar-text">Adoption Management</span>
      </a>

      <a href="admin_report.php" data-content="registration" class="flex font-poppins items-center space-x-4 p-2 hover:bg-blue-700 rounded">
        <i class="fa-solid fa-book"></i>
        <span class="sidebar-text">Report Management</span>
      </a>
    
      <a href="admin_reg.php" data-content="registration" class="flex font-poppins items-center space-x-4 p-2 hover:bg-blue-700 rounded">
        <i class="fa-solid fa-users-gear"></i>
        <span class="sidebar-text">Registration Management</span>
      </a>

      <a href="admin_acc.php" data-content="registration" class="flex font-poppins items-center space-x-4 p-2 hover:bg-blue-700 rounded">
        <i class="fa-solid fa-user-plus"></i>
        <span class="sidebar-text">Account Management</span>
      </a>

    </nav>
</aside>


<!--sidebar end-->  
</div>

<div class="w-full">
<!--navbar start-->
<nav class="flex bg-[#042752] shadow-md p-4  m-4 item-center justify-between rounded-lg">
    <button id="sidebarToggle" class="text-white focus:outline-none">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
        </svg>
    </button>
    <div class="relative flex">
      <div class="flex items-center p-1 mr-4 gap-2 text-white">
      <i class="fa-solid fa-bell"></i>
      <span class="float-l absolute top-0 left-0 inline-flex items-center justify-center w-4 h-4 text-xs font-bold text-white bg-red-500 rounded-full">
        99+
      </span>
      </div>
      <div class="flex items-center p-1 mr-4 gap-2 text-white ">
      <i class="fa-regular fa-user"></i>
      </div>
      <div class="">
      <button id="logoutBtn" class="flex items-center gap-2 text-white hover:bg-blue-700 rounded-lg p-1 focus:outline-none transition duration-200 ease-in-out" onclick="showModal()">
            <i class="fas fa-sign-out-alt"></i>
            <span class="hidden md:block"></span> <!-- Show text only on larger screens -->
        </button>
      </div>
    </div>
</nav>
<!--navbar end-->

</div>

<script>
//start:sidebar

 //Sidebar functions
 const toggleBtn = document.getElementById('toggleBtn');
 const sidebarToggle = document.getElementById('sidebarToggle');
 const sidebar = document.getElementById('sidebar');

 function toggleSidebar() {
   sidebar.classList.toggle('w-20');
   sidebar.classList.toggle('w-64');
   sidebar.classList.toggle('sidebar-expanded');
 
 }
  // Event Listeners
  toggleBtn.addEventListener('click', toggleSidebar);
  sidebarToggle.addEventListener('click', toggleSidebar);

 

  function toggleDropdown(dropdownContentId, arrowIconId) {
    // Get all dropdown contents and arrow icons
    const allDropdownContents = document.querySelectorAll('.dropdown-content');
    const allArrowIcons = document.querySelectorAll('.sidebar-text svg');
  
    // Loop through all dropdowns and close them
    allDropdownContents.forEach((content) => {
      if (content.id !== dropdownContentId) {
        content.classList.add('hidden'); // Close other dropdowns
      }
    });
  
    allArrowIcons.forEach((icon) => {
      if (icon.id !== arrowIconId) {
        icon.classList.remove('rotate-90'); // Reset other arrow rotations
      }
    });
  
    // Toggle the current dropdown and arrow
    const dropdownContent = document.getElementById(dropdownContentId);
    const arrowIcon = document.getElementById(arrowIconId);
  
    dropdownContent.classList.toggle('hidden'); // Toggle visibility
    arrowIcon.classList.toggle('rotate-90'); // Rotate arrow
  }
  //end:sidebar


      // Function to show the modal
      function showModal() {
        document.getElementById('logoutModal').style.display = 'flex';
    }

    // Function to hide the modal
    function closeModal() {
        document.getElementById('logoutModal').style.display = 'none';
    }

    // Function to handle the logout logic
    function confirmLogout() {
        window.location.href = '../logout.php';
    }

    // Optional: Close modal when clicking outside of it
    window.onclick = function(event) {
        const modal = document.getElementById('logoutModal');
        if (event.target === modal) {
            closeModal();
        }
    };

    // Attach the closeModal function to the Cancel button
    document.getElementById('cancelBtn').onclick = closeModal;  
  

</script>
</body>
</html>
