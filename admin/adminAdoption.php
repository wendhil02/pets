<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">
</head>
<?php include('navandside/nav.php'); ?>
<?php include('navandside/sidebar.php'); ?>
<?php include('navandside/head.php'); ?>
<style>
body {
    font-family: 'Poppins', sans-serif;
    margin: 0;
    padding: 0;
    background-color: #f4f4f9;
}

/* General Box Styles */
.box {
    width: 1400px;
    height: 600px;
    background-color: transparent;
    border: 2px solid #e5e7eb;
    border-radius: 5px;
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
    padding: 16px;
    margin: 8px;
    overflow: auto;
}

.box-container {
    display: flex;
    gap: 16px;
}

/* Table Styles */
table {
    width: 100%;
    border-collapse: collapse;
    background-color: #ffffff;
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

.filter-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding: 10px;
    background-color: transparent;
    font-family: Arial, sans-serif;
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
</style>

<body class="vertical light">
    <div class="box-container">
        <!-- Box 1 as Table -->
        <div class="box">
            <div class="filter-container">
                <div class="search-container">
                    <div class="search-bar-wrapper">
                        <i class="fa-solid fa-search"></i>
                        <input type="text" id="search-bar" class="search-bar" placeholder="Search by Pet Name, Email..."
                            onkeyup="filterTable()">
                    </div>
                </div>

                <h3 class="title">Adoption Management</h3>

                <div class="category-container">
                    <select id="category-filter" class="category-filter" onchange="filterTable()">
                        <option value="">Select Category</option>
                        <option value="owner">Owner</option>
                        <option value="email">Email</option>
                        <option value="breed">Pet Breed</option>
                        <option value="info">Information</option>
                    </select>
                </div>
            </div>

            <!-- Table -->
            <table id="pet-table">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Owner</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>Address</th>
                        <th>Pet</th>
                        <th>Age</th>
                        <th>Breed</th>
                        <th>Information</th>
                        <th>Pet Image</th>
                        <th>Pet Vaccine</th>
                        <th>Created At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    include('./dbconn/config.php');
                    $sql = "SELECT * FROM adoption";
                    $result = $conn->query($sql);
                    if ($result->num_rows > 0) {
                        $index = 1;
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>
                                    <td>{$index}</td>
                                    <td>{$row['owner']}</td>
                                    <td>{$row['phone']}</td>
                                    <td>{$row['email']}</td>
                                    <td>{$row['address']}</td>
                                    <td>{$row['pet']}</td>
                                    <td>{$row['age']}</td>
                                    <td>{$row['breed']}</td>
                                    <td>{$row['info']}</td>
                                    <td>{$row['pet_image']}</td>
                                    <td>{$row['pet_vaccine']}</td>
                                    <td>{$row['created_at']}</td>
                                  </tr>";
                            $index++;
                        }
                    } else {
                        echo '<tr><td colspan="12">No register found.</td></tr>';
                    }
                    $conn->close();
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
    function filterTable() {
        const searchQuery = document.getElementById('search-bar').value.toLowerCase();
        const category = document.getElementById('category-filter').value;
        const rows = document.querySelectorAll('#pet-table tbody tr');

        rows.forEach(row => {
            const cells = row.getElementsByTagName('td');
            let cellText = '';

            switch (category) {
                case 'owner':
                    cellText = cells[1].textContent.toLowerCase();
                    break;
                case 'email':
                    cellText = cells[3].textContent.toLowerCase();
                    break;
                case 'breed':
                    cellText = cells[7].textContent.toLowerCase();
                    break;
                case 'info':
                    cellText = cells[8].textContent.toLowerCase();
                    break;
                default:
                    cellText = [...cells].map(cell => cell.textContent.toLowerCase()).join(' ');
            }

            if (cellText.includes(searchQuery)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }
    </script>

</body>

</html>
