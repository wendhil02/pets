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
            align-items: flex-start;
            text-align: left;
            padding: 1rem;
        }
        .card-title {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 0.5rem;
            text-align: left;
        }
        .card-text {
            font-size: 14px;
            margin-bottom: 0.25rem;
            text-align: left;
            width: 100%;
        }
        .card-footer {
            background: transparent;
            border-top: none;
            display: flex;
            justify-content: space-between;
            gap: 0.5rem;
        }
        .card-footer .btn {
            flex: 1;
            font-size: 0.9rem;
            padding: 0.5rem;
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
                <?php if (isset($message)) : ?>
                    <div class="alert alert-success" role="alert"><?php echo htmlspecialchars($message); ?></div>
                <?php elseif (isset($error)) : ?>
                    <div class="alert alert-danger" role="alert"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                <div class="row">
                    <?php
                    $sql = "SELECT * FROM adoption WHERE approved = 1 ORDER BY created_at DESC";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                    ?>
                    <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4">
                        <div class="card">
                            <img src="<?php echo htmlspecialchars($row['pet_image']); ?>" class="card-img-top" alt="Pet Image">
                            <div class="card-body">
                                <h5 class="card-title">PET INFORMATION</h5>
                                <p class="card-text"><strong>Name:</strong> <?php echo htmlspecialchars($row['pet_name']); ?></p>
                                <p class="card-text"><strong>Breed:</strong> <?php echo htmlspecialchars($row['pet_breed']); ?></p>
                                <p class="card-text"><strong>Info:</strong> <?php echo htmlspecialchars($row['pet_info']); ?></p>
                                <p class="card-text"><strong>Owner Email:</strong> <?php echo htmlspecialchars($row['email']); ?></p>
                            </div>
                            <div class="card-footer">
                                <button 
                                    type="button" 
                                    class="btn btn-primary" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#adoptModal"
                                    data-pet-id="<?php echo $row['id']; ?>"
                                    data-pet-name="<?php echo htmlspecialchars($row['pet_name']); ?>"
                                    data-pet-breed="<?php echo htmlspecialchars($row['pet_breed']); ?>"
                                    data-pet-info="<?php echo htmlspecialchars($row['pet_info']); ?>"
                                    data-pet-image="<?php echo htmlspecialchars($row['pet_image']); ?>"
                                    data-owner-email="<?php echo htmlspecialchars($row['email']); ?>">
                                    Adopt
                                </button>
                                <?php if (isset($_SESSION['email']) && $_SESSION['email'] === $row['email']) : ?>
                                    <form action="" method="POST" onsubmit="return confirmDelete();">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="pet_id" value="<?php echo $row['id']; ?>">
                                        <button type="submit" class="btn btn-danger">Delete</button>
                                    </form>
                                <?php endif; ?>
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
        <?php include("./disc/partials/adopt-modal.php")?>
        <script>
            // Populate the adoption modal with data from the button's data attributes.
            document.getElementById('adoptModal').addEventListener('show.bs.modal', function (event) {
                var button = event.relatedTarget;
                var petId      = button.getAttribute('data-pet-id');
                var petName    = button.getAttribute('data-pet-name');
                var petBreed   = button.getAttribute('data-pet-breed');
                var petInfo    = button.getAttribute('data-pet-info');
                var petImage   = button.getAttribute('data-pet-image');
                var ownerEmail = button.getAttribute('data-owner-email');

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
                setTimeout(function(){
                    $('.alert').fadeOut('fast');
                }, 3000);
            });
        </script>
    </div>
</body>
</html>
