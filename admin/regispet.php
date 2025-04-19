<?php
ob_start();
session_start();

include 'design/top.php';
include 'design/mid.php';
include '../internet/connect_ka.php';
error_reporting(E_ALL);


ini_set('display_errors', 1);
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

function sendEmail($user_email, $first_name, $subject, $body)
{
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'wendhil10@gmail.com';
        $mail->Password = 'ffml onzu stox lcwb';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('no-reply@bpa.smartbarangayconnect.com', 'BPA Smart Barangay Connect');
        $mail->addAddress($user_email, $first_name);

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $body;

        $mail->send();
    } catch (Exception $e) {
        return 'Mailer Error: ' . $mail->ErrorInfo;
    }
    return null;
}

// Handle approval
if (isset($_GET['approve_id'])) {
    $id = $_GET['approve_id'];
    $sql = "UPDATE registerlanding SET status = 'approved' WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $sql_email = "SELECT email, first_name FROM registerlanding WHERE id = ?";
        $stmt_email = $conn->prepare($sql_email);
        $stmt_email->bind_param("i", $id);
        $stmt_email->execute();
        $result_email = $stmt_email->get_result();

        if ($result_email->num_rows > 0) {
            $user = $result_email->fetch_assoc();
            $user_email = $user['email'];
            $first_name = $user['first_name'];

            $body = "
                <html>
                <body>
                    <p>Dear $first_name,</p>
                    <p>Your account has been approved.</p>
                    <p>You can log in here: <a href='https://bpa.smartbarangayconnect.com/index.php'>Login</a></p>
                </body>
                </html>
            ";
            $subject = 'Account Approved - BPA Smart Barangay Connect';
            $error = sendEmail($user_email, $first_name, $subject, $body);

            if (!$error) {
                $_SESSION['message'] = ['type' => 'success', 'text' => 'User approved successfully!'];
            } else {
                $_SESSION['message'] = ['type' => 'error', 'text' => 'User approved, but email failed.'];
            }
        }
    } else {
        $_SESSION['message'] = ['type' => 'error', 'text' => 'Failed to approve user.'];
    }
    header("Location: regispet.php");
    exit();
}

// Handle rejection (DELETE user completely)
if (isset($_GET['reject_id'])) {
    $id = $_GET['reject_id'];

    if (!is_numeric($id)) {
        die("Invalid ID.");
    }

    // Get user email and first name before deletion
    $sql_email = "SELECT email, first_name FROM registerlanding WHERE id = ?";
    $stmt_email = $conn->prepare($sql_email);
    $stmt_email->bind_param("i", $id);
    $stmt_email->execute();
    $result_email = $stmt_email->get_result();

    if ($result_email->num_rows > 0) {
        $user = $result_email->fetch_assoc();
        $user_email = $user['email'];
        $first_name = $user['first_name'];

        // Email content for rejection
        $body = "
            <html>
            <body>
                <p>Dear $first_name,</p>
                <p>We regret to inform you that your registration request has been rejected.</p>
                <p><strong>Reason:</strong> I apologize, but you are not a resident here. You are not allowed to register.</p>
                <p>If you believe this was a mistake, please contact our support team.</p>
                <p>Thank you for your understanding.</p>
            </body>
            </html>
        ";
        $subject = 'Registration Rejected - BPA Smart Barangay Connect';

        // Send rejection email
        $error = sendEmail($user_email, $first_name, $subject, $body);
    }

    // Delete user from database
    $sql = "DELETE FROM registerlanding WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $_SESSION['message'] = ['type' => 'success', 'text' => 'User rejected and removed successfully!'];
    } else {
        die("Delete failed: " . $stmt->error);
    }

    header("Location: regispet.php");
    exit();
}


