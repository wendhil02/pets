<?php
include('./dbconn/config.php');
include('./dbconn/authentication.php');

$errorMessage = "";
$successMessage = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['petId'])) {
    // Sanitize and validate inputs
    $petId    = (int) $_POST['petId'];
    $petName  = trim($_POST['petName']);
    $owner    = trim($_POST['owner']);
    $petAge   = (int) $_POST['petAge'];
    $petBreed = trim($_POST['petBreed']);
    $petInfo  = trim($_POST['petInfo']);
    $petImage = trim($_POST['petImage']);

    // Check for duplicates: if the same pet_id already exists
    $checkQuery = "SELECT id FROM adoption WHERE pet_id = ?";
    if ($checkStmt = $conn->prepare($checkQuery)) {
        $checkStmt->bind_param("i", $petId);
        $checkStmt->execute();
        $checkStmt->store_result();
        
        if ($checkStmt->num_rows > 0) {
            // Duplicate found—set an error message.
            $errorMessage = "This adoption record already exists.";
        }
        $checkStmt->close();
    } else {
        $errorMessage = "Error preparing duplicate check: " . $conn->error;
    }

    // If no error, proceed with insertion.
    if (empty($errorMessage)) {
        $query = "INSERT INTO adoption (pet_id, owner, pet_name, pet_age, pet_breed, pet_info, pet_image, approved) VALUES (?, ?, ?, ?, ?, ?, ?, 0)";
        if ($stmt = $conn->prepare($query)) {
            // Correct binding string: "ississs"
            $stmt->bind_param("ississs", $petId, $owner, $petName, $petAge, $petBreed, $petInfo, $petImage);
            
            if ($stmt->execute()) {
                $successMessage = "Your adoption has been submitted successfully and is pending approval. You will be redirected shortly.";
                // Optionally, you could redirect the user here.
                // header("Location: mypet.php");
                // exit;
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
    <!-- Loader Mask moved inside body -->
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
                    // Fetch data from the "register" table
                    $sql = "SELECT * FROM register";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
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
                             data-image="<?php echo htmlspecialchars($row['pet_image']); ?>">
                            <img src="<?php echo htmlspecialchars($row['pet_image']); ?>" class="card-img-top" alt="Pet Image">
                            <div class="card-body">
                                <p class="card-text"><strong><?php echo htmlspecialchars($row['pet']); ?></strong></p>
                            </div>
                        </div>
                    </div>
                    <?php
                        }
                    } else {
                        // Centered text when no records are found
                        echo '<div class="col-12"><p class="text-center">No adoption listing available.</p></div>';
                    }
                    $conn->close();
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
                            </div>
                        </div>
                    </div>
                    <!-- Modal Footer with Adoption Form -->
                    <div class="modal-footer">
                        <form id="adoptForm" action="" method="post">
                            <!-- Hidden inputs to transfer pet data -->
                            <input type="hidden" name="petId" id="formPetId" value="">
                            <input type="hidden" name="owner" id="formOwner" value="">
                            <input type="hidden" name="petName" id="formPetName" value="">
                            <input type="hidden" name="petAge" id="formPetAge" value="">
                            <input type="hidden" name="petBreed" id="formPetBreed" value="">
                            <input type="hidden" name="petInfo" id="formPetInfo" value="">
                            <input type="hidden" name="mail" id="formMail" value="">
                            <input type="hidden" name="petImage" id="formPetImage" value="">
                            <button type="submit" class="btn btn-primary">Adoption</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
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
                // Initialize the pet modal (for adoption details)
                var petModal = new bootstrap.Modal(document.getElementById('petModal'));

                // Attach click event to each pet card
                document.querySelectorAll('.card').forEach(function(card) {
                    card.addEventListener('click', function() {
                        // Retrieve data attributes from the clicked card
                        var petId = card.getAttribute('data-id');
                        var owner = card.getAttribute('data-owner');
                        var petName = card.getAttribute('data-pet');
                        var petAge = card.getAttribute('data-age');
                        var petBreed = card.getAttribute('data-breed');
                        var petInfo = card.getAttribute('data-info');
                        var mail = card.getAttribute('data-mail');
                        var petImage = card.getAttribute('data-image');

                        // Update modal content
                        document.getElementById('modalPetImage').src = petImage;
                        document.getElementById('modalOwner').textContent = owner;
                        document.getElementById('modalPetName').textContent = petName;
                        document.getElementById('modalPetAge').textContent = petAge;
                        document.getElementById('modalPetBreed').textContent = petBreed;
                        document.getElementById('modalPetInfo').textContent = petInfo;
                        document.getElementById('modalMail').textContent = mail;
                      
                        // Assign values to hidden fields in the adoption form
                        document.getElementById('formPetId').value = petId;
                        document.getElementById('formOwner').value = owner;
                        document.getElementById('formPetName').value = petName;
                        document.getElementById('formPetAge').value = petAge;
                        document.getElementById('formPetBreed').value = petBreed;
                        document.getElementById('formPetInfo').value = petInfo;
                        document.getElementById('formMail').value = mail;
                        document.getElementById('formPetImage').value = petImage;

                        // Show the pet details modal
                        petModal.show();
                    });
                });

                // If there's an error message, show the error modal.
                <?php if (!empty($errorMessage)) { ?>
                    var errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
                    errorModal.show();
                <?php } ?>

                // If there's a success message, show the success modal.
                <?php if (!empty($successMessage)) { ?>
                    var successModal = new bootstrap.Modal(document.getElementById('successModal'));
                    successModal.show();
                <?php } ?>
            });
        </script>
    </div>
</body>
</html>
