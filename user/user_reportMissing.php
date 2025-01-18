<?php
include('../dbconn/config.php');
include('../dbconn/authentication.php');
checkAccess('user');

$showModal = false; // To control modal visibility in HTML

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $error = array();
    // Initialize variables to avoid undefined variable notices
    $reportParty = $phone = $email = $species = $breed = $number = $abuse_nature = $incidentDescription = $targetFile = '';


    // Validate each field and store error messages if any
    if (empty($_POST['report_party'])) {
        $error['report_party'] = "Report Party is required";
    } else {
        $reportParty = htmlspecialchars($_POST['report_party']);
    }

    if (empty($_POST['email'])) {
        $error['email'] = "Email is required";
    } else {
        $email = htmlspecialchars($_POST['email']);
        
        // Apply email validation filter
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error['email'] = "Invalid email format";
        }
    }

    if (empty($_POST['phone'])) {
        $error['phone'] = "Phone is required";
    } else {
        $phone = htmlspecialchars($_POST['phone']);
    }


    if (empty($_POST['breed'])) {
        $error['breed'] = "Breed is required";
    } else {
        $breed = htmlspecialchars($_POST['breed']);
    }

    if (empty($_POST['place'])) {
        $error['place'] = "this is required";
    } else {
        $place = htmlspecialchars($_POST['place']);
    }

    if (empty($_POST['gender'])) {
        $error['gender'] = "this is required";
    } else {
        $gender = htmlspecialchars($_POST['gender']);
    }


    if (empty($_POST['description'])) {
        $error['description'] = "Description is required";
    } else {
        $descript = htmlspecialchars($_POST['description']);
    }

    // Handle file upload
    if (isset($_FILES['imgInput']) && $_FILES['imgInput']['error'] === 0) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
        $fileType = mime_content_type($_FILES['imgInput']['tmp_name']);
        $fileSize = $_FILES['imgInput']['size'];

        // Check file type
        if (!in_array($fileType, $allowedTypes)) {
            $error['imgInput'] = "Only JPEG, JPG, and PNG files are allowed";
        } 
        // Check file size (limit to 2MB)
        elseif ($fileSize > 2 * 1024 * 1024) {
            $error['imgInput'] = "File size should not exceed 2MB";
        } else {
            $photoFile = basename($_FILES['imgInput']['name']);
            $targetDir = "../stored/pet_image/";

            // Ensure the target directory exists
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0755, true);
            }

            // Sanitize the filename and prepare the target file path
            $targetFile = $targetDir . preg_replace("/[^a-zA-Z0-9.\-_]/", "", $photoFile); 

            // Move the uploaded file to the target directory
            if (!move_uploaded_file($_FILES['imgInput']['tmp_name'], $targetFile)) {
                $error['imgInput'] = "Failed to move uploaded file.";
            }
        }
    } else {
        $error['imgInput'] = "Evidence file is required";
    }

    // Check if there are no errors before inserting into the database
    if (empty($error)) {
        $sql = "INSERT INTO Missing (m_name, m_mail, m_phone, m_breed, m_place, m_descript, m_photo) 
        VALUES (?, ?, ?, ?, ?, ?, ?)";

       $stmt = $conn->prepare($sql);

         if (!$stmt) {
           die("Error preparing statement: " . $conn->error);
        }

          $stmt->bind_param("sssssss", $reportParty, $email, $phone, $breed, $place, $descript, $targetFile);

// Execute and check for success
           if ($stmt->execute()) {
          $showModal = true;
           unset($_POST);
          } else {
             echo "Error executing query: " . $stmt->error;
           }

        $stmt->close();

    }

    $conn->close();
}
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
<?php include('disc/partials/sidebar.php'); ?>

