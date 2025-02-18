<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Adoption Management</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      margin: 0;
      padding: 0;
      background-color: #f4f4f9;
    }
    /* General Box Styles */
    .box {
      width: auto;
      height: 600px;
      background-color: transparent;
      border: 0px solid #e5e7eb;
      border-radius: 10px;
      box-shadow: 0px 0px 0px rgb(252, 251, 251);
      padding: 16px;
      margin: 8px;
      overflow: auto;
    }
    .box-container {
      display: flex;
      gap: 16px;
      flex-direction: column;
    }
    /* Sticky Filter Section */
    .filter-container {
      position: sticky;
      top: 0;
      z-index: 10;
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 10px;
      background-color: #fff;
      font-family: Arial, sans-serif;
      border-bottom: 2px solid transparent;
      box-shadow: 0 0px 0px 0px rgb(255, 255, 255);
    }
    .search-container {
      flex: 2;
    }
    .search-bar-wrapper {
      position: relative;
      display: flex;
      align-items: center;
    }
    .fa-search {
      position: absolute;
      left: 10px;
      color: #555;
    }
    .search-bar {
      width: 50%;
      padding: 10px 12px 10px 35px;
      font-size: 1rem;
      border-radius: 5px;
      border: 1px solid #ccc;
      outline: none;
      transition: border-color 0.3s;
    }
    .search-bar:focus {
      border-color: #007bff;
    }
    .category-container {
      flex: 1;
      display: flex;
      justify-content: flex-end;
    }
    .category-filter {
      padding: 8px;
      font-size: 1rem;
      border-radius: 5px;
      border: 1px solid #ccc;
      background-color: #fff;
      outline: none;
      width: 180px;
      transition: border-color 0.3s;
    }
    .category-filter:focus {
      border-color: #007bff;
    }
    .title {
      font-size: 1.25rem;
      font-weight: 600;
      color: #333;
      margin-left: 5px;
      flex: 2;
      text-align: left;
    }
    .filter-container i {
      color: #555;
      font-size: 1.2rem;
      margin-right: 8px;
    }
    /* Table Wrapper */
    .table-wrapper {
      overflow-x: auto;
      scrollbar-width: thin;
      -ms-overflow-style: none;
      transition: opacity 0.3s ease-in-out;
    }
    .table-wrapper::-webkit-scrollbar {
      height: 8px;
      transition: opacity 0.3s ease-in-out;
    }
    .table-wrapper::-webkit-scrollbar-thumb {
      background: rgba(0, 0, 0, 0.3);
      border-radius: 4px;
    }
    .table-wrapper::-webkit-scrollbar-track {
      background: rgba(0, 0, 0, 0.1);
    }
    .table-wrapper::-webkit-scrollbar,
    .table-wrapper::-webkit-scrollbar-thumb {
      opacity: 0;
    }
    .table-wrapper:hover::-webkit-scrollbar,
    .table-wrapper:hover::-webkit-scrollbar-thumb {
      opacity: 1;
    }
    /* Table Styles */
    table {
      width: 100%;
      border-collapse: collapse;
      background-color: white;
      border-radius: 5px;
      overflow: hidden;
    }
    th {
      background-color: rgb(0, 0, 0);
      color: #ffffff;
      text-align: center;
      padding: 12px;
      font-size: 16px;
    }
    td {
      padding: 12px;
      text-align: center;
      border-bottom: 1px solid #e5e7eb;
      color: #374151;
      font-size: 14px;
    }
    tr:last-child td {
      border-bottom: none;
    }
    tbody tr:hover {
      background-color: #f3f4f6;
    }
    /* Button Styles */
    .view-btn, .approve-btn, .reject-btn {
      padding: 8px 16px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      font-size: 14px;
    }
    .view-btn {
      background-color: #17a2b8;
      color: #fff;
    }
    .view-btn:hover {
      background-color: #138496;
    }
    .approve-btn {
      background-color: #28a745;
      color: #fff;
      margin-right: 8px;
    }
    .approve-btn:hover {
      background-color: #218838;
    }
    .reject-btn {
      background-color: #dc3545;
      color: #fff;
    }
    .reject-btn:hover {
      background-color: #c82333;
    }
    /* Modal Styles */
    .modal {
      display: none;
      position: fixed;
      z-index: 1000;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      overflow: auto;
      background-color: rgba(0,0,0,0.5);
      padding: 20px;
    }
    .modal-content {
      background-color: #fff;
      margin: 10% auto;
      padding: 20px;
      border-radius: 8px;
      width: 50%;
      position: relative;
    }
    .modal-content h3 {
      margin-top: 0;
    }
    .close {
      position: absolute;
      top: 10px;
      right: 20px;
      color: #aaa;
      font-size: 24px;
      font-weight: bold;
      cursor: pointer;
    }
    .close:hover {
      color: #000;
    }
  </style>
