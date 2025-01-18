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
    const qrCodeCanvas = document.getElementById('qrcodeCanvas'); // No longer needed if displaying an image
    const qrCodeImage = document.createElement('img'); // Create an image element for the QR code
    const downloadLink = document.getElementById('downloadQR');
    const viewContentsLink = document.getElementById('viewContents');
    const alertBox = document.getElementById('alert');

    form.addEventListener('submit', function (e) {
        e.preventDefault();

        // Collect form data
        const formData = new FormData(form);

        // Send data to PHP backend
        fetch('process_registration.php', {
            method: 'POST',
            body: formData,
        })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    const { name, email, qrcode } = data.data;
                    const qrData = `Name: ${name}\nEmail: ${email}`;

                    // Show the QR code image in the modal
                    qrCodeImage.src = qrcode; // Path to the QR code image returned from the backend
                    qrCodeImage.alt = 'Generated QR Code';
                    qrCodeImage.className = 'img-fluid mt-3';
                    document.querySelector('.modal-body').appendChild(qrCodeImage);

                    // Set download link
                    downloadLink.href = qrcode;
                    downloadLink.download = 'qrcode.png';

                    // Set "View Contents" link to open the QR code data in a new tab
                    const encodedData = encodeURIComponent(qrData);
                    viewContentsLink.href = `data:text/plain;charset=utf-8,${encodedData}`;

                    // Show the modal
                    qrModal.show();
                } else {
                    // Show error message
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


<!--

To store the generated QR code image in a folder within your project, you'll need to use the QR code library on the server side (in PHP) to generate and save the QR code as an image file. This file can then be served back to the user or saved for later use.

Here’s how you can modify the project to save the QR code image to a folder:

---

### **Steps to Save the QR Code on the Server**

1. **Install PHP QR Code Library**  
   Use the `phpqrcode` library to generate and save QR codes on the server. You can download the library from [PHP QR Code GitHub](https://github.com/chillerlan/php-qrcode).

   Save the `phpqrcode.php` file into a folder like `libs/` within your project directory.

---

### **Updated Backend Script (`process_registration.php`)**

```php
<?php
// Include the PHP QR Code library
require_once 'libs/phpqrcode.php';

// Database connection
$host = 'localhost';
$username = 'root';
$password = ''; // Replace with your MySQL password
$database = 'registration';

$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die(json_encode(['status' => 'error', 'message' => 'Database connection failed: ' . $conn->connect_error]));
}

// Process the form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];

    // Validate data
    if (empty($name) || empty($email)) {
        echo json_encode(['status' => 'error', 'message' => 'Name and email are required!']);
        exit;
    }

    // Insert data into the database
    $stmt = $conn->prepare("INSERT INTO users (name, email) VALUES (?, ?)");
    $stmt->bind_param("ss", $name, $email);

    if ($stmt->execute()) {
        // Generate the QR Code data
        $qrData = "Name: $name\nEmail: $email";

        // Set the path to save the QR code image
        $qrFolder = 'qrcodes/';
        if (!is_dir($qrFolder)) {
            mkdir($qrFolder, 0777, true); // Create the directory if it doesn't exist
        }

        $qrFileName = $qrFolder . uniqid('qrcode_', true) . '.png'; // Unique file name

        // Generate and save the QR code image
        QRcode::png($qrData, $qrFileName, QR_ECLEVEL_L, 10);

        // Respond with success, including the saved QR code path
        echo json_encode([
            'status' => 'success',
            'message' => 'Registration successful!',
            'data' => [
                'name' => $name,
                'email' => $email,
                'qrcode' => $qrFileName
            ]
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error: ' . $stmt->error]);
    }

    $stmt->close();
}

$conn->close();
?>
```

---

### **Updated Frontend Script**

Update the frontend JavaScript to handle the QR code image path returned by the backend and display the image in the modal.

```html
<script>
    const form = document.getElementById('registrationForm');
    const qrModal = new bootstrap.Modal(document.getElementById('qrModal'));
    const qrCodeCanvas = document.getElementById('qrcodeCanvas'); // No longer needed if displaying an image
    const qrCodeImage = document.createElement('img'); // Create an image element for the QR code
    const downloadLink = document.getElementById('downloadQR');
    const viewContentsLink = document.getElementById('viewContents');
    const alertBox = document.getElementById('alert');

    form.addEventListener('submit', function (e) {
        e.preventDefault();

        // Collect form data
        const formData = new FormData(form);

        // Send data to PHP backend
        fetch('process_registration.php', {
            method: 'POST',
            body: formData,
        })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    const { name, email, qrcode } = data.data;
                    const qrData = `Name: ${name}\nEmail: ${email}`;

                    // Show the QR code image in the modal
                    qrCodeImage.src = qrcode; // Path to the QR code image returned from the backend
                    qrCodeImage.alt = 'Generated QR Code';
                    qrCodeImage.className = 'img-fluid mt-3';
                    document.querySelector('.modal-body').appendChild(qrCodeImage);

                    // Set download link
                    downloadLink.href = qrcode;
                    downloadLink.download = 'qrcode.png';

                    // Set "View Contents" link to open the QR code data in a new tab
                    const encodedData = encodeURIComponent(qrData);
                    viewContentsLink.href = `data:text/plain;charset=utf-8,${encodedData}`;

                    // Show the modal
                    qrModal.show();
                } else {
                    // Show error message
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
```

---

### **Folder Structure**

Your project folder should look like this:

```
/project-folder
├── /libs
│   └── phpqrcode.php
├── /qrcodes
│   └── (QR code images will be saved here)
├── process_registration.php
├── index.html (or registration_form.html)
```

---

### **How It Works**

1. **Backend (PHP):**
   - The `QRcode::png()` function from the `phpqrcode` library generates the QR code image and saves it in the `/qrcodes` folder.
   - The file path is returned to the frontend in the JSON response.

2. **Frontend (JavaScript):**
   - The returned file path is used to set the `src` attribute of an `<img>` tag to display the QR code in the modal.
   - The same path is set as the `href` for the download link.

3. **Generated QR Codes:**
   - Each QR code is saved in the `/qrcodes` folder with a unique file name (e.g., `qrcode_64f1e3d8c08.png`).

4. **Download & View Contents:**
   - The "Download" button allows the user to download the QR code image.
   - The "View Contents" button opens the QR code data in a new browser tab.

---

Would you like to expand this further with additional features like email notifications or an admin panel to manage the registrations?

-->