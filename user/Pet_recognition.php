<?php
include('./dbconn/config.php');
include('./dbconn/authentication.php');
?>

<!DOCTYPE html>
<html lang='en'>

<head>
    <?php include('./disc/partials/header.php'); ?>
    <script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs"></script>
    <script src="https://cdn.jsdelivr.net/npm/@tensorflow-models/mobilenet"></script>
    <style>
        .form-container {
            max-width: 800px;
            margin: auto;
            padding: 30px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .form-title {
            text-align: center;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .btn-submit {
            display: block;
            width: auto;
            padding: 12px 30px;
            border-radius: 5px;
            margin: 0 auto;
        }

        .container {
            max-width: 1000px;
            margin: auto;
            padding: 20px;
            background: #f9f9f9;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
    </style>
</head>

<body class='vertical light'>
    <div class='wrapper'>
        <?php include('./disc/partials/navbar.php'); ?>
        <?php include('./disc/partials/sidebar.php'); ?>

        <main role='main' class='main-content row'>
    <div class="container">
        <div class="row justify-content-center g-4">
            <!-- Image Upload and Recognition -->
            <div class="col-md-5">
                <div class="card card-custom shadow-lg p-4">
                    <h2 class="text-center fw-bold"><i class="fas fa-paw me-2"></i> Pet Recognition</h2>
                    <div class="mb-3">
                        <input type="file" id="fileInput" accept="image/*" class="form-control" onchange="previewImage()">
                    </div>
                    <button onclick="uploadAndClassify()" class="btn btn-primary w-100">
                        <i class="fas fa-paw me-2"></i> Classify Image
                    </button>
                    <div id="imagePreview" class="text-center mt-4 d-none">
                        <img id="previewImg" src="" alt="Preview" class="img-fluid rounded shadow">
                    </div>
                    <div id="info" class="mt-3 text-center d-none">
                        <h5 class="fw-semibold">Information</h5>
                        <p id="petInfo"></p>
                    </div>
                    <div class="text-center mt-3">
                        <button onclick="refreshPage()" class="btn btn-light btn-sm">
                            <i class="fas fa-sync-alt"></i> Refresh
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Camera Scanner -->
            <div class="col-md-5">
                <div class="card card-custom shadow-lg p-4">
                    <h2 class="text-center fw-bold"><i class="fas fa-camera-retro me-2"></i> Pet Scanner</h2>
                    <div class="border p-2 rounded shadow-sm">
                        <video id="video" class="w-100 rounded" autoplay></video>
                    </div>
                    <div id="cameraInfo" class="text-center mt-3 d-none">
                        <h5 class="fw-semibold">Camera Prediction</h5>
                        <p id="cameraPetInfo"></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

        </main>
    </div>

<?php include('./script.php'); ?>
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
                    document.getElementById('imagePreview').classList.remove('d-none');
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
                    document.getElementById('imagePreview').classList.remove('d-none');
                    document.getElementById('info').classList.remove('d-none');
                    document.getElementById('petInfo').innerText = `Prediction: ${prediction[0].className} with ${Math.round(prediction[0].probability * 100)}% confidence.`;
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
                cameraInfo.classList.remove('d-none');
                cameraPetInfo.innerText = `Prediction: ${predictions[0].className} with ${Math.round(predictions[0].probability * 100)}% confidence.`;
            } else {
                cameraInfo.classList.add('d-none');
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
