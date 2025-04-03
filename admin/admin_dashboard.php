<?php
session_start();
include '../internet/connect_ka.php';

// Check if admin is logged in
if ($_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Fetch all tickets
$result = $conn->query("SELECT * FROM tickets ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - Manage Tickets</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">
    <div class="max-w-4xl mx-auto bg-white p-6 rounded-lg shadow-lg">
        <h2 class="text-lg font-bold mb-4">Admin Ticket Dashboard</h2>

        <table class="w-full border-collapse border border-gray-300">
            <thead>
                <tr class="bg-gray-200">
                    <th class="border p-2">Ticket #</th>
                    <th class="border p-2">User</th>
                    <th class="border p-2">Pet</th>
                    <th class="border p-2">Status</th>
                    <th class="border p-2">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr class="text-center">
                    <td class="border p-2"><?= htmlspecialchars($row['ticket_number']) ?></td>
                    <td class="border p-2"><?= htmlspecialchars($row['user_email']) ?></td>
                    <td class="border p-2"><?= htmlspecialchars($row['pet_name']) ?></td>
                    <td class="border p-2"><?= htmlspecialchars($row['status']) ?></td>
                    <td class="border p-2">
                        <a href="admin_chat.php?ticket=<?= $row['ticket_number'] ?>"
                            class="bg-blue-500 text-white px-3 py-1 rounded-lg hover:bg-blue-700">
                            View Chat
                        </a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
