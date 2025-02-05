<?php
include('./dbconn/config.php');
include('./dbconn/authentication.php');
?>

<!DOCTYPE html>
<html lang='en'>

<head>
    <?php include('./disc/partials/header.php'); ?>
</head>

<body class='vertical light'>
    <div class='wrapper'>
        <?php include('./disc/partials/navbar.php'); ?>
        <?php include('./disc/partials/sidebar.php'); ?>

        <main role='main' class='main-content'>
            <!-- Notification header -->
            <?php include('./disc/partials/modal-notif.php') ?>

            <div class='container'>
                <div class='card'>
                    <div class=''>
                        <h4 class='card-title mt-5'>REPORT MISSING</h4>
                    </div>
                    <div class='card-body'>
                        <form id="regForm" class='row ' enctype='multipart/form-data' method='POST'>
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
                                <textarea class='form-control' id='address' name='address' required></textarea>
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
                                <div id='petTypeError' class='text-danger d-none'>Pet age must be a positive number.</div>
                            </div>

                            <div class='col-md-6 mt-3'>
                                <label for='breed' class='form-label'>Breed</label>
                                <input type='text' class='form-control' id='petBreed' name='petBreed' required>
                                <div id='petBreedError' class='text-danger d-none'>Pet Breed is required.</div>
                            </div>

                            <div class='col-md-6 mt-3'>
                                <label for='additional_info' class='form-label'>Additional Information</label>
                                <textarea class='form-control' id='info' name='info'></textarea>
                                <div id='infoError' class='text-danger d-none'>Additional information is required.</div>
                            </div>

                            <div class='col-md-6 mt-3'>
                                <label for='pet_image' class='form-label'>Pet Image</label>
                                <input type='file' class='form-control' id='petImage' name='petImage' accept='image/*' required>
                                <div id='petImageError' class='text-danger d-none'>Please upload a valid image file for Pet Image.</div>
                            </div>

                            <div class="col-md-12 mt-4 align-items-center text-center">
                                <button type="button" id="submitForm" class="p-3 btn btn-primary">Submit</button>
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
    <?php include('./script.php'); ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
   document.getElementById('submitForm').addEventListener('click', function () {
    if (validateForm()) {
        const form = document.getElementById('regForm');
        const formData = new FormData(form);

        fetch('missing-process.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            const feedback = document.getElementById('submitFeedback');
            feedback.classList.remove('d-none');
            feedback.classList.add(data.status === 'success' ? 'alert-success' : 'alert-danger');
            feedback.textContent = data.status === 'success' ? 'Successfully Registered!' : 'Registration Failed!';

            if (data.status === 'success') {
                // Show the success modal
                const successModal = new bootstrap.Modal(document.getElementById('successModal'));
                successModal.show();

                // Reset the form
                form.reset();
            }

            setTimeout(function() {
        feedback.classList.add('d-none');
    }, 3000); 
        })
        .catch(error => {
            const feedback = document.getElementById('submitFeedback');
            feedback.classList.remove('d-none');
            feedback.classList.add('alert-danger');
            feedback.textContent = 'An error occurred during the submit process';
        });
    }
});


        function validateForm() {
            let isValid = true;

            const name = document.getElementById('name').value.trim();
            const phone = document.getElementById('phone').value.trim();
            const email = document.getElementById('email').value.trim();
            const address = document.getElementById('address').value.trim();
            const petName = document.getElementById('petName').value.trim();
            const petType = document.getElementById('petType').value.trim();
            const petBreed = document.getElementById('petBreed').value.trim();
            const info = document.getElementById('info').value.trim();
            const petImage = document.getElementById('petImage').files[0];

            const nameError = document.getElementById('nameError');
            if (!name || /\d/.test(name)) {
                nameError.classList.remove('d-none');
                isValid = false;
            } else {
                nameError.classList.add('d-none');
            }

            const phoneError = document.getElementById('phoneError');
            if (!/^[0-9]{11}$/.test(phone)) {
                phoneError.classList.remove('d-none');
                isValid = false;
            } else {
                phoneError.classList.add('d-none');
            }

            const emailError = document.getElementById('emailError');
            const emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
            if (!emailPattern.test(email)) {
                emailError.classList.remove('d-none');
                isValid = false;
            } else {
                emailError.classList.add('d-none');
            }

            const addressError = document.getElementById('addressError');
            if (!address) {
                addressError.classList.remove('d-none');
                isValid = false;
            } else {
                addressError.classList.add('d-none');
            }

            const petNameError = document.getElementById('petNameError');
            if (!petName) {
                petNameError.classList.remove('d-none');
                isValid = false;
            } else {
                petNameError.classList.add('d-none');
            }

            const petTypeError = document.getElementById('petTypeError');
            if (!petType) {
                petTypeError.classList.remove('d-none');
                isValid = false;
            } else {
                petTypeError.classList.add('d-none');
            }

            const petBreedError = document.getElementById('petBreedError');
            if (!petBreed) {
                petBreedError.classList.remove('d-none');
                isValid = false;
            } else {
                petBreedError.classList.add('d-none');
            }

            const infoError = document.getElementById('infoError');
            if (!info) {
                infoError.classList.remove('d-none');
                isValid = false;
            } else {
                infoError.classList.add('d-none');
            }

            const petImageError = document.getElementById('petImageError');
            if (!petImage || !petImage.type.startsWith('image/')) {
                petImageError.classList.remove('d-none');
                isValid = false;
            } else {
                petImageError.classList.add('d-none');
            }

            return isValid;
        }
    </script>
</body>

</html>
