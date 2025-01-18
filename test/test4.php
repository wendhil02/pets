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
    const qrCodeCanvas = document.getElementById('qrcodeCanvas');
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
            const { name, email } = data.data;
            const qrData = `Name: ${name}\nEmail: ${email}`;

            // Generate QR Code and render in canvas
            QRCode.toCanvas(qrCodeCanvas, qrData, { width: 200 }, function (error) {
              if (error) {
                console.error('Error generating QR code:', error);
              } else {
                console.log('QR Code rendered successfully!');

                // Generate downloadable QR code as data URL
                QRCode.toDataURL(qrData, { width: 200 }, function (err, url) {
                  if (!err) {
                    downloadLink.href = url; // Set download link
                  }
                });

                // Set "View Contents" link to open the QR code data in a new tab
                const encodedData = encodeURIComponent(qrData);
                viewContentsLink.href = `data:text/plain;charset=utf-8,${encodedData}`;

                // Show the modal
                qrModal.show();
              }
            });
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

To connect the registration form to a MySQL database using PHP, you need to process the form data, save it in the database, and then dynamically generate the QR code based on that data. Below is an enhanced implementation that integrates PHP and MySQL with the registration form.

---

### **Steps:**

1. **Database Setup**:
   - Create a MySQL database and a table to store the registration data.

2. **Backend (PHP)**:
   - Process the form submission using PHP.
   - Insert the form data into the database.
   - Return the data to the frontend for QR code generation and display.

3. **Frontend (HTML/JavaScript)**:
   - Use JavaScript to fetch the response from PHP and dynamically generate the QR code.

---

### **Database Schema Example**

Run the following SQL script to create the database and table:

```sql
CREATE DATABASE registration;

USE registration;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

---

### **PHP Backend Script (process_registration.php)**

Save this file as `process_registration.php`:

```php
<?php
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
        // Respond with success and the submitted data
        echo json_encode([
            'status' => 'success',
            'message' => 'Registration successful!',
            'data' => [
                'name' => $name,
                'email' => $email
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

### **Updated Frontend with PHP Integration**

```html
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
    const qrCodeCanvas = document.getElementById('qrcodeCanvas');
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
            const { name, email } = data.data;
            const qrData = `Name: ${name}\nEmail: ${email}`;

            // Generate QR Code and render in canvas
            QRCode.toCanvas(qrCodeCanvas, qrData, { width: 200 }, function (error) {
              if (error) {
                console.error('Error generating QR code:', error);
              } else {
                console.log('QR Code rendered successfully!');

                // Generate downloadable QR code as data URL
                QRCode.toDataURL(qrData, { width: 200 }, function (err, url) {
                  if (!err) {
                    downloadLink.href = url; // Set download link
                  }
                });

                // Set "View Contents" link to open the QR code data in a new tab
                const encodedData = encodeURIComponent(qrData);
                viewContentsLink.href = `data:text/plain;charset=utf-8,${encodedData}`;

                // Show the modal
                qrModal.show();
              }
            });
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


<!---

### **How It Works:**

1. **Frontend:**
   - The form collects user data and sends it to the backend via a `fetch` API call.

2. **Backend:**
   - PHP receives the data, validates it, and stores it in the MySQL database.
   - It returns a JSON response containing the success status and the submitted data.

3. **QR Code Generation:**
   - JavaScript uses the response data to generate the QR code and set up the modal with the QR code, download link, and "View Contents" link.

4. **Error Handling:**
   - If the database operation fails, an error message is shown in the alert box.

---

Would you like assistance setting this up on your local server?




-->