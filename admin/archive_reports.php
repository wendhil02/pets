<?php
session_start();
include '../internet/connect_ka.php';

// Fetch reports
$sql = "SELECT * FROM cruelty_reports_archive ORDER BY archived_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Archived Reports</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-poppins">
    <div class="max-w-6xl mx-auto mt-8 p-6 bg-white rounded-lg shadow-md">
        <h2 class="text-2xl font-bold text-gray-800 mb-4">📦 Archived Reports</h2>

        <?php if ($result->num_rows > 0): ?>
            <table class="w-full border border-gray-300 rounded-lg">
                <thead class="bg-gray-600 text-white text-sm">
                    <tr>
                        <th class="p-2 border">Reporter Email</th>
                        <th class="p-2 border">Location</th>
                        <th class="p-2 border">Date & Time</th>
                        <th class="p-2 border">Description</th>
                        <th class="p-2 border">Evidence</th> <!-- Added Evidence Column -->
                        <th class="p-2 border">Archived At</th>
                        <th class="p-2 border">Action</th> <!-- Added Action Column -->
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
                                    <a href="<?= htmlspecialchars($row["evidence_path"]); ?>" class="text-blue-500 hover:underline" target="_blank">View Evidence</a>
                                <?php else: ?>
                                    <span class="text-gray-500">No Evidence</span>
                                <?php endif; ?>
                            </td>
                            <td class="p-2 border"><?= htmlspecialchars($row["archived_at"]); ?></td>
                            <td class="p-2 border text-center">
                                <!-- Action Button (Download PDF) -->
                                <a href="download_report.php?id=<?= $row['id']; ?>" class="text-blue-500 hover:underline">Download PDF</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="text-gray-500 text-sm text-center">No archived reports.</p>
        <?php endif; ?>
    </div>
</body>
</html>

<?php
$conn->close();
?>