<!-- Main Content with Navbar -->
<div class="flex-1 flex flex-col">

    <!-- Top Navbar -->
    <?php include('disc/partials/navbar.php'); ?>

    <!-- Main Content Area -->
    <main id="mainContent" class="p-8">
        <div class="flex justify-center items-center w-full">
            <form action="" method="POST" class="bg-white p-8 rounded-lg shadow-md w-full" enctype="multipart/form-data">
            <i class="fas fa-paw text-3xl text-blue-500 mb-2"></i><h2 class="text-2xl font-bold mb-6 text-center">LOST AND FOUND PETS</h2>
                <div class="grid grid-cols-2 gap-4">

                    <!-- Report Party Field -->
                    <div>
                        <label class="block text-gray-700" for="report_party">Report Party</label>
                        <input class="w-full p-2 border border-gray-300 rounded mt-1" name="report_party" type="text" value="<?php echo isset($_POST['report_party']) ? htmlspecialchars($_POST['report_party']) : ''; ?>" >
                        <?php if (isset($error['report_party'])) echo "<span class='text-red-500 text-sm'>" . $error['report_party'] . "</span>"; ?>
                    </div>

                      <!-- Email Field -->
                      <div>
                        <label class="block text-gray-700" for="email">Email</label>
                        <input class="w-full p-2 border border-gray-300 rounded mt-1" name="email" type="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" >
                        <?php if (isset($error['email'])) echo "<span class='text-red-500 text-sm'>" . $error['email'] . "</span>"; ?>
                    </div>


                    <!-- Phone Field -->
                    <div>
                        <label class="block text-gray-700" for="phone">Phone</label>
                        <input class="w-full p-2 border border-gray-300 rounded mt-1" name="phone" type="tel" value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>" >
                        <?php if (isset($error['phone'])) echo "<span class='text-red-500 text-sm'>" . $error['phone'] . "</span>"; ?>
                    </div>
         

                    <!-- Breed Field -->
                    <div>
                        <label class="block text-gray-700" for="breed">Breed</label>
                        <input class="w-full p-2 border border-gray-300 rounded mt-1" name="breed" type="text" value="<?php echo isset($_POST['breed']) ? htmlspecialchars($_POST['breed']) : ''; ?>" >
                        <?php if (isset($error['breed'])) echo "<span class='text-red-500 text-sm'>" . $error['breed'] . "</span>"; ?>
                    </div>
 
                    <div>
                        <label class="block text-gray-700" for="place">Place Lost/Found</label>
                        <input class="w-full p-2 border border-gray-300 rounded mt-1" name="place" type="text" value="<?php echo isset($_POST['place']) ? htmlspecialchars($_POST['place']) : ''; ?>" >
                        <?php if (isset($error['place'])) echo "<span class='text-red-500 text-sm'>" . $error['place'] . "</span>"; ?>
                    </div>
               
                    <!-- Gender -->
                   <div>
            <label class="block text-gray-700" for="gender">Gender</label>
            <select class="w-full p-2 border border-gray-300 rounded mt-1" name="gender" >
                <option value="">Select</option>
                <option value="male" <?php echo (isset($_POST['male']) && $_POST['male'] == 'male') ? 'selected' : ''; ?>>Male</option>
                <option value="female" <?php echo (isset($_POST['female']) && $_POST['female'] == 'female') ? 'selected' : ''; ?>>Female</option>
                <option value="unsure" <?php echo (isset($_POST['unsure']) && $_POST['unsre'] == 'unsure') ? 'selected' : ''; ?>>Unsure</option>
            </select>
            <?php if (isset($error['gender'])) echo "<span class='text-red-500 text-sm'>" . $error['gender'] . "</span>"; ?>

                    
                  </div>
                  <div>
                        <label class="block text-gray-700" for="description">Description</label>
                        <textarea class="w-full p-2 border border-gray-300 rounded mt-1" name="description" ><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea>
                        <?php if (isset($error['description'])) echo "<span class='text-red-500 text-sm'>" . $error['description'] . "</span>"; ?>
                    </div>
                    <!-- photo Upload -->
                    <div>
                        <label class="block text-gray-700" for="imgInput">Photo Upload</label>
                        <input class="w-full p-2 border border-gray-300 rounded mt-1" name="imgInput" type="file" >
                        <?php if (isset($error['imgInput'])) echo "<span class='text-red-500 text-sm'>" . $error['imgInput'] . "</span>"; ?>
                    </div>

                </div>

                <div class="flex justify-center mt-6">
                    <button type="submit" class="bg-blue-600 text-white p-2 rounded">Submit</button>
                </div>
            </form>
        </div>
    </main>
</div>


<?php if ($showModal): ?>
<div id="successModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white p-6 rounded shadow-lg text-center">
        <h2 class="text-xl font-bold mb-4">Successful submitted!</h2>
        <!-- Buttons -->
        <div class="flex justify-center space-x-4">
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
        document.getElementById('successModal').style.display = 'none';
    });
  });
</script>
<?php endif; ?>
<script src="disc/js/script.js"></script>
</body>
</html>