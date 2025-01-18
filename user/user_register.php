<?php
include('../dbconn/config.php');
include('../dbconn/authentication.php');
checkAccess('user');
include('../phpqrcode/qrlib.php');


$showModal = false; // To control modal visibility in HTML
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $error = array();
    $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
    $targetDir = "../stored/pet_image/";
    $vaccineDir = "../stored/vaccine_record/";

    // Validate inputs
    if (empty($_POST['owner_name'])) {
        $error['owner_name'] = 'Owner Name is required';
    } else {
        $ownerName = htmlspecialchars($_POST['owner_name']);
    }
    
    if (empty($_POST['email'])) {
        $error['email'] = 'Email is required';
    } else {
        $email = htmlspecialchars($_POST['email']); // Corrected variable assignment
    }

    if (empty($_POST['pet_name'])) {
        $error['pet_name'] = 'Pet Name is required';
    } else {
        $petName = htmlspecialchars($_POST['pet_name']);
    }

    if (empty($_POST['pet_age'])) {
        $error['pet_age'] = 'Pet age is required';
    } else {
        $petAge = htmlspecialchars($_POST['pet_age']);
    }

    if (empty($_POST['pet_breed'])) {
        $error['pet_breed'] = 'Pet breed is required';
    } else {
        $petBreed = htmlspecialchars($_POST['pet_breed']);
    }

    if (empty($_POST['address'])) {
        $error['address'] = 'Address is required';
    } else {
        $address = htmlspecialchars($_POST['address']);
    }

    if (empty($_POST['additional_info'])) {
        $error['additional_info'] = 'Additional information is required';
    } else {
        $additionalInfo = htmlspecialchars($_POST['additional_info']);
    }

    // Handle file uploads
    function handleFileUpload($fileInputName, $allowedTypes, $targetDir, &$error) {
        if (isset($_FILES[$fileInputName]) && $_FILES[$fileInputName]['error'] === 0) {
            $fileType = mime_content_type($_FILES[$fileInputName]['tmp_name']);
            $fileSize = $_FILES[$fileInputName]['size'];

            if (!in_array($fileType, $allowedTypes)) {
                $error[$fileInputName] = "Only JPEG, JPG, and PNG files are allowed for $fileInputName";
            } elseif ($fileSize > 2 * 1024 * 1024) { // 2MB limit
                $error[$fileInputName] = "File size should not exceed 2MB for $fileInputName";
            } else {
                $fileName = basename($_FILES[$fileInputName]['name']);
                $sanitizedFile = preg_replace("/[^a-zA-Z0-9,\-_]/", "", $fileName); // Sanitize filename
                $targetFile = $targetDir . $sanitizedFile;

                if (move_uploaded_file($_FILES[$fileInputName]['tmp_name'], $targetFile)) {
                    return $targetFile;
                } else {
                    $error[$fileInputName] = "Failed to upload $fileInputName";
                }
            }
        } else {
            $error[$fileInputName] = "File is required for $fileInputName";
        }
        return null;
    }

    // Handle the pet image upload
    $petImagePath = handleFileUpload('pet_image', $allowedTypes, $targetDir, $error);

    // Handle the vaccine record upload
    $vaccineRecordPath = handleFileUpload('vaccine_record', $allowedTypes, $vaccineDir, $error);

    // Check for errors before database insertion
    if (empty($error)) {
        $sql = "INSERT INTO register (owner,email, pet, age, breed, address, pet_image, pet_vaccine, additional_info) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?,?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssssss", $ownerName, $email, $petName, $petAge, $petBreed, $address, $petImagePath, $vaccineRecordPath, $additionalInfo);

        if ($stmt->execute()) {
          // Generate QR code after successful insertion
          $registrationID = $stmt->insert_id; // get the last inserted ID (assuming it's auto-incremented)
          
          // URL for the pet's profile (make sure the URL is accessible)
          $profileUrl = "localhost/petlog/Pet_profiling.php?id=" . $registrationID; // Example URL with registration ID

         // Generate the QR code and save it to a file
$qrCodeFile = "../qrUpload/pet_" . $registrationID . "_qr.png"; // Set the QR code file path

// Make sure the qrUpload folder exists and is writable
if (!file_exists("../qrUpload")) {
    mkdir("../qrUpload", 0777, true); // Create the folder if it doesn't exist
}

QRcode::png($profileUrl, $qrCodeFile, QR_ECLEVEL_L, 10);

// Set $showModal to true to display the modal
$showModal = true;
unset($_POST); // Clear form data after submission

        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }

        $stmt->close();
    }
}

