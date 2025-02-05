<?php
include('./dbconn/config.php');
include('./dbconn/authentication.php');
?>


<!DOCTYPE html>
<html lang='en'>

<head>
    <?php include('./disc/partials/header.php'); ?>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
<div class='vertical light'>
    <div class='wrapper'>

        <?php include('./disc/partials/navbar.php'); ?>
        <?php include('./disc/partials/sidebar.php'); ?>

        <main role='main' class='main-content'>
            <?php include('./disc/partials/modal-notif.php'); ?>

            <!-- YOUR CONTENT HERE -->
            <div class='container'>
                <div class='card'>
                    <div class=''>
                        <h4 class='card-title mt-5'>PET ADOPTION</h4>
                    </div>
                    <div class='card-body'>
                        <form id="adoptForm" class='row' enctype='multipart/form-data' method='POST'>
                            <div class='col-md-6 mt-3'>
                                <label for='name' class='form-label'>Name</label>
                                <input type='text' class='form-control' id='name' name='name' required>
                                <div id='nameError' class='text-danger d-none'>Name is required and cannot contain numbers.</div>
                            </div>

                            <div class='col-md-6 mt-3'>
                                <label for='phone' class='form-label'>Phone Number</label>
                                <input type='text' class='form-control' id='phone' name='phone' required>
                                <div id='phoneError' class='text-danger d-none'>Please enter a valid phone number (11 digits).</div>
                            </div>

                            <div class='col-md-6 mt-3'>
                                <label for='email' class='form-label'>Email</label>
                                <input type='email' class='form-control' id='email' name='email' required>
                                <div id='emailError' class='text-danger d-none'>Please enter a valid email address.</div>
                            </div>

                            <div class='col-md-6 mt-3'>
                                <label for='address' class='form-label'>Address</label>
                                <input type="text" class='form-control' id='address' name='address' required>
                                <div id='addressError' class='text-danger d-none'>Address is required.</div>
                            </div>

                            <div class='col-md-6 mt-3'>
                                <label for='petName' class='form-label'>Pet Name</label>
                                <input type='text' class='form-control' id='petName' name='petName' required>
                                <div id='petNameError' class='text-danger d-none'>Pet name is required.</div>
                            </div>

                            <div class='col-md-6 mt-3'>
                                <label for='petType' class='form-label'>Pet Type</label>
                                <input type='text' class='form-control' id='petType' name='petType' required>
                                <div id='petTypeError' class='text-danger d-none'>Pet type is required.</div>
                            </div>

                            <div class='col-md-6 mt-3'>
                                <label for='breed' class='form-label'>Breed</label>
                                <input type='text' class='form-control' id='petBreed' name='petBreed' required>
                                <div id='petBreedError' class='text-danger d-none'>Breed is required.</div>
                            </div>

                            <div class='col-md-6 mt-3'>
                                <label for='info' class='form-label'>Additional Information</label>
                                <textarea class='form-control' id='info' name='info'></textarea>
                                <div id='infoError' class='text-danger d-none'>additional information is required.</div>
                            </div>

                            <div class='col-md-6 mt-3'>
                                <label for='reason' class='form-label'>Reason to Adopt</label>
                                <textarea class='form-control' id='reason' name='reason'></textarea>
                                <div id='reasonError' class='text-danger d-none'>This is required.</div>
                            </div>

                            <div class='col-md-6 mt-3'>
                                <label for='experience' class='form-label'>Experience with Pets</label>
                                <textarea class='form-control' id='experience' name='experience'></textarea>
                                <div id='experienceError' class='text-danger d-none'>This is required.</div>
                            </div>

                            <div class="col-md-12 mt-4 text-center">
                                <button type="button" id="submitForm" class="btn btn-primary">Submit</button>
                            </div>
                        </form>

                        <div id="submitFeedback" class="alert d-none mt-3"></div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Success Modal -->
        <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="successModalLabel">Success</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">x</button>
                    </div>
                    <div class="modal-body">
                        Your form has been submitted successfully!
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include('./script.php'); ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
   document.getElementById("submitForm").addEventListener("click", function () {
    let isValid = true;

    const requiredFields = ['name', 'phone', 'email', 'address', 'petName', 'petType', 'petBreed','info','reason','experience'];
    requiredFields.forEach(field => {
        const value = document.getElementById(field).value.trim();
        const errorDiv = document.getElementById(`${field}Error`);
        if (!value) {
            errorDiv.classList.remove('d-none');
            isValid = false;
        } else {
            errorDiv.classList.add('d-none');
        }
    });

    const phone = document.getElementById('phone').value.trim();
    if (!/^\d{11}$/.test(phone)) {
        document.getElementById('phoneError').classList.remove('d-none');
        isValid = false;
    }

    if (isValid) {
        const formData = new FormData(document.getElementById("adoptForm"));

        $.ajax({
            url: "adoption-process.php",
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                const res = JSON.parse(response);
                if (res.status === "success") {
                    $("#successModal").modal("show");
                    document.getElementById("adoptForm").reset();
                } else {
                    alert("Errors: " + res.errors.join("\n"));
                }
            },
            error: function (xhr, status, error) {
                alert("An error occurred: " + error);
            },
        });
    }
});

</script>
</body>
</html> 
