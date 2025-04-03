<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Census Data</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans leading-normal tracking-normal">

<div class="container mx-auto p-6">
    <h1 class="text-3xl font-semibold text-center mb-6">Census Data</h1>

    <?php
    // Function to fetch data from the API
    function fetchCensusData($url) {
        // Initialize cURL session
        $ch = curl_init();

        // Set the URL and options for the request
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);

        // Execute the cURL request
        $response = curl_exec($ch);

        // Check for cURL errors
        if(curl_errno($ch)) {
            echo 'Curl error: ' . curl_error($ch);
        }

        // Close the cURL session
        curl_close($ch);

        // Return the response as an array
        return json_decode($response, true);
    }

    // API URL
    $apiUrl = 'https://backend-api-5m5k.onrender.com/api/cencus';

    // Fetch census data
    $censusData = fetchCensusData($apiUrl);

    // Check if data was successfully fetched
    if ($censusData && isset($censusData['data'])) {
        echo '<div class="grid grid-cols-1 md:grid-cols-2 gap-6">';

        // Loop through the data
        foreach ($censusData['data'] as $entry) {
            // Personal Info - Display the field names in the first column and corresponding data in the second column
            echo '<div class="p-4 bg-white shadow rounded-lg border border-gray-300">';
            echo '<h2 class="text-xl font-semibold mb-4">Personal Info</h2>';

            echo '<div class="mb-2 flex justify-between"><strong>First Name:</strong><span>' . htmlspecialchars($entry['firstname']) . '</span></div>';
            echo '<div class="mb-2 flex justify-between"><strong>Middle Name:</strong><span>' . htmlspecialchars($entry['middlename']) . '</span></div>';
            echo '<div class="mb-2 flex justify-between"><strong>Last Name:</strong><span>' . htmlspecialchars($entry['lastname']) . '</span></div>';
            echo '<div class="mb-2 flex justify-between"><strong>Birthday:</strong><span>' . htmlspecialchars($entry['birthday']) . '</span></div>';
            echo '<div class="mb-2 flex justify-between"><strong>Age:</strong><span>' . htmlspecialchars($entry['age']) . '</span></div>';
            echo '<div class="mb-2 flex justify-between"><strong>Gender:</strong><span>' . htmlspecialchars($entry['gender']) . '</span></div>';
            echo '<div class="mb-2 flex justify-between"><strong>Civil Status:</strong><span>' . htmlspecialchars($entry['civilstatus']) . '</span></div>';
            echo '<div class="mb-2 flex justify-between"><strong>Current School Enrollment:</strong><span>' . htmlspecialchars($entry['currentschoolenrollment']) . '</span></div>';
            echo '<div class="mb-2 flex justify-between"><strong>Educational Attainment:</strong><span>' . htmlspecialchars($entry['educationalattainment']) . '</span></div>';
            echo '<div class="mb-2 flex justify-between"><strong>Employment Status:</strong><span>' . htmlspecialchars($entry['employmentstatus']) . '</span></div>';
            echo '<div class="mb-2 flex justify-between"><strong>Occupation:</strong><span>' . htmlspecialchars($entry['occupation']) . '</span></div>';
            echo '<div class="mb-2 flex justify-between"><strong>House Number:</strong><span>' . htmlspecialchars($entry['housenumber']) . '</span></div>';
            echo '<div class="mb-2 flex justify-between"><strong>Street Name:</strong><span>' . htmlspecialchars($entry['streetname']) . '</span></div>';
            echo '<div class="mb-2 flex justify-between"><strong>Barangay:</strong><span>' . htmlspecialchars($entry['barangay']) . '</span></div>';
            echo '<div class="mb-2 flex justify-between"><strong>City:</strong><span>' . htmlspecialchars($entry['city']) . '</span></div>';
            echo '<div class="mb-2 flex justify-between"><strong>Province:</strong><span>' . htmlspecialchars($entry['province']) . '</span></div>';
            echo '<div class="mb-2 flex justify-between"><strong>House Type:</strong><span>' . htmlspecialchars($entry['housetype']) . '</span></div>';
            echo '<div class="mb-2 flex justify-between"><strong>Health Status:</strong><span>' . htmlspecialchars($entry['healthstatus']) . '</span></div>';
            echo '<div class="mb-2 flex justify-between"><strong>Disability Status:</strong><span>' . htmlspecialchars($entry['disabilitystatus']) . '</span></div>';
            echo '<div class="mb-2 flex justify-between"><strong>Existing Health Condition:</strong><span>' . htmlspecialchars($entry['existinghealthcondition']) . '</span></div>';
            echo '<div class="mb-2 flex justify-between"><strong>Fully Immunized:</strong><span>' . htmlspecialchars($entry['fullyimmunized']) . '</span></div>';
            echo '<div class="mb-2 flex justify-between"><strong>COVID-19 Vaccination:</strong><span>' . htmlspecialchars($entry['covid19vaccination']) . '</span></div>';
            echo '<div class="mb-2 flex justify-between"><strong>Housing Type:</strong><span>' . htmlspecialchars($entry['housingtype']) . '</span></div>';
            echo '<div class="mb-2 flex justify-between"><strong>Year of Construction:</strong><span>' . htmlspecialchars($entry['yearofconstructed']) . '</span></div>';
            echo '<div class="mb-2 flex justify-between"><strong>Resident Lived:</strong><span>' . htmlspecialchars($entry['residentlived']) . '</span></div>';
            echo '<div class="mb-2 flex justify-between"><strong>Mobile Number:</strong><span>' . htmlspecialchars($entry['mobilenumber']) . '</span></div>';
            echo '<div class="mb-2 flex justify-between"><strong>Emergency Contact Name:</strong><span>' . htmlspecialchars($entry['emergencycontactname']) . '</span></div>';
            echo '<div class="mb-2 flex justify-between"><strong>Emergency Contact Number:</strong><span>' . htmlspecialchars($entry['emergencycontactnumber']) . '</span></div>';
            echo '<div class="mb-2 flex justify-between"><strong>Relationship to Emergency Contact:</strong><span>' . htmlspecialchars($entry['relationshiptoemergencycontact']) . '</span></div>';
            echo '<div class="mb-2 flex justify-between"><strong>Number of Household Members:</strong><span>' . htmlspecialchars($entry['numberofhousemembers']) . '</span></div>';

            // Household Members - Display members
            if (!empty($entry['householdMembers'])) {
                echo '<h2 class="text-xl font-semibold mb-4 mt-6">Household Members</h2>';
                foreach ($entry['householdMembers'] as $member) {
                    echo '<div class="mb-2 flex justify-between"><strong>Relationship:</strong><span>' . htmlspecialchars($member['relationship']) . '</span></div>';
                    echo '<div class="mb-2 flex justify-between"><strong>First Name:</strong><span>' . htmlspecialchars($member['firstname']) . '</span></div>';
                    echo '<div class="mb-2 flex justify-between"><strong>Middle Name:</strong><span>' . htmlspecialchars($member['middlename']) . '</span></div>';
                    echo '<div class="mb-2 flex justify-between"><strong>Last Name:</strong><span>' . htmlspecialchars($member['lastname']) . '</span></div>';
                    echo '<div class="mb-2 flex justify-between"><strong>Birthday:</strong><span>' . htmlspecialchars($member['birthday']) . '</span></div>';
                    echo '<div class="mb-2 flex justify-between"><strong>Age:</strong><span>' . htmlspecialchars($member['age']) . '</span></div>';
                    echo '<div class="mb-2 flex justify-between"><strong>Gender:</strong><span>' . htmlspecialchars($member['gender']) . '</span></div>';
                    echo '<div class="mb-2 flex justify-between"><strong>Civil Status:</strong><span>' . htmlspecialchars($member['civilstatus']) . '</span></div>';
                    echo '<div class="mb-2 flex justify-between"><strong>Current School Enrollment:</strong><span>' . htmlspecialchars($member['currentschoolenrollment']) . '</span></div>';
                    echo '<div class="mb-2 flex justify-between"><strong>Educational Attainment:</strong><span>' . htmlspecialchars($member['educationalattainment']) . '</span></div>';
                    echo '<div class="mb-2 flex justify-between"><strong>Employment Status:</strong><span>' . htmlspecialchars($member['employmentstatus']) . '</span></div>';
                    echo '<div class="mb-2 flex justify-between"><strong>Occupation:</strong><span>' . htmlspecialchars($member['occupation']) . '</span></div>';
                    echo '<div class="mb-2 flex justify-between"><strong>Health Status:</strong><span>' . htmlspecialchars($member['healthstatus']) . '</span></div>';
                    echo '<div class="mb-2 flex justify-between"><strong>Disability Status:</strong><span>' . htmlspecialchars($member['disabilitystatus']) . '</span></div>';
                    echo '<div class="mb-2 flex justify-between"><strong>Existing Health Condition:</strong><span>' . htmlspecialchars($member['existinghealthcondition']) . '</span></div>';
                    echo '<div class="mb-2 flex justify-between"><strong>Fully Immunized:</strong><span>' . htmlspecialchars($member['fullyimmunized']) . '</span></div>';
                    echo '<div class="mb-2 flex justify-between"><strong>COVID-19 Vaccination:</strong><span>' . htmlspecialchars($member['covid19vaccination']) . '</span></div>';
                }
            }

            echo '</div>';  // End of personal info section
        }

        echo '</div>';
    } else {
        echo '<p>No census data available.</p>';
    }
    ?>
</div>

</body>
</html>
