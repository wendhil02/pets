<?php
session_start();
include 'design/top.php';
// ✅ Ensure user is logged in
if (!isset($_SESSION['email'])) {
    // If not logged in, redirect to login page
    header("Location: ../index.php"); // or the file where login form is
    exit();
}

// You can also check if the session_key exists to make sure the session is valid
if (!isset($_SESSION['session_key'])) {
    // If session_key is missing, log the user out
    header("Location: ../logout.php"); // or the logout handler
    exit();
}

$first_name = $_SESSION['first_name'];
$middle_name = $_SESSION['middle_name'];
$last_name = $_SESSION['last_name'];
$session_key = $_SESSION['session_key'];

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

<body class="bg-white flex items-center justify-center min-h-screen">
    <div class="bg-gray-700 shadow-lg rounded-2xl p-6 max-w-md w-full text-center">
                    <div class="relative">
    <a href="dashboard.php" class="absolute top-0 left-0 mt-3 ml-3 px-3 py-1 bg-blue-600 text-white text-xs font-semibold rounded shadow-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition">
        Back
    </a>
</div>
    <div class="p-2 flex flex-col items-center space-y-3 px-4 text-white">
        <img src="logo/logo.png" alt="LGU Logo" class="w-12 h-12 rounded-full mb-2 border-2 border-yellow-500">
        <span class="text-sm font-semibold text-whie  text-center">

                    <i class="fa-solid fa-shield-dog text-yellow-500"></i> LGU - Pet Animal Welfare Protection System
                     <p><i class="fa-solid fa-magnifying-glass mr-2"></i>Pet Image Recognition</p>
                </span>
    </div>
