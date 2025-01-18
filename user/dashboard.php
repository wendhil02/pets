
<!DOCTYPE php>
<php lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="icon" href="../assets/images/unified-lgu-logo.png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.6.0/css/fontawesome.min.css">
    <link rel ="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <title>Systems</title>

    <!-- Simple bar CSS (for scvrollbar)-->
    <link rel="stylesheet" href="../css/simplebar.css">
    <!-- Fonts CSS -->
    <link href="https://fonts.googleapis.com/css2?family=Overpass:ital,wght@0,100;0,200;0,300;0,400;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
    <!-- Icons CSS -->
    <link rel="stylesheet" href="../css/feather.css">
    <!-- App CSS -->
    <link rel="stylesheet" href="../css/main.css">   
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <style>
    .avatar-initials {
    width: 165px;
    height: 165px;
    border-radius: 50%;
    display: flex;
    margin-left: 8px;
    justify-content: center;
    align-items: center;
    font-size: 50px;
    font-weight: bold;
    color: #fff;
    
    }

    .avatar-initials-min {
    width: 40px;
    height: 40px;
    background: #75e6da;
    border-radius: 50%;
    display: flex;
    margin-left: 8px;
    justify-content: center;
    align-items: center;
    font-size: 14px;
    font-weight: bold;
    color: #fff;
    
  }

    .upload-icon {
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  cursor: pointer;
  font-size: 24px;
  color: #fff;
  opacity: 0;
  transition: opacity 0.3s ease-in-out;
  background-color: #333;
  padding: 10px;
  border-radius: 50%;
  z-index: 1;
}

.avatar-img:hover .upload-icon {
  opacity: 1;
}

.avatar-img {
  position: relative;
  transition: background-color 0.3s ease-in-out;
}

.avatar-img:hover {
  background-color: #a0f0e6;
}

</style>
  
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

<?php include ('./disc/partials/navbar.php'); ?>
<?php include ('./disc/partials/sidebar.php'); ?>
</div>
      <main role="main" class="main-content">
        
        <!--For Notification header naman ito-->

        <div class="modal fade modal-notif modal-slide" tabindex="-1" role="dialog" aria-labelledby="defaultModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="defaultModalLabel">Notifications</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>


              <div class="modal-body">
  <div class="list-group list-group-flush my-n3">
   
      <div class="col-12 mb-4">
        <div class="alert alert-success alert-dismissible fade show" role="alert" id="notification">
          <img class="fade show" src="../assets/images/unified-lgu-logo.png" width="35" height="35">
          <strong style="font-size:12px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"></strong> 
          <button type="button" class="close" data-dismiss="alert" aria-label="Close" onclick="removeNotification()">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
      </div> <!-- /. col -->

    <div id="no-notifications" style="display: none; text-align:center; margin-top:10px;">No notifications</div>
  </div> <!-- / .list-group -->
 
</div>

<div class="modal-footer">
<button type="button" class="btn btn-secondary btn-block" onclick="clearAllNotifications()">Clear All</button>
              </div>
            </div>
          </div>
        </div>



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









                 
          <!--<footer class="card container">
            <div class="container">
                <div class="col-3">
                     Contact Information 
                    <div class="row">
                        <h3 class="text-lg font-semibold mb-4">Contact Us</h3>
                        <p>Email: <a href="mailto:info@yourwebsite.com" class="text-green-600 hover:underline">info@yourwebsite.com</a></p>
                        <p>Phone: <a href="tel:+11234567890" class="text-green-600 hover:underline">+1 (123) 456-7890</a></p>
                        <p>Address: 123 Main Street, Your City, Your Country</p>
                    </div>
        
                     Social Media Links
                    <div class="row">
                        <h3 class="text-lg font-semibold mb-4">Follow Us</h3>
                        <div class="flex space-x-4">
                            <a href="#" target="_blank" class="text-gray-600 hover:text-green-600">Facebook</a>
                            <a href="#" target="_blank" class="text-gray-600 hover:text-green-600">Twitter</a>
                            <a href="#" target="_blank" class="text-gray-600 hover:text-green-600">Instagram</a>
                            <a href="#" target="_blank" class="text-gray-600 hover:text-green-600">LinkedIn</a>
                        </div>
                    </div>
        
                     Quick Links
                    <div class="row">
                        <h3 class="text-lg font-semibold mb-4">Quick Links</h3>
                        <a href="#" class="block text-gray-600 hover:text-green-600 mb-1">About Us</a>
                        <a href="#" class="block text-gray-600 hover:text-green-600 mb-1">Services</a>
                        <a href="#" class="block text-gray-600 hover:text-green-600 mb-1">Volunteer</a>
                        <a href="#" class="block text-gray-600 hover:text-green-600 mb-1">Contact</a>
                        <a href="#" class="block text-gray-600 hover:text-green-600">FAQ</a>
                    </div>
                </div>
        
                 Footer Bottom 
                <div class="text-center mt-6">
                    <p class="text-sm">&copy; 2024 Your Organization. All rights reserved.</p>
                </div>
            </div>
        </footer> -->
        
                 


   <!--<div class="container mt-5">
    <div class="row">
      <div class="col-12 col-md-6">
        <div class="image-container">
          <img class=" img-fluid rounded-lg " src="img/animal_welfare.jpg" alt="animal_welfare">
          <div class="text-overly">
            <h2>Barangay Animal Welfare</h2>
            Barangay Animal Welfare promotes animal well-being by encouraging responsible pet ownership, preventing abuse, addressing stray animal issues, and providing resources for pet care, all to foster a compassionate and safe community for animals and residents.
          </div>
        </div>
      </div>
    </div>
   </div>-->
        
      </main>
      
      
  <!-- Include jQuery -->
<?php include ('./script.php'); ?>

  </body>
</php>

