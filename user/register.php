<?php
include('./dbconn/config.php');
include('./dbconn/authentication.php');
?>

<!DOCTYPE html>
<html lang='en'>

<head>
    <?php include('./disc/partials/header.php'); ?>
    <script src="https://cdn.jsdelivr.net/npm/qrcode/build/qrcode.min.js"></script>

<!-- Bootstrap JS (for modal functionality) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>



    <style>
        body {
            background-color: #f8f9fa;
        }

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

        /* Improve the vaccine status selection design */
.form-group select.form-select {
    width: 100%;
    padding: 12px;
    border-radius: 5px;
 
    background-color: #fff;
    font-size: 16px;
    transition: border-color 0.3s ease-in-out;
}

.form-group select.form-select:focus {

    outline: none;
 
}

/* Styling the vaccine section */
#vaccine_section {
    background: #f1f8e9;
    padding: 15px;
    border-radius: 10px;
    margin-top: 10px;

}

.vaccine_entry {
    background: #e9f5e9;
    padding: 10px;
    border-radius: 8px;
    margin-bottom: 10px;
  
}

.vaccine_entry input {
    border-radius: 5px;
    padding: 8px;
    border: 1px solid #ced4da;
}

.vaccine_entry button {
    margin-top: 10px;
}

/* Vaccine section visibility */
#vaccine_section.hidden {
    display: none;
}

/* General button styling */
.btn {
    font-size: 16px;
    padding: 10px 15px;
    border-radius: 5px;
    transition: background-color 0.3s ease-in-out;
}

/* Styling for the "Add Vaccine" button */
.btn-primary {
    background-color: #007bff;
    border: none;
}

.btn-primary:hover {
    background-color: #0056b3;
}

/* Styling for the "Remove" button */
.btn-danger {
    background-color: #dc3545;
    border: none;
}

.btn-danger:hover {
    background-color: #bd2130;
}

/* Improved vaccine section design */
#vaccine_section {
    background: #f9f9f9;
    padding: 20px;
    border-radius: 10px;
    margin-top: 15px;
    border: 1px solid #ddd;
}

.vaccine_entry {
    background: #ffffff;
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 10px;
    border: 1px solid #ddd;
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}

.vaccine_entry input {
    flex: 1;
    min-width: 180px;
}

.vaccine_entry button {
    padding: 8px 12px;
    font-size: 14px;
    cursor: pointer;
    align-self: center;
}

/* Aligns the vaccine section for better readability */
#vaccine_entries {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

/* Responsive Design Fixes */
@media (max-width: 768px) {
    .vaccine_entry {
        flex-direction: column;
    }
}
/* General button styling */
.btn {
    font-size: 16px;
    padding: 10px 15px;
    border-radius: 5px;
    transition: background-color 0.3s ease-in-out;
}

/* Styling for the "Add Vaccine" button */
.btn-primary {
    background-color: #007bff;
    border: none;
}

.btn-primary:hover {
    background-color: #0056b3;
}

/* Styling for the "Remove" button */
.btn-danger {
    background-color: #dc3545;
    border: none;
}

.btn-danger:hover {
    background-color: #bd2130;
}

/* Improved vaccine section design */
#vaccine_section {
    background: #f9f9f9;
    padding: 20px;
    border-radius: 10px;
    margin-top: 15px;
    border: 1px solid #ddd;
}

.vaccine_entry {
    background: #ffffff;
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 10px;
    border: 1px solid #ddd;
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}

.vaccine_entry input {
    flex: 1;
    min-width: 180px;
}

.vaccine_entry button {
    padding: 8px 12px;
    font-size: 14px;
    cursor: pointer;
    align-self: center;
}

/* Aligns the vaccine section for better readability */
#vaccine_entries {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

/* Responsive Design Fixes */
@media (max-width: 768px) {
    .vaccine_entry {
        flex-direction: column;
    }
}

    </style>
</head>


<div class="loader-mask">
    <div class="loader">
        <div></div>
        <div></div>
    </div>
</div>

<body class='vertical  light'>
    <div class='wrapper'>

        <?php include('./disc/partials/navbar.php');
        ?>
        <?php include('./disc/partials/sidebar.php');
        ?>

        <main role='main' class='main-content'>
        <?php
