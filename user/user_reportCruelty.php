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

    if (empty($_POST['phone'])) {
        $error['phone'] = "Phone is required";
    } else {
        $phone = htmlspecialchars($_POST['phone']);
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

    if (empty($_POST['species'])) {
        $error['species'] = "Species is required";
    } else {
        $species = htmlspecialchars($_POST['species']);
    }

    if (empty($_POST['breed'])) {
        $error['breed'] = "Breed is required";
    } else {
        $breed = htmlspecialchars($_POST['breed']);
    }

    if (empty($_POST['number'])) {
        $error['number'] = "Number is required";
    } else {
        $number = htmlspecialchars($_POST['number']);
    }

    if (empty($_POST['abuse_nature'])) {
        $error['abuse_nature'] = "Nature of Abuse is required";
    } else {
        $abuse_nature = htmlspecialchars($_POST['abuse_nature']);
    }

    if (empty($_POST['description'])) {
        $error['description'] = "Description is required";
    } else {
        $incidentDescription = htmlspecialchars($_POST['description']);
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
            $evidenceFile = basename($_FILES['imgInput']['name']);
            $targetDir = "../stored/reportEvidence/";

            // Ensure the target directory exists
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0755, true);
            }

            // Sanitize the filename and prepare the target file path
            $targetFile = $targetDir . preg_replace("/[^a-zA-Z0-9.\-_]/", "", $evidenceFile); 

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
        $sql = "INSERT INTO reports (name, phone, email, species, breed, numabuse, typeabuse, descript, evidence) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

        // Bind parameters
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssssss", $reportParty, $phone, $email, $species, $breed, $number, $abuse_nature, $incidentDescription, $targetFile);

        // Execute and check for success
        if ($stmt->execute()) {
            $showModal = true;
            unset($_POST);
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
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
            <i class="fas fa-paw text-3xl text-blue-500 mb-2"></i><h2 class="text-2xl font-bold mb-6 text-center">Report Cruelty</h2>
                <div class="grid grid-cols-2 gap-4">

                    <!-- Report Party Field -->
                    <div>
                        <label class="block text-gray-700" for="report_party">Report Party</label>
                        <input class="w-full p-2 border border-gray-300 rounded mt-1" name="report_party" type="text" value="<?php echo isset($_POST['report_party']) ? htmlspecialchars($_POST['report_party']) : ''; ?>" >
                        <?php if (isset($error['report_party'])) echo "<span class='text-red-500 text-sm'>" . $error['report_party'] . "</span>"; ?>
                    </div>

                    <!-- Phone Field -->
                    <div>
                        <label class="block text-gray-700" for="phone">Phone</label>
                        <input class="w-full p-2 border border-gray-300 rounded mt-1" name="phone" type="tel" value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>" >
                        <?php if (isset($error['phone'])) echo "<span class='text-red-500 text-sm'>" . $error['phone'] . "</span>"; ?>
                    </div>

                    <!-- Email Field -->
                    <div>
                        <label class="block text-gray-700" for="email">Email</label>
                        <input class="w-full p-2 border border-gray-300 rounded mt-1" name="email" type="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" >
                        <?php if (isset($error['email'])) echo "<span class='text-red-500 text-sm'>" . $error['email'] . "</span>"; ?>
                    </div>

                    <!-- Species Field -->
                    <div>
                        <label class="block text-gray-700" for="species">Species</label>
                        <input class="w-full p-2 border border-gray-300 rounded mt-1" name="species" type="text" value="<?php echo isset($_POST['species']) ? htmlspecialchars($_POST['species']) : ''; ?>" >
                        <?php if (isset($error['species'])) echo "<span class='text-red-500 text-sm'>" . $error['species'] . "</span>"; ?>
                    </div>

                    <!-- Breed Field -->
                    <div>
                        <label class="block text-gray-700" for="breed">Breed</label>
                        <input class="w-full p-2 border border-gray-300 rounded mt-1" name="breed" type="text" value="<?php echo isset($_POST['breed']) ? htmlspecialchars($_POST['breed']) : ''; ?>" >
                        <?php if (isset($error['breed'])) echo "<span class='text-red-500 text-sm'>" . $error['breed'] . "</span>"; ?>
                    </div>

               
                    <!-- Number of Abuse Field -->
                    <div>
                        <label class="block text-gray-700" for="number">Number of Abuses</label>
                        <input class="w-full p-2 border border-gray-300 rounded mt-1" name="number" type="number" value="<?php echo isset($_POST['number']) ? htmlspecialchars($_POST['number']) : ''; ?>" >
                        <?php if (isset($error['number'])) echo "<span class='text-red-500 text-sm'>" . $error['number'] . "</span>"; ?>
                    </div>

                    <!-- Nature of Abuse Field -->
                   <div>
            <label class="block text-gray-700" for="abuse_nature">Nature of Abuse</label>
            <select class="w-full p-2 border border-gray-300 rounded mt-1" name="abuse_nature" >
                <option value="">Select Nature of Abuse</option>
                <option value="Physical Abuse" <?php echo (isset($_POST['abuse_nature']) && $_POST['abuse_nature'] == 'Physical Abuse') ? 'selected' : ''; ?>>Physical Abuse</option>
                <option value="Emotional Abuse" <?php echo (isset($_POST['abuse_nature']) && $_POST['abuse_nature'] == 'Emotional Abuse') ? 'selected' : ''; ?>>Emotional Abuse</option>
                <option value="Neglect" <?php echo (isset($_POST['abuse_nature']) && $_POST['abuse_nature'] == 'Neglect') ? 'selected' : ''; ?>>Neglect</option>
                <option value="Abandonment" <?php echo (isset($_POST['abuse_nature']) && $_POST['abuse_nature'] == 'Abandonment') ? 'selected' : ''; ?>>Abandonment</option>
                <option value="Other" <?php echo (isset($_POST['abuse_nature']) && $_POST['abuse_nature'] == 'Other') ? 'selected' : ''; ?>>Other</option>
            </select>
            <?php if (isset($error['abuse_nature'])) echo "<span class='text-red-500 text-sm'>" . $error['abuse_nature'] . "</span>"; ?>
        </div>

                    <!-- Incident Description Field -->
                    <div class="col-span-2">
                        <label class="block text-gray-700" for="description">Description</label>
                        <textarea class="w-full p-2 border border-gray-300 rounded mt-1" name="description" ><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea>
                        <?php if (isset($error['description'])) echo "<span class='text-red-500 text-sm'>" . $error['description'] . "</span>"; ?>
                    </div>

                    <!-- Evidence File Upload -->
                    <div class="col-span-2">
                        <label class="block text-gray-700" for="imgInput">Evidence Upload</label>
                        <input class="w-full p-2 border border-gray-300 rounded mt-1" name="imgInput" type="file" >
                        <?php if (isset($error['imgInput'])) echo "<span class='text-red-500 text-sm'>" . $error['imgInput'] . "</span>"; ?>
                    </div>

                </div>

                <div class="flex justify-center mt-6">
                    <button type="submit" class="bg-blue-600 text-white p-2 rounded">Submit Report</button>
                </div>
            </form>
        </div>
    </main>
</div>


<?php if ($showModal): ?>
<div id="successModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white p-6 rounded shadow-lg text-center">
        <h2 class="text-xl font-bold mb-4">Report Successful!</h2>
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
