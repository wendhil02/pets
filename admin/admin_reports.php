<?php
session_start();
include '../internet/connect_ka.php';

// Ensure connection is established
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Approve and Archive Actions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['approve'])) {
        $report_id = $_POST['report_id'];
        $update_sql = "UPDATE cruelty_reports SET is_approved = 1 WHERE id = ?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("i", $report_id);
        $stmt->execute();
        $stmt->close();
    }

    if (isset($_POST['archive'])) {
        $report_id = $_POST['report_id'];
        
        // Move report to archive table
        $archive_sql = "INSERT INTO cruelty_reports_archive SELECT *, NOW() FROM cruelty_reports WHERE id = ?";
        $delete_sql = "DELETE FROM cruelty_reports WHERE id = ?";
        
        $stmt_archive = $conn->prepare($archive_sql);
        $stmt_archive->bind_param("i", $report_id);
        $stmt_archive->execute();
        $stmt_archive->close();

        $stmt_delete = $conn->prepare($delete_sql);
        $stmt_delete->bind_param("i", $report_id);
        $stmt_delete->execute();
        $stmt_delete->close();
    }

    header("Location: ".$_SERVER['PHP_SELF']); 
    exit();
}

// Fetch reports
$sql = "SELECT * FROM cruelty_reports ORDER BY submitted_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Animal Cruelty Reports</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-poppins">
    <div class="max-w-6xl mx-auto mt-8 p-6 bg-white rounded-lg shadow-md">
        <h2 class="text-2xl font-bold text-red-700 mb-4">📋 Animal Cruelty Reports</h2>

        <?php if ($result->num_rows > 0): ?>
            <div class="overflow-x-auto">
                <table class="w-full border border-gray-300 rounded-lg">
                    <thead class="bg-red-600 text-white text-sm">
                        <tr>
                            <th class="p-2 border">Reporter Email</th>
                            <th class="p-2 border">Location</th>
                            <th class="p-2 border">Date & Time</th>
                            <th class="p-2 border">Description</th>
                            <th class="p-2 border">Evidence</th>
                            <th class="p-2 border">Witness</th>
                            <th class="p-2 border">Social Media</th>
                            <th class="p-2 border">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr class="bg-gray-50 hover:bg-gray-100">
                                <td class="p-2 border"><?= htmlspecialchars($row["reporter_email"]); ?></td>
                                <td class="p-2 border"><?= htmlspecialchars($row["incident_location"]); ?></td>
                                <td class="p-2 border"><?= htmlspecialchars($row["incident_datetime"]); ?></td>
                                <td class="p-2 border"><?= nl2br(htmlspecialchars($row["incident_description"])); ?></td>
                                <td class="p-2 border text-center">
                                    <?php if (!empty($row["evidence_path"])): ?>
                                        <a href="<?= htmlspecialchars($row["evidence_path"]); ?>" class="text-blue-500 hover:underline" target="_blank">View</a>
                                    <?php else: ?>
                                        <span class="text-gray-500">No Evidence</span>
                                    <?php endif; ?>
                                </td>
                                <td class="p-2 border text-center"><?= $row["willing_to_testify"] == "Yes" ? "✅" : "❌"; ?></td>
                                <td class="p-2 border"><?= htmlspecialchars($row["social_media"]); ?></td>
                                <td class="p-2 border text-center">
                                    <form method="POST" class="inline-block">
                                        <input type="hidden" name="report_id" value="<?= $row['id']; ?>">
                                        <?php if ($row["is_approved"] == 0): ?>
                                            <button type="submit" name="approve" class="bg-green-500 text-white px-3 py-1 text-xs rounded hover:bg-green-600">Approve</button>
                                        <?php endif; ?>
                                        <button type="submit" name="archive" class="bg-gray-500 text-white px-3 py-1 text-xs rounded hover:bg-gray-600">Archive</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-gray-500 text-sm text-center mt-4">No reports found.</p>
        <?php endif; ?>
    </div>
</body>
</html>

<?php
$conn->close();
?>

