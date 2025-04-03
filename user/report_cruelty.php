<?php
session_start();
$user_email = isset($_SESSION['email']) ? $_SESSION['email'] : ''; // Get logged-in email
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

<div class="text-center mt-10">
    <h2 class="text-xl font-bold text-red-700">
        🐾 Report Animal Cruelty
    </h2>
    <p class="text-gray-600 mt-1 text-xs">Help protect animals by reporting cruelty. Fill out the form with as much detail as possible.</p>
</div>

<div class="border-t border-gray-300 my-3"></div>

<form action="submit_cruelty_report.php" method="POST" enctype="multipart/form-data" class="space-y-3">

    <!-- Reporter Information -->
    <div class="bg-gray-100 p-2 rounded">
        <h3 class="text-xs font-semibold text-gray-800">Your Information</h3>
        <p class="text-[11px] text-gray-600">Your email is auto-filled. You may choose to remain anonymous.</p>

        <label class="block mt-1">
            <span class="font-medium text-gray-700 text-xs">Email:</span>
            <input type="email" name="reporter_email" value="<?php echo htmlspecialchars($user_email); ?>" readonly class="w-full py-1 border-b border-gray-400 focus:outline-none focus:border-blue-500 text-xs">
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
            By submitting this report, you confirm that the information provided is accurate. False reporting may result in legal consequences.
        </p>

        <label class="flex items-center mt-1">
            <input type="checkbox" name="agree_policy" required class="mr-1">
            <span class="text-[11px]">I agree to the terms and policies.</span>
        </label>
    </div>

    <!-- Submit Button -->
    <button type="submit" class="w-full bg-red-600 text-white p-1 rounded-md text-xs font-semibold hover:bg-red-700 transition">📩 Submit Report</button>
</form>

<!-- Button to Trigger Modal -->
<div class="mt-3 text-center">
    <button id="openModal" class="text-[11px] text-blue-600 hover:underline">View Animal Protection Laws</button>
</div>

</div>


    <!-- Modal for Animal Protection Laws -->
    <div id="modal" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center hidden">
        <div class="bg-white p-6 rounded-lg shadow-lg max-w-2xl w-full">
            <h3 class="text-xl font-semibold text-gray-800 mb-4">📜 Animal Protection Laws</h3>
            <ul class="list-disc pl-5 text-gray-700 space-y-2">
                <li><strong>Republic Act No. 8485 (Animal Welfare Act)</strong> – Protects animals from cruelty, maltreatment, and neglect.</li>
                <li><strong>Republic Act No. 10631</strong> – Amends RA 8485, strengthening penalties against animal cruelty.</li>
                <li><strong>Republic Act No. 9482</strong> – Anti-Rabies Act, promoting responsible pet ownership.</li>
            </ul>

            <div class="mt-4 text-center">
                <button id="closeModal" class="bg-red-600 text-white p-2 rounded-md hover:bg-red-700">Close</button>
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
    </script>
</body>

</html>