<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>QR Code View in Modal</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <!-- QR Code Library -->
  <script src="https://cdn.jsdelivr.net/npm/qrcode/build/qrcode.min.js"></script>
</head>
<body>
  <div class="container mt-5">
    <h2 class="text-center">Registration Form</h2>
    <form id="registrationForm">
      <div class="mb-3">
        <label for="name" class="form-label">Name</label>
        <input type="text" class="form-control" id="name" name="name" required>
      </div>
      <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" class="form-control" id="email" name="email" required>
      </div>
      <button type="submit" class="btn btn-primary">Submit</button>
    </form>
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
          <canvas id="qrcodeCanvas"></canvas> <!-- QR Code will be rendered here -->
          <a id="downloadQR" class="btn btn-success mt-3" download="qrcode.png">Download QR Code</a>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

  <script>
    const form = document.getElementById('registrationForm');
    const qrModal = new bootstrap.Modal(document.getElementById('qrModal'));
    const qrCodeCanvas = document.getElementById('qrcodeCanvas');
    const downloadLink = document.getElementById('downloadQR');

    form.addEventListener('submit', function (e) {
      e.preventDefault();

      // Collect form data
      const name = document.getElementById('name').value;
      const email = document.getElementById('email').value;

      // Create QR Code data
      const qrData = `Name: ${name}\nEmail: ${email}`;

      // Generate QR Code and render in canvas
      QRCode.toCanvas(qrCodeCanvas, qrData, { width: 200 }, function (error) {
        if (error) {
          console.error('Error generating QR code:', error);
        } else {
          console.log('QR Code rendered successfully!');

          // Generate downloadable QR code as data URL
          QRCode.toDataURL(qrData, { width: 200 }, function (err, url) {
            if (err) {
              console.error('Error generating QR code URL:', err);
            } else {
              downloadLink.href = url; // Set the href attribute for download
            }
          });

          // Show the modal
          qrModal.show();
        }
      });
    });
  </script>
</body>
</html>


<!--
To display the QR code directly in the modal (as a "view" within the modal), we can enhance the previous implementation. Instead of generating the QR code dynamically within the form submission, we will focus on rendering it inside the modal for better visibility.

Here’s the updated example, including **QR code viewing directly in the modal**:

---

### **Complete Code: Registration Form with QR Code View in Modal**

```html
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>QR Code View in Modal</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <!-- QR Code Library -->
  <script src="https://cdn.jsdelivr.net/npm/qrcode/build/qrcode.min.js"></script>
</head>
<body>
  <div class="container mt-5">
    <h2 class="text-center">Registration Form</h2>
    <form id="registrationForm">
      <div class="mb-3">
        <label for="name" class="form-label">Name</label>
        <input type="text" class="form-control" id="name" name="name" required>
      </div>
      <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" class="form-control" id="email" name="email" required>
      </div>
      <button type="submit" class="btn btn-primary">Submit</button>
    </form>
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
          <canvas id="qrcodeCanvas"></canvas> <!-- QR Code will be rendered here -->
          <a id="downloadQR" class="btn btn-success mt-3" download="qrcode.png">Download QR Code</a>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

  <script>
    const form = document.getElementById('registrationForm');
    const qrModal = new bootstrap.Modal(document.getElementById('qrModal'));
    const qrCodeCanvas = document.getElementById('qrcodeCanvas');
    const downloadLink = document.getElementById('downloadQR');

    form.addEventListener('submit', function (e) {
      e.preventDefault();

      // Collect form data
      const name = document.getElementById('name').value;
      const email = document.getElementById('email').value;

      // Create QR Code data
      const qrData = `Name: ${name}\nEmail: ${email}`;

      // Generate QR Code and render in canvas
      QRCode.toCanvas(qrCodeCanvas, qrData, { width: 200 }, function (error) {
        if (error) {
          console.error('Error generating QR code:', error);
        } else {
          console.log('QR Code rendered successfully!');

          // Generate downloadable QR code as data URL
          QRCode.toDataURL(qrData, { width: 200 }, function (err, url) {
            if (err) {
              console.error('Error generating QR code URL:', err);
            } else {
              downloadLink.href = url; // Set the href attribute for download
            }
          });

          // Show the modal
          qrModal.show();
        }
      });
    });
  </script>
</body>
</html>


<!---

### **Key Features:**

1. **QR Code View in Modal:**
   - The QR code is dynamically generated and displayed in a `<canvas>` element inside the modal when the form is submitted.

2. **QR Code Download:**
   - The "Download QR Code" button allows the user to download the QR code as a PNG image.

3. **Modal Implementation:**
   - The modal is triggered programmatically using Bootstrap’s JavaScript utilities (`new bootstrap.Modal`).

4. **Dynamic QR Code Generation:**
   - The `qrcode.js` library generates a QR code based on the form data (name and email).

---

### **How It Works:**

1. **Form Submission:**
   - When the form is submitted, the default action is prevented, and the data is captured using JavaScript.

2. **QR Code Generation:**
   - The `QRCode.toCanvas` method renders the QR code directly onto the `<canvas>` inside the modal.
   - The `QRCode.toDataURL` method generates a data URL for the QR code, which is set as the `href` for the "Download QR Code" button.

3. **Modal Display:**
   - The modal is displayed using `qrModal.show()`.

4. **QR Code Download:**
   - The "Download QR Code" button allows users to download the QR code as an image by linking to the generated data URL.

---

### **Customization Options:**

1. **Modal Styling:**
   - Use Bootstrap classes to style the modal further.

2. **QR Code Options:**
   - Customize the size, error correction level, and colors of the QR code by passing options to `QRCode.toCanvas`.

3. **Backend Integration:**
   - If needed, send the form data to a server (e.g., via an AJAX request) for backend processing or data storage.

Would you like to enhance this further or integrate it with a backend?



-->