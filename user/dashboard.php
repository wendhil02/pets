<?php
include('../dbconn/config.php');
include('../dbconn/authentication.php');
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <?php include('./disc/partials/header.php');
    ?>
    <script src="https://cdn.jsdelivr.net/npm/qrcode/build/qrcode.min.js"></script>
</head>
    
    <div class="loader-mask">
        <div class="loader">
            <div></div>
            <div></div>
        </div>
    </div>
    
 
  <body class="vertical  light">
    <div class="wrapper">
    <div class="wrapper">


    <?php include('./disc/partials/navbar.php');
        ?>
        <?php include('./disc/partials/sidebar.php');
        ?>

      <main role="main" class="main-content">

            <!--For Notification header naman ito-->
            <?php include('./disc/partials/modal-notif.php') ?>

      <!--YOUR CONTENTHERE-->
        
        <div class="container my-6">
          <div class="row ">
            <div class="col col-md-6">
              <img class="img-fluid rounded-lg " src="img/animal_welfare.jpg" alt="animal_welfare">
            </div>
            <div class="col-md  "> 
              <h2 class="row px-4 pt-2 fw-bold">Barangay Animal Welfare</h2>
              <p class="row-md-4 px-4 text-overly">Barangay Animal Welfare promotes animal well-being by encouraging responsible pet ownership, preventing abuse, addressing stray animal issues, and providing resources for pet care, all to foster a compassionate and safe community for animals and residents.</p>
              <div class=" col-12-md-4 justify-content-center align-items-center text-center">
               <a href="#" class=" btn btn-lg btn-primary">Adoption</a> 
              </div>
            </div>
          </div>
        </div>
               

        <div class="container my-6">
          <div class="row  ">
            <div class="col-md-4 p-2">
              <img class="img-fluid rounded-lg" src="img/dog1.jpg" alt="Animal">
            </div>
            <div class="col-md-4 p-2">
              <img class="img-fluid rounded-lg" src="img/dog1.jpg" alt="Animal">
            </div>
            <div class="col-md-4 p-2">
            <img class="img-fluid rounded-lg" src="img/dog1.jpg" alt="Animal">
            </div>
            </div>
            <div class=" col-12-md-4 justify-content-center align-items-center text-center">
            <a href="#" class=" btn btn-lg btn-primary">Pet for adoption </a> 
            </div>
          </div> 
  
          <div class="container my-6">
            <div class="row ">
              <div class="col-md "> 
                <h2 class="row px-4 pt-2 fw-bold">Barangay Animal Welfare</h2>
                <p class="row-md-4 px-4 text-overly">Barangay Animal Welfare promotes animal well-being by encouraging responsible pet ownership, preventing abuse, addressing stray animal issues, and providing resources for pet care, all to foster a compassionate and safe community for animals and residents.</p>
                <div class=" col-12-md-4 justify-content-center align-items-center text-center">
                  <a href="#" class=" btn btn-lg btn-primary">Report </a> 
                </div>
              </div>
              <div class="col col-md-6">
                <img class="img-fluid rounded-lg " src="img/animal_welfare.jpg" alt="animal_welfare">
              </div>
            </div>
          </div>

          <div class="container my-6">
            <div class="row ">
              <div class="col-md  "> 
                <h2 class="col px-4 pt-2 fw-bold text-center">About</h2>
                <p class="row-md-4 px-4 text-overly">Lorem ipsum dolor sit amet consectetur adipisicing elit. Nam, quaerat? Quae cupiditate quisquam dolores sit, blanditiis quo perspiciatis ratione placeat quibusdam, odio odit animi mollitia, modi dicta veritatis sint aut?Lorem ipsum dolor sit amet consectetur adipisicing elit. Asperiores sapiente, aut itaque dolor nesciunt perferendis sit libero laboriosam qui esse maxime consequatur, nostrum voluptatibus culpa hic commodi inventore earum excepturi.</p>
              </div>
            </div>
          </div>

         <footer id="footer" class="row card-body  mt-6">
          
          <div class="col-md-4">
           <div class="card-body">
            <div class="row align-items-center">
              <div class="col">
               <div class="row ">
                <h3 class="text-lg font-semibold mb-4">Contact Us</h3>
                <div class="">
                 <p>Email: <a href="mailto:info@yourwebsite.com" class="text-green-600 hover:underline">info@yourwebsite.com</a></p>
                 <p>Phone: <a href="tel:+11234567890" class="text-green-600 hover:underline">+1 (123) 456-7890</a></p>
                 <p>Address: 123 Main Street, Your City, Your Country</p>
                </div>
               </div>
            </div>
            </div>
           </div>
          </div>
           <div class="col-md-4">
            <div class="card-body">
              <div class="row align-items-center">
               <div class="">
            <div class="col">
              <h3 class="text-lg font-semibold mb-4">Follow Us</h3>
              <div class="">
                <a href="#" target="_blank" class="text-gray-600 hover:text-green-600">Facebook</a>
                          <a href="#" target="_blank" class="text-gray-600 hover:text-green-600">Twitter</a>
                          <a href="#" target="_blank" class="text-gray-600 hover:text-green-600">Instagram</a>
                          <a href="#" target="_blank" class="text-gray-600 hover:text-green-600">LinkedIn</a>
              </div>
            </div>
               </div>
              </div>
           </div>
            </div>
           <div class="col-md-4">
            <div class="card-body">
              <div class="row align-items-center">
                <div class="col">
                  <div class="row">
                        <div class="col">
                          <h3 class="text-lg font-semibold mb-4">Quick Links</h3>
                        <div class="col">
                          <a href="#" class="row text-gray-600 hover:text-green-600 mb-1">About Us</a>
                          <a href="#" class="row text-gray-600 hover:text-green-600 mb-1">Services</a>
                          <a href="#" class="row text-gray-600 hover:text-green-600 mb-1">Volunteer</a>
                          <a href="#" class="row text-gray-600 hover:text-green-600 mb-1">Contact</a>
                          <a href="#" class="row text-gray-600 hover:text-green-600">FAQ</a>
                        </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
          


         </footer>
        
      </main>
      
      </div>  
  <!-- Include jQuery -->
<?php include ('./script.php'); ?>

  </body>
</>

