<?php
session_start();
include '../internet/connect_ka.php';  // Make sure this file includes the MySQLi connection setup
include 'design/top.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Ensure the user is logged in
if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

// Get logged-in user's email
$user_email = $_SESSION['email'];

// Query to get the role of the logged-in user from registerlanding table using MySQLi
$query = "SELECT * FROM registerlanding WHERE email = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('s', $user_email);
if ($stmt->execute()) {
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
} else {
    echo "SQL Error: " . $stmt->error;
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Shelter Information</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-blue-900 font-poppins">
  <div class="max-w-2xl mx-auto mt-6 p-4 bg-white rounded-lg shadow-md">

    <!-- Shelter Header -->
    <div class="relative">
      <a href="admin_dashboard.php"
        class="absolute top-0 left-0 mt-3 ml-3 px-3 py-1 bg-blue-600 text-white text-xs font-semibold rounded shadow-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition">
        Back
      </a>
    </div>
<?php if (isset($_SESSION['success_message'])): ?>
        <div class="fixed top-0 left-0 right-0 z-50 flex justify-center p-4 bg-green-500 text-white text-xs font-semibold rounded-md">
            <span><?php echo $_SESSION['success_message']; ?></span>
        </div>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>
    <div class="text-center mt-10">
      <h2 class="text-xl font-bold text-red-700">Sheltered Pet Information</h2>
      <p class="text-gray-600 mt-1 text-xs">Fill out the details of the pet and shelter information.</p>
    </div>

    <div class="border-t border-gray-300 my-3"></div>

    <!-- Form -->
    <form id="shelterForm" action="submit_shelter_report.php" method="POST" enctype="multipart/form-data" class="space-y-3">

      <!-- Reporter Information -->
      <div class="bg-gray-100 p-2 rounded">
        <h3 class="text-xs font-semibold text-gray-800">Your Information</h3>
        <label class="block mt-1">
          <span class="font-medium text-gray-700 text-xs">Email:</span>
          <input type="email" name="reporter_email" value="<?php echo htmlspecialchars($user_email); ?>" readonly
            class="w-full py-1 border-b border-gray-400 focus:outline-none focus:border-blue-500 text-xs" />
        </label>
      </div>

      <!-- Pet Information -->
      <div class="bg-gray-100 p-2 rounded">
        <h3 class="text-xs font-semibold text-gray-800">Pet Information</h3>

        <label class="block mt-1">
          <span class="font-medium text-gray-700 text-xs">Pet Name:</span>
          <input type="text" name="pet_name" required
            class="w-full py-1 border-b border-gray-400 focus:outline-none focus:border-blue-500 text-xs" />
        </label>

        <label class="block mt-1">
          <span class="font-medium text-gray-700 text-xs">Breed:</span>
          <input type="text" name="breed" required
            class="w-full py-1 border-b border-gray-400 focus:outline-none focus:border-blue-500 text-xs" />
        </label>

        <label class="block mt-1">
          <span class="font-medium text-gray-700 text-xs">Type:</span>
          <select name="pet_type" required
            class="w-full py-1 border-b border-gray-400 focus:outline-none focus:border-blue-500 text-xs bg-white">
            <option value="" disabled selected>Select Pet Type</option>
            <option value="Dog">Dog</option>
            <option value="Cat">Cat</option>
          </select>
        </label>

        <label class="block mt-1">
          <span class="font-medium text-gray-700 text-xs">Additional Information:</span>
          <textarea name="additional_info" rows="3"
            class="w-full py-1 border-b border-gray-400 focus:outline-none focus:border-blue-500 text-xs"
            placeholder="Enter any additional details about the pet..."></textarea>
        </label>

        <label class="block mt-1">
          <span class="font-medium text-gray-700 text-xs">Upload Pet Image:</span>
          <input type="file" name="pet_image" accept="image/*"
            class="w-full py-1 border-b border-gray-400 focus:outline-none focus:border-blue-500 text-xs" />
        </label>
      </div>

      <!-- Submit Button triggers modal -->
      <div class="flex justify-center">
        <button type="button" onclick="openModal()"
          class="bg-blue-600 text-white px-6 py-2 rounded-md text-xs font-semibold hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
          Add Stray Pet
        </button>
      </div>
    </form>
  </div>

  <!-- Modal -->
  <div id="confirmModal"
    class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 transition">
    <div class="bg-white p-6 rounded-lg shadow-xl max-w-sm w-full">
      <h2 class="text-base font-bold text-gray-800">Confirm Submission</h2>
      <p class="text-sm text-gray-600 mt-2">Are you sure you want to submit this pet information?</p>
      <div class="mt-4 flex justify-end space-x-2">
        <button onclick="closeModal()"
          class="px-4 py-1 bg-gray-300 text-gray-800 rounded hover:bg-gray-400 text-xs font-semibold">No</button>
        <button onclick="submitForm()"
          class="px-4 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 text-xs font-semibold">Yes</button>
      </div>
    </div>
  </div>

  <!-- JS Script for Modal Logic -->
  <script>
    function openModal() {
      document.getElementById("confirmModal").classList.remove("hidden");
    }

    function closeModal() {
      document.getElementById("confirmModal").classList.add("hidden");
    }

    function submitForm() {
      document.getElementById("shelterForm").submit();
    }
      // If needed, you can also add additional logic for closing the notification after a few seconds
        setTimeout(() => {
            const notification = document.querySelector('.bg-green-500');
            if (notification) {
                notification.classList.add('hidden');
            }
        }, 5000); // Notification will disappear after 5 seconds
  </script>
</body>

</html>
