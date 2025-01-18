<?php
include('../dbconn/config.php');
include('../dbconn/authentication.php');
checkAccess('user');
include('../phpqrcode/qrlib.php');


if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $error = array();
    $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
    $targetDir = "../stored/pet_image/";
    $vaccineDir = "../stored/vaccine_record/";

    // Validate inputs
    if (empty($_POST['owner_name'])) {
        $error['owner_name'] = 'Owner Name is required.';
    } else {
        $ownerName = htmlspecialchars($_POST['owner_name']);
    }

    if (empty($_POST['phone'])) {
        $error['phone'] = 'Phone Number is required';
    } else {
        $phone = htmlspecialchars($_POST['phone']); // Corrected variable assignment
    }
    
    if (empty($_POST['email'])) {
        $error['email'] = 'Email is required';
    } else {
        $email = htmlspecialchars($_POST['email']); // Corrected variable assignment
    }

    if (empty($_POST['address'])) {
        $error['address'] = 'Address is required';
    } else {
        $address = htmlspecialchars($_POST['address']);
    }

    if (empty($_POST['petType'])) {
        $error['petType'] = 'Type of Pet is required';
    } else {
        $petType = htmlspecialchars($_POST['petType']);
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
        $sql = "INSERT INTO register (owner, phone, email, address, petType, pet, age, breed, pet_image, pet_vaccine, additional_info) 
                VALUES (?, ?, ?, ?, ?, ?, ?,?,?,?,?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssssssss", $ownerName, $phone, $email, $address, $petType, $petName, $petAge, $petBreed, $petImagePath, $vaccineRecordPath, $additionalInfo);

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
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="icon" href="../assets/images/unified-lgu-logo.png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.6.0/css/fontawesome.min.css">
    <link rel ="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <title>Systems</title>

    <!-- Simple bar CSS (for scvrollbar)-->
    <link rel="stylesheet" href="../css/simplebar.css">
    <!-- Fonts CSS -->
    <link href="https://fonts.googleapis.com/css2?family=Overpass:ital,wght@0,100;0,200;0,300;0,400;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
    <!-- Icons CSS -->
    <link rel="stylesheet" href="../css/feather.css">
    <!-- App CSS -->
    <link rel="stylesheet" href="../css/main.css">   
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <style>
    .avatar-initials {
    width: 165px;
    height: 165px;
    border-radius: 50%;
    display: flex;
    margin-left: 8px;
    justify-content: center;
    align-items: center;
    font-size: 50px;
    font-weight: bold;
    color: #fff;
    
    }

    .avatar-initials-min {
    width: 40px;
    height: 40px;
    background: #75e6da;
    border-radius: 50%;
    display: flex;
    margin-left: 8px;
    justify-content: center;
    align-items: center;
    font-size: 14px;
    font-weight: bold;
    color: #fff;
    
  }

    .upload-icon {
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  cursor: pointer;
  font-size: 24px;
  color: #fff;
  opacity: 0;
  transition: opacity 0.3s ease-in-out;
  background-color: #333;
  padding: 10px;
  border-radius: 50%;
  z-index: 1;
}

.avatar-img:hover .upload-icon {
  opacity: 1;
}

.avatar-img {
  position: relative;
  transition: background-color 0.3s ease-in-out;
}

.avatar-img:hover {
  background-color: #a0f0e6;
}

</style>
  
  </head>

    
    <div class="loader-mask">
        <div class="loader">
            <div></div>
            <div></div>
        </div>
    </div>
    
 
  <body class="vertical  light">
    <div class="wrapper">
      <nav class="topnav navbar navbar-light">
        <button type="button" class="navbar-toggler text-muted mt-2 p-0 mr-3 collapseSidebar">
          <i class="fe fe-menu navbar-toggler-icon"></i>
        </button>


        <ul class="nav">
    
          
          <li class="nav-item">
            <section class="nav-link text-muted my-2 circle-icon" href="#" data-toggle="modal" data-target=".modal-shortcut">
              <span class="fe fe-message-circle fe-16"></span>
            </section>
          </li>


          <li class="nav-item nav-notif">
  <section class="nav-link text-muted my-2 circle-icon" href="#" data-toggle="modal" data-target=".modal-notif">
    <span class="fe fe-bell fe-16"></span>
   
      <span id="notification-count" style="
        position: absolute; 
        top: 12px; right: 5px; 
        font-size:13px; color: white;
        background-color: red;
        width:8px;
        height: 8px;
        display: flex;
        justify-content: center;
        align-items: center;
        border-radius: 50px;
      ">
      
  </section>
</li>

          <li class="nav-item dropdown">
            <span class="nav-link text-muted pr-0 avatar-icon" href="#" id="navbarDropdownMenuLink" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <span class="avatar avatar-sm mt-2">
  <div class="avatar-img rounded-circle avatar-initials-min text-center position-relative">
  

  </div>
</span>
</span>
            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownMenuLink">
              <a class="dropdown-item" href="#"><i class="fe fe-user"></i>&nbsp;&nbsp;&nbsp;Profile</a>
              <a class="dropdown-item" href="#"><i class="fe fe-settings"></i>&nbsp;&nbsp;&nbsp;Settings</a>
              <a class="dropdown-log-out" href="#"><i class="fe fe-log-out"></i>&nbsp;&nbsp;&nbsp;Log Out</a>
            </div>    
          </li>
        </ul>
      </nav>


      <aside class="sidebar-left border-right bg-white " id="leftSidebar" data-simplebar>
        <a href="#" class="btn collapseSidebar toggle-btn d-lg-none text-muted ml-2 mt-3" data-toggle="toggle">
          <i class="fe fe-x"><span class="sr-only"></span></i>
        </a>

        <nav class="vertnav navbar-side navbar-light">
          <!-- nav bar -->
          <div class=" m-4w-100 mb-4 d-flex">
            <a class=" navbar-brand mx-auto mt-2 flex-fill text-center" href="#">
              
                
                <img src="../assets/images/unified-lgu-logo.png" width="45">
              

            <div class="brand-title">
            <br>
              <span>LGU3 - TEMPLATE</span>
            </div>
                       
            </a>

          </div>

          <!--Sidebar ito-->

          <ul class="navbar-nav active flex-fill w-100 mb-2">
            <li class="nav-item dropdown">
            <a class="nav-link" href="dashboard.php">
              <i class="fas fa-chart-line"></i>
                <span class="ml-3 item-text">Dashboard</span>

              </a>
            </li>
          </ul>
         <p class="text-muted-nav nav-heading mt-4 mb-1">
          <span style="font-size: 10.5px; font-weight: bold; font-family: 'Inter', sans-serif;">REGISTRATION MANAGEMENT</span>
          </p> 
          <ul class="navbar-nav flex-fill w-100 mb-2">

          <ul class="navbar-nav flex-fill w-100 mb-2">
            <li class="nav-item w-100">
              <a class="nav-link" href="register.php
            ">
              <i class="fa-solid fa-wrench"></i>
                <span class="ml-3 item-text">Pet Register</span>
              </a>
            </li>
          </ul>

          <ul class="navbar-nav flex-fill w-100 mb-2">
            <li class="nav-item w-100">
              <a class="nav-link" href="mypet.php
            ">
              <i class="fa-solid fa-wrench"></i>
                <span class="ml-3 item-text">Pet Registered</span>
              </a>
            </li>
          </ul>


          
          <p class="text-muted-nav nav-heading mt-4 mb-1">
            <span style="font-size: 10.5px; font-weight: bold; font-family: 'Inter', sans-serif;">ADOPTION MANAGEMENT</span>
            </p>
        
          <ul class="navbar-nav flex-fill w-100 mb-2">
            <li class="nav-item w-100">
            <a class="nav-link" href="adoption.php
          ">
            <i class="fa-solid fa-wrench"></i>
                <span class="ml-3 item-text">Pet adoption</span>
              </a>
            </li>
          </ul>

          <ul class="navbar-nav flex-fill w-100 mb-2">
            <li class="nav-item w-100">
            <a class="nav-link" href="pet_view.php
          ">
            <i class="fa-solid fa-wrench"></i>
                <span class="ml-3 item-text">Open adoption</span>
              </a>
            </li>
          </ul>

          <ul class="navbar-nav flex-fill w-100 mb-2">
            <li class="nav-item w-100">
            <a class="nav-link" href="pet_view.php
          ">
            <i class="fa-solid fa-wrench"></i>
                <span class="ml-3 item-text">List adoption</span>
              </a>
            </li>
          </ul>

          <p class="text-muted-nav nav-heading mt-4 mb-1">
          <span style="font-size: 10.5px; font-weight: bold; font-family: 'Inter', sans-serif;">REPORT MANAGEMENT</span>
          </p>

          <ul class="navbar-nav flex-fill w-100 mb-2">
            <li class="nav-item w-100">
              <a class="nav-link" href="missing.php
            ">
              <i class="fa-solid fa-wrench"></i>
                <span class="ml-3 item-text">Missing animal </span>
              </a>
            </li>
          </ul>

          <ul class="navbar-nav flex-fill w-100 mb-2">
            <li class="nav-item w-100">
            <a class="nav-link" href="cruelty.php
          "> 
            <i class="fa-solid fa-wrench"></i>
                <span class="ml-3 item-text">Animal cruelty </span>
              </a>
            </li>
          </ul>

          <p class="text-muted-nav nav-heading mt-4 mb-1">
          <span style="font-size: 10.5px; font-weight: bold; font-family: 'Inter', sans-serif;">SETTINGS</span>
          </p>

          <ul class="navbar-nav flex-fill w-100 mb-2">
            <li class="nav-item w-100">
            <a class="nav-link" href="settings.php">
              <i class="fa-solid fa-screwdriver-wrench"></i>
                <span class="ml-3 item-text">Settings</span>
              </a>
            </li>
          </ul>
  
      
        </nav>
      </aside>
      <main role="main" class="main-content">
        
        <!--For Notification header naman ito-->

        <div class="modal fade modal-notif modal-slide" tabindex="-1" role="dialog" aria-labelledby="defaultModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="defaultModalLabel">Notifications</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>


              <div class="modal-body">
  <div class="list-group list-group-flush my-n3">
   
      <div class="col-12 mb-4">
        <div class="alert alert-success alert-dismissible fade show" role="alert" id="notification">
          <img class="fade show" src="../assets/images/unified-lgu-logo.png" width="35" height="35">
          <strong style="font-size:12px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"></strong> 
          <button type="button" class="close" data-dismiss="alert" aria-label="Close" onclick="removeNotification()">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
      </div> <!-- /. col -->

    <div id="no-notifications" style="display: none; text-align:center; margin-top:10px;">No notifications</div>
  </div> <!-- / .list-group -->
 
</div>

<div class="modal-footer">
<button type="button" class="btn btn-secondary btn-block" onclick="clearAllNotifications()">Clear All</button>
              </div>
            </div>
          </div>
        </div>



      <!--YOUR CONTENTHERE-->
      <div class="col-sm-12">
        <div class="card">
          <div class="">
            <h4 class="card-title mt-5">PET REGISTRATION FORM</h4>
          </div>
          <div class="card-body">
          <form class="row g-3" enctype="multipart/form-data" method="POST" >
  <div class="col-md-6 mt-3">
    <label for="owner_name" class="form-label">Owner Name</label>
    <input type="text" class="form-control" name="owner_name"   value="<?php echo isset($_POST['owner_name']) ? htmlspecialchars($_POST['owner_name']) : ''; ?>" >
    <?php if (isset($error['owner_name'])) echo "<span style='color:red;'>" . $error['owner_name'] . "</span>";?>
  </div>
 
  <div class="col-md-6 mt-3">
    <label for="inputPassword4" class="form-label">Phone Number</label>
    <input type="tel" class="form-control" name="phone" value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>">
    <?php if (isset($error['phone'])) echo "<span style='color:red;'>" . $error['phone'] . "</span>"; ?>
  </div>
 
  <div class="col-md-6 mt-3">
  <label for="inputPassword4" class="form-label">Email</label>
  <input type="email" class="form-control" name="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
  <?php if (isset($error['email'])) echo "<span style='color:red;'>" . $error['email'] . "</span>"; ?>
  </div>
 
  <div class="col-md-6 mt-3">
    <label for="inputAddress" class="form-label">Address</label>
    <input type="text" class="form-control" name="address" value="<?php echo isset($_POST['address']) ? htmlspecialchars($_POST['address']) : ''; ?>">
    <?php if (isset($error['address'])) echo "<span style='color:red;'>" . $error['address'] . "</span>"; ?>
  </div>
  
  <div class="col-md-6 mt-3">
    <label for="" class="form-label">Type of Pet</label>
    <select name="petType" class="form-control">
      <option selected>Choose...</option>
      <option value="Dog" <?php echo isset($_POST['petType']) && $_POST['petType'] == 'Dog' ? 'selected' : ''; ?>>Dog</option>
      <option value="Cat" <?php echo isset($_POST['petType']) && $_POST['petType'] == 'Cat' ? 'selected' : ''; ?>>Cat</option>
      <option value="Other" <?php echo isset($_POST['petType']) && $_POST['petType'] == 'Other' ? 'selected' : ''; ?>>Others</option>
</select>
<?php if (isset($error['petType']))echo "<span style='color:red;'>" . $error['petType'] . "</span>"; ?>
  </div>

  <div class="col-md-6 mt-3">
    <label for="inputAddress2" class="form-label">Pet Name</label>
    <input type="text" class="form-control" name="pet_name" type="text" value="<?php echo isset($_POST['pet_name']) ? htmlspecialchars($_POST['pet_name']) : ''; ?>">
    <?php if (isset($error['pet_name'])) echo "<span style='color:red;'>" . $error['pet_name'] . "</span>"; ?>
  </div>

  <div class="col-md-6 mt-3">
    <label for="pet_ace" class="form-label">Pet Age</label>
    <input type="number" name="pet_age" class="form-control" value="<?php echo isset($_POST['pet_age']) ? htmlspecialchars($_POST['pet_age']) : ''; ?>">
    <?php if (isset($error['pet_age'])) echo "<span style='color:red;'>" . $error['pet_age'] . "</span>"; ?>
  </div>
  
  <div class="col-md-6 mt-3">
    <label for="inputAddress2" class="form-label">Breed</label>
    <input type="text" name="pet_breed" class="form-control" value="<?php echo isset($_POST['pet_breed']) ? htmlspecialchars($_POST['pet_breed']) : ''; ?>">
    <?php if (isset($error['pet_breed'])) echo "<span style='color:red;'>" . $error['pet_breed'] . "</span>"; ?>
  </div>


  <div class="col-md-6 mt-3">
    <label for="inputAddress2" class="form-label">Pet image </label>
    <input type="file" class="form-control" name="pet_image" type="file">
    <?php if (isset($error['pet_image'])) echo "<span style='color:red;'>" . $error['pet_image'] . "</span>"; ?>
  </div>

  <div class="col-md-6 mt-3">
    <label for="inputAddress2" class="form-label">Vaccine Record</label>
    <input type="file" class="form-control" name="vaccine_record" type="file">
    <?php if (isset($error['vaccine_record'])) echo "<span style='color:red;'>" . $error['vaccine_record'] . "</span>"; ?>
  </div>
  
  <div class="form-floating col-md-12 mt-3">
  <label for="floatingTextarea">Additional Information</label>
  <textarea class="form-control" name="additional_info"><?php echo isset($_POST['additional_info']) ? htmlspecialchars($_POST['additional_info']) : ''; ?></textarea>
  <?php if (isset($error['additional_info'])) echo "<span style='color:red;'>" . $error['additional_info'] . "</span>"; ?>
  </div>
</div>
  
<button type="button" class="btn btn-primary my-4" data-bs-toggle="modal" data-bs-target="#qrCodeModal" >Submit Form</button>
</form>  
</div>
</div>
</div>

<div id="qrCodeModal" class="modal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <div class="modal-title">
          <h2 class="text-xl font-bold mb-4">Registration Successful!</h2>
          <p>Scan or download the QR Code below:</p>
        </div>
      </div>
      <div class="modal-body">
        <div>
          <?php if (is_file($qrCodeFile)) : ?>
            <img id="qrCodeImage" src="<?php echo $qrCodeFile; ?>" alt="QR Code" class="mx-auto my-4 w-48 h-48">
          <?php else : ?>
            <p class="text-red-500">QR Code image not found.</p>
          <?php endif; ?>
        </div>
      </div>
      <div class="modal-footer">
        <a href="<?php echo $qrCodeFile; ?>" download class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
          Download QR Code
        </a>
        <a href="../Pet_profiling.php?id=<?php echo $registrationID; ?>" target="_blank" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
          View Profile
        </a>
        <button type="button" data-bs-dismiss="modal" class="btn-close bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
          Close
        </button>
      </div>
    </div>
  </div>
</div>


  <!-- Include jQuery -->
  <?php include ('./script.php'); ?>
  </body>
</php>

   