$successMsg = isset($_GET['success']) ? $_GET['success'] : '';
$errorMsg = isset($_GET['error']) ? $_GET['error'] : '';
?>

            <div class='container'>
                <div class='form-container'>
                    <h4 class='form-title'>Pet Registration Form</h4>
                    <form id="petForm" name="petForm" action="register-process.php" method="post" enctype="multipart/form-data">
                   <div class="row">
                   <div class="col-md-6 form-group ">
                            <label class="form-label">Name</label>
                            <input type="text" name="name" class="form-control">
                            <span class="error"></span>
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="form-label">Phone</label>
                            <input type="text" name="phone" class="form-control">
                            <span class="error"></span>
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control">
                            <span class="error"></span>
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="form-label">Address</label>
                            <input type="text" name="address" class="form-control">
                            <span class="error"></span>
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="form-label">Pet Name</label>
                            <input type="text" name="pet_name" class="form-control">
                            <span class="error"></span>
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="form-label">Pet Age</label>
                            <input type="text" name="pet_age" class="form-control">
                            <span class="error"></span>
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="form-label">Pet Type</label>
                            <input type="text" name="pet_type" class="form-control">
                            <span class="error"></span>
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="form-label">Pet Breed</label>
                            <input type="text" name="pet_breed" class="form-control">
                            <span class="error"></span>
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="form-label">Pet Info</label>
                            <textarea name="pet_info" class="form-control"></textarea>
                            <span class="error"></span>
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="form-label">Pet Image</label>
                            <input type="file" name="pet_image" class="form-control">
                            <span class="error"></span>
                        </div>

                        <div class="col-md-12 form-group">
                            <label class="form-label">Vaccine Status</label>
                            <select name="vaccine_status" id="vaccine_status" class="form-select"
                                onchange="toggleVaccineForm()">
                                <option value="Not Vaccinated">Not Vaccinated</option>
                                <option value="Vaccinated">Vaccinated</option>
                            </select>
                        </div>

                        <div id="vaccine_section" class="col-md-12 form-group" style="display:none;">
                            <label class="form-label">Vaccine Details</label>
                            <div id="vaccine_entries"></div>
                            <button type="button" class="btn btn-primary mt-2" onclick="addVaccine()">Add
                                Vaccine</button>
                        </div>
                        <button type="submit" class="btn btn-primary btn-submit">Submit</button>

                   </div>
                    </form>

                </div>
            </div>

            <footer class=" text-dark py-4 border-top mt-3">
                <div class="container">
                    <div class="row align-items-center text-center text-md-start">

                        <!-- Social Media Icons -->
                        <div
                            class="col-12 col-md-4 col-md-6 form-group mb-md-0 d-flex justify-content-center justify-content-md-start">
                            <a href="#" class="text-primary m-3 fs-4"><i class="fab fa-facebook"></i></a>
                            <a href="#" class="text-danger m-3 fs-4"><i class="fab fa-instagram"></i></a>
                            <a href="#" class="text-info m-3 fs-4"><i class="fab fa-twitter"></i></a>
                        </div>

                        <!-- Copyright Text -->
                        <div class="col-12 col-md-4 text-center">
                            <small>&copy; 2025 Barangay Pet Welfare. <i>All Rights Reserved.</i></small>
                        </div>

                        <!-- Links and Sponsor -->
                        <div class="col-12 col-md-4 text-center text-md-end">
                            <a href="#" class="text-primary fw-bold me-2">Terms of Use</a>
                            <a href="#" class="text-primary fw-bold me-2">Privacy Policy</a>
                            <a href="#" class="text-primary fw-bold">Sitemap</a>
                            <p class="d-inline-block text-muted ms-2">
                                Website sponsored by <a href="#" class="text-primary fw-bold">BPWS</a>
                            </p>
                        </div>

                    </div>
                </div>
            </footer>

            <!-- Font Awesome for Icons -->
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<!-- Success Modal -->
        <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="successModalLabel">Success</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">X</button>
      </div>
      <div class="modal-body">
        <?php echo htmlspecialchars($successMsg); ?>
      </div>
     
    </div>
  </div>
</div>
       
