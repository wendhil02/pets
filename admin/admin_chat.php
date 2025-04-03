<?php
session_start();
include 'internet/connect_ka.php';

if ($_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['ticket'])) {
    die("Invalid Ticket!");
}

$ticket_number = $_GET['ticket'];

// Fetch ticket details
$stmt = $conn->prepare("SELECT * FROM tickets WHERE ticket_number = ?");
$stmt->bind_param("s", $ticket_number);
$stmt->execute();
$ticket = $stmt->get_result()->fetch_assoc();

if (!$ticket) {
    die("Ticket Not Found!");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Chat - Ticket <?= htmlspecialchars($ticket_number) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        function loadMessages() {
            fetch("fetch_messages.php?ticket=<?= $ticket_number ?>")
            .then(response => response.text())
            .then(data => {
                document.getElementById("chatBox").innerHTML = data;
            });
        }
        setInterval(loadMessages, 2000);
    </script>
</head>
<body class="bg-gray-100 p-6">
    <div class="bg-white rounded-lg shadow-lg max-w-2xl mx-auto p-6">
        <h2 class="text-lg font-bold">Ticket: <?= htmlspecialchars($ticket['ticket_number']) ?></h2>
        <p class="text-sm text-gray-600"><?= htmlspecialchars($ticket['description']) ?></p>

        <!-- Chat Box -->
        <div id="chatBox" class="mt-4 p-4 border rounded-lg h-60 overflow-y-auto bg-gray-50"></div>

        <!-- Message Input -->
        <form id="chatForm" action="send_message.php" method="POST">
            <input type="hidden" name="ticket_number" value="<?= $ticket_number ?>">
            <input type="text" name="message" placeholder="Type your message..." required
                class="mt-3 w-full p-2 border rounded-lg focus:ring-2 focus:ring-blue-400 outline-none">
            <button type="submit"
                class="mt-3 bg-blue-500 text-white py-2 px-4 rounded-lg hover:bg-blue-600 transition">
                Send
            </button>
        </form>

        <!-- Mark as Resolved -->
        <form action="resolve_ticket.php" method="POST">
            <input type="hidden" name="ticket_number" value="<?= $ticket_number ?>">
            <button type="submit"
                class="mt-3 bg-green-500 text-white py-2 px-4 rounded-lg hover:bg-green-600 transition">
                Mark as Resolved
            </button>
        </form>
    </div>
</body>
</html>
