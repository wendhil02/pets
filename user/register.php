<?php
include('./dbconn/config.php');
include('./dbconn/authentication.php');
?>

<!DOCTYPE html>
<html lang='en'>

<head>
    <?php include('./disc/partials/header.php'); ?>
    <script src="https://cdn.jsdelivr.net/npm/qrcode/build/qrcode.min.js"></script>

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
            <div class='container'>
                <div class='form-container'>
                    <h4 class='form-title'>Pet Registration Form</h4>
                    <form id='regForm' enctype='multipart/form-data' method='POST'>
                        <div class='row'>
                            <div class='col-md-6 form-group'>
                                <label for='name' class='form-label'>Name</label>
                                <input type='text' class='form-control' id='name' name='name' required>
                                <div id='nameError' class='text-danger d-none'>Name is required.</div>
                            </div>
                            <div class='col-md-6 form-group'>
                                <label for='phone' class='form-label'>Phone Number</label>
                                <input type='text' class='form-control' id='phone' name='phone' required>
                                <div id='phoneError' class='text-danger d-none'>Phone Number is required.</div>
                            </div>
                            <div class='col-md-6 form-group'>
                                <label for='email' class='form-label'>Email</label>
                                <input type='email' class='form-control' id='email' name='email' required>
                                <div id='emailError' class='text-danger d-none'>Email is required.
                                </div>
                            </div>
                            <div class='col-md-6 form-group'>
                                <label for='address' class='form-label'>Address</label>
                                <textarea class='form-control' id='address' name='address' required></textarea>
                                <div id='addressError' class='text-danger d-none'>Address is required.</div>
                            </div>
                            <div class='col-md-6 form-group'>
                                <label for='petName' class='form-label'>Pet Name</label>
                                <input type='text' class='form-control' id='petName' name='petName' required>
                                <div id='petNameError' class='text-danger d-none'>Pet name is required.</div>
                            </div>
                            <div class='col-md-6 form-group'>
                                <label for='petAge' class='form-label'>Pet Age</label>
                                <input type='number' class='form-control' id='petAge' name='petAge' required>
                                <div id='petAgeError' class='text-danger d-none'>Pet age is required.
                                </div>
                            </div>
                            <div class='col-md-6 form-group'>
                                <label for='petBreed' class='form-label'>Breed</label>
                                <input type='text' class='form-control' id='petBreed' name='petBreed' required>
                                <div id='petBreedError' class='text-danger d-none'>Pet Breed is required.</div>
                            </div>
                            <div class='col-md-6 form-group'>
                                <label for='info' class='form-label'>Additional Information</label>
                                <textarea class='form-control' id='info' name='info'></textarea>
                                <div id='infoError' class='text-danger d-none'>additional information is required.</div>
                            </div>
                            <div class='col-md-6 form-group'>
                                <label for='petImage' class='form-label'>Pet Image</label>
                                <input type='file' class='form-control' id='petImage' name='petImage' accept='image/*' required>
                                <div id='petImageError' class='text-danger d-none'>Please upload is required.</div>
                            </div>
                            <div class='col-md-6 form-group'>
                                <label for='vaccineImage' class='form-label'>Vaccine Record Image</label>
                                <input type='file' class='form-control' id='vaccineImage' name='vaccineImage' accept='image/*' required>
                                <div id='vaccineImageError' class='text-danger d-none'>Upload is required.</div>
                            </div>
                            <div class='col-md-12 form-group text-center'>
                                <button type='button' id='submitForm' class='btn btn-primary btn-submit'>Submit</button>
                            </div>
                        </div>
                    </form>
                    <div id='submitFeedback' class='alert d-none mt-3'></div>
                </div>
            </div>
        </main>

        <div class="modal fade" id="qrModal" tabindex="-1" aria-labelledby="qrModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="qrModalLabel">QR Code</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">x</button>
                    </div>
                    <div class="modal-body text-center">
                        <canvas id="qrcodeCanvas" class="mb-3"></canvas>
                        <div id="qrActions">
                            <a id="downloadQR" class="btn btn-success me-2 my-2" download="qrcode.png">Download QR
                                Code</a>
                            <a id="viewContents" class="btn btn-info me-2 my-2" target="_blank">View Uploaded Data</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>


    </div>
    <?php include('./script.php'); ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Form Submission
        document.getElementById('submitForm').addEventListener('click', function () {
    if (validateForm()) {
        const form = document.getElementById('regForm');
        const formData = new FormData(form);

        fetch('register-process.php', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                const feedback = document.getElementById('submitFeedback');
                feedback.classList.remove('d-none', 'alert-success', 'alert-danger');
                feedback.classList.add(data.status === 'success' ? 'alert-success' : 'alert-danger');
                feedback.textContent = data.message;
                feedback.style.opacity = "1";

                // Fade out effect after 3 seconds
                setTimeout(() => {
                    feedback.style.transition = "opacity 1s ease-out";
                    feedback.style.opacity = "0";

                    // Hide after transition
                    setTimeout(() => {
                        feedback.classList.add('d-none');
                    }, 1000);
                }, 3000);

                if (data.status === 'success') {
                    form.reset();
                    const qrCodeCanvas = document.getElementById('qrcodeCanvas');
                    QRCode.toCanvas(qrCodeCanvas, data.qrUrl, function (error) {
                        if (error) {
                            console.error('QR Code Generation Error:', error);
                        }
                    });

                    const downloadQR = document.getElementById('downloadQR');
                    const viewContents = document.getElementById('viewContents');

                    downloadQR.href = qrCodeCanvas.toDataURL();
                    viewContents.href = data.qrUrl;

                    // Show the modal
                    const qrModal = new bootstrap.Modal(document.getElementById('qrModal'));
                    qrModal.show();
                }
            })
            .catch(error => {
                const feedback = document.getElementById('submitFeedback');
                feedback.classList.remove('d-none');
                feedback.classList.add('alert-danger');
                feedback.textContent = 'An error occurred during the submit process';
                feedback.style.opacity = "1";

                // Fade out effect after 3 seconds
                setTimeout(() => {
                    feedback.style.transition = "opacity 1s ease-out";
                    feedback.style.opacity = "0";

                    setTimeout(() => {
                        feedback.classList.add('d-none');
                    }, 1000);
                }, 3000);
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
            const petAge = document.getElementById('petAge').value.trim();
            const petBreed = document.getElementById('petBreed').value.trim();
            const info = document.getElementById('info').value.trim();
            const petImage = document.getElementById('petImage').files[0];
            const vaccineImage = document.getElementById('vaccineImage').files[0];

            const nameError = document.getElementById('nameError');
            if (!name || /\d/.test(name)) {

                nameError.classList.remove('d-none');
                isValid = false;
            } else {
                nameError.classList.add('d-none');
            }

            // Phone Validation
            const phoneError = document.getElementById('phoneError');
            if (!/^[0-9]{11}$/.test(phone)) {

                phoneError.classList.remove('d-none');
                isValid = false;
            } else {
                phoneError.classList.add('d-none');
            }

            // Email Validation
            const emailError = document.getElementById('emailError');
            const emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
            if (!emailPattern.test(email)) {

                emailError.classList.remove('d-none');
                isValid = false;
            } else {
                emailError.classList.add('d-none');
            }

            // Address Validation
            const addressError = document.getElementById('addressError');
            if (!address) {

                addressError.classList.remove('d-none');
                isValid = false;
            } else {
                addressError.classList.add('d-none');
            }

            // Pet Name Validation
            const petNameError = document.getElementById('petNameError');
            if (!petName) {

                petNameError.classList.remove('d-none');
                isValid = false;
            } else {
                petNameError.classList.add('d-none');
            }

            // Pet Age Validation
            const petAgeError = document.getElementById('petAgeError');
            if (petAge <= 0) {

                petAgeError.classList.remove('d-none');
                isValid = false;
            } else {
                petAgeError.classList.add('d-none');
            }
            // Breed Validation
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
            // File Validation
            const petImageError = document.getElementById('petImageError');
            const vaccineImageError = document.getElementById('vaccineImageError');

            if (!petImage || !petImage.type.startsWith('image/')) {

                petImageError.classList.remove('d-none');
                isValid = false;
            } else {
                petImageError.classList.add('d-none');
            }

            if (!vaccineImage || !vaccineImage.type.startsWith('image/')) {

                vaccineImageError.classList.remove('d-none');
                isValid = false;
            } else {
                vaccineImageError.classList.add('d-none');
            }

            return isValid;


        }
    </script>
</body>

</html>