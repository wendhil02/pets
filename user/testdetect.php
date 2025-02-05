<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pet Recognition</title>
    <script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs"></script>
    <script src="https://cdn.jsdelivr.net/npm/@tensorflow-models/mobilenet"></script>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.0.0/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>

<body class="relative bg-gray-100 font-sans">

    <!-- Background with Overlay -->
    <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('backone.jpg'); z-index: -1;">
        <div class="absolute inset-0 bg-black opacity-50"></div> 
    </div>

    <div class="relative flex justify-center items-center min-h-screen px-4 flex-col sm:flex-row space-y-6 sm:space-y-0 sm:space-x-6">

        <!-- Left Box: Upload and Classify -->
        <div class="bg-[#DFA16A] p-6 rounded-lg shadow-lg shadow-white w-full sm:w-80 md:w-96 lg:w-1/3 xl:w-1/4 h-auto flex flex-col max-w-full relative z-10">
            <h1 class="text-3xl font-extrabold text-center text-white mb-6 flex items-center justify-center">
                <i class="fas fa-paw mr-2"></i> Pet Recognition
            </h1>

            <!-- Image Upload Section -->
            <div class="space-y-4 flex-grow">
                <div>
                    <input type="file" id="fileInput" accept="image/*"
                        class="block w-full text-sm text-white-700 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border file:border-gray-300 file:bg-gray-50 hover:file:bg-gray-100"
                        onchange="previewImage()">
                </div>

                <!-- Classify Button -->
                <button onclick="uploadAndClassify()"
                    class="w-full bg-blue-500 text-white py-2 rounded-md hover:bg-blue-600 transition duration-200 flex items-center justify-center">
                    <i class="fas fa-paw mr-2"></i> Classify Image
                </button>
            </div>

            <!-- Image Preview -->
            <div id="imagePreview" class="mt-6 hidden text-center">
                <img id="previewImg" src="" alt="Preview"
                    class="w-64 h-64 object-cover rounded-md mx-auto shadow-md">
            </div>

            <!-- Information Display -->
            <div id="info" class="mt-4 text-center hidden">
                <h2 class="text-xl font-semibold text-white">Information</h2>
                <p id="petInfo" class="text-white"></p>
            </div>

            <!-- Refresh Button -->
            <div class="mt-4 flex justify-center">
                <button onclick="refreshPage()" class="text-white hover:text-gray-800 p-2 rounded-full focus:outline-none">
                    <i class="fas fa-sync-alt h-6 w-6"></i>
                </button>
            </div>
        </div>

        <!-- Right Box: Camera Feed -->
        <div class="bg-[#DFA16A] p-6 rounded-lg shadow-lg shadow-white w-full sm:w-80 md:w-96 lg:w-1/3 xl:w-1/4 h-auto flex flex-col max-w-full relative z-10">
            <h1 class="text-3xl font-bold text-center text-white mb-6 flex items-center justify-center">
                <i class="fas fa-camera-retro mr-2"></i> Pets Scanner
            </h1>

            <!-- Camera Feed Section -->
            <div class="mb-6 border-1 p-4 rounded-md shadow-md flex-grow">
                <video id="video" width="100%" height="auto" autoplay class="border rounded-md shadow-md w-full"></video>
            </div>

            <!-- Information Below the Camera -->
            <div id="cameraInfo" class="text-center hidden">
                <h2 class="text-xl font-semibold text-white">Camera Prediction</h2>
                <p id="cameraPetInfo" class="text-gray-800"></p>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="absolute bottom-2 w-full text-center text-white text-sm z-10">
        © 2025 Pet Recognition. All Rights Reserved.
    </footer>

    <script>
        let model;
        const video = document.getElementById("video");

        async function loadModel() {
            model = await mobilenet.load();
            console.log("Model loaded successfully!");
            classifyCameraFrame();
        }

        async function setupCamera() {
            try {
                const stream = await navigator.mediaDevices.getUserMedia({ video: true });
                video.srcObject = stream;
            } catch (error) {
                console.error("Error accessing the camera: ", error);
                alert("Could not access the camera. Please check your permissions.");
            }
        }

        function previewImage() {
            const inputFile = document.getElementById('fileInput');
            if (inputFile.files && inputFile.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('previewImg').src = e.target.result;
                    document.getElementById('imagePreview').classList.remove('hidden');
                };
                reader.readAsDataURL(inputFile.files[0]);
            }
        }

        async function uploadAndClassify() {
            if (!model) {
                alert("Model is still loading. Please wait.");
                return;
            }

            const inputFile = document.getElementById('fileInput');
            if (inputFile.files.length > 0) {
                const img = new Image();
                img.src = URL.createObjectURL(inputFile.files[0]);
                img.onload = async () => {
                    const prediction = await model.classify(img);

                    document.getElementById('previewImg').src = img.src;
                    document.getElementById('imagePreview').classList.remove('hidden');

                    document.getElementById('info').classList.remove('hidden');
                    document.getElementById('petInfo').innerText = 
                        `Prediction: ${prediction[0].className} with ${Math.round(prediction[0].probability * 100)}% confidence.`;
                };
            }
        }

        async function classifyCameraFrame() {
            if (!model) {
                setTimeout(classifyCameraFrame, 3000);
                return;
            }

            const canvas = document.createElement("canvas");
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            const ctx = canvas.getContext("2d");

            ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
            const imageTensor = tf.browser.fromPixels(canvas);
            const predictions = await model.classify(imageTensor);
            imageTensor.dispose();

            const cameraInfo = document.getElementById('cameraInfo');
            const cameraPetInfo = document.getElementById('cameraPetInfo');

            if (predictions[0].className.toLowerCase().includes("cat") || predictions[0].className.toLowerCase().includes("dog")) {
                cameraInfo.classList.remove('hidden');
                cameraPetInfo.innerText = `Prediction: ${predictions[0].className} with ${Math.round(predictions[0].probability * 100)}% confidence.`;
            } else {
                cameraInfo.classList.add('hidden');
            }

            setTimeout(classifyCameraFrame, 3000);
        }

        function refreshPage() {
            location.reload();
        }

        loadModel();
        setupCamera();
    </script>

</body>
</html>
