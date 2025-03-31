<?php

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pet Recognition</title>
    <script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs"></script>
    <script src="https://cdn.jsdelivr.net/npm/@teachablemachine/image@0.8.3/dist/teachablemachine-image.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<style>
    .scroll-container {
        width: 100%;
        overflow-x: auto;
        /* Keep the scrollbar visible */
        white-space: nowrap;
        position: relative;
    }

    .scroll-content {
        display: flex;
        gap: 1rem;
        width: max-content;
        padding: 10px;
    }
</style>

<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white shadow-lg rounded-2xl p-6 max-w-md w-full text-center">
        <h2 class="text-2xl font-semibold text-gray-800 mb-4">Upload a Pet Image</h2>

        <!-- File upload -->
        <label class="cursor-pointer bg-blue-500 text-white py-2 px-4 rounded-lg hover:bg-blue-600 transition">
            Pet Image
            <input type="file" id="imageUpload" accept="image/*" class="hidden">
        </label>

        <!-- Image preview -->
        <div class="flex flex-col items-center justify-center">
            <img id="previewImage" class="max-w-xs mt-4 rounded-lg shadow-md hidden" src="#" alt="Preview">
        </div>

        <!-- Loading spinner (Hidden by Default) -->
        <div id="loadingSpinner" class="flex flex-col items-center justify-center mt-4 hidden">
            <div class="animate-spin rounded-full h-10 w-10 border-4 border-blue-500 border-t-transparent"></div>
            <p class="text-gray-600 text-sm mt-2">Processing...</p>
        </div>

        <!-- Result display -->
        <p id="result" class="text-lg font-semibold text-gray-700 mt-4 hidden">🔍 Waiting for image...</p>

        <!-- Horizontal scrolling image gallery -->
        <div class="overflow-x-auto mt-6 scroll-container">
            <div class="flex space-x-4 scroll-content">
                <!-- Abyssinian -->
                <div id="petContainer" class="flex gap-4 overflow-x-auto whitespace-nowrap"></div>

            </div>
        </div>



        <script>
            document.addEventListener("DOMContentLoaded", function() {
                const petContainer = document.getElementById("petContainer");

                // 🔹 List of breeds and their folder structure
                const pets = [{
                        "folder": "abyssinian",
                        "file": "Abyssinian",
                        "count": 5
                    },
                    {
                        "folder": "american",
                        "file": "american",
                        "count": 224
                    },
                    {
                        "folder": "bengal",
                        "file": "bengal",
                        "count": 204
                    },
                    {
                        "folder": "basset",
                        "file": "basset",
                        "count": 200
                    },
                    {
                        "folder": "beagle",
                        "file": "beagle",
                        "count": 50
                    },
                    {
                        "folder": "birman",
                        "file": "birman",
                        "count": 50
                    },
                    {
                        "folder": "bombay",
                        "file": "bombay",
                        "count": 50
                    },
                    {
                        "folder": "boxer",
                        "file": "boxer",
                        "count": 50
                    },
                    {
                        "folder": "british",
                        "file": "British",
                        "count": 50
                    },
                    {
                        "folder": "chihuahua",
                        "file": "chihuahua",
                        "count": 50
                    },
                    {
                        "folder": "egyptian",
                        "file": "Egyptian",
                        "count": 50
                    },
                    {
                        "folder": "english",
                        "file": "english",
                        "count": 50
                    },
                    {
                        "folder": "german",
                        "file": "german",
                        "count": 50
                    },
                    {
                        "folder": "goldenretriever",
                        "file": "goldenretriever",
                        "count": 100
                    },
                    {
                        "folder": "great",
                        "file": "great",
                        "count": 50
                    },
                    {
                        "folder": "havanese",
                        "file": "havanese",
                        "count": 50
                    },
                    {
                        "folder": "japanese",
                        "file": "japanese",
                        "count": 50
                    },
                    {
                        "folder": "keeshond",
                        "file": "keeshond",
                        "count": 50
                    },
                    {
                        "folder": "leonberger",
                        "file": "leonberger",
                        "count": 50
                    },
                    {
                        "folder": "maine",
                        "file": "Maine",
                        "count": 50
                    },
                    {
                        "folder": "miniature",
                        "file": "miniature",
                        "count": 50
                    },
                    {
                        "folder": "newfoundland",
                        "file": "newfoundland",
                        "count": 50
                    },
                    {
                        "folder": "persian",
                        "file": "persian",
                        "count": 50
                    },
                    {
                        "folder": "pomeranian",
                        "file": "pomeranian",
                        "count": 50
                    },
                    {
                        "folder": "pug",
                        "file": "pug",
                        "count": 50
                    },
                    {
                        "folder": "ragdoll",
                        "file": "ragdoll",
                        "count": 50
                    },
                    {
                        "folder": "russian",
                        "file": "Russian",
                        "count": 50
                    },
                    {
                        "folder": "saint",
                        "file": "saint",
                        "count": 50
                    },
                    {
                        "folder": "samoyed",
                        "file": "samoyed",
                        "count": 50
                    },
                    {
                        "folder": "scottish",
                        "file": "Scottish",
                        "count": 50
                    },
                    {
                        "folder": "shiba",
                        "file": "shiba",
                        "count": 50
                    },
                    {
                        "folder": "siamese",
                        "file": "siamese",
                        "count": 50
                    },
                    {
                        "folder": "sphynx",
                        "file": "sphynx",
                        "count": 50
                    },
                    {
                        "folder": "staffordshire",
                        "file": "staffordshire",
                        "count": 50
                    },
                    {
                        "folder": "wheaten",
                        "file": "wheaten",
                        "count": 50
                    },
                    {
                        "folder": "yorkshire",
                        "file": "yorkshire",
                        "count": 50
                    }
                ];

                pets.forEach(pet => {
                    const randomNum = Math.floor(Math.random() * pet.count) + 1;
                    const imagePath = `../newspetimage/${pet.folder}/${pet.file}_${randomNum}.jpg`;

                    const petDiv = document.createElement("div");
                    petDiv.className = "flex-shrink-0 text-center";
                    petDiv.innerHTML = `
                <img class="h-32 w-32 rounded-lg" src="${imagePath}" alt="${pet.file}">
                <p class="text-gray-700 mt-2">${pet.file.replace(/_/g, " ")}</p>
            `;
                    petContainer.appendChild(petDiv);
                });
            });

            document.addEventListener("DOMContentLoaded", function() {
                const container = document.querySelector(".scroll-container");
                let scrollSpeed = 1; // Adjust speed
                let direction = 1; // 1 for forward, -1 for reverse

                function autoScroll() {
                    container.scrollLeft += scrollSpeed * direction;

                    // Kapag umabot sa dulo, mag-reverse
                    if (container.scrollLeft >= container.scrollWidth - container.clientWidth) {
                        direction = -1; // Reverse
                    } else if (container.scrollLeft <= 0) {
                        direction = 1; // Forward ulit
                    }
                }

                setInterval(autoScroll, 25); // Smooth scrolling effect
            });


            let model;
            const modelURL = "../Mymodel/model.json";
            const metadataURL = "../Mymodel/metadata.json";

            async function loadModel() {
                if (model) return;
                try {
                    model = await tmImage.load(modelURL, metadataURL);
                    console.log("✅ Model Loaded!");
                } catch (error) {
                    console.error("❌ Failed to load model:", error);
                    document.getElementById("result").innerText = "❌ Failed to load model.";
                }
            }

            async function predictImage(imageElement) {
                const resultText = document.getElementById("result");
                const spinner = document.getElementById("loadingSpinner");

                if (!model) {
                    resultText.innerText = "⚠️ Model is not loaded yet. Loading...";
                    await loadModel();
                }

                // Show spinner
                spinner.classList.remove("hidden");
                resultText.classList.add("hidden");

                console.log("🔍 Predicting image...");
                const prediction = await model.predict(imageElement);

                let maxProb = 0;
                let bestClass = "Unknown";

                prediction.forEach((p) => {
                    if (p.probability > maxProb) {
                        maxProb = p.probability;
                        bestClass = p.className || "Unknown";
                    }
                });

                console.log(`✅ Best Match: ${bestClass} (${(maxProb * 100).toFixed(2)}%)`);

                const validBreeds = [
                    "abyssinian", "american", "basset", "beagle", "bengal", "bird", "birman", "bombay", "boxer", "british",
                    "chihuahua", "egyptian", "english", "german", "goldenretriever", "great", "havanese", "japanese",
                    "keeshond", "leonberger", "maine", "miniature", "newfoundland", "persian", "pomeranian", "pug",
                    "ragdoll", "russian", "saint", "samoyed", "scottish", "shiba", "siamese", "sphynx", "staffordshire",
                    "wheaten", "yorkshire"
                ];

                const isPet = validBreeds.some(breed => bestClass.toLowerCase().includes(breed));

                // Hide spinner and show result
                spinner.classList.add("hidden");
                resultText.classList.remove("hidden");

                if (!isPet || maxProb < 0.85) {
                    resultText.innerHTML = `No record found.`;
                    console.log(`❌ Detected object is NOT a pet.`);
                    return;
                }

                resultText.innerHTML = `✅ Best Match: <strong>${bestClass}</strong> (${(maxProb * 100).toFixed(2)}%)`;
            }

            document.getElementById("imageUpload").addEventListener("change", function(event) {
                let file = event.target.files[0];
                let reader = new FileReader();

                if (!file) return; // Huwag gawin kung walang pinili

                reader.onload = function(e) {
                    let img = document.getElementById("previewImage");
                    img.src = e.target.result;
                    img.classList.remove("hidden"); // Ipakita ang image preview

                    // Ipakita ang spinner bago mag-predict
                    document.getElementById("loadingSpinner").classList.remove("hidden");

                    img.onload = function() {
                        predictImage(img);
                    };
                };

                reader.readAsDataURL(file);
            });

            window.onload = loadModel;
        </script>
</body>

</html>