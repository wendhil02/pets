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
            height: 80%;
            display: flex;
            flex-direction: column;
            cursor: pointer;
        }
        .card-img-top {
            margin: 20px;
            width: 80%;
            height: 150px;
            object-fit: contain;
            background: #f8f9fa;
        }
        .card-body {
            flex-grow: 1;
            flex-direction: column;
            justify-content: center;
            align-items: flex-start;
            text-align: left;
        }
        .card-text {
            font-size: 20px;
            margin-bottom: 0.25rem;
        }
        @media (max-width: 576px) {
            .card-img-top {
                height: 150px;
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
        </main>

        <!-- Modal for Pet Details with Adoption Button -->
        <div class="modal fade" id="petModal" tabindex="-1" aria-labelledby="petModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="petModalLabel">Pet Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">x</button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <!-- Image Column -->
                            <div class="col-md-4 text-center">
                                <img id="modalPetImage" src="" alt="Pet Image" class="img-fluid">
                            </div>
                            <!-- Text Information Column -->
                            <div class="col-md-8">
                                <div class="card-title"><strong>Information</strong></div>
                                <p><strong>Owner:</strong> <span id="modalOwner"></span></p>
                                <p><strong>Email:</strong> <span id="modalMail"></span></p>
                                <p><strong>Pet Name:</strong> <span id="modalPetName"></span></p>
                                <p><strong>Age:</strong> <span id="modalPetAge"></span></p>
                                <p><strong>Breed:</strong> <span id="modalPetBreed"></span></p>
                                <p><strong>Info:</strong> <span id="modalPetInfo"></span></p>
                                <div>
                                    <strong>Vaccine Record:</strong><br/>
                                    <img id="modalPetVaccine" src="" alt="Vaccine Record" class="img-fluid" style="max-width: 100%;">
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Modal Footer with Adoption Form (Centered) -->
                    <div class="modal-footer justify-content-center">
                        <form id="adoptForm" action="" method="post" enctype="multipart/form-data">
                            <input type="hidden" name="petId" id="formPetId">
                            <input type="hidden" name="owner" id="formOwner">
                            <input type="hidden" name="petName" id="formPetName">
                            <input type="hidden" name="petAge" id="formPetAge">
                            <input type="hidden" name="petBreed" id="formPetBreed">
                            <input type="hidden" name="petInfo" id="formPetInfo">
                            <input type="hidden" name="mail" id="formMail">
                            <input type="hidden" name="petImage" id="formPetImage">
                            <input type="hidden" name="petVaccine" id="formPetVaccine">
                            <button type="submit" class="btn btn-primary">Adopt</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Error Modal (for duplicate or other errors) -->
        <div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="errorModalLabel">Error</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">x</button>
                    </div>
                    <div class="modal-body">
                        <?php echo htmlspecialchars($errorMessage); ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Success Modal (if needed) -->
        <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="successModalLabel">Success</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">x</button>
                    </div>
                    <div class="modal-body">
                        <?php echo htmlspecialchars($successMessage); ?>
                    </div>
                </div>
            </div>
        </div>

        <?php include('./script.php'); ?>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <script>
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