$conn->close();

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
 <?php include ('disc/partials/sidebar.php'); ?>  

  <!-- Main Content with Navbar -->
  <div class="flex-1 flex flex-col">
    <!-- Top Navbar -->
    <?php include('disc/partials/navbar.php'); ?>

    <!-- Main Content Area -->
    <main id="mainContent" class="p-8">
      <div class="flex justify-center items-center w-full">
        <form action="" method="POST" enctype="multipart/form-data" class="bg-white p-8 rounded-lg shadow-md w-full">
        <i class="fas fa-paw text-3xl text-blue-500 mb-2"></i> <h2 class="text-2xl font-bold mb-6 text-center">Registration Form</h2>
          <div class="grid grid-cols-2">
            <div class="">
              <div class="mb-4 mr-4">
                <label class="block text-gray-700" for="owner_name">Owner Name:</label>
                <input class="w-full p-2 border border-gray-300 rounded mt-1" name="owner_name" type="text" value="<?php echo isset($_POST['owner_name']) ? htmlspecialchars($_POST['owner_name']) : ''; ?>">
                <?php if (isset($error['owner_name'])) echo "<span class='text-red-500 text-sm'>" . $error['owner_name'] . "</span>"; ?>
              </div>
              <div class="mb-4 mr-4">
                <label class="block text-gray-700" for="pet_breed">Email:</label>
                <input class="w-full p-2 border border-gray-300 rounded mt-1" name="email" type="text" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                <?php if (isset($error['email'])) echo "<span class='text-red-500 text-sm'>" . $error['email'] . "</span>"; ?>
              </div>
              <div class="mb-4 mr-4">
                <label class="block text-gray-700" for="pet_name">Pet Name:</label>
                <input class="w-full p-2 border border-gray-300 rounded mt-1" name="pet_name" type="text" value="<?php echo isset($_POST['pet_name']) ? htmlspecialchars($_POST['pet_name']) : ''; ?>">
                <?php if (isset($error['pet_name'])) echo "<span class='text-red-500 text-sm'>" . $error['pet_name'] . "</span>"; ?>
              </div>
              <div class="mb-4 mr-4">
                <label class="block text-gray-700" for="pet_age">Pet Age:</label>
                <input class="w-full p-2 border border-gray-300 rounded mt-1" name="pet_age" type="text" value="<?php echo isset($_POST['pet_age']) ? htmlspecialchars($_POST['pet_age']) : ''; ?>">
                <?php if (isset($error['pet_age'])) echo "<span class='text-red-500 text-sm'>" . $error['pet_age'] . "</span>"; ?>
              </div>
              <div class="mb-4 mr-4">
                <label class="block text-gray-700" for="pet_breed">Pet Breed:</label>
                <input class="w-full p-2 border border-gray-300 rounded mt-1" name="pet_breed" type="text" value="<?php echo isset($_POST['pet_breed']) ? htmlspecialchars($_POST['pet_breed']) : ''; ?>">
                <?php if (isset($error['pet_breed'])) echo "<span class='text-red-500 text-sm'>" . $error['pet_breed'] . "</span>"; ?>
              </div>
            </div>

            <div class="">
            <div class="mb-4 mr-4">
                <label class="block text-gray-700" for="address">Address:</label>
                <input class="w-full p-2 border border-gray-300 rounded mt-1" name="address" type="text" value="<?php echo isset($_POST['address']) ? htmlspecialchars($_POST['address']) : ''; ?>">
                <?php if (isset($error['address'])) echo "<span class='text-red-500 text-sm'>" . $error['address'] . "</span>"; ?>
              </div>
              <div class="mb-4 mr-4">
                <label class="block text-gray-700" for="pet_image">Pet Image:</label>
                <input class="w-full p-2 border border-gray-300 rounded mt-1" name="pet_image" type="file">
                <?php if (isset($error['pet_image'])) echo "<span class='text-red-500 text-sm'>" . $error['pet_image'] . "</span>"; ?>
              </div>
              <div class="mb-4 mr-4">
                <label class="block text-gray-700" for="vaccine_record">Vaccine Record:</label>
                <input class="w-full p-2 border border-gray-300 rounded mt-1" name="vaccine_record" type="file">
                <?php if (isset($error['vaccine_record'])) echo "<span class='text-red-500 text-sm'>" . $error['vaccine_record'] . "</span>"; ?>
              </div>
              <div class="mb-4 mr-4">
                <label class="block text-gray-700" for="additional_info">Additional Information:</label>
                <textarea class="w-full p-2 border border-gray-300 rounded mt-1" name="additional_info"><?php echo isset($_POST['additional_info']) ? htmlspecialchars($_POST['additional_info']) : ''; ?></textarea>
                <?php if (isset($error['additional_info'])) echo "<span class='text-red-500 text-sm'>" . $error['additional_info'] . "</span>"; ?>
              </div>
            </div>
          </div>
          <div class="flex justify-center">
            <button type="submit" name="regsBtn" class="bg-blue-600 text-white p-2 rounded mt-4 hover:bg-blue-700">
              Submit Registration
            </button>
          </div>
        </form>
      </div>
    </main>
  </div>
<!-- Modal to show QR code after successful registration -->
<?php if ($showModal): ?>
<div id="qrCodeModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white p-6 rounded shadow-lg text-center">
        <h2 class="text-xl font-bold mb-4">Registration Successful!</h2>
        <p>Scan or download the QR Code below:</p>
        
        <!-- Display the QR Code -->
        <img id="qrCodeImage" src="<?php echo $qrCodeFile; ?>" alt="QR Code" class="mx-auto my-4 w-48 h-48">

        <!-- Buttons -->
        <div class="flex justify-center space-x-4">
            <!-- Download QR Code Button -->
            <a href="<?php echo $qrCodeFile; ?>" download
               class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                Download QR Code
            </a>
            <!-- View Profile Button -->
             <a href="../Pet_profiling.php?id=<?php echo $registrationID; ?>" 
               target='_blank' 
               class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                View Profile
            </a>
            <!-- Close Modal Button -->
            <button id="closeModal" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
                Close
            </button>
        </div>
    </div>
</div>
<script>
  document.addEventListener('DOMContentLoaded', function () {
    // Close Modal Functionality
    document.getElementById('closeModal').addEventListener('click', function () {
        document.getElementById('qrCodeModal').style.display = 'none';
    });
  });
</script>
<?php endif; ?>
<script src="disc/js/script.js"></script>
</body>
</html>