<br>
   
        <label class="cursor-pointer bg-blue-500 text-white py-2 px-4 rounded-lg hover:bg-blue-600 transition">
            Pet Image
           <input type="file" id="imageUpload" accept=".jpg,.jpeg,.png,.gif,.bmp,.tiff" class="hidden">

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
        <div id="breedDescription" class="mt-4 text-gray-600 hidden"></div>

        <!-- Horizontal scrolling image gallery -->
        <div class="overflow-x-auto mt-6 scroll-container">
      
            <div class="flex space-x-4 scroll-content">
                <!-- Abyssinian -->
                <div id="petContainer" class="flex gap-4 overflow-x-auto whitespace-nowrap "></div>

            </div>
        </div>



        <script>
            document.addEventListener("DOMContentLoaded", function() {
                const petContainer = document.getElementById("petContainer");

                // 🔹 List of breeds and their folder structure
                const pets = [{
                        "folder": "Abyssinian",
                        "file": "Abyssinian",
                        "count": 10
                    },
                    {
                        "folder": "american",
                        "file": "american",
                        "count": 3
                    },
                    {
                        "folder": "Bengal",
                        "file": "bengal",
                        "count": 3
                    },
                    {
                        "folder": "basset",
                        "file": "basset",
                        "count": 3
                    },
                    {
                        "folder": "beagle",
                        "file": "beagle",
                        "count": 5
                    },
                    {
                        "folder": "birman",
                        "file": "birman",
                        "count": 3
                    },
                    {
                        "folder": "bombay",
                        "file": "bombay",
                        "count": 3
                    },
                    {
                        "folder": "boxer",
                        "file": "boxer",
                        "count": 3
                    },
                    {
                        "folder": "british",
                        "file": "British",
                        "count": 5
                    },
                    {
                        "folder": "chihuahua",
                        "file": "chihuahua",
                        "count": 5
                    },
                    {
                        "folder": "egyptian",
                        "file": "Egyptian",
                        "count": 5
                    },
                    {
                        "folder": "english",
                        "file": "english",
                        "count": 5
                    },
                    {
                        "folder": "german",
                        "file": "german",
                        "count": 5
                    },
                    {
                        "folder": "goldenretriever",
                        "file": "goldenretriever",
                        "count": 3
                    },
                    {
                        "folder": "great",
                        "file": "great",
                        "count": 5
                    },
                    {
                        "folder": "havanese",
                        "file": "havanese",
                        "count": 5
                    },
                    {
                        "folder": "japanese",
                        "file": "japanese",
                        "count": 5
                    },
                    {
                        "folder": "keeshond",
                        "file": "keeshond",
                        "count": 5
                    },
                    {
                        "folder": "leonberger",
                        "file": "leonberger",
                        "count": 5
                    },
                    {
                        "folder": "maine",
                        "file": "Maine",
                        "count": 5
                    },
                    {
                        "folder": "miniature",
                        "file": "miniature",
                        "count": 5
                    },
                    {
                        "folder": "newfoundland",
                        "file": "newfoundland",
                        "count": 5
                    },
                    {
                        "folder": "persian",
                        "file": "persian",
                        "count": 5
                    },
                    {
                        "folder": "pomeranian",
                        "file": "pomeranian",
                        "count": 5
                    },
                    {
                        "folder": "pug",
                        "file": "pug",
                        "count": 5
                    },
                    {
                        "folder": "ragdoll",
                        "file": "ragdoll",
                        "count": 5
                    },
                    {
                        "folder": "russian",
                        "file": "Russian",
                        "count": 5
                    },
                    {
                        "folder": "saint",
                        "file": "saint",
                        "count": 5
                    },
                    {
                        "folder": "samoyed",
                        "file": "samoyed",
                        "count": 5
                    },
                    {
                        "folder": "scottish",
                        "file": "Scottish",
                        "count": 5
                    },
                    {
                        "folder": "shiba",
                        "file": "shiba",
                        "count": 5
                    },
                    {
                        "folder": "siamese",
                        "file": "siamese",
                        "count": 5
                    },
                    {
                        "folder": "sphynx",
                        "file": "sphynx",
                        "count": 5
                    },
                    {
                        "folder": "staffordshire",
                        "file": "staffordshire",
                        "count": 5
                    },
                    {
                        "folder": "wheaten",
                        "file": "wheaten",
                        "count": 5
                    },
                    {
                        "folder": "yorkshire",
                        "file": "yorkshire",
                        "count": 5
                    }
                ];

                pets.forEach(pet => {
                    const randomNum = Math.floor(Math.random() * pet.count) + 1;
                    const imagePath = `../newspetimage/${pet.folder}/${pet.file}_${randomNum}.jpg`;

                    const petDiv = document.createElement("div");
                    petDiv.className = "flex-shrink-0 text-center";
                    petDiv.innerHTML = `
                <img class="h-32 w-32 rounded-lg" src="${imagePath}" alt="${pet.file}">
                <p class="text-white mt-2">${pet.file.replace(/_/g, " ")}</p>
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
                    console.log(" Model Loaded!");
                } catch (error) {
                    console.error("❌ Failed to load model:", error);
                    document.getElementById("result").innerText = " Failed to load model.";
                }
            }

            async function predictImage(imageElement) {
                const resultText = document.getElementById("result");
                const spinner = document.getElementById("loadingSpinner");
                const descriptionText = document.getElementById("breedDescription"); // Element for breed description

                if (!model) {
                    resultText.innerText = "⚠️ Model is not loaded yet. Loading...";
                    await loadModel();
                }

                // Show spinner
                spinner.classList.remove("hidden");
                resultText.classList.add("hidden");
                descriptionText.classList.add("hidden"); // Hide description initially

                console.log(" Predicting image...");
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

                resultText.innerHTML = `<p class="text-white">✅ Best Match: <strong class="text-white">${bestClass}</strong> (${(maxProb * 100).toFixed(2)}%)</p>`;


                const breedDescriptions = {
                    "abyssinian": "The Abyssinian is a highly energetic and playful breed, known for its graceful and agile nature. They are one of the oldest known cat breeds, with a distinctive ticked coat.",
                    "american": "The American breed, often referring to American Shorthair or American Cats, is known for its gentle and easygoing nature. They are affectionate and make great family pets.",
                    "basset": "Basset Hounds are known for their long ears, short legs, and strong sense of smell. They are friendly, loyal, and make excellent companions.",
                    "beagle": "Beagles are small to medium-sized dogs with a friendly and curious temperament. They are known for their excellent sense of smell and are often used in detection work.",
                    "bengal": "Bengals are large, muscular cats with a distinctive spotted or marbled coat that looks like a wild leopard. They are active, intelligent, and playful.",
                    "bird": "Birds as pets, like parrots or canaries, are social, intelligent, and can mimic sounds or speech. They are known for their colorful feathers and playful personalities.",
                    "birman": "Birmans are affectionate and social cats with stunning blue eyes and a silky coat. They are often referred to as 'Sacred Cats of Burma' and are known for their friendly demeanor.",
                    "bombay": "Bombay cats are sleek, black cats with a playful personality. They are affectionate, intelligent, and enjoy being around their human family members.",
                    "boxer": "Boxers are strong, medium-sized dogs known for their energetic and friendly nature. They are loyal, protective, and make great family pets.",
                    "british": "British Shorthairs are calm, easygoing, and independent cats. They are known for their round faces, dense fur, and are often friendly and laid-back.",
                    "chihuahua": "Chihuahuas are the smallest dog breed and are known for their bold and sassy personalities. Despite their small size, they have big hearts and are loyal companions.",
                    "egyptian": "Egyptian Mau cats are known for their striking spotted coat and green eyes. They are intelligent, playful, and have a strong attachment to their families.",
                    "english": "English breeds, such as English Bulldogs or English Cocker Spaniels, are known for their calm and friendly demeanor. They are affectionate and make great companions.",
                    "german": "German Shepherds are strong, intelligent, and loyal dogs. Known for their versatility, they are often used as service dogs or police dogs due to their intelligence and training ability.",
                    "goldenretriever": "Golden Retrievers are friendly, intelligent, and devoted dogs. They are one of the most popular family pets due to their affectionate nature and trainability.",
                    "great": "Great Danes are large, majestic dogs known for their gentle and friendly nature. Despite their size, they are known as 'gentle giants' and are affectionate with their families.",
                    "havanese": "Havanese are small, friendly dogs with a long, silky coat. They are playful, affectionate, and make great companions for families or individuals.",
                    "japanese": "Japanese breeds, like the Japanese Chin, are known for their graceful appearance and calm demeanor. They are loyal and form strong bonds with their families.",
                    "keeshond": "Keeshonds are medium-sized dogs with a thick, plush coat and a friendly, outgoing personality. They are known for their 'smiling' expression and are loyal companions.",
                    "leonberger": "Leonbergers are large, gentle dogs with a friendly and calm nature. They are excellent family pets and are known for their impressive size and majestic appearance.",
                    "maine": "Maine Coons are large, friendly cats with long, luxurious fur and tufted ears. They are playful, affectionate, and are known for being one of the largest domestic cat breeds.",
                    "miniature": "Miniature breeds, such as Miniature Schnauzers or Miniature Poodles, are smaller versions of their larger counterparts. They retain the traits of their breed but in a more compact size.",
                    "newfoundland": "Newfoundlands are large, gentle dogs with a calm and patient demeanor. They are known for their swimming ability and often serve as water rescue dogs.",
                    "persian": "Persian cats are known for their luxurious long coats and calm, laid-back personalities. They are affectionate, though they tend to be more independent than some other breeds.",
                    "pomeranian": "Pomeranians are small dogs with a fluffy coat and lively, friendly personalities. Despite their small size, they are energetic and love to be the center of attention.",
                    "pug": "Pugs are small dogs with a distinct wrinkled face and a playful, friendly demeanor. They are affectionate, loyal, and make excellent companions.",
                    "ragdoll": "Ragdolls are large, affectionate cats with a soft, semi-long coat. They are known for their docile temperament and often go limp when held, hence the name 'Ragdoll.'",
                    "russian": "Russian Blue cats are known for their striking blue-gray coat and green eyes. They are gentle, intelligent, and can be quite reserved around strangers.",
                    "saint": "Saint Bernards are large, gentle dogs originally bred for rescue work in the Swiss Alps. They are known for their calm, patient nature and impressive size.",
                    "samoyed": "Samoyeds are fluffy, white dogs with a friendly and gentle disposition. They are known for their 'Sammy smile' and are great family companions.",
                    "scottish": "Scottish Fold cats are known for their unique folded ears and round faces. They are affectionate, playful, and get along well with other pets and children.",
                    "shiba": "Shiba Inus are small, fox-like dogs from Japan. They are independent, intelligent, and can be reserved with strangers, but are loyal to their families.",
                    "siamese": "Siamese cats are known for their striking blue almond-shaped eyes and sleek coats. They are social, vocal, and affectionate with their human companions.",
                    "sphynx": "Sphynx cats are hairless, with wrinkled skin and large ears. Despite their appearance, they are known for being affectionate, playful, and love attention.",
                    "staffordshire": "Staffordshire Bull Terriers are strong, muscular dogs known for their loyalty and friendly nature. They are affectionate and make great family pets.",
                    "wheaten": "Soft Coated Wheaten Terriers are medium-sized dogs with a soft, silky coat. They are friendly, outgoing, and make excellent companions for active families.",
                    "yorkshire": "Yorkshire Terriers are small dogs with a bold personality. They are affectionate, energetic, and make great companions despite their small size."
                };



                const description = breedDescriptions[bestClass.toLowerCase()] || "No description available.";
                descriptionText.innerHTML = `<strong class="text-white">About ${bestClass}:</strong><p class="text-white"> ${description}</p>`;
                descriptionText.classList.remove("hidden"); // Remove the hidden class to show the element

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