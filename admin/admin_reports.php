<?php
ob_start();
session_start();
include 'design/top.php';
include 'design/mid.php';
include '../internet/connect_ka.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer-master/src/Exception.php';
require '../PHPMailer-master/src/PHPMailer.php';
require '../PHPMailer-master/src/SMTP.php';

// Check if user is admin
if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

// PHPMailer Setup
function sendEmail($to, $subject, $body) {
    $mail = new PHPMailer(true);
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';  // Set your SMTP server here
        $mail->SMTPAuth = true;
        $mail->Username = 'wendhil10@gmail.com'; // SMTP username
        $mail->Password = 'ffml onzu stox lcwb'; // SMTP password (generated app password)
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Recipients
        $mail->setFrom('your_email@gmail.com', 'Admin');
        $mail->addAddress($to); // Add recipient

        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $body;

        $mail->send();
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
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

        // Get the report details to send the email
        $select_sql = "SELECT * FROM cruelty_reports WHERE id = ?";
        $stmt_select = $conn->prepare($select_sql);
        $stmt_select->bind_param("i", $report_id);
        $stmt_select->execute();
        $result = $stmt_select->get_result();
        $report = $result->fetch_assoc();

        $to = $report['reporter_email'];
        $subject = "Your Animal Cruelty Report - Approved";
        $body = "<p>Hello,</p>
                 <p>Your report regarding animal cruelty has been approved. Thank you for bringing this issue to our attention.</p>
                 <p>Incident Location: {$report['incident_location']}<br>
                 Incident Date & Time: {$report['incident_datetime']}<br>
                 Description: {$report['incident_description']}</p>
                 <p>Best regards,<br>Admin Team</p>";

        sendEmail($to, $subject, $body); // Send approval email
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

        // Get the report details to send the email
        $select_sql = "SELECT * FROM cruelty_reports WHERE id = ?";
        $stmt_select = $conn->prepare($select_sql);
        $stmt_select->bind_param("i", $report_id);
        $stmt_select->execute();
        $result = $stmt_select->get_result();
        $report = $result->fetch_assoc();

        $to = $report['reporter_email'];
        $subject = "Your Animal Cruelty Report - Archived";
        $body = "<p>Hello,</p>
                 <p>Your report regarding animal cruelty has been archived. Thank you for your submission.</p>
                 <p>Incident Location: {$report['incident_location']}<br>
                 Incident Date & Time: {$report['incident_datetime']}<br>
                 Description: {$report['incident_description']}</p>
                 <p>Best regards,<br>Admin Team</p>";

        sendEmail($to, $subject, $body); // Send archived email
    }

    header("Location: ".$_SERVER['PHP_SELF']);
    exit();
}

// Fetch reports
$sql = "SELECT * FROM cruelty_reports ORDER BY submitted_at DESC";
$result = $conn->query($sql);

// End output buffering and send headers
ob_end_flush();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Animal Cruelty Reports</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex bg-gray-100 font-poppins">
    <div id="mainContent" class="main-content flex-1 transition-all">
        <!-- Navbar -->
        <nav class="bg-[#0077b6] shadow-md mt-3 mr-2 ml-2 p-2 flex items-center justify-between rounded-lg max-w-auto mx-auto">
            <!-- ☰ Button (For PC and Mobile) -->
            <button id="toggleSidebar" class="text-white text-lg px-2 py-1 hover:bg-blue-100 rounded-md border border-transparent">
                ☰
            </button>
            <span class="font-bold text-white text-sm md:text-base lg:text-lg">Welcome, Wendhil Himarangan</span>
        </nav>

        <!-- Dashboard Content -->
      
    <div class="max-w-6xl mx-auto mt-8 p-6 bg-white rounded-lg shadow-md mr-3 ml-3">
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
        </script>
</body>
</html>

<?php
$conn->close();
?>

