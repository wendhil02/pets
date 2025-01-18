
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
      <nav class="topnav navbar navbar-light">
        <button type="button" class="navbar-toggler text-muted mt-2 p-0 mr-3 collapseSidebar">
          <i class="fe fe-menu navbar-toggler-icon"></i>
        </button>
        <form class="form-inline mr-auto searchform text-muted mt-3 ">
          <input class="form-control bg-transparent border-0 pl-4 " type="search" placeholder="Type something....." aria-label="Search">
        </form>

        <ul class="nav">
    
          
          <li class="nav-item">
            <section class="nav-link text-muted my-2 circle-icon" href="#" data-toggle="modal" data-target=".modal-shortcut">
              <span class="fe fe-message-circle fe-16"></span>
            </section>
          </li>


          <li class="nav-item nav-notif">
  <section class="nav-link text-muted my-2 circle-icon" href="#" data-toggle="modal" data-target=".modal-notif">
    <span class="fe fe-bell fe-16"></span>
   
      <span id="notification-count" style="
        position: absolute; 
        top: 12px; right: 5px; 
        font-size:13px; color: white;
        background-color: red;
        width:8px;
        height: 8px;
        display: flex;
        justify-content: center;
        align-items: center;
        border-radius: 50px;
      ">
      
  </section>
</li>

          <li class="nav-item dropdown">
            <span class="nav-link text-muted pr-0 avatar-icon" href="#" id="navbarDropdownMenuLink" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <span class="avatar avatar-sm mt-2">
  <div class="avatar-img rounded-circle avatar-initials-min text-center position-relative">
  

  </div>
</span>
</span>
            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownMenuLink">
              <a class="dropdown-item" href="#"><i class="fe fe-user"></i>&nbsp;&nbsp;&nbsp;Profile</a>
              <a class="dropdown-item" href="#"><i class="fe fe-settings"></i>&nbsp;&nbsp;&nbsp;Settings</a>
              <a class="dropdown-log-out" href="#"><i class="fe fe-log-out"></i>&nbsp;&nbsp;&nbsp;Log Out</a>
            </div>    
          </li>
        </ul>
      </nav>


      <aside class="sidebar-left border-right bg-white " id="leftSidebar" data-simplebar>
        <a href="#" class="btn collapseSidebar toggle-btn d-lg-none text-muted ml-2 mt-3" data-toggle="toggle">
          <i class="fe fe-x"><span class="sr-only"></span></i>
        </a>

        <nav class="vertnav navbar-side navbar-light">
          <!-- nav bar -->
          <div class="w-100 mb-4 d-flex">
            <a class="navbar-brand mx-auto mt-2 flex-fill text-center" href="#">
              
                
                <img src="../assets/images/unified-lgu-logo.png" width="45">
              

            <div class="brand-title">
            <br>
              <span>LGU3 - TEMPLATE</span>
            </div>
                       
            </a>

          </div>

          <!--Sidebar ito-->

          <ul class="navbar-nav active flex-fill w-100 mb-2">
            <li class="nav-item dropdown">
              <a class="nav-link" href="dashboard.php">
              <i class="fas fa-chart-line"></i>
                <span class="ml-3 item-text">Dashboard</span>

              </a>
            </li>
          </ul>
         <p class="text-muted-nav nav-heading mt-4 mb-1">
          <span style="font-size: 10.5px; font-weight: bold; font-family: 'Inter', sans-serif;">REGISTRATION MANAGEMENT</span>
          </p> 
          <ul class="navbar-nav flex-fill w-100 mb-2">

          <ul class="navbar-nav flex-fill w-100 mb-2">
            <li class="nav-item w-100">
              <a class="nav-link" href="register.php
            ">
              <i class="fa-solid fa-wrench"></i>
                <span class="ml-3 item-text">Pet Register</span>
              </a>
            </li>
          </ul>

          <ul class="navbar-nav flex-fill w-100 mb-2">
            <li class="nav-item w-100">
              <a class="nav-link" href="mypet.php
            ">
              <i class="fa-solid fa-wrench"></i>
                <span class="ml-3 item-text">Pet Registered</span>
              </a>
            </li>
          </ul>


          
          <p class="text-muted-nav nav-heading mt-4 mb-1">
            <span style="font-size: 10.5px; font-weight: bold; font-family: 'Inter', sans-serif;">ADOPTION MANAGEMENT</span>
            </p>
        
          <ul class="navbar-nav flex-fill w-100 mb-2">
            <li class="nav-item w-100">
            <a class="nav-link" href="adopt.php
          ">
            <i class="fa-solid fa-wrench"></i>
                <span class="ml-3 item-text">Pet adoption</span>
              </a>
            </li>
          </ul>

          <ul class="navbar-nav flex-fill w-100 mb-2">
            <li class="nav-item w-100">
            <a class="nav-link" href="adoption.php
          ">
            <i class="fa-solid fa-wrench"></i>
                <span class="ml-3 item-text">Open adoption</span>
              </a>
            </li>
          </ul>

          <ul class="navbar-nav flex-fill w-100 mb-2">
            <li class="nav-item w-100">
            <a class="nav-link" href="pet_view.php
          ">
            <i class="fa-solid fa-wrench"></i>
                <span class="ml-3 item-text">List adoption</span>
              </a>
            </li>
          </ul>

          <p class="text-muted-nav nav-heading mt-4 mb-1">
          <span style="font-size: 10.5px; font-weight: bold; font-family: 'Inter', sans-serif;">REPORT MANAGEMENT</span>
          </p>


          <ul class="navbar-nav flex-fill w-100 mb-2">
            <li class="nav-item w-100">
              <a class="nav-link" href="missing.php
            ">
              <i class="fa-solid fa-wrench"></i>
                <span class="ml-3 item-text">Missing animal </span>
              </a>
            </li>
          </ul>

          <ul class="navbar-nav flex-fill w-100 mb-2">
            <li class="nav-item w-100">
            <a class="nav-link" href="cruelty.php
          "> 
            <i class="fa-solid fa-wrench"></i>
                <span class="ml-3 item-text">Animal cruelty </span>
              </a>
            </li>
          </ul>

          <ul class="navbar-nav flex-fill w-100 mb-2">
            <li class="nav-item w-100">
            <a class="nav-link" href="cruelty.php
          "> 
            <i class="fa-solid fa-wrench"></i>
                <span class="ml-3 item-text">Animal cruelty </span>
              </a>
            </li>
          </ul>

          <p class="text-muted-nav nav-heading mt-4 mb-1">
          <span style="font-size: 10.5px; font-weight: bold; font-family: 'Inter', sans-serif;">SETTINGS</span>
          </p>

          <ul class="navbar-nav flex-fill w-100 mb-2">
            <li class="nav-item w-100">
            <a class="nav-link" href="settings.php">
              <i class="fa-solid fa-screwdriver-wrench"></i>
                <span class="ml-3 item-text">Settings</span>
              </a>
            </li>
          </ul>
  
      
        </nav>
      </aside>
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

