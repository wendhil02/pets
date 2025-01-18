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
    
    if (empty($_POST['phonee'])) {
      $error['phonee'] = 'Phone Number is required';
  } else {
      $phone = htmlspecialchars($_POST['phonee']);
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
        $sql = "INSERT INTO register (owner,phone, email, phonee, pet, age, breed, address, pet_image, pet_vaccine, additional_info) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?,?,?,?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssssssss", $ownerName,$phone, $email, $petName, $petAge, $petBreed, $address, $petImagePath, $vaccineRecordPath, $additionalInfo);

        if ($stmt->execute()) {
          // Generate QR code after successful insertion
          $registrationID = $stmt->insert_id; // get the last inserted ID (assuming it's auto-incremented)
          
          // URL for the pet's profile (make sure the URL is accessible)
          $profileUrl = "localhost/userside/user/Pet_profiling.phpid=" . $registrationID; // Example URL with registration ID

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
          <br>
           <div class="card">
            <div class="card-header justify-content-center">
              <h4 class="card-title">Pet Registration</h4>
            </div>
            <div class="card-body">
               <form action="" method="POST" enctype="multipart/form-data">
                <div class="row">
                <div class="col-md mb-3">
                  <label for="" class="form-label">Owner Name</label>
                    <input  class="form-control" name="owner_name" type="text" value="<?php echo isset($_POST['owner_name']) ? htmlspecialchars($_POST['owner_name']) : ''; ?>">
                    <?php if (isset($error['owner_name']))  echo "<span style='color:red;'>" . $error['owner_name'] . "</span>"; ?>
                </div>
                <div class="col-md mb-3">
                  <label for="" class="form-label">Phone No.</label>
                    <input  class="form-control" name="phonee" type="tel" value="<?php echo isset($_POST['phonee']) ? htmlspecialchars($_POST['phonee']) : ''; ?>">
                    <?php if (isset($error['phonee']))  echo "<span style='color:red;'>" . $error['phonee'] . "</span>"; ?>
                </div>
                </div>
                <div class="row">
                <div class="col-md mb-3">
                  <label for="" class="form-label">Email</label>
                    <input  class="form-control"  name="email" type="text" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                    <?php if (isset($error['email'])) echo "<span style='color:red;'>" . $error['email'] . "</span>"; ?>
                </div>
                <div class="col-md mb-3">
                  <label for="" class="form-label">Address</label>
                    <input type="text"name="address" class="form-control" value="<?php echo isset($_POST['address']) ? htmlspecialchars($_POST['address']) : ''; ?>">
                    <?php if (isset($error['address']))  echo "<span style='color:red;'>" . $error['address'] . "</span>"; ?>
                </div>
                </div>
                <div class="row">
                <div class="col-md mb-3">
                  <label for="" class="form-label">Pet Name</label>
                    <input class="form-control" name="pet_name" type="text" value="<?php echo isset($_POST['pet_name']) ? htmlspecialchars($_POST['pet_name']) : ''; ?>">
                    <?php if (isset($error['pet_name']))  echo "<span style='color:red;'>" . $error['pet_name'] . "</span>"; ?>
                </div>
                <div class="col-md mb-3">
                  <label for="" class="form-label">Pet Age</label>
                    <input  class="form-control" name="pet_age" type="number" value="<?php echo isset($_POST['pet_age']) ? htmlspecialchars($_POST['pet_age']) : ''; ?>">
                    <?php if (isset($error['pet_age']))  echo "<span style='color:red;'>" . $error['pet_age'] . "</span>"; ?>
                </div>
                </div>
                <div class="row">
                <div class="col-md mb-3">
                  <label for="" class="form-label">Pet Breed</label>
                    <input class="form-control" name="pet_breed" type="text" value="<?php echo isset($_POST['pet_breed']) ? htmlspecialchars($_POST['pet_breed']) : ''; ?>">
                    <?php if (isset($error['pet_breed'])) echo "<span style='color:red;'>" . $error['pet_breed'] . "</span>"; ?>
                </div>
                <div class="col-md mb-3">
                  <label for="" class="form-label">Additional Information</label>
                  <textarea name="additional_info" id="" class="form-control"><?php echo isset($_POST['additional_info']) ? htmlspecialchars($_POST['additional_info']) : ''; ?></textarea>
                    <?php if (isset($error['additional_info'])) echo "<span style='color:red;'>" . $error['additional_info'] . "</span>"; ?>
                </div>
                </div>
                <div class="row">
                <div class="col-md mb-3">
                  <label for="" class="form-label">Pet Image</label>
                  <input class="form-control" name="pet_image" type="file" >
                   <?php if (isset($error['pet_image']))  echo "<span style='color:red;'>" . $error['pet_image'] . "</span>"; ?>
                </div>
                <div class="col-md mb-3">
                  <label for="" class="form-label">Vaccination Record</label>
                  <input class="form-control"name=" vaccine_record" type="file">
                  <?php if (isset($error['vaccine_record']))  echo "<span style='color:red;'>". $error['vaccine_record'] . "</span>"; ?>
                </div>
                </div>
                <button type="button" class="col-md-12 p-2 mt-3 btn btn-primary" data-bs-toggle="modal" data-bs-target="#qrCodeModal">Submit</button>
               </form>
            </div>
          </div>
        </div>
      </div>
</main>


<div class="modal " tabindex="-1" id="qrCodeModal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title mt-1">Registration Successful!</h5>
        <button type="button" class="btn-close" style="border-radius:28px;" data-bs-dismiss="modal" aria-label="Close">x</button>
      </div>
      <div class="modal-body">
      <p>Scan or download the QR Code below:</p>
        <div class="">
        <img id="qrCodeImage" src="<?php echo $qrCodeFile; ?>" alt="QR Code" class="mx-auto my-4 w-48 h-48"> 
        </div>
      </div>
      <div class="modal-footer">
        <a href="<?php echo $qrCodeFile; ?>" download class="btn p-2 btn-primary">Download</a>

        <a href="userside/user/Pet_profiling.php?id=<?php echo $registrationID; ?>" target='_blank' class="p-2 btn btn-primary" data-bs-dismiss="modal">View Profile</a>
        
        <button type="button" class="btn btn-secondary p-2" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
</div>
  <!-- Include jQuery -->
  <?php include ('./script.php'); ?>
  </body>
</php>

   