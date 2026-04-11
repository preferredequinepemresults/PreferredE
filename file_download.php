<?php
$bred_param = $_GET['bred'];

// Define the local file path based on the `bred_param` value
$file_path = "";
if ($bred_param == "S") {
    $file_path = __DIR__ . "/sampleFileUpload.csv"; // Adjust path if necessary
} elseif ($bred_param == "T") {
    $file_path = __DIR__ . "/sampleFileUpload_T.csv"; // Adjust path if necessary
}

// Get the file name for the download
$file_name = basename($file_path);
$info = pathinfo($file_name);

// Check if the file is a CSV and exists
if ($info["extension"] == "csv" && file_exists($file_path)) {
    // Set headers to prompt the download
    header("Content-Description: File Transfer");
    header("Content-Type: application/octet-stream");
    header("Content-Disposition: attachment; filename=\"" . $file_name . "\"");
    header("Content-Length: " . filesize($file_path));
    
    // Clear output buffer and read the file
    ob_clean();
    flush();
    readfile($file_path);
    exit;
} else {
    // Display an error message if the file is not found or not a CSV
    echo "Error: File not found or not a CSV file.";
}