<!-- Error Modal -->
<div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title" id="errorModalLabel">Error</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <?php echo htmlspecialchars($errorMsg); ?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>


        </main>
    </div>
    <?php include('script.php'); ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
document.addEventListener("DOMContentLoaded", function () {
    // Function to validate the form
    function validateForm(event) {
        event.preventDefault(); // Prevent form submission

        let isValid = true;
        let form = document.forms["petForm"];

        // Validation messages for general fields
        let errorMessages = {
            "name": "Please enter your name.",
            "phone": "Please enter a valid phone number.",
            "email": "Please enter a valid email address.",
            "address": "Please enter your address.",
            "pet_name": "Please enter your pet's name.",
            "pet_age": "Please enter a valid pet age.",
            "pet_type": "Please enter the pet type.",
            "pet_breed": "Please enter the pet breed.",
            "pet_info": "Please provide some details about your pet.",
            "pet_image": "Please upload an image of your pet."
        };

        // Validate General Fields
        Object.keys(errorMessages).forEach(field => {
            let input = form[field];
            let errorSpan = input.nextElementSibling; 

            if (!errorSpan || !errorSpan.classList.contains("error")) return;

            if (input.value.trim() === "") {
                errorSpan.textContent = errorMessages[field];
                errorSpan.style.color = "red";
                isValid = false;
            } else {
                errorSpan.textContent = "";
            }
        });

        // Validate Vaccine Section if "Vaccinated" is selected
        let vaccineStatus = document.getElementById("vaccine_status").value;
        if (vaccineStatus === "Vaccinated") {
            let vaccineEntries = document.querySelectorAll(".vaccine_entry");

            if (vaccineEntries.length === 0) {
                alert("Please add at least one vaccine entry.");
                isValid = false;
            }

            vaccineEntries.forEach(entry => {
                let vaccineType = entry.querySelector('input[name="vaccine_type[]"]');
                let vaccineName = entry.querySelector('input[name="vaccine_name[]"]');
                let vaccineDate = entry.querySelector('input[name="vaccine_date[]"]');
                let administeredBy = entry.querySelector('input[name="administered_by[]"]');

                if (!vaccineType.value.trim() || !vaccineName.value.trim() || !vaccineDate.value.trim() || !administeredBy.value.trim()) {
                    alert("All vaccine details must be filled out.");
                    isValid = false;
                }
            });
        }

        // Submit form if valid
        if (isValid) {
            form.submit();
        }
    }

    // Attach event listener to the form submission
    document.getElementById("petForm").addEventListener("submit", validateForm);

    // Toggle Vaccine Form Visibility
    document.getElementById("vaccine_status").addEventListener("change", function () {
        document.getElementById("vaccine_section").style.display = (this.value === "Vaccinated") ? "block" : "none";
    });

    // Function to add a vaccine entry dynamically
    window.addVaccine = function () {
        let div = document.createElement("div");
        div.classList.add("vaccine_entry", "border", "p-3", "mb-2", "rounded");
        div.innerHTML = `
            <div class="mb-2">
                <label class="form-label">Vaccine Type</label>
                <input type="text" name="vaccine_type[]" class="form-control">
            </div>
            <div class="mb-2">
                <label class="form-label">Vaccine Name</label>
                <input type="text" name="vaccine_name[]" class="form-control">
            </div>
            <div class="mb-2">
                <label class="form-label">Vaccine Date</label>
                <input type="date" name="vaccine_date[]" class="form-control">
            </div>
            <div class="mb-2">
                <label class="form-label">Administered By</label>
                <input type="text" name="administered_by[]" class="form-control">
            </div>
            <button type="button" class="btn btn-danger btn-sm" onclick="this.parentElement.remove()">Remove</button>
        `;
        document.getElementById("vaccine_entries").appendChild(div);
    };
});

document.addEventListener("DOMContentLoaded", function() {
    var successMsg = "<?php echo $successMsg; ?>";
    var errorMsg = "<?php echo $errorMsg; ?>";

    if (successMsg) {
        var successModal = new bootstrap.Modal(document.getElementById('successModal'));
        successModal.show();
    }

    if (errorMsg) {
        var errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
        errorModal.show();
    }
});
</script>
</body>

</html>