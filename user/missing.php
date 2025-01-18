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

    
    if (empty($_POST['petType'])) {
      $error['petType'] = 'Type of Pet is required';
  } else {
      $petType = htmlspecialchars($_POST['petType']);
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
            $targetDir = "../stored/imgInput/";

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
    <script src="https://cdn.tailwindcss.com"></script>

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
<?php include ('./disc/partials/navbar.php'); ?>
<?php include ('./disc/partials/sidebar.php'); ?>
</div>
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
            <h4 class="card-title mt-5">PET MISSING REPORT FORM</h4>
          </div>
          <div class="card-body">
          <form class="row g-3" enctype="multipart/form-data" method="POST" >
  <div class="col-md-6 mt-3">
    <label for="report_party" class="form-label">Report party</label>
    <input type="text" class="form-control" name="report_party"   value="<?php echo isset($_POST['report_party']) ? htmlspecialchars($_POST['report_party']) : ''; ?>" >
    <?php if (isset($error['report_party'])) echo "<span style='color:red;'>" . $error['report_party'] . "</span>";?>
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
    <label for="inputplace" class="form-label">Lost & found</label>
    <input type="text" class="form-control" name="place" value="<?php echo isset($_POST['place']) ? htmlspecialchars($_POST['place']) : ''; ?>">
    <?php if (isset($error['place'])) echo "<span style='color:red;'>" . $error['place'] . "</span>"; ?>
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
    <label for="inputplace2" class="form-label">Breed</label>
    <input type="text" name="pet_breed" class="form-control" value="<?php echo isset($_POST['pet_breed']) ? htmlspecialchars($_POST['pet_breed']) : ''; ?>">
    <?php if (isset($error['pet_breed'])) echo "<span style='color:red;'>" . $error['pet_breed'] . "</span>"; ?>
  </div>
  
  <div class="col-md-6 mt-3">
    <label for="inputplace2" class="form-label">Photo upload</label>
    <input type="file" class="form-control"name="imgInput" type="file">
    <?php if (isset($error['imgInput'])) echo "<span style='color:red;'>" . $error['imgInput'] . "</span>"; ?>
  </div>

  
  <div class="form-floating col-md-6 mt-3">
  <label for="floatingTextarea">Additional Information</label>
  <textarea class="form-control" name="description"><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea>
  <?php if (isset($error['description'])) echo "<span style='color:red;'>" . $error['description'] . "</span>"; ?>
  </div>
</div>
  
<div  data-id="" data-bs-toggle="modal" data-bs-target="#addUserModal" class=" col-12-md-4 justify-content-center align-items-center text-center">
               <a href="#" class=" btn btn-lg btn-primary">Submit</a> 
              </div>
</form>
          </div>
        </div>
      </div>
    

  <!-- Include jQuery -->
  <?php include ('./script.php'); ?>
  </body>
</php>

  