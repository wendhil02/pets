<?php
ob_start();
session_start();
include 'design/top.php';
include 'design/mid.php';
require '../internet/connect_ka.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Ensure the auto increment starts from 1 if no records exist
$result = $conn->query("SELECT COUNT(*) AS total FROM adoption_requests");
$row = $result->fetch_assoc();
if ($row['total'] == 0) {
    $conn->query("ALTER TABLE adoption_requests AUTO_INCREMENT = 1");
}

if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

$email = $_SESSION['email'];

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer-master/src/Exception.php';
require '../PHPMailer-master/src/PHPMailer.php';
require '../PHPMailer-master/src/SMTP.php';
require '../vendor/autoload.php';

$sql = "SELECT ar.id, p.id AS pet_id, p.petname, ar.email AS adopter_email, ar.contact, ar.status, ar.schedule_date, ar.pickup_time, ar.email_confirmed, ar.owner_mark
        FROM adoption_requests ar
        JOIN pet p ON ar.pet_id = p.id
        WHERE ar.status = 'pending'";


$result = $conn->query($sql);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['request_id'], $_POST['action'])) {
    $request_id = intval($_POST['request_id']);
    $action = $_POST['action'];

    if ($action == "approve" && isset($_POST['schedule_date'], $_POST['pickup_time']) && !empty($_POST['schedule_date']) && !empty($_POST['pickup_time'])) {

        // Assign schedule date and pickup time to variables
        $schedule_date = $_POST['schedule_date'];
        $pickup_time = $_POST['pickup_time'];

        // Check if the request has already been approved
        $checkApproval = "SELECT email_confirmed FROM adoption_requests WHERE id = ? AND email_confirmed = 0";
        $stmt = $conn->prepare($checkApproval);
        $stmt->bind_param("i", $request_id);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 0) {
            $_SESSION['notification'] = ['type' => 'error', 'message' => 'This adoption request has already been processed.'];
            header("Location: manage_adoptions.php");
            exit();
        }

        // Get adopter's email and pet_id
        $query = "SELECT pet_id, email FROM adoption_requests WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $request_id);
        $stmt->execute();
        $stmt->bind_result($pet_id, $adopter_email);
        $stmt->fetch();
        $stmt->close();

        // Generate confirmation token
        $confirmation_token = bin2hex(random_bytes(16));

        // Update adoption_requests with schedule and token
        $sql = "UPDATE adoption_requests SET schedule_date = ?, pickup_time = ?, confirmation_token = ?, email_confirmed = 0 WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $schedule_date, $pickup_time, $confirmation_token, $request_id);
        $stmt->execute();
        $stmt->close();

        // Send Confirmation Email
        $confirmation_link = "http://localhost/revise/admin/confirm_adoption.php?token=$confirmation_token";

        $mail = new PHPMailer;
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'wendhil10@gmail.com';
        $mail->Password = 'ffml onzu stox lcwb'; // Use app password
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('your-email@gmail.com', 'Pet Adoption Center');
        $mail->addAddress($adopter_email);
        $mail->isHTML(true);
        $mail->Subject = 'Confirm Your Adoption';
        $mail->Body = "
            <h2>Adoption Confirmation</h2>
            <p>Your adoption request was approved by admin. Please confirm the pickup schedule below:</p>
            <p><strong>Date:</strong> $schedule_date</p>
            <p><strong>Time:</strong> $pickup_time</p>
            <p><a href='$confirmation_link'>✅ Click here to Confirm Adoption</a></p>
            <p>If you do not confirm, the adoption won't be finalized.</p>
        ";

        if ($mail->send()) {
            $_SESSION['notification'] = ['type' => 'success', 'message' => 'Approval email with confirmation link sent.'];
        } else {
            $_SESSION['notification'] = ['type' => 'error', 'message' => 'Failed to send confirmation email.'];
        }

        header("Location: manage_adoptions.php");
        exit();
    } elseif ($action == "reject") {
        // Get adopter's email to send rejection message
        $query = "SELECT email, pet_id FROM adoption_requests WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $request_id);
        $stmt->execute();
        $stmt->bind_result($adopter_email, $pet_id);
        $stmt->fetch();
        $stmt->close();

        // Delete the adoption request from the database
        $sql = "DELETE FROM adoption_requests WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $request_id);
        $stmt->execute();
        $stmt->close();

        // Send rejection email to the adopter
        $mail = new PHPMailer;
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'wendhil10@gmail.com';
        $mail->Password = 'ffml onzu stox lcwb'; // Use app password
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('your-email@gmail.com', 'Pet Adoption Center');
        $mail->addAddress($adopter_email);
        $mail->isHTML(true);
        $mail->Subject = 'Adoption Request Rejected';
        $mail->Body = "
            <h2>Adoption Request Rejected</h2>
            <p>We regret to inform you that your adoption request for the pet has been rejected. You are welcome to reapply for adoption in the future.</p>
            <p>If you have any questions, please feel free to reach out to us.</p>
            <p>Thank you for your understanding!</p>
        ";

        if ($mail->send()) {
            $_SESSION['notification'] = ['type' => 'error', 'message' => 'Request rejected. The adopter will receive an email notification.'];
        } else {
            $_SESSION['notification'] = ['type' => 'error', 'message' => 'Failed to send rejection email.'];
        }

        header("Location: manage_adoptions.php");
        exit();
    } else {
        $_SESSION['notification'] = ['type' => 'error', 'message' => 'Invalid request!'];
        header("Location: manage_adoptions.php");
        exit();
    }
}

