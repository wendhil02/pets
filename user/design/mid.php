<?php

include '../internet/connect_ka.php';
$current_page = basename($_SERVER['PHP_SELF']); // Get current file name
?>

<div id="sidebar" class="sidebar bg-[#0A2A52] text-white fixed h-screen transition-all md:translate-x-0 w-64 overflow-y-auto scrollbar-hide">
    <!-- ☰ Button (Kapag Mobile, nasa Sidebar kapag Open) -->

    <div class="p-1 flex justify-end md:hidden">
        <button id="closeSidebarMobile" class="text-white text-lg px-2 py-1 hover:bg-blue-700 rounded">x</button>
    </div>
    <!-- Logo Section -->
    <div class="p-2 flex flex-col items-center space-y-3 px-4">
        <img src="logo/logo.png" alt="LGU Logo" class="w-12 h-12 rounded-full mb-2 border-2 border-yellow-500">
        <span class="text-sm font-semibold text-whie uppercase text-center">
                    <i class="fa-solid fa-shield-dog text-yellow-500"></i> LGU - Pet Animal Welfare Protection System
                </span>
    </div>

    <ul class="space-y-3 px-4">
    <!-- Dashboard -->
    <li class="mt-5 text-xs font-bold text-gray-500 uppercase">Dashboard</li>
    <li>
        <a href="dashboard.php" class="flex items-center text-sm py-3 px-3 rounded transition-all duration-300 
        <?= $current_page == 'dashboard.php' ? 'bg-blue-800 text-white animate-slideRight' : 'hover:bg-blue-800 hover:text-white hover:translate-x-2' ?>">
            <i class="fa-solid fa-house mr-2"></i> Dashboard
        </a>
    </li>

    <!-- PROFILE MANAGEMENT -->
    <li class="mt-5 text-xs font-bold text-gray-500 uppercase">Profile</li>
    <li>
        <a href="petprofile.php" class="flex items-center text-sm py-3 px-3 rounded transition-all duration-300 
        <?= $current_page == 'petprofile.php' ? 'bg-blue-800 text-white animate-slideRight' : 'hover:bg-blue-800 hover:text-white hover:translate-x-2' ?>">
            <i class="fa-solid fa-paw mr-2"></i> Pet Profile
        </a>
    </li>
    <li>
        <a href="petai.php" class="flex items-center text-sm py-3 px-3 rounded transition-all duration-300 
        <?= $current_page == 'petai.php' ? 'bg-blue-800 text-white animate-slideRight' : 'hover:bg-blue-800 hover:text-white hover:translate-x-2' ?>">
        <i class="fa-solid fa-magnifying-glass mr-2"></i> Pet Image Recognition
        </a>
    </li>

    <!-- REGISTRATION MANAGEMENT -->
    <li class="mt-5 text-xs font-bold text-gray-500 uppercase">Registration</li>
    <li>
        <a href="parehistro.php" class="flex items-center text-sm py-3 px-3 rounded transition-all duration-300 
        <?= $current_page == 'parehistro.php' ? 'bg-blue-800 text-white animate-slideRight' : 'hover:bg-blue-800 hover:text-white hover:translate-x-2' ?>">
            <i class="fa-solid fa-dog mr-2"></i> Pet Register
        </a>
    </li>
    <li>
        <a href="sarilingpet.php" class="flex items-center text-sm py-3 px-3 rounded transition-all duration-300 
        <?= $current_page == 'sarilingpet.php' ? 'bg-blue-800 text-white animate-slideRight' : 'hover:bg-blue-800 hover:text-white hover:translate-x-2' ?>">
            <i class="fa-solid fa-list-check mr-2"></i> My Pets
        </a>
    </li>
    <li>
        <a href="viewofreq.php" class="flex items-center text-sm py-3 px-3 rounded transition-all duration-300 
        <?= $current_page == 'viewofreq.php' ? 'bg-blue-800 text-white animate-slideRight' : 'hover:bg-blue-800 hover:text-white hover:translate-x-2' ?>">
            <i class="fa-solid fa-list-check mr-2"></i> Requested Email Pet
        </a>
    </li>
    <!-- ADOPTION MANAGEMENT -->
    <li class="mt-5 text-xs font-bold text-gray-500 uppercase">Adoption & Adopted</li>
    <li>
        <a href="iwantadopt.php" class="flex items-center text-sm py-3 px-3 rounded transition-all duration-300 
        <?= $current_page == 'iwantadopt.php' ? 'bg-blue-800 text-white animate-slideRight' : 'hover:bg-blue-800 hover:text-white hover:translate-x-2' ?>">
            <i class="fa-solid fa-hand-holding-heart mr-2"></i> Adopt a Pet
        </a>
    </li>
    <li>
        <a href="available_pets.php" class="flex items-center text-sm py-3 px-3 rounded transition-all duration-300 
        <?= $current_page == 'available_pets.php' ? 'bg-blue-800 text-white animate-slideRight' : 'hover:bg-blue-800 hover:text-white hover:translate-x-2' ?>">
            <i class="fa-solid fa-clipboard-list mr-2"></i> Adopted List
        </a>
    </li>

    <!-- REPORT MANAGEMENT -->
    <li class="mt-5 text-xs font-bold text-gray-500 uppercase">Report Services</li>
    <li>
        <a href="report_cruelty.php" class="flex items-center text-sm py-3 px-3 rounded transition-all duration-300 
        <?= $current_page == 'report_cruelty.php' ? 'bg-blue-800 text-white animate-slideRight' : 'hover:bg-blue-800 hover:text-white hover:translate-x-2' ?>">
            <i class="fa-solid fa-triangle-exclamation fa-fade mr-2"></i> Report Animal Cruelty
        </a>
    </li>
    
    <li class="mt-5 text-xs font-bold text-gray-500 uppercase">Settings</li>
    <li>
        <a href="../logout.php" class="flex items-center text-sm py-3 px-3 rounded transition-all duration-300 
        <?= $current_page == '../logout.php' ? 'bg-blue-800 text-white animate-slideRight' : 'hover:bg-blue-800 hover:text-white hover:translate-x-2' ?>">
            <i class="fa-solid fa-triangle-exclamation fa-fade mr-2"></i>Logout
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