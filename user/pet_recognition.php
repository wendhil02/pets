<?php
header("Content-Type: application/json");

if (!isset($_GET['breed']) || empty($_GET['breed'])) {
    echo json_encode(["status" => "error", "message" => "No breed provided"]);
    exit;
}

$breed = $_GET['breed'];
$imageDir = "newspetimage/";  

// Debugging: Check if directory exists
if (!is_dir($imageDir)) {
    echo json_encode(["status" => "error", "message" => "Directory not found"]);
    exit;
}

// Debugging: Log search path
$searchPath = $imageDir . $breed . "_*.jpg";
error_log("Searching for: " . $searchPath);

// Find matching images
$matches = glob($searchPath);

if (!empty($matches)) {
    error_log("✅ Match found: " . $matches[0]);
    echo json_encode(["status" => "success", "image_path" => $matches[0]]);
} else {
    error_log("❌ No match found for: " . $searchPath);
    echo json_encode(["status" => "error", "message" => "No matching image found"]);
}
?>
