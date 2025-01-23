<?php
include('dbconn/config.php');
include('dbconn/authentication.php');
?>

<!DOCTYPE html>
<html lang='en'>

<head>
    <?php include('./disc/partials/header.php');
    ?>
    <script src="https://cdn.jsdelivr.net/npm/qrcode/build/qrcode.min.js"></script>
</head>


<body class='vertical  light'>
    <div class='wrapper'>

        <?php include('./disc/partials/navbar.php');
        ?>
        <?php include('./disc/partials/sidebar.php');
        ?>

        <main role='main' class='main-content'>

            <!--For Notification header naman ito-->
            <?php include('./disc/partials/modal-notif.php') ?>

            <!--YOUR CONTENTHERE-->
          
        </main>
    </div>
    <?php include('./script.php'); ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>