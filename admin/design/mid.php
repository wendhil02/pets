<?php
include '../internet/connect_ka.php';
?>
<div id="sidebar" class="sidebar bg-[#0A2A52] text-white fixed h-screen transition-all md:translate-x-0 w-64 overflow-y-auto scrollbar-hide">
      <!-- ☰ Button (Kapag Mobile, nasa Sidebar kapag Open) -->
    
      <div class="p-1 flex justify-end md:hidden">
        <button id="closeSidebarMobile" class="text-white text-lg px-2 py-1 hover:bg-blue-700 rounded">x</button>
    </div>
<!-- Logo Section -->
    <div class="p-2 flex flex-col items-center space-y-3 px-4">
        <img src="logo/logo.png" alt="LGU Logo" class="w-12 h-12 rounded-full mb-2">
        <span class="text-sm font-bold text-center">LGU - Pet Animal Welfare Protection System</span>
    </div>

 

    <ul class="space-y-3 px-4">
    <?php
$current_page = basename($_SERVER['PHP_SELF']); // Get current file name
?>

<ul class="space-y-3 px-4">
    <!-- Dashboard -->
    <li class="mt-5 text-xs font-bold text-gray-300 uppercase">Dashboard</li>
    <li>
        <a href="#" class="block text-sm py-3 px-3 rounded transition-all duration-300 
            <?= $current_page == 'dashboard.php' ? 'bg-blue-800 text-white animate-slideRight' : 'hover:bg-blue-800 hover:text-white hover:translate-x-2' ?>">
            🏠 Dashboard
        </a>
    </li>

    <!-- REGISTRATION MANAGEMENT -->
    <li class="mt-5 text-xs font-bold text-gray-300 uppercase">Registration Management</li>
    <li>
        <a href="regispet.php" class="block text-sm py-3 px-3 rounded transition-all duration-300 
            <?= $current_page == 'parehistro.php' ? 'bg-blue-800 text-white animate-slideRight' : 'hover:bg-blue-800 hover:text-white hover:translate-x-2' ?>">
            🐾 Pet Register
        </a>
    </li>
   

    <!-- ADOPTION MANAGEMENT -->
    <li class="mt-5 text-xs font-bold text-gray-300 uppercase">Adoption Management</li>
    <li>
        <a href="manage_adoptions.php" class="block text-sm py-3 px-3 rounded transition-all duration-300 
            <?= $current_page == 'iwantadopt.php' ? 'bg-blue-800 text-white animate-slideRight' : 'hover:bg-blue-800 hover:text-white hover:translate-x-2' ?>">
            🔍  Adoption
        </a>
    </li>
    <li class="mt-5 text-xs font-bold text-gray-300 uppercase">Adopted Management</li>
    <li>
        <a href="pending_surrender.php" class="block text-sm py-3 px-3 rounded transition-all duration-300 
            <?= $current_page == 'missingpet.php' ? 'bg-blue-800 text-white animate-slideRight' : 'hover:bg-blue-800 hover:text-white hover:translate-x-2' ?>">
            🔍 Adopted
        </a>
    </li>
    

    <!-- REPORT MANAGEMENT -->
    <li class="mt-5 text-xs font-bold text-gray-300 uppercase">Report Services</li>
   
    <li>
        <a href="admin_reports.php" class="block text-sm py-3 px-3 rounded transition-all duration-300 
            <?= $current_page == 'admin_reports.php' ? 'bg-blue-800 text-white animate-slideRight' : 'hover:bg-blue-800 hover:text-white hover:translate-x-2' ?>">
            ⚠️ Report Animal Cruelty
        </a>
    </li>
      
    <li>
        <a href="archive_reports.php" class="block text-sm py-3 px-3 rounded transition-all duration-300 
            <?= $current_page == 'archive_reports.php' ? 'bg-blue-800 text-white animate-slideRight' : 'hover:bg-blue-800 hover:text-white hover:translate-x-2' ?>">
            Archived Reports
        </a>
    </li>
    <li>
        <a href="census_admin.php" class="block text-sm py-3 px-3 rounded transition-all duration-300 
            <?= $current_page == 'archive_reports.php' ? 'bg-blue-800 text-white animate-slideRight' : 'hover:bg-blue-800 hover:text-white hover:translate-x-2' ?>">
            census verification
        </a>
    </li>
</ul>




</div>

<!-- Custom CSS -->
<style>
    @keyframes slideRight {
    0% {
        transform: translateX(-5px);
        opacity: 0;
    }
    100% {
        transform: translateX(0);
        opacity: 1;
    }
}

.animate-slideRight {
    animation: slideRight 0.3s ease-in-out;
}
    /* Auto-hide scrollbar */
    .scrollbar-hide::-webkit-scrollbar {
        width: 6px;
    }

    .scrollbar-hide::-webkit-scrollbar-thumb {
        background: transparent;
        border-radius: 3px;
    }

    .scrollbar-hide:hover::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.5);
    }
</style>
