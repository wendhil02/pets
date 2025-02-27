<?php
include('./dbconn/authentication.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include('./disc/partials/header.php'); ?>
    <script src="https://cdn.jsdelivr.net/npm/qrcode/build/qrcode.min.js"></script>
    <style>
        .hero-section {
            background-color: #f8f9fa;
            padding: 3rem 1rem;
            border-radius: 1rem;
        }
        .hero-section img {
            border-radius: 1rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        .content-section {
            padding: 4rem 1rem;
            background-color: #fff;
            border-radius: 1rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            margin-bottom: 2rem;
        }
        .gallery-img {
            height: 300px;
            object-fit: cover;
            border-radius: 1rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        .btn-custom {
            border-radius: 2rem;
            padding: 0.75rem 2rem;
        }
        @media (max-width: 768px) {
            .hero-section img,
            .gallery-img {
                height: 200px;
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

            <section class="container hero-section text-center">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <img class="img-fluid" src="img/animal_welfare.jpg" alt="animal_welfare">
                    </div>
                    <div class="col-md-6 text-md-start">
                        <h2 class="fw-bold">Barangay Animal Welfare</h2>
                        <p>Promoting animal well-being through responsible pet ownership, preventing abuse, addressing stray issues, and providing resources for pet care to foster a compassionate community.</p>
                    </div>
                </div>
            </section>

            <section class="container content-section text-center">
                <h2 class="fw-bold mb-4">Pets Available for Adoption</h2>
                <div class="row g-4">
                    <div class="col-md-4">
                        <img class="img-fluid gallery-img" src="img/dog1.jpg" alt="Animal">
                    </div>
                    <div class="col-md-4">
                        <img class="img-fluid gallery-img" src="img/dog1.jpg" alt="Animal">
                    </div>
                    <div class="col-md-4">
                        <img class="img-fluid gallery-img" src="img/dog1.jpg" alt="Animal">
                    </div>
                </div>
                <div class="mt-4">
                    <a href="pet_view.php" class="btn btn-primary btn-lg btn-custom">View Pets for Adoption</a>
                </div>
            </section>

            <section class="container content-section text-center">
                <h2 class="fw-bold mb-4">Report Animal Cruelty</h2>
                <p>Help us protect our community's animals. Report any cases of cruelty and be part of the solution for a safe environment for all.</p>
                <a href="cruelty.php" class="btn btn-danger btn-lg btn-custom">Report Now</a>
            </section>

            <section class="container content-section">
                <h2 class="fw-bold text-center mb-4">About Us</h2>
                <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Nam, quaerat? Quae cupiditate quisquam dolores sit, blanditiis quo perspiciatis ratione placeat quibusdam, odio odit animi mollitia, modi dicta veritatis sint aut? Asperiores sapiente, aut itaque dolor nesciunt perferendis sit libero laboriosam qui esse maxime consequatur, nostrum voluptatibus culpa hic commodi inventore earum excepturi.</p>
            </section>

            <footer class="container text-center py-4 border-top">
                <p class="mb-0">&copy; 2025 Barangay Animal Welfare. All rights reserved.</p>
            </footer>
        </main>
    </div>

    <?php include('./script.php'); ?>
</body>
</html>
