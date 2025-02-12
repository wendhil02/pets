<?php
include('./dbconn/config.php');
include('./dbconn/authentication.php');

// Check if a deletion has been requested
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete' && isset($_POST['pet_id'])) {
    $pet_id = intval($_POST['pet_id']);

    // Prepare the statement to safely delete the pet listing
    $stmt = $conn->prepare("DELETE FROM adoption WHERE id = ?");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("i", $pet_id);
    
    if ($stmt->execute()) {
        $message = "Pet listing deleted successfully";
    } else {
        $error = "Failed to delete pet listing";
    }

    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include('./disc/partials/header.php'); ?>
    <!-- Bootstrap CSS and Custom Styles -->
    <style>
        .card {
            height: 100%;
            display: flex;
            flex-direction: column;
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
        .card-footer {
            background: transparent;
            border-top: none;
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
                <!-- Display success/error messages if available -->
                <?php if (isset($message)) : ?>
                    <div class="alert alert-success" role="alert"><?php echo htmlspecialchars($message); ?></div>
                <?php elseif (isset($error)) : ?>
                    <div class="alert alert-danger" role="alert"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <div class="row">
                    <?php
                    // Fetch data from the "adoption" table
                    $sql = "SELECT * FROM adoption";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                    ?>
                    <!-- Card for each pet -->
                    <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4">
                        <div class="card">
                            <img src="<?php echo htmlspecialchars($row['pet_image']); ?>" class="card-img-top" alt="Pet Image">
                            <div class="card-body">
                                <h5 class="card-title">PET INFORMATION</h5>
                                <p class="card-text"><strong>Name:</strong> <?php echo htmlspecialchars($row['pet_name']); ?></p>
                                <p class="card-text"><strong>Breed:</strong> <?php echo htmlspecialchars($row['pet_breed']); ?></p>
                                <p class="card-text"><strong>Info:</strong> <?php echo htmlspecialchars($row['pet_info']); ?></p>
                                <p class="card-text"><strong>Owner Email:</strong> <?php echo htmlspecialchars($row['mail']); ?></p>
                            </div>
                            <!-- Card Footer with Buttons -->
                            <div class="card-footer d-flex justify-content-around align-items-center">
                                <button 
                                    type="button" 
                                    class="btn btn-primary flex-grow-1 mx-1" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#adoptModal"
                                    data-pet-id="<?php echo $row['id']; ?>"
                                    data-pet-name="<?php echo htmlspecialchars($row['pet_name']); ?>"
                                    data-pet-breed="<?php echo htmlspecialchars($row['pet_breed']); ?>"
                                    data-pet-info="<?php echo htmlspecialchars($row['pet_info']); ?>"
                                    data-pet-image="<?php echo htmlspecialchars($row['pet_image']); ?>"
                                    data-owner-email="<?php echo htmlspecialchars($row['mail']); ?>">
                                    Adopt
                                </button>
                                <form action="" method="POST" onsubmit="return confirmDelete();" class="flex-grow-1 mx-1">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="pet_id" value="<?php echo $row['id']; ?>">
                                    <button type="submit" class="btn btn-danger w-100">Delete</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <?php
                        }
                    } else {
                        echo "<p>No adoption listings available.</p>";
                    }
                    $conn->close();
                    ?>
                </div>
            </div>
        </main>
        
        <?php include('./script.php'); ?>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        
        <!-- Adoption Request Modal -->
        <div class="modal fade" id="adoptModal" tabindex="-1" aria-labelledby="adoptModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="adoptModalLabel">Adoption Request</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">x</button>
              </div>
              <div class="modal-body">
                <div class="container my-3">
                  <h5 id="modal-pet-name"></h5>
                  <div class="row">
                    <div class="col-md-4">
                      <img id="modal-pet-image" src="" alt="Pet Image" class="img-fluid">
                    </div>
                    <div class="col-md-8">
                      <p><strong>Breed:</strong> <span id="modal-pet-breed"></span></p>
                      <p><strong>Info:</strong> <span id="modal-pet-info"></span></p>
                      <!-- Display Owner Email -->
                      <p><strong>Owner Email:</strong> <span id="modal-owner-email"></span></p>
                      <!-- Hidden input for Owner Email to pass along with the form -->
                      <input type="hidden" name="owner_email" id="modal-owner-email-hidden">
                      <!-- Adoption Request Form -->
                      <form action="send_email.php" method="post" class="mt-3">
                        <!-- Hidden field for pet ID -->
                        <input type="hidden" name="pet_id" id="modal-pet-id">
                        <div class="mb-3">
                          <label for="adopter_email" class="form-label">Your Email:</label>
                          <input type="email" name="adopter_email" id="adopter_email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                          <label for="message" class="form-label">Message to the Owner:</label>
                          <textarea name="message" id="message" class="form-control" rows="4" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-success">Submit Adoption Request</button>
                      </form>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <!-- JavaScript to Populate Modal Fields and Confirm Deletion -->
        <script>
            document.getElementById('adoptModal').addEventListener('show.bs.modal', function (event) {
                var button = event.relatedTarget;
                // Extract data attributes from the clicked button
                var petId      = button.getAttribute('data-pet-id');
                var petName    = button.getAttribute('data-pet-name');
                var petBreed   = button.getAttribute('data-pet-breed');
                var petInfo    = button.getAttribute('data-pet-info');
                var petImage   = button.getAttribute('data-pet-image');
                var ownerEmail = button.getAttribute('data-owner-email');

                // Populate modal fields
                document.getElementById('modal-pet-id').value = petId;
                document.getElementById('modal-pet-name').textContent = petName;
                document.getElementById('modal-pet-breed').textContent = petBreed;
                document.getElementById('modal-pet-info').textContent = petInfo;
                document.getElementById('modal-pet-image').src = petImage;
                document.getElementById('modal-owner-email').textContent = ownerEmail;
                document.getElementById('modal-owner-email-hidden').value = ownerEmail;
            });
            
            function confirmDelete() {
                return confirm("Are you sure you want to delete this adoption listing?");
            }
            $(document).ready(function(){
    // Wait 3 seconds (3000 ms) and then fade out any element with the "alert" class.
    setTimeout(function(){
      $('.alert').fadeOut('fast');
    }, 3000);
  });
        </script>
    </div>
</body>
</html>
