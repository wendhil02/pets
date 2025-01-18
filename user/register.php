<?php
include('../dbconn/config.php');
include('../dbconn/authentication.php');
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

<!DOCTYPE php>
<php lang="en">
  <head>
 <?php include ('./disc/partials/header.php'); ?>
 </head>
  </head>

    
    <div class="loader-mask">
        <div class="loader">
            <div></div>
            <div></div>
        </div>
    </div>
    
 
  <body class="vertical  light">
  <div class="wrapper">

<?php include ('./disc/partials/navbar.php'); ?>
<?php include ('./disc/partials/sidebar.php'); ?>

      <main role="main" class="main-content">
        
        <!--For Notification header naman ito-->
       <?php include('./disc/partials/modal-notif.php')?>

      <!--YOUR CONTENTHERE-->
      <div class="container-fluid">
        <div class="col-md-12">
           <div class="card">
            <div class="card-header">
              <h4 class="card-title">Pet Registration</h4>
            </div>
            <div class="card-body">
               <form action="">
                <div class="row">
                <div class="col-md mb-3">
                  <label for="" class="form-label">Owner Name</label>
                    <input type="text" name="owner_name" class="form-control" placeholder="Owner Name">
                </div>
                <div class="col-md mb-3">
                  <label for="" class="form-label">Owner Name</label>
                    <input type="text" name="owner_name" class="form-control" placeholder="Owner Name">
                </div>
                </div>
                <div class="row">
                <div class="col-md mb-3">
                  <label for="" class="form-label">Owner Name</label>
                    <input type="text" name="owner_name" class="form-control" placeholder="Owner Name">
                </div>
                <div class="col-md mb-3">
                  <label for="" class="form-label">Owner Name</label>
                    <input type="text" name="owner_name" class="form-control" placeholder="Owner Name">
                </div>
                </div>
                <div class="row">
                <div class="col-md mb-3">
                  <label for="" class="form-label">Owner Name</label>
                    <input type="text" name="owner_name" class="form-control" placeholder="Owner Name">
                </div>
                <div class="col-md mb-3">
                  <label for="" class="form-label">Owner Name</label>
                    <input type="text" name="owner_name" class="form-control" placeholder="Owner Name">
                </div>
                </div>
                <div class="row">
                <div class="col-md mb-3">
                  <label for="" class="form-label">Owner Name</label>
                  <input class="form-control" type="file" id="formFile" >
                </div>
                <div class="col-md mb-3">
                  <label for="" class="form-label">Owner Name</label>
                  <input class="form-control" type="file" id="formFile">
                </div>
                </div>
                <button type="submit" class="col-md-12 p-2 mt-3 btn btn-primary">Submit</button>   <div class="row">
                  
                 </div>
               </form>
            </div>
          </div>
        </div>
      </div>


</main>
</div>
  <!-- Include jQuery -->
  <?php include ('./script.php'); ?>
  </body>
</php>

   