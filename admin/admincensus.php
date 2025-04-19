<?php
include '../internet/connect_ka.php';
if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}
// Check if the table is empty
$result = $conn->query("SELECT COUNT(*) AS count FROM census_data");
$row = $result->fetch_assoc();

if ($row['count'] == 0) {
    // Reset AUTO_INCREMENT to 1 if the table is empty
    $conn->query("ALTER TABLE census_data AUTO_INCREMENT = 1");
}

// Function to fetch data from the API
function fetchCensusData($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, false);

    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        echo 'Curl error: ' . curl_error($ch);
    }
    curl_close($ch);

    return json_decode($response, true);
}

// API URL
$apiUrl = 'https://backend-api-5m5k.onrender.com/api/cencus';

// Fetch census data
$censusData = fetchCensusData($apiUrl);

if ($censusData && isset($censusData['data'])) {
    foreach ($censusData['data'] as $entry) {
        $firstname = $entry['firstname'];
        $middlename = $entry['middlename'];
        $lastname = $entry['lastname'];
        $age = $entry['age'];
        $gender = $entry['gender'];
        $occupation = $entry['occupation'];
        $housenumber = $entry['housenumber'];
        $streetname = $entry['streetname'];
        $barangay = $entry['barangay'];
        $city = $entry['city'];

        // Insert or update existing record
        $sql = "INSERT INTO census_data (firstname, middlename, lastname, age, gender, occupation, housenumber, streetname, barangay, city) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE 
                age = VALUES(age), 
                gender = VALUES(gender), 
                occupation = VALUES(occupation)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssissssss", $firstname, $middlename, $lastname, $age, $gender, $occupation, $housenumber, $streetname, $barangay, $city);
        $stmt->execute();
        $stmt->close();
    }
}
$conn->close();
?>
<meta http-equiv="refresh" content="30">
<!-- Auto-Refresh Script -->
<script>
    setTimeout(function() {
        location.reload(); // Refresh the page every 30 seconds
    }, 30000); 
</script>


