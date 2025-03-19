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
    <!-- Dashboard -->
    <li>
        <a href="#" class="block text-sm py-3 px-3 rounded hover:bg-blue-700">🏠 Dashboard</a>
    </li>

    <!-- NEW MANAGEMENT -->
    <li class="mt-5 text-xs font-bold text-gray-300 uppercase">New Management</li>
    <li><a href="#" class="block text-sm py-3 px-3 rounded hover:bg-blue-700">📷 Pet Identification</a></li>

    <!-- REGISTRATION MANAGEMENT -->
    <li class="mt-5 text-xs font-bold text-gray-300 uppercase">Registration Management</li>
    <li><a href="#" class="block text-sm py-3 px-3 rounded hover:bg-blue-700">🐾 Pet Register</a></li>
    <li><a href="#" class="block text-sm py-3 px-3 rounded hover:bg-blue-700">📋 My Pets</a></li>

    <!-- ADOPTION MANAGEMENT -->
    <li class="mt-5 text-xs font-bold text-gray-300 uppercase">Adoption Management</li>
    <li><a href="#" class="block text-sm py-3 px-3 rounded hover:bg-blue-700">🏡 Adopt a Pet</a></li>
    <li><a href="#" class="block text-sm py-3 px-3 rounded hover:bg-blue-700">📜 Adopted List</a></li>

    <!-- REPORT MANAGEMENT -->
    <li class="mt-5 text-xs font-bold text-gray-300 uppercase">Report Management</li>
    <li><a href="#" class="block text-sm py-3 px-3 rounded hover:bg-blue-700">🔍 Report Missing Pet</a></li>
    <li><a href="#" class="block text-sm py-3 px-3 rounded hover:bg-blue-700">📄 Pet Missing List</a></li>
    <li><a href="#" class="block text-sm py-3 px-3 rounded hover:bg-blue-700">⚠️ Report Animal Cruelty</a></li>
</ul>


</div>

<!-- Custom CSS -->
<style>
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
