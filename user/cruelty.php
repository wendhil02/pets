<?php
include('./dbconn/config.php');
include('./dbconn/authentication.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include('./disc/partials/header.php'); ?>
    <style>
        .form-container {
            max-width: 800px;
            margin: auto;
            padding: 30px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .form-title {
            text-align: center;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .btn-submit {
            display: block;
            width: auto;
            padding: 12px 30px;
            border-radius: 5px;
            margin: 0 auto;
        }
    </style>
</head>
<body class="vertical light">
    <div class="wrapper">
        <?php include('./disc/partials/navbar.php'); ?>
        <?php include('./disc/partials/sidebar.php'); ?>

        <main role="main" class="main-content">
            <div class="container">
                <div class="form-container">
                    <h4 class="form-title">Report Cruelty</h4>
                    <form id="regForm" enctype="multipart/form-data" method="POST">
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="text" class="form-control" id="phone" name="phone" required>
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="address" class="form-label">Address</label>
                                <textarea class="form-control" id="address" name="address" required></textarea>
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="petType" class="form-label">Pet Type</label>
                                <input type="text" class="form-control" id="petType" name="petType" required>
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="petBreed" class="form-label">Breed</label>
                                <input type="text" class="form-control" id="petBreed" name="petBreed" required>
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="info" class="form-label">Additional Information</label>
                                <textarea class="form-control" id="info" name="info"></textarea>
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="petImage" class="form-label">Pet Image</label>
                                <input type="file" class="form-control" id="petImage" name="petImage" accept="image/*" required>
                            </div>
                            <div class="col-md-12 form-group text-center">
                                <button type="button" id="submitForm" class="btn btn-primary btn-submit">Submit</button>
                            </div>
                        </div>
                    </form>
                    <div id="submitFeedback" class="alert d-none mt-3"></div>
                </div>
            </div>
        </main>
    </div>
    <?php include('./script.php'); ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>