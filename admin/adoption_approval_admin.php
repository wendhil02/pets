<?php
include('./dbconn/authentication.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include('./disc/partial/header.php'); ?>
    <style>
      body {
        font-family: 'Poppins', sans-serif;
        margin: 0;
        padding: 0;
        background-color: #f4f4f9;
      }
      .main-content {
        padding: 20px;
        margin: 20px;
        background: white;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      }
    </style>
</head>
<body class="vertical light">
  <div class="wrapper">
    <?php include('./disc/partial/navbar.php'); ?>
    <?php include('./disc/partial/sidebar.php'); ?>
    <main class="main-content">
      <div class="box">
        <div class="filter-container d-flex justify-content-between align-items-center p-3  border-bottom">
          <h3 class="title">Adoption Management</h3>
        </div>
        <div class="table-responsive">
          <table class="table table-striped table-hover text-center">
            <thead class="table-dark">
              <tr>
                <th>User Name</th>
                <th>View</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <?php
              include('./dbconn/config.php');
              $sql = "SELECT * FROM adoption WHERE approved = 0";
              $result = $conn->query($sql);
              if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                  echo "<tr data-id='{$row['pet_id']}' data-info='" . htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8') . "'>
                          <td>{$row['owner']}</td>
                          <td><button class='btn btn-info' onclick='viewDetails(this)'>View</button></td>
                          <td>
                            <form action='adoption-process.php' method='POST' class='d-inline'>
                              <input type='hidden' name='pet_id' value='{$row["pet_id"]}'>
                              <button type='submit' name='action' value='approve' class='btn btn-success'>Approve</button>
                            </form>
                            <form action='adoption-process.php' method='POST' class='d-inline'>
                              <input type='hidden' name='pet_id' value='{$row["pet_id"]}'>
                              <button type='submit' name='action' value='reject' class='btn btn-danger'>Reject</button>
                            </form>
                          </td>
                        </tr>";
                }
              } else {
                echo '<tr><td colspan="3">No records found.</td></tr>';
              }
              $conn->close();
              ?>
            </tbody>
          </table>
        </div>
      </div>
    </main>
  </div>

  <!-- Modal for Viewing Pet Details -->
  <div id="viewModal" class="modal fade" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Pet Details</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body" id="modal-details">
          <!-- Content will be dynamically added -->
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

  <?php include('./disc/partial/script.php'); ?>
  <script>
    function viewDetails(button) {
      var row = button.closest("tr");
      var recordStr = row.getAttribute("data-info");
      if (recordStr) {
        var data = JSON.parse(recordStr);
        var detailsHtml = `
          <p><strong>Pet Name:</strong> ${data.pet_name || 'N/A'}</p>
          <p><strong>Pet Age:</strong> ${data.pet_age || 'N/A'}</p>
          <p><strong>Pet Breed:</strong> ${data.pet_breed || 'N/A'}</p>
          <p><strong>Information:</strong> ${data.pet_info || 'N/A'}</p>
          <p><strong>User Name:</strong> ${data.owner || 'N/A'}</p>
          <p><strong>Email/Owner:</strong> ${data.email || 'N/A'}</p>
          <p><strong>Pet Image:</strong> 
        ${data.pet_image 
          ? `<br/><img src="data:image/jpeg;base64,${data.pet_image}" alt="Pet Image" class="img-fluid rounded">` 
          : 'N/A'}
      </p>
          <p><strong>Created At:</strong> ${data.created_at || 'N/A'}</p>
        `;
        document.getElementById("modal-details").innerHTML = detailsHtml;
        var viewModal = new bootstrap.Modal(document.getElementById("viewModal"));
        viewModal.show();
      }
    }
  </script>
</body>
</html>
