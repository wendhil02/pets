<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registration with QR Code</title>
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

  <!-- Modal -->
  <div class="modal fade" id="qrModal" tabindex="-1" aria-labelledby="qrModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="qrModalLabel">Your QR Code</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body text-center">
          <div id="qrcode"></div>
          <a id="downloadQR" class="btn btn-success mt-3" download="qrcode.png">Download QR Code</a>
        </div>
      </div>
    </div>
  </div>

  <script>
    const form = document.getElementById('registrationForm');
    const qrCodeContainer = document.getElementById('qrcode');
    const downloadLink = document.getElementById('downloadQR');

    form.addEventListener('submit', function (e) {
      e.preventDefault();

      // Get form data
      const name = document.getElementById('name').value;
      const email = document.getElementById('email').value;

      // Generate QR Code data
      const qrData = `Name: ${name}\nEmail: ${email}`;
      QRCode.toCanvas(qrCodeContainer, qrData, { width: 200 }, function (error) {
        if (error) console.error(error);
        else console.log('QR Code generated!');
      });

      // Generate QR Code as a downloadable image
      QRCode.toDataURL(qrData, { width: 200 }, function (error, url) {
        if (error) console.error(error);
        downloadLink.href = url; // Set download link
      });

      // Show modal
      const qrModal = new bootstrap.Modal(document.getElementById('qrModal'));
      qrModal.show();
    });
  </script>
</body>
</html>


<!-- How it works
 To create a registration form with a pop-up modal that shows a QR code after submission and allows downloading or viewing the QR code, you can use the following approach. Here's a step-by-step guide:

---

### **Steps to Implement:**

1. **Set Up Your Project:**
   - Include Bootstrap for styling and modal functionality.
   - Include a QR code library like [`qrcode.js`](https://github.com/davidshimjs/qrcodejs) or use a server-side QR code generator in PHP like `phpqrcode`.

2. **Create the Registration Form:**
   - Use HTML and Bootstrap to design the form.

3. **Generate QR Code After Form Submission:**
   - Use JavaScript (client-side) or PHP (server-side) to generate the QR code based on submitted form data.

4. **Display the Modal with the QR Code:**
   - Use Bootstrap's modal to show the QR code and options to download or view it.

5. **Allow QR Code Download:**
   - Provide a button to download the QR code as an image.

---

### **Example Implementation**

#### **HTML + Bootstrap**
```html
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registration with QR Code</title>
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

  <!-- Modal -->
  <div class="modal fade" id="qrModal" tabindex="-1" aria-labelledby="qrModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="qrModalLabel">Your QR Code</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body text-center">
          <div id="qrcode"></div>
          <a id="downloadQR" class="btn btn-success mt-3" download="qrcode.png">Download QR Code</a>
        </div>
      </div>
    </div>
  </div>

  <script>
    const form = document.getElementById('registrationForm');
    const qrCodeContainer = document.getElementById('qrcode');
    const downloadLink = document.getElementById('downloadQR');

    form.addEventListener('submit', function (e) {
      e.preventDefault();

      // Get form data
      const name = document.getElementById('name').value;
      const email = document.getElementById('email').value;

      // Generate QR Code data
      const qrData = `Name: ${name}\nEmail: ${email}`;
      QRCode.toCanvas(qrCodeContainer, qrData, { width: 200 }, function (error) {
        if (error) console.error(error);
        else console.log('QR Code generated!');
      });

      // Generate QR Code as a downloadable image
      QRCode.toDataURL(qrData, { width: 200 }, function (error, url) {
        if (error) console.error(error);
        downloadLink.href = url; // Set download link
      });

      // Show modal
      const qrModal = new bootstrap.Modal(document.getElementById('qrModal'));
      qrModal.show();
    });
  </script>
</body>
</html>


<!---

### **How It Works:**

1. **Form Submission:**
   - When the form is submitted, the JavaScript `submit` event prevents the default action and captures the form data.

2. **QR Code Generation:**
   - The `qrcode.js` library generates a QR code in the `<div>` with `id="qrcode"` based on the form data.
   - It also generates a downloadable data URL for the QR code image, which is linked to the "Download QR Code" button.

3. **Bootstrap Modal:**
   - The QR code is displayed in a Bootstrap modal, which is triggered programmatically using JavaScript.

4. **Download Option:**
   - The QR code can be downloaded as a PNG image using the "Download QR Code" button.

---

### **Additional Enhancements:**
- **Backend Integration:** If needed, submit the form data to a server using AJAX or fetch, and generate the QR code server-side using PHP.
- **Validation:** Add client-side and server-side validation for better security.
- **Styling:** Customize the modal and form to match your project’s theme.

Would you like me to help integrate this into your project or extend it further? 

-->
  