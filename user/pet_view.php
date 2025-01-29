<?php
include('dbconn/config.php');
include('dbconn/authentication.php');
?>

<!DOCTYPE html>
<html lang='en'>

<head>
    <?php include('./disc/partials/header.php');
    ?>
</head>

<body class='vertical light'>
    <div class='wrapper'>
        <?php include('./disc/partials/navbar.php');
        ?>
        <?php include('./disc/partials/sidebar.php');
        ?>

        <main role='main' class='main-content'>
            <!-- Notification header -->
            <?php include('./disc/partials/modal-notif.php') ?>

            <div class='container mt-5'>
                <div class='row'>
                    <?php
                    // Fetch data
                    $sql = 'SELECT * FROM register';
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            ?>

                            <div class='card mb-3 m-2' style='max-width: 540px;'>
                                <div class='row g-0'>
                                    <div class='col-md-4'>
                                        <img src="<?php echo $row['pet_image']; ?>" class='card-img-top'
                                            alt="<?php echo $row['pet_image']; ?>">
                                    </div>
                                    <div class='col-md-8'>
                                        <div class='card-body'>
                                            <h5 class='card-title'>PET INFORMATION</h5>
                                            <p class='card-text'>Name:<?php echo $row['pet'];
                                            ?></p>
                                            <p class='card-text'>Age:<?php echo $row['age'];
                                            ?></p>
                                            <p class='card-text'>breed:<?php echo $row['breed'];
                                            ?></p>
                                            <p class='card-text'>info:<?php echo $row['info'];
                                            ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <?php
                        }
                    } else {
                        echo '<p>No register found.</p>';
                    }

                    $conn->close();
                    ?>
                </div>
            </div>

        </main>

        <?php include('./script.php');
        ?>
        <script src='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js'></script>

</body>

</html>