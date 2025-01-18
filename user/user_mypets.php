<?php
include('../dbconn/config.php');
include('../dbconn/authentication.php');
checkAccess('user'); 
?>
<!DOCTYPE html>
<html lang="en">
<head>
<head>
   <?php include('disc/partials/header.php');?>
  </head>
</head>

<body class="flex bg-[#90e0ef]">

  <!-- Sidebar -->
<?php
  include('disc/partials/sidebar.php');
 ?>

  <!-- Main Content with Navbar -->
  <div class="flex-1 flex flex-col">
    
    <!-- Top Navbar -->
 <?php
  include('disc/partials/navbar.php');
 ?>

    <!-- Main Content Area -->
    <main id="mainContent" class="p-8">
    <div class="w-full">
  <h1 class="text-2xl font-bold flex justify-center mb-4">MY PETS</h1>
  <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
    <!-- Card Template -->
    
    <div onclick="openModal()" class="card bg-white shadow-md rounded-lg overflow-hidden transform transition duration-300 hover:scale-105 hover:shadow-xl">
      <img src="../stored/pet_image/cat.jpg" alt="Animal" class="w-full h-45 object-cover">
      <div class="p-4">
        <h2 class="font-bold text-lg text-center mb-2">sunny</h2>
        <p class="text-sm text-gray-700 text-center mb-2"></p>
      </div>
    </div>
    <!-- Add more cards as needed -->
  </div>
 
<!-- Modal -->
<div id="adoptionModal" class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50 hidden">
        <div class="bg-[url('img/bgDog.jpg')] grid grid-cols-2 rounded-lg w-1/2 p-10 shadow-lg">
             <img src="../stored/pet_image/cat.jpg" alt="Animal" class="w-full h-45 rounded-lg ">
                <div class="m-4">
                  <div class="flex flex-col item-center justify-end ml-12">
                  <h1 class="text-black font-semibold text-xl ">Name: Sunny</h1>
                  <h2 class="text-black font-semibold text-xl ">Birth: April 25 2023</h2>
                  <h2 class="text-black font-semibold text-xl ">Age: 1 yrs</h2>
                  <h2 class="text-black font-semibold text-xl ">breed: kupal</h2>
                  <h2 class="text-black font-semibold text-xl ">Owner Name : Wan</h2>
                  </div>
                  <div class="flex item-center justify-end mt-20">
                  <a href="user_reportMissing.php" target="_blank" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition duration-300 inline-block">
                    Missing Report
                  </a>
                  <button onclick="closeModal()" class="bg-red-600 text-white px-4 py-2 rounded ml-2 hover:bg-red-700 transition duration-300">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>   
    </main>
  </div>

 
<script>
  // Open the modal
  function openModal() {
    document.getElementById('adoptionModal').classList.remove('hidden');
  }

  // Close the modal
  function closeModal(event = null) {
    // Close modal only if clicked on the background or when triggered directly
    if (!event || event.target.id === 'adoptionModal') {
      document.getElementById('adoptionModal').classList.add('hidden');
    }
  }
</script>
  <script src="disc/js/script.js"></script>
</body>
</html>


