<?php
include('./dbconn/config.php');
include('./dbconn/authentication.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include('./disc/partials/header.php'); ?>
    <!-- Bootstrap CSS (if not already included in your header partial) -->
    <style>
        /* Ensure every card stretches to fill its column height */
        .card {
            height: 100%;
            display: flex;
            flex-direction: column;
            cursor: pointer; /* Indicate that the card is clickable */
        }
        .card-img-top {
            width: 100%;
            height: 200px;
            object-fit: contain;
            background: #f8f9fa;
        }
        .card-body {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: flex-start;
            text-align: left;
        }
        .card-title {
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
        .card-text {
            font-size: 14px;
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
                             data-mail="<?php echo htmlspecialchars($row['email']); ?>"  
                             data-image="<?php echo htmlspecialchars($row['pet_image']); ?>">
                            <img src="<?php echo htmlspecialchars($row['pet_image']); ?>" class="card-img-top" alt="Pet Image">
                            <div class="card-body">
                                <h5 class="card-title">PET INFORMATION</h5>
                                <p class="card-text"><strong>Name:</strong> <?php echo htmlspecialchars($row['pet']); ?></p>
                                <p class="card-text"><strong>Age:</strong> <?php echo htmlspecialchars($row['age']); ?></p>
                                <p class="card-text"><strong>Breed:</strong> <?php echo htmlspecialchars($row['breed']); ?></p>
                                <p class="card-text"><strong>Info:</strong> <?php echo htmlspecialchars($row['info']); ?></p>
                                <p class="card-text"><strong>Info:</strong> <?php echo htmlspecialchars($row['email']); ?></p>
                            </div>
                        </div>
                    </div>
                    <?php
                        }
                    } else {
                        echo "<p>No register found.</p>";
                    }
                    $conn->close();
                    ?>
                </div>
            </div>
        </main>

        <!-- Modal for Pet Details with Adoption Button -->
        <div class="modal fade" id="petModal" tabindex="-1" aria-labelledby="petModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="petModalLabel">Pet Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">x</button>
                    </div>
                    <div class="modal-body">
                        <!-- Pet image -->
                        <img id="modalPetImage" src="" alt="Pet Image" class="img-fluid mb-3">
                        <!-- Pet details -->
                        <h5 id="modalPetName"></h5>
                        <p><strong>Age:</strong> <span id="modalPetAge"></span></p>
                        <p><strong>Breed:</strong> <span id="modalPetBreed"></span></p>
                        <p><strong>Info:</strong> <span id="modalPetInfo"></span></p>
                        <p><strong>Owner email:</strong> <span id="modalMail"></span></p>
                    </div>
                    <!-- Modal Footer with Adoption Form -->
                    <div class="modal-footer">
                        <form id="adoptForm" action="adoption-process.php" method="post">
                            <!-- Hidden inputs to transfer pet data -->
                            <input type="hidden" name="petId" id="formPetId" value="">
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

        <?php include('./script.php'); ?>
        <!-- Bootstrap JS Bundle (if not already included) -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

        <!-- JavaScript to handle card clicks, modal population, and form data assignment -->
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                // Initialize the modal (Bootstrap 5)
                var petModal = new bootstrap.Modal(document.getElementById('petModal'));

                // Attach click event to each card
                document.querySelectorAll('.card').forEach(function(card) {
                    card.addEventListener('click', function() {
                        // Retrieve data attributes from the clicked card
                        var petId = card.getAttribute('data-id');
                        var petName = card.getAttribute('data-pet');
                        var petAge = card.getAttribute('data-age');
                        var petBreed = card.getAttribute('data-breed');
                        var petInfo = card.getAttribute('data-info');
                        var mail = card.getAttribute('data-mail');
                        var petImage = card.getAttribute('data-image');

                        // Update modal content
                        document.getElementById('modalPetImage').src = petImage;
                        document.getElementById('modalPetName').textContent = petName;
                        document.getElementById('modalPetAge').textContent = petAge;
                        document.getElementById('modalPetBreed').textContent = petBreed;
                        document.getElementById('modalPetInfo').textContent = petInfo;
                        document.getElementById('modalMail').textContent = mail;

                        // Assign values to the hidden fields in the adoption form
                        document.getElementById('formPetId').value = petId;
                        document.getElementById('formPetName').value = petName;
                        document.getElementById('formPetAge').value = petAge;
                        document.getElementById('formPetBreed').value = petBreed;
                        document.getElementById('formPetInfo').value = petInfo;
                        document.getElementById('formMail').value = mail;
                        document.getElementById('formPetImage').value = petImage;

                        // Show the modal
                        petModal.show();
                    });
                });
            });
        </script>
    </div>
</body>
</html>