</head>
<body class="vertical light">
  <?php include('navandside/nav.php'); ?>
  <?php include('navandside/sidebar.php'); ?>
  <?php include('navandside/head.php'); ?>

  <div class="box-container">
    <!-- Box 1 as Table -->
    <?php if(isset($_GET['success'])): ?>
    <div class="alert alert-success">Post approved successfully!</div>
  <?php endif; ?>
  
  <?php if(isset($_GET['rejected'])): ?>
    <div class="alert alert-warning">Post rejected successfully!</div>
  <?php endif; ?>

    <div class="box">
      <div class="filter-container">
        <h3 class="title">Adoption Management</h3>
      </div>
  
      <!-- Table Wrapper for Scroll -->
      <div class="table-wrapper">
      <table id="pet-table">
          <thead>
            <tr>
              <th>User Name</th>
              <th>View</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php
              include('./dbconn/config.php');

              $sql = "SELECT pet_id, pet_name, pet_age, pet_breed, pet_info, pet_image, created_at, mail 
                      FROM adoption WHERE approved = 0";
              $result = $conn->query($sql);
              
              if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                  $jsonData = htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8');
                  $userName = htmlspecialchars($row['mail']);
                  echo "<tr data-record='{$jsonData}'>
                          <td>{$userName}</td>
                          <td><button class='view-btn' onclick='viewDetails(this)'>View</button></td>
                          <td>
                            <form action='approve_adoption.php' method='POST' class='d-inline'>
                              <input type='hidden' name='pet_id' value='{$row["pet_id"]}'>
                              <button type='submit' name='action' value='approve' class='approve-btn'>Approve</button>
                            </form>
                            <form action='approve_adoption.php' method='POST' class='d-inline'>
                              <input type='hidden' name='pet_id' value='{$row["pet_id"]}'>
                              <button type='submit' name='action' value='reject' class='reject-btn'>Reject</button>
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
  </div>

  <!-- Modal for Viewing Adoption Details -->
  <div id="viewModal" class="modal">
    <div class="modal-content">
      <span class="close" onclick="closeModal()">&times;</span>
      <h3>Adoption Details</h3>
      <div id="modal-details">
        <!-- Details will be populated here -->
      </div>
    </div>
  </div>

  <script>
    // Opens the modal and displays the record details.
    function viewDetails(button) {
      var row = button.closest("tr");
      var recordStr = row.getAttribute("data-record");
      if (recordStr) {
        var data = JSON.parse(recordStr);
        var detailsHtml = `
          <p><strong>Pet Name:</strong> ${data.pet_name || 'N/A'}</p>
          <p><strong>Pet Age:</strong> ${data.pet_age || 'N/A'}</p>
          <p><strong>Pet Breed:</strong> ${data.pet_breed || 'N/A'}</p>
          <p><strong>Information:</strong> ${data.pet_info || 'N/A'}</p>
          <p><strong>User Name:</strong> ${data.user_name || 'N/A'}</p>
          <p><strong>Email/Owner:</strong> ${data.mail || 'N/A'}</p>
          <p><strong>Pet Image:</strong> ${
            data.pet_image 
              ? `<br/><img src="${data.pet_image}" alt="Pet Image" style="max-width:200px; height:auto;">` 
              : 'N/A'
          }</p>
          <p><strong>Pet Vaccine:</strong> ${data.pet_vaccine || 'N/A'}</p>
          <p><strong>Created At:</strong> ${data.created_at || 'N/A'}</p>
        `;
        document.getElementById("modal-details").innerHTML = detailsHtml;
        document.getElementById("viewModal").style.display = "block";
      }
    }

    // Closes the modal.
    function closeModal() {
      document.getElementById("viewModal").style.display = "none";
    }

    // Handles the approval action.
    function approveAdoption(button) {
      var row = button.closest("tr");
      var recordStr = row.getAttribute("data-record");
      if (recordStr) {
        var data = JSON.parse(recordStr);
        // Use fetch to post the approval to the server.
        fetch('adoption-process.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({ adoption_id: data.id }) // Ensure your record has an 'id' field.
        })
        .then(response => response.json())
        .then(result => {
          if(result.success) {
            alert("Approved adoption for " + (data.pet_name || 'N/A'));
            // Optionally remove the row from the table:
            row.remove();
            // Optionally, redirect to the user pet view page:
            // window.location.href = 'user_petview.php';
          } else {
            alert("Approval failed. Please try again.");
          }
        })
        .catch(error => {
          console.error('Error:', error);
          alert("An error occurred during approval.");
        });
      }
    }

    // Handles the rejection action.
    function rejectAdoption(button) {
      var row = button.closest("tr");
      var recordStr = row.getAttribute("data-record");
      if (recordStr) {
        var data = JSON.parse(recordStr);
        alert("Rejected adoption for " + (data.pet_name || 'N/A'));
        // TODO: Add your AJAX call or further logic for rejection here.
      }
    }

    // Close the modal if clicking outside of it.
    window.onclick = function(event) {
      var modal = document.getElementById("viewModal");
      if (event.target == modal) {
        closeModal();
      }
    }
  </script>
</body>
</html>
