<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>QR Code with View Contents</title>
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
          <canvas id="qrcodeCanvas"></canvas> <!-- QR Code rendered here -->
          <a id="downloadQR" class="btn btn-success mt-3" download="qrcode.png">Download QR Code</a>
          <a id="viewContents" class="btn btn-secondary mt-3" target="_blank">View QR Contents</a>
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
    const viewContentsLink = document.getElementById('viewContents');

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

          // Set the "View Contents" link to open the QR code data in a new tab
          const encodedData = encodeURIComponent(qrData); // Encode special characters
          viewContentsLink.href = `data:text/plain;charset=utf-8,${encodedData}`;

          // Show the modal
          qrModal.show();
        }
      });
    });
  </script>
</body>
</html>

<!--

To allow the user to view the contents of the QR code in a new tab (using `target="_blank"`), we can add a "View Contents" button in the modal. This button will generate a link that opens the QR code's contents (e.g., the text or data encoded) in a new browser tab.

Here’s how to enhance the implementation:

---

### **Updated Code: View QR Code Contents in a New Tab**

```html
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>QR Code with View Contents</title>
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
          <canvas id="qrcodeCanvas"></canvas> <!-- QR Code rendered here -->
          <a id="downloadQR" class="btn btn-success mt-3" download="qrcode.png">Download QR Code</a>
          <a id="viewContents" class="btn btn-secondary mt-3" target="_blank">View QR Contents</a>
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
    const viewContentsLink = document.getElementById('viewContents');

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

          // Set the "View Contents" link to open the QR code data in a new tab
          const encodedData = encodeURIComponent(qrData); // Encode special characters
          viewContentsLink.href = `data:text/plain;charset=utf-8,${encodedData}`;

          // Show the modal
          qrModal.show();
        }
      });
    });
  </script>
</body>
</html>


<!---

### **Explanation of New Features:**

1. **"View QR Contents" Button:**
   - A new `<a>` tag with `id="viewContents"` is added to the modal.
   - Its `href` is dynamically set to a `data:text/plain` URL containing the QR code's content.
   - The `target="_blank"` attribute ensures it opens in a new tab.

2. **Encoding the QR Data:**
   - The `encodeURIComponent` function is used to properly encode the QR code's content (e.g., handling special characters like `\n` or spaces).
   - This ensures the QR code data can be viewed safely in a new tab.

3. **Behavior:**
   - When the "View QR Contents" button is clicked, the browser opens a new tab showing the raw text data of the QR code.

---

### **How It Works in Practice:**

1. **Form Submission:**
   - User submits the form.
   - QR code is generated and displayed in the modal.

2. **Download Button:**
   - Allows users to download the QR code as a PNG image.

3. **View Contents Button:**
   - Clicking this button opens a new tab with the contents of the QR code displayed as plain text.

---

### **Example Output in New Tab:**
For the following input:
- **Name:** John Doe
- **Email:** john.doe@example.com

The new tab will display:
```
Name: John Doe
Email: john.doe@example.com
```

---

Would you like further enhancements, such as backend integration or additional customization?



-->