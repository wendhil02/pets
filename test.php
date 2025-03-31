<?php
require __DIR__ . '/vendor/autoload.php';

use Google\Cloud\Vision\V1\ImageAnnotatorClient;

if (class_exists('Google\Cloud\Vision\V1\ImageAnnotatorClient')) {
    echo "Success: ImageAnnotatorClient class is found!";
} else {
    echo "Error: ImageAnnotatorClient class not found!";
}
?>