$conn->close();
ob_end_flush();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Adoption Requests</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.0.3/dist/tailwind.min.css" rel="stylesheet">
    <script>
        function hideApproveButton(requestId) {
            const approveBtn = document.getElementById('approveBtn' + requestId);
            approveBtn.style.display = 'none'; // Hide the button once clicked
        }
    </script>
</head>

<body class="flex bg-gray-100">
    <!-- Notification Display -->
    <?php if (isset($_SESSION['notification'])): ?>
        <?php
        $notifType = $_SESSION['notification']['type'];
        $notifMessage = $_SESSION['notification']['message'];
        ?>
        <div id="notification" class="fixed top-5 right-5 p-3 text-white text-sm rounded shadow-md 
             <?= ($notifType == 'success' ? 'bg-green-500' : 'bg-red-500') ?>">
            <?= $notifMessage ?>
            <button onclick="hideNotification()" class="ml-2 text-white">&times;</button>
        </div>
        <?php unset($_SESSION['notification']); ?>
    <?php endif; ?>

<div id="mainContent" class="main-content flex-1 transition-all">
    <nav class="bg-[#0077b6] shadow-md mt-3 mx-2 p-3 flex items-center justify-between rounded-lg max-w-auto mx-auto">
        <!-- Button -->
        <button id="toggleSidebar" class="text-white text-lg px-3 py-2 hover:bg-blue-100 rounded-md border border-transparent">
            ☰
        </button>

        <div class="flex items-center gap-6 flex-grow">
            <!-- Current Time and Date -->
            <span id="currentTime" class="text-white text-sm md:text-base lg:text-sm"></span>
            <div id="currentDate" class="text-white text-sm md:text-base lg:text-sm"></div>
        </div>

        <div class="flex items-center gap-6">
            <!-- Welcome Message -->
            <span class="font-bold text-white text-sm md:text-base lg:text-sm">
                Welcome, <?= htmlspecialchars($email) ?>
            </span>
        </div>
    </nav>

    <div class="max-w-5xl mx-auto bg-white shadow-lg rounded-xl p-4 mt-6 mx-2">
        <h2 class="text-2xl font-semibold text-center text-[#0077b6] mb-4 flex items-center justify-between">
            <span class="flex-1">🐾 Manage Adoption Requests</span>
            <button id="openModalButton" class="bg-green-600 text-white px-4 py-2 rounded-md shadow-md hover:bg-blue-700 focus:outline-none" onclick="window.location.href='email_request_approval.php';">
                 Request Transfer Pet Approval
            </button>
        </h2>

        <div class="overflow-x-auto">
            <table class="w-full border-collapse text-sm text-gray-700">
                <thead>
                    <tr class="bg-[#0077b6] text-white text-center">
                        <th class="border border-gray-300 p-2">Pet Name</th>
                        <th class="border border-gray-300 p-2">Adopter Email</th>
                        <th class="border border-gray-300 p-2">Contact</th>
                        <th class="border border-gray-300 p-2">Action</th>
                        <th class="border border-gray-300 p-2">Mark</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr class="bg-white hover:bg-gray-50 transition-all duration-200 ease-in-out">
                            <td class="border border-gray-300 p-2 text-center"><?= htmlspecialchars($row['petname']) ?></td>
                            <td class="border border-gray-300 p-2 text-center"><?= htmlspecialchars($row['adopter_email']) ?></td>
                            <td class="border border-gray-300 p-2 text-center"><?= htmlspecialchars($row['contact']) ?></td>
                            <td class="border border-gray-300 p-2 text-center">
                                <form action="manage_adoptions.php" method="POST" class="space-y-3">
                                    <input type="hidden" name="request_id" value="<?= $row['id'] ?>">
                                    <div class="flex justify-center gap-4 mb-2">
                                        <button name="action" value="approve" type="submit" class="bg-green-600 text-white py-1 px-4 rounded-md transition duration-200 hover:bg-green-700" id="approveBtn<?= $row['id'] ?>" onclick="hideApproveButton(<?= $row['id'] ?>)">
                                            Approve
                                        </button>
                                        <button name="action" value="reject" type="submit" class="bg-red-600 text-white py-1 px-4 rounded-md transition duration-200 hover:bg-red-700">
                                            Reject
                                        </button>
                                    </div>
                                    <div class="space-y-2 text-center">
                                        <input type="datetime-local" name="schedule_date" class="border p-2 rounded w-48 mx-auto" placeholder="Schedule">
                                        <input type="time" name="pickup_time" class="border p-2 rounded w-48 mx-auto" placeholder="Pickup">
                                    </div>
                                </form>
                            </td>
                            <td class="border border-gray-300 p-2 text-center">
                                <div class="w-5 h-5 <?= ($row['owner_mark'] == 1 ? 'bg-green-500' : 'bg-gray-300') ?> rounded-full mx-auto flex items-center justify-center">
                                    <?= ($row['owner_mark'] == 1 ? '✔' : '') ?>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

    <script>
    function toggleApproveButton(input, buttonId) {
        const approveButton = document.getElementById(buttonId);
        approveButton.disabled = !input.form.schedule_date.value || !input.form.pickup_time.value;
    }

    function hideNotification() {
        const notification = document.getElementById("notification");
        if (notification) {
            notification.style.display = "none";
        }
    }

    setTimeout(hideNotification, 3000); // Auto-hide after 3 seconds
</script>


    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const sidebar = document.getElementById("sidebar");
            const mainContent = document.getElementById("mainContent");
            const toggleSidebar = document.getElementById("toggleSidebar");
            const closeSidebarMobile = document.getElementById("closeSidebarMobile");

            if (sidebar && mainContent && toggleSidebar && closeSidebarMobile) {
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
            }

            // JavaScript to update current time and date
            function updateTimeAndDate() {
                // Get current date and time
                const currentTime = new Date();

                // Format current time (e.g., 12:34 PM)
                const formattedTime = currentTime.toLocaleTimeString([], {
                    hour: '2-digit',
                    minute: '2-digit'
                });

                // Format current date (e.g., April 4, 2025)
                const formattedDate = currentTime.toLocaleDateString([], {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });

                // Update the current time and date in the DOM
                const currentTimeElement = document.getElementById('currentTime');
                const currentDateElement = document.getElementById('currentDate');

                if (currentTimeElement && currentDateElement) {
                    currentTimeElement.textContent = formattedTime;
                    currentDateElement.textContent = formattedDate;
                }
            }

            // Update time and date every minute
            setInterval(updateTimeAndDate, 60000);

            // Initial call to update the time and date immediately
            updateTimeAndDate();
        });
    </script>
</body>

</html>