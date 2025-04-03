<?php
// Include database connection
include '../internet/connect_ka.php';
// ✅ Reset AUTO_INCREMENT If No Pets Exist
$result = $conn->query("SELECT COUNT(*) AS total FROM marked_census_data");
$row = $result->fetch_assoc();
if ($row['total'] == 0) {
    $conn->query("ALTER TABLE marked_census_data AUTO_INCREMENT = 1");
}
// Check if an ID is passed via POST
if (isset($_POST['id'])) {
    $id = $_POST['id'];

    // Check if the record is already marked
    $check_marked_sql = "SELECT * FROM census_data WHERE id = ? AND marked = 1";
    $check_stmt = $conn->prepare($check_marked_sql);
    $check_stmt->bind_param("i", $id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        echo "This record is already marked.";
    } else {
        // Update the `marked` column to 1 (mark the record)
        $sql = "UPDATE census_data SET marked = 1 WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id); // Bind the ID to the query
        $stmt->execute();

        // Check if the record is successfully marked
        if ($stmt->affected_rows > 0) {
            // Check if the record already exists in the marked_census_data table
            $check_insert_sql = "SELECT * FROM marked_census_data WHERE census_id = ?";
            $check_insert_stmt = $conn->prepare($check_insert_sql);
            $check_insert_stmt->bind_param("i", $id);
            $check_insert_stmt->execute();
            $check_insert_result = $check_insert_stmt->get_result();

            if ($check_insert_result->num_rows == 0) {
                // Insert the marked record into the `marked_census_data` table
                $sql_insert = "INSERT INTO marked_census_data (census_id, marked_at) VALUES (?, NOW())";
                $stmt_insert = $conn->prepare($sql_insert);
                $stmt_insert->bind_param("i", $id);
                $stmt_insert->execute();

                // Check if the insert was successful
                if ($stmt_insert->affected_rows > 0) {
                    // Redirect back to the census report page with a success message
                    header("Location: census_admin.php?message=Record marked successfully.");
                } else {
                    echo "Failed to insert into marked_census_data.";
                    // Debugging: Check the error message
                    echo " Error: " . $stmt_insert->error;
                }

                // Close the prepared statements
                $stmt_insert->close();
            } else {
                echo "This record is already in the marked_census_data table.";
            }

        } else {
            echo "Failed to mark the record. No rows affected.";
            // Debugging: Check the error message
            echo " Error: " . $stmt->error;
        }

        // Close the prepared statement
        $stmt->close();
    }

    // Close the prepared statement for the check
    $check_stmt->close();
}

// Close the database connection
$conn->close();

?>