// Fetch pending users
$sql = "SELECT id, first_name, last_name, email, house, street, barangay FROM registerlanding WHERE status = 'pending'";
$result = $conn->query($sql);
ob_end_flush();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pending User Approvals</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 flex">
    <div id="mainContent" class="main-content flex-1 transition-all ">
        <!-- Navbar -->


        <nav class="bg-[#0077b6] shadow-md mt-3 mr-2 ml-2 p-2 flex items-center justify-between rounded-lg max-w-auto mx-auto">
            <!--  Button -->
            <button id="toggleSidebar" class="text-white text-lg px-2 py-1 hover:bg-blue-100 rounded-md border border-transparent">
                ☰
            </button>

            <div class="flex items-center gap-4 flex-grow">
                <!-- Current Time and Date -->
                <span id="currentTime" class="text-white font-semibold text-sm md:text-base lg:text-lg"></span>
                <div id="currentDate" class="text-white font-semibold text-sm md:text-base lg:text-lg"></div>
            </div>

            <div class="flex items-center gap-4">
                <!-- Welcome Message -->
                <span class="font-bold text-white text-sm md:text-base lg:text-lg">
                    Welcome, <?= htmlspecialchars($email) ?>
                </span>
            </div>
        </nav>

        <!-- Notification Toast -->
        <?php if (isset($_SESSION['message'])): ?>
            <div id="toast" class="fixed top-5 right-5 bg-<?= $_SESSION['message']['type'] == 'success' ? 'green' : 'red' ?>-500 text-white px-4 py-2 rounded shadow">
                <?= $_SESSION['message']['text'] ?>
            </div>
            <script>
                setTimeout(() => {
                    document.getElementById("toast").style.display = "none";
                }, 3000);
            </script>
        <?php unset($_SESSION['message']);
        endif; ?>

<div class="flex justify-center items-center p-6 w-full bg-gray-100">
    <div class="max-w-3xl w-full bg-white shadow-lg rounded-lg p-8 mt-6">
        <div class="flex flex-col items-center space-y-4 mb-6">
            <img src="logo/logo.png" alt="LGU Logo" class="w-16 h-16 rounded-full mb-2 border-4 border-yellow-500 shadow-lg">
            <span class="text-sm font-semibold text-gray-700 uppercase text-center flex items-center space-x-2">
                <i class="fa-solid fa-shield-dog text-yellow-500"></i> 
                <span>LGU - Pet Animal Welfare Protection System</span>
            </span>
            <h2 class="text-2xl font-semibold text-center text-[#0077b6]">Account Approval</h2>
        </div>

        <?php if ($result->num_rows > 0): ?>
            <div class="overflow-x-auto">
                <table class="w-full table-auto border-collapse border border-gray-300 rounded-lg shadow-sm">
                    <thead>
                        <tr class="bg-[#0077b6] text-white text-center">
                            <th class="border border-gray-300 p-4 ">Full Name</th>
                            <th class="border border-gray-300 p-4 ">Email</th>
                            <th class="border border-gray-300 p-4 ">House</th>
                            <th class="border border-gray-300 p-4 ">Street</th>
                            <th class="border border-gray-300 p-4 ">Barangay</th>
                            <th class="border border-gray-300 p-4 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr class="text-center bg-white hover:bg-gray-50 transition duration-300">
                                <td class="border border-gray-300 p-4"><?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?></td>
                                <td class="border border-gray-300 p-4"><?= htmlspecialchars($row['email']) ?></td>
                                <td class="border border-gray-300 p-4"><?= htmlspecialchars($row['house']) ?></td>
                                <td class="border border-gray-300 p-4"><?= htmlspecialchars($row['street']) ?></td>
                                <td class="border border-gray-300 p-4"><?= htmlspecialchars($row['barangay']) ?></td>
                                <td class="border border-gray-300 p-4">
                                    <div class="flex justify-center gap-3">
                                        <a href="?approve_id=<?= $row['id'] ?>" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition duration-200 text-sm">Approve</a>
                                        <a href="?reject_id=<?= $row['id'] ?>" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition duration-200 text-sm">Reject</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="text-center text-gray-500 p-6">No pending approval requests.</div>
        <?php endif; ?>
    </div>
</div>


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

            function updateTime() {
                let now = new Date();
                let timeString = now.toLocaleTimeString(); // Format: HH:MM:SS AM/PM
                document.getElementById("currentTime").textContent = timeString;
            }

            // Update time every second
            setInterval(updateTime, 1000);
            updateTime(); // Call once to display immediately

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
                document.getElementById('currentTime').textContent = formattedTime;
                document.getElementById('currentDate').textContent = formattedDate;
            }

            // Update time and date every minute
            setInterval(updateTimeAndDate, 60000);

            // Initial call to update the time and date immediately
            updateTimeAndDate();
        </script>

</body>

</html>