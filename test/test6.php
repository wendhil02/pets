<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registration with MySQL & QR Code</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <!-- QR Code Library -->
  <script src="https://cdn.jsdelivr.net/npm/qrcode/build/qrcode.min.js"></script>
</head>
<body>
  <div class="container mt-5">
    <h2 class="text-center">Registration Form</h2>
    <form id="registrationForm" action="" enctype="multipart/form-data">
  <div class="mb-3">
    <label for="name" class="form-label">Name</label>
    <input type="text" class="form-control" id="name" name="name" required>
  </div>
  <div class="mb-3">
    <label for="email" class="form-label">Email</label>
    <input type="email" class="form-control" id="email" name="email" required>
  </div>
  <div class="mb-3">
    <label for="image" class="form-label">Upload Image</label>
    <input type="file" class="form-control" id="image" name="image" accept="image/*" required>
  </div>
  <button type="submit" class="btn btn-primary">Submit</button>
</form>


    <div id="alert" class="alert d-none mt-3"></div>
  </div>

  <!-- Modal for Viewing QR Code -->
  <div class="modal fade" id="qrModal" tabindex="-1" aria-labelledby="qrModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="qrModalLabel">Your QR Code</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body text-center">
          <canvas id="qrcodeCanvas"></canvas> <!-- QR Code rendered here -->

        </div>
        <div class="modal-footer">
           <a id="downloadQR" class="btn btn-success " download="qrcode.png">Download QR Code</a>
           <a id="viewContents" class="btn btn-secondary" target="_blank">View QR Contents</a>
          <button type="button" class="btn btn-secondary " data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
  <script>
  const form = document.getElementById('registrationForm');
    const alertBox = document.getElementById('alert');
    const qrModal = new bootstrap.Modal(document.getElementById('qrModal'));
    const downloadLink = document.getElementById('downloadQR');
    const viewContentsLink = document.getElementById('viewContents');

    form.addEventListener('submit', function (e) {
        e.preventDefault(); // Prevent default form submission

        const formData = new FormData(form); // Collect form data including the image

        // Make a POST request to the backend
        fetch('process_registration.php', {
            method: 'POST',
            body: formData,
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                const { name, email, image, qrcode } = data.data;

                // Create the QR code image
                const qrCodeImage = document.createElement('img');
                qrCodeImage.src = qrcode; // Set the QR code image source
                qrCodeImage.alt = 'Generated QR Code';
                qrCodeImage.className = 'img-fluid mt-3';

                const modalBody = document.querySelector('.modal-body');
                modalBody.innerHTML = ''; // Clear existing modal content
                modalBody.appendChild(qrCodeImage); // Add the QR code to the modal

                // Set the download link for the QR code
                downloadLink.href = qrcode;
                downloadLink.download = 'qrcode.png';

                // Set the "View QR Contents" link to open the data inside the QR code
                const qrContents = `Name: ${name}\nEmail: ${email}\nImage Path: ${image}`;
                const encodedContents = encodeURIComponent(qrContents);
                viewContentsLink.href = `data:text/plain;charset=utf-8,${encodedContents}`;
                viewContentsLink.textContent = 'View QR Contents';

                // Show the modal
                qrModal.show();
            } else {
                // Show an error alert if the backend response indicates failure
                alertBox.className = 'alert alert-danger';
                alertBox.textContent = data.message;
                alertBox.classList.remove('d-none');
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    });
  </script>

</body>
</html>


