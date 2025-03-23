<?php
session_start();
include 'design/top.php';
include 'design/mid.php';
require '../internet/connect_ka.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer-master/src/Exception.php';
require '../PHPMailer-master/src/PHPMailer.php';
require '../PHPMailer-master/src/SMTP.php';
require '../vendor/autoload.php'; 

$sql = "SELECT ar.id, p.id AS pet_id, p.petname, ar.email AS adopter_email, ar.contact, ar.status, ar.schedule_date, ar.pickup_time
        FROM adoption_requests ar
        JOIN pet p ON ar.pet_id = p.id
        WHERE ar.status = 'pending'";

$result = $conn->query($sql);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['request_id'], $_POST['action'])) {
    $request_id = intval($_POST['request_id']);
    $action = $_POST['action'];

    if ($action == "approve" && isset($_POST['schedule_date'], $_POST['pickup_time']) && !empty($_POST['schedule_date']) && !empty($_POST['pickup_time'])) {
        $schedule_date = $_POST['schedule_date'];
        $pickup_time = $_POST['pickup_time'];

        // Get adopter's email and pet_id for updating the pet's email
        $query = "SELECT pet_id, email FROM adoption_requests WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $request_id);
        $stmt->execute();
        $stmt->bind_result($pet_id, $adopter_email);
        $stmt->fetch();
        $stmt->close();

        // Approve request and clear schedule_date & pickup_time
        $sql = "UPDATE adoption_requests SET status = 'approved', schedule_date = NULL, pickup_time = NULL WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $request_id);
        $stmt->execute();
        $stmt->close();

        // Update the pet's email to the adopter's email
        $sql = "UPDATE pet SET email = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $adopter_email, $pet_id);
        $stmt->execute();
        $stmt->close();

        // Send Email Notification
        $mail = new PHPMailer;
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; 
        $mail->SMTPAuth = true;
        $mail->Username = 'wendhil10@gmail.com'; 
        $mail->Password = 'ffml onzu stox lcwb'; 
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('your-email@gmail.com', 'Pet Adoption Center');
        $mail->addAddress($adopter_email);
        $mail->isHTML(true);
        $mail->Subject = 'Adoption Approval - Pickup Schedule';
        $mail->Body = "
            <h2>From Admin</h2>
            <p>Your adoption request has been approved.</p>
            <p><strong>Pickup Date:</strong> $schedule_date</p>
            <p><strong>Pickup Time:</strong> $pickup_time</p>
            <p>Please visit our adoption center at the scheduled date and time.</p>
            <p>Thank you for adopting!</p>
        ";

        if ($mail->send()) {
            echo "<script>alert('Request approved and email sent successfully!'); window.location.href='manage_adoptions.php';</script>";
        } else {
            echo "<script>alert('Request approved, but email failed to send.'); window.location.href='manage_adoptions.php';</script>";
        }
    } elseif ($action == "reject") {
        $sql = "UPDATE adoption_requests SET status = 'rejected' WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $request_id);
        $stmt->execute();
        $stmt->close();
        
        echo "<script>alert('Request rejected.'); window.location.href='manage_adoptions.php';</script>";
    } else {
        echo "<script>alert('Invalid request!'); window.history.back();</script>";
        exit();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Adoption Requests</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="flex bg-gray-100">
    <div id="mainContent" class="main-content flex-1 transition-all ">
        <nav class="bg-[#0077b6] shadow-md mt-3 mr-2 ml-2 p-2 flex items-center justify-between rounded-lg max-w-auto mx-auto">
            <button id="toggleSidebar" class="text-white text-lg px-2 py-1 hover:bg-blue-100 rounded-md border border-transparent">
                ☰
            </button>
            <span class="font-bold text-white text-sm md:text-base lg:text-lg">Welcome, Wendhil Himarangan</span>
        </nav>

        <div class="max-w-4xl mx-auto bg-white shadow-md rounded-lg p-6 mt-4">
            <h2 class="text-2xl font-semibold text-center mb-4">Manage Adoption Requests</h2>

            <table class="w-full border-collapse border border-gray-300">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="border border-gray-300 p-2">Pet Name</th>
                        <th class="border border-gray-300 p-2">Adopter Email</th>
                        <th class="border border-gray-300 p-2">Contact</th>
                        <th class="border border-gray-300 p-2">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                    <tr class="bg-white text-center">
                        <td class="border border-gray-300 p-2"><?= htmlspecialchars($row['petname']) ?></td>
                        <td class="border border-gray-300 p-2"><?= htmlspecialchars($row['adopter_email']) ?></td>
                        <td class="border border-gray-300 p-2"><?= htmlspecialchars($row['contact']) ?></td>
                        <td class="border border-gray-300 p-2">
                        <form action="manage_adoptions.php" method="POST" class="text-sm text-center space-y-2">
    <input type="hidden" name="request_id" value="<?= $row['id'] ?>">

    <div class="flex flex-col items-center space-y-2">
        <div class="w-2/3">
            <label class="block text-gray-700 text-sm">Pickup Date:</label>
            <input type="date" name="schedule_date" class="border p-1 rounded w-full text-xs" required 
                   oninput="toggleApproveButton(this, 'approveBtn<?= $row['id'] ?>')">
        </div>
        <div class="w-2/3">
            <label class="block text-gray-700 text-sm">Pickup Time:</label>
            <input type="time" name="pickup_time" class="border p-1 rounded w-full text-xs" required 
                   oninput="toggleApproveButton(this, 'approveBtn<?= $row['id'] ?>')">
        </div>
    </div>

    <div class="flex justify-center gap-2 mt-2">
        <button type="submit" name="action" value="approve" 
                id="approveBtn<?= $row['id'] ?>" 
                class="bg-green-500 text-white px-3 py-1 rounded text-xs hover:bg-green-600 disabled:bg-gray-400 disabled:cursor-not-allowed"
                disabled>
            Approve
        </button>

        <button type="submit" name="action" value="reject" 
                class="bg-red-500 text-white px-3 py-1 rounded text-xs hover:bg-red-600">
            Reject
        </button>
    </div>
</form>


                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <script>
            function toggleApproveButton(input, buttonId) {
                const approveButton = document.getElementById(buttonId);
                approveButton.disabled = !input.form.schedule_date.value || !input.form.pickup_time.value;
            }
        </script>
    
    <script>
            const sidebar = document.getElementById("sidebar");
            const mainContent = document.getElementById("mainContent");
            const toggleSidebar = document.getElementById("toggleSidebar");
            const closeSidebarMobile = document.getElementById("closeSidebarMobile");

            // Toggle Sidebar for PC & Mobile
            toggleSidebar.addEventListener("click", function() {
                if (window.innerWidth <= 768) {
                    sidebar.classList.toggle("open"); // Mobile Mode
                } else {
                    sidebar.classList.toggle("closed"); // PC Mode
                    mainContent.classList.toggle("shrink");
                }
            });

            // Close Sidebar on Mobile when "✖" is clicked
            closeSidebarMobile.addEventListener("click", function() {
                sidebar.classList.remove("open");
            });
        </script>
</body>
</html>
