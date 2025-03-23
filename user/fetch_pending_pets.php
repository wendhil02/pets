<?php
session_start();
include '../internet/connect_ka.php';

if (!isset($_SESSION['email'])) {
    echo json_encode([]);
    exit;
}

$user_email = $_SESSION['email'];

// ✅ Kunin ang pets kasama ang kanilang schedule_date at email
$sql = "SELECT id, petname, type, schedule_date, email FROM pet WHERE email = ? AND status = 'Pending' ORDER BY schedule_date DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_email);
$stmt->execute();
$result = $stmt->get_result();

$pets = [];
while ($row = $result->fetch_assoc()) {
    $pets[] = [
        "id" => $row["id"],
        "petname" => $row["petname"],
        "type" => $row["type"],
        "email" => $row["email"], // ✅ Dinagdag ang email
        "schedule_date" => $row["schedule_date"] ? date("F j, Y g:i A", strtotime($row["schedule_date"])) : "Not Scheduled",
        "has_schedule" => !empty($row["schedule_date"]) // ✅ Flag kung may schedule na
    ];
}

echo json_encode($pets);
?>

