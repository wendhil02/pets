<?php
include '../internet/connect_ka.php.php';

if (!isset($_GET['ticket'])) {
    exit();
}

$ticket_number = $_GET['ticket'];

// Get ticket ID
$stmt = $conn->prepare("SELECT id FROM tickets WHERE ticket_number = ?");
$stmt->bind_param("s", $ticket_number);
$stmt->execute();
$stmt->bind_result($ticket_id);
$stmt->fetch();
$stmt->close();

// Fetch messages
$stmt = $conn->prepare("SELECT sender, message, sent_at FROM ticket_messages WHERE ticket_id = ? ORDER BY sent_at ASC");
$stmt->bind_param("i", $ticket_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    echo "<div class='p-2 border-b'>";
    echo "<strong class='text-blue-500'>" . htmlspecialchars($row['sender']) . ":</strong> ";
    echo "<span class='text-gray-700'>" . htmlspecialchars($row['message']) . "</span>";
    echo "<span class='text-sm text-gray-500 ml-2'>" . $row['sent_at'] . "</span>";
    echo "</div>";
}
?>
