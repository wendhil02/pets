<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" 
      integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" 
      crossorigin="anonymous" referrerpolicy="no-referrer" />


<div id="sidebar" class="bg-[#042752] text-white w-64 transition-width duration-300 min-h-screen flex flex-col sidebar-expanded sticky top-0 ">
    <div class="flex justify-center ">
      <div class="flex justify-center p-4 ">
        <img width="100" height="100"  src="img/barangay.png" alt="">
      </div>
    </div> 
    <a href="user_dashboard.php" data-content="dashboard" class="text-center font-poppins font-bold space-x-4 p-2 ">
        <span class="sidebar-text">Pet AnimalWelfare Protection System</span>
      </a>
    <hr class="mx-4">
    <!-- Sidebar Links -->
    <nav class="flex-1 flex flex-col space-y-4 mt-4 p-4">
 
      <a href="user_dashboard.php" data-content="dashboard" class="flex items-center space-x-4 p-2 hover:bg-blue-700 rounded">
      
      <i class="fa-solid fa-book"></i>
        <span class="sidebar-text font-poppins font-bold">Dashboard</span>
      </a>
      
      <div class="relative">
        <button onclick="toggleDropdown('RegDropdownContent', 'RegArrowIcon')" class="flex items-center justify-between p-2 w-full text-left hover:bg-blue-700 rounded transition duration-300">
          <div class="flex items-center space-x-4">
          <i class="fa-solid fa-users-gear"></i>
            <span class="sidebar-text font-poppins font-bold">Registration</span>
          </div>
          <svg id="RegArrowIcon" class="sidebar-text h-5 w-5 transform transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
          </svg>
        </button>
        <div id="RegDropdownContent" class="ml-10 text-gray-200 hidden">
       <a href="user_register.php" data-content="PetReg" class="sidebar-text block p-2 hover:bg-blue-700 rounded content-link font-poppins font-bold">Pet Registration</a></span>
       <a href="user_mypets.php" data-content="PetRegList" class="sidebar-text block p-2 hover:bg-blue-700 rounded content-link font-poppins font-bold">My Pets</a></span>

        </div>
      </div>
      
      <div class="relative">
        <button onclick="toggleDropdown('adoptionDropdownContent', 'adoptionArrowIcon')" class="flex items-center justify-between p-2 w-full text-left hover:bg-blue-700 rounded transition duration-300">
          <div class="flex items-center space-x-4">
          <i class="fa-solid fa-user"></i>
            <span class="sidebar-text font-poppins font-bold">Adoption</span>
          </div>
          <svg id="adoptionArrowIcon" class="sidebar-text h-5 w-5 transform transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
          </svg>
        </button>
        <div id="adoptionDropdownContent" class="ml-10 text-gray-200 hidden">

        <a href="user_petview.php" data-content="adoptionView" class="sidebar-text block p-2 hover:bg-blue-700 rounded content-link font-poppins font-bold">Pet Adoption</a></span>

       <a href="user_adoption.php" data-content="adoptionForm" class="sidebar-text block p-2 hover:bg-blue-700 rounded content-link font-poppins font-bold">Open For Adoption</a></span>
       
       <a href="user_adoption.php" data-content="adoptionForm" class="sidebar-text block p-2 hover:bg-blue-700 rounded content-link font-poppins font-bold">List Of Adoption</a></span>

        </div>
      </div>
      

      <div class="relative">
        <button onclick="toggleDropdown('reportDropdownContent', 'reportArrowIcon')" class="flex items-center justify-between p-2 w-full text-left hover:bg-blue-700 rounded transition duration-300">
          <div class="flex items-center space-x-4">
          <i class="fa-solid fa-circle-exclamation"></i>
            <span class="sidebar-text font-poppins font-bold">Report</span>
          </div>
          <svg id="reportArrowIcon" class="sidebar-text h-5 w-5 transform transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
          </svg>
        </button>
        <div id="reportDropdownContent" class="ml-10 text-gray-200 hidden">
       <a href="user_reportMissing.php" data-content="missingreport" class="sidebar-text block p-2 hover:bg-blue-700 rounded content-link font-poppins font-bold">Report Animal Missing</a></span>
       <a href="user_reportCruelty.php" data-content="reportcruelty" class="sidebar-text block p-2 hover:bg-blue-700 rounded content-link font-poppins font-bold">Report Animal Cruelty</a></span>
        </div>
      </div>
        
    <!--  <a href="#" data-content="registration" class="flex items-center space-x-4 p-2 hover:bg-blue-700 rounded content-link">
      <i class="fa-solid fa-circle-exclamation"></i>
        <span class="sidebar-text">History</span>
      </a>
-->
      
    </nav>
  </div>
