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
  <h1 class="text-2xl font-bold flex justify-center mb-4">Our Animals</h1>
  <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
    <!-- Card Template -->
    <div class="card bg-white shadow-md rounded-lg overflow-hidden transform transition duration-300 hover:scale-105 hover:shadow-xl">
      <img src="../stored/pet_image/cat.jpg" alt="Animal" class="w-full h-45 object-cover">
      <div class="p-4">
        <h2 class="font-bold text-lg text-center mb-2">sunny</h2>
        <p class="text-sm text-gray-700 text-center mb-2"></p>
        <div class="flex justify-center">
          <button onclick="openModal()" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition duration-300">
            Adopt Me
          </button>
        </div>
      </div>
    </div>
    <div class="card bg-white shadow-md rounded-lg overflow-hidden transform transition duration-300 hover:scale-105 hover:shadow-xl">
      <img src="../stored/pet_image/dog.jpg" alt="Animal" class="w-full h-45 object-cover">
      <div class="p-4">
        <h2 class="font-bold text-lg text-center mb-2">winky</h2>
        <p class="text-sm text-gray-700 text-center mb-2"></p>
        <div class="flex justify-center">
          <button onclick="openModal()" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition duration-300">
            Adopt Me
          </button>
        </div>
      </div>
    </div>
    <div class="card bg-white shadow-md rounded-lg overflow-hidden transform transition duration-300 hover:scale-105 hover:shadow-xl">
      <img src="../stored/pet_image/cat1.jpg" alt="Animal" class="w-full h-45 object-cover">
      <div class="p-4">
        <h2 class="font-bold text-lg text-center mb-2">Bravo</h2>
        <p class="text-sm text-gray-700 text-center mb-2"></p>
        <div class="flex justify-center">
          <button onclick="openModal()" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition duration-300">
            Adopt Me
          </button>
        </div>
      </div>
    </div>
    <div class="card bg-white shadow-md rounded-lg overflow-hidden transform transition duration-300 hover:scale-105 hover:shadow-xl">
      <img src="../stored/pet_image/dog1.jpg" alt="Animal" class="w-full h-45 object-cover">
      <div class="p-4">
        <h2 class="font-bold text-lg text-center mb-2">smiley</h2>
        <p class="text-sm text-gray-700 text-center mb-2"></p>
        <div class="flex justify-center">
          <button onclick="openModal()" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition duration-300">
            Adopt Me
          </button>
        </div>
      </div>
    </div>
    <div class="card bg-white shadow-md rounded-lg overflow-hidden transform transition duration-300 hover:scale-105 hover:shadow-xl">
      <img src="../stored/pet_image/cat2.jpg" alt="Animal" class="w-full h-45 object-cover">
      <div class="p-4">
        <h2 class="font-bold text-lg text-center mb-2">Alex</h2>

        <p class="text-sm text-gray-700 text-center mb-2"></p>
        <div class="flex justify-center">
          <button onclick="openModal()" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition duration-300">
            Adopt Me
          </button>
        </div>
      </div>
    </div>
    <div class="card bg-white shadow-md rounded-lg overflow-hidden transform transition duration-300 hover:scale-105 hover:shadow-xl">
      <img src="../stored/pet_image/dog2.jpg" alt="Animal" class="w-full h-45 object-cover">
      <div class="p-4">
        <h2 class="font-bold text-lg text-center mb-2">shaggy</h2>
        <p class="text-sm text-gray-700 text-center mb-2"></p>
        <div class="flex justify-center">
          <button onclick="openModal()" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition duration-300">
            Adopt Me
          </button>
        </div>
      </div>
    </div>    
    
    <div class="card bg-white shadow-md rounded-lg overflow-hidden transform transition duration-300 hover:scale-105 hover:shadow-xl">
      <img src="../stored/pet_image/cat3.jpg" alt="Animal" class="w-full h-45 object-cover">
      <div class="p-4">
        <h2 class="font-bold text-lg text-center mb-2">Jhonny</h2>
        <p class="text-sm text-gray-700 text-center mb-2"></p>
        <div class="flex justify-center">
          <button onclick="openModal()" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition duration-300">
            Adopt Me
          </button>
        </div>
      </div>
    </div>    
    
    <div class="card bg-white shadow-md rounded-lg overflow-hidden transform transition duration-300 hover:scale-105 hover:shadow-xl">
      <img src="../stored/pet_image/dog3.jpg" alt="Animal" class="w-full h-45 object-cover">
      <div class="p-4">
        <h2 class="font-bold text-lg text-center mb-2">miles</h2>
        <p class="text-sm text-gray-700 text-center mb-2"></p>
        <div class="flex justify-center">
          <button onclick="openModal()" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition duration-300">
            Adopt Me
          </button>
        </div>
      </div>
    </div>
    <!-- Add more cards as needed -->
  </div>
 
    <!-- Modal -->
    <div id="adoptionModal" class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50 hidden">
    <div class="bg-white rounded-lg w-96 p-6 shadow-lg">
      <h2 class="text-2xl font-bold mb-4">Name</h2>
      <p class="text-gray-600 mb-4">Information.</p>
      <p class="text-gray-600 mb-4">Type: </p>
      <p class="text-gray-600 mb-4">Age: </p>
      <p class="text-gray-600 mb-4">Breed: </p>
      <div class="flex justify-end">
        <button onclick="closeModal()" class="bg-red-600 text-white px-4 py-2 rounded mr-2 hover:bg-red-700 transition duration-300">Close</button>
        <a href="user_adopt.php" target="_blank" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition duration-300 inline-block">
        Proceed to Adopt
      </a>


      </div>
    </div>
  </div>
</div>   
    </main>
  </div>
  <script src="disc/js/script.js"></script>
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
</body>
</html>


