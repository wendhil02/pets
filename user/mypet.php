<?php
include('./dbconn/config.php');
include('./dbconn/authentication.php');

$errorMessage = "";
$successMessage = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['petId'])) {
    // Sanitize inputs
    $petId    = (int) $_POST['petId'];
    $petName  = trim($_POST['petName']);
    $owner    = trim($_POST['owner']);
    $petAge   = (int) $_POST['petAge'];
    $petBreed = trim($_POST['petBreed']);
    $petInfo  = trim($_POST['petInfo']);
    $mail     = filter_var(trim($_POST['mail']), FILTER_SANITIZE_EMAIL);

    // Handle Pet Image Upload
    if (!empty($_FILES['petImage']['tmp_name'])) {
        $imageData = file_get_contents($_FILES['petImage']['tmp_name']);
        $petImage  = base64_encode($imageData);
    } elseif (!empty($_POST['petImage'])) {
        $petImage = $_POST['petImage'];
    } else {
        $petImage = "";
    }

    // Handle Vaccine Image Upload
    if (!empty($_FILES['petVaccine']['tmp_name'])) {
        $vaccineData = file_get_contents($_FILES['petVaccine']['tmp_name']);
        $petVaccine  = base64_encode($vaccineData);
    } elseif (!empty($_POST['petVaccine'])) {
        $petVaccine = $_POST['petVaccine'];
    } else {
        $petVaccine = "";
    }

    // Check for duplicates
    $checkQuery = "SELECT id FROM adoption WHERE pet_id = ?";
    if ($checkStmt = $conn->prepare($checkQuery)) {
        $checkStmt->bind_param("i", $petId);
        $checkStmt->execute();
        $checkStmt->store_result();

        if ($checkStmt->num_rows > 0) {
            $errorMessage = "This adoption record already exists.";
        }
        $checkStmt->close();
    } else {
        $errorMessage = "Error preparing duplicate check: " . $conn->error;
    }

    // If no error, proceed with insertion
    if (empty($errorMessage)) {
        $query = "INSERT INTO adoption (pet_id, owner, pet_name, pet_age, pet_breed, pet_info, email, pet_image, pet_vaccine, approved) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 0)";
        if ($stmt = $conn->prepare($query)) {
            $stmt->bind_param("ississsss", $petId, $owner, $petName, $petAge, $petBreed, $petInfo, $mail, $petImage, $petVaccine);

            if ($stmt->execute()) {
                $successMessage = "Your adoption request has been submitted successfully and is pending approval.";
            } else {
                $errorMessage = "Error executing statement: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $errorMessage = "Error preparing statement: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include('./disc/partials/header.php'); ?>
    <style>
       /* Card Styles */
.card {
    height: 100%;
    display: flex;
    flex-direction: column;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    cursor: pointer;
    background: #fff;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0px 6px 12px rgba(0, 0, 0, 0.15);
}

.card-img-top {
    width: 100%;
    height: 200px;
    object-fit: cover;
    background: #f8f9fa;
    border-bottom: 3px solid #ddd;
}

.card-body {
    flex-grow: 1;
    padding: 15px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    text-align: center;
}

.card-text {
    font-size: 18px;
    font-weight: 600;
    color: #333;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.card-text:hover {
    overflow: visible;
    white-space: normal;
}

@media (max-width: 576px) {
    .card-img-top {
        height: 180px;
    }
}

    </style>
</head>

<body class="vertical light">
    <!-- Loader Mask -->
    <div class="loader-mask">
        <div class="loader">
            <div></div>
            <div></div>
        </div>
    </div>

    <div class="wrapper">
        <?php include('./disc/partials/navbar.php'); ?>
        <?php include('./disc/partials/sidebar.php'); ?>

        <main role="main" class="main-content">
            <?php include('./disc/partials/modal-notif.php'); ?>
            <div class="container-fluid">
                <div class="row">
                    <?php
                    $sql = "SELECT * FROM register";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            // Prepare the pet image source
                            $imageSrc = !empty($row['pet_image']) 
                                ? 'data:image/jpeg;base64,' . htmlspecialchars($row['pet_image']) 
                                : 'default.jpg';
                            // Prepare the vaccine image source
                            $vaccineSrc = !empty($row['pet_vaccine']) 
                                ? 'data:image/jpeg;base64,' . htmlspecialchars($row['pet_vaccine']) 
                                : 'default_vaccine.jpg';
                            ?>
                            <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4">
                                <div class="card" 
                                     data-id="<?php echo $row['id']; ?>"
                                     data-pet="<?php echo htmlspecialchars($row['pet']); ?>"
                                     data-age="<?php echo htmlspecialchars($row['age']); ?>"
                                     data-breed="<?php echo htmlspecialchars($row['breed']); ?>"
                                     data-info="<?php echo htmlspecialchars($row['info']); ?>"
                                     data-owner="<?php echo htmlspecialchars($row['owner']); ?>"
                                     data-mail="<?php echo htmlspecialchars($row['email']); ?>"
                                     data-image="<?php echo $imageSrc; ?>"
                                     data-vaccine="<?php echo $vaccineSrc; ?>">
                                    <img src="<?php echo $imageSrc; ?>" class="card-img-top" alt="Pet Image">
                                    <div class="card-body">
                                        <p class="card-text"><strong><?php echo htmlspecialchars($row['pet']); ?></strong></p>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                    } else {
                        echo '<div class="col-12"><p class="text-center">No adoption listing available.</p></div>';
                    }
                    ?>
                </div>
            </div>

            
            <footer class=" text-dark py-4 border-top mt-3">
  <div class="container">
    <div class="row align-items-center text-center text-md-start">
      
      <!-- Social Media Icons -->
      <div class="col-12 col-md-4 mb-3 mb-md-0 d-flex justify-content-center justify-content-md-start">
        <a href="#" class="text-primary m-3 fs-4"><i class="fab fa-facebook"></i></a>
        <a href="#" class="text-danger m-3 fs-4"><i class="fab fa-instagram"></i></a>
        <a href="#" class="text-info m-3 fs-4"><i class="fab fa-twitter"></i></a>
      </div>

      <!-- Copyright Text -->
      <div class="col-12 col-md-4 text-center">
        <small>&copy; 2025 Barangay Pet Welfare. <i>All Rights Reserved.</i></small>
      </div>

      <!-- Links and Sponsor -->
      <div class="col-12 col-md-4 text-center text-md-end">
        <a href="#" class="text-primary fw-bold me-2">Terms of Use</a>
        <a href="#" class="text-primary fw-bold me-2">Privacy Policy</a>
        <a href="#" class="text-primary fw-bold">Sitemap</a>
        <p class="d-inline-block text-muted ms-2">
          Website sponsored by <a href="#" class="text-primary fw-bold">BPWS</a>
        </p>
      </div>

    </div>
  </div>
</footer>

        </main>

<!-- Updated Modal for Pet Details with Improved UI -->
<div class="modal fade" id="petModal" tabindex="-1" aria-labelledby="petModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold" id="petModalLabel">🐾 Pet Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close">X</button>
            </div>
            <div class="modal-body">
                <div class="row row-cols-1 row-cols-md-2 g-3">
                    <!-- Image Column -->
                    <div class="col text-center">
                        <img id="modalPetImage" src="" alt="Pet Image" class="img-fluid rounded border shadow-sm"
                             style="cursor: pointer;" onclick="openFullSize(this)">
                    </div>
                    <!-- Pet Details Column -->
                    <div class="col">
                        <h3 class="fw-bold text-primary" id="modalPetName"></h3><br>
                        <p class="mb-1"><strong>Owner:</strong> <span id="modalOwner" class="text-secondary"></span></p>
                        <p class="mb-1"><strong>Email:</strong> <span id="modalMail" class="text-secondary"></span></p>
                        <p class="mb-1"><strong>Age:</strong> <span id="modalPetAge" class="text-secondary"></span></p>
                        <p class="mb-1"><strong>Breed:</strong> <span id="modalPetBreed" class="text-secondary"></span></p>
                        <p><strong>Info:</strong> <span id="modalPetInfo" class="text-secondary"></span></p>
                    </div>
                </div>
                <!-- Vaccine Record -->
                <div class="mt-3 text-center">
                    <strong class="d-block">Vaccine Record:</strong>
                    <img id="modalPetVaccine" src="" alt="Vaccine Record" class="img-fluid rounded border shadow-sm"
                         style="max-width: 100%; cursor: pointer;" onclick="openFullSize(this)">
                </div>
            </div>
            <div class="modal-footer d-flex justify-content-center">
                <form id="adoptForm" action="" method="post">
                    <input type="hidden" name="petId" id="formPetId">
                    <input type="hidden" name="owner" id="formOwner">
                    <input type="hidden" name="petName" id="formPetName">
                    <input type="hidden" name="petAge" id="formPetAge">
                    <input type="hidden" name="petBreed" id="formPetBreed">
                    <input type="hidden" name="petInfo" id="formPetInfo">
                    <input type="hidden" name="mail" id="formMail">
                    <input type="hidden" name="petImage" id="formPetImage">
                    <input type="hidden" name="petVaccine" id="formPetVaccine">
                    <button type="submit" class="btn btn-success fw-bold px-4 py-2">🐶 Adopt This Pet</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Fullscreen Image Viewer -->
<div id="fullSizeImageViewer" class="d-none position-fixed top-0 start-0 w-100 h-100 bg-dark bg-opacity-75 d-flex align-items-center justify-content-center" style="z-index: 1050;">
    <img id="fullSizeImage" src="" alt="Full Size Image" class="img-fluid rounded shadow-lg">
    <button class="btn btn-danger position-absolute top-0 end-0 m-3 " onclick="closeFullSize()">X</button>
</div>


<!-- Error Modal (Compact & Styled) -->
<div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-sm">
            <div class="modal-header bg-white border-0 p-2">
            <h6 class="modal-title w-100 text-white fw-bold">
                    <i class="bi bi-check-circle-fill me-1"></i> 
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">X</button>
            </div>
            <div class="modal-body text-center">
                <p class="text-dark fw-semibold mb-2"><?php echo htmlspecialchars($errorMessage); ?></p>
            </div>
            <div class="modal-footer border-0 d-flex justify-content-center">
                <button type="button" class="btn btn-sm btn-success px-3" data-bs-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>        

<!-- Success Modal (Compact & Styled) -->
<div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-sm">
            <div class="modal-header bg-white border-0 p-2">
            <h6 class="modal-title w-100 text-white fw-bold">
                    <i class="bi bi-check-circle-fill me-1"></i>
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">X</button>
            </div>
            <div class="modal-body text-center">
                <p class="text-dark fw-semibold mb-2"><?php echo htmlspecialchars($successMessage); ?></p>
            </div>
            <div class="modal-footer border-0 d-flex justify-content-center">
                <button type="button" class="btn btn-sm btn-success px-3" data-bs-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>

        <?php include('./script.php'); ?>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <script>
              function openFullSize(img) {
        document.getElementById("fullSizeImage").src = img.src;
        document.getElementById("fullSizeImageViewer").classList.remove("d-none");
    }

    function closeFullSize() {
        document.getElementById("fullSizeImageViewer").classList.add("d-none");
    }
  

            document.addEventListener("DOMContentLoaded", function() {
                var petModal = new bootstrap.Modal(document.getElementById('petModal'));

                document.querySelectorAll('.card').forEach(function(card) {
                    card.addEventListener('click', function() {
                        var petId    = card.getAttribute('data-id');
                        var owner    = card.getAttribute('data-owner');
                        var petName  = card.getAttribute('data-pet');
                        var petAge   = card.getAttribute('data-age');
                        var petBreed = card.getAttribute('data-breed');
                        var petInfo  = card.getAttribute('data-info');
                        var mail     = card.getAttribute('data-mail');
                        var petImage = card.getAttribute('data-image');
                        var petVaccine = card.getAttribute('data-vaccine');

                        document.getElementById('modalPetImage').src = petImage;
                        document.getElementById('modalOwner').textContent    = owner;
                        document.getElementById('modalPetName').textContent  = petName;
                        document.getElementById('modalPetAge').textContent   = petAge;
                        document.getElementById('modalPetBreed').textContent = petBreed;
                        document.getElementById('modalPetInfo').textContent  = petInfo;
                        document.getElementById('modalMail').textContent     = mail;
                        document.getElementById('modalPetVaccine').src       = petVaccine;

                        document.getElementById('formPetId').value    = petId;
                        document.getElementById('formOwner').value    = owner;
                        document.getElementById('formPetName').value  = petName;
                        document.getElementById('formPetAge').value   = petAge;
                        document.getElementById('formPetBreed').value = petBreed;
                        document.getElementById('formPetInfo').value  = petInfo;
                        document.getElementById('formMail').value     = mail;
                        document.getElementById('formPetImage').value = petImage;
                        document.getElementById('formPetVaccine').value = petVaccine;

                        petModal.show();
                    });
                });

                // Show error modal if there's an error
                <?php if (!empty($errorMessage)) { ?>
                    var errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
                    errorModal.show();
                <?php } ?>

                // Show success modal if there's success
                <?php if (!empty($successMessage)) { ?>
                    var successModal = new bootstrap.Modal(document.getElementById('successModal'));
                    successModal.show();
                <?php } ?>
            });
        </script>
    </div>
</body>

</html>
