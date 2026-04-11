<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if salecode is provided in the query string
if (empty($_GET['salecode'])) {
    echo "Error: No salecode specified.";
    exit;
}

// Get salecode from the query string
$salecode = $_GET['salecode'];

// Define the allowed file extensions and check the file type
$fileExtensions = ['csv', 'xls', 'xlsx']; // Allowed file extensions

// Function to fetch the file from Flask server if not found locally
function fetchFileFromFlask($salecode, $extension) {
    // Flask server URL with the specified file extension
    $flaskUrl = "https://python.preferredequinesalesresults.com/views/uploads/formatted_" . $salecode . "." . $extension;
    
    // Use file_get_contents to fetch file from Flask server
    $fileContent = @file_get_contents($flaskUrl); // "@" suppresses warnings in case of failure

    // Check if file_get_contents() was successful
    if ($fileContent === false) {
        return false;  // Return false if the file could not be fetched
    }

    return $fileContent;  // Return the file content from Flask
}

// Try to find the file locally in each allowed extension
foreach ($fileExtensions as $ext) {
    $filePath = "./uploads/$salecode.$ext";

    // Check if the file exists locally
    if (file_exists($filePath)) {
        // Set headers to prompt the download from the local directory
        header('Content-Description: File Transfer');
        header('Content-Type: application/' . ($ext == 'csv' ? 'csv' : 'octet-stream'));
        header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filePath));

        // Output the file content from the local file system
        readfile($filePath);
        exit;
    } else {
        // File not found locally, attempt to fetch from Flask server
        $fileContent = fetchFileFromFlask($salecode, $ext);

        if ($fileContent !== false) {
            // If the file is fetched from Flask, serve it to the user
            header('Content-Description: File Transfer');
            header('Content-Type: application/' . ($ext == 'csv' ? 'csv' : 'octet-stream'));
            header('Content-Disposition: attachment; filename="' . $salecode . '.' . $ext . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . strlen($fileContent));

            // Output the file content fetched from Flask
            echo $fileContent;
            exit;
        }
    }
}

// If no file is found locally or fetched from Flask
echo '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Not Found</title>
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
        }
        .message-box {
            text-align: center;
            padding: 20px;
            border: 1px solid #ddd;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #e74c3c;
            font-size: 24px;
        }
        p {
            font-size: 16px;
        }
        a {
            color: #3498db;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="message-box">
        <h1>File Not Found</h1>
        <p>Sorry, the file you are looking for does not exist.</p>
        <p><a href="javascript:history.back()">Go back to the previous page</a></p>
    </div>
</body>
</html>';
exit;
