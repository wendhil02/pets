<?php
session_start();
include '../internet/connect_ka.php';
include 'design/top.php';

// Get logged-in email and user details from session
$user_email = isset($_SESSION['email']) ? $_SESSION['email'] : ''; 
$user_first_name = isset($_SESSION['first_name']) ? $_SESSION['first_name'] : '';
$user_middle_name = isset($_SESSION['middle_name']) ? $_SESSION['middle_name'] : '';
$user_last_name = isset($_SESSION['last_name']) ? $_SESSION['last_name'] : '';

if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'user') {
    header("Location: index.php");
    exit();
}

$email = $_SESSION['email'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Animal Cruelty</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-blue-900 font-poppins">
<div class="max-w-2xl mx-auto mt-6 p-4 bg-white rounded-lg shadow-md">

<!-- Report Header -->
<div class="relative">
    <a href="dashboard.php" class="absolute top-0 left-0 mt-3 ml-3 px-3 py-1 bg-blue-600 text-white text-xs font-semibold rounded shadow-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition">
        Back
    </a>
</div>
<?php if (isset($_SESSION['report_success'])): ?>
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded relative text-xs mb-4">
        <?php 
            echo $_SESSION['report_success']; 
            unset($_SESSION['report_success']); // Remove it after displaying
        ?>
    </div>
<?php endif; ?>


<div class="text-center mt-10">
    <h2 class="text-xl font-bold text-red-700">
         Report Animal Cruelty
    </h2>
    <p class="text-gray-600 mt-1 text-xs">Animal Cruelty Reporting System empowers individuals to take a stand against animal abuse by providing a secure and confidential platform to report incidents of neglect, mistreatment, and cruelty. Issues regarding animal cruelty in locals arises.Animal Cruelty Reporting System requires user collaboration to enhance efficacy in lowering animal abuse cases. Animal cruelty and neglect is a form of criminal offense and must be stopped right away. You as a concern citizen can help intervene and help animals live away from abuses as they also deserved to be treated right. If you witness any act of cruelty, do not stay silent—report it today and be a voice for the voiceless!</p>
</div>

<div class="border-t border-gray-300 my-3"></div>

<form action="submit_cruelty_report.php" method="POST" enctype="multipart/form-data" class="space-y-3">

    <!-- Reporter Information -->
  <div class="bg-gray-100 p-2 rounded">
    <h3 class="text-xs font-semibold text-gray-800">Your Information</h3>
    <p class="text-[11px] text-gray-600">Your email is auto-filled. You may choose to remain anonymous.</p>

    <!-- Email Field -->
    <label class="block mt-1">
        <span class="font-medium text-gray-700 text-xs">Email:</span>
        <input type="email" name="reporter_email" value="<?php echo htmlspecialchars($user_email); ?>" readonly class="w-full py-1 border-b border-gray-400 focus:outline-none focus:border-blue-500 text-xs">
    </label>

    <!-- First Name Field -->
    <label class="block mt-1">
        <span class="font-medium text-gray-700 text-xs">First Name:</span>
        <input type="text" name="first_name" value="<?php echo htmlspecialchars($user_first_name); ?>" readonly class="w-full py-1 border-b border-gray-400 focus:outline-none focus:border-blue-500 text-xs">
    </label>

    <!-- Middle Name Field -->
    <label class="block mt-1">
        <span class="font-medium text-gray-700 text-xs">Middle Name:</span>
        <input type="text" name="middle_name" value="<?php echo htmlspecialchars($user_middle_name); ?>" readonly class="w-full py-1 border-b border-gray-400 focus:outline-none focus:border-blue-500 text-xs">
    </label>

    <!-- Last Name Field -->
    <label class="block mt-1">
        <span class="font-medium text-gray-700 text-xs">Last Name:</span>
        <input type="text" name="last_name" value="<?php echo htmlspecialchars($user_last_name); ?>" readonly class="w-full py-1 border-b border-gray-400 focus:outline-none focus:border-blue-500 text-xs">
    </label>
</div>


    <!-- Incident Details -->
    <div class="bg-gray-100 p-2 rounded">
        <h3 class="text-xs font-semibold text-gray-800">Incident Details</h3>

        <label class="block mt-1">
            <span class="font-medium text-gray-700 text-xs">Location of Incident:</span>
            <input type="text" name="incident_location" required class="w-full py-1 border-b border-gray-400 focus:outline-none focus:border-blue-500 text-xs">
        </label>

        <label class="block mt-1">
            <span class="font-medium text-gray-700 text-xs">Date & Time:</span>
            <input type="datetime-local" name="incident_datetime" required class="w-full py-1 border-b border-gray-400 focus:outline-none focus:border-blue-500 text-xs">
        </label>

        <label class="block mt-1">
            <span class="font-medium text-gray-700 text-xs">Describe the Incident:</span>
            <textarea name="incident_description" required class="w-full py-1 border-b border-gray-400 focus:outline-none focus:border-blue-500 text-xs" rows="2"></textarea>
        </label>
    </div>

    <!-- Upload Video Evidence -->
    <div class="bg-gray-100 p-2 rounded">
        <h3 class="text-xs font-semibold text-gray-800">Upload Video Evidence</h3>
        <p class="text-[11px] text-gray-600">Attach a video (MP4, AVI, MOV, MKV). Max size: 5MB.</p>

        <label class="block mt-1">
            <input type="file" name="evidence" accept="video/mp4,video/avi,video/mov,video/mkv" required class="w-full py-1 border-b border-gray-400 focus:outline-none focus:border-blue-500 text-xs">
        </label>
    </div>

    <!-- Witness Confirmation -->
    <div class="bg-gray-100 p-2 rounded">
        <h3 class="text-xs font-semibold text-gray-800">Witness Information</h3>

        <label class="block mt-1">
            <span class="font-medium text-gray-700 text-xs">Are you willing to testify as a witness?</span>
            <select name="willing_to_testify" required class="w-full py-1 border-b border-gray-400 focus:outline-none focus:border-blue-500 text-xs">
                <option value="" disabled selected>Select an option</option>
                <option value="Yes">Yes, I am willing to testify</option>
                <option value="No">No, I want to remain anonymous</option>
            </select>
        </label>

        <label class="block mt-1">
            <span class="font-medium text-gray-700 text-xs">Social Media Account (Optional, for follow-ups):</span>
            <input type="text" name="social_media" placeholder="@facebookusername or @twitterhandle" class="w-full py-1 border-b border-gray-400 focus:outline-none focus:border-blue-500 text-xs placeholder-gray-500">
        </label>
    </div>

    <!-- Policy Agreement -->
    <div class="bg-gray-100 p-2 rounded border border-gray-300">
        <h3 class="text-xs font-semibold text-gray-800">Policy Agreement</h3>
        <p class="text-[11px] text-gray-600 mt-1">
           By submitting this report, you confirm that the information provided is accurate. False reporting may result in legal consequences
        </p>

        <label class="flex items-center mt-1">
            <input type="checkbox" name="agree_policy" required class="mr-1">
            <span class="text-[11px]">I agree to the terms and policies.</span>
        </label>
    </div>

    <!-- Submit Button -->
    <button type="submit" class="w-full bg-red-600 text-white p-1 rounded-md text-xs font-semibold hover:bg-red-700 transition"> Submit Report</button>
</form>

<!-- Button to Trigger Modal -->
<div class="mt-3 text-center">
    <button id="openModal" class="text-[11px] text-blue-600 hover:underline">View Animal Protection Laws</button>
</div>

</div>


    <!-- Modal for Animal Protection Laws -->
    <div id="modal" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center hidden">
   <div class="bg-white p-6 rounded-lg shadow-lg max-w-2xl w-full overflow-y-auto max-h-[90vh]">
    <h3 class="text-xl font-semibold text-gray-800 mb-4">📜 Terms and Conditions</h3>
    <p class="text-gray-700 mb-4">
        By submitting an Animal Cruelty Report through our platform, you agree to the following terms and conditions:
    </p>

    <ul class="list-disc pl-5 text-gray-700 space-y-4">
        <li>
            <strong>Truthful and Accurate Information</strong><br>
            You affirm that all information you provide is truthful, accurate, and based on your genuine knowledge or direct observation.
            False or misleading reports may hinder legitimate investigations and may result in restrictions on your access to our platform.
        </li>
        <li>
            <strong>Confidentiality and Anonymity</strong><br>
            While we allow anonymous reporting, any personal information you choose to provide will be handled in accordance with the
            <strong>Data Privacy Act of 2012 (RA 10173)</strong>. We are committed to protecting your identity and personal data, and will not disclose your information without your consent, unless required by law.
        </li>
        <li>
            <strong>Data Privacy Compliance</strong><br>
            All collected data—such as names, contact details, uploaded images, and report details—will be securely stored and processed solely for the purpose of addressing animal cruelty concerns. We ensure that your data is protected from unauthorized access and will not be used for purposes beyond what is stated without your explicit consent.
        </li>
        <li>
            <strong>Prohibited Content</strong><br>
            You agree not to submit any content that is offensive, defamatory, obscene, threatening, or otherwise unlawful. This includes uploading graphic content that is unnecessarily violent or abusive.
        </li>
        <li>
            <strong>Use of Submitted Information</strong><br>
            By submitting a report, you grant us permission to use the information, including attached files and images, for investigation, documentation, or legal purposes. This may involve forwarding the report to local authorities or animal welfare organizations, when appropriate.
        </li>
        <li>
            <strong>Responsibility</strong><br>
            You are solely responsible for the content of your report. We are not liable for any consequences resulting from false or malicious submissions.
        </li>
        <li>
            <strong>Changes to Terms</strong><br>
            We reserve the right to modify these terms at any time. Continued use of the reporting service constitutes your acceptance of any updated terms.
        </li>
    </ul>

    <div class="mt-6 text-center">
        <button id="closeModal" class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700">Close</button>
    </div>
</div>

    </div>

    <script>
    
        // Get Modal elements
        const modal = document.getElementById('modal');
        const openModalButton = document.getElementById('openModal');
        const closeModalButton = document.getElementById('closeModal');

        // Open Modal
        openModalButton.addEventListener('click', () => {
            modal.classList.remove('hidden');
        });

        // Close Modal
        closeModalButton.addEventListener('click', () => {
            modal.classList.add('hidden');
        });
           // Auto-hide the success alert after 3 seconds
 window.addEventListener('DOMContentLoaded', () => {
        const alert = document.getElementById('successAlert');
        if (alert) {
            setTimeout(() => {
                alert.classList.add('opacity-0');
                setTimeout(() => alert.remove(), 1000); // Remove from DOM after fade
            }, 3000); // 3 seconds before fading
        }
    });
    </script>
</body>

</html>